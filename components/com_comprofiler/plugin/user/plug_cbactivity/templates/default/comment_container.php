<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;
use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Comments;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityCommentContainer
{

	/**
	 * @param CommentTable    $row
	 * @param UserTable       $viewer
	 * @param Comments        $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 * @return string
	 */
	static public function showCommentContainer( $row, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$canModerate					=	CBActivity::canModerate( $stream );
		$rowId							=	md5( $stream->id() . '_' . $row->get( 'id', 0, GetterInterface::INT ) );
		$rowOwner						=	( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user_id', 0, GetterInterface::INT ) );
		$rowPinned						=	false;
		$rowReported					=	false;

		$pinnedTooltip					=	cbTooltip( null, CBTxt::T( 'Pinned' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$reportedTooltip				=	cbTooltip( null, CBTxt::T( 'Controversial' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );

		$cbUser							=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false );
		$message						=	$row->get( 'message', null, GetterInterface::STRING );
		$date							=	null;
		$insert							=	null;
		$footer							=	null;
		$menu							=	array();

		$integrations					=	implode( '', $_PLUGINS->trigger( 'activity_onDisplayStreamComment', array( &$row, &$message, &$insert, &$date, &$footer, &$menu, $stream, $output ) ) );

		if ( $integrations ) {
			$footer						=	$integrations . $footer;
		}

		$showMenu						=	CBActivity::findParamOverrde( $row, 'menu', true );

		if ( CBActivity::findParamOverrde( $row, 'pinned', true ) !== false ) {
			$rowPinned					=	( $stream->get( 'pinned', true, GetterInterface::BOOLEAN ) && $row->get( 'pinned', false, GetterInterface::BOOLEAN ) );
		}

		if ( $row->params()->get( 'reports', 0, GetterInterface::INT ) ) {
			$rowReported				=	true;
		}

		$canEdit						=	CBActivity::findParamOverrde( $row, 'edit', true );

		if ( $canEdit !== false ) {
			$canEdit					=	( $rowOwner || $canModerate );
		}

		$showActions					=	CBActivity::findParamOverrde( $row, 'actions', false, $stream );
		$showLocations					=	CBActivity::findParamOverrde( $row, 'locations', false, $stream );
		$showLinks						=	CBActivity::findParamOverrde( $row, 'links', false, $stream );
		$showTags						=	CBActivity::findParamOverrde( $row, 'tags', false, $stream );
		$showLikes						=	CBActivity::findParamOverrde( $row, 'likes', false, $stream );
		$showReplies					=	CBActivity::findParamOverrde( $row, 'replies', false, $stream );

		if ( $row->get( '_hidden', null, GetterInterface::STRING ) ) {
			$canEdit					=	false;
			$showReplies				=	false;
		}

		$message						=	$stream->parser( $message )->parse( array( 'linebreaks' ) );
		$action							=	( $showActions ? $stream->parser( $row->action() )->parse( array( 'linebreaks' ) ) : null );
		$location						=	( $showLocations ? $row->location() : null );
		$tags							=	null;

		if ( $showTags ) {
			$tagsStream					=	$row->tags( $stream );

			if ( $row->get( '_tags', null, GetterInterface::BOOLEAN ) === false ) {
				$tagsStream->set( 'query', false );
			} else {
				$tags					=	$tagsStream->tags( 'tagged' );
			}

			if ( $tags ) {
				$tags					=	CBTxt::T( 'COMMENT_TAGS', 'with [tags]', array( '[tags]' => $tags ) );
			}
		}

		if ( $action || $location || $tags ) {
			$subContent					=	( $action ? $action : null )
										.	( $location ? ( $action ? ' ' : null ) . $location : null )
										.	( $tags ? ( $action || $location ? ' ' : null ) . $tags : null );

			$message					.=	' <span class="streamItemSubContent">&mdash; ' . $subContent . '</span>';
		}

		if ( $showLikes ) {
			$likesStream				=	$row->likes( $stream );

			if ( $row->get( '_likes', null, GetterInterface::BOOLEAN ) === false ) {
				$likesStream->set( 'query', false );
			}

			$like						=	$likesStream->likes( 'button' );

			if ( $like ) {
				$date					.=	( $date ? ' ' : null ) . '&nbsp;-&nbsp; ' . $like;
			}
		}

		if ( $showReplies ) {
			if ( CBActivity::canCreate( 'reply', $stream ) ) {
				$date					.=	( $date ? ' ' : null ) . '<span class="streamToggle streamToggleReplies" data-cbactivity-toggle-target=".commentContainerNew" data-cbactivity-toggle-close="false" data-cbactivity-toggle-filter="false" data-cbactivity-toggle-active-classes="hidden">&nbsp;-&nbsp; <a href="javascript: void(0);">' . CBTxt::T( 'Reply' ) . '</a></span>';
			}

			$repliesStream				=	$row->replies( $stream );

			if ( $row->get( '_comments', null, GetterInterface::BOOLEAN ) === false ) {
				$repliesStream->set( 'query', false );
			}

			$replies					=	$repliesStream->comments();

			if ( $replies ) {
				if ( $footer ) {
					$footer				.=	'<div class="streamPanelFooterDivider border-default"></div>';
				}

				$footer					.=	$replies;
			}
		}

		$modified						=	$row->params()->get( 'modified', null, GetterInterface::STRING );

		if ( $modified ) {
			$modified					=	' ' . cbTooltip( null, cbFormatDate( $modified ), null, 'auto', null, '<span class="fa fa-edit"></span>', null, 'class="streamIconEdited" data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		}

		$return							=	'<div class="streamItem commentContainer' . ( $rowPinned ? ' streamItemPinned' : null ) . ( $stream->get( 'inline', false, GetterInterface::BOOLEAN ) ? ' streamItemInline' : ' streamPanel panel ' . ( $rowPinned ? 'panel-warning' : 'panel-default' ) ) . ' streamItem' . $rowId . '" data-cbactivity-id="' . $row->get( 'id', 0, GetterInterface::INT ) . '">'
										.		'<div class="streamItemInner">'
										.			'<div class="streamMedia commentContainerHeader media' . ( ! $stream->get( 'inline', false, GetterInterface::BOOLEAN ) ? ' streamPanelHeading panel-heading' : null ) . ' clearfix">'
										.				'<div class="streamMediaLeft commentContainerLogo media-left">'
										.					$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
										.				'</div>'
										.				'<div class="streamMediaBody streamItemDisplay commentContainerContent media-body">'
										.					'<div class="commentContainerContentInner cbMoreLess text-small" data-cbmoreless-height="50">'
										.						'<div class="streamItemContent cbMoreLessContent clearfix">'
										.							'<strong>' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</strong>'
										.							( $message ? ' ' . $message : null )
										.						'</div>'
										.						'<div class="cbMoreLessOpen fade-edge hidden">'
										.							'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
										.						'</div>'
										.					'</div>'
										.					( $insert ? '<div class="commentContainerContentInsert clearfix">' . $insert . '</div>' : null )
										.					'<div class="commentContainerContentDate text-muted text-small clearfix">'
										.						cbTooltip( null, cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ) ), null, 'auto', null, cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, 'timeago' ), null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' )
										.						$modified
										.						( $date ? ' ' . $date : null )
										.					'</div>';

		if ( $showLinks && $row->attachments()->count() ) {
			$return						.=					'<div class="streamItemDisplay commentContainerAttachments">'
										.						'<div class="commentContainerAttachmentsInner clearfix">'
										.							HTML_cbactivityStreamAttachments::showAttachments( $row, $viewer, $stream, $plugin, $output )
										.						'</div>'
										.					'</div>';
		}

		$return							.=					( $footer ? '<div class="commentContainerContentFooter clearfix">' . $footer . '</div>' : null )
										.				'</div>'
										.			'</div>';

		if ( $showMenu && ( $canModerate || $rowOwner || ( $viewer->get( 'id', 0, GetterInterface::INT ) && ( ! $rowOwner ) ) || $menu ) ) {
			$menuItems					=	'<ul class="streamItemMenuItems commentMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

			if ( $viewer->get( 'id', 0, GetterInterface::INT ) && ( ! $rowOwner ) ) {
				$hideUser				=	$cbUser->getField( 'formatname', null, 'html', 'none', 'profile', 0, true );

				if ( $row->get( '_hidden', null, GetterInterface::STRING ) ) {
					switch ( $row->get( '_hidden', null, GetterInterface::STRING ) ) {
						case 'comment.user':
							$menuItems	.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'unhide', 'type' => 'user', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentMenuItemUnhide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-unlock-alt"></span> ' . CBTxt::T( 'UNHIDE_COMMENTS_FROM_USER', 'Unhide Comments from <span class="text-info">[user]</span>', array( '[user]' => $hideUser ) ) . '</a></li>';
							break;
						case 'comment.asset':
							$menuItems	.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'unhide', 'type' => 'asset', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentMenuItemUnhide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-unlock-alt"></span> ' . CBTxt::T( 'Unhide similar Comments' ) . '</a></li>';
							break;
						case 'comment':
						default:
							$menuItems	.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'unhide', 'type' => 'comment', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentMenuItemUnhide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-unlock-alt"></span> ' . CBTxt::T( 'Unhide this Comment' ) . '</a></li>';
							break;
					}
				} else {
					$menuItems			.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'hide', 'type' => 'user', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentMenuItemHide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'ARE_YOU_SURE_HIDE_COMMENTS_FROM_USER', 'Are you sure you want to hide all Comments from <span class="text-info">[user]</span>?', array( '[user]' => $hideUser ) ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'HIDE_COMMENTS_FROM_USER_BUTTON', 'Hide Comments from <strong>[user]</strong>', array( '[user]' => $hideUser ) ) ) . '"><span class="fa fa-lock"></span> ' . CBTxt::T( 'HIDE_COMMENTS_FROM_USER', 'Hide Comments from <span class="text-info">[user]</span>', array( '[user]' => $hideUser ) ) . '</a></li>'
										.		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'hide', 'type' => 'asset', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentMenuItemHide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to hide all Comments similar to this Comment?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Hide similar Comments' ) ) . '"><span class="fa fa-lock"></span> ' . CBTxt::T( 'Hide similar Comments' ) . '</a></li>'
										.		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'hide', 'type' => 'comment', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentMenuItemHide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to hide this Comment?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Hide this Comment' ) ) . '"><span class="fa fa-lock"></span> ' . CBTxt::T( 'Hide this Comment' ) . '</a></li>';
				}

				if ( $canModerate || $rowOwner || $menu ) {
					$menuItems			.=		'<li class="streamItemMenuItem commentMenuItem divider"></li>';
				}
			}

			if ( $canModerate || $rowOwner ) {
				if ( $stream->get( 'pinned', true, GetterInterface::BOOLEAN ) && Application::MyUser()->isGlobalModerator() && ( ! $row->get( '_hidden', null, GetterInterface::STRING ) ) ) {
					if ( $rowPinned ) {
						$menuItems		.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'unpin', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentMenuItemEdit streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-star-o"></span> ' . CBTxt::T( 'Unpin this Comment' ) . '</a></li>';
					} else {
						$menuItems		.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'pin', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentMenuItemEdit streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-star"></span> ' . CBTxt::T( 'Pin this Comment' ) . '</a></li>';
					}
				}

				if ( $canEdit ) {
					$menuItems			.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentMenuItemEdit streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';
				}

				$menuItems				.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentMenuItemDelete streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to delete this Comment?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Delete Comment' ) ) . '"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
			}

			if ( $plugin->params->get( 'reporting', true, GetterInterface::BOOLEAN ) && $viewer->get( 'id', 0, GetterInterface::INT ) && ( ! $rowOwner ) && ( ! $canModerate ) && ( ! CBActivity::canModerate( $stream, $row->get( 'user_id', 0, GetterInterface::INT ) ) ) && ( ! $row->get( '_hidden', null, GetterInterface::STRING ) ) ) {
				$menuItems				.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'report', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentMenuItemReport streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to report and hide this Comment?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Report and Hide this Comment' ) ) . '"><span class="fa fa-exclamation-circle"></span> ' . CBTxt::T( 'Report this Comment' ) . '</a></li>';
			}

			if ( $menu ) {
				$menuItems				.=		'<li class="streamItemMenuItem commentMenuItem">' . implode( '</li><li class="streamItemMenuItem commentMenuItem">', $menu ) . '</li>';
			}

			$menuItems					.=	'</ul>';

			$menuAttr					=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="fa fa-chevron-down text-muted" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle" data-cbtooltip-open-classes="open"' );

			$return						.=			'<div class="streamItemMenu commentContainerMenu text-small">'
										.				( $rowPinned ? '<span class="streamItemPinnedIcon fa fa-star text-warning"' . $pinnedTooltip . '></span> ' : null )
										.				( $rowReported ? '<span class="streamItemReportedIcon fa fa-warning text-danger"' . $reportedTooltip . '></span> ' : null )
										.				'<span ' . trim( $menuAttr ) . '></span>'
										.			'</div>';
		} elseif ( $rowPinned || $rowReported ) {
			$return						.=			'<div class="streamItemMenu commentContainerMenu hidden-xs">'
										.				( $rowPinned ? '<span class="streamItemPinnedIcon fa fa-star text-warning"' . $pinnedTooltip . '></span>' . ( $rowReported ? ' ' : null ) : null )
										.				( $rowReported ? '<span class="streamItemReportedIcon fa fa-warning text-danger"' . $reportedTooltip . '></span>' : null )
										.			'</div>';
		}

		$return							.=		'</div>'
										.		( $output == 'save' ? CBActivity::reloadHeaders() : null )
										.	'</div>';

		return $return;
	}
}