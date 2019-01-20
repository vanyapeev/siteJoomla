<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Registry\ParametersStore;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CB\Plugin\Activity\Table\TagTable;

defined('CBLIB') or die();

/**
 * @method string getAsset()
 * @method Tags setAsset( $asset )
 * @method UserTable getUser()
 * @method Tags setUser( $user )
 * @method int|array getId()
 * @method Tags setId( $id )
 * @method array getModerators()
 * @method Tags setModerators( $moderators )
 * @method int|array getUserId()
 * @method Tags setUserId( $userId )
 * @method string getPlaceholder()
 * @method Tags setPlaceholder( $placeholder )
 * @method bool getInline()
 * @method Tags setInline( $inline )
 */
class Tags extends ParametersStore implements TagsInterface
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
	protected $defaults					=	array(	'paging'				=>	true,
													'paging_first_limit'	=>	15,
													'paging_limit'			=>	15
												);

	/** @var TagTable[] $loadedRows */
	protected static $loadedRows		=	array();

	/**
	 * Constructor for tags object
	 *
	 * @param null|string        $asset
	 * @param null|int|UserTable $user
	 */
	public function __construct( $asset = null, $user = null )
	{
		global $_CB_framework, $_PLUGINS;

		static $loaded			=	0;

		if ( ! $loaded++ ) {
			\cbValidator::loadValidation();
			\initToolTip();

			$_CB_framework->addJQueryPlugin( 'cbactivity', '/components/com_comprofiler/plugin/user/plug_cbactivity/js/jquery.cbactivity.js', array( -1 => array( 'ui-all', 'form', 'autosize', 'livestamp', 'qtip', 'cbtooltip', 'cbmoreless', 'cbrepeat', 'cbselect', 'cbtimeago', 'cbvalidate' ) ) );
		}

		$_PLUGINS->loadPluginGroup( 'user' );

		$_PLUGINS->trigger( 'activity_onTags', array( &$asset, &$user, &$this->defaults, &$this ) );

		if ( $user === null ) {
			$user				=	\CBuser::getMyUserDataInstance();
		}

		$this->user( $user );
		$this->asset( $asset );

		$pluginParams			=	CBActivity::getGlobalParams();

		foreach ( $this->defaults as $param => $default ) {
			$value				=	$pluginParams->get( 'tags_' . $param, $default, GetterInterface::STRING );

			if ( is_int( $default ) ) {
				$value			=	(int) $value;
			} elseif ( is_bool( $default ) ) {
				$value			=	(bool) $value;
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
			$variables							=	array( 'asset', 'user', 'id', 'moderators', 'user_id', 'placeholder', 'inline' );
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
							case 'moderators':
								$default		=	array();
								$type			=	GetterInterface::RAW;
								break;
							case 'inline':
								$default		=	false;
								$type			=	GetterInterface::BOOLEAN;
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
	 * Reloads the tags from session by id optionally exclude id, asset, or user
	 *
	 * @param string $id
	 * @param array  $exclude
	 * @return bool
	 */
	public function load( $id, $exclude = array() )
	{
		$session				=	Application::Session()->subTree( 'tags.' . $id );

		if ( $session->count() == 1 ) {
			$inherit			=	Application::Session()->get( 'tags.' . $id, null, GetterInterface::STRING );

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
	 * Parses parameters into the tags
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
	 * Gets the tags id
	 *
	 * @return string
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * Gets or sets the tags asset
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
				if ( in_array( $asset, array( 'self', 'self.connections', 'self.connectionsonly', 'self.following', 'self.followingonly', 'self.likes', 'self.likesonly', 'user', 'user.connections', 'user.connectionsonly', 'user.following', 'user.followingonly', 'user.likes', 'user.likesonly' ) ) ) {
					$assetsUser			=	\CBuser::getMyUserDataInstance();
				}

				if ( in_array( $asset, array( 'user', 'user.connections', 'user.connectionsonly', 'user.following', 'user.followingonly', 'user.likes', 'user.likesonly', 'displayed', 'displayed.connections', 'displayed.connectionsonly', 'displayed.following', 'displayed.followingonly', 'displayed.likes', 'displayed.likesonly' ) ) ) {
					if ( $_CB_framework->displayedUser() ) {
						$assetsUser		=	\CBuser::getUserDataInstance( $_CB_framework->displayedUser() );
					} elseif ( ! in_array( $asset, array( 'user', 'user.connections', 'user.connectionsonly', 'user.following', 'user.followingonly', 'user.likes', 'user.likesonly' ) ) ) {
						$assetsUser		=	\CBuser::getUserDataInstance( 0 );
					}
				}

				if ( $assetsUser === null ) {
					$assetsUser			=	$this->user();
				}
			}

			if ( ( $asset === null ) || in_array( $asset, array( 'profile', 'following', 'followingonly', 'likes', 'likesonly', 'connections', 'connectionsonly', 'self', 'self.connections', 'self.connectionsonly', 'self.following', 'self.followingonly', 'self.likes', 'self.likesonly', 'user', 'user.connections', 'user.connectionsonly', 'user.following', 'user.followingonly', 'user.likes', 'user.likesonly', 'displayed', 'displayed.connections', 'displayed.connectionsonly', 'displayed.following', 'displayed.followingonly', 'displayed.likes', 'displayed.likesonly' ) ) ) {
				$asset					=	'profile.' . $assetsUser->get( 'id', 0, GetterInterface::INT );
			}

			$asset						=	\CBuser::getInstance( $assetsUser->get( 'id', 0, GetterInterface::INT ), false )->replaceUserVars( str_replace( '*', '%', $asset ), true, false, $extras, false );

			if ( $assetsUser !== null ) {
				$this->user( $assetsUser );
			}

			$this->asset				=	$asset;
		}

		if ( ! $this->asset ) {
			return null;
		}

		$asset							=	strtolower( trim( preg_replace( '/[^a-zA-Z0-9.%_-]/i', '', $this->asset ) ) );

		// Replace profile wildcard for storage purposes:
		if ( strpos( $asset, 'profile.' ) !== false ) {
			$asset						=	preg_replace( '/profile\.%(\.|$)/i', 'profile.' . $this->user()->get( 'id', 0, GetterInterface::INT ) . '$1', $asset );
		}

		// Replace tab wildcard for storage purposes:
		if ( strpos( $asset, 'tab.' ) !== false ) {
			$asset						=	preg_replace( '/tab\.%(\.|$)/i', 'tab.' . $this->get( 'tab', 0, GetterInterface::INT ) . '$1', $asset );
		}

		// Replace field wildcard for storage purposes:
		if ( strpos( $asset, 'field.' ) !== false ) {
			$asset						=	preg_replace( '/field\.%(\.|$)/i', 'field.' . $this->get( 'field', 0, GetterInterface::INT ) . '$1', $asset );
		}

		if ( $asset == 'all' ) {
			$asset						=	'profile.' . $this->user()->get( 'id', 0, GetterInterface::INT );
		}

		return $asset;
	}

	/**
	 * Gets or sets the tags target user (owner)
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
	 * Resets the tags filters
	 *
	 * @return self
	 */
	public function reset()
	{
		$tags	=	new self( $this->asset(), $this->user() );

		return $tags->parse( $this );
	}

	/**
	 * Retrieves tags rows or row count
	 *
	 * @param string $output
	 * @return TagTable[]|int
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
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $userId;
			}
		}

		if ( $this->asset() && ( $this->asset() != 'all' ) ) {
			$where[]						=	"a." . $_CB_database->NameQuote( 'asset' ) . " = " . $_CB_database->Quote( $this->asset() );
		}

		$_PLUGINS->trigger( 'activity_onQueryTagsStream', array( $output, &$select, &$join, &$where, &$this ) );

		$query								=	"SELECT " . implode( ", ", $select )
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_tags' ) . " AS a"
											.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
											.	" ON cb." . $_CB_database->NameQuote( 'id' ) . " = a." . $_CB_database->NameQuote( 'user_id' )
											.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
											.	" ON j." . $_CB_database->NameQuote( 'id' ) . " = cb." . $_CB_database->NameQuote( 'id' )
											.	( $join ? "\n " . implode( "\n ", $join ) : null )
											.	"\n WHERE cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
											.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
											.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0"
											.	( $where ? "\n AND " . implode( "\n AND ", $where ) : null );

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

				$this->set( 'paging_limitstart', ( $this->get( 'paging_limitstart', 0, GetterInterface::INT ) + $pageLimit ) );

				$rows						=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\TagTable', array( $_CB_database ) );
				$rowsCount					=	count( $rows );
				$userIds					=	array();

				/** @var TagTable[] $rows */
				foreach ( $rows as $row ) {
					if ( preg_match( '/^profile\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
						$userIds[]			=	(int) $matches[1];
					}

					$userIds[]				=	$row->get( 'user_id', 0, GetterInterface::INT );

					if ( is_numeric( $row->get( 'tag', null, GetterInterface::STRING ) ) ) {
						$userIds[]			=	$row->get( 'tag', 0, GetterInterface::INT );
					}
				}

				if ( $userIds ) {
					\CBuser::advanceNoticeOfUsersNeeded( $userIds );
				}

				$_PLUGINS->trigger( 'activity_onLoadTagsStream', array( &$rows, $this ) );

				if ( $rows ) {
					self::$loadedRows		=	( self::$loadedRows + $rows );
				}

				if ( $paging && $rowsCount && ( count( $rows ) < $rowsCount ) ) {
					$nextLimit				=	( $pageLimit - count( $rows ) );

					if ( $nextLimit <= 0 ) {
						$nextLimit			=	1;
					}

					$this->set( 'paging_limit', $nextLimit );

					$cache[$cacheId]		=	( $rows + $this->rows( $output ) );

					$this->set( 'paging_limit', $pageLimit );
				} else {
					$cache[$cacheId]		=	$rows;
				}
			}
		} elseif ( $output != 'count' ) {
			$this->set( 'paging_limitstart', ( $this->get( 'paging_limitstart', 0, GetterInterface::INT ) + count( $cache[$cacheId] ) ) );
		}

		return $cache[$cacheId];
	}

	/**
	 * Retrieves tags row
	 *
	 * @param int $id
	 * @return TagTable
	 */
	public function row( $id )
	{
		if ( ! $id ) {
			return new TagTable();
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
				$row		=	new TagTable();
			}

			$cache[$id]		=	$row;
		}

		return $cache[$id];
	}

	/**
	 * Outputs tags HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function tags( $view = null )
	{
		global $_CB_framework, $_PLUGINS;

		static $plugin		=	null;

		if ( ! $plugin ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' );
		}

		if ( ! $plugin ) {
			return null;
		}

		if ( ! class_exists( 'CBplug_cbactivity' ) ) {
			$component		=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/user/plug_cbactivity/component.cbactivity.php';

			if ( file_exists( $component ) ) {
				include_once( $component );
			}
		}

		$this->cache();

		ob_start();
		$pluginArguements	=	array( &$this, $view );

		$_PLUGINS->call( $plugin->id, 'getStream', 'CBplug_cbactivity', $pluginArguements );
		$return				=	ob_get_contents();
		ob_end_clean();

		return $return;
	}

	/**
	 * Returns an array of the tags variables
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
	 * Caches the tags into session; this is normally only done on creation or parse to preserve parameters between loads
	 * It is not advised to call this manually unless tags parameters have changed after creation and desired result is for them to persist
	 *
	 * @return self
	 */
	public function cache()
	{
		$newId				=	md5( self::asJson() );

		if ( $this->id() != $newId ) {
			$session		=	Application::Session();
			$tags			=	$session->subTree( 'tags' );

			if ( $this->id() ) {
				$tags->set( $this->id(), $newId );
			}

			$this->id		=	$newId;
			$this->ini		=	$this->asArray();

			$tags->set( $this->id(), $this->ini );

			$session->set( 'tags', $tags->asArray() );
		}

		return $this;
	}
}