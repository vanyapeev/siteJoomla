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

defined('CBLIB') or die();

class Stream extends ParametersStore implements StreamInterface
{
	/** @var string $namespace */
	private $namespace					=	null;

	/** @var string $id */
	protected $id						=	null;
	/** @var array $assets */
	protected $assets					=	array();
	/** @var UserTable $user */
	protected $user						=	null;
	/** @var array $ini */
	protected $ini						=	array();

	/** @var array $defaults */
	protected $defaults					=	array();

	/** @var bool $clearRowCount */
	protected $clearRowCount			=	false;
	/** @var bool $clearRowSelect */
	protected $clearRowSelect			=	false;

	/**
	 * Constructor for stream object
	 *
	 * @param null|string|array  $assets
	 * @param null|int|UserTable $user
	 */
	public function __construct( $assets = null, $user = null )
	{
		global $_CB_framework, $_PLUGINS;

		$this->namespace	=	strtolower( substr( strrchr( get_class( $this ), '\\' ), 1 ) );

		static $loaded		=	0;

		if ( ! $loaded++ ) {
			\cbValidator::loadValidation();
			\initToolTip();

			$_CB_framework->addJQueryPlugin( 'cbactivity', '/components/com_comprofiler/plugin/user/plug_cbactivity/js/jquery.cbactivity.js', array( -1 => array( 'ui-all', 'form', 'autosize', 'livestamp', 'qtip', 'cbtooltip', 'cbmoreless', 'cbrepeat', 'cbselect', 'cbtimeago', 'cbvalidate' ) ) );
		}

		$_PLUGINS->loadPluginGroup( 'user' );

		$_PLUGINS->trigger( 'activity_on' . ucfirst( $this->namespace ), array( &$assets, &$user, &$this->defaults, &$this ) );

		if ( $user === null ) {
			$user			=	\CBuser::getMyUserDataInstance();
		}

		$this->user( $user );
		$this->assets( $assets );

		$pluginParams		=	CBActivity::getGlobalParams();

		foreach ( $this->defaults as $param => $default ) {
			$value			=	$pluginParams->get( $this->namespace . '_' . $param, $default, GetterInterface::STRING );

			if ( is_int( $default ) ) {
				$value		=	(int) $value;
			} elseif ( is_bool( $default ) ) {
				$value		=	(bool) $value;
			}

			$this->set( $param, $value );
		}
	}

	/**
	 * Reloads the stream from session by id optionally exclude id, assets, or user
	 *
	 * @param string $id
	 * @param array  $exclude
	 * @return bool
	 */
	public function load( $id, $exclude = array() )
	{
		$session				=	Application::Session()->subTree( $this->namespace . '.' . $id );

		if ( $session->count() == 1 ) {
			$inherit			=	Application::Session()->get( $this->namespace . '.' . $id, null, GetterInterface::STRING );

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

			if ( ! in_array( 'assets', $exclude ) ) {
				$this->assets	=	$session->get( 'assets', array(), GetterInterface::RAW );
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
	 * Resets the stream filters
	 *
	 * @return static
	 */
	public function reset()
	{
		$stream		=	new static( $this->assets(), $this->user() );

		return $stream->parse( $this );
	}

	/**
	 * Parses parameters into the stream
	 *
	 * @param ParamsInterface|array|string $params
	 * @param null|string                  $namespace
	 * @return static
	 */
	public function parse( $params, $namespace = null )
	{
		if ( ! $params ) {
			return $this;
		}

		if ( $params instanceof static ) {
			$this->id			=	$params->id();
			$this->assets		=	$params->assets();
			$this->user			=	$params->user();
			$this->ini			=	$params->ini;

			parent::load( $params->ini );
		} else {
			if ( is_array( $params ) ) {
				$params			=	new Registry( $params );
			}

			foreach ( $this->defaults as $param => $default ) {
				$value			=	$params->get( $namespace . $param, null, GetterInterface::STRING );

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
	 * Gets the stream id
	 *
	 * @return string
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * Gets the primary stream asset
	 *
	 * @return string
	 */
	public function asset()
	{
		$assets		=	$this->assets( false );

		if ( ! $assets ) {
			return null;
		}

		return $assets[0];
	}

	/**
	 * Gets or sets the raw stream assets
	 *
	 * @param null|array|string|bool $assets null|true: get with wildcards; false: get without wildcards; string: set assets
	 * @return array
	 */
	public function assets( $assets = null )
	{
		global $_CB_framework;

		if ( ( $assets !== null ) && ( $assets !== true ) && ( $assets !== false ) ) {
			$extras							=	array(	'displayed_id'	=>	$_CB_framework->displayedUser(),
														'viewer_id'		=>	Application::MyUser()->getUserId()
													);

			if ( ! is_array( $assets ) ) {
				$assets						=	explode( ',', $assets );
			}

			$primaryUser					=	null;

			foreach ( $assets as $k => $asset ) {
				$assetsUser					=	null;

				if ( in_array( $asset, array( 'self', 'self.connections', 'self.connectionsonly', 'self.following', 'self.followingonly', 'self.likes', 'self.likesonly', 'user', 'user.connections', 'user.connectionsonly', 'user.following', 'user.followingonly', 'user.likes', 'user.likesonly' ) ) ) {
					$assetsUser				=	\CBuser::getMyUserDataInstance();
				}

				if ( in_array( $asset, array( 'user', 'user.connections', 'user.connectionsonly', 'user.following', 'user.followingonly', 'user.likes', 'user.likesonly', 'displayed', 'displayed.connections', 'displayed.connectionsonly', 'displayed.following', 'displayed.followingonly', 'displayed.likes', 'displayed.likesonly' ) ) ) {
					if ( $_CB_framework->displayedUser() ) {
						$assetsUser			=	\CBuser::getUserDataInstance( $_CB_framework->displayedUser() );
					} elseif ( ! in_array( $asset, array( 'user', 'user.connections', 'user.connectionsonly', 'user.following', 'user.followingonly', 'user.likes', 'user.likesonly' ) ) ) {
						$assetsUser			=	\CBuser::getUserDataInstance( 0 );
					}
				}

				if ( $assetsUser === null ) {
					$assetsUser				=	$this->user();
				} elseif ( $k === 0 ) {
					$primaryUser			=	$assetsUser;
				}

				if ( ( $asset === null ) || in_array( $asset, array( 'profile', 'following', 'followingonly', 'likes', 'likesonly', 'connections', 'connectionsonly', 'self', 'self.connections', 'self.connectionsonly', 'self.following', 'self.followingonly', 'self.likes', 'self.likesonly', 'user', 'user.connections', 'user.connectionsonly', 'user.following', 'user.followingonly', 'user.likes', 'user.likesonly', 'displayed', 'displayed.connections', 'displayed.connectionsonly', 'displayed.following', 'displayed.followingonly', 'displayed.likes', 'displayed.likesonly' ) ) ) {
					$newAsset				=	'profile.' . $assetsUser->get( 'id', 0, GetterInterface::INT );

					if ( Application::Config()->get( 'allowConnections', true, GetterInterface::BOOLEAN ) ) {
						if ( strpos( $asset, 'connectionsonly' ) !== false ) {
							$newAsset		.=	'.connectionsonly';
						} elseif ( strpos( $asset, 'connections' ) !== false ) {
							$newAsset		.=	'.connections';
						}
					}

					if ( strpos( $asset, 'following' ) !== false ) {
						$newAsset			.=	'.following';
					}

					if ( strpos( $asset, 'likes' ) !== false ) {
						$newAsset			.=	'.likes';
					}

					$asset					=	$newAsset;
				}

				$assets[$k]					=	\CBuser::getInstance( $assetsUser->get( 'id', 0, GetterInterface::INT ), false )->replaceUserVars( str_replace( '*', '%', $asset ), true, false, $extras, false );
			}

			if ( $primaryUser !== null ) {
				$this->user( $primaryUser );
			}

			$this->assets					=	$assets;
		}

		if ( ! $this->assets ) {
			return array();
		}

		if ( $assets === false ) {
			$assets							=	$this->assets;

			foreach ( $assets as $k => $asset ) {
				$asset						=	strtolower( trim( preg_replace( '/[^a-zA-Z0-9.%_-]/i', '', $asset ) ) );

				// Replace profile wildcard for storage purposes:
				if ( strpos( $asset, 'profile.' ) !== false ) {
					$asset					=	preg_replace( '/profile\.%(\.|$)/i', 'profile.' . $this->user()->get( 'id', 0, GetterInterface::INT ) . '$1', $asset );
				}

				// Replace tab wildcard for storage purposes:
				if ( strpos( $asset, 'tab.' ) !== false ) {
					$asset					=	preg_replace( '/tab\.%(\.|$)/i', 'tab.' . $this->get( 'tab', 0, GetterInterface::INT ) . '$1', $asset );
				}

				// Replace field wildcard for storage purposes:
				if ( strpos( $asset, 'field.' ) !== false ) {
					$asset					=	preg_replace( '/field\.%(\.|$)/i', 'field.' . $this->get( 'field', 0, GetterInterface::INT ) . '$1', $asset );
				}

				if ( ( $asset == 'all' ) || ( strpos( $asset, 'connections' ) !== false ) || ( strpos( $asset, 'following' ) !== false ) ) {
					$asset					=	'profile.' . $this->user()->get( 'id', 0, GetterInterface::INT );
				}

				$assets[$k]					=	$asset;
			}

			return array_unique( $assets );
		}

		return $this->assets;
	}

	/**
	 * Prepares assets array for query usage
	 *
	 * @param array $assets
	 * @param array $wildcards
	 * @param array $users
	 */
	protected function queryAssets( &$assets = array(), &$wildcards = array(), &$users = array() )
	{
		foreach ( $this->assets() as $asset ) {
			if ( $asset == 'all' ) {
				continue;
			}

			if ( strpos( $asset, 'connections' ) !== false ) {
				if ( preg_match( '/^profile\.(\d+)\.connections/', $asset, $matches ) ) {
					$profileId				=	(int) $matches[1];
				} else {
					$profileId				=	$this->user()->get( 'id', 0, GetterInterface::INT );
				}

				if ( $profileId ) {
					if ( strpos( $asset, 'connectionsonly' ) === false ) {
						$users[]			=	$profileId;
						$assets[]			=	'profile.' . $profileId;
					}

					foreach( CBActivity::getConnections( $profileId ) as $connection ) {
						$users[]			=	$connection;
						$assets[]			=	'profile.' . $connection;
					}
				}
			} elseif ( strpos( $asset, 'following' ) !== false ) {
				if ( preg_match( '/^profile\.(\d+)\.following/', $asset, $matches ) ) {
					$profileId				=	(int) $matches[1];
				} else {
					$profileId				=	$this->user()->get( 'id', 0, GetterInterface::INT );
				}

				if ( $profileId ) {
					if ( strpos( $asset, 'followingonly' ) === false ) {
						$users[]			=	$profileId;
						$assets[]			=	'profile.' . $profileId;
					}

					foreach( CBActivity::getFollowing( $profileId ) as $following ) {
						if ( preg_match( '/^profile\.(\d+)/', $following, $matches ) ) {
							$users[]		=	(int) $matches[1];
						}

						if ( ( strpos( $following, '%' ) !== false ) || ( strpos( $following, '_' ) !== false ) ) {
							$wildcards[]	=	$following;
						} else {
							$assets[]		=	$following;
						}
					}
				}
			} elseif ( strpos( $asset, 'likes' ) !== false ) {
				if ( preg_match( '/^profile\.(\d+)\.likes/', $asset, $matches ) ) {
					$profileId				=	(int) $matches[1];
				} else {
					$profileId				=	$this->user()->get( 'id', 0, GetterInterface::INT );
				}

				if ( $profileId ) {
					if ( strpos( $asset, 'likesonly' ) === false ) {
						$users[]			=	$profileId;
						$assets[]			=	'profile.' . $profileId;
					}

					foreach( CBActivity::getLikes( $profileId ) as $like ) {
						if ( preg_match( '/^profile\.(\d+)/', $like, $matches ) ) {
							$users[]		=	(int) $matches[1];
						}

						if ( ( strpos( $like, '%' ) !== false ) || ( strpos( $like, '_' ) !== false ) ) {
							$wildcards[]	=	$like;
						} else {
							$assets[]		=	$like;
						}
					}
				}
			} elseif ( ( strpos( $asset, '%' ) !== false ) || ( strpos( $asset, '_' ) !== false ) ) {
				$wildcards[]				=	$asset;
			} else {
				if ( preg_match( '/^profile\.(\d+)$/', $asset, $matches ) ) {
					$users[]				=	(int) $matches[1];
				}

				$assets[]					=	$asset;
			}
		}

		$assets								=	array_unique( $assets );
		$wildcards							=	array_unique( $wildcards );
		$users								=	array_unique( $users );
	}

	/**
	 * Gets or sets the stream target user (owner)
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
	 * @return static
	 */
	public function clear()
	{
		$this->clearRowCount		=	true;
		$this->clearRowSelect		=	true;

		return $this;
	}

	/**
	 * Returns a parser object for parsing stream content
	 *
	 * @param string $string
	 * @return Parser
	 */
	public function parser( $string = '' )
	{
		$parser		=	new Parser( $string, $this );

		return $parser;
	}

	/**
	 * Returns an array of the stream variables
	 *
	 * @return array
	 */
	public function asArray()
	{
		$params				=	parent::asArray();

		if ( isset( $params['paging_limitstart'] ) ) {
			unset( $params['paging_limitstart'] );
		}

		if ( isset( $params['search'] ) ) {
			unset( $params['search'] );
		}

		if ( isset( $params['query'] ) ) {
			unset( $params['query'] );
		}

		$params['assets']	=	$this->assets();
		$params['user']		=	$this->user()->get( 'id', 0, GetterInterface::INT );

		return $params;
	}

	/**
	 * Caches the stream into session; this is normally only done on creation or parse to preserve parameters between loads
	 * It is not advised to call this manually unless stream parameters have changed after creation and desired result is for them to persist
	 *
	 * @return static
	 */
	public function cache()
	{
		$newId				=	md5( self::asJson() );

		if ( $this->id() != $newId ) {
			$session		=	Application::Session();
			$stream			=	$session->subTree( $this->namespace );

			if ( $this->id() ) {
				$stream->set( $this->id(), $newId );
			}

			$this->id		=	$newId;
			$this->ini		=	$this->asArray();

			$stream->set( $this->id(), $this->ini );

			$session->set( $this->namespace, $stream->asArray() );
		}

		return $this;
	}

	/**
	 * Outputs stream HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	protected function display( $view = null )
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
}