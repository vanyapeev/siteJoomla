<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Privacy;

use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CBLib\Registry\ParametersStore;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CB\Plugin\Privacy\Table\PrivacyTable;

defined('CBLIB') or die();

/**
 * @method string getAsset()
 * @method Privacy setAsset( $asset )
 * @method UserTable getUser()
 * @method Privacy setUser( $user )
 * @method int|array getId()
 * @method Privacy setId( $id )
 * @method int|array getUserId()
 * @method Privacy setUserId( $userId )
 */
class Privacy extends ParametersStore implements PrivacyInterface
{
	/** @var string $id */
	protected $id						=	null;
	/** @var array $asset */
	protected $asset					=	null;
	/** @var UserTable $user */
	protected $user						=	null;
	/** @var array $ini */
	protected $ini						=	array();

	/** @var bool $clearRowCount */
	protected $clearRowCount			=	false;
	/** @var bool $clearRowSelect */
	protected $clearRowSelect			=	false;

	/** @var array $defaults */
	protected $defaults					=	array(	'template'					=>	'default',
													'layout'					=>	'button',
													'ajax'						=>	false,
													'paging'					=>	true,
													'paging_first_limit'		=>	15,
													'paging_limit'				=>	15,
													'options_default'			=>	'0',
													'options_visible'			=>	true,
													'options_users'				=>	true,
													'options_invisible'			=>	true,
													'options_moderator'			=>	false,
													'options_conn'				=>	true,
													'options_connofconn'		=>	true,
													'options_conntype'			=>	true,
													'options_conntypes'			=>	'0',
													'options_viewaccesslevel'	=>	false,
													'options_viewaccesslevels'	=>	'0',
													'options_usergroup'			=>	false,
													'options_usergroups'		=>	'0'
												);

	/** @var PrivacyTable[] $loadedRows */
	protected static $loadedRows		=	array();

	/**
	 * Constructor for privacy object
	 *
	 * @param null|string        $asset
	 * @param null|int|UserTable $user
	 */
	public function __construct( $asset = null, $user = null )
	{
		global $_CB_framework, $_PLUGINS;

		$_CB_framework->addJQueryPlugin( 'cbprivacy', '/components/com_comprofiler/plugin/user/plug_cbprivacy/js/jquery.cbprivacy.js', array( -1 => array( 'qtip', 'cbtooltip', 'cbselect' ) ) );

		$_PLUGINS->loadPluginGroup( 'user' );

		if ( $user === null ) {
			$user			=	\CBuser::getMyUserDataInstance();
		}

		$this->user( $user );
		$this->asset( $asset );

		$pluginParams		=	CBPrivacy::getGlobalParams();

		foreach ( $this->defaults as $param => $default ) {
			$value			=	$pluginParams->get( 'privacy_' . $param, $default, GetterInterface::STRING );

			if ( is_int( $default ) ) {
				$value		=	(int) $value;
			} elseif ( is_bool( $default ) ) {
				$value		=	(bool) $value;
			}

			$this->set( $param, $value );
		}
	}

	/**
	 * @param string $name
	 * @param array  $arguments
	 * @return self|string|int|array|null
	 */
	public function __call( $name, $arguments )
	{
		$method									=	substr( $name, 0, 3 );

		if ( in_array( $method, array( 'get', 'set' ) ) ) {
			$variables							=	array( 'asset', 'user', 'id', 'user_id' );
			$variable							=	strtolower( substr( $name, 3 ) );

			switch ( $variable ) {
				case 'userid':
					$variable					=	'user_id';
					break;
			}

			if ( in_array( $variable, $variables ) ) {
				switch ( $method ) {
					case 'get':
						switch ( $variable ) {
							case 'asset':
								return $this->asset();
								break;
							case 'user':
								return $this->user();
								break;
							case 'id':
							case 'user_id':
								if ( is_array( $this->get( $variable, null, GetterInterface::RAW ) ) ) {
									$default	=	array();
									$type		=	GetterInterface::RAW;
								} else {
									$default	=	0;
									$type		=	GetterInterface::INT;
								}
								break;
							default:
								if ( is_array( $this->get( $variable, null, GetterInterface::RAW ) ) ) {
									$default	=	array();
									$type		=	GetterInterface::RAW;
								} else {
									$default	=	null;
									$type		=	GetterInterface::STRING;
								}
								break;
						}

						return $this->get( $variable, $default, $type );
						break;
					case 'set':
						switch ( $variable ) {
							case 'asset':
								$this->asset( ( $arguments ? $arguments[0] : null ) );
								break;
							case 'user':
								$this->user( ( $arguments ? $arguments[0] : null ) );
								break;
							default:
								$this->set( $variable, ( $arguments ? $arguments[0] : null ) );
								break;
						}

						return $this;
						break;
				}
			}
		}

		trigger_error( 'Call to undefined method ' . __CLASS__ . '::' . $name . '()', E_USER_ERROR );

		return null;
	}

	/**
	 * Reloads the privacy from session by id optionally exclude id, asset, or user
	 *
	 * @param string $id
	 * @param array  $exclude
	 * @return bool
	 */
	public function load( $id, $exclude = array() )
	{
		$session				=	Application::Session()->subTree( 'privacy.' . $id );

		if ( $session->count() == 1 ) {
			$inherit			=	Application::Session()->get( 'privacy.' . $id, null, GetterInterface::STRING );

			if ( $inherit ) {
				if ( ! $this->id ) {
					$this->id	=	$id;
				}

				if ( ! in_array( 'id', $exclude ) ) {
					$exclude[]	=	'id';
				}

				return $this->load( $inherit, $exclude );
			}

			return false;
		}

		if ( $session->count() ) {
			if ( ! in_array( 'id', $exclude ) ) {
				$this->id		=	$id;
			}

			if ( ! in_array( 'asset', $exclude ) ) {
				$this->asset	=	$session->get( 'asset', null, GetterInterface::STRING );
			}

			if ( ! in_array( 'user', $exclude ) ) {
				$this->user		=	\CBuser::getUserDataInstance( $session->get( 'user', 0, GetterInterface::INT ) );
			}

			$this->ini			=	$session->asArray();

			parent::load( $session );

			return true;
		}

		return false;
	}

	/**
	 * Parses parameters into the privacy
	 *
	 * @param ParamsInterface|array|string $params
	 * @param null|string                  $prefix
	 * @return self
	 */
	public function parse( $params, $prefix = null )
	{
		if ( ! $params ) {
			return $this;
		}

		if ( $params instanceof self ) {
			$this->id			=	$params->id();
			$this->asset		=	$params->asset();
			$this->user			=	$params->user();
			$this->ini			=	$params->ini;

			parent::load( $params->ini );
		} else {
			if ( is_array( $params ) ) {
				$params			=	new Registry( $params );
			}

			foreach ( $this->defaults as $param => $default ) {
				$value			=	$params->get( $prefix . $param, null, GetterInterface::STRING );

				if ( ( $value !== '' ) && ( $value !== null ) && ( $value !== '-1' ) ) {
					if ( is_int( $default ) ) {
						$value	=	(int) $value;
					} elseif ( is_bool( $default ) ) {
						$value	=	(bool) $value;
					}

					$this->set( $param, $value );
				}
			}
		}

		return $this;
	}

	/**
	 * Gets the privacy id
	 *
	 * @return null|string
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * Gets or sets the privacy asset
	 *
	 * @param null|string $asset
	 * @return null|string
	 */
	public function asset( $asset = null )
	{
		global $_CB_framework;

		if ( $asset !== null ) {
			$extras						=	array(	'displayed_id'	=>	$_CB_framework->displayedUser(),
													'viewer_id'		=>	Application::MyUser()->getUserId()
												);

			$assetsUser					=	null;

			if ( $assetsUser === null ) {
				if ( in_array( $asset, array( 'self', 'user' ) ) ) {
					$assetsUser			=	\CBuser::getMyUserDataInstance();
				}

				if ( in_array( $asset, array( 'user', 'displayed' ) ) ) {
					if ( $_CB_framework->displayedUser() ) {
						$assetsUser		=	\CBuser::getUserDataInstance( $_CB_framework->displayedUser() );
					} elseif ( ! in_array( $asset, array( 'user' ) ) ) {
						$assetsUser		=	\CBuser::getUserDataInstance( 0 );
					}
				}

				if ( $assetsUser === null ) {
					$assetsUser			=	$this->user();
				}
			}

			if ( ( $asset === null ) || in_array( $asset, array( 'profile', 'self', 'user', 'displayed' ) ) ) {
				$asset					=	'profile';
			}

			$asset						=	\CBuser::getInstance( $assetsUser->get( 'id', 0, GetterInterface::INT ), false )->replaceUserVars( $asset, true, false, $extras, false );

			if ( $assetsUser !== null ) {
				$this->user( $assetsUser );
			}

			$this->asset				=	$asset;
		}

		if ( ! $this->asset ) {
			return null;
		}

		$asset							=	strtolower( trim( preg_replace( '/[^a-zA-Z0-9.]/i', '', $this->asset ) ) );

		if ( $asset == 'all' ) {
			$asset						=	'profile';
		}

		return $asset;
	}

	/**
	 * Gets or sets the privacy target user (owner)
	 *
	 * @param null|UserTable|int $user
	 * @return UserTable|int|null
	 */
	public function user( $user = null )
	{
		if ( $user !== null ) {
			if ( is_numeric( $user ) ) {
				$user		=	\CBuser::getUserDataInstance( (int) $user );
			}

			$this->user		=	$user;
		}

		return $this->user;
	}

	/**
	 * Returns the available privacy rules
	 *
	 * @param bool $raw
	 * @return array
	 */
	public function rules( $raw = false )
	{
		return CBPrivacy::getPrivacyOptions( $this, $raw );
	}

	/**
	 * Clears the data cache
	 *
	 * @return self
	 */
	public function clear()
	{
		$this->clearRowCount		=	true;
		$this->clearRowSelect		=	true;

		return $this;
	}

	/**
	 * Resets the privacy filters
	 *
	 * @return self
	 */
	public function reset()
	{
		$privacy	=	new self( $this->asset(), $this->user() );

		return $privacy->parse( $this );
	}

	/**
	 * Retrieves privacy rows or row count
	 *
	 * @param string $output
	 * @return PrivacyTable[]|int
	 */
	public function rows( $output = null )
	{
		global $_CB_database, $_PLUGINS;

		static $cache						=	array();

		$id									=	$this->get( 'id', null, GetterInterface::RAW );
		$hasId								=	( ( ( $id !== '' ) && ( $id !== null ) ) || ( is_array( $id ) && $id ) );

		$select								=	array();
		$join								=	array();
		$where								=	array();

		if ( $output == 'count' ) {
			$select[]						=	'COUNT( a.' . $_CB_database->NameQuote( 'id' ) . ' )';
		} else {
			$select[]						=	'a.*';
		}

		if ( $hasId ) {
			if ( is_array( $this->get( 'id', null, GetterInterface::RAW ) ) ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $id );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'id' ) . " = " . (int) $id;
			}
		}

		$userId								=	$this->get( 'user_id', null, GetterInterface::RAW );

		if ( ( ( $userId !== '' ) && ( $userId !== null ) ) || ( is_array( $userId ) && $userId ) ) {
			if ( is_array( $userId ) ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'user_id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $userId );
			} elseif ( $userId != 'all' ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $userId;
			}
		} else {
			$where[]						=	"a." . $_CB_database->NameQuote( 'user_id' ) . " = " . $this->user()->get( 'id', 0, GetterInterface::INT );
		}

		if ( $this->asset() && ( $this->asset() != 'all' ) ) {
			$where[]						=	"a." . $_CB_database->NameQuote( 'asset' ) . " = " . $_CB_database->Quote( $this->asset() );
		}

		$_PLUGINS->trigger( 'privacy_onQueryPrivacy', array( $output, &$select, &$join, &$where, &$this ) );

		$query								=	"SELECT " . implode( ", ", $select )
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_privacy' ) . " AS a"
											.	( $where ? "\n WHERE " . implode( "\n AND ", $where ) : null );

		if ( $this->get( 'paging_limitstart', 0, GetterInterface::INT ) == 0 ) {
			$pageLimit						=	$this->get( 'paging_first_limit', 15, GetterInterface::INT );
		} else {
			$pageLimit						=	$this->get( 'paging_limit', 15, GetterInterface::INT );
		}

		$paging								=	( ( ! $hasId ) && $pageLimit && ( $output != 'all' ) );
		$cacheId							=	md5( $query . ( $output ? $output : ( $paging ? $this->get( 'paging_limitstart', 0, GetterInterface::INT ) . $pageLimit : null ) ) );

		if ( ( ! isset( $cache[$cacheId] ) ) || ( ( ( $output == 'count' ) && $this->clearRowCount ) || $this->clearRowSelect ) ) {
			if ( $output == 'count' ) {
				$this->clearRowCount		=	false;

				$_CB_database->setQuery( $query );

				$cache[$cacheId]			=	(int) $_CB_database->loadResult();
			} else {
				$this->clearRowSelect		=	false;

				if ( $paging ) {
					$_CB_database->setQuery( $query, $this->get( 'paging_limitstart', 0, GetterInterface::INT ), $pageLimit );
				} else {
					$_CB_database->setQuery( $query );
				}

				$rows						=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Privacy\Table\PrivacyTable', array( $_CB_database ) );
				$rowsCount					=	count( $rows );
				$userIds					=	array();

				/** @var PrivacyTable[] $rows */
				foreach ( $rows as $row ) {
					$userIds[]				=	$row->get( 'user_id', 0, GetterInterface::INT );
				}

				if ( $userIds ) {
					\CBuser::advanceNoticeOfUsersNeeded( $userIds );
				}

				$_PLUGINS->trigger( 'privacy_onLoadPrivacy', array( &$rows, $this ) );

				if ( $rows ) {
					self::$loadedRows		=	( self::$loadedRows + $rows );
				}

				if ( $paging && $rowsCount && ( count( $rows ) < $rowsCount ) ) {
					$limitCache				=	$pageLimit;
					$nextLimit				=	( $limitCache - count( $rows ) );

					if ( $nextLimit <= 0 ) {
						$nextLimit			=	1;
					}

					$this->set( 'paging_limitstart', ( $this->get( 'paging_limitstart', 0, GetterInterface::INT ) + $limitCache ) );
					$this->set( 'paging_limit', $nextLimit );

					$cache[$cacheId]		=	( $rows + $this->rows( $output ) );

					$this->set( 'paging_limit', $limitCache );
				} else {
					$cache[$cacheId]		=	$rows;
				}
			}
		}

		return $cache[$cacheId];
	}

	/**
	 * Retrieves privacy row
	 *
	 * @param int $id
	 * @return PrivacyTable
	 */
	public function row( $id )
	{
		if ( ! $id ) {
			return new PrivacyTable();
		}

		if ( isset( self::$loadedRows[$id] ) ) {
			return self::$loadedRows[$id];
		}

		static $cache		=	array();

		if ( ! isset( $cache[$id] ) ) {
			$rows			=	$this->reset()->setId( $id )->rows();

			if ( isset( $rows[$id] ) ) {
				$row		=	$rows[$id];
			} else {
				$row		=	new PrivacyTable();
			}

			$cache[$id]		=	$row;
		}

		return $cache[$id];
	}

	/**
	 * Checks if supplied user is authorized to view this privacy row
	 * if forced authorized check will always be against privacy default option
	 *
	 * @param null|int|UserTable $user
	 * @param bool               $forced
	 * @return bool
	 */
	public function authorized( $user = null, $forced = false )
	{
		$privacy			=	array();

		if ( ! $forced ) {
			$privacy		=	CBPrivacy::getPrivacy( $this->user() );
		}

		if ( $forced || ( ! isset( $privacy[$this->asset()] ) ) ) {
			$defaults		=	explode( '|*|', $this->get( 'options_default', '0', GetterInterface::STRING ) );

			foreach ( $defaults as $default ) {
				$rule		=	new PrivacyTable();

				$rule->set( 'user_id', $this->user()->get( 'id', 0, GetterInterface::INT ) );
				$rule->set( 'asset', $this->asset() );
				$rule->set( 'rule', $default );

				if ( $rule->authorized( $user ) ) {
					return true;
				}
			}

			return false;
		}

		foreach ( $privacy[$this->asset()] as $rule ) {
			/** @var PrivacyTable $rule */
			if ( $rule->authorized( $user ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Outputs privacy HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function privacy( $view = null )
	{
		global $_CB_framework, $_PLUGINS;

		static $plugin		=	null;

		if ( ! $plugin ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbprivacy' );
		}

		if ( ! $plugin ) {
			return null;
		}

		if ( ! class_exists( 'CBplug_cbprivacy' ) ) {
			$component		=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/user/plug_cbprivacy/component.cbprivacy.php';

			if ( file_exists( $component ) ) {
				include_once( $component );
			}
		}

		$this->cache();

		ob_start();
		$pluginArguements	=	array( &$this, $view );

		$_PLUGINS->call( $plugin->id, 'getPrivacy', 'CBplug_cbprivacy', $pluginArguements );
		$return				=	ob_get_contents();
		ob_end_clean();

		return $return;
	}

	/**
	 * Returns an array of the privacy variables
	 *
	 * @return array
	 */
	public function asArray()
	{
		$params				=	parent::asArray();

		if ( isset( $params['paging_limitstart'] ) ) {
			unset( $params['paging_limitstart'] );
		}

		if ( isset( $params['query'] ) ) {
			unset( $params['query'] );
		}

		$params['asset']	=	$this->asset();
		$params['user']		=	$this->user()->get( 'id', 0, GetterInterface::INT );

		return $params;
	}

	/**
	 * Caches the privacy into session; this is normally only done on creation or parse to preserve parameters between loads
	 * It is not advised to call this manually unless privacy parameters have changed after creation and desired result is for them to persist
	 *
	 * @return self
	 */
	public function cache()
	{
		$newId				=	md5( self::asJson() );

		if ( $this->id() != $newId ) {
			$session		=	Application::Session();
			$privacy		=	$session->subTree( 'privacy' );

			if ( $this->id() ) {
				$privacy->set( $this->id(), $newId );
			}

			$this->id		=	$newId;
			$this->ini		=	$this->asArray();

			$privacy->set( $this->id(), $this->ini );

			$session->set( 'privacy', $privacy->asArray() );
		}

		return $this;
	}
}