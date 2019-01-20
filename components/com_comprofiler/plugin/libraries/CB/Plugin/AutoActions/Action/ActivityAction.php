<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Action;

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\Comments;
use CB\Plugin\Activity\Following;
use CB\Plugin\Activity\Likes;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Table\HiddenTable;
use CB\Plugin\Activity\Table\FollowTable;
use CB\Plugin\Activity\Table\LikeTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class ActivityAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null|string
	 */
	public function execute( $user )
	{
		global $_CB_database;

		if ( ! $this->installed() ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NOT_INSTALLED', ':: Action [action] :: CB Activity is not installed', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		$return										=	null;

		foreach ( $this->autoaction()->params()->subTree( 'activity' ) as $row ) {
			/** @var ParamsInterface $row */
			$mode									=	$row->get( 'mode', null, GetterInterface::STRING );
			$method									=	$row->get( 'method', 'create', GetterInterface::STRING );
			$owner									=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner								=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$owner								=	(int) $this->string( $user, $owner );
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $owner ) {
				$actionOwner						=	\CBuser::getUserDataInstance( $owner );
			} else {
				$actionOwner						=	$user;
			}

			$userId									=	$row->get( 'user', null, GetterInterface::STRING );

			if ( ! $userId ) {
				$userId								=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$userId								=	(int) $this->string( $actionOwner, $userId );
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $userId ) {
				$actionUser							=	\CBuser::getUserDataInstance( $userId );
			} else {
				$actionUser							=	$user;
			}

			$asset									=	$this->string( $actionOwner, $row->get( 'asset', null, GetterInterface::STRING ) );

			if ( $mode == 'stream' ) {
				$stream								=	null;

				switch ( $row->get( 'stream', 'activity', GetterInterface::STRING ) ) {
					case 'comments':
						if ( ! $asset ) {
							continue 2;
						}

						$commentsStream				=	new Comments( $asset, $actionOwner );

						$commentsStream->set( 'moderators', explode( ',', $this->string( $actionOwner, $row->get( 'moderators', null, GetterInterface::STRING ) ) ) );
						$commentsStream->set( 'autoaction', $this->autoaction()->get( 'id', 0, GetterInterface::INT ) );

						$commentsStream->parse( $row->subTree( 'comments_stream' ), 'comments_' );

						if ( ( ! $commentsStream->rows( 'count' ) ) && ( ! CBActivity::canCreate( 'comment', $commentsStream ) ) ) {
							continue 2;
						}

						if ( in_array( $commentsStream->get( 'layout', 'stream', GetterInterface::STRING ), array( 'button', 'toggle' ) ) ) {
							$stream					=	$commentsStream->comments( 'button' );
						} else {
							$stream					=	$commentsStream->comments();
						}
						break;
					case 'follow':
						if ( ! $asset ) {
							continue 2;
						}

						$followingStream			=	new Following( $asset, $actionOwner );

						$followingStream->set( 'moderators', explode( ',', $this->string( $actionOwner, $row->get( 'moderators', null, GetterInterface::STRING ) ) ) );
						$followingStream->set( 'autoaction', $this->autoaction()->get( 'id', 0, GetterInterface::INT ) );

						$followingStream->parse( $row->subTree( 'following_stream' ), 'following_' );

						if ( ( ! $followingStream->rows( 'count' ) ) && ( ! CBActivity::canCreate( 'follow', $followingStream ) ) ) {
							continue 2;
						}

						if ( $followingStream->get( 'layout', 'button', GetterInterface::STRING ) == 'stream' ) {
							$stream					=	$followingStream->following();
						} else {
							$stream					=	$followingStream->following( 'button' );
						}
						break;
					case 'like':
						if ( ! $asset ) {
							continue 2;
						}

						$likesStream				=	new Likes( $asset, $actionOwner );

						$likesStream->set( 'moderators', explode( ',', $this->string( $actionOwner, $row->get( 'moderators', null, GetterInterface::STRING ) ) ) );
						$likesStream->set( 'autoaction', $this->autoaction()->get( 'id', 0, GetterInterface::INT ) );

						$likesStream->parse( $row->subTree( 'likes_stream' ), 'likes_' );

						if ( ( ! $likesStream->rows( 'count' ) ) && ( ! CBActivity::canCreate( 'like', $likesStream ) ) ) {
							continue 2;
						}

						if ( $likesStream->get( 'layout', 'button', GetterInterface::STRING ) == 'stream' ) {
							$stream					=	$likesStream->likes();
						} else {
							$stream					=	$likesStream->likes( 'button' );
						}
						break;
					case 'notifications':
						if ( ( ! $actionOwner->get( 'id', 0, GetterInterface::INT ) ) || ( $actionOwner->get( 'id', 0, GetterInterface::INT ) != Application::MyUser()->getUserId() ) ) {
							continue 2;
						}

						$notificationsStream		=	new Notifications( $asset, $actionOwner );

						$notificationsStream->set( 'moderators', explode( ',', $this->string( $actionOwner, $row->get( 'moderators', null, GetterInterface::STRING ) ) ) );
						$notificationsStream->set( 'autoaction', $this->autoaction()->get( 'id', 0, GetterInterface::INT ) );

						$notificationsParams		=	$row->subTree( 'notifications_stream' );

						$notificationsStream->parse( $notificationsParams, 'notifications_' );

						$layout						=	$notificationsStream->get( 'layout', 'button', GetterInterface::STRING );

						switch ( $notificationsParams->get( 'notifications_state', 'unread', GetterInterface::STRING ) ) {
							case 'read':
								if ( in_array( $layout, array( 'button', 'toggle' ) ) ) {
									$notificationsStream->set( 'read', 'read' );
								} else {
									$notificationsStream->set( 'read', 'readonly' );
								}
								break;
							case 'unread':
								if ( in_array( $layout, array( 'button', 'toggle' ) ) ) {
									$notificationsStream->set( 'read', 'unread' );
								} else {
									$notificationsStream->set( 'read', 'unreadonly' );
								}
								break;
							case 'all':
								$notificationsStream->set( 'read', 'status' );
								break;
						}

						if ( in_array( $layout, array( 'button', 'toggle' ) ) ) {
							$stream					=	$notificationsStream->notifications( 'button' );
						} else {
							if ( ! $notificationsStream->rows( 'count' ) ) {
								continue 2;
							}

							$stream					=	$notificationsStream->notifications();
						}
						break;
					case 'activity':
					default:
						$activityStream				=	new Activity( $asset, $actionOwner );

						$activityStream->set( 'moderators', explode( ',', $this->string( $actionOwner, $row->get( 'moderators', null, GetterInterface::STRING ) ) ) );
						$activityStream->set( 'autoaction', $this->autoaction()->get( 'id', 0, GetterInterface::INT ) );

						$activityStream->parse( $row->subTree( 'activity_stream' ), 'activity_' );

						if ( ( ! $activityStream->rows( 'count' ) ) && ( ! CBActivity::canCreate( 'activity', $activityStream ) ) ) {
							continue 2;
						}

						if ( in_array( $activityStream->get( 'layout', 'stream', GetterInterface::STRING ), array( 'button', 'toggle' ) ) ) {
							$stream					=	$activityStream->activity( 'button' );
						} else {
							$stream					=	$activityStream->activity();
						}
						break;
				}

				if ( ! $stream ) {
					continue;
				}

				$return							.=	$stream;
			} elseif ( $method == 'delete' ) {
				switch ( $mode ) {
					case 'comment':
						$table						=	'#__comprofiler_plugin_activity_comments';
						$class						=	'\CB\Plugin\Activity\Table\CommentTable';
						break;
					case 'hidden':
						$table						=	'#__comprofiler_plugin_activity_hidden';
						$class						=	'\CB\Plugin\Activity\Table\HiddenTable';
						break;
					case 'follow':
						$table						=	'#__comprofiler_plugin_activity_following';
						$class						=	'\CB\Plugin\Activity\Table\FollowTable';
						break;
					case 'like':
						$table						=	'#__comprofiler_plugin_activity_likes';
						$class						=	'\CB\Plugin\Activity\Table\LikeTable';
						break;
					case 'tag':
						$table						=	'#__comprofiler_plugin_activity_tags';
						$class						=	'\CB\Plugin\Activity\Table\TagTable';
						break;
					case 'notification':
						$table						=	'#__comprofiler_plugin_activity_notifications';
						$class						=	'\CB\Plugin\Activity\Table\NotificationTable';
						break;
					case 'activity':
					default:
						$table						=	'#__comprofiler_plugin_activity';
						$class						=	'\CB\Plugin\Activity\Table\ActivityTable';

						if ( ! $asset ) {
							$asset					=	'profile.' . $actionOwner->get( 'id', 0, GetterInterface::INT );
						}
						break;
				}

				$where								=	array();

				if ( $mode == 'hidden' ) {
					$item							=	$this->string( $actionOwner, $row->get( 'item', null, GetterInterface::STRING ) );

					if ( ! $item ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NO_ITEM', ':: Action [action] :: CB Activity skipped due to missing item', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$where[]						=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionOwner->get( 'id', 0, GetterInterface::INT );
					$where[]						=	$_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( $row->get( 'type', 'activity', GetterInterface::STRING ) );
					$where[]						=	$_CB_database->NameQuote( 'item' ) . ( strpos( $item, '%' ) !== false ? ' LIKE ' : ' = ' ) . $_CB_database->Quote( $item );
				} else {
					$assetsWhere					=	array();

					if ( $asset ) {
						$queryAssets				=	array();
						$queryWildcards				=	array();

						foreach ( explode( ',', $asset ) as $queryAsset ) {
							if ( ( strpos( $queryAsset, '%' ) !== false ) || ( strpos( $queryAsset, '_' ) !== false ) ) {
								$queryWildcards[]	=	$queryAsset;
							} else {
								$queryAssets[]		=	$queryAsset;
							}
						}

						$queryAssets				=	array_unique( $queryAssets );
						$queryWildcards				=	array_unique( $queryWildcards );

						if ( $queryAssets ) {
							$assetsWhere[]			=	$_CB_database->NameQuote( 'asset' ) . ( count( $queryAssets ) > 1 ? " IN " . $_CB_database->safeArrayOfStrings( $queryAssets ) : " = " . $_CB_database->Quote( $queryAssets[0] ) );
						}

						if ( $queryWildcards ) {
							foreach ( $queryWildcards as $queryWildcard ) {
								$assetsWhere[]		=	$_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( $queryWildcard );
							}
						}
					}

					switch ( $row->get( 'delete_by', 'asset', GetterInterface::STRING ) ) {
						case 'owner':
							$where[]				=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionOwner->get( 'id', 0, GetterInterface::INT );
							break;
						case 'user':
							if ( $mode != 'notification' ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_WRONG_MODE', ':: Action [action] :: CB Activity skipped due to wrong mode', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							}

							$where[]				=	$_CB_database->NameQuote( 'user' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT );
							break;
						case 'owner_user':
							if ( $mode != 'notification' ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_WRONG_MODE', ':: Action [action] :: CB Activity skipped due to wrong mode', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							}

							$where[]				=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionOwner->get( 'id', 0, GetterInterface::INT );
							$where[]				=	$_CB_database->NameQuote( 'user' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT );
							break;
						case 'asset_user':
							if ( ! $asset ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NO_ASSET', ':: Action [action] :: CB Activity skipped due to missing asset', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							} elseif ( $mode != 'notification' ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_WRONG_MODE', ':: Action [action] :: CB Activity skipped due to wrong mode', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							}

							$where[]				=	$_CB_database->NameQuote( 'user' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT );
							$where[]				=	( count( $assetsWhere ) > 1 ? "( " . implode( " OR ", $assetsWhere ) . " )" : $assetsWhere[0] );
							break;
						case 'asset_owner_user':
							if ( ! $asset ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NO_ASSET', ':: Action [action] :: CB Activity skipped due to missing asset', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							} elseif ( $mode != 'notification' ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_WRONG_MODE', ':: Action [action] :: CB Activity skipped due to wrong mode', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							}

							$where[]				=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionOwner->get( 'id', 0, GetterInterface::INT );
							$where[]				=	$_CB_database->NameQuote( 'user' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT );
							$where[]				=	( count( $assetsWhere ) > 1 ? "( " . implode( " OR ", $assetsWhere ) . " )" : $assetsWhere[0] );
							break;
						case 'asset_owner':
							if ( ! $asset ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NO_ASSET', ':: Action [action] :: CB Activity skipped due to missing asset', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							}

							$where[]				=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionOwner->get( 'id', 0, GetterInterface::INT );
							$where[]				=	( count( $assetsWhere ) > 1 ? "( " . implode( " OR ", $assetsWhere ) . " )" : $assetsWhere[0] );
							break;
						case 'asset':
						default:
							if ( ! $asset ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NO_ASSET', ':: Action [action] :: CB Activity skipped due to missing asset', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							}

							$where[]				=	( count( $assetsWhere ) > 1 ? "( " . implode( " OR ", $assetsWhere ) . " )" : $assetsWhere[0] );
							break;
					}
				}

				$query								=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( $table )
													.	"\n WHERE " . implode( "\n AND ", $where );
				$_CB_database->setQuery( $query );
				$objects							=	$_CB_database->loadObjectList( null, $class, array( $_CB_database ) );

				/** @var ActivityTable[]|NotificationTable[]|CommentTable[]|HiddenTable[]|FollowTable[]|LikeTable[]|TagTable[] $objects */
				foreach ( $objects as $object ) {
					$object->delete();
				}
			} else {
				if ( ! $actionOwner->get( 'id', 0, GetterInterface::INT ) ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NO_OWNER', ':: Action [action] :: CB Activity skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
					continue;
				}

				switch ( $mode ) {
					case 'comment':
						$object						=	new CommentTable();
						break;
					case 'hidden':
						$object						=	new HiddenTable();
						break;
					case 'follow':
						$object						=	new FollowTable();
						break;
					case 'like':
						$object						=	new LikeTable();
						break;
					case 'tag':
						$object						=	new TagTable();
						break;
					case 'notification':
						$object						=	new NotificationTable();
						break;
					case 'activity':
					default:
						$object						=	new ActivityTable();
						break;
				}

				if ( $mode == 'hidden' ) {
					$item							=	$this->string( $actionOwner, $row->get( 'item', null, GetterInterface::STRING ) );

					if ( ! $item ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NO_ITEM', ':: Action [action] :: CB Activity skipped due to missing item', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$object->load( array( 'user_id' => $actionOwner->get( 'id', 0, GetterInterface::INT ), 'type' => $row->get( 'type', 'activity', GetterInterface::STRING ), 'item' => $item ) );

					$object->set( 'item', $item );
				} else {
					if ( ! $asset ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NO_ASSET', ':: Action [action] :: CB Activity skipped due to missing asset', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					switch ( $row->get( 'create_by', 'asset', GetterInterface::STRING ) ) {
						case 'owner':
							$object->load( array( 'user_id' => $actionOwner->get( 'id', 0, GetterInterface::INT ) ) );
							break;
						case 'user':
							if ( $mode != 'notification' ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_WRONG_MODE', ':: Action [action] :: CB Activity skipped due to wrong mode', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							}

							$object->load( array( 'user' => $actionUser->get( 'id', 0, GetterInterface::INT ) ) );
							break;
						case 'owner_user':
							if ( $mode != 'notification' ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_WRONG_MODE', ':: Action [action] :: CB Activity skipped due to wrong mode', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							}

							$object->load( array( 'user_id' => $actionOwner->get( 'id', 0, GetterInterface::INT ), 'user' => $actionUser->get( 'id', 0, GetterInterface::INT ) ) );
							break;
						case 'asset_user':
							if ( $mode != 'notification' ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_WRONG_MODE', ':: Action [action] :: CB Activity skipped due to wrong mode', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							}

							$object->load( array( 'user' => $actionUser->get( 'id', 0, GetterInterface::INT ), 'asset' => $asset ) );
							break;
						case 'asset_owner_user':
							if ( $mode != 'notification' ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_WRONG_MODE', ':: Action [action] :: CB Activity skipped due to wrong mode', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
								continue 2;
							}

							$object->load( array( 'user_id' => $actionOwner->get( 'id', 0, GetterInterface::INT ), 'user' => $actionUser->get( 'id', 0, GetterInterface::INT ), 'asset' => $asset ) );
							break;
						case 'asset_owner':
							$object->load( array( 'user_id' => $actionOwner->get( 'id', 0, GetterInterface::INT ), 'asset' => $asset ) );
							break;
						case 'asset':
							$object->load( array( 'asset' => $asset ) );
							break;
					}

					$object->set( 'asset', $asset );

					if ( in_array( $mode, array( 'activity', 'notification' ) ) ) {
						$title						=	$this->string( $actionOwner, $row->get( 'title', null, GetterInterface::RAW ), false );

						if ( $title ) {
							$object->set( 'title', $title );
						}
					} elseif ( $mode == 'like' ) {
						$object->set( 'type', $row->get( 'type', 0, GetterInterface::INT ) );
					} elseif ( $mode == 'tag' ) {
						$tag						=	$this->string( $actionOwner, $row->get( 'tag', null, GetterInterface::STRING ) );

						if ( ! $tag ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NO_TAG', ':: Action [action] :: CB Activity skipped due to missing tag', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						$object->set( 'tag', $tag );
					}

					if ( in_array( $mode, array( 'activity', 'notification', 'comment' ) ) ) {
						$message					=	$this->string( $actionOwner, $row->get( 'message', null, GetterInterface::STRING ), false );

						if ( $message ) {
							// Remove duplicate spaces:
							$message				=	preg_replace( '/ {2,}/i', ' ', $message );
							// Remove duplicate tabs:
							$message				=	preg_replace( '/\t{2,}/i', "\t", $message );
							// Remove duplicate linebreaks:
							$message				=	preg_replace( '/(\r\n|\r|\n){2,}/i', '$1', $message );

							$object->set( 'message', $message );
						}

						if ( in_array( $mode, array( 'activity', 'comment' ) ) ) {
							$action					=	$row->subTree( 'action' );
							$actionId				=	$action->get( 'id', 0, GetterInterface::INT );

							if ( $actionId ) {
								$actionMessage		=	$this->string( $actionOwner, $action->get( 'message', null, GetterInterface::STRING ), false );

								if ( $actionMessage ) {
									// Remove linebreaks:
									$actionMessage	=	str_replace( array( "\n", "\r\n" ), ' ', $actionMessage );
									// Remove duplicate spaces:
									$actionMessage	=	preg_replace( '/ {2,}/i', ' ', $actionMessage );
									// Remove duplicate tabs:
									$actionMessage	=	preg_replace( '/\t{2,}/i', "\t", $actionMessage );

									$newAction		=	array(	'id'		=>	$actionId,
																'message'	=>	$actionMessage,
																'emote'		=>	$action->get( 'emote', 0, GetterInterface::INT )
															);

									$object->params()->set( 'action', $newAction );
								}
							}

							$location				=	$row->subTree( 'location' );
							$locationId				=	$location->get( 'id', 0, GetterInterface::INT );

							if ( $locationId ) {
								$locationPlace		=	$this->string( $actionOwner, $location->get( 'place', null, GetterInterface::STRING ), false );

								if ( $locationPlace ) {
									$newLocation	=	array(	'id'		=>	$locationId,
																'place'		=>	$locationPlace,
																'address'	=>	$this->string( $actionOwner, $location->get( 'address', null, GetterInterface::STRING ), false )
															);

									$object->params()->set( 'location', $newLocation );
								}
							}
						}

						$newLinks					=	array();

						foreach ( $row->subTree( 'links' ) as $link ) {
							/** @var ParamsInterface $link */
							$linkType				=	$link->get( 'type', null, GetterInterface::STRING );
							$linkUrl				=	$this->string( $actionOwner, $link->get( 'url', null, GetterInterface::STRING ), false );

							if ( ( ! $linkType ) || ( ( $linkType != 'custom' ) && ( ! $linkUrl ) ) ) {
								continue;
							}

							$linkMedia				=	$link->subTree( 'media' );
							$linkMediaURL			=	$this->string( $actionOwner, $linkMedia->get( 'url', null, GetterInterface::STRING ), false );
							$linkMediaCustom		=	$this->string( $actionOwner, $linkMedia->get( 'custom', null, GetterInterface::RAW ), false );

							if ( ( $linkType == 'custom' ) && ( ! $linkMediaCustom ) ) {
								continue;
							}

							$linkDate				=	$this->string( $actionOwner, $link->get( 'date', null, GetterInterface::STRING ) );

							if ( $linkDate ) {
								try {
									$linkDate		=	Application::Date( $linkDate, 'UTC' )->getTimestamp();
								} catch ( \Exception $e ) {
									$linkDate		=	null;
								}
							} else {
								$linkDate			=	Application::Database()->getUtcDateTime();
							}

							$newLinks[]				=	array(	'type'			=>	$linkType,
																'url'			=>	trim( $linkUrl ),
																'title'			=>	trim( $this->string( $actionOwner, $link->get( 'title', null, GetterInterface::STRING ), false ) ),
																'description'	=>	trim( $this->string( $actionOwner, $link->get( 'description', null, GetterInterface::STRING ), false ) ),
																'media'			=>	array(	'type'		=>	$linkType,
																							'url'		=>	$linkMediaURL,
																							'filename'	=>	$this->string( $actionOwner, $linkMedia->get( 'filename', null, GetterInterface::STRING ) ),
																							'mimetype'	=>	$this->string( $actionOwner, $linkMedia->get( 'mimetype', null, GetterInterface::STRING ) ),
																							'extension'	=>	$this->string( $actionOwner, $linkMedia->get( 'extension', null, GetterInterface::STRING ) ),
																							'filesize'	=>	(int) $this->string( $actionOwner, $linkMedia->get( 'filesize', null, GetterInterface::STRING ) ),
																							'custom'	=>	$linkMediaCustom,
																							'internal'	=>	\JUri::isInternal( $linkMediaURL )
																						),
																'thumbnails'	=>	array(),
																'selected'		=>	0,
																'thumbnail'		=>	$link->get( 'thumbnail', true, GetterInterface::BOOLEAN ),
																'internal'		=>	\JUri::isInternal( $linkUrl ),
																'date'			=>	$linkDate,
																'embedded'		=>	false
															);
						}

						if ( $newLinks ) {
							$object->params()->set( 'links', $newLinks );
						}

						if ( $mode == 'activity' ) {
							$row->set( 'defaults.tags_asset', $this->string( $actionOwner, $row->get( 'defaults.tags_asset', null, GetterInterface::STRING ) ) );
							$row->set( 'defaults.tags_user', $this->string( $actionOwner, $row->get( 'defaults.tags_user', null, GetterInterface::STRING ) ) );

							$row->set( 'defaults.likes_asset', $this->string( $actionOwner, $row->get( 'defaults.likes_asset', null, GetterInterface::STRING ) ) );
							$row->set( 'defaults.likes_user', $this->string( $actionOwner, $row->get( 'defaults.likes_user', null, GetterInterface::STRING ) ) );

							$row->set( 'defaults.comments_asset', $this->string( $actionOwner, $row->get( 'defaults.comments_asset', null, GetterInterface::STRING ) ) );
							$row->set( 'defaults.comments_user', $this->string( $actionOwner, $row->get( 'defaults.comments_user', null, GetterInterface::STRING ) ) );

							$object->params()->set( 'defaults', $row->subTree( 'defaults' )->asArray() );
						}

						$object->set( 'params', $object->params()->asJson() );
					}

					$date							=	$this->string( $actionOwner, $row->get( 'date', null, GetterInterface::STRING ) );

					if ( $date ) {
						try {
							$date					=	Application::Date( $date, 'UTC' )->format( 'Y-m-d H:i:s' );
						} catch ( \Exception $e ) {
							$date					=	null;
						}
					}

					if ( $date ) {
						$object->set( 'date', $date );
					}

					$published						=	$row->get( 'published', null, GetterInterface::STRING );

					if ( ( $published === null ) || ( $published === '' ) ) {
						$object->set( 'published', 1 );
					} else {
						$object->set( 'published', ( (int) $this->string( $actionOwner, $published ) ? 1 : 0 ) );
					}

					$pinned							=	$row->get( 'pinned', null, GetterInterface::STRING );

					if ( ( $pinned === null ) || ( $pinned === '' ) ) {
						$object->set( 'pinned', 0 );
					} else {
						$object->set( 'pinned', ( (int) $this->string( $actionOwner, $pinned ) ? 1 : 0 ) );
					}
				}

				$object->set( 'user_id', $actionOwner->get( 'id', 0, GetterInterface::INT ) );

				if ( $mode == 'notification' ) {
					$object->set( 'user', $actionUser->get( 'id', 0, GetterInterface::INT ) );
				}

				if ( ! $object->store() ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_ACTIVITY_CREATE_FAILED', ':: Action [action] :: CB Activity failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $object->getError() ) ) );
				}
			}
		}

		return $return;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' );

		if ( ! $plugin ) {
			return false;
		}

		$pluginVersion		=	str_replace( '+build.', '+', $_PLUGINS->getPluginVersion( $plugin, true ) );

		if ( version_compare( $pluginVersion, '4.1.0', '>=' ) && version_compare( $pluginVersion, '5.0.0', '<' ) ) {
			return true;
		}

		return false;
	}
}