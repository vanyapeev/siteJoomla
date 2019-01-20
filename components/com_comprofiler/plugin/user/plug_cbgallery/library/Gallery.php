<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery;

use CB\Plugin\Gallery\Table\FolderTable;
use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Registry\ParametersStore;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\Table\ItemTable;
use CBLib\Registry\Registry;

defined('CBLIB') or die();

/**
 * @method string getAsset()
 * @method Gallery setAsset( $asset )
 * @method array getAssets()
 * @method Gallery setAssets( $assets )
 * @method UserTable getUser()
 * @method Gallery setUser( $user )
 * @method int getId()
 * @method Gallery setId( $id )
 * @method array getModerators()
 * @method Gallery setModerators( $moderators )
 * @method array getNotify()
 * @method Gallery setNotify( $recipients )
 * @method int getUserId()
 * @method Gallery setUserId( $folder )
 * @method string|array getType()
 * @method Gallery setType( $type )
 * @method int getFolder()
 * @method Gallery setFolder( $folder )
 * @method string getSearch()
 * @method Gallery setSearch( $search )
 * @method string getFile()
 * @method Gallery setFile( $file )
 * @method string getValue()
 * @method Gallery setValue( $value )
 * @method string getTitle()
 * @method Gallery setTitle( $title )
 * @method string getDescription()
 * @method Gallery setDescription( $description )
 * @method int getPublished()
 * @method Gallery setPublished( $published )
 * @method string getLocation()
 * @method Gallery setLocation( $url )
 */
class Gallery extends ParametersStore implements GalleryInterface
{
	/** @var string $id */
	protected $id						=	null;
	/** @var array $assets */
	protected $assets					=	array();
	/** @var UserTable $user */
	protected $user						=	null;
	/** @var array $ini */
	protected $ini						=	array();

	/** @var bool $clearFolderCount */
	protected $clearFolderCount			=	false;
	/** @var bool $clearFolderSelect */
	protected $clearFolderSelect		=	false;

	/** @var bool $clearItemCount */
	protected $clearItemCount			=	false;
	/** @var bool $clearItemSelect */
	protected $clearItemSelect			=	false;

	/** @var array $defaults */
	protected $defaults					=	array(	'folders'							=>	true,
													'folders_width'						=>	200,
													'folders_create'					=>	true,
													'folders_create_access'				=>	2,
													'folders_create_limit'				=>	'custom',
													'folders_create_limit_custom'		=>	0,
													'folders_create_approval'			=>	false,
													'folders_create_approval_notify'	=>	true,
													'folders_create_captcha'			=>	false,
													'folders_paging'					=>	true,
													'folders_paging_limit'				=>	15,
													'folders_search'					=>	true,
													'folders_orderby'					=>	'date_desc',
													'folders_items_paging'				=>	true,
													'folders_items_paging_limit'		=>	15,
													'folders_items_search'				=>	true,
													'folders_items_orderby'				=>	'date_desc',
													'items_width'						=>	200,
													'items_create'						=>	true,
													'items_create_captcha'				=>	false,
													'items_create_approval_notify'		=>	true,
													'items_paging'						=>	true,
													'items_paging_limit'				=>	15,
													'items_search'						=>	true,
													'items_orderby'						=>	'date_desc',
													'photos'							=>	true,
													'photos_download'					=>	true,
													'photos_avatar'						=>	false,
													'photos_canvas'						=>	false,
													'photos_create'						=>	true,
													'photos_create_access'				=>	2,
													'photos_create_limit'				=>	'custom',
													'photos_create_limit_custom'		=>	0,
													'photos_upload'						=>	true,
													'photos_link'						=>	true,
													'photos_create_approval'			=>	false,
													'photos_metadata'					=>	false,
													'photos_resample'					=>	1,
													'photos_image_height'				=>	640,
													'photos_image_width'				=>	1280,
													'photos_thumbnail_height'			=>	320,
													'photos_thumbnail_width'			=>	640,
													'photos_maintain_aspect_ratio'		=>	1,
													'photos_min_size'					=>	0,
													'photos_max_size'					=>	1024,
													'photos_client_resize'				=>	true,
													'videos'							=>	true,
													'videos_download'					=>	false,
													'videos_create'						=>	true,
													'videos_create_access'				=>	2,
													'videos_create_limit'				=>	'custom',
													'videos_create_limit_custom'		=>	0,
													'videos_upload'						=>	true,
													'videos_link'						=>	true,
													'videos_create_approval'			=>	false,
													'videos_min_size'					=>	0,
													'videos_max_size'					=>	1024,
													'files'								=>	true,
													'files_create'						=>	true,
													'files_create_access'				=>	2,
													'files_create_limit'				=>	'custom',
													'files_create_limit_custom'			=>	0,
													'files_upload'						=>	true,
													'files_link'						=>	true,
													'files_create_approval'				=>	false,
													'files_md5'							=>	false,
													'files_sha1'						=>	false,
													'files_extensions'					=>	'zip,rar,doc,pdf,txt,xls',
													'files_min_size'					=>	0,
													'files_max_size'					=>	1024,
													'music'								=>	true,
													'music_download'					=>	false,
													'music_create'						=>	true,
													'music_create_access'				=>	2,
													'music_create_limit'				=>	'custom',
													'music_create_limit_custom'			=>	0,
													'music_upload'						=>	true,
													'music_link'						=>	true,
													'music_create_approval'				=>	false,
													'music_min_size'					=>	0,
													'music_max_size'					=>	1024,
													'thumbnails'						=>	true,
													'thumbnails_upload'					=>	true,
													'thumbnails_link'					=>	false,
													'thumbnails_resample'				=>	1,
													'thumbnails_image_height'			=>	320,
													'thumbnails_image_width'			=>	640,
													'thumbnails_maintain_aspect_ratio'	=>	1,
													'thumbnails_min_size'				=>	0,
													'thumbnails_max_size'				=>	1024
												);

	/** @var FolderTable[] $loadedFolders */
	protected static $loadedFolders		=	array();
	/** @var ItemTable[] $loadedItems */
	protected static $loadedItems		=	array();

	/**
	 * Constructor for gallery object
	 *
	 * @param null|string|array  $assets
	 * @param null|int|UserTable $user
	 */
	public function __construct( $assets = null, $user = null )
	{
		global $_CB_framework, $_PLUGINS;

		static $loaded				=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->addJQueryPlugin( 'cbgallery', '/components/com_comprofiler/plugin/user/plug_cbgallery/js/cbgallery.js', array( -1 => array( 'ui-all', 'iframe-transport', 'fileupload', 'form', 'cbmoreless', 'livestamp', 'cbtimeago', 'qtip', 'cbtooltip' ) ) );
		}

		$_PLUGINS->loadPluginGroup( 'user' );

		$_PLUGINS->trigger( 'gallery_onGallery', array( &$assets, &$user, &$this->defaults, &$this ) );

		if ( ! $assets ) {
			$assets					=	array( 'profile', 'uploads' );
		}

		if ( $user === null ) {
			$user					=	\CBuser::getMyUserDataInstance();
		}

		$this->user( $user );
		$this->assets( $assets );

		$pluginParams				=	CBGallery::getGlobalParams();

		foreach ( $this->defaults as $param => $default ) {
			$value					=	$pluginParams->get( $param, $default, GetterInterface::STRING );

			if ( is_int( $default ) ) {
				$value				=	(int) $value;
			} elseif ( is_bool( $default ) ) {
				$value				=	(bool) $value;
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
			$variables							=	array( 'asset', 'assets', 'user', 'id', 'id', 'moderators', 'notify', 'user_id', 'type', 'folder', 'search', 'file', 'value', 'title', 'description', 'published', 'location' );
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
							case 'assets':
								return $this->assets();
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
							case 'folder':
							case 'published':
								$default		=	0;
								$type			=	GetterInterface::INT;
								break;
							case 'moderators':
							case 'notify':
								$default		=	array();
								$type			=	GetterInterface::RAW;
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
							case 'assets':
								$this->assets( ( $arguments ? $arguments[0] : null ) );
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
	 * Reloads the gallery from session by id optionally exclude id, assets, or user
	 *
	 * @param string $id
	 * @param array  $exclude
	 * @return bool
	 */
	public function load( $id, $exclude = array() )
	{
		$session				=	Application::Session()->subTree( 'gallery.' . $id );

		if ( $session->count() == 1 ) {
			$inherit			=	Application::Session()->get( 'gallery.' . $id, null, GetterInterface::STRING );

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
	 * Parses parameters into the gallery
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
			$this->assets		=	$params->assets();
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
	 * Gets the gallery location
	 *
	 * @return string
	 */
	public function location()
	{
		global $_CB_framework;

		$location					=	CBGallery::getReturn( false, true );

		if ( $this->get( 'location', null, GetterInterface::STRING ) ) {
			$location				=	$this->get( 'location', null, GetterInterface::STRING );

			if ( $location == 'plugin' ) {
				$location			=	$_CB_framework->pluginClassUrl( 'cbgallery', false, array( 'action' => 'gallery', 'gallery' => $this->id() ) );
			} elseif ( $location == 'current' ) {
				$location			=	CBGallery::getReturn( false, true );

				$this->set( 'location', $location );
			}
		} elseif ( preg_match( '/^profile(?:\.(\d+)(?:\.field\.(\d+))?)?/', $this->asset(), $matches ) || $this->get( 'tab', 0, GetterInterface::INT ) || $this->get( 'field', 0, GetterInterface::INT ) ) {
			$profileId				=	( isset( $matches[1] ) ? (int) $matches[1] : $this->user()->get( 'id', 0, GetterInterface::INT ) );
			$fieldId				=	( isset( $matches[2] ) ? (int) $matches[2] : $this->get( 'field', 0, GetterInterface::INT ) );

			if ( $fieldId ) {
				$field				=	CBGallery::getField( $fieldId, $profileId );

				if ( $field ) {
					$location		=	$_CB_framework->userProfileUrl( $profileId, false, $field->get( 'tabid', 0, GetterInterface::INT ) );
				}
			} else {
				$tabId				=	$this->get( 'tab', 0, GetterInterface::INT );
				$tab				=	CBGallery::getTab( $tabId, $profileId );

				if ( $tab ) {
					$location		=	$_CB_framework->userProfileUrl( $profileId, false, $tab->get( 'tabid', 0, GetterInterface::INT ) );
				}
			}
		}

		if ( $this->get( 'folder', 0, GetterInterface::INT ) ) {
			$location				=	$_CB_framework->pluginClassUrl( 'cbgallery', false, array( 'action' => 'folder', 'func' => 'show', 'id' => $this->get( 'folder', 0, GetterInterface::INT ), 'gallery' => $this->id(), 'return' => ( $location ? base64_encode( $location ) : null ) ) );
		}

		return $location;
	}

	/**
	 * Gets the gallery id
	 *
	 * @return string
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * Gets the gallery asset
	 *
	 * @return string
	 */
	public function asset()
	{
		if ( ! $this->assets ) {
			return null;
		}

		$asset		=	strtolower( trim( preg_replace( '/[^a-zA-Z0-9.%]/i', '', $this->assets[0] ) ) );

		// Replace profile wildcard for storage purposes:
		if ( strpos( $asset, 'profile.' ) !== false ) {
			$asset	=	preg_replace( '/profile\.%(\.|$)/i', 'profile.' . $this->user()->get( 'id', 0, GetterInterface::INT ) . '$1', $asset );
		}

		// Replace tab wildcard for storage purposes:
		if ( strpos( $asset, 'tab.' ) !== false ) {
			$asset	=	preg_replace( '/tab\.%(\.|$)/i', 'tab.' . $this->get( 'tab', 0, GetterInterface::INT ) . '$1', $asset );
		}

		// Replace field wildcard for storage purposes:
		if ( strpos( $asset, 'field.' ) !== false ) {
			$asset	=	preg_replace( '/field\.%(\.|$)/i', 'field.' . $this->get( 'field', 0, GetterInterface::INT ) . '$1', $asset );
		}

		if ( ( $asset == 'all' ) || ( strpos( $asset, 'connections' ) !== false ) ) {
			$asset	=	'profile.' . $this->user()->get( 'id', 0, GetterInterface::INT );
		}

		return $asset;
	}

	/**
	 * Gets or sets the raw gallery assets
	 *
	 * @param null|array|string $assets
	 * @return array|null
	 */
	public function assets( $assets = null )
	{
		global $_CB_framework;

		if ( $assets !== null ) {
			$extras							=	array(	'displayed_id'	=>	$_CB_framework->displayedUser(),
														'viewer_id'		=>	Application::MyUser()->getUserId()
													);

			if ( ! is_array( $assets ) ) {
				$assets						=	explode( ',', $assets );
			}

			$assetsUser						=	null;

			foreach ( $assets as $k => $asset ) {
				if ( $assetsUser === null ) {
					if ( in_array( $asset, array( 'self', 'self.uploads', 'self.connections', 'self.connectionsonly', 'user', 'user.uploads', 'user.connections', 'user.connectionsonly' ) ) ) {
						$assetsUser			=	\CBuser::getMyUserDataInstance();
					}

					if ( in_array( $asset, array( 'user', 'user.uploads', 'user.connections', 'user.connectionsonly', 'displayed', 'displayed.uploads', 'displayed.connections', 'displayed.connectionsonly' ) ) ) {
						if ( $_CB_framework->displayedUser() ) {
							$assetsUser		=	\CBuser::getUserDataInstance( $_CB_framework->displayedUser() );
						} elseif ( ! in_array( $asset, array( 'user', 'user.connections', 'user.connectionsonly' ) ) ) {
							$assetsUser		=	\CBuser::getUserDataInstance( 0 );
						}
					}

					if ( $assetsUser === null ) {
						$assetsUser			=	$this->user();
					}
				}

				if ( ( $asset === null ) || in_array( $asset, array( 'profile', 'uploads', 'connections', 'connectionsonly', 'self', 'self.uploads', 'self.connections', 'self.connectionsonly', 'user', 'user.uploads', 'user.connections', 'user.connectionsonly', 'displayed', 'displayed.uploads', 'displayed.connections', 'displayed.connectionsonly' ) ) ) {
					$newAsset				=	'profile.' . $assetsUser->get( 'id', 0, GetterInterface::INT );

					if ( Application::Config()->get( 'allowConnections', true, GetterInterface::BOOLEAN ) ) {
						if ( strpos( $asset, 'connectionsonly' ) !== false ) {
							$newAsset		.=	'.connectionsonly';
						} elseif ( strpos( $asset, 'connections' ) !== false ) {
							$newAsset		.=	'.connections';
						}
					}

					if ( strpos( $asset, 'uploads' ) !== false ) {
						$newAsset			.=	'.uploads';
					}

					$asset					=	$newAsset;
				}

				$assets[$k]					=	\CBuser::getInstance( $assetsUser->get( 'id', 0, GetterInterface::INT ), false )->replaceUserVars( str_replace( '*', '%', $asset ), true, false, $extras, false );
			}

			if ( $assetsUser !== null ) {
				$this->user( $assetsUser );
			}

			$this->assets					=	$assets;
		}

		if ( ! $this->assets ) {
			return array();
		}

		return $this->assets;
	}

	/**
	 * Gets or sets the gallery target user (owner)
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
	 * Gets the types allowed in this gallery
	 *
	 * @return array
	 */
	public function types()
	{
		$types			=	array();

		if ( $this->get( 'photos', true, GetterInterface::BOOLEAN ) ) {
			$types[]	=	'photos';
		}

		if ( $this->get( 'videos', true, GetterInterface::BOOLEAN ) ) {
			$types[]	=	'videos';
		}

		if ( $this->get( 'files', true, GetterInterface::BOOLEAN ) ) {
			$types[]	=	'files';
		}

		if ( $this->get( 'music', true, GetterInterface::BOOLEAN ) ) {
			$types[]	=	'music';
		}

		return $types;
	}

	/**
	 * Clears the data cache
	 *
	 * @return self
	 */
	public function clear()
	{
		$this->clearFolderCount		=	true;
		$this->clearFolderSelect	=	true;

		$this->clearItemCount		=	true;
		$this->clearItemSelect		=	true;

		return $this;
	}

	/**
	 * Resets the gallery filters
	 *
	 * @return self
	 */
	public function reset()
	{
		$gallery	=	new self( $this->assets(), $this->user() );

		return $gallery->parse( $this );
	}

	/**
	 * Retrieves gallery folder rows or row count
	 *
	 * @param string $output
	 * @return FolderTable[]|int
	 */
	public function folders( $output = null )
	{
		global $_CB_database, $_PLUGINS;

		if ( ! $this->get( 'folders', true, GetterInterface::BOOLEAN ) ) {
			if ( $output == 'count' ) {
				return 0;
			} else {
				return array();
			}
		}

		static $cache							=	array();

		$id										=	$this->get( 'id', null, GetterInterface::RAW );
		$hasId									=	( ( ( $id !== '' ) && ( $id !== null ) ) || ( is_array( $id ) && $id ) );

		$select									=	array();
		$join									=	array();
		$where									=	array();

		if ( $output == 'count' ) {
			$select[]							=	'COUNT( a.' . $_CB_database->NameQuote( 'id' ) . ' )';
		} else {
			$select[]							=	'a.*';
		}

		if ( $output != 'count' ) {
			$itemsSelect						=	array( 'COUNT( b.' . $_CB_database->NameQuote( 'id' ) . ' )' );
			$itemsJoin							=	array();
			$itemsWhere							=	array();
			$itemsDisable						=	false;

			if ( $this->assets() && ( ! in_array( 'all', $this->assets() ) ) )  {
				$itemsAssets					=	array();
				$itemsWildcards					=	array();

				foreach ( $this->assets() as $asset ) {
					if ( strpos( $asset, 'connections' ) !== false ) {
						if ( preg_match( '/^profile\.(\d+)\.connections/', $asset, $matches ) ) {
							$profileId			=	(int) $matches[1];
						} else {
							$profileId			=	$this->user()->get( 'id', 0, GetterInterface::INT );
						}

						if ( $profileId ) {
							if ( strpos( $asset, 'connectionsonly' ) === false ) {
								$itemsAssets[]	=	'profile.' . $profileId;
							}

							foreach( CBGallery::getConnections( $profileId ) as $connection ) {
								$itemsAssets[]	=	'profile.' . (int) $connection->id;
							}
						}
					} elseif ( ( strpos( $asset, '%' ) !== false ) || ( strpos( $asset, '_' ) !== false ) ) {
						$itemsWildcards[]		=	$asset;
					} else {
						$itemsAssets[]			=	$asset;
					}
				}

				$itemsAssets					=	array_unique( $itemsAssets );
				$itemsWildcards					=	array_unique( $itemsWildcards );

				if ( $itemsAssets || $itemsWildcards ) {
					$itemsAssetsWhere			=	array();

					if ( $itemsAssets ) {
						$itemsAssetsWhere[]		=	"b." . $_CB_database->NameQuote( 'asset' ) . ( count( $itemsAssets ) > 1 ? " IN " . $_CB_database->safeArrayOfStrings( $itemsAssets ) : " = " . $_CB_database->Quote( $itemsAssets[0] ) );
					}

					if ( $itemsWildcards ) {
						foreach ( $itemsWildcards as $wildcard ) {
							$itemsAssetsWhere[]	=	"b." . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( $wildcard );
						}
					}

					$itemsWhere[]				=	( count( $itemsAssetsWhere ) > 1 ? "( " . implode( " OR ", $itemsAssetsWhere ) . " )" : $itemsAssetsWhere[0] );
				} else {
					$itemsDisable				=	true;
				}
			}

			if ( $this->types() && ( count( $this->types() ) < 4 ) ) {
				$itemsWhere[]					=	"b." . $_CB_database->NameQuote( 'type' ) . " IN " . $_CB_database->safeArrayOfStrings( $this->types() );
			}

			$itemsWhere[]						=	"b." . $_CB_database->NameQuote( 'folder' ) . " = a." . $_CB_database->NameQuote( 'id' );

			if ( ( $this->get( 'published', null, GetterInterface::RAW ) !== '' ) && ( $this->get( 'published', null, GetterInterface::RAW ) !== null ) ) {
				if ( ( $this->get( 'published', null, GetterInterface::INT ) == 1 ) && Application::MyUser()->getUserId() ) {
					$itemsWhere[]				=	"( b." . $_CB_database->NameQuote( 'published' ) . " = 1"
												.	" OR b." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) Application::MyUser()->getUserId() . " )";
				} else {
					$itemsWhere[]				=	"b." . $_CB_database->NameQuote( 'published' ) . " = " . $this->get( 'published', null, GetterInterface::INT );
				}
			}

			$_PLUGINS->trigger( 'gallery_onQueryFolderItems', array( $output, &$itemsSelect, &$itemsWhere, &$itemsJoin, &$this ) );

			$items								=	'SELECT ' . implode( ', ', $itemsSelect )
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' ) . " AS b"
												.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cbb"
												.	" ON cbb." . $_CB_database->NameQuote( 'id' ) . " = b." . $_CB_database->NameQuote( 'user_id' )
												.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS jb"
												.	" ON jb." . $_CB_database->NameQuote( 'id' ) . " = cbb." . $_CB_database->NameQuote( 'id' )
												.	( $itemsJoin ? "\n " . implode( "\n ", $itemsJoin ) : null )
												.	"\n WHERE cbb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
												.	"\n AND cbb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
												.	"\n AND jb." . $_CB_database->NameQuote( 'block' ) . " = 0"
												.	( $itemsWhere ? "\n AND " . implode( "\n AND ", $itemsWhere ) : null );

			$select[]							=	'( ' . ( $itemsDisable ? 0 : $items ) . ' ) AS _items';
		}

		if ( $hasId ) {
			if ( is_array( $this->get( 'id', null, GetterInterface::RAW ) ) ) {
				$where[]						=	"a." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $id );
			} else {
				$where[]						=	"a." . $_CB_database->NameQuote( 'id' ) . " = " . (int) $id;
			}
		}

		$userId									=	$this->get( 'user_id', null, GetterInterface::RAW );

		if ( ( ( $userId !== '' ) && ( $userId !== null ) ) || ( is_array( $userId ) && $userId ) ) {
			if ( is_array( $userId ) ) {
				$where[]						=	"a." . $_CB_database->NameQuote( 'user_id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $userId );
			} else {
				$where[]						=	"a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $userId;
			}
		}

		if ( $this->assets() && ( ! in_array( 'all', $this->assets() ) ) )  {
			$assets								=	array();
			$wildcards							=	array();

			foreach ( $this->assets() as $asset ) {
				if ( strpos( $asset, 'connections' ) !== false ) {
					if ( preg_match( '/^profile\.(\d+)\.connections/', $asset, $matches ) ) {
						$profileId				=	(int) $matches[1];
					} else {
						$profileId				=	$this->user()->get( 'id', 0, GetterInterface::INT );
					}

					if ( $profileId ) {
						if ( strpos( $asset, 'connectionsonly' ) === false ) {
							$assets[]			=	'profile.' . $profileId;
						}

						foreach( CBGallery::getConnections( $profileId ) as $connection ) {
							$assets[]			=	'profile.' . (int) $connection->id;
						}
					}
				} elseif ( ( strpos( $asset, '%' ) !== false ) || ( strpos( $asset, '_' ) !== false ) ) {
					$wildcards[]				=	$asset;
				} else {
					$assets[]					=	$asset;
				}
			}

			$assets								=	array_unique( $assets );
			$wildcards							=	array_unique( $wildcards );

			if ( $assets || $wildcards ) {
				$assetsWhere					=	array();

				if ( $assets ) {
					$assetsWhere[]				=	"a." . $_CB_database->NameQuote( 'asset' ) . ( count( $assets ) > 1 ? " IN " . $_CB_database->safeArrayOfStrings( $assets ) : " = " . $_CB_database->Quote( $assets[0] ) );
				}

				if ( $wildcards ) {
					foreach ( $wildcards as $wildcard ) {
						$assetsWhere[]			=	"a." . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( $wildcard );
					}
				}

				$where[]						=	( count( $assetsWhere ) > 1 ? "( " . implode( " OR ", $assetsWhere ) . " )" : $assetsWhere[0] );
			} else {
				if ( $output == 'count' ) {
					return 0;
				} else {
					return array();
				}
			}
		}

		if ( ! $hasId ) {
			if ( $this->get( 'title', null, GetterInterface::STRING ) != ''  ) {
				if ( strpos( $this->get( 'title', null, GetterInterface::STRING ), '%' ) !== false ) {
					$where[]					=	"a." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( $this->get( 'title', null, GetterInterface::STRING ) );
				} else {
					$where[]					=	"a." . $_CB_database->NameQuote( 'title' ) . " = " . $_CB_database->Quote( $this->get( 'title', null, GetterInterface::STRING ) );
				}
			}

			if ( $this->get( 'description', null, GetterInterface::STRING ) != ''  ) {
				if ( strpos( $this->get( 'description', null, GetterInterface::STRING ), '%' ) !== false ) {
					$where[]					=	"a." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( $this->get( 'description', null, GetterInterface::STRING ) );
				} else {
					$where[]					=	"a." . $_CB_database->NameQuote( 'description' ) . " = " . $_CB_database->Quote( $this->get( 'description', null, GetterInterface::STRING ) );
				}
			}

			if ( ( ! $hasId ) && $this->get( 'folders_search', true, GetterInterface::BOOLEAN ) ) {
				if ( $this->get( 'search', null, GetterInterface::STRING ) != '' ) {
					$where[]					=	"( a." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false )
												.	" OR a." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false ) . " )";
				}
			}
		}

		if ( ( $this->get( 'published', null, GetterInterface::RAW ) !== '' ) && ( $this->get( 'published', null, GetterInterface::RAW ) !== null ) ) {
			if ( ( $this->get( 'published', null, GetterInterface::INT ) == 1 ) && Application::MyUser()->getUserId() ) {
				$where[]						=	"( a." . $_CB_database->NameQuote( 'published' ) . " = 1"
												.	" OR a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) Application::MyUser()->getUserId() . " )";
			} else {
				$where[]						=	"a." . $_CB_database->NameQuote( 'published' ) . " = " . $this->get( 'published', null, GetterInterface::INT );
			}
		}

		$_PLUGINS->trigger( 'gallery_onQueryFolders', array( $output, &$select, &$join, &$where, &$this ) );

		$query									=	'SELECT ' . implode( ', ', $select )
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_folders' ) . " AS a"
												.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
												.	" ON cb." . $_CB_database->NameQuote( 'id' ) . " = a." . $_CB_database->NameQuote( 'user_id' )
												.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
												.	" ON j." . $_CB_database->NameQuote( 'id' ) . " = cb." . $_CB_database->NameQuote( 'id' )
												.	( $join ? "\n " . implode( "\n ", $join ) : null )
												.	"\n WHERE cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
												.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
												.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0"
												.	( $where ? "\n AND " . implode( "\n AND ", $where ) : null );

		if ( $output != 'count' ) {
			$orderBy							=	$this->get( 'folders_orderby', 'date_desc', GetterInterface::STRING );

			if ( ! $orderBy ) {
				$orderBy						=	'date_desc';
			}

			if ( $orderBy == 'random' ) {
				$query							.=	"\n ORDER BY RAND( " . $_CB_database->Quote( Application::Session()->get( 'gallery.random.' . $this->id() . '.folders', rand(), GetterInterface::STRING ) ) . " )";
			} else {
				$orderBy						=	explode( '_', $orderBy );

				$query							.=	"\n ORDER BY a." . $_CB_database->NameQuote( $orderBy[0] ) . ( $orderBy[1] == 'asc' ? " ASC" : ( $orderBy[1] == 'desc' ? " DESC" : null ) );
			}
		}

		$paging									=	( ( ! $hasId ) && $this->get( 'folders_paging_limit', 15, GetterInterface::INT ) && ( $output != 'all' ) );
		$cacheId								=	md5( $query . ( $output ? $output : ( $paging ? $this->get( 'folders_paging_limitstart', 0, GetterInterface::INT ) . $this->get( 'folders_paging_limit', 15, GetterInterface::INT ) : null ) ) );

		if ( ( ! isset( $cache[$cacheId] ) ) || ( ( ( $output == 'count' ) && $this->clearFolderCount ) || $this->clearFolderSelect ) ) {
			if ( $output == 'count' ) {
				$this->clearFolderCount			=	false;

				$_CB_database->setQuery( $query );

				$cache[$cacheId]				=	(int) $_CB_database->loadResult();
			} else {
				$this->clearFolderSelect		=	false;

				if ( $paging ) {
					$_CB_database->setQuery( $query, $this->get( 'folders_paging_limitstart', 0, GetterInterface::INT ), $this->get( 'folders_paging_limit', 15, GetterInterface::INT ) );
				} else {
					$_CB_database->setQuery( $query );
				}

				$rows							=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Gallery\Table\FolderTable', array( $_CB_database ) );
				$rowsCount						=	count( $rows );
				$userIds						=	array();

				/** @var FolderTable[] $rows */
				foreach ( $rows as $row ) {
					if ( preg_match( '/^profile\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
						$userIds[]				=	(int) $matches[1];
					}

					$userIds[]					=	$row->get( 'user_id', 0, GetterInterface::INT );
				}

				if ( $userIds ) {
					\CBuser::advanceNoticeOfUsersNeeded( $userIds );
				}

				$_PLUGINS->trigger( 'gallery_onLoadFolders', array( &$rows, $this ) );

				if ( $rows ) {
					self::$loadedFolders		=	( self::$loadedFolders + $rows );
				}

				if ( $paging && $rowsCount && ( count( $rows ) < round( $rowsCount / 1.25 ) ) ) {
					$limitCache					=	$this->get( 'folders_paging_limit', 15, GetterInterface::INT );
					$nextLimit					=	( $limitCache - count( $rows ) );

					if ( $nextLimit <= 0 ) {
						$nextLimit				=	1;
					}

					$this->set( 'folders_paging_limitstart', ( $this->get( 'folders_paging_limitstart', 0, GetterInterface::INT ) + $limitCache ) );
					$this->set( 'folders_paging_limit', $nextLimit );

					$cache[$cacheId]			=	( $rows + $this->folders( $output ) );

					$this->set( 'folders_paging_limit', $limitCache );
				} else {
					$cache[$cacheId]			=	$rows;
				}
			}
		}

		return $cache[$cacheId];
	}

	/**
	 * Retrieves gallery folder row
	 *
	 * @param int $id
	 * @return FolderTable
	 */
	public function folder( $id )
	{
		if ( ! $id ) {
			return new FolderTable();
		}

		if ( isset( self::$loadedFolders[$id] ) ) {
			return self::$loadedFolders[$id];
		}

		static $cache		=	array();

		if ( ! isset( $cache[$id] ) ) {
			$folders		=	$this->reset()->setId( $id )->folders();

			if ( isset( $folders[$id] ) ) {
				$folder		=	$folders[$id];
			} else {
				$folder		=	new FolderTable();
			}

			$cache[$id]		=	$folder;
		}

		return $cache[$id];
	}

	/**
	 * Retrieves gallery item rows or row count
	 *
	 * @param string $output
	 * @return ItemTable[]|int
	 */
	public function items( $output = null )
	{
		global $_CB_database, $_PLUGINS;

		static $cache							=	array();

		$id										=	$this->get( 'id', null, GetterInterface::RAW );
		$hasId									=	( ( ( $id !== '' ) && ( $id !== null ) ) || ( is_array( $id ) && $id ) );

		$select									=	array();
		$join									=	array();
		$where									=	array();

		if ( $output == 'count' ) {
			$select[]							=	'COUNT( a.' . $_CB_database->NameQuote( 'id' ) . ' )';
		} else {
			$select[]							=	'a.*';
		}

		if ( $hasId ) {
			if ( is_array( $this->get( 'id', null, GetterInterface::RAW ) ) ) {
				$where[]						=	"a." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $id );
			} else {
				$where[]						=	"a." . $_CB_database->NameQuote( 'id' ) . " = " . (int) $id;
			}
		}

		$userId									=	$this->get( 'user_id', null, GetterInterface::RAW );

		if ( ( ( $userId !== '' ) && ( $userId !== null ) ) || ( is_array( $userId ) && $userId ) ) {
			if ( is_array( $userId ) ) {
				$where[]						=	"a." . $_CB_database->NameQuote( 'user_id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $userId );
			} else {
				$where[]						=	"a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $userId;
			}
		}

		if ( $this->assets() && ( ! in_array( 'all', $this->assets() ) ) )  {
			$assets								=	array();
			$wildcards							=	array();

			foreach ( $this->assets() as $asset ) {
				if ( strpos( $asset, 'connections' ) !== false ) {
					if ( preg_match( '/^profile\.(\d+)\.connections/', $asset, $matches ) ) {
						$profileId				=	(int) $matches[1];
					} else {
						$profileId				=	$this->user()->get( 'id', 0, GetterInterface::INT );
					}

					if ( $profileId ) {
						if ( strpos( $asset, 'connectionsonly' ) === false ) {
							$assets[]			=	'profile.' . $profileId;
						}

						foreach( CBGallery::getConnections( $profileId ) as $connection ) {
							$assets[]			=	'profile.' . (int) $connection->id;
						}
					}
				} elseif ( ( strpos( $asset, '%' ) !== false ) || ( strpos( $asset, '_' ) !== false ) ) {
					$wildcards[]				=	$asset;
				} else {
					$assets[]					=	$asset;
				}
			}

			$assets								=	array_unique( $assets );
			$wildcards							=	array_unique( $wildcards );

			if ( $assets || $wildcards ) {
				$assetsWhere					=	array();

				if ( $assets ) {
					$assetsWhere[]				=	"a." . $_CB_database->NameQuote( 'asset' ) . ( count( $assets ) > 1 ? " IN " . $_CB_database->safeArrayOfStrings( $assets ) : " = " . $_CB_database->Quote( $assets[0] ) );
				}

				if ( $wildcards ) {
					foreach ( $wildcards as $wildcard ) {
						$assetsWhere[]			=	"a." . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( $wildcard );
					}
				}

				$where[]						=	( count( $assetsWhere ) > 1 ? "( " . implode( " OR ", $assetsWhere ) . " )" : $assetsWhere[0] );
			} else {
				if ( $output == 'count' ) {
					return 0;
				} else {
					return array();
				}
			}
		}

		$type									=	$this->get( 'type', null, GetterInterface::RAW );

		if ( $type ) {
			if ( is_array( $type ) ) {
				$allowedTypes					=	array();

				foreach ( $type as $allowedType ) {
					if ( in_array( $allowedType, $this->types() ) ) {
						$allowedTypes[]			=	$allowedType;
					}
				}

				if ( ! $allowedTypes ) {
					if ( $output == 'count' ) {
						return 0;
					} else {
						return array();
					}
				}

				$where[]						=	"a." . $_CB_database->NameQuote( 'type' ) . " IN " . $_CB_database->safeArrayOfStrings( $allowedTypes );
			} else {
				if ( ! in_array( $type, $this->types() ) ) {
					if ( $output == 'count' ) {
						return 0;
					} else {
						return array();
					}
				}

				$where[]						=	"a." . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $type );
			}
		} else {
			if ( $this->types() && ( count( $this->types() ) < 4 ) ) {
				$where[]						=	"a." . $_CB_database->NameQuote( 'type' ) . " IN " . $_CB_database->safeArrayOfStrings( $this->types() );
			}
		}

		$foldersWhere							=	null;
		$pagingPrefix							=	null;

		if ( $this->get( 'folders', true, GetterInterface::BOOLEAN ) ) {
			$folder								=	$this->get( 'folder', null, GetterInterface::RAW );

			if ( ( ( $folder !== '' ) && ( $folder !== null ) ) || ( is_array( $folder ) && $folder ) ) {
				if ( is_array( $folder ) ) {
					$foldersWhere				=	"a." . $_CB_database->NameQuote( 'folder' ) . " IN " . $_CB_database->safeArrayOfIntegers( $folder );
				} else {
					$foldersWhere				=	"a." . $_CB_database->NameQuote( 'folder' ) . " = " . (int) $folder;

					if ( $folder !== 0 ) {
						$pagingPrefix			=	'folders_';
					}
				}
			}
		}

		if ( ! $hasId ) {
			if ( $this->get( 'value', null, GetterInterface::STRING ) != '' ) {
				if ( strpos( $this->get( 'value', null, GetterInterface::STRING ), '%' ) !== false ) {
					$where[]					=	"a." . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( $this->get( 'value', null, GetterInterface::STRING ) );
				} else {
					$where[]					=	"a." . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $this->get( 'value', null, GetterInterface::STRING ) );
				}
			}

			if ( $this->get( 'file', null, GetterInterface::STRING ) != '' ) {
				if ( strpos( $this->get( 'file', null, GetterInterface::STRING ), '%' ) !== false ) {
					$where[]					=	"a." . $_CB_database->NameQuote( 'file' ) . " LIKE " . $_CB_database->Quote( $this->get( 'file', null, GetterInterface::STRING ) );
				} else {
					$where[]					=	"a." . $_CB_database->NameQuote( 'file' ) . " = " . $_CB_database->Quote( $this->get( 'file', null, GetterInterface::STRING ) );
				}
			}

			if ( $this->get( 'title', null, GetterInterface::STRING ) != '' ) {
				if ( strpos( $this->get( 'title', null, GetterInterface::STRING ), '%' ) !== false ) {
					$where[]					=	"a." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( $this->get( 'title', null, GetterInterface::STRING ) );
				} else {
					$where[]					=	"a." . $_CB_database->NameQuote( 'title' ) . " = " . $_CB_database->Quote( $this->get( 'title', null, GetterInterface::STRING ) );
				}
			}

			if ( $this->get( 'description', null, GetterInterface::STRING ) != '' ) {
				if ( strpos( $this->get( 'description', null, GetterInterface::STRING ), '%' ) !== false ) {
					$where[]					=	"a." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( $this->get( 'description', null, GetterInterface::STRING ) );
				} else {
					$where[]					=	"a." . $_CB_database->NameQuote( 'description' ) . " = " . $_CB_database->Quote( $this->get( 'description', null, GetterInterface::STRING ) );
				}
			}

			if ( ( ! $hasId ) && $this->get( $pagingPrefix . 'items_search', true, GetterInterface::BOOLEAN ) ) {
				if ( $this->get( 'search', null, GetterInterface::STRING ) != '' ) {
					$where[]					=	"( a." . $_CB_database->NameQuote( 'file' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false )
												.	" OR a." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false )
												.	" OR a." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false )
												.	" OR a." . $_CB_database->NameQuote( 'date' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false ) . " )";
				}
			}
		}

		if ( $foldersWhere ) {
			$where[]							=	$foldersWhere;
		}

		if ( ( $this->get( 'published', null, GetterInterface::RAW ) !== '' ) && ( $this->get( 'published', null, GetterInterface::RAW ) !== null ) ) {
			if ( ( $this->get( 'published', null, GetterInterface::INT ) == 1 ) && Application::MyUser()->getUserId() ) {
				$where[]						=	"( a." . $_CB_database->NameQuote( 'published' ) . " = 1"
												.	" OR a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) Application::MyUser()->getUserId() . " )";
			} else {
				$where[]						=	"a." . $_CB_database->NameQuote( 'published' ) . " = " . $this->get( 'published', null, GetterInterface::INT );
			}
		}

		$_PLUGINS->trigger( 'gallery_onQueryItems', array( $output, &$select, &$join, &$where, &$this ) );

		$query									=	'SELECT ' . implode( ', ', $select )
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' ) . " AS a"
												.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
												.	" ON cb." . $_CB_database->NameQuote( 'id' ) . " = a." . $_CB_database->NameQuote( 'user_id' )
												.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
												.	" ON j." . $_CB_database->NameQuote( 'id' ) . " = cb." . $_CB_database->NameQuote( 'id' )
												.	( $join ? "\n " . implode( "\n ", $join ) : null )
												.	"\n WHERE cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
												.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
												.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0"
												.	( $where ? "\n AND " . implode( "\n AND ", $where ) : null );

		if ( $output != 'count' ) {
			$orderBy							=	$this->get( $pagingPrefix . 'items_orderby', 'date_desc', GetterInterface::STRING );

			if ( ! $orderBy ) {
				$orderBy						=	'date_desc';
			}

			if ( $orderBy == 'random' ) {
				$query							.=	"\n ORDER BY RAND( " . $_CB_database->Quote( Application::Session()->get( 'gallery.random.' . $this->id() . '.' . $pagingPrefix . 'items', rand(), GetterInterface::STRING ) ) . " )";
			} else {
				$orderBy						=	explode( '_', $orderBy );

				$query							.=	"\n ORDER BY a." . $_CB_database->NameQuote( $orderBy[0] ) . ( $orderBy[1] == 'asc' ? " ASC" : ( $orderBy[1] == 'desc' ? " DESC" : null ) );
			}
		}

		$paging									=	( ( ! $hasId ) && $this->get( $pagingPrefix . 'items_paging_limit', 15, GetterInterface::INT ) && ( $output != 'all' ) );
		$cacheId								=	md5( $query . ( $output ? $output : ( $paging ? $this->get( $pagingPrefix . 'items_paging_limitstart', 0, GetterInterface::INT ) . $this->get( $pagingPrefix . 'items_paging_limit', 15, GetterInterface::INT ) : null ) ) );

		if ( ( ! isset( $cache[$cacheId] ) ) || ( ( ( $output == 'count' ) && $this->clearItemCount ) || $this->clearItemSelect ) ) {
			if ( $output == 'count' ) {
				$this->clearItemCount			=	false;

				$_CB_database->setQuery( $query );

				$cache[$cacheId]				=	(int) $_CB_database->loadResult();
			} else {
				$this->clearItemSelect			=	false;

				if ( $paging ) {
					$_CB_database->setQuery( $query, $this->get( $pagingPrefix . 'items_paging_limitstart', 0, GetterInterface::INT ), $this->get( $pagingPrefix . 'items_paging_limit', 15, GetterInterface::INT ) );
				} else {
					$_CB_database->setQuery( $query );
				}

				$rows							=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Gallery\Table\ItemTable', array( $_CB_database ) );
				$rowsCount						=	count( $rows );
				$userIds						=	array();

				/** @var ItemTable[] $rows */
				foreach ( $rows as $row ) {
					if ( preg_match( '/^profile\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
						$userIds[]				=	(int) $matches[1];
					}

					$userIds[]					=	$row->get( 'user_id', 0, GetterInterface::INT );
				}

				if ( $userIds ) {
					\CBuser::advanceNoticeOfUsersNeeded( $userIds );
				}

				$_PLUGINS->trigger( 'gallery_onLoadItems', array( &$rows, $this ) );

				if ( $rows ) {
					self::$loadedItems			=	( self::$loadedItems + $rows );
				}

				if ( $paging && $rowsCount && ( count( $rows ) < $rowsCount ) ) {
					$limitCache					=	$this->get( $pagingPrefix . 'items_paging_limit', 15, GetterInterface::INT );
					$nextLimit					=	( $limitCache - count( $rows ) );

					if ( $nextLimit <= 0 ) {
						$nextLimit				=	1;
					}

					$this->set( $pagingPrefix . 'items_paging_limitstart', ( $this->get( $pagingPrefix . 'items_paging_limitstart', 0, GetterInterface::INT ) + $limitCache ) );
					$this->set( $pagingPrefix . 'items_paging_limit', $nextLimit );

					$cache[$cacheId]			=	( $rows + $this->items( $output ) );

					$this->set( $pagingPrefix . 'items_paging_limit', $limitCache );
				} else {
					$cache[$cacheId]			=	$rows;
				}
			}
		}

		return $cache[$cacheId];
	}

	/**
	 * Retrieves gallery item row
	 *
	 * @param int $id
	 * @return ItemTable
	 */
	public function item( $id )
	{
		if ( ! $id ) {
			return new ItemTable();
		}

		if ( isset( self::$loadedItems[$id] ) ) {
			return self::$loadedItems[$id];
		}

		static $cache		=	array();

		if ( ! isset( $cache[$id] ) ) {
			$items			=	$this->reset()->setId( $id )->items();

			if ( isset( $items[$id] ) ) {
				$item		=	$items[$id];
			} else {
				$item		=	new ItemTable();
			}

			$cache[$id]		=	$item;
		}

		return $cache[$id];
	}

	/**
	 * Outputs gallery HTML
	 *
	 * @return string
	 */
	public function gallery()
	{
		global $_CB_framework, $_PLUGINS;

		static $plugin		=	null;

		if ( ! $plugin ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' );
		}

		if ( ! $plugin ) {
			return null;
		}

		if ( ! class_exists( 'CBplug_cbgallery' ) ) {
			$component		=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/user/plug_cbgallery/component.cbgallery.php';

			if ( file_exists( $component ) ) {
				include_once( $component );
			}
		}

		$this->cache();

		ob_start();
		$pluginArguements	=	array( &$this );

		$_PLUGINS->call( $plugin->id, 'getGallery', 'CBplug_cbgallery', $pluginArguements );
		$return				=	ob_get_contents();
		ob_end_clean();

		return $return;
	}

	/**
	 * Returns an array of the galleries variables
	 *
	 * @return array
	 */
	public function asArray()
	{
		$params				=	parent::asArray();

		if ( isset( $params['folders_paging_limitstart'] ) ) {
			unset( $params['folders_paging_limitstart'] );
		}

		if ( isset( $params['folders_items_paging_limitstart'] ) ) {
			unset( $params['folders_items_paging_limitstart'] );
		}

		if ( isset( $params['items_paging_limitstart'] ) ) {
			unset( $params['items_paging_limitstart'] );
		}

		if ( isset( $params['search'] ) ) {
			unset( $params['search'] );
		}

		$params['assets']	=	$this->assets();
		$params['user']		=	$this->user()->get( 'id', 0, GetterInterface::INT );

		return $params;
	}

	/**
	 * Caches the gallery into session; this is normally only done on creation or parse to preserve parameters between loads
	 * It is not advised to call this manually unless gallery parameters have changed after creation and desired result is for them to persist
	 *
	 * @return self
	 */
	public function cache()
	{
		$newId				=	md5( self::asJson() );

		if ( $this->id() != $newId ) {
			$session		=	Application::Session();
			$galleries		=	$session->subTree( 'gallery' );

			if ( $this->id() ) {
				$galleries->set( $this->id(), $newId );
			}

			$this->id		=	$newId;
			$this->ini		=	$this->asArray();

			$galleries->set( $this->id(), $this->ini );

			$session->set( 'gallery', $galleries->asArray() );
		}

		return $this;
	}
}