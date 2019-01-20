<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery\Trigger;

use CB\Plugin\Gallery\Table\FolderTable;
use CB\Plugin\Gallery\Table\ItemTable;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CB\Plugin\Activity\Comments;
use CB\Database\Table\UserTable;

defined('CBLIB') or die();

class ActivityTrigger extends \cbPluginHandler
{

	/**
	 * @return bool
	 */
	private function isCompatible()
	{
		global $_PLUGINS;

		static $compatible		=	null;

		if ( $compatible === null ) {
			$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' );

			if ( $plugin ) {
				$pluginVersion	=	str_replace( '+build.', '+', $_PLUGINS->getPluginVersion( $plugin, true ) );

				$compatible		=	( version_compare( $pluginVersion, '4.0.0', '>=' ) && version_compare( $pluginVersion, '5.0.0', '<' ) );
			}
		}

		return $compatible;
	}

	/**
	 * @param int               $profileId
	 * @param null|string|array $asset
	 * @return Gallery|null
	 */
	private function activityGallery( $profileId, $asset = null )
	{
		if ( ! $profileId ) {
			return null;
		}

		static $galleries						=	array();

		if ( ! isset( $galleries[$profileId][$asset] ) ) {
			$tab								=	CBGallery::getTab( null, $profileId );

			if ( ! $tab ) {
				return null;
			}

			$gallery							=	new Gallery( $asset, $profileId );

			$gallery->set( 'tab', $tab->get( 'tabid', 0, GetterInterface::INT ) );

			$gallery->parse( $tab->params, 'gallery_' );

			$gallery->cache();

			$galleries[$profileId][$asset]		=	$gallery;
		}

		return $galleries[$profileId][$asset];
	}

	/**
	 * @param null|string|array  $assets
	 * @param null|int|UserTable $user
	 * @param array              $defaults
	 * @param Activity|Comments  $stream
	 */
	public function extendParameters( &$assets, &$user, &$defaults, &$stream )
	{
		if ( $stream instanceof Comments ) {
			$defaults['gallery']						=	true;
			$defaults['replies_gallery']				=	false;
		} else {
			$defaults['gallery']						=	true;
			$defaults['comments_gallery']				=	false;
			$defaults['comments_replies_gallery']		=	false;
		}
	}

	/**
	 * @param string                 $output
	 * @param array                  $select
	 * @param array                  $join
	 * @param array                  $where
	 * @param Activity|Notifications $stream
	 */
	public function activityQuery( $output, &$select, &$join, &$where, &$stream )
	{
		global $_CB_database;

		if ( ! $this->isCompatible() ) {
			return;
		}

		$media			=	"SELECT COUNT(*)"
						.	" FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' ) . " AS gallery_item"
						.	" LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_folders' ) . " AS gallery_folder"
						.	" ON gallery_item." . $_CB_database->NameQuote( 'folder' ) . " = gallery_folder." . $_CB_database->NameQuote( 'id' )
						.	" WHERE gallery_item." . $_CB_database->NameQuote( 'id' ) . " = SUBSTRING_INDEX( REPLACE( REPLACE( a." . $_CB_database->NameQuote( 'asset' ) . ", CONCAT( 'notification.gallery.', gallery_item." . $_CB_database->NameQuote( 'type' ) . ", '.' ), '' ), CONCAT( 'gallery.', gallery_item." . $_CB_database->NameQuote( 'type' ) . ", '.' ), '' ), '.', 1 )";

		if ( ! Application::MyUser()->isGlobalModerator() ) {
			$media		.=	" AND ( gallery_item." . $_CB_database->NameQuote( 'published' ) . " = 1"
						.	" OR gallery_item." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) Application::MyUser()->getUserId() . " )"
						.	" AND ( gallery_item." . $_CB_database->NameQuote( 'folder' ) . " = 0"
						.	" OR ( gallery_folder." . $_CB_database->NameQuote( 'id' ) . " IS NOT NULL"
						.	" AND ( gallery_folder." . $_CB_database->NameQuote( 'published' ) . " = 1"
						.	" OR gallery_folder." . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) Application::MyUser()->getUserId() . " ) ) )";
		} else {
			$media		.=	" AND ( gallery_item." . $_CB_database->NameQuote( 'folder' ) . " = 0"
						.	" OR gallery_folder." . $_CB_database->NameQuote( 'id' ) . " IS NOT NULL )";
		}

		$where[]		=	"( ( a." . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( 'gallery.%' )
						.	" AND ( " . $media . " ) > 0 )"
						.	" OR ( a." . $_CB_database->NameQuote( 'asset' ) . " NOT LIKE " . $_CB_database->Quote( 'gallery.%' ) . " ) )";
	}

	/**
	 * @param ActivityTable[]|NotificationTable[] $rows
	 * @param Activity|Notifications              $stream
	 */
	public function activityLoad( &$rows, $stream )
	{
		if ( ! $this->isCompatible() ) {
			return;
		}

		$notification									=	( $stream instanceof NotificationsInterface );
		$items											=	array();

		foreach ( $rows as $k => $row ) {
			$profileId									=	$row->get( 'user_id', 0, GetterInterface::INT );

			if ( ! $notification ) {
				$uploads								=	cbToArrayOfInt( $row->params()->get( 'gallery', array(), GetterInterface::RAW ) );

				if ( $uploads && ( ! $row->get( 'message', null, GetterInterface::STRING ) ) ) {
					$gallery							=	$this->activityGallery( $profileId );

					if ( ! $gallery ) {
						unset( $rows[$k] );
						continue;
					}

					if ( ! $gallery->reset()->setId( $uploads )->items( 'count' ) ) {
						unset( $rows[$k] );
						continue;
					}
				}
			}

			if ( ! preg_match( '/^gallery\.(photos|files|videos|music)\.(\d+)(?:\.(like|comment|tag))?/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
				continue;
			}

			$id											=	(int) $matches[2];

			if ( ! $id ) {
				unset( $rows[$k] );
				continue;
			}

			$found										=	false;

			if ( $this->activityGallery( $profileId )  ) {
				$items[$profileId][$k]					=	$id;

				$found									=	true;
			}

			if ( ! $found ) {
				unset( $rows[$k] );
				continue;
			}

			if ( ! $notification ) {
				$row->params()->set( 'overrides.tags_asset', 'asset' );
				$row->params()->set( 'overrides.likes_asset', 'asset' );
				$row->params()->set( 'overrides.comments_asset', 'asset' );
			}
		}

		static $previous								=	array();

		$found											=	array();

		foreach ( $items as $profileId => $media ) {
			$mediaIds									=	cbToArrayOfInt( array_unique( $media ) );

			if ( ! $mediaIds ) {
				continue;
			}

			$gallery									=	$this->activityGallery( $profileId );

			if ( ! $gallery ) {
				continue;
			}

			$folders									=	array();
			$galleryItems								=	$gallery->reset()->setId( $mediaIds )->items();
			$found										=	( $found + $galleryItems );

			foreach ( $mediaIds as $rowId => $itemId ) {
				if ( ( ! key_exists( $itemId, $found ) ) && isset( $rows[$rowId] ) ) {
					continue;
				}

				$folder									=	$found[$itemId]->get( 'folder', null, GetterInterface::INT );

				$folders[$folder][$rowId]				=	$itemId;
			}

			if ( $folders ) {
				$galleryFolders							=	array_keys( $gallery->reset()->setId( array_keys( $folders ) )->folders() );

				foreach ( $folders as $folderId => $folderItems ) {
					if ( count( $folderItems ) <= 1 ) {
						if ( $folderId && ( ! in_array( $folderId, $galleryFolders ) ) ) {
							foreach ( $folderItems as $rowId => $itemId ) {
								unset( $found[$itemId] );
							}

							continue;
						}

						continue;
					}

					foreach ( $folderItems as $rowId => $itemId ) {
						if ( $folderId ) {
							if ( ! in_array( $folderId, $galleryFolders ) ) {
								unset( $found[$itemId] );
								continue;
							}

							if ( ! $notification ) {
								$rows[$rowId]->params()->set( 'overrides.tags_asset', 'gallery.folder.' . $folderId );
								$rows[$rowId]->params()->set( 'overrides.likes_asset', 'gallery.folder.' . $folderId );
								$rows[$rowId]->params()->set( 'overrides.comments_asset', 'gallery.folder.' . $folderId );
							}
						}

						if ( $notification ) {
							continue;
						}

						if ( ! isset( $previous[$profileId] ) ) {
							$previous[$profileId]		=	array( &$rows[$rowId], &$found[$itemId] );
						} else {
							/** @var ActivityTable $previousRow */
							$previousRow				=	$previous[$profileId][0];
							/** @var ItemTable $previousItem */
							$previousItem				=	$previous[$profileId][1];

							$previousItems				=	$previousRow->params()->get( 'overrides.gallery_items', array(), GetterInterface::RAW );
							$dateDiff					=	Application::Date( $previousItem->get( 'date', null, GetterInterface::STRING ), 'UTC' )->diff( $found[$itemId]->get( 'date', null, GetterInterface::STRING ) );

							if ( ( $dateDiff->days == 0 ) && ( $dateDiff->m <= 15 ) ) {
								$previousItems[]		=	$itemId;

								$previousRow->params()->set( 'overrides.gallery_items', $previousItems );

								unset( $rows[$rowId] );
							} else {
								$previous[$profileId]	=	array( &$rows[$rowId], &$found[$itemId] );
							}
						}
					}
				}
			}
		}

		foreach ( $items as $profileId => $media ) {
			$mediaIds									=	cbToArrayOfInt( array_unique( $media ) );

			if ( ! $mediaIds ) {
				continue;
			}

			foreach ( $mediaIds as $rowId => $itemId ) {
				if ( ( ! key_exists( $itemId, $found ) ) && isset( $rows[$rowId] ) ) {
					unset( $rows[$rowId] );
				}
			}
		}
	}

	/**
	 * @param ActivityTable|NotificationTable $row
	 * @param null|string                     $title
	 * @param null|string                     $date
	 * @param null|string                     $message
	 * @param null|string                     $insert
	 * @param null|string                     $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param string                          $output
	 */
	public function activityDisplay( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $output )
	{
		if ( ! $this->isCompatible() ) {
			return;
		}

		$uploads				=	cbToArrayOfInt( $row->params()->get( 'gallery', array(), GetterInterface::RAW ) );
		$matches				=	array();

		if ( ( ! $uploads ) && ( ! preg_match( '/^gallery\.(photos|files|videos|music)\.(\d+)(?:\.(like|comment|tag))?/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) ) {
			return;
		}

		$ids					=	$row->params()->get( 'overrides.gallery_items', array(), GetterInterface::RAW );

		array_unshift( $ids, ( isset( $matches[2] ) ? (int) $matches[2] : 0 ) );

		$ids					=	array_unique( array_merge( $ids, $uploads ) );

		if ( ! $ids ) {
			return;
		}

		$notification			=	( $stream instanceof NotificationsInterface );
		$notifId				=	$row->get( 'user', 0, GetterInterface::INT );
		$profileId				=	$row->get( 'user_id', 0, GetterInterface::INT );
		$items					=	array();

		if ( $notification && ( $notifId != $profileId ) ) {
			$gallery			=	$this->activityGallery( $notifId );

			if ( $gallery ) {
				$items			=	$gallery->reset()->setId( $ids )->items();
			}

			if ( ! $items ) {
				$gallery		=	$this->activityGallery( $profileId );

				if ( $gallery ) {
					$items		=	$gallery->reset()->setId( $ids )->items();
				}
			}
		} else {
			$gallery			=	$this->activityGallery( $profileId );

			if ( $gallery ) {
				$items			=	$gallery->reset()->setId( $ids )->items();
			}
		}

		if ( ! $items ) {
			return;
		}

		CBGallery::getTemplate( array( 'activity', 'item_container' ) );

		if ( $matches && ( ! $notification ) ) {
			if ( count( $items ) > 1 ) {
				$folderId		=	null;

				/** @var ItemTable[] $items */
				foreach ( $items as $i => $item ) {
					if ( $folderId !== null ) {
						break;
					}

					$folderId	=	$item->get( 'folder', 0, GetterInterface::INT );
				}

				if ( $folderId ) {
					$row->params()->set( 'overrides.tags_asset', 'gallery.folder.' . $folderId );
					$row->params()->set( 'overrides.likes_asset', 'gallery.folder.' . $folderId );
					$row->params()->set( 'overrides.comments_asset', 'gallery.folder.' . $folderId );
				} else {
					$row->params()->set( 'overrides.tags_asset', 'asset' );
					$row->params()->set( 'overrides.likes_asset', 'asset' );
					$row->params()->set( 'overrides.comments_asset', 'asset' );
				}
			} else {
				$row->params()->set( 'overrides.tags_asset', 'asset' );
				$row->params()->set( 'overrides.likes_asset', 'asset' );
				$row->params()->set( 'overrides.comments_asset', 'asset' );
			}
		}

		\HTML_cbgalleryActivity::showActivity( $row, $title, $date, $message, $insert, $footer, $menu, $stream, $matches, $items, $gallery, $this, $output );
	}

	/**
	 * @param CommentTable $row
	 * @param null|string  $message
	 * @param null|string  $insert
	 * @param null|string  $date
	 * @param null|string  $footer
	 * @param array        $menu
	 * @param Comments     $stream
	 * @param string       $output
	 */
	public function commentDisplay( &$row, &$message, &$insert, &$date, &$footer, &$menu, $stream, $output )
	{
		if ( ! $this->isCompatible() ) {
			return;
		}

		$uploads		=	cbToArrayOfInt( $row->params()->get( 'gallery', array(), GetterInterface::RAW ) );

		if ( ! $uploads ) {
			return;
		}

		$gallery		=	$this->activityGallery( $row->get( 'user_id', 0, GetterInterface::INT ) );

		if ( ! $gallery ) {
			return;
		}

		$items			=	$gallery->reset()->setId( $uploads )->items();

		if ( ! $items ) {
			return;
		}

		$title			=	null;
		$matches		=	array();

		CBGallery::getTemplate( array( 'activity', 'item_container' ) );

		\HTML_cbgalleryActivity::showActivity( $row, $title, $date, $message, $insert, $footer, $menu, $stream, $matches, $items, $gallery, $this, $output );
	}

	/**
	 * @param string $asset
	 * @param mixed  $source
	 */
	public function assetSource( $asset, &$source )
	{
		if ( ! preg_match( '/^gallery\.(folder|photos|files|videos|music)\.(\d+)(?:\.(like|comment|tag))?/', $asset, $matches ) ) {
			return;
		}

		if ( ( isset( $matches[1] ) ? $matches[1] : null ) == 'folder' ) {
			$row	=	new FolderTable();
		} else {
			$row	=	new ItemTable();
		}

		$row->load( ( isset( $matches[2] ) ? (int) $matches[2] : 0 ) );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$source		=	$row;
	}

	/**
	 * @param array             $buttons
	 * @param UserTable         $viewer
	 * @param Activity|Comments $stream
	 * @param string            $output
	 * @return mixed
	 */
	public function activityUploadNew( &$buttons, $viewer, $stream, $output )
	{
		if ( ! $this->isCompatible() || ( ! $stream->get( 'gallery', false, GetterInterface::BOOLEAN ) ) ) {
			return null;
		}

		$gallery	=	$this->activityGallery( $viewer->get( 'id', 0, GetterInterface::INT ), 'uploads,profile' );

		if ( ! $gallery ) {
			return null;
		}

		CBGallery::getTemplate( 'activity_new' );

		return \HTML_cbgalleryActivityNew::showActivityNew( $buttons, $viewer, $stream, $gallery, $this, $output );
	}

	/**
	 * @param ActivityTable|CommentTable $row
	 * @param array                      $buttons
	 * @param UserTable                  $viewer
	 * @param Activity|Comments          $stream
	 * @param string                     $output
	 * @return mixed
	 */
	public function activityUploadEdit( &$row, &$buttons, $viewer, $stream, $output )
	{
		if ( ! $this->isCompatible() ) {
			return null;
		}

		$uploads	=	cbToArrayOfInt( $row->params()->get( 'gallery', array(), GetterInterface::RAW ) );

		if ( ! $uploads ) {
			return null;
		}

		$gallery	=	$this->activityGallery( $viewer->get( 'id', 0, GetterInterface::INT ) );

		if ( ! $gallery ) {
			return null;
		}

		$items		=	$gallery->reset()->setId( $uploads )->items();

		if ( ! $items ) {
			return null;
		}

		CBGallery::getTemplate( array( 'activity_edit', 'item_edit_micro', 'item_container' ) );

		return \HTML_cbgalleryActivityEdit::showActivityEdit( $row, $items, $buttons, $viewer, $stream, $gallery, $this, $output );
	}

	/**
	 * @param Activity|Comments          $stream
	 * @param mixed                      $source
	 * @param ActivityTable|CommentTable $row
	 */
	public function activityUploadSave( $stream, $source, &$row )
	{
		if ( ( ! $this->isCompatible() ) || ( $row->get( 'user_id', 0, GetterInterface::INT ) != Application::MyUser()->getUserId() ) || ( ! $stream->get( 'gallery', false, GetterInterface::BOOLEAN ) ) ) {
			return;
		}

		$gallery		=	$this->activityGallery( $row->get( 'user_id', 0, GetterInterface::INT ) );

		if ( ! $gallery ) {
			return;
		}

		$items			=	cbToArrayOfInt( Application::Input()->get( 'items', array(), GetterInterface::RAW ) );

		if ( ! $items ) {
			return;
		}

		$media			=	array();

		foreach ( $gallery->reset()->setId( $items )->items() as $item ) {
			$media[]	=	$item->get( 'id', 0, GetterInterface::INT );
		}

		if ( $media ) {
			$row->params()->set( 'overrides.message', false );
			$row->params()->set( 'gallery', $media );
		}
	}
}