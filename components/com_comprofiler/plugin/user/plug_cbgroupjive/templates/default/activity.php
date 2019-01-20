<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveActivity
{

	/**
	 * render frontend group activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param null|string                     $title
	 * @param null|string                     $date
	 * @param null|string                     $message
	 * @param null|string                     $insert
	 * @param null|string                     $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param GroupTable                      $group
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static function showActivity( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $group, $plugin, $output )
	{
		global $_CB_framework, $_PLUGINS;

		$type							=	( isset( $assetMatches[2] ) ? $assetMatches[2] : '' );

		if ( ( ! $type ) && ( ! $message ) ) {
			$type						=	'create';
		}

		$user							=	CBuser::getMyUserDataInstance();
		$groupOwner						=	( $user->get( 'id', 0, GetterInterface::INT ) == $group->get( 'user_id', 0, GetterInterface::INT ) );
		$groupName						=	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => $group->get( 'id', 0, GetterInterface::INT ) ) ) . '">' . htmlspecialchars( CBTxt::T( $group->get( 'name', null, GetterInterface::STRING ) ) ) . '</a>';

		if ( $stream instanceof NotificationsInterface ) {
			switch ( $type ) {
				case 'join':
					if ( $groupOwner ) {
						$title			=	CBTxt::T( 'JOINED_YOUR_GROUP', 'joined your group [group]', array( '[group]' => $groupName ) );
					} else {
						$title			=	CBTxt::T( 'JOINED_GROUP', 'joined group [group]', array( '[group]' => $groupName ) );
					}
					break;
				case 'leave':
					if ( $groupOwner ) {
						$title			=	CBTxt::T( 'LEFT_YOUR_GROUP', 'left your group [group]', array( '[group]' => $groupName ) );
					} else {
						$title			=	CBTxt::T( 'LEFT_GROUP', 'left group [group]', array( '[group]' => $groupName ) );
					}
					break;
				case 'create':
					$title				=	CBTxt::T( 'CREATED_GROUP', 'created group [group]', array( '[group]' => $groupName ) );
					break;
				case 'invite':
					if ( $groupOwner ) {
						$title			=	CBTxt::T( 'INVITED_TO_YOUR_GROUP', 'was invited to your group [group]', array( '[group]' => $groupName ) );
					} else {
						if ( $user->get( 'user_id', 0, GetterInterface::INT ) != $row->get( 'user_id', 0, GetterInterface::INT ) ) {
							$title		=	CBTxt::T( 'INVITED_YOU_TO_GROUP', 'invited you to join group [group]', array( '[group]' => $groupName ) );
						} else {
							$title		=	CBTxt::T( 'INVITED_TO_GROUP', 'was invited to group [group]', array( '[group]' => $groupName ) );
						}
					}
					break;
				case 'message':
					$title				=	CBTxt::T( 'MESSAGE_FROM_GROUP', 'message from group [group]', array( '[group]' => $groupName ) );
					break;
				default:
					if ( ! $title ) {
						$title			=	' <span class="fa fa-caret-right"></span> <strong>' . $groupName . '</strong>';
					}
					break;
			}
		} else {
			$viewingGroup				=	( $stream->get( 'groupjive.ingroup', 0, GetterInterface::INT ) == $group->get( 'id', 0, GetterInterface::INT ) );
			$showInsert					=	true;

			switch( $type ) {
				case 'join':
					if ( $viewingGroup ) {
						$title			=	CBTxt::T( 'joined this group' );
					} else {
						$title			=	CBTxt::T( 'joined a group' );
					}
					break;
				case 'leave':
					$message			=	null;

					if ( $viewingGroup ) {
						$title			=	CBTxt::T( 'left this group' );
					} else {
						$title			=	CBTxt::T( 'left a group' );
					}
					break;
				case 'create':
					if ( $viewingGroup ) {
						$title			=	CBTxt::T( 'created this group' );
					} else {
						$title			=	CBTxt::T( 'created a group' );
					}
					break;
				case 'invite':
					if ( $viewingGroup ) {
						$title			=	CBTxt::T( 'was invited to this group' );
					} else {
						$title			=	CBTxt::T( 'was invited to a group' );
					}
					break;
				default:
					if ( ! $viewingGroup ) {
						$title			=	' <span class="fa fa-caret-right"></span> <strong>' . $groupName . '</strong>';
					}

					$showInsert			=	false;
					break;
			}

			if ( ( ! $viewingGroup ) && $showInsert ) {
				initToolTip();

				$isModerator			=	CBGroupJive::isModerator( $user->get( 'id', 0, GetterInterface::INT ) );
				$userStatus				=	CBGroupJive::getGroupStatus( $user, $group );
				$cssClass				=	$group->get( 'css', null, GetterInterface::STRING );

				$insert					=	'<div class="gjActivity gjActivity' . $group->get( 'id', 0, GetterInterface::INT ) . ( $cssClass ? ' ' . htmlspecialchars( $cssClass ) : null ) . '">'
										.		'<div class="gjCanvas cbCanvasLayout border-default">'
										.			'<div class="gjCanvasTop cbCanvasLayoutTop">'
										.				'<div class="gjCanvasBackground cbCanvasLayoutBackground bg-muted">'
										.					$group->canvas()
										.				'</div>'
										.				'<div class="gjCanvasPhoto cbCanvasLayoutPhoto">'
										.					$group->logo( false, true, true, 'cbCanvasLayoutPhotoImage' )
										.				'</div>';

				if ( $isModerator || $groupOwner || ( ( ! $groupOwner ) && ( ( $userStatus === null ) || ( $userStatus === 0 ) || ( $userStatus >= 1 ) ) ) ) {
					$insert				.=				'<div class="gjCanvasButtons cbCanvasLayoutButtons text-right">';

					if ( $isModerator && ( $group->get( 'published', 1, GetterInterface::INT ) == -1 ) && $plugin->params->get( 'groups_create_approval', 0, GetterInterface::INT ) ) {
						$insert			.=					' <span class="gjCanvasButton cbCanvasLayoutButton">'
										.						'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'publish', 'id' => $group->get( 'id', 0, GetterInterface::INT ), 'return' => CBGroupJive::getReturn() ) ) . '\';" class="gjButton gjButtonApprove btn btn-xs btn-success">' . CBTxt::T( 'Approve' ) . '</button>'
										.					'</span>';
					} elseif ( ! $groupOwner ) {
						if ( $userStatus === null ) {
							$insert		.=					' <span class="gjCanvasButton cbCanvasLayoutButton">'
										.						( $group->get( '_invite_id', 0, GetterInterface::INT ) ? '<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to reject all invites to this Group?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'reject', 'id' => $group->get( 'id', 0, GetterInterface::INT ), 'return' => CBGroupJive::getReturn() ) ) ) . '\'; })" class="gjButton gjButtonReject btn btn-xs btn-danger">' . CBTxt::T( 'Reject' ) . '</button> ' : null )
										.						'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'join', 'id' => $group->get( 'id', 0, GetterInterface::INT ), 'return' => CBGroupJive::getReturn() ) ) . '\';" class="gjButton gjButtonJoin btn btn-xs btn-success">' . ( $group->get( '_invite_id', 0, GetterInterface::INT ) ? CBTxt::T( 'Accept Invite' ) : CBTxt::T( 'Join' ) ) . '</button>'
										.					'</span>';
						} elseif ( $userStatus === 0 ) {
							$insert		.=					' <span class="gjCanvasButton cbCanvasLayoutButton">'
										.						'<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel your pending join request to this Group?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'cancel', 'id' => $group->get( 'id', 0, GetterInterface::INT ), 'return' => CBGroupJive::getReturn() ) ) ) . '\'; })" class="gjButton gjButtonCancel btn btn-xs btn-danger">' . CBTxt::T( 'Cancel' ) . '</button> '
										.						'<span class="gjButton gjButtonPending btn btn-xs btn-warning disabled">' . CBTxt::T( 'Pending Approval' ) . '</span>'
										.					'</span>';
						}
					}

					$insert				.=				'</div>';
				}

				$insert					.=			'</div>'
										.			'<div class="gjCanvasBottom cbCanvasLayoutBottom border-default">'
										.				'<div class="gjCanvasTitle cbCanvasLayoutTitle text-primary">'
										.					'<strong><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'show', 'id' => $group->get( 'id', 0, GetterInterface::INT ) ) ) . '">' . htmlspecialchars( CBTxt::T( $group->get( 'name', null, GetterInterface::STRING ) ) ) . '</a></strong>'
										.					( $group->get( 'description', null, GetterInterface::STRING ) ? ' ' . cbTooltip( 1, CBTxt::T( $group->get( 'description', null, GetterInterface::STRING ) ), CBTxt::T( $group->get( 'name', null, GetterInterface::STRING ) ), 400, null, '<span class="fa fa-info-circle text-muted"></span>', null, 'class="gjCanvasDescription small"' ) : null )
										.				'</div>'
										.				'<div class="gjCanvasCounters cbCanvasLayoutCounters text-muted small">';

				if ( $group->get( 'category', 0, GetterInterface::INT ) ) {
					$insert				.=					'<span class="gjCanvasCounter cbCanvasLayoutCounter"><span class="gjGroupCategoryIcon fa-before fa-folder">'
										.						' <a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'categories', 'func' => 'show', 'id' => $group->get( 'category', 0, GetterInterface::INT ) ) ) . '">' . CBTxt::T( $group->category()->get( 'name', null, GetterInterface::STRING ) ) . '</a>'
										.					'</span></span>';
				}

				$insert					.=					' <span class="gjCanvasCounter cbCanvasLayoutCounter"><span class="gjGroupTypeIcon fa-before fa-globe"> ' . $group->type() . '</span></span>'
										.					' <span class="gjCanvasCounter cbCanvasLayoutCounter"><span class="gjGroupUsersIcon fa-before fa-user"> ' . CBTxt::T( 'GROUP_USERS_COUNT', '%%COUNT%% User|%%COUNT%% Users', array( '%%COUNT%%' => $group->get( '_users', 0, GetterInterface::INT ) ) ) . '</span></span>'
										.				'</div>'
										.			'</div>'
										.		'</div>'
										.	'</div>';
			}
		}

		$_PLUGINS->trigger( 'gj_onAfterGroupActivity', array( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $group, $plugin, $output ) );
	}
}