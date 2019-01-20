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
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Input\Get;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\Activity\ActivityInterface;
use CB\Plugin\Activity\NotificationsInterface;
use CB\Plugin\Activity\CommentsInterface;
use CB\Plugin\Activity\TagsInterface;
use CB\Plugin\Activity\FollowingInterface;
use CB\Plugin\Activity\LikesInterface;
use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Table\FollowTable;
use CB\Plugin\Activity\Table\LikeTable;
use CB\Plugin\Activity\Table\HiddenTable;
use CB\Plugin\Activity\Table\ReadTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\Comments;
use CB\Plugin\Activity\Tags;
use CB\Plugin\Activity\Following;
use CB\Plugin\Activity\Likes;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

class CBplug_cbactivity extends cbPluginHandler
{

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		if ( $this->input( 'action', null, GetterInterface::STRING ) == 'cleanup' ) {
			$this->cleanUp();
		} else {
			$this->getStream();
		}
	}

	/**
	 * Loads in a stream directly or by URL
	 *
	 * @param null|Activity|Comments|Tags $stream
	 * @param null|string                 $view
	 */
	public function getStream( $stream = null, $view = null )
	{
		global $_CB_framework, $_PLUGINS;

		$viewer								=	CBuser::getMyUserDataInstance();
		$raw								=	false;
		$menu								=	null;
		$inline								=	false;
		$access								=	true;

		$streamLoaded						=	false;

		if ( $stream ) {
			if ( $stream instanceof ActivityInterface ) {
				$action						=	'activity';
			} elseif ( $stream instanceof NotificationsInterface ) {
				$action						=	'notifications';
			} elseif ( $stream instanceof CommentsInterface ) {
				$action						=	'comments';
			} elseif ( $stream instanceof TagsInterface ) {
				$action						=	'tags';
			} elseif ( $stream instanceof FollowingInterface ) {
				$action						=	'following';
			} elseif ( $stream instanceof LikesInterface ) {
				$action						=	'likes';
			} else {
				return;
			}

			$function						=	( $view ? $view : 'show' );

			if ( strpos( $function, '.' ) !== false ) {
				list( $action, $function )	=	explode( '.', $function, 2 );
			}

			$id								=	0;
			$inline							=	true;

			$streamLoaded					=	true;
		} else {
			$raw							=	( $this->input( 'format', null, GetterInterface::STRING ) == 'raw' );
			$action							=	$this->input( 'action', null, GetterInterface::STRING );

			if ( strpos( $action, '.' ) !== false ) {
				list( $action, $function )	=	explode( '.', $action, 2 );
			} else {
				$function					=	$this->input( 'func', null, GetterInterface::STRING );
			}

			$id								=	$this->input( 'id', 0, GetterInterface::INT );
			$streamId						=	$this->input( 'stream', null, GetterInterface::STRING );
			$streamAsset					=	null;

			switch ( $action ) {
				case 'recentactivity':
					$action					=	'activity';
					$function				=	'show';
					$streamAsset			=	'all';
					break;
				case 'myactivity':
					$action					=	'activity';
					$function				=	'show';
					$streamAsset			=	'user.connections,following,global';
					break;
				case 'hiddenactivity':
					$action					=	'activity';
					$function				=	'hidden';
					$streamAsset			=	'all';
					break;
				case 'hiddencomments':
					$action					=	'comments';
					$function				=	'hidden';
					$streamAsset			=	'all';
					break;
				case 'hiddennotifications':
					$action					=	'notifications';
					$function				=	'hidden';
					$streamAsset			=	'all';
					break;
			}

			if ( ! $streamId ) {
				if ( $id ) {
					$streamAsset			=	CBActivity::getAsset( $action, $id );
				}

				$menu						=	JFactory::getApplication()->getMenu()->getActive();

				if ( $menu && isset( $menu->id ) && ( ! $streamAsset ) ) {
					$streamAsset			=	str_replace( '[page_id]', $menu->id, $menu->params->get( $action . '_asset' ) );
				}
			}

			switch ( $action ) {
				case 'likes':
					$stream					=	new Likes( $streamAsset, $viewer );
					break;
				case 'following':
					$stream					=	new Following( $streamAsset, $viewer );
					break;
				case 'tags':
					$stream					=	new Tags( $streamAsset, $viewer );
					break;
				case 'comments':
					$stream					=	new Comments( $streamAsset, $viewer );
					break;
				case 'notifications':
					$stream					=	new Notifications( $streamAsset, $viewer );
					break;
				case 'activity':
				default:
					$stream					=	new Activity( $streamAsset, $viewer );
					break;
			}

			if ( $menu && isset( $menu->id ) ) {
				$streamLoaded				=	true;

				$stream->parse( $menu->params->toArray(), $action . '_' );

				$stream->set( 'menu', (int) $menu->id );

				if ( $action == 'notifications' ) {
					switch ( $menu->params->get( 'notifications_state' ) ) {
						case 'read':
							$stream->set( 'read', 'readonly' );
							break;
						case 'unread':
							$stream->set( 'read', 'unreadonly' );
							break;
					}
				}
			}

			if ( $streamId ) {
				if ( $stream->load( $streamId ) ) {
					$streamLoaded			=	true;
				} elseif ( $id && in_array( $action, array( 'activity', 'comments' ) ) ) {
					$streamAsset			=	CBActivity::getAsset( $action, $id );

					if ( $streamAsset ) {
						$stream->assets( $streamAsset );
					} else {
						$access				=	false;
					}
				} else {
					$access					=	false;
				}
			} elseif ( $function && ( ! in_array( $function, array( 'show', 'hidden' ) ) ) ) {
				$access						=	false;
			}
		}

		if ( ! $stream->asset() ) {
			$access							=	false;
		} elseif ( $stream instanceof NotificationsInterface ) {
			if ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( $viewer->get( 'id', 0, GetterInterface::INT ) != $stream->user()->get( 'id', 0, GetterInterface::INT ) ) ) {
				$access						=	false;
			}
		} elseif ( preg_match( '/^profile(?:\.(\d+)(?:\.field\.(\d+))?)?/', $stream->asset(), $matches ) || $stream->get( 'tab', 0, GetterInterface::INT ) || $stream->get( 'field', 0, GetterInterface::INT ) ) {
			$profileId						=	( isset( $matches[1] ) ? (int) $matches[1] : $stream->user()->get( 'id', 0, GetterInterface::INT ) );
			$fieldId						=	( isset( $matches[2] ) ? (int) $matches[2] : $stream->get( 'field', 0, GetterInterface::INT ) );
			$tabId							=	$stream->get( 'tab', 0, GetterInterface::INT );

			if ( $profileId != $stream->user()->get( 'id', 0, GetterInterface::INT ) ) {
				$stream->user( $profileId );
			}

			if ( $fieldId ) {
				$field						=	CBActivity::getField( $fieldId, $profileId );

				if ( ! $field ) {
					$access					=	false;
				} elseif ( ! $streamLoaded ) {
					$streamLoaded			=	true;

					$stream->parse( $field->params, 'stream_' );

					$stream->set( 'field', $field->get( 'fieldid', 0, GetterInterface::INT ) );
				}
			} else {
				$tab						=	CBActivity::getTab( $tabId, $profileId );

				if ( ! $tab ) {
					if ( ! in_array( 'all', $stream->assets() ) ) {
						$access				=	false;
					}
				} elseif ( ! $streamLoaded ) {
					$streamLoaded			=	true;

					$stream->parse( $tab->params, 'stream_' );

					$stream->set( 'tab', $tab->get( 'tabid', 0, GetterInterface::INT ) );
				}
			}
		}

		$_PLUGINS->trigger( 'activity_onStreamAccess', array( &$stream, &$access, $streamLoaded ) );

		if ( ! $access ) {
			if ( $inline ) {
				return;
			} elseif ( $raw ) {
				header( 'HTTP/1.0 401 Unauthorized' );
				exit();
			} else {
				cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
			}
		}

		if ( ! $stream->id() ) {
			$stream->cache();
		}

		if ( ! $raw ) {
			outputCbJs();
			outputCbTemplate();

			ob_start();
		}

		switch ( $action ) {
			case 'activity':
				switch ( $function ) {
					case 'edit':
						$this->showActivityEdit( $id, $viewer, $stream );
						break;
					case 'save':
						$this->saveActivity( $id, $viewer, $stream );
						break;
					case 'delete':
						$this->deleteActivity( $id, $viewer, $stream );
						break;
					case 'button':
						$this->showActivityButton( $viewer, $stream, ( $raw ? 'refresh' : null ) );
						break;
					case 'hidden':
						if ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$stream->user( $viewer );
						$stream->assets( 'all' );

						$stream->set( 'hidden', 'hidden' );
						$stream->set( 'create', false );
						$stream->set( 'auto_update', false );
						$stream->set( 'pinned', false );
						$stream->set( 'likes', false );
						$stream->set( 'comments', false );

						$stream->cache();

						$this->showActivity( $id, $viewer, $stream );
						break;
					case 'hide':
						$this->hideActivity( $id, $viewer, $stream );
						break;
					case 'unhide':
						$this->unhideActivity( $id, $viewer, $stream );
						break;
					case 'unfollow':
						$this->unfollowActivity( $id, $viewer, $stream );
						break;
					case 'report':
						$this->reportActivity( $id, $viewer, $stream );
						break;
					case 'pin':
						$this->pinActivity( $id, $viewer, $stream );
						break;
					case 'unpin':
						$this->unpinActivity( $id, $viewer, $stream );
						break;
					case 'load':
					case 'modal':
					case 'toggle':
						$this->showActivity( null, $viewer, $stream, $function );
						break;
					case 'update':
						$this->showActivity( $id, $viewer, $stream, 'update' );
						break;
					case 'show':
					default:
						$this->showActivity( $id, $viewer, $stream );
						break;
				}
				break;
			case 'notifications':
				switch ( $function ) {
					case 'delete':
						$this->deleteNotification( $id, $viewer, $stream );
						break;
					case 'button':
						$this->showNotificationsButton( $viewer, $stream, ( $raw ? 'refresh' : null ) );
						break;
					case 'hidden':
						if ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$stream->user( $viewer );
						$stream->assets( 'all' );

						$stream->set( 'hidden', 'hidden' );
						$stream->set( 'read', null );
						$stream->set( 'auto_update', false );
						$stream->set( 'pinned', false );

						$stream->cache();

						$this->showNotifications( $id, $viewer, $stream );
						break;
					case 'hide':
						$this->hideNotification( $id, $viewer, $stream );
						break;
					case 'unhide':
						$this->unhideNotification( $id, $viewer, $stream );
						break;
					case 'load':
					case 'modal':
					case 'toggle':
						$this->showNotifications( null, $viewer, $stream, $function );
						break;
					case 'update':
						$this->showNotifications( $id, $viewer, $stream, 'update' );
						break;
					case 'show':
					default:
						$this->showNotifications( $id, $viewer, $stream );
						break;
				}
				break;
			case 'comments':
				switch ( $function ) {
					case 'edit':
						$this->showCommentEdit( $id, $viewer, $stream );
						break;
					case 'save':
						$this->saveComment( $id, $viewer, $stream );
						break;
					case 'delete':
						$this->deleteComment( $id, $viewer, $stream );
						break;
					case 'button':
						$this->showCommentsButton( $viewer, $stream, ( $raw ? 'refresh' : null ) );
						break;
					case 'hidden':
						if ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$stream->user( $viewer );
						$stream->assets( 'all' );

						$stream->set( 'hidden', 'hidden' );
						$stream->set( 'create', false );
						$stream->set( 'auto_update', false );
						$stream->set( 'pinned', false );
						$stream->set( 'likes', false );
						$stream->set( 'replies', false );

						$stream->cache();

						$this->showComments( $id, $viewer, $stream );
						break;
					case 'hide':
						$this->hideComment( $id, $viewer, $stream );
						break;
					case 'unhide':
						$this->unhideComment( $id, $viewer, $stream );
						break;
					case 'report':
						$this->reportComment( $id, $viewer, $stream );
						break;
					case 'pin':
						$this->pinComment( $id, $viewer, $stream );
						break;
					case 'unpin':
						$this->unpinComment( $id, $viewer, $stream );
						break;
					case 'load':
					case 'modal':
					case 'toggle':
						$this->showComments( null, $viewer, $stream, $function );
						break;
					case 'update':
						$this->showComments( $id, $viewer, $stream, 'update' );
						break;
					case 'show':
					default:
						$this->showComments( $id, $viewer, $stream );
						break;
				}
				break;
			case 'tags':
				switch ( $function ) {
					case 'edit':
						$this->showTagsEdit( $viewer, $stream );
						break;
					case 'save':
						$this->saveTags( $viewer, $stream );
						break;
					case 'tagged':
						$this->showTagged( $viewer, $stream );
						break;
					case 'load':
					case 'modal':
						$this->showTags( $viewer, $stream, $function );
						break;
					case 'show':
					default:
						$this->showTags( $viewer, $stream );
						break;
				}
				break;
			case 'following':
				switch ( $function ) {
					case 'follow':
						$this->followAsset( $viewer, $stream );
						break;
					case 'unfollow':
						$this->unfollowAsset( $viewer, $stream );
						break;
					case 'button':
						$this->showFollowButton( $viewer, $stream );
						break;
					case 'load':
					case 'modal':
						$this->showFollowing( $viewer, $stream, $function );
						break;
					case 'show':
					default:
						$this->showFollowing( $viewer, $stream );
						break;
				}
				break;
			case 'likes':
				switch ( $function ) {
					case 'like':
						$this->likeAsset( $viewer, $stream );
						break;
					case 'unlike':
						$this->unlikeAsset( $viewer, $stream );
						break;
					case 'button':
						$this->showLikeButton( $viewer, $stream );
						break;
					case 'load':
					case 'modal':
						$this->showLikes( $viewer, $stream, $function );
						break;
					case 'show':
					default:
						$this->showLikes( $viewer, $stream );
						break;
				}
				break;
		}

		if ( ! $raw ) {
			$html							=	ob_get_contents();
			ob_end_clean();

			if ( ! $html ) {
				return;
			}

			if ( $stream->get( 'inline', false, GetterInterface::BOOLEAN ) ) {
				echo $html;
			} else {
				$class						=	$this->params->get( 'general_class', null, GetterInterface::STRING );

				$return						=	'<' . ( in_array( $function, array( 'button', 'tagged' ) ) ? 'span' : 'div' ) . ' class="cbActivity' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
											.		$html
											.	'</' . ( in_array( $function, array( 'button', 'tagged' ) ) ? 'span' : 'div' ) . '>';

				echo $return;

				if ( $menu && isset( $menu->id ) ) {
					$_CB_framework->setMenuMeta();
				}
			}
		}
	}

	/**
	 * Prunes old activity entries
	 */
	private function cleanUp()
	{
		global $_CB_database, $_CB_framework;

		if ( $this->input( 'token', null, GetterInterface::STRING ) != md5( $_CB_framework->getCfg( 'secret' ) ) ) {
			header( 'HTTP/1.0 401 Unauthorized' );
			exit();
		}

		$durationActivity		=	$this->params->get( 'cleanup_activity', '-1 YEAR', GetterInterface::STRING );

		if ( $durationActivity != 'forever' ) {
			$query				=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'date' ) . " <= " . $_CB_database->Quote( Application::Date( 'now', 'UTC' )->modify( $durationActivity )->format( 'Y-m-d H:i:s' ) );
			$_CB_database->setQuery( $query );
			$activities			=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\ActivityTable', array( $_CB_database ) );

			/** @var ActivityTable[] $activities */
			foreach ( $activities as $activity ) {
				$activity->delete();
			}
		}

		$durationComments		=	$this->params->get( 'cleanup_comments', '-2 YEARS', GetterInterface::STRING );

		if ( $durationComments != 'forever' ) {
			$query				=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'date' ) . " <= " . $_CB_database->Quote( Application::Date( 'now', 'UTC' )->modify( $durationComments )->format( 'Y-m-d H:i:s' ) );
			$_CB_database->setQuery( $query );
			$comments			=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\CommentTable', array( $_CB_database ) );

			/** @var CommentTable[] $comments */
			foreach ( $comments as $comment ) {
				$comment->delete();
			}
		}

		header( 'HTTP/1.0 200 OK' );
		exit();
	}

	/**
	 * Displays activity stream
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Activity  $stream
	 * @param string    $output
	 */
	private function showActivity( $id, $viewer, $stream, $output = null )
	{
		global $_CB_framework;

		CBActivity::getTemplate( array( 'activity', 'activity_new', 'activity_container', 'comment_container', 'activity_core', 'stream_attachments', 'twemoji' ) );

		$activityPrefix			=	'activity_' . $stream->id() . '_';

		$stream->set( 'published', 1 );

		if ( $id ) {
			if ( $output == 'update' ) {
				$stream->set( 'date', array( Application::Database()->getUtcDateTime( $id ), '>' ) );
			} else {
				$stream->set( 'id', $id );
			}
		} elseif ( $output == 'update' ) {
			return;
		}

		$rowsHashtag			=	$this->input( 'hashtag', null, GetterInterface::STRING );

		if ( $rowsHashtag && preg_match( '/^(\w+)$/i', $rowsHashtag ) ) {
			$rowsSearch			=	'#' . $rowsHashtag;
		} else {
			$rowsHashtag		=	null;
			$rowsSearch			=	$this->input( $activityPrefix . 'search', null, GetterInterface::STRING );
		}

		$searching				=	false;

		if ( $rowsSearch != '' ) {
			$searching			=	true;

			$stream->set( 'search', $rowsSearch );
		}

		$rowsLimitstart			=	$this->input( $activityPrefix . 'limitstart', 0, GetterInterface::INT );

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$rowsTotal			=	$stream->rows( 'count' );
		} else {
			$rowsTotal			=	0;
		}

		if ( $rowsTotal <= $rowsLimitstart ) {
			$rowsLimitstart		=	0;
		}

		$pageNav				=	new cbPageNav( $rowsTotal, $rowsLimitstart, $stream->get( 'paging_limit', 15, GetterInterface::INT ) );

		$pageNav->setInputNamePrefix( $activityPrefix );

		if ( $rowsSearch != '' ) {
			if ( $rowsHashtag ) {
				$pageVars		=	array( 'action' => 'activity', 'func' => 'load', 'stream' => $stream->id(), 'hashtag' => $rowsHashtag );
			} else {
				$pageVars		=	array( 'action' => 'activity', 'func' => 'load', 'stream' => $stream->id(), $activityPrefix . 'search' => $rowsSearch );
			}
		} else {
			$pageVars			=	array( 'action' => 'activity', 'func' => 'load', 'stream' => $stream->id() );
		}

		$pageNav->setBaseURL( $_CB_framework->pluginClassUrl( $this->element, false, $pageVars, 'raw', 0, true ) );

		$stream->set( 'paging_limitstart', $pageNav->limitstart );

		$rows					=	array();

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) && $rowsTotal ) {
			$rows				=	$stream->rows();

			if ( $stream->get( 'direction', 'down', GetterInterface::STRING ) == 'up' ) {
				$rows			=	array_reverse( $rows, true );
			}
		}

		$pageNav->limitstart	=	$stream->get( 'paging_limitstart', 0, GetterInterface::INT );

		HTML_cbactivityActivity::showActivity( $rows, $pageNav, $searching, $viewer, $stream, $this, $output );
	}

	/**
	 * Displays activity edit
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Activity  $stream
	 */
	private function showActivityEdit( $id, $viewer, $stream )
	{
		CBActivity::getTemplate( array( 'activity_edit', 'stream_attachments' ), false, false );

		$row	=	$stream->row( $id );

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBActivity::canModerate( $stream ) ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to edit this activity.' ), 'error' );
			}
		} else {
			CBActivity::ajaxResponse( CBTxt::T( 'No activity to edit.' ), 'error' );
		}

		CBActivity::ajaxResponse( HTML_cbactivityActivityEdit::showActivityEdit( $row, $viewer, $stream, $this ) );
	}

	/**
	 * Saves activity
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Activity  $stream
	 */
	private function saveActivity( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		$canModerate						=	CBActivity::canModerate( $stream );

		CBActivity::getTemplate( array( 'activity_container', 'comment_container', 'activity_core', 'stream_attachments' ), false, false );

		$row								=	$stream->row( $id );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ! CBActivity::canCreate( 'activity', $stream, $viewer ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to create activity.' ), 'error' );
			}
		} elseif ( ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! $canModerate ) ) || ( ! CBActivity::findParamOverrde( $row, 'edit', true ) ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to edit this activity.' ), 'error' );
		}

		$messageLimit						=	( $canModerate ? 0 : $stream->get( 'message_limit', 400, GetterInterface::INT ) );
		$showActions						=	CBActivity::findParamOverrde( $row, 'actions', true, $stream );
		$actionLimit						=	( $canModerate ? 0 : $stream->get( 'actions_message_limit', 100, GetterInterface::INT ) );
		$showLocations						=	CBActivity::findParamOverrde( $row, 'locations', true, $stream );
		$locationLimit						=	( $canModerate ? 0 : $stream->get( 'locations_address_limit', 200, GetterInterface::INT ) );
		$showLinks							=	CBActivity::findParamOverrde( $row, 'links', true, $stream );
		$linkLimit							=	( $canModerate ? 0 : $stream->get( 'links_link_limit', 5, GetterInterface::INT ) );
		$showTags							=	CBActivity::findParamOverrde( $row, 'tags', true, $stream );

		$row->set( 'user_id', $row->get( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT ) );
		$row->set( 'asset', $row->get( 'asset', $stream->asset(), GetterInterface::STRING ) );

		if ( Application::MyUser()->isGlobalModerator() && $this->input( 'global', false, GetterInterface::BOOLEAN ) && ( in_array( 'all', $stream->assets() ) || in_array( 'global', $stream->assets() ) ) ) {
			$row->set( 'asset', 'global' );
		} elseif ( $row->get( 'asset', null, GetterInterface::STRING ) == 'global' ) {
			$row->set( 'asset', $stream->asset() );
		}

		$message							=	trim( $this->input( 'message', $row->get( 'message', null, GetterInterface::STRING ), GetterInterface::STRING ) );

		// Remove duplicate spaces:
		$message							=	preg_replace( '/ {2,}/i', ' ', $message );
		// Remove duplicate tabs:
		$message							=	preg_replace( '/\t{2,}/i', "\t", $message );
		// Remove duplicate linebreaks:
		$message							=	preg_replace( '/((?:\r\n|\r|\n){2})(?:\r\n|\r|\n)*/i', '$1', $message );

		if ( $messageLimit && ( cbutf8_strlen( $message ) > $messageLimit ) ) {
			$message						=	cbutf8_substr( $message, 0, $messageLimit );
		}

		$row->set( 'message', $message );

		$new								=	( $row->get( 'id', 0, GetterInterface::INT ) ? false : true );

		if ( $showActions ) {
			$existingAction					=	$row->params()->subTree( 'action' );
			$action							=	$this->getInput()->subTree( 'actions' );
			$actionId						=	$action->get( 'id', $existingAction->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT );

			if ( ! in_array( $actionId, array_keys( CBActivity::loadActionOptions( true, $stream, $existingAction->get( 'id', 0, GetterInterface::INT ) ) ) ) ) {
				$actionId					=	0;
			}

			$actionMessage					=	( $actionId ? trim( $action->get( 'message', $existingAction->get( 'message', '', GetterInterface::STRING ), GetterInterface::STRING ) ) : '' );
			$actionEmote					=	( $actionId ? $action->get( 'emote', $existingAction->get( 'emote', 0, GetterInterface::INT ), GetterInterface::INT ) : 0 );

			if ( ! in_array( $actionEmote, array_keys( CBActivity::loadEmoteOptions( false, true ) ) ) ) {
				$actionEmote				=	0;
			}

			// Remove linebreaks:
			$actionMessage					=	str_replace( array( "\n", "\r\n" ), ' ', $actionMessage );
			// Remove duplicate spaces:
			$actionMessage					=	preg_replace( '/ {2,}/i', ' ', $actionMessage );
			// Remove duplicate tabs:
			$actionMessage					=	preg_replace( '/\t{2,}/i', "\t", $actionMessage );

			if ( $actionLimit && ( cbutf8_strlen( $actionMessage ) > $actionLimit ) ) {
				$actionMessage				=	cbutf8_substr( $actionMessage, 0, $actionLimit );
			}

			$actionId						=	( $actionMessage ? $actionId : 0 );

			$newAction						=	array(	'id'		=>	$actionId,
														'message'	=>	( $actionId ? $actionMessage : '' ),
														'emote'		=>	( $actionId ? $actionEmote : 0 )
													);

			if ( $actionId ) {
				$row->params()->set( 'overrides.message', false );
			}

			$row->params()->set( 'action', $newAction );
		}

		if ( $showLocations ) {
			$existingLocation				=	$row->params()->subTree( 'location' );
			$location						=	$this->getInput()->subTree( 'location' );
			$locationId						=	$location->get( 'id', $existingLocation->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT );

			if ( ! in_array( $locationId, array_keys( CBActivity::loadLocationOptions( true, $stream, $existingLocation->get( 'id', 0, GetterInterface::INT ) ) ) ) ) {
				$locationId					=	0;
			}

			$locationPlace					=	( $locationId ? trim( $location->get( 'place', $existingLocation->get( 'place', '', GetterInterface::STRING ), GetterInterface::STRING ) ) : '' );
			$locationAddress				=	( $locationId ? trim( $location->get( 'address', $existingLocation->get( 'place', '', GetterInterface::STRING ), GetterInterface::STRING ) ) : '' );

			if ( $locationLimit && ( cbutf8_strlen( $locationPlace ) > $locationLimit ) ) {
				$locationPlace				=	cbutf8_substr( $locationPlace, 0, $locationLimit );
			}

			if ( $locationLimit && ( cbutf8_strlen( $locationAddress ) > $locationLimit ) ) {
				$locationAddress			=	cbutf8_substr( $locationAddress, 0, $locationLimit );
			}

			$locationId						=	( $locationPlace ? $locationId : 0 );

			$newLocation					=	array(	'id'		=>	$locationId,
														'place'		=>	( $locationId ? $locationPlace : '' ),
														'address'	=>	( $locationId ? $locationAddress : '' )
													);

			if ( $locationId ) {
				$row->params()->set( 'overrides.message', false );
			}

			$row->params()->set( 'location', $newLocation );
		}

		if ( $showLinks ) {
			$newUrls						=	array();
			$newLinks						=	array();
			$urls							=	$stream->parser( $message )->urls();

			/** @var ParamsInterface $links */
			$links							=	$this->getInput()->subTree( 'links' );

			if ( $stream->get( 'links_embedded', false, GetterInterface::BOOLEAN ) ) {
				$index						=	( $links->count() - 1 );

				foreach ( $urls as $url ) {
					foreach ( $links as $link ) {
						/** @var ParamsInterface $link */
						if ( trim( $link->get( 'url', '', GetterInterface::STRING ) ) == $url ) {
							continue 2;
						}
					}

					$index++;

					$links->set( $index, array( 'url' => $url, 'embedded' => true ) );
				}
			}

			foreach ( $links as $i => $link ) {
				if ( $linkLimit && ( ( $i + 1 ) > $linkLimit ) ) {
					break;
				}

				$linkUrl					=	trim( $link->get( 'url', '', GetterInterface::STRING ) );

				if ( ( ! $linkUrl ) || in_array( $linkUrl, $newUrls ) ) {
					continue;
				}

				$linkEmbedded				=	$link->get( 'embedded', false, GetterInterface::BOOLEAN );

				if ( $linkEmbedded && ( ! in_array( $linkUrl, $urls ) ) ) {
					continue;
				}

				if ( $link->get( 'parsed', false, GetterInterface::BOOLEAN ) ) {
					foreach ( $row->params()->subTree( 'links' ) as $existingLink ) {
						/** @var ParamsInterface $existingLink */
						if ( trim( $existingLink->get( 'url', '', GetterInterface::STRING ) ) == $linkUrl ) {
							$existingLink->set( 'title', trim( $link->get( 'title', $existingLink->get( 'title', '', GetterInterface::STRING ), GetterInterface::STRING ) ) );
							$existingLink->set( 'description', trim( $link->get( 'description', $existingLink->get( 'description', '', GetterInterface::STRING ), GetterInterface::STRING ) ) );
							$existingLink->set( 'thumbnail', $link->get( 'thumbnail', true, GetterInterface::BOOLEAN ) );

							if ( $existingLink->get( 'type', null, GetterInterface::STRING ) == 'url' ) {
								$selected	=	$link->get( 'selected', 0, GetterInterface::INT );

								/** @var ParamsInterface $thumbnail */
								$thumbnail	=	$existingLink->subTree( 'thumbnails' )->subTree( $selected );

								if ( $thumbnail->get( 'url', '', GetterInterface::STRING ) ) {
									$existingLink->set( 'media', $thumbnail->asArray() );
									$existingLink->set( 'selected', $selected );
								}
							}

							$newLinks[]		=	$existingLink->asArray();
							break;
						}
					}

					continue;
				}

				$attachment					=	$stream->parser()->attachment( $linkUrl );

				if ( ! $attachment ) {
					continue;
				}

				$newLink					=	$this->attachmentToLink( $link, $attachment );

				if ( ! $newLink ) {
					continue;
				}

				$newLinks[]					=	$newLink;
				$newUrls[]					=	$linkUrl;
			}

			if ( ! $new ) {
				foreach ( $row->params()->subTree( 'links' ) as $link ) {
					/** @var ParamsInterface $link */
					if ( $link->get( 'type', 'url', GetterInterface::STRING ) != 'custom' ) {
						continue;
					}

					$newLinks[]				=	$link->asArray();
				}
			}

			if ( $newLinks ) {
				$row->params()->set( 'overrides.message', false );
			}

			$row->params()->set( 'links', $newLinks );
		}

		$old								=	new ActivityTable();
		$source								=	$row->source();

		if ( ! $new ) {
			$old->load( $row->get( 'id', 0, GetterInterface::INT ) );

			if ( Application::Date( $row->get( 'date', null, GetterInterface::STRING ), 'UTC' )->modify( '+5 MINUTES' )->getTimestamp() < Application::Date( 'now', 'UTC' )->getTimestamp() ) {
				$row->params()->set( 'modified', Application::Database()->getUtcDateTime() );
			}

			$_PLUGINS->trigger( 'activity_onBeforeUpdateStreamActivity', array( $stream, $source, &$row, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateStreamActivity', array( $stream, $source, &$row ) );
		}

		if ( ( $row->get( 'asset', null, GetterInterface::STRING ) == $stream->asset() ) && ( $message == '' ) && CBActivity::findParamOverrde( $row, 'message', true ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'Please provide a message.' ), 'warning' );
		}

		$newParams							=	clone $row->params();

		$newParams->unsetEntry( 'overrides' );

		$row->set( 'params', $newParams->asJson() );

		if ( $row->getError() || ( ! $row->check() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_EDIT_FAILED_TO_SAVE', 'Activity failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_EDIT_FAILED_TO_SAVE', 'Activity failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $showTags ) {
			$tagsStream						=	$row->tags( $stream );

			$this->saveTags( $viewer, $tagsStream );

			$row->set( '_tags', null );
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateStreamActivity', array( $stream, $source, $row, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateStreamActivity', array( $stream, $source, $row ) );
		}

		CBActivity::ajaxResponse( HTML_cbactivityActivityContainer::showActivityContainer( $row, $viewer, $stream, $this, 'save' ) );
	}

	/**
	 * Parses an activity or comment attachment to params link
	 *
	 * @param ParamsInterface $link
	 * @param ParamsInterface $attachment
	 * @return array|null
	 */
	private function attachmentToLink( $link, $attachment )
	{
		$type					=	$attachment->get( 'type', 'url', GetterInterface::STRING );

		switch ( $type ) {
			case 'custom':
				return null;
				break;
			case 'video':
				$mediaType		=	'video';
				$media			=	$attachment->subTree( 'media' )->subTree( 'video' )->subTree( 0 );
				break;
			case 'audio':
				$mediaType		=	'audio';
				$media			=	$attachment->subTree( 'media' )->subTree( 'audio' )->subTree( 0 );
				break;
			case 'file':
				$mediaType		=	'file';
				$media			=	$attachment->subTree( 'media' )->subTree( 'file' )->subTree( 0 );
				break;
			case 'image':
			case 'url':
			default:
				$mediaType		=	'image';
				$media			=	$attachment->subTree( 'media' )->subTree( 'image' )->subTree( 0 );
				break;
		}

		$thumbnails				=	array();

		if ( $type == 'url' ) {
			$images				=	array(	$media,
											$attachment->subTree( 'media' )->subTree( 'image' )->subTree( 1 ),
											$attachment->subTree( 'media' )->subTree( 'image' )->subTree( 2 ),
											$attachment->subTree( 'media' )->subTree( 'image' )->subTree( 3 ),
											$attachment->subTree( 'media' )->subTree( 'image' )->subTree( 4 )
										);

			$thumbnails			=	array();

			foreach ( $images as $image ) {
				$imageUrl		=	$image->get( 'url', '', GetterInterface::STRING );

				if ( ! $imageUrl ) {
					continue;
				}

				$thumbnails[]	=	array(	'type'		=>	$image->get( 'type', $mediaType, GetterInterface::STRING ),
											'url'		=>	$imageUrl,
											'filename'	=>	$image->get( 'filename', '', GetterInterface::STRING ),
											'mimetype'	=>	$image->get( 'mimetype', '', GetterInterface::STRING ),
											'extension'	=>	$image->get( 'extension', '', GetterInterface::STRING ),
											'filesize'	=>	$image->get( 'filesize', 0, GetterInterface::INT ),
											'custom'	=>	'',
											'internal'	=>	$image->get( 'internal', false, GetterInterface::BOOLEAN )
										);
			}

			if ( $thumbnails <= 1 ) {
				$thumbnails		=	array();
			}
		}

		$newLink				=	array(	'type'			=>	$type,
											'url'			=>	trim( $link->get( 'url', '', GetterInterface::STRING ) ),
											'title'			=>	trim( $link->get( 'title', $attachment->subTree( 'title' )->get( 0, '', GetterInterface::STRING ), GetterInterface::STRING ) ),
											'description'	=>	trim( $link->get( 'description', $attachment->subTree( 'description' )->get( 0, '', GetterInterface::STRING ), GetterInterface::STRING ) ),
											'media'			=>	array(	'type'		=>	$media->get( 'type', $mediaType, GetterInterface::STRING ),
																		'url'		=>	$media->get( 'url', '', GetterInterface::STRING ),
																		'filename'	=>	$media->get( 'filename', '', GetterInterface::STRING ),
																		'mimetype'	=>	$media->get( 'mimetype', '', GetterInterface::STRING ),
																		'extension'	=>	$media->get( 'extension', '', GetterInterface::STRING ),
																		'filesize'	=>	$media->get( 'filesize', 0, GetterInterface::INT ),
																		'custom'	=>	'',
																		'internal'	=>	$media->get( 'internal', false, GetterInterface::BOOLEAN )
																	),
											'thumbnails'	=>	$thumbnails,
											'selected'		=>	$link->get( 'selected', 0, GetterInterface::INT ),
											'thumbnail'		=>	$link->get( 'thumbnail', true, GetterInterface::BOOLEAN ),
											'internal'		=>	$attachment->get( 'internal', false, GetterInterface::BOOLEAN ),
											'date'			=>	$attachment->get( 'date', null, GetterInterface::STRING ),
											'embedded'		=>	$link->get( 'embedded', false, GetterInterface::BOOLEAN )
										);

		return $newLink;
	}

	/**
	 * Deletes activity
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Activity  $stream
	 */
	private function deleteActivity( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		$row		=	$stream->row( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBActivity::canModerate( $stream ) ) ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to delete this activity.' ), 'error' );
		}

		$source		=	$row->source();

		$_PLUGINS->trigger( 'activity_onBeforeDeleteStreamActivity', array( $stream, $source, &$row ) );

		if ( $row->getError() || ( ! $row->canDelete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_FAILED_TO_DELETE', 'Activity failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->delete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_FAILED_TO_DELETE', 'Activity failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteStreamActivity', array( $stream, $source, $row ) );

		CBActivity::ajaxResponse( CBTxt::T( 'This activity has been deleted.' ), 'notice' );
	}

	/**
	 * Hides activity
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Activity  $stream
	 */
	private function hideActivity( $id, $viewer, $stream )
	{
		global $_CB_framework, $_PLUGINS;

		$type				=	$this->input( 'type', 'activity', GetterInterface::STRING );
		$activity			=	$stream->row( $id );

		if ( ( ! $activity->get( 'id', 0, GetterInterface::INT ) )
			 || ( ! $viewer->get( 'id', 0, GetterInterface::INT ) )
			 || ( $viewer->get( 'id', 0, GetterInterface::INT ) == $activity->get( 'user_id', 0, GetterInterface::INT ) )
			 || ( ! in_array( $type, array( 'activity', 'asset', 'user' ) ) ) )
		{
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to hide this activity.' ), 'error' );
		}

		$row				=	new HiddenTable();

		switch ( $type ) {
			case 'user':
				$hideType	=	'activity.' . $type;
				$hideItem	=	$activity->get( 'user_id', 0, GetterInterface::INT );
				break;
			case 'asset':
				$hideType	=	'activity.' . $type;
				$hideItem	=	$activity->get( 'asset', null, GetterInterface::STRING );
				break;
			case 'activity':
			default:
				$hideType	=	'activity';
				$hideItem	=	$activity->get( 'id', 0, GetterInterface::INT );
				break;
		}

		$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'type' => $hideType, 'item' => $hideItem ) );

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You have already hidden this activity.' ), 'error' );
		}

		$row->set( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ) );
		$row->set( 'type', $hideType );
		$row->set( 'item', $hideItem );

		$source				=	$activity->source();

		$_PLUGINS->trigger( 'activity_onBeforeHideStreamActivity', array( $stream, $source, &$row, $activity ) );

		if ( $row->getError() || ( ! $row->check() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_HIDE_FAILED_TO_SAVE', 'Activity failed to hide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_HIDE_FAILED_TO_SAVE', 'Activity failed to hide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterHideStreamActivity', array( $stream, $source, $row, $activity ) );

		$unhide				=	'<a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'activity', 'func' => 'unhide', 'type' => $type, 'id' => $activity->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityContainerUnhide streamItemAction streamItemActionResponsesRevert">' . CBTxt::T( 'Unhide' ) . '</a>';

		CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_HIDDEN_UNHIDE', 'This activity has been hidden. [unhide]', array( '[unhide]' => $unhide ) ), 'notice' );
	}

	/**
	 * Deletes activity hide
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Activity  $stream
	 */
	private function unhideActivity( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		CBActivity::getTemplate( array( 'activity_container', 'comment_container', 'activity_core', 'stream_attachments' ), false, false );

		$type				=	$this->input( 'type', 'activity', GetterInterface::STRING );
		$activity			=	$stream->row( $id );

		if ( ( ! $activity->get( 'id', 0, GetterInterface::INT ) )
			 || ( ! $viewer->get( 'id', 0, GetterInterface::INT ) )
			 || ( ! in_array( $type, array( 'activity', 'asset', 'user' ) ) ) )
		{
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to unhide this activity.' ), 'error' );
		}

		$row				=	new HiddenTable();

		switch ( $type ) {
			case 'user':
				$hideType	=	'activity.' . $type;
				$hideItem	=	$activity->get( 'user_id', 0, GetterInterface::INT );
				break;
			case 'asset':
				$hideType	=	'activity.' . $type;
				$hideItem	=	$activity->get( 'asset', null, GetterInterface::STRING );
				break;
			case 'activity':
			default:
				$hideType	=	'activity';
				$hideItem	=	$activity->get( 'id', 0, GetterInterface::INT );
				break;
		}

		$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'type' => $hideType, 'item' => $hideItem ) );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You have not hidden this activity.' ), 'error' );
		}

		$source				=	$activity->source();

		$_PLUGINS->trigger( 'activity_onBeforeUnhideStreamActivity', array( $stream, $source, &$row, $activity ) );

		if ( $row->getError() || ( ! $row->canDelete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_HIDE_FAILED_TO_DELETE', 'Activity failed to unhide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->delete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_HIDE_FAILED_TO_DELETE', 'Activity failed to unhide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterUnhideStreamActivity', array( $stream, $source, $row, $activity ) );

		if ( $stream->get( 'hidden', 'visibile', GetterInterface::STRING ) == 'hidden' ) {
			CBActivity::ajaxResponse( CBTxt::T( 'This activity has been unhidden.' ), 'notice' );
		}

		CBActivity::ajaxResponse( HTML_cbactivityActivityContainer::showActivityContainer( $activity, $viewer, $stream, $this, 'save' ), 'html', 'replace', 'container' );
	}

	/**
	 * Deletes activity follow
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Activity  $stream
	 */
	private function unfollowActivity( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		CBActivity::getTemplate( array( 'activity_container', 'comment_container', 'activity_core', 'stream_attachments' ), false, false );

		$activity		=	$stream->row( $id );

		if ( ( ! $activity->get( 'id', 0, GetterInterface::INT ) ) || ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to unfollow this activity.' ), 'error' );
		}

		$row			=	new FollowTable();

		$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'asset' => $activity->get( 'asset', null, GetterInterface::STRING ) ) );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You have not followed this activity.' ), 'error' );
		}

		$source			=	$activity->source();

		$_PLUGINS->trigger( 'activity_onBeforeUnfollowStreamActivity', array( $stream, $source, &$row, $activity ) );

		if ( $row->getError() || ( ! $row->canDelete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_FOLLOW_FAILED_TO_DELETE', 'Activity failed to unfollow! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->delete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_FOLLOW_FAILED_TO_DELETE', 'Activity failed to unfollow! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterUnfollowStreamActivity', array( $stream, $source, $row, $activity ) );

		$activity->params()->set( 'overrides.following', false );

		CBActivity::ajaxResponse( HTML_cbactivityActivityContainer::showActivityContainer( $activity, $viewer, $stream, $this, 'save' ), 'html', 'replace', 'container' );
	}

	/**
	 * Reports an activity entry
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Activity  $stream
	 */
	private function reportActivity( $id, $viewer, $stream )
	{
		global $_CB_framework, $_PLUGINS;

		$activity		=	$stream->row( $id );

		if ( ( ! $this->params->get( 'reporting', true, GetterInterface::BOOLEAN ) )
			 || ( ! $activity->get( 'id', 0, GetterInterface::INT ) )
			 || ( ! $viewer->get( 'id', 0, GetterInterface::INT ) )
			 || ( $viewer->get( 'id', 0, GetterInterface::INT ) == $activity->get( 'user_id', 0, GetterInterface::INT ) )
			 || CBActivity::canModerate( $stream, $activity->get( 'user_id', 0, GetterInterface::INT ) ) )
		{
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to report this activity.' ), 'error' );
		}

		$row			=	new HiddenTable();

		$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'type' => 'activity', 'item' => $activity->get( 'id', 0, GetterInterface::INT ) ) );

		$source			=	$activity->source();

		$activity->params()->set( 'reports', ( $activity->params()->get( 'reports', 0, GetterInterface::INT ) + 1 ) );
		$activity->params()->set( 'reported', Application::Database()->getUtcDateTime() );

		$reportLimit	=	$this->params->get( 'reporting_limit', 10, GetterInterface::INT );

		if ( $reportLimit && ( $activity->params()->get( 'reports', 0, GetterInterface::INT ) >= $reportLimit ) ) {
			$activity->set( 'published', 0 );
		}

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			$row->set( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ) );
			$row->set( 'type', 'activity' );
			$row->set( 'item', $activity->get( 'id', 0, GetterInterface::INT ) );

			$_PLUGINS->trigger( 'activity_onBeforeHideStreamActivity', array( $stream, $source, &$row, $activity ) );

			if ( $row->getError() || ( ! $row->check() ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_HIDE_FAILED_TO_SAVE', 'Activity failed to hide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			if ( $row->getError() || ( ! $row->store() ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_HIDE_FAILED_TO_SAVE', 'Activity failed to hide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			$_PLUGINS->trigger( 'activity_onAfterHideStreamActivity', array( $stream, $source, $row, $activity ) );
		}

		$_PLUGINS->trigger( 'activity_onBeforeReportStreamActivity', array( $stream, $source, &$activity, $row ) );

		$newParams		=	clone $activity->params();

		$newParams->unsetEntry( 'overrides' );

		$activity->set( 'params', $newParams->asJson() );

		if ( $activity->getError() || ( ! $activity->check() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_REPORT_FAILED_TO_SAVE', 'Activity failed to report! Error: [error]', array( '[error]' => $activity->getError() ) ), 'error' );
		}

		if ( $activity->getError() || ( ! $activity->store() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_REPORT_FAILED_TO_SAVE', 'Activity failed to report! Error: [error]', array( '[error]' => $activity->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterReportStreamActivity', array( &$stream, $source, $activity, $row ) );

		if ( $this->params->get( 'reporting_notify', true, GetterInterface::BOOLEAN ) ) {
			$cbUser				=	CBuser::getInstance( $viewer->get( 'id', 0, GetterInterface::INT ), false );

			$extraStrings		=	array(	'activity_id'		=>	$activity->get( 'id', 0, GetterInterface::INT ),
											'activity_title'	=>	$activity->get( 'title', null, GetterInterface::STRING ),
											'activity_message'	=>	$activity->get( 'message', null, GetterInterface::STRING ),
											'activity_url'		=>	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'activity', 'func' => 'show', 'id' => $activity->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ) ),
											'user_url'			=>	$_CB_framework->viewUrl( 'userprofile', true, array( 'user' => $viewer->get( 'user_id', 0, GetterInterface::INT ) ) )
										);

			$subject			=	$cbUser->replaceUserVars( CBTxt::T( 'Activity - Reported!' ), false, true, $extraStrings, false );

			if ( ! $activity->get( 'published', 1, GetterInterface::INT ) ) {
				$message		=	$cbUser->replaceUserVars( CBTxt::T( '<a href="[user_url]">[formatname]</a> reported activity <a href="[activity_url]">[cb:if activity_message!=""][activity_message][cb:else]#[activity_id][/cb:else][/cb:if]</a> as controversial and has now been unpublished!' ), false, true, $extraStrings, false );
			} else {
				$message		=	$cbUser->replaceUserVars( CBTxt::T( '<a href="[user_url]">[formatname]</a> reported activity <a href="[activity_url]">[cb:if activity_message!=""][activity_message][cb:else]#[activity_id][/cb:else][/cb:if]</a> as controversial!' ), false, true, $extraStrings, false );
			}

			$notifications		=	new cbNotification();
			$recipients			=	$stream->get( 'moderators', array(), GetterInterface::RAW );

			if ( $recipients ) {
				cbToArrayOfInt( $recipients );

				foreach ( $recipients as $recipient ) {
					$notifications->sendFromSystem( $recipient, $subject, $message, false, 1 );
				}
			} else {
				$notifications->sendToModerators( $subject, $message, false, 1 );
			}
		}

		CBActivity::ajaxResponse( CBTxt::T( 'This activity has been reported and hidden.' ), 'notice' );
	}

	/**
	 * Pins activity to the top of a stream
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Activity  $stream
	 */
	private function pinActivity( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		CBActivity::getTemplate( array( 'activity_container', 'comment_container', 'activity_core', 'stream_attachments' ), false, false );

		$row		=	$stream->row( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ! Application::MyUser()->isGlobalModerator() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to pin this activity.' ), 'error' );
		}

		if ( $row->get( 'pinned', false, GetterInterface::BOOLEAN ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You have already pinned this activity.' ), 'error' );
		}

		$row->set( 'pinned', 1 );

		$source		=	$row->source();

		$_PLUGINS->trigger( 'activity_onBeforePinStreamActivity', array( $stream, $source, &$row ) );

		if ( $row->getError() || ( ! $row->check() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_PIN_FAILED_TO_SAVE', 'Activity failed to pin! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_PIN_FAILED_TO_SAVE', 'Activity failed to pin! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterPinStreamActivity', array( $stream, $source, $row ) );

		CBActivity::ajaxResponse( HTML_cbactivityActivityContainer::showActivityContainer( $row, $viewer, $stream, $this, 'save' ), 'html', 'replace', 'container' );
	}

	/**
	 * Unpins activity from the top of a stream
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Activity  $stream
	 */
	private function unpinActivity( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		CBActivity::getTemplate( array( 'activity_container', 'comment_container', 'activity_core', 'stream_attachments' ), false, false );

		$row		=	$stream->row( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ! Application::MyUser()->isGlobalModerator() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to unpin this activity.' ), 'error' );
		}

		if ( ! $row->get( 'pinned', false, GetterInterface::BOOLEAN ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You have not pinned this activity.' ), 'error' );
		}

		$row->set( 'pinned', 0 );

		$source		=	$row->source();

		$_PLUGINS->trigger( 'activity_onBeforeUnpinStreamActivity', array( $stream, $source, &$row ) );

		if ( $row->getError() || ( ! $row->check() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_UNPIN_FAILED_TO_SAVE', 'Activity failed to unpin! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'ACTIVITY_UNPIN_FAILED_TO_SAVE', 'Activity failed to unpin! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterUnpinStreamActivity', array( $stream, $source, $row ) );

		CBActivity::ajaxResponse( HTML_cbactivityActivityContainer::showActivityContainer( $row, $viewer, $stream, $this, 'save' ), 'html', 'replace', 'container' );
	}

	/**
	 * Displays activity as a button
	 *
	 * @param UserTable $viewer
	 * @param Activity  $stream
	 * @param string    $output
	 */
	private function showActivityButton( $viewer, $stream, $output = null )
	{
		CBActivity::getTemplate( 'activity_button' );

		HTML_cbactivityActivityButton::showActivityButton( $viewer, $stream, $this );
	}

	/**
	 * Displays notifications stream
	 *
	 * @param int           $id
	 * @param UserTable     $viewer
	 * @param Notifications $stream
	 * @param string        $output
	 */
	private function showNotifications( $id, $viewer, $stream, $output = null )
	{
		global $_CB_framework;

		CBActivity::getTemplate( array( 'notifications', 'notification_container', 'activity_container', 'comment_container', 'activity_core', 'stream_attachments', 'twemoji' ) );

		$notificationPrefix		=	'notifications_' . $stream->id() . '_';

		$stream->set( 'published', 1 );

		if ( $id ) {
			if ( $output == 'update' ) {
				$stream->set( 'date', array( Application::Database()->getUtcDateTime( $id ), '>' ) );
			} else {
				$stream->set( 'id', $id );
			}
		} elseif ( $output == 'update' ) {
			return;
		}

		$rowsLimitstart			=	$this->input( $notificationPrefix . 'limitstart', 0, GetterInterface::INT );

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$rowsTotal			=	$stream->rows( 'count' );
		} else {
			$rowsTotal			=	0;
		}

		if ( $rowsTotal <= $rowsLimitstart ) {
			$rowsLimitstart		=	0;
		}

		$pageNav				=	new cbPageNav( $rowsTotal, $rowsLimitstart, $stream->get( 'paging_limit', 15, GetterInterface::INT ) );

		$pageNav->setInputNamePrefix( $notificationPrefix );

		$pageNav->setBaseURL( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'notifications', 'func' => 'load', 'stream' => $stream->id() ), 'raw', 0, true ) );

		$stream->set( 'paging_limitstart', $pageNav->limitstart );

		$rows					=	array();

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) && $rowsTotal ) {
			$rows				=	$stream->rows();

			if ( $stream->get( 'direction', 'down', GetterInterface::STRING ) == 'up' ) {
				$rows			=	array_reverse( $rows, true );
			}
		}

		$pageNav->limitstart	=	$stream->get( 'paging_limitstart', 0, GetterInterface::INT );

		HTML_cbactivityNotifications::showNotifications( $rows, $pageNav, $viewer, $stream, $this, $output );

		// Skip the read check if the stream isn't outputting read state:
		if ( $stream->get( 'read', null, GetterInterface::STRING ) === null ) {
			return;
		}

		$read					=	new ReadTable();

		$read->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'asset' => $stream->asset() ) );

		$read->set( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ) );
		$read->set( 'asset', $stream->asset() );
		$read->set( 'date', Application::Database()->getUtcDateTime() );

		$read->store();
	}

	/**
	 * Deletes notification
	 *
	 * @param int           $id
	 * @param UserTable     $viewer
	 * @param Notifications $stream
	 */
	private function deleteNotification( $id, $viewer, $stream )
	{
		global $_CB_database, $_PLUGINS;

		if ( $id ) {
			$row				=	$stream->row( $id );

			if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user', 0, GetterInterface::INT ) ) && ( ! CBActivity::canModerate( $stream ) ) ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to delete this notification.' ), 'error' );
			}

			$source				=	$row->source();

			$_PLUGINS->trigger( 'activity_onBeforeDeleteStreamNotification', array( $stream, $source, &$row ) );

			if ( $row->getError() || ( ! $row->canDelete() ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'NOTIFICATION_FAILED_TO_DELETE', 'Notification failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			if ( $row->getError() || ( ! $row->delete() ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'NOTIFICATION_FAILED_TO_DELETE', 'Notification failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			$_PLUGINS->trigger( 'activity_onAfterDeleteStreamNotification', array( $stream, $source, $row ) );

			CBActivity::ajaxResponse();
		} else {
			if ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $stream->user()->get( 'id', 0, GetterInterface::INT ) ) && ( ! CBActivity::canModerate( $stream ) ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to delete notifications.' ), 'error' );
			}

			$query				=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_notifications' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'user' ) . " = " . $stream->user()->get( 'id', 0, GetterInterface::INT )
								.	"\n AND " . $_CB_database->NameQuote( 'asset' ) . " != " . $_CB_database->Quote( 'global' );
			$_CB_database->setQuery( $query );
			$rows				=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\NotificationTable', array( $_CB_database ) );

			/** @var NotificationTable[] $rows */
			foreach ( $rows as $row ) {
				$source			=	$row->source();

				$_PLUGINS->trigger( 'activity_onBeforeDeleteStreamNotification', array( $stream, $source, &$row ) );

				if ( $row->getError() || ( ! $row->canDelete() ) ) {
					continue;
				}

				if ( $row->getError() || ( ! $row->delete() ) ) {
					continue;
				}

				$_PLUGINS->trigger( 'activity_onAfterDeleteStreamNotification', array( $stream, $source, $row ) );
			}

			// Hide the remaining notifications that the user doesn't own:
			foreach ( $stream->rows( 'all' ) as $row ) {
				if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user_id', 0, GetterInterface::INT ) ) {
					continue;
				}

				$hidden			=	new HiddenTable();

				$hidden->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'type' => 'notification', 'item' => $row->get( 'id', 0, GetterInterface::INT ) ) );

				if ( $hidden->get( 'id', 0, GetterInterface::INT ) ) {
					continue;
				}

				$hidden->set( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ) );
				$hidden->set( 'type', 'notification' );
				$hidden->set( 'item', $row->get( 'id', 0, GetterInterface::INT ) );

				$source			=	$row->source();

				$_PLUGINS->trigger( 'activity_onBeforeHideStreamNotification', array( $stream, $source, &$hidden, $row ) );

				if ( $hidden->getError() || ( ! $hidden->check() ) ) {
					continue;
				}

				if ( $hidden->getError() || ( ! $hidden->store() ) ) {
					continue;
				}

				$_PLUGINS->trigger( 'activity_onAfterHideStreamNotification', array( $stream, $source, $hidden, $row ) );
			}

			CBActivity::ajaxResponse( CBTxt::T( 'No notifications to display.' ) );
		}
	}

	/**
	 * Hides notification
	 *
	 * @param int           $id
	 * @param UserTable     $viewer
	 * @param Notifications $stream
	 */
	private function hideNotification( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		$type				=	$this->input( 'type', 'notification', GetterInterface::STRING );
		$notification		=	$stream->row( $id );

		if ( ( ! $notification->get( 'id', 0, GetterInterface::INT ) )
			 || ( ! $viewer->get( 'id', 0, GetterInterface::INT ) )
			 || ( $viewer->get( 'id', 0, GetterInterface::INT ) == $notification->get( 'user_id', 0, GetterInterface::INT ) )
			 || ( ! in_array( $type, array( 'notification', 'asset', 'user' ) ) ) )
		{
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to hide this notification.' ), 'error' );
		}

		$row				=	new HiddenTable();

		switch ( $type ) {
//			case 'user':
//				$hideType	=	'notification.' . $type;
//				$hideItem	=	$notification->get( 'user_id', 0, GetterInterface::INT );
//				break;
//			case 'asset':
//				$hideType	=	'notification.' . $type;
//				$hideItem	=	$notification->get( 'asset', null, GetterInterface::STRING );
//				break;
			case 'notification':
			default:
				$hideType	=	'notification';
				$hideItem	=	$notification->get( 'id', 0, GetterInterface::INT );
				break;
		}

		$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'type' => $hideType, 'item' => $hideItem ) );

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You have already hidden this notification.' ), 'error' );
		}

		$row->set( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ) );
		$row->set( 'type', $hideType );
		$row->set( 'item', $hideItem );

		$source				=	$notification->source();

		$_PLUGINS->trigger( 'activity_onBeforeHideStreamNotification', array( $stream, $source, &$row, $notification ) );

		if ( $row->getError() || ( ! $row->check() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'NOTIFICATION_HIDE_FAILED_TO_SAVE', 'Notification failed to hide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'NOTIFICATION_HIDE_FAILED_TO_SAVE', 'Notification failed to hide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterHideStreamNotification', array( $stream, $source, $row, $notification ) );

		CBActivity::ajaxResponse();
	}

	/**
	 * Deletes notification hide
	 *
	 * @param int           $id
	 * @param UserTable     $viewer
	 * @param Notifications $stream
	 */
	private function unhideNotification( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		$type				=	$this->input( 'type', 'notification', GetterInterface::STRING );
		$notification		=	$stream->row( $id );

		if ( ( ! $notification->get( 'id', 0, GetterInterface::INT ) )
			 || ( ! $viewer->get( 'id', 0, GetterInterface::INT ) )
			 || ( ! in_array( $type, array( 'notification', 'asset', 'user' ) ) ) )
		{
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to unhide this notification.' ), 'error' );
		}

		$row				=	new HiddenTable();

		switch ( $type ) {
//			case 'user':
//				$hideType	=	'notification.' . $type;
//				$hideItem	=	$notification->get( 'user_id', 0, GetterInterface::INT );
//				break;
//			case 'asset':
//				$hideType	=	'notification.' . $type;
//				$hideItem	=	$notification->get( 'asset', null, GetterInterface::STRING );
//				break;
			case 'notification':
			default:
				$hideType	=	'notification';
				$hideItem	=	$notification->get( 'id', 0, GetterInterface::INT );
				break;
		}

		$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'type' => $hideType, 'item' => $hideItem ) );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You have not hidden this notification.' ), 'error' );
		}

		$source				=	$notification->source();

		$_PLUGINS->trigger( 'activity_onBeforeUnhideStreamNotification', array( $stream, $source, &$row, $notification ) );

		if ( $row->getError() || ( ! $row->canDelete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'NOTIFICATION_HIDE_FAILED_TO_DELETE', 'Notification failed to unhide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->delete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'NOTIFICATION_HIDE_FAILED_TO_DELETE', 'Notification failed to unhide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterUnhideStreamNotification', array( $stream, $source, $row, $notification ) );

		CBActivity::ajaxResponse();
	}

	/**
	 * Displays notifications as a button
	 *
	 * @param UserTable     $viewer
	 * @param Notifications $stream
	 * @param string        $output
	 */
	private function showNotificationsButton( $viewer, $stream, $output = null )
	{
		CBActivity::getTemplate( 'notifications_button' );

		HTML_cbactivityNotificationsButton::showNotificationsButton( $viewer, $stream, $this, $output );
	}

	/**
	 * Displays comments stream
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Comments  $stream
	 * @param string    $output
	 */
	private function showComments( $id, $viewer, $stream, $output = null )
	{
		global $_CB_framework;

		CBActivity::getTemplate( array( 'comments', 'comment_new', 'comment_container', 'stream_attachments', 'twemoji' ) );

		$commentsPrefix			=	'comments_' . $stream->id() . '_';

		$stream->set( 'published', 1 );

		if ( $id ) {
			if ( $output == 'update' ) {
				$stream->set( 'date', array( $id, '>' ) );
			} else {
				$stream->set( 'id', $id );
			}
		} elseif ( $output == 'update' ) {
			return;
		}

		$rowsLimitstart			=	$this->input( $commentsPrefix . 'limitstart', 0, GetterInterface::INT );

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$rowsTotal			=	$stream->rows( 'count' );
		} else {
			$rowsTotal			=	0;
		}

		if ( $rowsTotal <= $rowsLimitstart ) {
			$rowsLimitstart		=	0;
		}

		$pageNav				=	new cbPageNav( $rowsTotal, $rowsLimitstart, $stream->get( 'paging_limit', 15, GetterInterface::INT ) );

		$pageNav->setInputNamePrefix( $commentsPrefix );
		$pageNav->setBaseURL( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'comments', 'func' => 'load', 'stream' => $stream->id() ), 'raw', 0, true ) );

		$stream->set( 'paging_limitstart', $pageNav->limitstart );

		$rows					=	array();

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) && $rowsTotal ) {
			$rows				=	$stream->rows();

			if ( $stream->get( 'direction', 'down', GetterInterface::STRING ) == 'up' ) {
				$rows			=	array_reverse( $rows, true );
			}
		}

		$pageNav->limitstart	=	$stream->get( 'paging_limitstart', 0, GetterInterface::INT );

		HTML_cbactivityComments::showComments( $rows, $pageNav, $viewer, $stream, $this, $output );
	}

	/**
	 * Displays comment edit
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Comments  $stream
	 */
	private function showCommentEdit( $id, $viewer, $stream )
	{
		CBActivity::getTemplate( array( 'comment_edit', 'stream_attachments' ), false, false );

		$row	=	$stream->row( $id );

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBActivity::canModerate( $stream ) ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to edit this comment.' ), 'error' );
			}
		} else {
			CBActivity::ajaxResponse( CBTxt::T( 'No comment to edit.' ), 'error' );
		}

		CBActivity::ajaxResponse( HTML_cbactivityCommentEdit::showCommentEdit( $row, $viewer, $stream, $this ) );
	}

	/**
	 * Saves comment
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Comments  $stream
	 */
	private function saveComment( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		$canModerate						=	CBActivity::canModerate( $stream );

		CBActivity::getTemplate( array( 'comment_container', 'stream_attachments' ), false, false );

		$row								=	$stream->row( $id );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ! CBActivity::canCreate( 'comment', $stream, $viewer ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to create comments.' ), 'error' );
			}
		} elseif ( ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! $canModerate ) ) || ( ! CBActivity::findParamOverrde( $row, 'edit', true ) ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to edit this comment.' ), 'error' );
		}

		$messageLimit						=	( $canModerate ? 0 : $stream->get( 'message_limit', 400, GetterInterface::INT ) );
		$showActions						=	CBActivity::findParamOverrde( $row, 'actions', false, $stream );
		$actionLimit						=	( $canModerate ? 0 : $stream->get( 'actions_message_limit', 100, GetterInterface::INT ) );
		$showLocations						=	CBActivity::findParamOverrde( $row, 'locations', false, $stream );
		$locationLimit						=	( $canModerate ? 0 : $stream->get( 'locations_address_limit', 200, GetterInterface::INT ) );
		$showLinks							=	CBActivity::findParamOverrde( $row, 'links', false, $stream );
		$linkLimit							=	( $canModerate ? 0 : $stream->get( 'links_link_limit', 5, GetterInterface::INT ) );
		$showTags							=	CBActivity::findParamOverrde( $row, 'tags', false, $stream );

		$row->set( 'user_id', $row->get( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT ) );
		$row->set( 'asset', $row->get( 'asset', $stream->asset(), GetterInterface::STRING ) );

		$message							=	trim( $this->input( 'message', $row->get( 'message', null, GetterInterface::STRING ), GetterInterface::STRING ) );

		// Remove duplicate spaces:
		$message							=	preg_replace( '/ {2,}/i', ' ', $message );
		// Remove duplicate tabs:
		$message							=	preg_replace( '/\t{2,}/i', "\t", $message );
		// Remove duplicate linebreaks:
		$message							=	preg_replace( '/((?:\r\n|\r|\n){2})(?:\r\n|\r|\n)*/i', '$1', $message );

		if ( $messageLimit && ( cbutf8_strlen( $message ) > $messageLimit ) ) {
			$message						=	cbutf8_substr( $message, 0, $messageLimit );
		}

		$row->set( 'message', $message );

		if ( $message == '' ) {
			CBActivity::ajaxResponse( CBTxt::T( 'Please provide a message.' ), 'warning' );
		}

		$new								=	( $row->get( 'id', 0, GetterInterface::INT ) ? false : true );

		if ( $showActions ) {
			$existingAction					=	$row->params()->subTree( 'action' );
			$action							=	$this->getInput()->subTree( 'actions' );
			$actionId						=	$action->get( 'id', $existingAction->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT );

			if ( ! in_array( $actionId, array_keys( CBActivity::loadActionOptions( true, $stream, $existingAction->get( 'id', 0, GetterInterface::INT ) ) ) ) ) {
				$actionId					=	0;
			}

			$actionMessage					=	( $actionId ? trim( $action->get( 'message', $existingAction->get( 'message', '', GetterInterface::STRING ), GetterInterface::STRING ) ) : '' );
			$actionEmote					=	( $actionId ? $action->get( 'emote', $existingAction->get( 'emote', 0, GetterInterface::INT ), GetterInterface::INT ) : 0 );

			if ( ! in_array( $actionEmote, array_keys( CBActivity::loadEmoteOptions( false, true ) ) ) ) {
				$actionEmote				=	0;
			}

			// Remove linebreaks:
			$actionMessage					=	str_replace( array( "\n", "\r\n" ), ' ', $actionMessage );
			// Remove duplicate spaces:
			$actionMessage					=	preg_replace( '/ {2,}/i', ' ', $actionMessage );
			// Remove duplicate tabs:
			$actionMessage					=	preg_replace( '/\t{2,}/i', "\t", $actionMessage );

			if ( $actionLimit && ( cbutf8_strlen( $actionMessage ) > $actionLimit ) ) {
				$actionMessage				=	cbutf8_substr( $actionMessage, 0, $actionLimit );
			}

			$actionId						=	( $actionMessage ? $actionId : 0 );

			$newAction						=	array(	'id'		=>	$actionId,
														'message'	=>	( $actionId ? $actionMessage : '' ),
														'emote'		=>	( $actionId ? $actionEmote : 0 )
													);

			$row->params()->set( 'action', $newAction );
		}

		if ( $showLocations ) {
			$existingLocation				=	$row->params()->subTree( 'location' );
			$location						=	$this->getInput()->subTree( 'location' );
			$locationId						=	$location->get( 'id', $existingLocation->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT );

			if ( ! in_array( $locationId, array_keys( CBActivity::loadLocationOptions( true, $stream, $existingLocation->get( 'id', 0, GetterInterface::INT ) ) ) ) ) {
				$locationId					=	0;
			}

			$locationPlace					=	( $locationId ? trim( $location->get( 'place', $existingLocation->get( 'place', '', GetterInterface::STRING ), GetterInterface::STRING ) ) : '' );
			$locationAddress				=	( $locationId ? trim( $location->get( 'address', $existingLocation->get( 'place', '', GetterInterface::STRING ), GetterInterface::STRING ) ) : '' );

			if ( $locationLimit && ( cbutf8_strlen( $locationPlace ) > $locationLimit ) ) {
				$locationPlace				=	cbutf8_substr( $locationPlace, 0, $locationLimit );
			}

			if ( $locationLimit && ( cbutf8_strlen( $locationAddress ) > $locationLimit ) ) {
				$locationAddress			=	cbutf8_substr( $locationAddress, 0, $locationLimit );
			}

			$locationId						=	( $locationPlace ? $locationId : 0 );

			$newLocation					=	array(	'id'		=>	$locationId,
														'place'		=>	( $locationId ? $locationPlace : '' ),
														'address'	=>	( $locationId ? $locationAddress : '' )
													);

			$row->params()->set( 'location', $newLocation );
		}

		if ( $showLinks ) {
			$newUrls						=	array();
			$newLinks						=	array();
			$urls							=	$stream->parser( $message )->urls();

			/** @var ParamsInterface $links */
			$links							=	$this->getInput()->subTree( 'links' );

			if ( $stream->get( 'links_embedded', false, GetterInterface::BOOLEAN ) ) {
				$index						=	( $links->count() - 1 );

				foreach ( $urls as $url ) {
					foreach ( $links as $link ) {
						/** @var ParamsInterface $link */
						if ( trim( $link->get( 'url', '', GetterInterface::STRING ) ) == $url ) {
							continue 2;
						}
					}

					$index++;

					$links->set( $index, array( 'url' => $url, 'embedded' => true ) );
				}
			}

			foreach ( $links as $i => $link ) {
				if ( $linkLimit && ( ( $i + 1 ) > $linkLimit ) ) {
					break;
				}

				$linkUrl					=	trim( $link->get( 'url', '', GetterInterface::STRING ) );

				if ( ( ! $linkUrl ) || in_array( $linkUrl, $newUrls ) ) {
					continue;
				}

				$linkEmbedded				=	$link->get( 'embedded', false, GetterInterface::BOOLEAN );

				if ( $linkEmbedded && ( ! in_array( $linkUrl, $urls ) ) ) {
					continue;
				}

				if ( $link->get( 'parsed', false, GetterInterface::BOOLEAN ) ) {
					foreach ( $row->params()->subTree( 'links' ) as $existingLink ) {
						/** @var ParamsInterface $existingLink */
						if ( trim( $existingLink->get( 'url', '', GetterInterface::STRING ) ) == $linkUrl ) {
							$existingLink->set( 'title', trim( $link->get( 'title', $existingLink->get( 'title', '', GetterInterface::STRING ), GetterInterface::STRING ) ) );
							$existingLink->set( 'description', trim( $link->get( 'description', $existingLink->get( 'description', '', GetterInterface::STRING ), GetterInterface::STRING ) ) );
							$existingLink->set( 'thumbnail', $link->get( 'thumbnail', true, GetterInterface::BOOLEAN ) );

							if ( $existingLink->get( 'type', null, GetterInterface::STRING ) == 'url' ) {
								$selected	=	$link->get( 'selected', 0, GetterInterface::INT );

								/** @var ParamsInterface $thumbnail */
								$thumbnail	=	$existingLink->subTree( 'thumbnails' )->subTree( $selected );

								if ( $thumbnail->get( 'url', '', GetterInterface::STRING ) ) {
									$existingLink->set( 'media', $thumbnail->asArray() );
									$existingLink->set( 'selected', $selected );
								}
							}

							$newLinks[]		=	$existingLink->asArray();
							break;
						}
					}

					continue;
				}

				$attachment					=	$stream->parser()->attachment( $linkUrl );

				if ( ! $attachment ) {
					continue;
				}

				$newLink					=	$this->attachmentToLink( $link, $attachment );

				if ( ! $newLink ) {
					continue;
				}

				$newLinks[]					=	$newLink;
				$newUrls[]					=	$linkUrl;
			}

			if ( ! $new ) {
				foreach ( $row->params()->subTree( 'links' ) as $link ) {
					/** @var ParamsInterface $link */
					if ( $link->get( 'type', 'url', GetterInterface::STRING ) != 'custom' ) {
						continue;
					}

					$newLinks[]				=	$link->asArray();
				}
			}

			$row->params()->set( 'links', $newLinks );
		}

		$old								=	new CommentTable();
		$source								=	$row->source();

		if ( ! $new ) {
			$old->load( $row->get( 'id', 0, GetterInterface::INT ) );

			if ( Application::Date( $row->get( 'date', null, GetterInterface::STRING ), 'UTC' )->modify( '+5 MINUTES' )->getTimestamp() < Application::Date( 'now', 'UTC' )->getTimestamp() ) {
				$row->params()->set( 'modified', Application::Database()->getUtcDateTime() );
			}

			$_PLUGINS->trigger( 'activity_onBeforeUpdateStreamComment', array( $stream, $source, &$row, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateStreamComment', array( $stream, $source, &$row ) );
		}

		$newParams							=	clone $row->params();

		$newParams->unsetEntry( 'overrides' );

		$row->set( 'params', $newParams->asJson() );

		if ( $row->getError() || ( ! $row->check() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_EDIT_FAILED_TO_SAVE', 'Comment failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_EDIT_FAILED_TO_SAVE', 'Comment failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $showTags ) {
			$tagsStream						=	$row->tags( $stream );

			$this->saveTags( $viewer, $tagsStream );

			$row->set( '_tags', null );
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateStreamComment', array( $stream, $source, $row, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateStreamComment', array( $stream, $source, $row ) );
		}

		CBActivity::ajaxResponse( HTML_cbactivityCommentContainer::showCommentContainer( $row, $viewer, $stream, $this, 'save' ) );
	}

	/**
	 * Deletes comment
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Comments  $stream
	 */
	private function deleteComment( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		$row		=	$stream->row( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! CBActivity::canModerate( $stream ) ) ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to delete this comment.' ), 'error' );
		}

		$source		=	$row->source();

		$_PLUGINS->trigger( 'activity_onBeforeDeleteStreamComment', array( $stream, $source, &$row ) );

		if ( $row->getError() || ( ! $row->canDelete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_FAILED_TO_DELETE', 'Comment failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->delete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_FAILED_TO_DELETE', 'Comment failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteStreamComment', array( $stream, $source, $row ) );

		if ( preg_match( '/^comment\.(\d+)/', $stream->asset() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'This reply has been deleted.' ), 'notice' );
		} else {
			CBActivity::ajaxResponse( CBTxt::T( 'This comment has been deleted.' ), 'notice' );
		}
	}

	/**
	 * Hides comment
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Comments  $stream
	 */
	private function hideComment( $id, $viewer, $stream )
	{
		global $_CB_framework, $_PLUGINS;

		$type				=	$this->input( 'type', 'comment', GetterInterface::STRING );
		$comment			=	$stream->row( $id );

		if ( ( ! $comment->get( 'id', 0, GetterInterface::INT ) )
			 || ( ! $viewer->get( 'id', 0, GetterInterface::INT ) )
			 || ( $viewer->get( 'id', 0, GetterInterface::INT ) == $comment->get( 'user_id', 0, GetterInterface::INT ) )
			 || ( ! in_array( $type, array( 'comment', 'asset', 'user' ) ) ) )
		{
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to hide this comment.' ), 'error' );
		}

		$row				=	new HiddenTable();

		switch ( $type ) {
			case 'user':
				$hideType	=	'comment.' . $type;
				$hideItem	=	$comment->get( 'user_id', 0, GetterInterface::INT );
				break;
			case 'asset':
				$hideType	=	'comment.' . $type;
				$hideItem	=	$comment->get( 'asset', null, GetterInterface::STRING );
				break;
			case 'comment':
			default:
				$hideType	=	'comment';
				$hideItem	=	$comment->get( 'id', 0, GetterInterface::INT );
				break;
		}

		$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'type' => $hideType, 'item' => $hideItem ) );

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You have already hidden this comment.' ), 'error' );
		}

		$row->set( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ) );
		$row->set( 'type', $hideType );
		$row->set( 'item', $hideItem );

		$source				=	$comment->source();

		$_PLUGINS->trigger( 'activity_onBeforeHideStreamComment', array( $stream, $source, &$row, $comment ) );

		if ( $row->getError() || ( ! $row->check() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_HIDE_FAILED_TO_SAVE', 'Comment failed to hide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_HIDE_FAILED_TO_SAVE', 'Comment failed to hide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterHideStreamComment', array( $stream, $source, $row, $comment ) );

		$unhide				=	'<a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'comments', 'func' => 'unhide', 'type' => $type, 'id' => $comment->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentsContainerUnhide streamItemAction streamItemActionResponsesRevert">' . CBTxt::T( 'Unhide' ) . '</a>';

		if ( preg_match( '/^comment\.(\d+)/', $stream->asset() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_REPLY_HIDDEN_UNHIDE', 'This reply has been hidden. [unhide]', array( '[unhide]' => $unhide ) ), 'notice' );
		} else {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_HIDDEN_UNHIDE', 'This comment has been hidden. [unhide]', array( '[unhide]' => $unhide ) ), 'notice' );
		}
	}

	/**
	 * Deletes comment hide
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Comments  $stream
	 */
	private function unhideComment( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		CBActivity::getTemplate( array( 'comment_container', 'stream_attachments' ), false, false );

		$type				=	$this->input( 'type', 'comment', GetterInterface::STRING );
		$comment			=	$stream->row( $id );

		if ( ( ! $comment->get( 'id', 0, GetterInterface::INT ) ) || ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( ! in_array( $type, array( 'comment', 'asset', 'user' ) ) ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to unhide this comment.' ), 'error' );
		}

		$row				=	new HiddenTable();

		switch ( $type ) {
			case 'user':
				$hideType	=	'comment.' . $type;
				$hideItem	=	$comment->get( 'user_id', 0, GetterInterface::INT );
				break;
			case 'asset':
				$hideType	=	'comment.' . $type;
				$hideItem	=	$comment->get( 'asset', null, GetterInterface::STRING );
				break;
			case 'comment':
			default:
				$hideType	=	'comment';
				$hideItem	=	$comment->get( 'id', 0, GetterInterface::INT );
				break;
		}

		$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'type' => $hideType, 'item' => $hideItem ) );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You have not hidden this comment.' ), 'error' );
		}

		$source				=	$comment->source();

		$_PLUGINS->trigger( 'activity_onBeforeUnhideStreamComment', array( $stream, $source, &$row, $comment ) );

		if ( $row->getError() || ( ! $row->canDelete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_HIDE_FAILED_TO_DELETE', 'Comment failed to unhide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->delete() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_HIDE_FAILED_TO_DELETE', 'Comment failed to unhide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterUnhideStreamComment', array( $stream, $source, $row, $comment ) );

		if ( $stream->get( 'hidden', 'visibile', GetterInterface::STRING ) == 'hidden' ) {
			CBActivity::ajaxResponse( CBTxt::T( 'This comment has been unhidden.' ), 'notice' );
		}

		CBActivity::ajaxResponse( HTML_cbactivityCommentContainer::showCommentContainer( $comment, $viewer, $stream, $this, 'save' ), 'html', 'replace', 'container' );
	}

	/**
	 * Reports a comment entry
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Comments  $stream
	 */
	private function reportComment( $id, $viewer, $stream )
	{
		global $_CB_framework, $_PLUGINS;

		$comment				=	$stream->row( $id );

		if ( ( ! $this->params->get( 'reporting', true, GetterInterface::BOOLEAN ) )
			 || ( ! $comment->get( 'id', 0, GetterInterface::INT ) )
			 || ( ! $viewer->get( 'id', 0, GetterInterface::INT ) )
			 || ( $viewer->get( 'id', 0, GetterInterface::INT ) == $comment->get( 'user_id', 0, GetterInterface::INT ) )
			 || CBActivity::canModerate( $stream, $comment->get( 'user_id', 0, GetterInterface::INT ) ) )
		{
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to report this comment.' ), 'error' );
		}

		$row					=	new HiddenTable();

		$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'type' => 'comment', 'item' => $comment->get( 'id', 0, GetterInterface::INT ) ) );

		$source					=	$comment->source();

		$comment->params()->set( 'reports', ( $comment->params()->get( 'reports', 0, GetterInterface::INT ) + 1 ) );
		$comment->params()->set( 'reported', Application::Database()->getUtcDateTime() );

		$reportLimit			=	$this->params->get( 'reporting_limit', 10, GetterInterface::INT );

		if ( $reportLimit && ( $comment->params()->get( 'reports', 0, GetterInterface::INT ) >= $reportLimit ) ) {
			$comment->set( 'published', 0 );
		}

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			$row->set( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ) );
			$row->set( 'type', 'comment' );
			$row->set( 'item', $comment->get( 'id', 0, GetterInterface::INT ) );

			$_PLUGINS->trigger( 'activity_onBeforeHideStreamComment', array( $stream, $source, &$row, $comment ) );

			if ( $row->getError() || ( ! $row->check() ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_HIDE_FAILED_TO_SAVE', 'Comment failed to hide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			if ( $row->getError() || ( ! $row->store() ) ) {
				CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_HIDE_FAILED_TO_SAVE', 'Comment failed to hide! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			$_PLUGINS->trigger( 'activity_onAfterHideStreamComment', array( $stream, $source, $row, $comment ) );
		}

		$_PLUGINS->trigger( 'activity_onBeforeReportStreamComment', array( $stream, $source, &$comment, $row ) );

		$newParams				=	clone $comment->params();

		$newParams->unsetEntry( 'overrides' );

		$comment->set( 'params', $newParams->asJson() );

		if ( $comment->getError() || ( ! $comment->check() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_REPORT_FAILED_TO_SAVE', 'Comment failed to report! Error: [error]', array( '[error]' => $comment->getError() ) ), 'error' );
		}

		if ( $comment->getError() || ( ! $comment->store() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_REPORT_FAILED_TO_SAVE', 'Comment failed to report! Error: [error]', array( '[error]' => $comment->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterReportStreamComment', array( &$stream, $source, $comment, $row ) );

		if ( $this->params->get( 'reporting_notify', true, GetterInterface::BOOLEAN ) ) {
			$cbUser				=	CBuser::getInstance( $viewer->get( 'id', 0, GetterInterface::INT ), false );

			$extraStrings		=	array(	'comment_id'		=>	$comment->get( 'id', 0, GetterInterface::INT ),
											'comment_message'	=>	$comment->get( 'message', null, GetterInterface::STRING ),
											'comment_url'		=>	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'comments', 'func' => 'show', 'id' => $comment->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ) ),
											'user_url'			=>	$_CB_framework->viewUrl( 'userprofile', true, array( 'user' => $viewer->get( 'user_id', 0, GetterInterface::INT ) ) )
										);

			$subject			=	$cbUser->replaceUserVars( CBTxt::T( 'Comment - Reported!' ), false, true, $extraStrings, false );

			if ( ! $comment->get( 'published', 1, GetterInterface::INT ) ) {
				$message		=	$cbUser->replaceUserVars( CBTxt::T( '<a href="[user_url]">[formatname]</a> reported comment <a href="[comment_url]">[cb:if comment_message!=""][comment_message][cb:else]#[comment_id][/cb:else][/cb:if]</a> as controversial and has now been unpublished!' ), false, true, $extraStrings, false );
			} else {
				$message		=	$cbUser->replaceUserVars( CBTxt::T( '<a href="[user_url]">[formatname]</a> reported comment <a href="[comment_url]">[cb:if comment_message!=""][comment_message][cb:else]#[comment_id][/cb:else][/cb:if]</a> as controversial!' ), false, true, $extraStrings, false );
			}

			$notifications		=	new cbNotification();
			$recipients			=	$stream->get( 'moderators', array(), GetterInterface::RAW );

			if ( $recipients ) {
				cbToArrayOfInt( $recipients );

				foreach ( $recipients as $recipient ) {
					$notifications->sendFromSystem( $recipient, $subject, $message, false, 1 );
				}
			} else {
				$notifications->sendToModerators( $subject, $message, false, 1 );
			}
		}

		CBActivity::ajaxResponse( CBTxt::T( 'This comment has been reported and hidden.' ), 'notice' );
	}

	/**
	 * Pins comment to the top of a stream
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Comments  $stream
	 */
	private function pinComment( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		CBActivity::getTemplate( array( 'comment_container', 'stream_attachments' ), false, false );

		$row		=	$stream->row( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ! Application::MyUser()->isGlobalModerator() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to pin this comment.' ), 'error' );
		} elseif ( $row->get( 'pinned', false, GetterInterface::BOOLEAN ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You have already pinned this comment.' ), 'error' );
		}

		$row->set( 'pinned', 1 );

		$source		=	$row->source();

		$_PLUGINS->trigger( 'activity_onBeforePinStreamComment', array( $stream, $source, &$row ) );

		if ( $row->getError() || ( ! $row->check() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_PIN_FAILED_TO_SAVE', 'Comment failed to pin! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_PIN_FAILED_TO_SAVE', 'Comment failed to pin! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterPinStreamComment', array( $stream, $source, $row ) );

		CBActivity::ajaxResponse( HTML_cbactivityCommentContainer::showCommentContainer( $row, $viewer, $stream, $this, 'save' ), 'html', 'replace', 'container' );
	}

	/**
	 * Unpins comment from the top of a stream
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 * @param Comments  $stream
	 */
	private function unpinComment( $id, $viewer, $stream )
	{
		global $_PLUGINS;

		CBActivity::getTemplate( array( 'comment_container', 'stream_attachments' ), false, false );

		$row		=	$stream->row( $id );

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ! Application::MyUser()->isGlobalModerator() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to unpin this comment.' ), 'error' );
		} elseif ( ! $row->get( 'pinned', false, GetterInterface::BOOLEAN ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You have not pinned this comment.' ), 'error' );
		}

		$row->set( 'pinned', 0 );

		$source		=	$row->source();

		$_PLUGINS->trigger( 'activity_onBeforeUnpinStreamComment', array( $stream, $source, &$row ) );

		if ( $row->getError() || ( ! $row->check() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_UNPIN_FAILED_TO_SAVE', 'Comment failed to unpin! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'COMMENT_UNPIN_FAILED_TO_SAVE', 'Comment failed to unpin! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$_PLUGINS->trigger( 'activity_onAfterUnpinStreamComment', array( $stream, $source, $row ) );

		CBActivity::ajaxResponse( HTML_cbactivityCommentContainer::showCommentContainer( $row, $viewer, $stream, $this, 'save' ), 'html', 'replace', 'container' );
	}

	/**
	 * Displays comments as a button
	 *
	 * @param UserTable $viewer
	 * @param Comments  $stream
	 * @param string    $output
	 */
	private function showCommentsButton( $viewer, $stream, $output = null )
	{
		CBActivity::getTemplate( 'comments_button' );

		HTML_cbactivityCommentsButton::showCommentsButton( $viewer, $stream, $this );
	}

	/**
	 * Displays tags stream
	 *
	 * @param UserTable $viewer
	 * @param Tags      $stream
	 * @param string    $output
	 */
	private function showTags( $viewer, $stream, $output = null )
	{
		global $_CB_framework;

		CBActivity::getTemplate( 'tags', ( $output ? false : true ), ( $output ? false : true ) );

		$tagsPrefix				=	'tags_' . $stream->id() . '_';
		$rowsLimitstart			=	$this->input( $tagsPrefix . 'limitstart', 0, GetterInterface::INT );

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$rowsTotal			=	$stream->rows( 'count' );
		} else {
			$rowsTotal			=	0;
		}

		if ( $rowsTotal <= $rowsLimitstart ) {
			$rowsLimitstart		=	0;
		}

		$pageNav				=	new cbPageNav( $rowsTotal, $rowsLimitstart, 15 );

		$pageNav->setInputNamePrefix( $tagsPrefix );
		$pageNav->setBaseURL( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'tags', 'func' => 'load', 'stream' => $stream->id() ), 'raw', 0, true ) );

		$stream->set( 'paging', true );
		$stream->set( 'paging_first_limit', $pageNav->limit );
		$stream->set( 'paging_limit', $pageNav->limit );
		$stream->set( 'paging_limitstart', $pageNav->limitstart );

		$rows					=	array();

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) && $rowsTotal ) {
			$rows				=	$stream->rows();
		}

		$pageNav->limitstart	=	$stream->get( 'paging_limitstart', 0, GetterInterface::INT );

		HTML_cbactivityTags::showTags( $rows, $pageNav, $viewer, $stream, $this, $output );
	}

	/**
	 * Displays tagged stream
	 *
	 * @param UserTable $viewer
	 * @param Tags      $stream
	 * @param string    $output
	 */
	private function showTagged( $viewer, $stream, $output = null )
	{
		CBActivity::getTemplate( 'tagged' );

		$stream->set( 'paging', true );
		$stream->set( 'paging_first_limit', 3 );
		$stream->set( 'paging_limit', 3 );
		$stream->set( 'paging_limitstart', 0 );

		$rows		=	array();

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$rows	=	$stream->rows();
		}

		HTML_cbactivityTagged::showTagged( $rows, $viewer, $stream, $this, $output );
	}

	/**
	 * Displays tags stream
	 *
	 * @param UserTable $viewer
	 * @param Tags      $stream
	 */
	private function showTagsEdit( $viewer, $stream )
	{
		CBActivity::getTemplate( 'tags_edit' );

		if ( ! CBActivity::canCreate( 'tag', $stream, $viewer ) ) {
			return;
		}

		$rows		=	array();

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$rows	=	$stream->rows();
		}

		HTML_cbactivityTagsEdit::showTagsEdit( $rows, $viewer, $stream, $this );
	}

	/**
	 * Save tags
	 *
	 * @param UserTable $viewer
	 * @param Tags      $stream
	 */
	private function saveTags( $viewer, $stream )
	{
		global $_PLUGINS;

		if ( ! CBActivity::canCreate( 'tag', $stream, $viewer ) ) {
			return;
		}

		$tags				=	$this->input( md5( 'tags_' . $stream->asset() ), array(), GetterInterface::RAW );

		if ( ! $tags ) {
			// Check if for post values if this is a new entry:
			$tags			=	$this->input( md5( 'tags_' . preg_replace( '/\.(\d+)$/', '.0', $stream->asset() ) ), array(), GetterInterface::RAW );
		}

		foreach ( $stream->reset()->rows( 'all' ) as $tag ) {
			/** @var TagTable $tag */
			if ( ! in_array( $tag->get( 'tag', null, GetterInterface::STRING ), $tags ) ) {
				$source		=	$tag->source();

				$_PLUGINS->trigger( 'activity_onBeforeDeleteStreamTag', array( $stream, $source, &$tag ) );

				if ( $tag->getError() || ( ! $tag->canDelete() ) || ( ! $tag->delete() ) ) {
					continue;
				}

				$_PLUGINS->trigger( 'activity_onAfterDeleteStreamTag', array( $stream, $source, $tag ) );
			} else {
				$key		=	array_search( $tag->get( 'tag', null, GetterInterface::STRING ), $tags );

				if ( $key !== false ) {
					unset( $tags[$key] );
				}
			}
		}

		foreach ( $tags as $tagUser ) {
			if ( is_numeric( $tagUser ) ) {
				$tagUser	=	(int) $tagUser;

				if ( ! in_array( $tagUser, CBActivity::getConnections( $stream->user()->get( 'id', 0, GetterInterface::INT ) ) ) ) {
					continue;
				}
			} else {
				$tagUser	=	Get::clean( $tagUser, GetterInterface::STRING );
			}

			$tag			=	new TagTable();

			$tag->set( 'user_id', $stream->user()->get( 'id', 0, GetterInterface::INT ) );
			$tag->set( 'asset', $stream->asset() );
			$tag->set( 'tag', $tagUser );

			$source			=	$tag->source();

			$_PLUGINS->trigger( 'activity_onBeforeCreateStreamTag', array( $stream, $source, &$tag ) );

			if ( $tag->getError() || ( ! $tag->check() ) || ( ! $tag->store() ) ) {
				continue;
			}

			$_PLUGINS->trigger( 'activity_onAfterCreateStreamTag', array( $stream, $source, $tag ) );
		}

		$stream->clear();
	}

	/**
	 * Follow a stream asset
	 *
	 * @param UserTable $viewer
	 * @param Following $stream
	 */
	private function followAsset( $viewer, $stream )
	{
		global $_PLUGINS;

		CBActivity::getTemplate( 'follow', false, false );

		if ( ! CBActivity::canCreate( 'follow', $stream, $viewer ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to follow this stream.' ), 'error' );
		}

		$following			=	false;

		foreach ( $stream->assets( false ) as $asset ) {
			$row			=	new FollowTable();

			$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'asset' => $asset ) );

			if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
				continue;
			}

			$row->set( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ) );
			$row->set( 'asset', $asset );

			$source			=	$row->source();

			$_PLUGINS->trigger( 'activity_onBeforeFollowStream', array( $stream, $source, &$row ) );

			if ( $row->getError() || ( ! $row->check() ) ) {
				continue;
			}

			if ( $row->getError() || ( ! $row->store() ) ) {
				continue;
			}

			$_PLUGINS->trigger( 'activity_onAfterFollowStream', array( $stream, $source, $row ) );

			$following		=	true;
		}

		CBActivity::ajaxResponse( HTML_cbactivityFollow::showFollow( $following, $viewer, $stream, $this, 'save' ), 'html', 'replace', 'container' );
	}

	/**
	 * Unfollow a stream asset
	 *
	 * @param UserTable $viewer
	 * @param Following $stream
	 */
	private function unfollowAsset( $viewer, $stream )
	{
		global $_PLUGINS;

		CBActivity::getTemplate( 'follow', false, false );

		if ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to unfollow this stream.' ), 'error' );
		}

		foreach ( $stream->assets( false ) as $asset ) {
			$row		=	new FollowTable();

			$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'asset' => $asset ) );

			if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
				continue;
			}

			$source		=	$row->source();

			$_PLUGINS->trigger( 'activity_onBeforeUnfollowStream', array( $stream, $source, &$row ) );

			if ( $row->getError() || ( ! $row->canDelete() ) ) {
				continue;
			}

			if ( $row->getError() || ( ! $row->delete() ) ) {
				continue;
			}

			$_PLUGINS->trigger( 'activity_onAfterUnfollowStream', array( $stream, $source, $row ) );
		}

		CBActivity::ajaxResponse( HTML_cbactivityFollow::showFollow( false, $viewer, $stream, $this, 'save' ), 'html', 'replace', 'container' );
	}

	/**
	 * Displays following stream button
	 *
	 * @param UserTable $viewer
	 * @param Following $stream
	 * @param string    $output
	 */
	private function showFollowButton( $viewer, $stream, $output = null )
	{
		CBActivity::getTemplate( 'follow' );

		$following				=	false;

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$rows				=	CBActivity::getFollowing( $viewer->get( 'id', 0, GetterInterface::INT ), true );

			if ( $rows ) {
				$following		=	isset( $rows[$stream->asset()] );
			}
		}

		echo HTML_cbactivityFollow::showFollow( $following, $viewer, $stream, $this, $output );
	}

	/**
	 * Displays following stream
	 *
	 * @param UserTable $viewer
	 * @param Following $stream
	 * @param string    $output
	 */
	private function showFollowing( $viewer, $stream, $output = null )
	{
		global $_CB_framework;

		CBActivity::getTemplate( 'following', ( $output ? false : true ), ( $output ? false : true ) );

		$followingPrefix		=	'following_' . $stream->id() . '_';
		$rowsLimitstart			=	$this->input( $followingPrefix . 'limitstart', 0, GetterInterface::INT );

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$rowsTotal			=	$stream->rows( 'count' );
		} else {
			$rowsTotal			=	0;
		}

		if ( $rowsTotal <= $rowsLimitstart ) {
			$rowsLimitstart		=	0;
		}

		$pageNav				=	new cbPageNav( $rowsTotal, $rowsLimitstart, 15 );

		$pageNav->setInputNamePrefix( $followingPrefix );
		$pageNav->setBaseURL( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'following', 'func' => 'load', 'stream' => $stream->id() ), 'raw', 0, true ) );

		$stream->set( 'paging', true );
		$stream->set( 'paging_first_limit', $pageNav->limit );
		$stream->set( 'paging_limit', $pageNav->limit );
		$stream->set( 'paging_limitstart', $pageNav->limitstart );

		$rows					=	array();

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) && $rowsTotal ) {
			$rows				=	$stream->rows();
		}

		$pageNav->limitstart	=	$stream->get( 'paging_limitstart', 0, GetterInterface::INT );

		HTML_cbactivityFollowing::showFollowing( $rows, $pageNav, $viewer, $stream, $this, $output );
	}

	/**
	 * Follow a stream asset
	 *
	 * @param UserTable $viewer
	 * @param Likes     $stream
	 */
	private function likeAsset( $viewer, $stream )
	{
		global $_PLUGINS;

		CBActivity::getTemplate( array( 'like', 'twemoji' ), false, false );

		if ( ! CBActivity::canCreate( 'like', $stream, $viewer ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to follow this stream.' ), 'error' );
		}

		$liked			=	false;

		foreach ( $stream->assets( false ) as $asset ) {
			$row		=	new LikeTable();

			$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'asset' => $asset ) );

			if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
				continue;
			}

			$types		=	array_keys( CBActivity::loadLikeOptions( true, $stream, $row->get( 'type', 0, GetterInterface::INT ) ) );
			$type		=	$this->input( 'type', ( isset( $types[0] ) ? $types[0] : 0 ), GetterInterface::INT );

			if ( ! in_array( $type, $types ) ) {
				continue;
			}

			$row->set( 'user_id', $viewer->get( 'id', 0, GetterInterface::INT ) );
			$row->set( 'asset', $asset );
			$row->set( 'type', $type );

			$source		=	$row->source();

			$_PLUGINS->trigger( 'activity_onBeforeLikeStream', array( $stream, $source, &$row ) );

			if ( $row->getError() || ( ! $row->check() ) ) {
				continue;
			}

			if ( $row->getError() || ( ! $row->store() ) ) {
				continue;
			}

			$_PLUGINS->trigger( 'activity_onAfterLikeStream', array( $stream, $source, $row ) );

			$liked		=	true;
		}

		CBActivity::ajaxResponse( HTML_cbactivityLike::showLike( $liked, $viewer, $stream, $this, 'save' ), 'html', 'replace', 'container' );
	}

	/**
	 * Unlike a stream asset
	 *
	 * @param UserTable $viewer
	 * @param Likes     $stream
	 */
	private function unlikeAsset( $viewer, $stream )
	{
		global $_PLUGINS;

		CBActivity::getTemplate( array( 'like', 'twemoji' ), false, false );

		if ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) {
			CBActivity::ajaxResponse( CBTxt::T( 'You do not have permission to unlike this stream.' ), 'error' );
		}

		foreach ( $stream->assets( false ) as $asset ) {
			$row		=	new LikeTable();

			$row->load( array( 'user_id' => $viewer->get( 'id', 0, GetterInterface::INT ), 'asset' => $asset ) );

			if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
				continue;
			}

			$source		=	$row->source();

			$_PLUGINS->trigger( 'activity_onBeforeUnlikeStream', array( $stream, $source, &$row ) );

			if ( $row->getError() || ( ! $row->canDelete() ) ) {
				continue;
			}

			if ( $row->getError() || ( ! $row->delete() ) ) {
				continue;
			}

			$_PLUGINS->trigger( 'activity_onAfterUnlikeStream', array( $stream, $source, $row ) );
		}

		CBActivity::ajaxResponse( HTML_cbactivityLike::showLike( false, $viewer, $stream, $this, 'save' ), 'html', 'replace', 'container' );
	}

	/**
	 * Displays likes stream button
	 *
	 * @param UserTable $viewer
	 * @param Likes     $stream
	 * @param string    $output
	 */
	private function showLikeButton( $viewer, $stream, $output = null )
	{
		CBActivity::getTemplate( array( 'like', 'twemoji' ) );

		$liked				=	false;

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$rows			=	CBActivity::getLikes( $viewer->get( 'id', 0, GetterInterface::INT ), true );

			if ( $rows ) {
				$liked		=	isset( $rows[$stream->asset()] );
			}
		}

		echo HTML_cbactivityLike::showLike( $liked, $viewer, $stream, $this, $output );
	}

	/**
	 * Displays likes stream
	 *
	 * @param UserTable $viewer
	 * @param Likes     $stream
	 * @param string    $output
	 */
	private function showLikes( $viewer, $stream, $output = null )
	{
		global $_CB_framework;

		CBActivity::getTemplate( array( 'likes', 'twemoji' ), ( $output ? false : true ), ( $output ? false : true ) );

		$likesPrefix			=	'likes_' . $stream->id() . '_';
		$rowsLimitstart			=	$this->input( $likesPrefix . 'limitstart', 0, GetterInterface::INT );

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$rowsTotal			=	$stream->rows( 'count' );
		} else {
			$rowsTotal			=	0;
		}

		if ( $rowsTotal <= $rowsLimitstart ) {
			$rowsLimitstart		=	0;
		}

		$pageNav				=	new cbPageNav( $rowsTotal, $rowsLimitstart, 15 );

		$pageNav->setInputNamePrefix( $likesPrefix );
		$pageNav->setBaseURL( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'likes', 'func' => 'load', 'stream' => $stream->id() ), 'raw', 0, true ) );

		$stream->set( 'paging', true );
		$stream->set( 'paging_first_limit', $pageNav->limit );
		$stream->set( 'paging_limit', $pageNav->limit );
		$stream->set( 'paging_limitstart', $pageNav->limitstart );

		$rows					=	array();

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) && $rowsTotal ) {
			$rows				=	$stream->rows();
		}

		$pageNav->limitstart	=	$stream->get( 'paging_limitstart', 0, GetterInterface::INT );

		HTML_cbactivityLikes::showLikes( $rows, $pageNav, $viewer, $stream, $this, $output );
	}
}