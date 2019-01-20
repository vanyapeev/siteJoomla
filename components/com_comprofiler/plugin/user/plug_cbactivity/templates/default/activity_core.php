<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CBLib\Database\Table\Table;
use CB\Plugin\Activity\CBActivity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityActivityCore
{

	/**
	 * parses core activity access checks
	 *
	 * @param ActivityTable[]|NotificationTable[] $rows
	 * @param Activity|Notifications              $stream
	 */
	static public function parseAccess( &$rows, $stream )
	{
		static $group						=	array();

		$notification						=	( $stream instanceof NotificationsInterface );

		foreach ( $rows as $k => $row ) {
			$asset							=	$row->get( 'asset', null, GetterInterface::STRING );

			if ( preg_match( '/^profile\.(\d+)\.field\.(\d+)/', $asset, $matches ) ) {
				$profileId					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );
				$fieldId					=	( isset( $matches[2] ) ? (int) $matches[2] : 0 );

				if ( ( ! $profileId ) || ( ! $fieldId ) || ( ! CBActivity::getField( $fieldId, $profileId ) ) ) {
					unset( $rows[$k] );
					continue;
				}
			} else {
				if ( preg_match( '/^blog\.(\d+)(?:\.(comment|like)(?:\.(\d+))?)?/', $asset, $matches ) ) {
					if ( $notification ) {
						continue;
					}

					if ( isset( $matches[2] ) ) {
						$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

						$row->params()->set( 'overrides.tags_asset', 'blog.' . $id );
						$row->params()->set( 'overrides.likes_asset', 'blog.' . $id );
						$row->params()->set( 'overrides.comments_asset', 'blog.' . $id );
					} else {
						$row->params()->set( 'overrides.tags_asset', 'asset' );
						$row->params()->set( 'overrides.likes_asset', 'asset' );
						$row->params()->set( 'overrides.comments_asset', 'asset' );
					}
				} elseif ( preg_match( '/^article\.(\d+)(?:\.(comment|like)(?:\.(\d+))?)?/', $asset, $matches ) ) {
					if ( $notification ) {
						continue;
					}

					if ( isset( $matches[2] ) ) {
						$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

						$row->params()->set( 'overrides.tags_asset', 'article.' . $id );
						$row->params()->set( 'overrides.likes_asset', 'article.' . $id );
						$row->params()->set( 'overrides.comments_asset', 'article.' . $id );
					} else {
						$row->params()->set( 'overrides.tags_asset', 'asset' );
						$row->params()->set( 'overrides.likes_asset', 'asset' );
						$row->params()->set( 'overrides.comments_asset', 'asset' );
					}
				} elseif ( preg_match( '/^(?:activity|comment)\.(\d+)\.(reply|comment|tag|like)(?:\.(\d+))?/', $asset, $matches ) ) {
					if ( ! isset( $group[$asset] ) ) {
						$group[$asset]		=	&$rows[$k];
					} else {
						$dateDiff			=	Application::Date( $group[$asset]->get( 'date', null, GetterInterface::STRING ), 'UTC' )->diff( $row->get( 'date', null, GetterInterface::STRING ) );

						if ( $dateDiff->days <= 10 ) {
							$names			=	$group[$asset]->params()->get( 'overrides.names', array(), GetterInterface::RAW );

							$names[]		=	$row->get( 'user_id', 0, GetterInterface::INT );

							$group[$asset]->params()->set( 'overrides.names', array_unique( $names ) );

							unset( $rows[$k] );
						} else {
							$group[$asset]	=	&$rows[$k];
						}
					}
				}
			}
		}
	}

	/**
	 * parses core activity asset source
	 *
	 * @param string $asset
	 * @param mixed  $source
	 */
	static public function parseSource( $asset, &$source )
	{
		global $_CB_database, $_PLUGINS;

		if ( preg_match( '/^article\.(\d+)/', $asset, $matches ) ) {
			if ( ! $_PLUGINS->getLoadedPlugin( 'user', 'cbarticles' ) ) {
				return;
			}

			$model				=	cbarticlesClass::getModel();

			if ( ! $model->file ) {
				return;
			}

			static $article		=	array();

			$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

			if ( ! isset( $article[$id] ) ) {
				if ( $model->type == 5 ) {
					$query		=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__k2_items' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " = " . (int) $id;
					$_CB_database->setQuery( $query, 0, 1 );
					$details	=	$_CB_database->loadAssoc();

					$row		=	new Table( null, '#__k2_items', 'id' );
				} else {
					$query		=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__content' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " = " . (int) $id;
					$_CB_database->setQuery( $query, 0, 1 );
					$details	=	$_CB_database->loadAssoc();

					$row		=	new Table( null, '#__content', 'id' );
				}

				foreach ( $details as $k => $v ) {
					$row->set( $k, $v );
				}

				$article[$id]	=	$row;
			}

			$source				=	$article[$id];
		} elseif ( preg_match( '/^blog\.(\d+)/', $asset, $matches ) ) {
			if ( ! $_PLUGINS->getLoadedPlugin( 'user', 'cbblogs' ) ) {
				return;
			}

			$model				=	cbblogsClass::getModel();

			if ( ! $model->file ) {
				return;
			}

			static $blog		=	array();

			$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

			if ( ! isset( $blog[$id] ) ) {
				$row			=	new cbblogsBlogTable();

				$row->load( $id );

				$blog[$id]		=	$row;
			}

			$source				=	$blog[$id];
		} elseif ( preg_match( '/^kunena\.(\d+)/', $asset, $matches ) ) {
			if ( ! $_PLUGINS->getLoadedPlugin( 'user', 'cbforums' ) ) {
				return;
			}

			$model				=	cbforumsClass::getModel();

			if ( ! $model->file ) {
				return;
			}

			if ( ! class_exists( 'KunenaForumMessageHelper' ) ) {
				return;
			}

			static $post		=	array();

			$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

			if ( ! isset( $post[$id] ) ) {
				$post[$id]		=	KunenaForumMessageHelper::get( $id );
			}

			$source				=	$post[$id];
		}
	}

	/**
	 * parses core activity display
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $message
	 * @param string                          $date
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static public function parseDisplay( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $plugin, $output )
	{
		$rowAsset		=	$row->get( 'asset', null, GetterInterface::STRING );

		if ( preg_match( '/^profile\.(\d+)\.([a-zA-Z]+)(?:\.(.+))?/', $rowAsset, $matches ) ) {
			switch ( $matches[2] ) {
				case 'login';
					self::showLogin( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
					break;
				case 'logout';
					self::showLogout( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
					break;
				case 'registration';
					self::showRegistration( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
					break;
				case 'update';
					self::showUpdate( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
					break;
				case 'avatar';
					self::showAvatar( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
					break;
				case 'canvas';
					self::showCanvas( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
					break;
				case 'connection';
					self::showConnected( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
					break;
			}
		} elseif ( preg_match( '/^activity\.(\d+)(?:\.(comment|tag|like)(?:\.(\d+))?)?/', $rowAsset, $matches ) ) {
			self::showActivity( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
		} elseif ( preg_match( '/^comment\.(\d+)(?:\.(reply|tag|like)(?:\.(\d+))?)?/', $rowAsset, $matches ) ) {
			self::showComment( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
		} elseif ( preg_match( '/^blog\.(\d+)(?:\.(comment|like)(?:\.(\d+))?)?/', $rowAsset, $matches ) ) {
			self::showBlog( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
		} elseif ( preg_match( '/^article\.(\d+)(?:\.(comment|like)(?:\.(\d+))?)?/', $rowAsset, $matches ) ) {
			self::showArticle( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
		} elseif ( preg_match( '/^kunena\.(\d+)\.(create|reply)/', $rowAsset, $matches ) ) {
			self::showTopic( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $matches, $plugin, $output );
		}
	}

	/**
	 * render frontend login activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showLogin( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		$title			=	CBTxt::T( 'has logged in' );

		if ( ! $stream instanceof NotificationsInterface ) {
			$message	=	null;

			$row->params()->set( 'overrides.edit', false );
			$row->params()->set( 'overrides.actions', false );
			$row->params()->set( 'overrides.locations', false );
			$row->params()->set( 'overrides.links', false );
			$row->params()->set( 'overrides.tags', false );
			$row->params()->set( 'overrides.likes', false );
		}
	}

	/**
	 * render frontend logout activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showLogout( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		$title			=	CBTxt::T( 'has logged out' );

		if ( ! $stream instanceof NotificationsInterface ) {
			$message	=	null;

			$row->params()->set( 'overrides.edit', false );
			$row->params()->set( 'overrides.actions', false );
			$row->params()->set( 'overrides.locations', false );
			$row->params()->set( 'overrides.links', false );
			$row->params()->set( 'overrides.tags', false );
			$row->params()->set( 'overrides.likes', false );
		}
	}

	/**
	 * render frontend registration activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showRegistration( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		$title			=	CBTxt::T( 'joined the site' );

		if ( ! $stream instanceof NotificationsInterface ) {
			$message	=	null;

			$row->params()->set( 'overrides.edit', false );
			$row->params()->set( 'overrides.actions', false );
			$row->params()->set( 'overrides.locations', false );
			$row->params()->set( 'overrides.links', false );
			$row->params()->set( 'overrides.tags', false );
			$row->params()->set( 'overrides.likes', false );
		}
	}

	/**
	 * render frontend profile update activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showUpdate( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		$title			=	CBTxt::T( 'updated their profile' );

		if ( ! $stream instanceof NotificationsInterface ) {
			$message	=	null;

			$row->params()->set( 'overrides.edit', false );
			$row->params()->set( 'overrides.actions', false );
			$row->params()->set( 'overrides.locations', false );
			$row->params()->set( 'overrides.links', false );
			$row->params()->set( 'overrides.tags', false );
			$row->params()->set( 'overrides.likes', false );
		}
	}

	/**
	 * render frontend avatar activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showAvatar( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		$title			=	CBTxt::T( 'updated their profile picture' );

		if ( ! $stream instanceof NotificationsInterface ) {
			$message	=	null;

			$insert		=	'<div class="streamItemInsert">'
						.		CBuser::getInstance( (int) $assetMatches[1], false )->getField( 'avatar', null, 'html', 'none', 'profile', 0, true, array( '_noAjax' => true ) )
						.	'</div>';

			$row->params()->set( 'overrides.edit', false );
			$row->params()->set( 'overrides.actions', false );
			$row->params()->set( 'overrides.locations', false );
			$row->params()->set( 'overrides.links', false );
			$row->params()->set( 'overrides.tags', false );
			$row->params()->set( 'overrides.likes', false );
		}
	}

	/**
	 * render frontend canvas activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showCanvas( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		$title			=	CBTxt::T( 'updated their canvas photo' );

		$insert			=	'<div class="streamItemInsert">'
						.		CBuser::getInstance( (int) $assetMatches[1], false )->getField( 'canvas', null, 'html', 'none', 'profile', 0, true, array( '_noAjax' => true ) )
						.	'</div>';

		if ( ! $stream instanceof NotificationsInterface ) {
			$message	=	null;

			$row->params()->set( 'overrides.edit', false );
			$row->params()->set( 'overrides.actions', false );
			$row->params()->set( 'overrides.locations', false );
			$row->params()->set( 'overrides.links', false );
			$row->params()->set( 'overrides.tags', false );
			$row->params()->set( 'overrides.likes', false );
		}
	}

	/**
	 * render frontend connected activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showConnected( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		global $_CB_framework;

		$profileId						=	(int) $assetMatches[1];

		if ( $stream instanceof NotificationsInterface ) {
			$viewer						=	CBuser::getMyUserDataInstance();

			if ( isset( $assetMatches[3] ) ) {
				switch ( $assetMatches[3] ) {
					case 'pending':
						if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $profileId ) {
							$title		=	CBTxt::T( 'would like to connect with you' );

							$date		.=	'<div class="notificationsButtonsConn pull-right">'
										.		'<a href="' . $_CB_framework->viewUrl( 'acceptconnection', true, array( 'connectionid' => $row->get( 'user_id', 0, GetterInterface::INT ) ) ) . '" class="notificationsButton notificationsButtonAcceptConn btn btn-xs btn-success">' . CBTxt::T( 'Accept' ) . '</a>'
										.		' <a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->viewUrl( 'denyconnection', true, array( 'connectionid' => $row->get( 'user_id', 0, GetterInterface::INT ) ) ) ) . '\'; })" class="notificationsButton notificationsButtonRejectConn btn btn-xs btn-danger">' . CBTxt::T( 'Reject' ) . '</a>'
										.	'</div>';
						} else {
							if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user', 0, GetterInterface::INT ) ) {
								$title	=	CBTxt::T( 'connection request is pending' );

								$date	.=	'<div class="notificationsButtonsConn pull-right">'
										.		'<a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->viewUrl( 'removeconnection', true, array( 'connectionid' => $row->get( 'user_id', 0, GetterInterface::INT ) ) ) ) . '\'; })" class="notificationsButton notificationsButtonCancelConn btn btn-xs btn-danger">' . CBTxt::T( 'Cancel Request' ) . '</a>'
										.	'</div>';
							} else {
								$title	=	CBTxt::T( 'ACTIVITY_WOULD_LIKE_CONNECT_WITH_USER', 'would like to connect with [user]', array( '[user]' => CBuser::getInstance( $profileId, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) ) );
							}
						}
						break;
					case 'accepted':
						if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $profileId ) {
							$title		=	CBTxt::T( 'accepted your connection request' );
						} else {
							if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user', 0, GetterInterface::INT ) ) {
								$title	=	CBTxt::T( 'connection request was accepted' );
							} else {
								$title	=	CBTxt::T( 'ACTIVITY_ACCEPTED_CONNECT_FROM_USER', 'accepted connection request from [user]', array( '[user]' => CBuser::getInstance( $profileId, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) ) );
							}
						}
						break;
					case 'rejected':
						if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $profileId ) {
							$title		=	CBTxt::T( 'rejected your connection request' );
						} else {
							if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user', 0, GetterInterface::INT ) ) {
								$title	=	CBTxt::T( 'connection request was rejected' );
							} else {
								$title	=	CBTxt::T( 'ACTIVITY_REJECTED_CONNECT_FROM_USER', 'rejected connection request from [user]', array( '[user]' => CBuser::getInstance( $profileId, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) ) );
							}
						}
						break;
				}
			} else {
				if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $profileId ) {
					$title				=	CBTxt::T( 'is now connected with you' );
				} else {
					$title				=	CBTxt::T( 'ACTIVITY_IS_NOW_CONNECTED_WITH_USER', 'is now connected with [user]', array( '[user]' => CBuser::getInstance( $profileId, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) ) );
				}
			}
		} else {
			$title						=	CBTxt::T( 'ACTIVITY_IS_NOW_CONNECTED_WITH_USER', 'is now connected with [user]', array( '[user]' => CBuser::getInstance( $profileId, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) ) );
		}
	}

	/**
	 * render frontend activity activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showActivity( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		global $_CB_framework;

		$notification					=	( $stream instanceof NotificationsInterface );
		$id								=	(int) $assetMatches[1];
		$type							=	( isset( $assetMatches[2] ) ? $assetMatches[2] : null );

		switch ( $type ) {
			case 'comment':
				if ( $notification ) {
					$title				=	CBTxt::T( 'COMMENTED_ON_POST_TITLE', 'commented on post <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'show', 'id' => $id ) ), '[title]' => '#' . $id ) );
				} else {
					$title				=	CBTxt::T( 'commented on this' );
				}
				break;
			case 'tag':
				if ( $notification ) {
					$title				=	CBTxt::T( 'TAGGED_IN_POST_TITLE', 'tagged in post <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'show', 'id' => $id ) ), '[title]' => '#' . $id ) );
				} else {
					$title				=	CBTxt::T( 'was tagged in this' );
				}
				break;
			case 'like':
				if ( $notification ) {
					$title				=	CBTxt::T( 'LIKED_POST_TITLE', 'liked post <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'show', 'id' => $id ) ), '[title]' => '#' . $id ) );
				} else {
					$title				=	CBTxt::T( 'liked this' );
				}
				break;
		}

		static $cache					=	array();

		$activity						=	$stream->row( $id );

		if ( ! $activity->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ! isset( $cache[$id] ) ) {
				$activityRow			=	new ActivityTable();

				$activityRow->load( $id );

				$cache[$id]				=	$activityRow;
			}

			/** @var ActivityTable $activity */
			$activity					=	$cache[$id];
		}

		if ( ! $activity->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$viewer							=	CBuser::getMyUserDataInstance();

		if ( $notification ) {
			$activityTitle				=	$stream->parser( $activity->get( 'title', null, GetterInterface::STRING ) )->parse( array( 'linebreaks' ), false );

			if ( ! $activityTitle ) {
				$activityTitle			=	$stream->parser( $activity->get( 'message', null, GetterInterface::STRING ) )->parse( array( 'linebreaks' ), false );
			}

			if ( cbutf8_strlen( $activityTitle ) > 50 ) {
				$activityTitle			=	trim( cbutf8_substr( $activityTitle, 0, 50 ) ) . '...';
			}

			if ( $activityTitle ) {
				switch ( $type ) {
					case 'comment':
						if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $activity->get( 'user_id', 0, GetterInterface::INT ) ) {
							$title		=	CBTxt::T( 'COMMENTED_ON_YOUR_POST_TITLE', 'commented on your post <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'show', 'id' => $id ) ), '[title]' => $activityTitle ) );
						} else {
							$title		=	CBTxt::T( 'COMMENTED_ON_POST_TITLE', 'commented on post <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'show', 'id' => $id ) ), '[title]' => $activityTitle ) );
						}
						break;
					case 'tag':
						if ( $viewer->get( 'id', 0, GetterInterface::INT ) != $activity->get( 'user_id', 0, GetterInterface::INT ) ) {
							$title		=	CBTxt::T( 'TAGGED_YOU_IN_POST_TITLE', 'tagged you in post <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'show', 'id' => $id ) ), '[title]' => $activityTitle ) );
						} else {
							$title		=	CBTxt::T( 'TAGGED_IN_POST_TITLE', 'tagged in post <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'show', 'id' => $id ) ), '[title]' => $activityTitle ) );
						}
						break;
					case 'like':
						if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $activity->get( 'user_id', 0, GetterInterface::INT ) ) {
							$title		=	CBTxt::T( 'LIKED_YOUR_POST_TITLE', 'liked your post <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'show', 'id' => $id ) ), '[title]' => $activityTitle ) );
						} else {
							$title		=	CBTxt::T( 'LIKED_POST_TITLE', 'liked post <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'show', 'id' => $id ) ), '[title]' => $activityTitle ) );
						}
						break;
					default:
						$title			=	CBTxt::T( 'POSTED_TITLE', 'posted <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'show', 'id' => $id ) ), '[title]' => $activityTitle ) );
						break;
				}
			}
		} else {
			$message					=	null;

			$activity->params()->set( 'overrides.menu', false );

			$insert						=	'<div class="streamItemDivider border-default"></div>'
										.	HTML_cbactivityActivityContainer::showActivityContainer( $activity, $viewer, $stream, $plugin, $output );

			$activity->params()->set( 'overrides.menu', true );

			$row->params()->set( 'overrides.compact', true );
			$row->params()->set( 'overrides.edit', false );
			$row->params()->set( 'overrides.likes', false );
			$row->params()->set( 'overrides.comments', false );
		}
	}

	/**
	 * render frontend comment activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showComment( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		global $_CB_framework;

		$notification				=	( $stream instanceof NotificationsInterface );
		$id							=	(int) $assetMatches[1];
		$type						=	( isset( $assetMatches[2] ) ? $assetMatches[2] : null );

		switch ( $type ) {
			case 'reply':
				if ( $notification ) {
					$title			=	CBTxt::T( 'REPLIED_TO_COMMENT_TITLE', 'replied to comment <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'show', 'id' => $id ) ), '[title]' => '#' . $id ) );
				} else {
					$title			=	CBTxt::T( 'replied to this comment' );
				}
				break;
			case 'tag':
				if ( $notification ) {
					$title			=	CBTxt::T( 'TAGGED_IN_COMMENT_TITLE', 'tagged in comment <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'show', 'id' => $id ) ), '[title]' => '#' . $id ) );
				} else {
					$title			=	CBTxt::T( 'was tagged in this comment' );
				}
				break;
			case 'like':
				if ( $notification ) {
					$title			=	CBTxt::T( 'LIKED_COMMENT_TITLE', 'liked comment <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'show', 'id' => $id ) ), '[title]' => '#' . $id ) );
				} else {
					$title			=	CBTxt::T( 'liked this comment' );
				}
				break;
		}

		static $cache				=	array();

		if ( ! isset( $cache[$id] ) ) {
			$commentRow				=	new CommentTable();

			$commentRow->load( $id );

			$cache[$id]				=	$commentRow;
		}

		/** @var CommentTable $comment */
		$comment					=	$cache[$id];

		if ( ! $comment->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$viewer						=	CBuser::getMyUserDataInstance();

		if ( $notification ) {
			$commentTitle			=	$stream->parser( $comment->get( 'message', null, GetterInterface::STRING ) )->parse( array( 'linebreaks' ), false );

			if ( cbutf8_strlen( $commentTitle ) > 50 ) {
				$commentTitle		=	trim( cbutf8_substr( $commentTitle, 0, 50 ) ) . '...';
			}

			if ( ! $commentTitle ) {
				$commentTitle		=	'#' . $id;
			}

			switch ( $type ) {
				case 'reply':
					if ( $viewer->get( 'id', 0, GetterInterface::INT ) != $comment->get( 'user_id', 0, GetterInterface::INT ) ) {
						$title		=	CBTxt::T( 'REPLIED_TO_YOUR_COMMENT', 'replied to your comment <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'show', 'id' => $id ) ), '[title]' => $commentTitle ) );
					} else {
						$title		=	CBTxt::T( 'REPLIED_TO_COMMENT', 'replied to comment <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'show', 'id' => $id ) ), '[title]' => $commentTitle ) );
					}
					break;
				case 'tag':
					if ( $viewer->get( 'id', 0, GetterInterface::INT ) != $comment->get( 'user_id', 0, GetterInterface::INT ) ) {
						$title		=	CBTxt::T( 'TAGGED_YOU_IN_COMMENT_TITLE', 'tagged you in comment <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'show', 'id' => $id ) ), '[title]' => $commentTitle ) );
					} else {
						$title		=	CBTxt::T( 'TAGGED_IN_COMMENT_TITLE', 'tagged in comment <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'show', 'id' => $id ) ), '[title]' => $commentTitle ) );
					}
					break;
				case 'like':
					if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $comment->get( 'user_id', 0, GetterInterface::INT ) ) {
						$title		=	CBTxt::T( 'LIKED_YOUR_COMMENT_TITLE', 'liked your comment <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'show', 'id' => $id ) ), '[title]' => $commentTitle ) );
					} else {
						$title		=	CBTxt::T( 'LIKED_COMMENT_TITLE', 'liked comment <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'show', 'id' => $id ) ), '[title]' => $commentTitle ) );
					}
					break;
				default:
					$title			=	CBTxt::T( 'COMMENTED_TITLE', 'commented <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'show', 'id' => $id ) ), '[title]' => $commentTitle ) );
					break;
			}
		} else {
			$comment->params()->set( 'overrides.menu', false );

			$comments				=	$row->comments( $stream );

			$inlineCache			=	$comments->get( 'inline', false, GetterInterface::BOOLEAN );
			$repliesCache			=	$comments->get( 'replies', false, GetterInterface::BOOLEAN );

			$comments->set( 'inline', false );
			$comments->set( 'replies', false );

			$insert					=	'<div class="streamItemDivider border-default"></div>'
									.	HTML_cbactivityCommentContainer::showCommentContainer( $comment, $viewer, $comments, $plugin, $output );

			$comments->set( 'inline', $inlineCache );
			$comments->set( 'replies', $repliesCache );

			$comment->params()->set( 'overrides.menu', true );

			$row->params()->set( 'overrides.compact', true );
			$row->params()->set( 'overrides.edit', false );
			$row->params()->set( 'overrides.likes', false );
		}
	}

	/**
	 * render frontend blog activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showBlog( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		global $_CB_framework, $_PLUGINS;

		$notification			=	( $stream instanceof NotificationsInterface );
		$id						=	(int) $assetMatches[1];
		$type					=	( isset( $assetMatches[2] ) ? $assetMatches[2] : null );

		switch ( $type ) {
			case 'comment':
				if ( $notification ) {
					$title		=	CBTxt::T( 'COMMENTED_ON_BLOG_TITLE', 'commented on blog <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( 'cbblogs', true, array( 'action' => 'blogs', 'func' => 'show', 'id' => $id ) ), '[title]' => '#' . $id ) );
				} else {
					$title		=	CBTxt::T( 'commented on this blog' );
				}
				break;
			case 'like':
				if ( $notification ) {
					$title		=	CBTxt::T( 'LIKED_BLOG_TITLE', 'liked blog <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( 'cbblogs', true, array( 'action' => 'blogs', 'func' => 'show', 'id' => $id ) ), '[title]' => '#' . $id ) );
				} else {
					$title		=	CBTxt::T( 'liked this blog' );
				}
				break;
			default:
				if ( $notification ) {
					$title		=	CBTxt::T( 'PUBLISHED_BLOG_TITLE', 'published article <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->pluginClassUrl( 'cbblogs', true, array( 'action' => 'blogs', 'func' => 'show', 'id' => $id ) ), '[title]' => '#' . $id ) );
				} else {
					$title		=	CBTxt::T( 'published a new blog' );
				}
				break;
		}

		if ( ! $_PLUGINS->getLoadedPlugin( 'user', 'cbblogs' ) ) {
			return;
		}

		$model					=	cbblogsClass::getModel();

		if ( ! $model->file ) {
			return;
		}

		static $cache			=	array();

		if ( ! isset( $cache[$id] ) ) {
			$blogRow			=	new cbblogsBlogTable();

			$blogRow->load( $id );

			$cache[$id]			=	$blogRow;
		}

		$blog					=	$cache[$id];

		if ( ! $blog->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		if ( $notification ) {
			$blogTitle			=	$blog->get( 'title', null, GetterInterface::STRING );

			if ( ! $blogTitle ) {
				$blogTitle		=	$blog->get( 'blog_intro', null, GetterInterface::STRING );
			}

			if ( cbutf8_strlen( $blogTitle ) > 50 ) {
				$blogTitle		=	trim( cbutf8_substr( $blogTitle, 0, 50 ) ) . '...';
			}

			if ( $blogTitle ) {
				switch ( $type ) {
					case 'comment':
						$title	=	CBTxt::T( 'COMMENTED_ON_BLOG_TITLE', 'commented on blog <a href="[url]">[title]</a>', array( '[url]' => cbblogsModel::getUrl( $blog, true, 'article' ), '[title]' => $blogTitle ) );
						break;
					case 'like':
						$title	=	CBTxt::T( 'LIKED_BLOG_TITLE', 'liked blog <a href="[url]">[title]</a>', array( '[url]' => cbblogsModel::getUrl( $blog, true, 'article' ), '[title]' => $blogTitle ) );
						break;
					default:
						$title	=	CBTxt::T( 'PUBLISHED_BLOG_TITLE', 'published blog <a href="[url]">[title]</a>', array( '[url]' => cbblogsModel::getUrl( $blog, true, 'article' ), '[title]' => $blogTitle ) );
						break;
				}
			}
		} else {
			$insert				=	'<div class="streamItemInsert">'
								.		'<div class="streamPanelBody panel-body bg-muted cbMoreLess">'
								.			'<div class="streamItemContent cbMoreLessContent">'
								.				'<div><strong><a href="' . cbblogsModel::getUrl( $blog, true, 'article' ) . '">' . $blog->get( 'title', null, GetterInterface::STRING ) . '</a></strong></div>'
								.				( $model->type == 2 ? $blog->get( 'blog_intro', null, GetterInterface::HTML ) : Application::Cms()->prepareHtmlContentPlugins( $blog->get( 'blog_intro', null, GetterInterface::HTML ) ) )
								.			'</div>'
								.			'<div class="cbMoreLessOpen fade-edge hidden">'
								.				'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'Read More...' ) . '</a>'
								.			'</div>'
								.		'</div>'
								.	'</div>';

			if ( $type ) {
				$row->params()->set( 'overrides.tags_asset', 'article.' . $id );
				$row->params()->set( 'overrides.likes_asset', 'article.' . $id );
				$row->params()->set( 'overrides.comments_asset', 'article.' . $id );
			} else {
				$row->params()->set( 'overrides.tags_asset', 'asset' );
				$row->params()->set( 'overrides.likes_asset', 'asset' );
				$row->params()->set( 'overrides.comments_asset', 'asset' );
			}
		}
	}

	/**
	 * render frontend article activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showArticle( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		global $_CB_framework, $_CB_database, $_PLUGINS;

		$notification			=	( $stream instanceof NotificationsInterface );
		$id						=	(int) $assetMatches[1];
		$type					=	( isset( $assetMatches[2] ) ? $assetMatches[2] : null );

		switch ( $type ) {
			case 'comment':
				if ( $notification ) {
					$title		=	CBTxt::T( 'COMMENTED_ON_ARTICLE_TITLE', 'commented on article <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->getCfg( 'live_site' ) . '/index.php?option=com_content&view=article&mesid=' . $id, '[title]' => '#' . $id ) );
				} else {
					$title		=	CBTxt::T( 'commented on this article' );
				}
				break;
			case 'like':
				if ( $notification ) {
					$title		=	CBTxt::T( 'LIKED_ARTICLE_TITLE', 'liked article <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->getCfg( 'live_site' ) . '/index.php?option=com_content&view=article&mesid=' . $id, '[title]' => '#' . $id ) );
				} else {
					$title		=	CBTxt::T( 'liked this article' );
				}
				break;
			default:
				if ( $notification ) {
					$title		=	CBTxt::T( 'PUBLISHED_ARTICLE_TITLE', 'published article <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->getCfg( 'live_site' ) . '/index.php?option=com_content&view=article&mesid=' . $id, '[title]' => '#' . $id ) );
				} else {
					$title		=	CBTxt::T( 'published a new article' );
				}
				break;
		}

		if ( ! $_PLUGINS->getLoadedPlugin( 'user', 'cbarticles' ) ) {
			return;
		}

		$model					=	cbarticlesClass::getModel();

		if ( ! $model->file ) {
			return;
		}

		static $cache			=	array();

		if ( ! isset( $cache[$id] ) ) {
			if ( $model->type == 5 ) {
				$query			=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__k2_items' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " = " . (int) $id;
				$_CB_database->setQuery( $query, 0, 1 );
				$details		=	$_CB_database->loadAssoc();

				$articleRow		=	new Table( null, '#__k2_items', 'id' );
			} else {
				$query			=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__content' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " = " . (int) $id;
				$_CB_database->setQuery( $query, 0, 1 );
				$details		=	$_CB_database->loadAssoc();

				$articleRow		=	new Table( null, '#__content', 'id' );
			}

			foreach ( $details as $k => $v ) {
				$articleRow->set( $k, $v );
			}

			$cache[$id]			=	$articleRow;
		}

		$article				=	$cache[$id];

		if ( ! $article->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		if ( $notification ) {
			$articleTitle		=	$article->get( 'title', null, GetterInterface::STRING );

			if ( ! $articleTitle ) {
				$articleTitle	=	$article->get( 'introtext', null, GetterInterface::STRING );
			}

			if ( cbutf8_strlen( $articleTitle ) > 50 ) {
				$articleTitle	=	trim( cbutf8_substr( $articleTitle, 0, 50 ) ) . '...';
			}

			if ( $articleTitle ) {
				switch ( $type ) {
					case 'comment':
						$title	=	CBTxt::T( 'COMMENTED_ON_ARTICLE_TITLE', 'commented on article <a href="[url]">[title]</a>', array( '[url]' => cbarticlesModel::getUrl( $article, true, 'article' ), '[title]' => $articleTitle ) );
						break;
					case 'like':
						$title	=	CBTxt::T( 'LIKED_ARTICLE_TITLE', 'liked article <a href="[url]">[title]</a>', array( '[url]' => cbarticlesModel::getUrl( $article, true, 'article' ), '[title]' => $articleTitle ) );
						break;
					default:
						$title	=	CBTxt::T( 'PUBLISHED_ARTICLE_TITLE', 'published article <a href="[url]">[title]</a>', array( '[url]' => cbarticlesModel::getUrl( $article, true, 'article' ), '[title]' => $articleTitle ) );
						break;
				}
			}
		} else {
			$insert				=	'<div class="streamItemInsert">'
								.		'<div class="streamPanelBody panel-body bg-muted cbMoreLess">'
								.			'<div class="streamItemContent cbMoreLessContent">'
								.				'<div><strong><a href="' . cbarticlesModel::getUrl( $article, true, 'article' ) . '">' . $article->get( 'title', null, GetterInterface::STRING ) . '</a></strong></div>'
								.				Application::Cms()->prepareHtmlContentPlugins( $article->get( 'introtext', null, GetterInterface::HTML ) )
								.			'</div>'
								.			'<div class="cbMoreLessOpen fade-edge hidden">'
								.				'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'Read More...' ) . '</a>'
								.			'</div>'
								.		'</div>'
								.	'</div>';

			if ( $type ) {
				$row->params()->set( 'overrides.tags_asset', 'article.' . $id );
				$row->params()->set( 'overrides.likes_asset', 'article.' . $id );
				$row->params()->set( 'overrides.comments_asset', 'article.' . $id );
			} else {
				$row->params()->set( 'overrides.tags_asset', 'asset' );
				$row->params()->set( 'overrides.likes_asset', 'asset' );
				$row->params()->set( 'overrides.comments_asset', 'asset' );
			}
		}
	}

	/**
	 * render frontend blog activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param string                          $title
	 * @param string                          $date
	 * @param string                          $message
	 * @param string                          $insert
	 * @param string                          $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static private function showTopic( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $plugin, $output )
	{
		global $_CB_framework, $_PLUGINS;

		$notification			=	( $stream instanceof NotificationsInterface );
		$id						=	(int) $assetMatches[1];

		if ( $assetMatches[2] == 'reply' ) {
			if ( $notification ) {
				$title			=	CBTxt::T( 'REPLIED_TO_DISCUSSION_TITLE', 'replied to discussion <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->getCfg( 'live_site' ) . '/index.php?option=com_kunena&view=topic&mesid=' . $id, '[title]' => '#' . $id ) );
			} else {
				$title			=	CBTxt::T( 'replied to a discussion' );
			}
		} else {
			if ( $notification ) {
				$title			=	CBTxt::T( 'STARTED_DISCUSSION_TITLE', 'started discussion <a href="[url]">[title]</a>', array( '[url]' => $_CB_framework->getCfg( 'live_site' ) . '/index.php?option=com_kunena&view=topic&mesid=' . $id, '[title]' => '#' . $id ) );
			} else {
				$title			=	CBTxt::T( 'started a new discussion' );
			}
		}

		if ( ! $_PLUGINS->getLoadedPlugin( 'user', 'cbforums' ) ) {
			return;
		}

		$model					=	cbforumsClass::getModel();

		if ( ! $model->file ) {
			return;
		}

		if ( ! class_exists( 'KunenaForumMessageHelper' ) ) {
			return;
		}

		static $cache			=	array();

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]			=	KunenaForumMessageHelper::get( $id );
		}

		$post					=	$cache[$id];

		if ( ! $post->id ) {
			return;
		}

		if ( $notification ) {
			$postTitle			=	cbforumsClass::cleanPost( $post->subject );

			if ( ! $postTitle ) {
				$postTitle		=	cbforumsClass::cleanPost( $post->message );
			}

			if ( cbutf8_strlen( $postTitle ) > 50 ) {
				$postTitle		=	trim( cbutf8_substr( $postTitle, 0, 50 ) ) . '...';
			}

			if ( $postTitle ) {
				if ( $assetMatches[2] == 'reply' ) {
					$title		=	CBTxt::T( 'REPLIED_TO_DISCUSSION_TITLE', 'replied to discussion <a href="[url]">[title]</a>', array( '[url]' => cbforumsModel::getForumURL( null, $post->id ), '[title]' => $postTitle ) );
				} else {
					$title		=	CBTxt::T( 'STARTED_DISCUSSION_TITLE', 'started discussion <a href="[url]">[title]</a>', array( '[url]' => cbforumsModel::getForumURL( null, $post->id ), '[title]' => $postTitle ) );
				}
			}
		} else {
			$insert				=	'<div class="streamItemInsert">'
								.		'<div class="streamPanelBody panel-body bg-muted cbMoreLess">'
								.			'<div class="streamItemContent cbMoreLessContent">'
								.				'<div><strong><a href="' . cbforumsModel::getForumURL( null, $post->id ) . '">' . cbforumsClass::cleanPost( $post->subject ) . '</a></strong></div>'
								.				cbforumsClass::cleanPost( $post->message )
								.			'</div>'
								.			'<div class="cbMoreLessOpen fade-edge hidden">'
								.				'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'Read More...' ) . '</a>'
								.			'</div>'
								.		'</div>'
								.	'</div>';
		}
	}
}