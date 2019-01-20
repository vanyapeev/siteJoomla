<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CBLib\Application\Application;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Gallery\GalleryInterface;
use CB\Plugin\Gallery\Table\FolderTable;
use CB\Plugin\Gallery\Table\ItemTable;
use CB\Database\Table\FieldTable;
use CBLib\Image\Image;
use CBLib\Registry\Registry;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

class CBplug_cbgallery extends cbPluginHandler
{

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		$this->getGallery();
	}

	/**
	 * Loads in a gallery directly or by URL
	 *
	 * @param null|Gallery $gallery
	 * @param null|string  $view
	 */
	public function getGallery( $gallery = null, $view = null )
	{
		global $_CB_framework, $_PLUGINS;

		$viewer								=	CBuser::getMyUserDataInstance();
		$raw								=	false;
		$menu								=	null;
		$inline								=	false;
		$access								=	true;

		$galleryLoaded						=	false;

		if ( $gallery ) {
			if ( $gallery instanceof GalleryInterface ) {
				$action						=	'gallery';
			} else {
				return;
			}

			$function						=	( $view ? $view : 'show' );

			if ( strpos( $function, '.' ) !== false ) {
				list( $action, $function )	=	explode( '.', $function, 2 );
			}

			$id								=	0;
			$inline							=	true;
			$galleryLoaded					=	true;
		} else {
			$raw							=	( $this->input( 'format', null, GetterInterface::STRING ) == 'raw' );
			$action							=	$this->input( 'action', null, GetterInterface::STRING );

			if ( strpos( $action, '.' ) !== false ) {
				list( $action, $function )	=	explode( '.', $action, 2 );
			} else {
				$function					=	$this->input( 'func', null, GetterInterface::STRING );
			}

			$id								=	$this->input( 'id', 0, GetterInterface::INT );

			// TODO: For B/C: remove in next major release
			if ( ( $action == 'items' ) && ( ( ! $function ) || in_array( $function, array( 'preview', 'show' ) ) ) ) {
				$action						=	'item';
			}

			$galleryId						=	$this->input( 'gallery', null, GetterInterface::STRING );
			$galleryAsset					=	null;

			if ( ! $galleryId ) {
				if ( $id && in_array( $action, array( 'item', 'folder' ) ) ) {
					$galleryAsset			=	CBGallery::getAsset( $action, $id );
				}

				$menu						=	JFactory::getApplication()->getMenu()->getActive();

				if ( $menu && isset( $menu->id ) && ( ! $galleryAsset ) ) {
					$galleryAsset			=	$menu->params->get( 'gallery_asset' );

					if ( ( ! $galleryAsset ) && ( $action == 'approval' ) ) {
						$galleryAsset		=	'all';
					}
				}
			}

			$gallery						=	new Gallery( $galleryAsset, $viewer );

			if ( $menu && isset( $menu->id ) ) {
				$galleryLoaded				=	true;

				$gallery->set( 'menu', (int) $menu->id );

				if ( $function == 'new' ) {
					$gallery->set( 'location', 'plugin' );
				} else {
					$gallery->set( 'location', JRoute::_( $menu->link, false ) );
				}

				$gallery->parse( $menu->params->toArray(), 'gallery_' );
			}

			if ( $galleryId ) {
				if ( $gallery->load( $galleryId ) ) {
					$galleryLoaded			=	true;
				} elseif ( $id && in_array( $action, array( 'item', 'folder' ) ) ) {
					$galleryAsset			=	CBGallery::getAsset( $action, $id );

					if ( $galleryAsset ) {
						$gallery->assets( $galleryAsset );
					} else {
						$access				=	false;
					}
				} else {
					$access					=	false;
				}
			} elseif ( $function && ( ! in_array( $function, array( 'show', 'new', 'preview', 'display', 'download' ) ) ) ) {
				$access						=	false;
			}
		}

		if ( ! $gallery->asset() ) {
			$access							=	false;
		} elseif ( preg_match( '/^profile(?:\.(\d+)(?:\.field\.(\d+))?)?/', $gallery->asset(), $matches ) || $gallery->get( 'tab', 0, GetterInterface::INT ) || $gallery->get( 'field', 0, GetterInterface::INT ) ) {
			$profileId						=	( isset( $matches[1] ) ? (int) $matches[1] : $gallery->user()->get( 'id', 0, GetterInterface::INT ) );
			$fieldId						=	( isset( $matches[2] ) ? (int) $matches[2] : $gallery->get( 'field', 0, GetterInterface::INT ) );
			$tabId							=	$gallery->get( 'tab', 0, GetterInterface::INT );

			if ( $profileId != $gallery->user()->get( 'id', 0, GetterInterface::INT ) ) {
				$gallery->user( $profileId );
			}

			if ( $fieldId ) {
				$field						=	CBGallery::getField( $fieldId, $profileId );

				if ( ! $field ) {
					$access					=	false;
				} elseif ( ! $galleryLoaded ) {
					$galleryLoaded			=	true;

					$gallery->set( 'field', $field->get( 'fieldid', 0, GetterInterface::INT ) );

					$gallery->parse( $field->params, 'gallery_' );
				}
			} else {
				$tab						=	CBGallery::getTab( $tabId, $profileId );

				if ( ! $tab ) {
					if ( ! in_array( 'all', $gallery->assets() ) ) {
						$access				=	false;
					}
				} elseif ( ! $galleryLoaded ) {
					$galleryLoaded			=	true;

					$gallery->set( 'tab', $tab->get( 'tabid', 0, GetterInterface::INT ) );

					$gallery->parse( $tab->params, 'gallery_' );
				}
			}
		}

		$_PLUGINS->trigger( 'gallery_onGalleryAccess', array( &$gallery, &$access, $galleryLoaded ) );

		if ( ! $access ) {
			if ( $inline ) {
				return;
			} elseif ( $raw ) {
				header( 'HTTP/1.0 401 Unauthorized' );
				exit();
			} else {
				CBGallery::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
			}
		}

		if ( ! $gallery->id() ) {
			$gallery->cache();
		}

		if ( ! $raw ) {
			outputCbJs();
			outputCbTemplate();

			ob_start();
		}

		switch ( $action ) {
			case 'items':
				switch ( $function ) {
					case 'new':
						$this->showItemsNew( $viewer, $gallery );
						break;
				}
				break;
			case 'item':
				switch ( $function ) {
					case 'download':
						$this->outputItem( 'download', $id, $viewer, $gallery );
						break;
					case 'edit':
						$this->showItemEdit( $id, $viewer, $gallery );
						break;
					case 'new':
						$this->showItemEdit( null, $viewer, $gallery );
						break;
					case 'save':
						cbSpoofCheck( 'plugin', ( $raw ? 'REQUEST' : 'POST' ) );
						$this->saveItemEdit( $id, $viewer, $gallery, ( $raw ? 'ajax' : null ) );
						break;
					case 'publish':
						$this->stateItem( 1, $id, $viewer, $gallery );
						break;
					case 'unpublish':
						$this->stateItem( 0, $id, $viewer, $gallery );
						break;
					case 'rotate':
						$this->rotateItem( $id, $viewer, $gallery );
						break;
					case 'avatar':
					case 'canvas':
						$this->saveItemField( $function, $id, $viewer, $gallery );
						break;
					case 'delete':
						$this->deleteItem( $id, $viewer, $gallery, ( $raw ? 'ajax' : null ) );
						break;
					case 'display':
						$this->showItem( $id, $viewer, $gallery );
						break;
					case 'preview':
						$this->outputItem( 'thumbnail', $id, $viewer, $gallery );
						break;
					case 'show':
					default:
						$this->outputItem( 'full', $id, $viewer, $gallery );
						break;
				}
				break;
			case 'folder':
				if ( ! $gallery->get( 'folders', true, GetterInterface::BOOLEAN ) ) {
					CBGallery::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
				}

				switch ( $function ) {
					case 'cover':
						$this->saveFolderCover( $id, $viewer, $gallery );
						break;
					case 'edit':
						$this->showFolderEdit( $id, $viewer, $gallery );
						break;
					case 'new':
						$this->showFolderEdit( null, $viewer, $gallery );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveFolderEdit( $id, $viewer, $gallery );
						break;
					case 'publish':
						$this->stateFolder( 1, $id, $viewer, $gallery );
						break;
					case 'unpublish':
						$this->stateFolder( 0, $id, $viewer, $gallery );
						break;
					case 'delete':
						$this->deleteFolder( $id, $viewer, $gallery );
						break;
					case 'show':
					default:
						$gallery->set( 'folder', $id );

						$this->showItems( $viewer, $gallery );
						break;
				}
				break;
			case 'gallery':
			case 'approval':
			default:
				$this->showItems( $viewer, $gallery, $inline );
				break;
		}

		if ( ! $raw ) {
			$html							=	ob_get_contents();
			ob_end_clean();

			if ( ! $html ) {
				return;
			}

			$class							=	$this->params->get( 'general_class', null, GetterInterface::STRING );

			$return							=	'<div class="cbGallery' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
											.		$html
											.	'</div>';

			echo $return;

			if ( $menu && isset( $menu->id ) ) {
				$_CB_framework->setMenuMeta();
			}
		}
	}

	/**
	 * Outputs a JSON ajax response
	 *
	 * @param bool        $status
	 * @param null|string $mssage
	 */
	private function ajaxResponse( $status, $mssage = null )
	{
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-Type: application/json' );

		while ( @ob_end_clean() );

		echo json_encode( array( 'status' => $status, 'message' => $mssage ) );

		exit();
	}

	/**
	 * Displays items gallery page
	 *
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 * @param bool      $inline
	 */
	public function showItems( $viewer, $gallery, $inline = false )
	{
		global $_CB_framework;

		$canModerate						=	CBGallery::canModerate( $gallery );

		CBGallery::getTemplate( array( 'gallery', 'folder', 'folders', 'folder_container', 'items', 'item_container' ) );

		$output								=	null;

		if ( ! $canModerate ) {
			$gallery->set( 'published', 1 );
		} elseif ( $this->input( 'action', null, GetterInterface::STRING ) == 'approval' ) {
			$output							=	'approval';

			$gallery->set( 'location', $_CB_framework->pluginClassUrl( 'cbgallery', false, array( 'action' => 'approval' ) ) );
			$gallery->set( 'published', -1 );
		}

		$folderId							=	$this->input( 'folder', $gallery->get( 'folder', 0, GetterInterface::INT ), GetterInterface::INT );

		if ( $folderId && ( $gallery->get( 'published', null, GetterInterface::INT ) != -1 ) ) {
			$gallery->set( 'folder', $folderId );
		}

		$galleryPrefix						=	'gallery_' . $gallery->id() . '_' . ( $folderId ? $folderId . '_' : null ) . ( ( $gallery->get( 'published', null, GetterInterface::INT ) == -1 ) ? 'approval_' : null );
		$gallerySearch						=	$_CB_framework->getUserStateFromRequest( $galleryPrefix . 'search{com_comprofiler}', $galleryPrefix . 'search' );
		$searching							=	false;

		if ( $gallerySearch != '' ) {
			$searching						=	true;

			$gallery->set( 'search', $gallerySearch );
		}

		$folder								=	null;
		$folders							=	null;
		$foldersPageNav						=	null;
		$pagingPrefix						=	null;
		$input								=	array();

		// Folders:
		if ( $gallery->get( 'folders', true, GetterInterface::BOOLEAN ) ) {
			if ( ! $gallery->get( 'folder', 0, GetterInterface::INT ) ) {
				$gallery->set( 'folder', 0 );

				$foldersPrefix				=	$galleryPrefix . 'folders_';
				$foldersLimitstart			=	(int) $_CB_framework->getUserStateFromRequest( $foldersPrefix . 'limitstart{com_comprofiler}', $foldersPrefix . 'limitstart' );

				if ( $gallery->get( 'query', true, GetterInterface::BOOLEAN ) ) {
					$foldersTotal			=	$gallery->folders( 'count' );
				} else {
					$foldersTotal			=	0;
				}

				if ( $foldersTotal ) {
					if ( $foldersTotal <= $foldersLimitstart ) {
						$foldersLimitstart	=	0;
					}

					$foldersPageNav			=	new cbPageNav( $foldersTotal, $foldersLimitstart, $gallery->get( 'folders_paging_limit', 0, GetterInterface::INT ) );

					$foldersPageNav->setInputNamePrefix( $foldersPrefix );

					$gallery->set( 'folders_paging_limitstart', $foldersPageNav->limitstart );

					if ( ( $foldersLimitstart == 0 ) && ( $gallery->get( 'folders_orderby', 'date_desc', GetterInterface::STRING ) == 'random' ) ) {
						Application::Session()->subTree( 'gallery.random.' . $gallery->id() )->set( 'folders', rand() );
					}

					$folders				=	array();

					if ( $gallery->get( 'query', true, GetterInterface::BOOLEAN ) && $foldersTotal ) {
						$folders			=	$gallery->folders();
					}

					$thumbnails				=	array();

					foreach ( $folders as $fld ) {
						$thumbnail			=	$fld->get( 'thumbnail', 0, GetterInterface::INT );

						if ( $thumbnail ) {
							$thumbnails[]	=	$thumbnail;
						}
					}

					if ( $thumbnails ) {
						$gallery->reset()->setId( $thumbnails )->items();
					}
				}
			} else {
				$folder						=	$gallery->folder( $folderId );

				if ( ( ! $folder->get( 'id', 0, GetterInterface::INT ) ) || ( ( ! $folder->get( 'published', 1, GetterInterface::INT ) ) && ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $folder->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! $canModerate ) ) ) ) {
					if ( $inline ) {
						return;
					} else {
						CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
					}
				}

				$pagingPrefix				=	'folders_';
			}
		} elseif ( $folderId ) {
			if ( $inline ) {
				return;
			} else {
				CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
			}
		}

		// Items:
		$itemsPrefix						=	$galleryPrefix . 'items_';
		$itemsType							=	$_CB_framework->getUserStateFromRequest( $itemsPrefix . 'type{com_comprofiler}', $itemsPrefix . 'type' );

		if ( $itemsType ) {
			$searching						=	true;

			$gallery->set( 'type', $itemsType );
		}

		$itemsLimitstart					=	(int) $_CB_framework->getUserStateFromRequest( $itemsPrefix . 'limitstart{com_comprofiler}', $itemsPrefix . 'limitstart' );

		if ( $gallery->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$itemsTotal						=	$gallery->items( 'count' );
		} else {
			$itemsTotal						=	0;
		}

		if ( $itemsTotal <= $itemsLimitstart ) {
			$itemsLimitstart				=	0;
		}

		$itemsPageNav						=	new cbPageNav( $itemsTotal, $itemsLimitstart, $gallery->get( $pagingPrefix . 'items_paging_limit', 0, GetterInterface::INT ) );

		$itemsPageNav->setInputNamePrefix( $itemsPrefix );

		$gallery->set( $pagingPrefix . 'items_paging_limitstart', $itemsPageNav->limitstart );

		if ( ( $itemsLimitstart == 0 ) && ( $gallery->get( $pagingPrefix . 'items_orderby', 'date_desc', GetterInterface::STRING ) == 'random' ) ) {
			Application::Session()->set( 'gallery.random.' . $gallery->id() . '.' . $pagingPrefix . 'items', rand() );
		}

		$items								=	array();

		if ( $gallery->get( 'query', true, GetterInterface::BOOLEAN ) && $itemsTotal ) {
			$items							=	$gallery->items();
		}

		$input['type']						=	null;

		$galleryTypes						=	$gallery->types();

		if ( count( $galleryTypes ) > 1 ) {
			$filterTypes					=	array();
			$filterTypes[]					=	moscomprofilerHTML::makeOption( 0, CBTxt::T( 'All' ) );

			foreach ( $galleryTypes as $filterType ) {
				$filterTypes[]				=	moscomprofilerHTML::makeOption( $filterType, CBGallery::translateType( $filterType, true ) );
			}

			$input['type']					=	moscomprofilerHTML::selectList( $filterTypes, $itemsPrefix . 'type', 'class="gallerySearchType" style="display: none;"', 'value', 'text', $itemsType, 0, false, false );

			if ( $folderId ) {
				$searchPlaceholder			=	CBTxt::T( 'Search Album...' );
			} else {
				$searchPlaceholder			=	CBTxt::T( 'Search Gallery...' );
			}
		} else {
			// CBTxt::T( 'SEARCH_TYPE', 'Search [type]...', array( '[type]' => CBGallery::translateType( $galleryTypes[0] ) ) )
			$searchPlaceholder				=	CBTxt::T( 'SEARCH_TYPE SEARCH_' . strtoupper( $galleryTypes[0] ), 'Search [type]...', array( '[type]' => CBGallery::translateType( $galleryTypes[0], true ) ) );
		}

		$input['search']					=	'<input type="text" name="' . htmlspecialchars( $galleryPrefix . 'search' ) . '" value="' . htmlspecialchars( $gallerySearch ) . '" onchange="document.galleryForm' . htmlspecialchars( $gallery->id() ) . '.submit();" placeholder="' . htmlspecialchars( $searchPlaceholder ) . '" class="gallerySearch form-control" />';

		HTML_cbgalleryGallery::showGallery( $folder, $folders, $foldersPageNav, $items, $itemsPageNav, $searching, $input, $viewer, $gallery, $this, $output );
	}

	/**
	 * Displays items new page
	 *
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 */
	public function showItemsNew( $viewer, $gallery )
	{
		CBGallery::getTemplate( 'items_new' );

		$folderId		=	$this->input( 'folder', $gallery->get( 'folder', 0, GetterInterface::INT ), GetterInterface::INT );

		if ( $folderId ) {
			$gallery->set( 'folder', $folderId );
		}

		if ( ! CBGallery::canCreateItems( 'all', 'both', $gallery ) ) {
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		HTML_cbgalleryItemsNew::showItemsNew( $viewer, $gallery, $this );
	}

	/**
	 * Outputs the header for an item
	 *
	 * @param string    $output
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 */
	private function outputItem( $output, $id, $viewer, $gallery )
	{
		$row	=	$gallery->item( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ( ! $row->get( 'published', 1, GetterInterface::INT ) ) && ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBGallery::canModerate( $gallery ) ) ) ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		$type	=	$row->get( 'type', null, GetterInterface::STRING );

		if ( ( $output == 'download' ) && ( ( $type == 'files' ) || ( $gallery->get( $type . '_download', false, GetterInterface::BOOLEAN ) ) ) ) {
			$row->download();
		} elseif ( $output == 'thumbnail' ) {
			$row->preview( true );
		} else {
			$row->preview();
		}
	}

	/**
	 * Displays item modal page
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 */
	public function showItem( $id, $viewer, $gallery )
	{
		$row	=	$gallery->item( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ( ! $row->get( 'published', 1, GetterInterface::INT ) ) && ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBGallery::canModerate( $gallery ) ) ) ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		CBGallery::getTemplate( 'item', false, false );

		HTML_cbgalleryItem::showItem( $row, $viewer, $gallery, $this );
	}

	/**
	 * Displays item create/edit page
	 *
	 * @param int|ItemTable $id
	 * @param UserTable     $viewer
	 * @param Gallery       $gallery
	 * @param string        $output
	 * @return null|string
	 */
	public function showItemEdit( $id, $viewer, $gallery, $output = null )
	{
		global $_CB_framework;

		CBGallery::getTemplate( array( ( $output ? 'item_edit_mini' : 'item_edit' ), 'item_container' ), ( $output == 'ajax' ? false : true ), ( $output == 'ajax' ? false : true ) );

		if ( $id instanceof ItemTable ) {
			$row											=	$id;
		} else {
			$row											=	$gallery->item( $id );
			$folderId										=	$this->input( 'folder', $gallery->get( 'folder', 0, GetterInterface::INT ), GetterInterface::INT );

			if ( $folderId ) {
				$row->set( 'folder', $folderId );
			}
		}

		$canModerate										=	CBGallery::canModerate( $gallery );

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! $canModerate ) ) {
				if ( $output ) {
					if ( $output == 'folder' ) {
						return null;
					} else {
						header( 'HTTP/1.0 404 Not Found' );
						exit();
					}
				} else {
					CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
				}
			}
		} elseif ( ! CBGallery::canCreateItems( 'all', 'both', $gallery ) ) {
			if ( $output ) {
				if ( $output == 'folder' ) {
					return null;
				} else {
					header( 'HTTP/1.0 404 Not Found' );
					exit();
				}
			} else {
				CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
			}
		}

		$input												=	array();

		$publishedTooltip									=	cbTooltip( null, CBTxt::T( 'Select publish status of the file. If unpublished the file will not be visible to the public.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['published']									=	moscomprofilerHTML::yesnoSelectList( 'published', 'class="form-control"' . $publishedTooltip, (int) $this->input( 'post/published', $row->get( 'published', 1, GetterInterface::INT ), GetterInterface::INT ) );

		$titleTooltip										=	cbTooltip( null, CBTxt::T( 'Optionally input a title. If no title is provided the filename will be displayed as the title.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['title']										=	'<input type="title" id="title" name="title" value="' . htmlspecialchars( $this->input( 'post/title', $row->get( 'title', null, GetterInterface::STRING ), GetterInterface::STRING ) ) . '" class="form-control" size="25"' . ( $output ? ' placeholder="' . htmlspecialchars( CBTxt::T( 'Title' ) ) . '"' : null ) . $titleTooltip . ' />';

		$listFolders										=	array();

		$folders											=	$gallery->reset()->folders();

		if ( $folders ) {
			$listFolders[]									=	moscomprofilerHTML::makeOption( 0, CBTxt::T( 'None' ) );

			/** @var FolderTable[] $folders */
			foreach ( $folders as $folder ) {
				$listFolders[]								=	moscomprofilerHTML::makeOption( $folder->get( 'id', 0, GetterInterface::INT ), ( $folder->get( 'title', null, GetterInterface::STRING ) ? $folder->get( 'title', null, GetterInterface::STRING ) : cbFormatDate( $folder->get( 'date', null, GetterInterface::STRING ), true, false, 'F j, Y', ' g:i A' ) ) );
			}

			$folderTooltip									=	cbTooltip( null, CBTxt::T( 'Select the album for this file.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['folder']								=	moscomprofilerHTML::selectList( $listFolders, 'folder', 'class="form-control"' . $folderTooltip, 'value', 'text', $this->input( 'post/folder', $row->get( 'folder', 0 ), GetterInterface::INT ), 1, false, false );
		} else {
			$input['folder']								=	null;
		}

		$descriptionTooltip									=	cbTooltip( null, CBTxt::T( 'Optionally input a description.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['description']								=	'<textarea id="description" name="description" class="form-control" cols="40" rows="5"' . ( $output ? ' placeholder="' . htmlspecialchars( CBTxt::T( 'Description' ) ) . '"' : null ) . $descriptionTooltip . '>' . $this->input( 'post/description', $row->get( 'description', null, GetterInterface::STRING ), GetterInterface::STRING ) . '</textarea>';

		$canUpload											=	CBGallery::canCreateItems( 'all', 'upload', $gallery );
		$canLink											=	CBGallery::canCreateItems( 'all', 'link', $gallery );

		if ( $row->get( 'id', 0, GetterInterface::INT ) || ( $canUpload && $canLink ) ) {
			static $JS_LOADED								=	0;

			if ( ! $JS_LOADED++ ) {
				$js											=	"$( '#method' ).on( 'change', function() {"
															.		"var value = $( this ).val();"
															.		"if ( value == 1 ) {"
															.			"$( '#itemUpload' ).removeClass( 'hidden' ).find( 'input' ).removeClass( 'cbValidationDisabled' );"
															.			"$( '#itemLink' ).addClass( 'hidden' ).find( 'input' ).addClass( 'cbValidationDisabled' );"
															.		"} else if ( value == 2 ) {"
															.			"$( '#itemUpload' ).addClass( 'hidden' ).find( 'input' ).addClass( 'cbValidationDisabled' ).val( '' );"
															.			"$( '#itemLink' ).removeClass( 'hidden' ).find( 'input' ).removeClass( 'cbValidationDisabled' );"
															.		"} else {"
															.			"$( '#itemUpload' ).addClass( 'hidden' ).find( 'input' ).addClass( 'cbValidationDisabled' ).val( '' );"
															.			"$( '#itemLink' ).addClass( 'hidden' ).find( 'input' ).addClass( 'cbValidationDisabled' );"
															.		"}"
															.	"}).change();";

				$_CB_framework->outputCbJQuery( $js );
			}

			$listMethods									=	array();

			if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
				$listMethods[]								=	moscomprofilerHTML::makeOption( 0, CBTxt::T( 'No Change' ) );
			}

			if ( $canUpload ) {
				$listMethods[]								=	moscomprofilerHTML::makeOption( 1, CBTxt::T( 'Upload' ) );
			}

			if ( $canLink ) {
				$listMethods[]								=	moscomprofilerHTML::makeOption( 2, CBTxt::T( 'Link' ) );
			}

			$input['method']								=	moscomprofilerHTML::selectList( $listMethods, 'method', 'class="form-control"', 'value', 'text', $this->input( 'post/method', 0, GetterInterface::INT ), 1, false, false );
		} else {
			$input['method']								=	null;
		}

		$minFileSizes										=	array(	$gallery->get( 'photos_min_size', 0, GetterInterface::INT ),
																		$gallery->get( 'videos_min_size', 0, GetterInterface::INT ),
																		$gallery->get( 'files_min_size', 0, GetterInterface::INT ),
																		$gallery->get( 'music_min_size', 0, GetterInterface::INT )
																	);

		sort( $minFileSizes, SORT_NUMERIC );

		$maxFileSizes										=	array(	$gallery->get( 'photos_max_size', 0, GetterInterface::INT ),
																		$gallery->get( 'videos_max_size', 0, GetterInterface::INT ),
																		$gallery->get( 'files_max_size', 0, GetterInterface::INT ),
																		$gallery->get( 'music_max_size', 0, GetterInterface::INT )
																	);

		rsort( $maxFileSizes, SORT_NUMERIC );

		$minFileSize										=	( ! in_array( 0, $minFileSizes ) ? $minFileSizes[0] : 0 );
		$maxFileSize										=	( ! in_array( 0, $maxFileSizes ) ? $maxFileSizes[0] : 0 );
		$uploadExtLimit										=	CBGallery::getExtensions( 'all', $gallery, 'upload' );

		$fileValidation										=	array();

		if ( $minFileSize || $maxFileSize ) {
			$fileValidation[]								=	cbValidator::getRuleHtmlAttributes( 'filesize', array( $minFileSize, $maxFileSize, 'KB' ) );
		}

		if ( $uploadExtLimit ) {
			$fileValidation[]								=	cbValidator::getRuleHtmlAttributes( 'extension', implode( ',', $uploadExtLimit ) );
		}

		if ( $canUpload ) {
			$fileTooltip									=	cbTooltip( null, CBTxt::T( 'Select the file to upload.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['upload']								=	'<input type="file" id="upload" name="upload" value="" class="form-control' . ( ! $row->get( 'id', 0, GetterInterface::INT ) ? ' required' : null ) . '"' . $fileTooltip . implode( ' ', $fileValidation ) . ' />';

			$input['upload_limits']							=	array();

			if ( $uploadExtLimit ) {
				$input['upload_limits'][]					=	CBTxt::T( 'FILE_MUST_BE_EXTS', 'Your file must be of [extensions] type.', array( '[extensions]' => implode( ', ', $uploadExtLimit ) ) );
			}

			if ( $minFileSize ) {
				$input['upload_limits'][]					=	CBTxt::T( 'FILE_SHOULD_EXCEED_SIZE', 'Your file should exceed [size].', array( '[size]' => CBGallery::getFormattedFileSize( $minFileSize * 1024 ) ) );
			}

			if ( $maxFileSize ) {
				$input['upload_limits'][]					=	CBTxt::T( 'FILE_SHOUND_NOT_EXCEED_SIZE', 'Your file should not exceed [size].', array( '[size]' => CBGallery::getFormattedFileSize( $maxFileSize * 1024 ) ) );
			}
		} else {
			$input['upload']								=	null;
			$input['upload_limits']							=	null;
		}

		if ( $canLink ) {
			$linkExtLimit									=	CBGallery::getExtensions( 'all', $gallery, 'link' );

			$linkTooltip									=	cbTooltip( null, CBTxt::T( 'Input the URL to the file to link.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['link']									=	'<input type="text" id="value" name="value" value="' . htmlspecialchars( $this->input( 'post/value', ( $row->domain() ? $row->get( 'value', null, GetterInterface::STRING ) : null ), GetterInterface::STRING ) ) . '" size="40" class="form-control' . ( ! $row->get( 'id', 0, GetterInterface::INT ) ? ' required' : null ) . '"' . $linkTooltip . ' />';

			$input['link_limits']							=	array();

			if ( $linkExtLimit ) {
				$input['link_limits'][]						=	CBTxt::T( 'LINK_MUST_BE_EXTS', 'Your file link must be of [extensions] type.', array( '[extensions]' => implode( ', ', $linkExtLimit ) ) );
			}
		} else {
			$input['link']									=	null;
			$input['link_limits']							=	null;
		}

		if ( ( $row->get( 'type', null, GetterInterface::STRING ) != 'photos' ) && $gallery->get( 'thumbnails', true, GetterInterface::BOOLEAN ) ) {
			$canUploadThumbnail								=	$gallery->get( 'thumbnails_upload', true, GetterInterface::BOOLEAN );
			$canLinkThumbnail								=	$gallery->get( 'thumbnails_link', false, GetterInterface::BOOLEAN );

			if ( $row->get( 'id', 0, GetterInterface::INT ) || ( $canUploadThumbnail && $canLinkThumbnail ) ) {
				static $JS_LOADED							=	0;

				if ( ! $JS_LOADED++ ) {
					$js										=	"$( '#thumbnail_method' ).on( 'change', function() {"
															.		"var value = $( this ).val();"
															.		"if ( value == 1 ) {"
															.			"$( '#itemThumbnailUpload' ).removeClass( 'hidden' ).find( 'input' ).removeClass( 'cbValidationDisabled' );"
															.			"$( '#itemThumbnailLink' ).addClass( 'hidden' ).find( 'input' ).addClass( 'cbValidationDisabled' );"
															.		"} else if ( value == 2 ) {"
															.			"$( '#itemThumbnailUpload' ).addClass( 'hidden' ).find( 'input' ).addClass( 'cbValidationDisabled' ).val( '' );"
															.			"$( '#itemThumbnailLink' ).removeClass( 'hidden' ).find( 'input' ).removeClass( 'cbValidationDisabled' );"
															.		"} else {"
															.			"$( '#itemThumbnailUpload' ).addClass( 'hidden' ).find( 'input' ).addClass( 'cbValidationDisabled' ).val( '' );"
															.			"$( '#itemThumbnailLink' ).addClass( 'hidden' ).find( 'input' ).addClass( 'cbValidationDisabled' );"
															.		"}"
															.	"}).change();";

					$_CB_framework->outputCbJQuery( $js );
				}

				$listThumbnailMethods						=	array();

				if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
					$listThumbnailMethods[]					=	moscomprofilerHTML::makeOption( 0, CBTxt::T( 'No Change' ) );
				}

				if ( $canUploadThumbnail ) {
					$listThumbnailMethods[]					=	moscomprofilerHTML::makeOption( 1, CBTxt::T( 'Upload' ) );
				}

				if ( $canLinkThumbnail ) {
					$listThumbnailMethods[]					=	moscomprofilerHTML::makeOption( 2, CBTxt::T( 'Link' ) );
				}

				if ( $row->get( 'thumbnail', null, GetterInterface::STRING ) && ( ! $row->domain( true ) ) ) {
					$listThumbnailMethods[]					=	moscomprofilerHTML::makeOption( 3, CBTxt::T( 'Delete' ) );
				}

				$input['thumbnail_method']					=	moscomprofilerHTML::selectList( $listThumbnailMethods, 'thumbnail_method', 'class="form-control"', 'value', 'text', $this->input( 'post/thumbnail_method', 0, GetterInterface::INT ), 1, false, false );
			} else {
				$input['thumbnail_method']					=	null;
			}

			$minThumbnailSize								=	$gallery->get( 'thumbnails_min_size', 0, GetterInterface::INT );
			$maxThumbnailSize								=	$gallery->get( 'thumbnails_max_size', 1024, GetterInterface::INT );

			$thumbnailValidation							=	array();

			if ( $minThumbnailSize || $maxThumbnailSize ) {
				$thumbnailValidation[]						=	cbValidator::getRuleHtmlAttributes( 'filesize', array( $minThumbnailSize, $maxThumbnailSize, 'KB' ) );
			}

			$thumbnailValidation[]							=	cbValidator::getRuleHtmlAttributes( 'extension', implode( ',', CBGallery::getExtensions( 'photos' ) ) );

			if ( $canUploadThumbnail ) {
				$thumbnailTooltip							=	cbTooltip( null, CBTxt::T( 'Optionally select the thumbnail file to upload.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

				$input['thumbnail_upload']					=	'<input type="file" id="thumbnail_upload" name="thumbnail_upload" value="" class="form-control"' . $thumbnailTooltip . implode( ' ', $thumbnailValidation ) . ' />';

				$input['thumbnail_upload_limits']			=	array();
				$input['thumbnail_upload_limits'][]			=	CBTxt::T( 'THUMBNAIL_MUST_BE_EXTS', 'Your thumbnail file must be of [extensions] type.', array( '[extensions]' => implode( ', ', CBGallery::getExtensions( 'photos' ) ) ) );

				if ( $minThumbnailSize ) {
					$input['thumbnail_upload_limits'][]		=	CBTxt::T( 'THUMBNAIL_SHOULD_EXCEED_SIZE', 'Your thumbnail file should exceed [size].', array( '[size]' => CBGallery::getFormattedFileSize( $minThumbnailSize * 1024 ) ) );
				}

				if ( $maxThumbnailSize ) {
					$input['thumbnail_upload_limits'][]		=	CBTxt::T( 'THUMBNAIL_SHOUND_NOT_EXCEED_SIZE', 'Your thumbnail file should not exceed [size].', array( '[size]' => CBGallery::getFormattedFileSize( $maxThumbnailSize * 1024 ) ) );
				}
			} else {
				$input['thumbnail_upload']					=	null;
				$input['thumbnail_upload_limits']			=	null;
			}

			if ( $canLinkThumbnail ) {
				$linkExtLimit								=	CBGallery::getExtensions( 'photos' );

				$linkThumbnailTooltip						=	cbTooltip( null, CBTxt::T( 'Optionally input the URL to the thumbnail file to link.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

				$input['thumbnail_link']					=	'<input type="text" id="thumbnail" name="thumbnail" value="' . htmlspecialchars( $this->input( 'post/thumbnail', ( $row->domain( true ) ? $row->get( 'thumbnail', null, GetterInterface::STRING ) : null ), GetterInterface::STRING ) ) . '" size="40" class="form-control' . ( ! $row->get( 'id', 0, GetterInterface::INT ) ? ' required' : null ) . '"' . $linkThumbnailTooltip . ' />';

				$input['thumbnail_link_limits']				=	array();

				if ( $linkExtLimit ) {
					$input['thumbnail_link_limits'][]		=	CBTxt::T( 'THUMBNAIL_LINK_MUST_BE_EXTS', 'Your thumbnail file link must be of [extensions] type.', array( '[extensions]' => implode( ', ', $linkExtLimit ) ) );
				}
			} else {
				$input['thumbnail_link']					=	null;
				$input['thumbnail_link_limits']				=	null;
			}
		} else {
			$input['thumbnail_method']						=	null;
			$input['thumbnail_upload']						=	null;
			$input['thumbnail_upload_limits']				=	null;
			$input['thumbnail_link']						=	null;
			$input['thumbnail_link_limits']					=	null;
		}

		$ownerTooltip										=	cbTooltip( null, CBTxt::T( 'Input owner as single integer user_id.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['user_id']									=	'<input type="text" id="user_id" name="user_id" value="' . $this->input( 'post/user_id', $row->get( 'user_id', $gallery->user()->get( 'id', $viewer->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT ) ), GetterInterface::INT ) . '" class="digits required form-control" size="6"' . $ownerTooltip . ' />';

		if ( $output ) {
			$return											=	HTML_cbgalleryItemEditMini::showItemEditMini( $row, $input, $viewer, $gallery, $this, $output );

			if ( $output == 'folder' ) {
				return $return;
			} else {
				echo $return;
			}
		} else {
			HTML_cbgalleryItemEdit::showItemEdit( $row, $input, $viewer, $gallery, $this );
		}

		return null;
	}

	/**
	 * Saves an item
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 * @param string    $output
	 */
	private function saveItemEdit( $id, $viewer, $gallery, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$input							=	$this->getInput();
		$files							=	$input->getNamespaceRegistry( 'files' );

		$row							=	$gallery->item( $id );

		$row->set( '_input', $input );
		$row->set( '_files', $files );

		$folderId						=	$this->input( 'folder', $gallery->get( 'folder', 0, GetterInterface::INT ), GetterInterface::INT );

		if ( $folderId ) {
			$row->set( 'folder', $folderId );
		}

		$canModerate					=	CBGallery::canModerate( $gallery );
		$type							=	$row->discoverType( $gallery );

		$upload							=	$files->subTree( 'upload' );
		$link							=	$this->input( 'value', null, GetterInterface::STRING );
		$method							=	null;

		if ( $upload->get( 'name', null, GetterInterface::STRING ) ) {
			$method						=	'upload';
		} elseif ( $link && ( $link != $row->get( 'value', null, GetterInterface::STRING ) ) ) {
			$method						=	'link';
		}

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! $canModerate ) ) {
				if ( $output == 'ajax' ) {
					$this->ajaxResponse( false, CBTxt::T( 'You do not have permission to edit this file.' ) );
				} else {
					CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
				}
			}
		} elseif ( ! CBGallery::canCreateItems( $type, $method, $gallery ) ) {
			if ( $method ) {
				if ( CBGallery::createLimitedItems( $type, $gallery ) ) {
					if ( $method == 'upload' ) {
						// CBTxt::T( 'YOU_CANNOT_UPLOAD_ANYMORE_TYPES', 'You can not upload anymore [types]. You have reached your quota.', array( '[type]' => CBGallery::translateType( $row, false, true ) ) )
						$error			=	CBTxt::T( 'YOU_CANNOT_UPLOAD_ANYMORE_TYPES YOU_CANNOT_UPLOAD_ANYMORE_' . strtoupper( $type ), 'You can not upload anymore [types]. You have reached your quota.', array( '[types]' => CBGallery::translateType( $type, true, true ) ) );
					} else {
						// CBTxt::T( 'YOU_CANNOT_LINK_ANYMORE_TYPES', 'You can not link anymore [types]. You have reached your quota.', array( '[type]' => CBGallery::translateType( $row, false, true ) ) )
						$error			=	CBTxt::T( 'YOU_CANNOT_LINK_ANYMORE_TYPES YOU_CANNOT_LINK_ANYMORE_' . strtoupper( $type ), 'You can not link anymore [types]. You have reached your quota.', array( '[types]' => CBGallery::translateType( $type, true, true ) ) );
					}
				} else {
					if ( $method == 'upload' ) {
						$error			=	CBTxt::T( 'FILE_UPLOAD_INVALID_UPLOAD_ONLY_EXTS', 'Invalid file. Please upload only [extensions]!', array( '[extensions]' => implode( ', ', CBGallery::getExtensions( 'all', $gallery, $method ) ) ) );
					} else {
						$error			=	CBTxt::T( 'FILE_LINK_INVALID_LINK_ONLY_EXTS', 'Invalid file URL. Please link only [extensions]!', array( '[extensions]' => implode( ', ', CBGallery::getExtensions( 'all', $gallery, $method ) ) ) );
					}
				}

				if ( $output == 'ajax' ) {
					$this->ajaxResponse( false, $error );
				} else {
					$_CB_framework->enqueueMessage( $error, 'error' );

					$this->showItemEdit( $id, $viewer, $gallery );
					return;
				}
			} else {
				if ( $output == 'ajax' ) {
					// CBTxt::T( 'NO_PERMISSION_TO_CREATE_TYPES', 'You do not have permission to create [types] in this gallery.', array( '[types]' => CBGallery::translateType( $type, true, true ) ) )
					$this->ajaxResponse( false, CBTxt::T( 'NO_PERMISSION_TO_CREATE_TYPES NO_PERMISSION_TO_CREATE_' . strtoupper( $type ), 'You do not have permission to create [types] in this gallery.', array( '[types]' => CBGallery::translateType( $type, true, true ) ) ) );
				} else {
					CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
				}
			}
		}

		if ( preg_match( '/^profile\.(\d+).uploads/', $gallery->asset() ) && ( $type != 'files' ) ) {
			$row->set( 'published', 1 );
		} else {
			if ( $canModerate || ( ! $gallery->get( $type . '_create_approval', false, GetterInterface::BOOLEAN ) ) || ( $gallery->get( $type . '_create_approval', false, GetterInterface::BOOLEAN ) && ( $row->get( 'published', 1, GetterInterface::INT ) != -1 ) && ( ! $method ) ) ) {
				$row->set( 'published', $this->input( 'post/published', $row->get( 'published', 1, GetterInterface::INT ), GetterInterface::INT ) );
			} else {
				$row->set( 'published', ( $gallery->get( $type . '_create_approval', false, GetterInterface::BOOLEAN  ) ? -1 : $row->get( 'published', 1, GetterInterface::INT ) ) );
			}
		}

		$row->set( 'asset', $row->get( 'asset', $gallery->asset(), GetterInterface::STRING ) );
		$row->set( 'title', $this->input( 'post/title', $row->get( 'title', null, GetterInterface::STRING ), GetterInterface::STRING ) );
		$row->set( 'description', $this->input( 'post/description', $row->get( 'description', null, GetterInterface::STRING ), GetterInterface::STRING ) );

		if ( $method == 'link' ) {
			$row->set( 'value', $link );
		}

		if ( Application::MyUser()->isGlobalModerator() ) {
			$row->set( 'user_id', $this->input( 'post/user_id', $row->get( 'user_id', $gallery->user()->get( 'id', $viewer->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT ) ), GetterInterface::INT ) );
		} else {
			$row->set( 'user_id', $row->get( 'user_id', $gallery->user()->get( 'id', $viewer->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT ), GetterInterface::INT  ) );
		}

		if ( $output != 'ajax' ) {
			if ( $gallery->get( 'items_create_captcha', false, GetterInterface::BOOLEAN ) && ( ! $canModerate ) ) {
				$_PLUGINS->loadPluginGroup( 'user' );

				$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );

				if ( $_PLUGINS->is_errors() ) {
					$row->setError( $_PLUGINS->getErrorMSG() );
				}
			}

			if ( ( $type != 'photos' ) && $gallery->get( 'thumbnails', true, GetterInterface::BOOLEAN ) ) {
				$thumbnail				=	$row->get( 'thumbnail', null, GetterInterface::STRING );

				if ( $files->subTree( 'thumbnail_upload' )->get( 'name', null, GetterInterface::STRING ) ) {
					if ( ! $gallery->get( 'thumbnails_upload', true, GetterInterface::BOOLEAN ) ) {
						if ( $output == 'ajax' ) {
							$this->ajaxResponse( false, CBTxt::T( 'Custom thumbnail files are not allowed in this gallery.' ) );
						} else {
							CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Custom thumbnail files are not allowed in this gallery.' ), 'error' );
						}
					}
				} elseif ( $thumbnail && ( $thumbnail != $row->get( 'thumbnail', null, GetterInterface::STRING ) ) ) {
					if ( ! $gallery->get( 'thumbnails_link', false, GetterInterface::BOOLEAN ) ) {
						if ( $output == 'ajax' ) {
							$this->ajaxResponse( false, CBTxt::T( 'Custom thumbnail links are not allowed in this gallery.' ) );
						} else {
							CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Custom thumbnail links are not allowed in this gallery.' ), 'error' );
						}
					}
				}
			}
		}

		// Create an uploads folder on demand:
		if ( $folderId === -1 ) {
			$folder						=	new FolderTable();

			$folder->load( array( 'user_id' => $row->get( 'user_id', 0, GetterInterface::INT  ), 'asset' => 'profile.' . $row->get( 'user_id', 0, GetterInterface::INT  ) . '.uploads' ) );

			if ( $folder->get( 'id', 0, GetterInterface::INT ) ) {
				$row->set( 'folder', $folder->get( 'id', 0, GetterInterface::INT ) );
			} else {
				$folder->set( 'user_id', $row->get( 'user_id', 0, GetterInterface::INT  ) );
				$folder->set( 'asset', 'profile.' . $row->get( 'user_id', 0, GetterInterface::INT  ) . '.uploads' );
				$folder->set( 'title', CBTxt::T( 'Uploads' ) );
				$folder->set( 'published', 1 );

				if ( $folder->store() ) {
					$row->set( 'folder', $folder->get( 'id', 0, GetterInterface::INT ) );
				}
			}
		}

		$new							=	( $row->get( 'id', 0, GetterInterface::INT ) ? false : true );
		$old							=	new ItemTable();
		$source							=	$row->source();

		if ( ! $new ) {
			$old->load( $row->get( 'id', 0, GetterInterface::INT ) );

			$_PLUGINS->trigger( 'gallery_onBeforeUpdateGalleryItem', array( $gallery, $source, &$row, $old ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onBeforeCreateGalleryItem', array( $gallery, $source, &$row ) );
		}

		if ( $row->getError() || ( ! $row->check( $gallery ) ) ) {
			// CBTxt::T( 'TYPE_FAILED_TO_SAVE', '[type] failed to save! Error: [error]', array( '[type]' => CBGallery::translateType( $row ), '[error]' => $row->getError() ) )
			$error						=	CBTxt::T( 'TYPE_FAILED_TO_SAVE ' . strtoupper( $type ) . '_FAILED_TO_SAVE', '[type] failed to save! Error: [error]', array( '[type]' => CBGallery::translateType( $row ), '[error]' => $row->getError() ) );

			if ( $output == 'ajax' ) {
				$this->ajaxResponse( false, $error );
			} else {
				$_CB_framework->enqueueMessage( $error, 'error' );

				$this->showItemEdit( $id, $viewer, $gallery );
				return;
			}
		}

		if ( $row->getError() || ( ! $row->store( null, $gallery ) ) ) {
			// CBTxt::T( 'TYPE_FAILED_TO_SAVE', '[type] failed to save! Error: [error]', array( '[type]' => CBGallery::translateType( $row ), '[error]' => $row->getError() ) )
			$error					=	CBTxt::T( 'TYPE_FAILED_TO_SAVE ' . strtoupper( $type ) . '_FAILED_TO_SAVE', '[type] failed to save! Error: [error]', array( '[type]' => CBGallery::translateType( $row ), '[error]' => $row->getError() ) );

			if ( $output == 'ajax' ) {
				$this->ajaxResponse( false, $error );
			} else {
				$_CB_framework->enqueueMessage( $error, 'error' );

				$this->showItemEdit( $id, $viewer, $gallery );
				return;
			}
		}

		if ( $row->get( 'folder', 0, GetterInterface::INT ) ) {
			$folder					=	$row->folder();

			// Update the folder thumbnail if one doesn't exist or if we're using the uploads folder always update to latest upload:
			if ( $folder->get( 'id', 0, GetterInterface::INT ) && ( ( ! $folder->get( 'thumbnail', 0, GetterInterface::INT ) ) || ( $folderId === -1 ) ) ) {
				$folder->set( 'thumbnail', $row->get( 'id', 0, GetterInterface::INT ) );

				$folder->store();
			}
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gallery_onAfterUpdateGalleryItem', array( $gallery, $source, $row, $old ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onAfterCreateGalleryItem', array( $gallery, $source, $row ) );
		}

		$newParams					=	clone $row->params();

		$newParams->unsetEntry( 'overrides' );

		$row->set( 'params', $newParams->asJson() );

		if ( $row->get( 'published', 1, GetterInterface::INT ) == -1 ) {
			if ( $new && ( ! $canModerate ) && $gallery->get( 'items_create_approval_notify', true, GetterInterface::BOOLEAN ) ) {
				$cbUser				=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false );

				if ( $row->domain() ) {
					$itemUrl		=	htmlspecialchars( $row->path() );
				} else {
					$itemUrl		=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'item', 'func' => 'show', 'type' => $type, 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true );
				}

				$extraStrings		=	array(	'item_id'			=>	$row->get( 'id', 0, GetterInterface::INT ),
												'item_value'		=>	$row->get( 'value', null, GetterInterface::STRING ),
												'item_file'			=>	$row->get( 'file', null, GetterInterface::STRING ),
												'item_title'		=>	( $row->get( 'title', null, GetterInterface::STRING ) ? $row->get( 'title', null, GetterInterface::STRING ) : $row->name() ),
												'item_description'	=>	$row->get( 'description', null, GetterInterface::STRING ),
												'item_extension'	=>	$row->extension(),
												'item_size'			=>	$row->size(),
												'item_date'			=>	cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ) ),
												'item_folder'		=>	$row->get( 'folder', 0, GetterInterface::INT ),
												'item_type'			=>	$row->get( 'type', null, GetterInterface::STRING ),
												'item_url'			=>	$itemUrl,
												'gallery_location'	=>	htmlspecialchars( $gallery->location() ),
												'user_url'			=>	$_CB_framework->viewUrl( 'userprofile', true, array( 'user' => $row->get( 'user_id', 0, GetterInterface::INT ) ) )
											);

				// CBTxt::T( 'GALLERY_NEW_TYPE_CREATED', 'Gallery - New [type] Created!', array( '[type]' => CBGallery::translateType( $row ) ) )
				$subject			=	$cbUser->replaceUserVars( CBTxt::T( 'GALLERY_NEW_TYPE_CREATED GALLERY_NEW_' . strtoupper( $type ) . '_CREATED', 'Gallery - New [type] Created!', array( '[type]' => CBGallery::translateType( $row ) ) ), false, true, $extraStrings, false );
				// CBTxt::T( 'TYPE_PENDING_APPROVAL', '<a href="[user_url]">[formatname]</a> created [type] <a href="[item_url]">[item_title]</a> and requires <a href="[gallery_location]">approval</a>!', array( '[type]' => CBGallery::translateType( $row, false, true ) ) )
				$message			=	$cbUser->replaceUserVars( CBTxt::T( 'TYPE_PENDING_APPROVAL ' . strtoupper( $type ) . '_PENDING_APPROVAL', '<a href="[user_url]">[formatname]</a> created [type] <a href="[item_url]">[item_title]</a> and requires <a href="[gallery_location]">approval</a>!', array( '[type]' => CBGallery::translateType( $row, false, true ) ) ), false, true, $extraStrings, false );

				$notifications		=	new cbNotification();

				$recipients			=	$gallery->getNotify();

				if ( $recipients ) {
					cbToArrayOfInt( $recipients );

					foreach ( $recipients as $recipient ) {
						$notifications->sendFromSystem( $recipient, $subject, $message, false, 1 );
					}
				} else {
					$notifications->sendToModerators( $subject, $message, false, 1 );
				}
			}

			if ( $output == 'ajax' ) {
				if ( preg_match( '/^profile\.(\d+).uploads/', $gallery->asset() ) ) {
					CBGallery::getTemplate( array( 'item_edit_micro', 'item_container' ), false, false );

					echo HTML_cbgalleryItemEditMicro::showItemEditMicro( $row, $viewer, $gallery, $this, $output );
					return;
				} else {
					$this->showItemEdit( $row->get( 'id', 0, GetterInterface::INT ), $viewer, $gallery, $output );
					return;
				}
			} else {
				// CBTxt::T( 'TYPE_SAVED_SUCCESSFULLY_AND_AWAITING_APPROVAL', '[type] saved successfully and awaiting approval!', array( '[type]' => CBGallery::translateType( $row ) ) )
				$message			=	CBTxt::T( 'TYPE_SAVED_SUCCESSFULLY_AND_AWAITING_APPROVAL ' . strtoupper( $type ) . '_SAVED_SUCCESSFULLY_AND_AWAITING_APPROVAL', '[type] saved successfully and awaiting approval!', array( '[type]' => CBGallery::translateType( $row ) ) );

				CBGallery::returnRedirect( $gallery->location(), $message );
			}
		} else {
			if ( $output == 'ajax' ) {
				if ( preg_match( '/^profile\.(\d+).uploads/', $gallery->asset() ) ) {
					CBGallery::getTemplate( array( 'item_edit_micro', 'item_container' ), false, false );

					echo HTML_cbgalleryItemEditMicro::showItemEditMicro( $row, $viewer, $gallery, $this, $output );
					return;
				} else {
					$this->showItemEdit( $row->get( 'id', 0, GetterInterface::INT ), $viewer, $gallery, $output );
					return;
				}
			} else {
				// CBTxt::T( 'TYPE_SAVED_SUCCESSFULLY', '[type] saved successfully!', array( '[type]' => CBGallery::translateType( $row ) ) )
				$message			=	CBTxt::T( 'TYPE_SAVED_SUCCESSFULLY ' . strtoupper( $type ) . '_SAVED_SUCCESSFULLY', '[type] saved successfully!', array( '[type]' => CBGallery::translateType( $row ) ) );

				CBGallery::returnRedirect( $gallery->location(), $message );
			}
		}
	}

	/**
	 * Rotates the image in the angle specified
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 */
	private function rotateItem( $id, $viewer, $gallery )
	{
		global $_PLUGINS;

		$row			=	$gallery->item( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) )
			 || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBGallery::canModerate( $gallery ) ) )
			 || ( $row->get( 'type', null, GetterInterface::STRING ) != 'photos' )
		) {
			$this->ajaxResponse( false, CBTxt::T( 'Nothing to rotate.' ) );
		}

		$rotate			=	$row->params()->get( 'rotate', 0, GetterInterface::INT );

		if ( $rotate >= 360 ) {
			$rotate		=	0;
		}

		if ( $this->input( 'direction', null, GetterInterface::STRING ) == 'left' ) {
			$angle		=	( $rotate ? -90 : 270 );
		} else {
			$angle		=	90;
		}

		$rotate			=	( $rotate + $angle );

		$row->params()->set( 'rotate', $rotate );

		$row->set( 'params', $row->params()->asJson() );

		$source			=	$row->source();

		$_PLUGINS->trigger( 'gallery_onBeforeRotateGalleryItem', array( $gallery, $source, &$row ) );

		if ( $row->getError() || ( ! $row->store() ) ) {
			$this->ajaxResponse( false, CBTxt::T( 'PHOTO_FAILED_TO_ROTATE', 'Photo failed to rotate! Error: [error]', array( '[error]' => $row->getError() ) ) );
		}

		$_PLUGINS->trigger( 'gallery_onAfterRotateGalleryItem', array( $gallery, $source, $row ) );

		$this->ajaxResponse( true );
	}

	/**
	 * Saves an item to a field
	 *
	 * @param string    $fieldName
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 */
	private function saveItemField( $fieldName, $id, $viewer, $gallery )
	{
		global $_PLUGINS, $_CB_framework;

		$row										=	$gallery->item( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) )
			 || ( $row->get( 'published', 0, GetterInterface::INT ) === -1 )
			 || $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT )
			 || ( $row->get( 'type', null, GetterInterface::STRING ) != 'photos' )
			 || ( ! in_array( $fieldName, array( 'avatar', 'canvas' ) ) )
			 || ( ! $gallery->get( 'photos_' . $fieldName, false, GetterInterface::BOOLEAN ) )
		) {
			$this->ajaxResponse( false );
		}

		$field										=	new FieldTable();

		$field->load( array( 'name' => $fieldName ) );

		$field->set( 'params', new Registry( $field->get( 'params', null, GetterInterface::RAW ) ) );

		$isModerator								=	Application::MyUser()->isModeratorFor( Application::User( $viewer->get( 'id', 0, GetterInterface::INT ) ) );
		$path										=	$row->path();

		$_PLUGINS->trigger( 'onBeforeUserAvatarUpdate', array( &$viewer, &$viewer, $isModerator, &$path ) );

		if ( $_PLUGINS->is_errors() ) {
			$this->_setErrorMSG( $_PLUGINS->getErrorMSG() );
		}

		$conversionType								=	Application::Config()->get( 'conversiontype', 0, GetterInterface::INT );
		$imageSoftware								=	( $conversionType == 5 ? 'gmagick' : ( $conversionType == 1 ? 'imagick' : ( $conversionType == 4 ? 'gd' : 'auto' ) ) );
		$imagePath									=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/';

		$resize										=	$field->params->get( 'avatarResizeAlways', '' );

		if ( $resize == '' ) {
			$resize									=	Application::Config()->get( 'avatarResizeAlways', 1, GetterInterface::INT );
		}

		$aspectRatio								=	$field->params->get( 'avatarMaintainRatio', '' );

		if ( $aspectRatio == '' ) {
			$aspectRatio							=	Application::Config()->get( 'avatarMaintainRatio', 1, GetterInterface::INT );
		}

		$width										=	$field->params->get( 'avatarWidth', ( $fieldName == 'canvas' ? 1280 : '' ) );

		if ( $width == '' ) {
			$width									=	Application::Config()->get( 'avatarWidth', 200, GetterInterface::INT );
		}

		$height										=	$field->params->get( 'avatarHeight', ( $fieldName == 'canvas' ? 640 : '' ) );

		if ( $height == '' ) {
			$height									=	Application::Config()->get( 'avatarHeight', 500, GetterInterface::INT );
		}

		$thumbWidth									=	$field->params->get( 'thumbWidth', ( $fieldName == 'canvas' ? 640 : '' ) );

		if ( $thumbWidth == '' ) {
			$thumbWidth								=	Application::Config()->get( 'thumbWidth', 60, GetterInterface::INT );
		}

		$thumbHeight								=	$field->params->get( 'thumbHeight', ( $fieldName == 'canvas' ? 320 : '' ) );

		if ( $thumbHeight == '' ) {
			$thumbHeight							=	Application::Config()->get( 'thumbHeight', 86, GetterInterface::INT );
		}

		$fileName									=	( $fieldName == 'canvas' ? 'canvas_' : '' ) . uniqid( $viewer->get( 'id', 0, GetterInterface::INT ) . '_' );
		$newFileName								=	null;

		try {
			$image									=	new Image( $imageSoftware, $resize, $aspectRatio );

			$image->setName( $fileName );
			$image->setSource( $path );
			$image->setDestination( $imagePath );

			switch ( $row->params()->get( 'rotate', 0, GetterInterface::INT ) ) {
				case 90:
					$image->rotateImage( 90 );
					break;
				case 180:
					$image->rotateImage( 180 );
					break;
				case 270:
					$image->rotateImage( 270 );
					break;
			}

			$image->processImage( $width, $height );

			$newFileName							=	$image->getCleanFilename();

			$image->setName( 'tn' . $fileName );

			$image->processImage( $thumbWidth, $thumbHeight );
		} catch ( Exception $e ) {
			if ( $field == 'canvas' ) {
				$error								=	CBTxt::T( 'PROFILE_CANVAS_FAILED_TO_UPDATE', 'Profile canvas failed to update! Error: [error]', array( '[error]' => $e->getMessage() ) );
			} else {
				$error								=	CBTxt::T( 'PROFILE_AVATAR_FAILED_TO_UPDATE', 'Profile avatar failed to update! Error: [error]', array( '[error]' => $e->getMessage() ) );
			}

			$this->ajaxResponse( true, $error );
		}

		$_PLUGINS->trigger( 'onLogChange', array( 'update', 'user', 'field', &$viewer, &$this, &$field, $viewer->get( $fieldName, null, GetterInterface::STRING ), $newFileName, 'edit' ) );

		if ( $viewer->get( $fieldName, null, GetterInterface::STRING ) ) {
			deleteAvatar( $viewer->get( $fieldName, null, GetterInterface::STRING ) );
		}

		$newValues									=	array(	$fieldName				=>	$newFileName,
																$fieldName . 'approved'	=>	1
															);

		if ( $fieldName == 'canvas' ) {
			$newValues[$fieldName . 'position']		=	50;
		}

		$_PLUGINS->trigger( 'onAfterUserAvatarUpdate', array( &$viewer, &$viewer, $isModerator, $newFileName ) );

		if ( $viewer->getError() || ( ! $viewer->storeDatabaseValues( $newValues ) ) ) {
			deleteAvatar( $viewer->get( $fieldName, null, GetterInterface::STRING ) );

			if ( $field == 'canvas' ) {
				$error								=	CBTxt::T( 'PROFILE_CANVAS_FAILED_TO_UPDATE', 'Profile canvas failed to update! Error: [error]', array( '[error]' => $viewer->getError() ) );
			} else {
				$error								=	CBTxt::T( 'PROFILE_AVATAR_FAILED_TO_UPDATE', 'Profile avatar failed to update! Error: [error]', array( '[error]' => $viewer->getError() ) );
			}

			$this->ajaxResponse( true, $error );
		}

		if ( $field == 'canvas' ) {
			$message								=	CBTxt::T( 'Profile canvas successfully set to this photo!' );
		} else {
			$message								=	CBTxt::T( 'Profile avatar successfully set to this photo!' );
		}

		$this->ajaxResponse( true, $message );
	}

	/**
	 * Sets the published state of an item
	 *
	 * @param int       $state
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 */
	private function stateItem( $state, $id, $viewer, $gallery )
	{
		global $_PLUGINS;

		$row		=	$gallery->item( $id );
		$type		=	$row->get( 'type', null, GetterInterface::STRING );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) )
			 || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBGallery::canModerate( $gallery ) ) )
			 || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( $row->get( 'published', 1, GetterInterface::INT ) == -1 ) && $gallery->get( $type . '_create_approval', false, GetterInterface::BOOLEAN ) ) ) {
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$row->set( 'published', (int) $state );

		$source		=	$row->source();

		if ( $state ) {
			$_PLUGINS->trigger( 'gallery_onBeforePublishGalleryItem', array( $gallery, $source, &$row ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onBeforeUnpublishGalleryItem', array( $gallery, $source, &$row ) );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			// CBTxt::T( 'TYPE_STATE_FAILED_TO_SAVE', '[type] state failed to save! Error: [error]', array( '[type]' => CBGallery::translateType( $row ), '[error]' => $row->getError() ) )
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'TYPE_STATE_FAILED_TO_SAVE ' . strtoupper( $type ) . '_STATE_FAILED_TO_SAVE', '[type] state failed to save! Error: [error]', array( '[type]' => CBGallery::translateType( $row ), '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $state ) {
			$_PLUGINS->trigger( 'gallery_onAfterPublishGalleryItem', array( $gallery, $source, $row ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onAfterUnpublishGalleryItem', array( $gallery, $source, $row ) );
		}

		// CBTxt::T( 'TYPE_STATE_SAVED_SUCCESSFULLY', '[type] state saved successfully!', array( '[type]' => CBGallery::translateType( $row ) ) )
		CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'TYPE_STATE_SAVED_SUCCESSFULLY ' . strtoupper( $type ) . '_STATE_SAVED_SUCCESSFULLY', '[type] state saved successfully!', array( '[type]' => CBGallery::translateType( $row ) ) ) );
	}

	/**
	 * Deletes an item
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 * @param string    $output
	 */
	private function deleteItem( $id, $viewer, $gallery, $output = null )
	{
		global $_PLUGINS;

		$row		=	$gallery->item( $id );
		$type		=	$row->get( 'type', null, GetterInterface::STRING );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBGallery::canModerate( $gallery ) ) ) ) {
			if ( $output == 'ajax' ) {
				$this->ajaxResponse( false, CBTxt::T( 'Nothing to delete.' ) );
			} else {
				CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
			}
		}

		$source		=	$row->source();

		$_PLUGINS->trigger( 'gallery_onBeforeDeleteGalleryItem', array( $gallery, $source, &$row ) );

		if ( $row->getError() || ( ! $row->canDelete() ) ) {
			// CBTxt::T( 'TYPE_FAILED_TO_DELETE', '[type] failed to delete! Error: [error]', array( '[type]' => CBGallery::translateType( $row ), '[error]' => $row->getError() ) )
			$error	=	CBTxt::T( 'TYPE_FAILED_TO_DELETE ' . strtoupper( $type ) . '_FAILED_TO_DELETE', '[type] failed to delete! Error: [error]', array( '[type]' => CBGallery::translateType( $row ), '[error]' => $row->getError() ) );

			if ( $output == 'ajax' ) {
				$this->ajaxResponse( false, $error );
			} else {
				CBGallery::returnRedirect( $gallery->location(), $error, 'error' );
			}
		}

		if ( $row->getError() || ( ! $row->delete() ) ) {
			// CBTxt::T( 'TYPE_FAILED_TO_DELETE', '[type] failed to delete! Error: [error]', array( '[type]' => CBGallery::translateType( $row ), '[error]' => $row->getError() ) )
			$error	=	CBTxt::T( 'TYPE_FAILED_TO_DELETE ' . strtoupper( $type ) . '_FAILED_TO_DELETE', '[type] failed to delete! Error: [error]', array( '[type]' => CBGallery::translateType( $row ), '[error]' => $row->getError() ) );

			if ( $output == 'ajax' ) {
				$this->ajaxResponse( false, $error );
			} else {
				CBGallery::returnRedirect( $gallery->location(), $error, 'error' );
			}
		}

		$_PLUGINS->trigger( 'gallery_onAfterDeleteGalleryItem', array( $gallery, $source, $row ) );

		if ( $output == 'ajax' ) {
			$this->ajaxResponse( true );
		} else {
			// CBTxt::T( 'TYPE_DELETED_SUCCESSFULLY', '[type] deleted successfully!', array( '[type]' => CBGallery::translateType( $row ) ) )
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'TYPE_DELETED_SUCCESSFULLY ' . strtoupper( $type ) . '_DELETED_SUCCESSFULLY', '[type] deleted successfully!', array( '[type]' => CBGallery::translateType( $row ) ) ) );
		}
	}

	/**
	 * Displays folder create/edit page
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 */
	public function showFolderEdit( $id, $viewer, $gallery )
	{
		global $_CB_framework;

		CBGallery::getTemplate( array( 'folder_edit', 'folder_container' ) );

		$row							=	$gallery->folder( $id );
		$canModerate					=	CBGallery::canModerate( $gallery );

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! $canModerate ) ) {
				CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
			}
		} elseif ( ! CBGallery::canCreateFolders( $gallery ) ) {
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$input							=	array();

		$publishedTooltip				=	cbTooltip( null, CBTxt::T( 'Select publish status of the album. If unpublished the album will not be visible to the public.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['published']				=	moscomprofilerHTML::yesnoSelectList( 'published', 'class="form-control"' . $publishedTooltip, (int) $this->input( 'post/published', $row->get( 'published', 1, GetterInterface::INT ), GetterInterface::INT ) );

		$titleTooltip					=	cbTooltip( null, CBTxt::T( 'Optionally input a title. If no title is provided the date will be displayed as the title.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['title']					=	'<input type="title" id="title" name="title" value="' . htmlspecialchars( $this->input( 'post/title', $row->get( 'title', null, GetterInterface::STRING ), GetterInterface::STRING ) ) . '" class="form-control" size="25"' . $titleTooltip . ' />';

		$descriptionTooltip				=	cbTooltip( null, CBTxt::T( 'Optionally input a description.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['description']			=	'<textarea id="description" name="description" class="form-control" cols="40" rows="5"' . $descriptionTooltip . '>' . $this->input( 'post/description', $row->get( 'description', null, GetterInterface::STRING ), GetterInterface::STRING ) . '</textarea>';

		$ownerTooltip					=	cbTooltip( null, CBTxt::T( 'Input owner as single integer user_id.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['user_id']				=	'<input type="text" id="user_id" name="user_id" value="' . $this->input( 'post/user_id', $row->get( 'user_id', $gallery->user()->get( 'id', $viewer->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT ) ), GetterInterface::INT ) . '" class="digits required form-control" size="6"' . $ownerTooltip . ' />';

		$items							=	array();

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			$gallery->set( 'folder', $row->get( 'id', 0, GetterInterface::INT ) );

			$itemsPrefix				=	'gallery_' . $gallery->id() . '_' . $row->get( 'id', 0, GetterInterface::INT ) . '_edit_items_';
			$itemsLimitstart			=	(int) $_CB_framework->getUserStateFromRequest( $itemsPrefix . 'limitstart{com_comprofiler}', $itemsPrefix . 'limitstart' );

			if ( ! $canModerate ) {
				$gallery->set( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ) );
			}

			$itemsTotal					=	$gallery->items( 'count' );

			if ( $itemsTotal <= $itemsLimitstart ) {
				$itemsLimitstart		=	0;
			}

			$itemsPageNav				=	new cbPageNav( $itemsTotal, $itemsLimitstart, $gallery->get( 'folders_items_paging_limit', 0, GetterInterface::INT ) );

			$itemsPageNav->setBaseURL( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'folder', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn( true ) ) ) );
			$itemsPageNav->setInputNamePrefix( $itemsPrefix );

			$gallery->set( 'folders_items_paging_limitstart', $itemsPageNav->limitstart );

			foreach ( $gallery->items() as $item ) {
				$items[]				=	$this->showItemEdit( $item, $viewer, $gallery, 'folder' );
			}
		} else {
			$itemsPageNav				=	new cbPageNav( 0, 0, 0 );
		}

		HTML_cbgalleryFolderEdit::showFolderEdit( $row, $items, $itemsPageNav, $input, $viewer, $gallery, $this );
	}

	/**
	 * Saves a folder
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 */
	private function saveFolderEdit( $id, $viewer, $gallery )
	{
		global $_CB_framework, $_PLUGINS;

		$row						=	$gallery->folder( $id );
		$canModerate				=	CBGallery::canModerate( $gallery );

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! $canModerate ) ) {
				CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
			}
		} elseif ( ! CBGallery::canCreateFolders( $gallery ) ) {
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( $canModerate || ( ! $gallery->get( 'folders_create_approval', false, GetterInterface::BOOLEAN ) ) || ( $row->get( 'id', 0, GetterInterface::INT ) && ( $row->get( 'published', 1, GetterInterface::INT ) != -1 ) ) ) {
			$row->set( 'published', $this->input( 'post/published', $row->get( 'published', 1, GetterInterface::INT ), GetterInterface::INT ) );
		} else {
			$row->set( 'published', ( $gallery->get( 'folders_create_approval', false, GetterInterface::BOOLEAN  ) ? -1 : $row->get( 'published', 1, GetterInterface::INT ) ) );
		}

		$row->set( 'asset', $row->get( 'asset', $gallery->asset(), GetterInterface::STRING ) );
		$row->set( 'title', $this->input( 'post/title', $row->get( 'title', null, GetterInterface::STRING ), GetterInterface::STRING ) );
		$row->set( 'description', $this->input( 'post/description', $row->get( 'description', null, GetterInterface::STRING ), GetterInterface::STRING ) );

		if ( Application::MyUser()->isGlobalModerator() ) {
			$row->set( 'user_id', $this->input( 'post/user_id', $row->get( 'user_id', $gallery->user()->get( 'id', $viewer->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT ) ), GetterInterface::INT ) );
		} else {
			$row->set( 'user_id', $row->get( 'user_id', $gallery->user()->get( 'id', $viewer->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT ), GetterInterface::INT  ) );
		}

		if ( $gallery->get( 'folders_create_captcha', false, GetterInterface::BOOLEAN ) && ( ! $canModerate ) ) {
			$_PLUGINS->loadPluginGroup( 'user' );

			$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );

			if ( $_PLUGINS->is_errors() ) {
				$row->setError( $_PLUGINS->getErrorMSG() );
			}
		}

		$new						=	( $row->get( 'id', 0, GetterInterface::INT ) ? false : true );
		$old						=	new ItemTable();
		$source						=	$row->source();

		if ( ! $new ) {
			$old->load( $row->get( 'id', 0, GetterInterface::INT ) );

			$_PLUGINS->trigger( 'gallery_onBeforeUpdateGalleryFolder', array( $gallery, $source, &$row, $old ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onBeforeCreateGalleryFolder', array( $gallery, $source, &$row ) );
		}

		$newParams					=	clone $row->params();

		$newParams->unsetEntry( 'overrides' );

		$row->set( 'params', $newParams->asJson() );

		if ( $row->getError() || ( ! $row->check() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'ALBUM_FAILED_TO_SAVE', 'Album failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showFolderEdit( $id, $viewer, $gallery );
			return;
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'ALBUM_FAILED_TO_SAVE', 'Album failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showFolderEdit( $id, $viewer, $gallery );
			return;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gallery_onAfterUpdateGalleryFolder', array( $gallery, $source, $row, $old ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onAfterCreateGalleryFolder', array( $gallery, $source, $row ) );
		}

		if ( $row->get( 'published', 1, GetterInterface::INT ) == -1 ) {
			if ( $new && ( ! $canModerate ) && $gallery->get( 'folders_create_approval_notify', true, GetterInterface::BOOLEAN ) ) {
				$cbUser				=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false );

				$extraStrings		=	array(	'folder_id'				=>	$row->get( 'id', 0, GetterInterface::INT ),
												'folder_title'			=>	( $row->get( 'title', null, GetterInterface::STRING ) ? $row->get( 'title', null, GetterInterface::STRING ) : cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, false, 'M j, Y', ' g:i A' ) ),
												'folder_description'	=>	$row->get( 'description', null, GetterInterface::STRING ),
												'folder_date'			=>	$row->get( 'date', null, GetterInterface::STRING ),
												'folder_url'			=>	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'folder', 'func' => 'show', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ) ),
												'gallery_location'		=>	htmlspecialchars( $gallery->location() ),
												'user_url'				=>	$_CB_framework->viewUrl( 'userprofile', true, array( 'user' => $row->get( 'user_id', 0, GetterInterface::INT ) ) )
											);

				$subject			=	$cbUser->replaceUserVars( CBTxt::T( 'Gallery - New Album Created!' ), false, true, $extraStrings, false );
				$message			=	$cbUser->replaceUserVars( CBTxt::T( 'ALBUM_PENDING_APPROVAL', '<a href="[user_url]">[formatname]</a> created album <a href="[folder_url]">[folder_title]</a> and requires approval!' ), false, true, $extraStrings, false );

				$notifications		=	new cbNotification();

				$recipients			=	$gallery->getNotify();

				if ( $recipients ) {
					cbToArrayOfInt( $recipients );

					foreach ( $recipients as $recipient ) {
						$notifications->sendFromSystem( $recipient, $subject, $message, false, 1 );
					}
				} else {
					$notifications->sendToModerators( $subject, $message, false, 1 );
				}
			}

			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Album saved successfully and awaiting approval!' ) );
		} else {
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Album saved successfully!' ) );
		}
	}

	/**
	 * Sets the folder cover to an item in the folder
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 */
	private function saveFolderCover( $id, $viewer, $gallery )
	{
		$row	=	$gallery->folder( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBGallery::canModerate( $gallery ) ) ) ) {
			$this->ajaxResponse( false, CBTxt::T( 'Album does not exist.' ) );
		}

		$item	=	$gallery->item( $this->input( 'item', 0, GetterInterface::INT ) );

		if ( ( ! $item->get( 'id', 0, GetterInterface::INT ) ) || ( $item->get( 'folder', 0, GetterInterface::INT ) != $row->get( 'id', 0, GetterInterface::INT ) ) ) {
			$this->ajaxResponse( false, CBTxt::T( 'File does not exist.' ) );
		}

		$row->set( 'thumbnail', $item->get( 'id', 0, GetterInterface::INT ) );

		if ( $row->getError() || ( ! $row->store() ) ) {
			$this->ajaxResponse( false, CBTxt::T( 'ALBUM_COVER_FAILED_TO_SAVE', 'Album cover failed to save! Error: [error]', array( '[error]' => $row->getError() ) ) );
		}

		$this->ajaxResponse( true, CBTxt::T( 'Album cover saved successfully!' ) );
	}

	/**
	 * Sets the published state of a folder
	 *
	 * @param int       $state
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 */
	private function stateFolder( $state, $id, $viewer, $gallery )
	{
		global $_PLUGINS;

		$row		=	$gallery->folder( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) )
			 || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBGallery::canModerate( $gallery ) ) )
			 || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( $row->get( 'published', 1, GetterInterface::INT ) == -1 ) && $gallery->get( 'folders_create_approval', false, GetterInterface::BOOLEAN ) ) ) {
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$row->set( 'published', (int) $state );

		$source		=	$row->source();

		if ( $state ) {
			$_PLUGINS->trigger( 'gallery_onBeforePublishGalleryFolder', array( $gallery, $source, &$row ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onBeforeUnpublishGalleryFolder', array( $gallery, $source, &$row ) );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'ALBUM_STATE_FAILED_TO_SAVE', 'Album state failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $state ) {
			$_PLUGINS->trigger( 'gallery_onAfterPublishGalleryFolder', array( $gallery, $source, $row ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onAfterUnpublishGalleryFolder', array( $gallery, $source, $row ) );
		}

		CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Album state saved successfully!' ) );
	}

	/**
	 * Deletes a folder
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Gallery   $gallery
	 */
	private function deleteFolder( $id, $viewer, $gallery )
	{
		global $_PLUGINS;

		$row		=	$gallery->folder( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBGallery::canModerate( $gallery ) ) ) ) {
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$source		=	$row->source();

		$_PLUGINS->trigger( 'gallery_onBeforeDeleteGalleryFolder', array( $gallery, $source, &$row ) );

		if ( $row->getError() || ( ! $row->canDelete() ) ) {
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'ALBUM_FAILED_TO_DELETE', 'Album failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->delete() ) ) {
			CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'ALBUM_FAILED_TO_DELETE', 'Album failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'gallery_onAfterDeleteGalleryFolder', array( $gallery, $source, $row ) );

		CBGallery::returnRedirect( $gallery->location(), CBTxt::T( 'Album deleted successfully!' ) );
	}
}