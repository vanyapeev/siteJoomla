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
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityActivityContainer
{

	/**
	 * @param ActivityTable   $row
	 * @param UserTable       $viewer
	 * @param Activity        $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 * @return string
	 */
	static public function showActivityContainer( $row, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$canModerate						=	CBActivity::canModerate( $stream );
		$rowId								=	md5( $stream->id() . '_' . $row->get( 'id', 0, GetterInterface::INT ) );
		$rowOwner							=	( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user_id', 0, GetterInterface::INT ) );
		$rowAsset							=	$row->get( 'asset', null, GetterInterface::STRING );
		$rowGlobal							=	false;
		$rowPinned							=	false;
		$rowFollowing						=	false;
		$rowReported						=	false;
		$rowRead							=	true;

		$pinnedTooltip						=	cbTooltip( null, CBTxt::T( 'Pinned' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$globalTooltip						=	cbTooltip( null, CBTxt::T( 'Announcement' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$reportedTooltip					=	cbTooltip( null, CBTxt::T( 'Controversial' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );

		$cbUser								=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false );
		$title								=	$row->get( 'title', null, GetterInterface::STRING );
		$message							=	$row->get( 'message', null, GetterInterface::STRING );
		$date								=	null;
		$insert								=	null;
		$footer								=	null;
		$menu								=	array();

		HTML_cbactivityActivityCore::parseDisplay( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $plugin, $output );

		$integrations						=	implode( '', $_PLUGINS->trigger( 'activity_onDisplayStreamActivity', array( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $output ) ) );

		if ( $integrations ) {
			$footer							=	$integrations . $footer;
		}

		$showMenu							=	CBActivity::findParamOverrde( $row, 'menu', true );

		if ( CBActivity::findParamOverrde( $row, 'global', true ) !== false ) {
			$rowGlobal						=	( $row->get( 'asset', null, GetterInterface::STRING ) == 'global' );
		}

		if ( CBActivity::findParamOverrde( $row, 'pinned', true ) !== false ) {
			$rowPinned						=	( $stream->get( 'pinned', true, GetterInterface::BOOLEAN ) && $row->get( 'pinned', false, GetterInterface::BOOLEAN ) );
		}

		if ( $row->params()->get( 'reports', 0, GetterInterface::INT ) ) {
			$rowReported					=	true;
		}

		if ( CBActivity::findParamOverrde( $row, 'following', true ) !== false ) {
			foreach ( $stream->assets() as $asset ) {
				if ( strpos( $asset, 'following' ) !== false ) {
					$rowFollowing			=	true;
					break;
				}
			}

			if ( $rowFollowing ) {
				$rowFollowing				=	in_array( $rowAsset, CBActivity::getFollowing( $viewer->get( 'id', 0, GetterInterface::INT ) ) );
			}
		}

		$compact							=	CBActivity::findParamOverrde( $row, 'compact', false );
		$canEdit							=	CBActivity::findParamOverrde( $row, 'edit', true );

		if ( $canEdit !== false ) {
			$canEdit						=	( $rowOwner || $canModerate );
		}

		$showActions						=	CBActivity::findParamOverrde( $row, 'actions', true, $stream );
		$showLocations						=	CBActivity::findParamOverrde( $row, 'locations', true, $stream );
		$showLinks							=	CBActivity::findParamOverrde( $row, 'links', true, $stream );
		$showTags							=	CBActivity::findParamOverrde( $row, 'tags', true, $stream );
		$showLikes							=	CBActivity::findParamOverrde( $row, 'likes', true, $stream );
		$showComments						=	CBActivity::findParamOverrde( $row, 'comments', true, $stream );

		if ( $row->get( '_hidden', null, GetterInterface::STRING ) ) {
			$rowPinned						=	false;
			$rowReported					=	false;
			$canEdit						=	false;
			$showLikes						=	false;
			$showComments					=	false;
		}

		$title								=	$stream->parser( $title )->parse( array( 'linebreaks' ) );
		$message							=	$stream->parser( $message )->parse();
		$action								=	( $showActions ? $stream->parser( $row->action() )->parse( array( 'linebreaks' ) ) : null );
		$location							=	( $showLocations ? $row->location() : null );
		$tags								=	null;

		if ( ( ! $title ) && preg_match( '/^profile\.(\d+)/', $rowAsset, $matches ) ) {
			if ( ( (int) $matches[1] != $row->get( 'user_id', 0, GetterInterface::INT ) ) && ( (int) $matches[1] != $_CB_framework->displayedUser() ) ) {
				$targetUser					=	CBuser::getInstance( (int) $matches[1], false );

				if ( $targetUser->getUserData()->get( 'id', 0, GetterInterface::INT ) ) {
					$title					.=	' <span class="fa fa-caret-right"></span> <strong>' . $targetUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</strong>';
				}
			}
		}

		if ( $showTags ) {
			$tagsStream						=	$row->tags( $stream );

			if ( $row->get( '_tags', null, GetterInterface::BOOLEAN ) === false ) {
				$tagsStream->set( 'query', false );
			} else {
				$tags						=	$tagsStream->tags( 'tagged' );
			}

			if ( $tags ) {
				$tags						=	CBTxt::T( 'ACTIVITY_TAGS', 'with [tags]', array( '[tags]' => $tags ) );
			}
		}

		if ( $action || $location || $tags ) {
			$subContent						=	( $action ? $action : null )
											.	( $location ? ( $action ? ' ' : null ) . $location : null )
											.	( $tags ? ( $action || $location ? ' ' : null ) . $tags : null );

			if ( $title ) {
				$message					.=	'<div class="streamItemSubContent">&mdash; ' . $subContent . '</div>';
			} else {
				$title						.=	$subContent;
			}
		}

		if ( $showLikes ) {
			$likesStream					=	$row->likes( $stream );

			if ( $row->get( '_likes', null, GetterInterface::BOOLEAN ) === false ) {
				$likesStream->set( 'query', false );
			}

			$like							=	$likesStream->likes( 'button' );

			if ( $like ) {
				if ( $footer ) {
					$footer					.=	'<div class="streamPanelFooterDivider border-default"></div>';
				}

				$footer						.=	$like;
			}
		}

		if ( $showComments ) {
			$commentsStream					=	$row->comments( $stream );

			if ( $row->get( '_comments', null, GetterInterface::BOOLEAN ) === false ) {
				$commentsStream->set( 'query', false );
			}

			$comments						=	$commentsStream->comments();

			if ( $comments ) {
				if ( $footer ) {
					$footer					.=	'<div class="streamPanelFooterDivider border-default"></div>';
				}

				$footer						.=	$comments;
			}
		}

		$modified							=	$row->params()->get( 'modified', null, GetterInterface::STRING );

		if ( $modified ) {
			$modified						=	' ' . cbTooltip( null, cbFormatDate( $modified ), null, 'auto', null, '<span class="fa fa-edit"></span>', null, 'class="streamIconEdited" data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		}

		$name								=	'<strong>' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</strong>';
		$names								=	$row->params()->get( 'overrides.names', array(), GetterInterface::RAW );

		if ( $names ) {
			$totalNames						=	( count( $names ) + 1 );
			$namesList						=	array( $name );

			foreach ( $names as $k => $nameId ) {
				if ( is_numeric( $nameId ) ) {
					$newUser				=	CBuser::getInstance( $nameId, false );

					if ( count( $namesList ) > 1 ) {
						$newName			=	$newUser->getField( 'formatname', null, 'html', 'none', 'profile', 0, true );
					} else {
						$newName			=	$newUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true );
					}
				} else {
					$newName				=	htmlspecialchars( $nameId );
				}

				if ( ! $newName ) {
					--$totalNames;
					continue;
				}

				if ( count( $namesList ) > 1 ) {
					$namesList[]			=	$newName;

					if ( count( $namesList ) > 15 ) {
						$namesList[]		=	CBTxt::T( 'more...' );
						break;
					}
				} else {
					$namesList[]			=	'<strong>' . $newName . '</strong>';
				}
			}

			if ( $totalNames > 2 ) {
				$nameOne					=	array_shift( $namesList );
				$nameTwo					=	array_shift( $namesList );

				$more						=	cbTooltip( null, implode( '<br />', $namesList ), null, 'auto', null, CBTxt::T( 'NAMES_MORE', '[names] other|[names] others|%%COUNT%%', array( '%%COUNT%%' => ( $totalNames - 2 ), '[names]' => CBActivity::getFormattedTotal( ( $totalNames - 2 ) ) ) ), 'javascript: void(0);', 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );

				$name						=		CBTxt::T( 'NAMES_MORE_THAN_TWO', '[name_1], [name_2], and [more]', array( '[name_1]' => $nameOne, '[name_2]' => $nameTwo, '[more]' => $more ) );
			} elseif ( $totalNames > 1 ) {
				$name						=		CBTxt::T( 'NAMES_TWO', '[name_1] and [name_2]', array( '[name_1]' => $namesList[0], '[name_2]' => $namesList[1] ) );
			}
		}

		$return								=	'<div class="streamItem activityContainer' . ( $compact ? ' streamItemCompact' : null ) . ( $rowGlobal ? ' streamItemGlobal' : null ) . ( $rowPinned ? ' streamItemPinned' : null ) . ( ! $rowRead ? ' streamItemUnread' : null ) . ' streamPanel panel ' . ( $rowGlobal ? 'panel-info' : ( $rowPinned ? 'panel-warning' : ( ! $rowRead ? 'panel-primary' : 'panel-default' ) ) ) . ' streamItem' . $rowId . '" data-cbactivity-id="' . $row->get( 'id', 0, GetterInterface::INT ) . '" data-cbactivity-timestamp="' . Application::Date( $row->get( 'date', null, GetterInterface::STRING ), 'UTC' )->getTimestamp() . '">'
											.		'<div class="streamItemInner">'
											.			'<div class="streamMedia streamPanelHeading activityContainerHeader' . ( ! $compact ? ' media' : null ) . ' panel-heading clearfix">';

		if ( ! $compact ) {
			$return							.=				'<div class="streamMediaLeft activityContainerLogo media-left">'
											.					$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
											.				'</div>';
		}

		$return								.=				'<div class="streamMediaBody activityContainerTitle' . ( ! $compact ? ' media-body' : null ) . '">'
											.					'<div class="activityContainerTitleTop text-muted clearfix">'
											.						$name
											.						( $title ? ' ' . $title : null );

		if ( ! $compact ) {
			$return							.=					'</div>'
											.					'<div class="activityContainerTitleBottom text-muted' . ( ! $compact ? ' text-small' : null ) . ' clearfix">';
		} else {
			$return							.=					' ';
		}

		$return								.=						cbTooltip( null, cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ) ), null, 'auto', null, cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, 'timeago' ), null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' )
											.						$modified
											.						( $date ? ' ' . $date : null )
											.					'</div>'
											.				'</div>'
											.			'</div>';

		if ( $message ) {
			$return							.=			'<div class="streamPanelBody streamItemDisplay activityContainerContent panel-body">'
											.				'<div class="activityContainerContentInner cbMoreLess">'
											.					'<div class="streamItemContent cbMoreLessContent clearfix">'
											.						$message
											.					'</div>'
											.					'<div class="cbMoreLessOpen fade-edge hidden">'
											.						'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
											.					'</div>'
											.				'</div>'
											.			'</div>';
		}

		$return								.=			( $insert ? '<div class="streamItemDisplay activityContainerInsert clearfix">' . $insert . '</div>' : null );

		if ( $showLinks && $row->attachments()->count() ) {
			$return							.=			'<div class="streamPanelBody streamItemDisplay activityContainerAttachments panel-body">'
											.				'<div class="activityContainerAttachmentsInner clearfix">'
											.					HTML_cbactivityStreamAttachments::showAttachments( $row, $viewer, $stream, $plugin, $output )
											.				'</div>'
											.			'</div>';
		}

		$return								.=			( $footer ? '<div class="streamPanelFooter streamItemDisplay activityContainerFooter panel-footer clearfix">' . $footer . '</div>' : null );

		if ( $showMenu && ( $canModerate || $rowOwner || ( $viewer->get( 'id', 0, GetterInterface::INT ) && ( ! $rowOwner ) ) || $menu ) ) {
			$menuItems						=	'<ul class="streamItemMenuItems activityMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

			if ( $viewer->get( 'id', 0, GetterInterface::INT ) && ( ! $rowOwner ) ) {
				$hideUser					=	$cbUser->getField( 'formatname', null, 'html', 'none', 'profile', 0, true );

				if ( $row->get( '_hidden', null, GetterInterface::STRING ) ) {
					switch ( $row->get( '_hidden', null, GetterInterface::STRING ) ) {
						case 'activity.user':
							$menuItems		.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'unhide', 'type' => 'user', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemUnhide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-unlock-alt"></span> ' . CBTxt::T( 'UNHIDE_ACTIVITY_FROM_USER', 'Unhide Activity from <span class="text-info">[user]</span>', array( '[user]' => $hideUser ) ) . '</a></li>';
							break;
						case 'activity.asset':
							$menuItems		.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'unhide', 'type' => 'asset', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemUnhide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-unlock-alt"></span> ' . CBTxt::T( 'Unhide similar Activity' ) . '</a></li>';
							break;
						case 'activity':
						default:
							$menuItems		.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'unhide', 'type' => 'activity', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemUnhide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-unlock-alt"></span> ' . CBTxt::T( 'Unhide this Activity' ) . '</a></li>';
							break;
					}
				} else {
					$menuItems				.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'hide', 'type' => 'user', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemHide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'ARE_YOU_SURE_HIDE_ACTIVITY_FROM_USER', 'Are you sure you want to hide all Activity from <span class="text-info">[user]</span>?', array( '[user]' => $hideUser ) ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'HIDE_ACTIVITY_FROM_USER_BUTTON', 'Hide Activity from <strong>[user]</strong>', array( '[user]' => $hideUser ) ) ) . '"><span class="fa fa-lock"></span> ' . CBTxt::T( 'HIDE_ACTIVITY_FROM_USER', 'Hide Activity from <span class="text-info">[user]</span>', array( '[user]' => $hideUser ) ) . '</a></li>'
											.		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'hide', 'type' => 'asset', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemHide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to hide all Activity similar to this Activity?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Hide similar Activity' ) ) . '"><span class="fa fa-lock"></span> ' . CBTxt::T( 'Hide similar Activity' ) . '</a></li>'
											.		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'hide', 'type' => 'activity', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemHide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to hide this Activity?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Hide this Activity' ) ) . '"><span class="fa fa-lock"></span> ' . CBTxt::T( 'Hide this Activity' ) . '</a></li>';
				}

				if ( $rowFollowing ) {
					$menuItems				.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'unfollow', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemUnfollow streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-user"></span> ' . CBTxt::T( 'Unfollow this Activity' ) . '</a></li>';
				}

				if ( $canModerate || $rowOwner || $menu ) {
					$menuItems				.=		'<li class="streamItemMenuItem activityMenuItem divider"></li>';
				}
			}

			if ( $canModerate || $rowOwner ) {
				if ( $stream->get( 'pinned', true, GetterInterface::BOOLEAN ) && Application::MyUser()->isGlobalModerator() && ( ! $row->get( '_hidden', null, GetterInterface::STRING ) ) ) {
					if ( $rowPinned ) {
						$menuItems			.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'unpin', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemUnpin streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-star-o"></span> ' . CBTxt::T( 'Unpin this Activity' ) . '</a></li>';
					} else {
						$menuItems			.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'pin', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemPin streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-star"></span> ' . CBTxt::T( 'Pin this Activity' ) . '</a></li>';
					}
				}

				if ( $canEdit ) {
					$menuItems				.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemEdit streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';
				}

				$menuItems					.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemDelete streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to delete this Activity?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Delete Activity' ) ) . '"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
			}

			if ( $plugin->params->get( 'reporting', true, GetterInterface::BOOLEAN ) && $viewer->get( 'id', 0, GetterInterface::INT ) && ( ! $rowOwner ) && ( ! $canModerate ) && ( ! CBActivity::canModerate( $stream, $row->get( 'user_id', 0, GetterInterface::INT ) ) ) && ( ! $row->get( '_hidden', null, GetterInterface::STRING ) ) && ( ! $rowGlobal ) ) {
				$menuItems					.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'report', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityMenuItemReport streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to report and hide this Activity?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Report and Hide this Activity' ) ) . '"><span class="fa fa-exclamation-circle"></span> ' . CBTxt::T( 'Report this Activity' ) . '</a></li>';
			}

			if ( $menu ) {
				$menuItems					.=		'<li class="streamItemMenuItem activityMenuItem">' . implode( '</li><li class="streamItemMenuItem activityMenuItem">', $menu ) . '</li>';
			}

			$menuItems						.=	'</ul>';

			$menuAttr						=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="fa fa-chevron-down text-muted" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle" data-cbtooltip-open-classes="open"' );

			$return							.=			'<div class="streamItemMenu activityContainerMenu">'
											.				( $rowPinned ? '<span class="streamItemPinnedIcon fa fa-star text-warning"' . $pinnedTooltip . '></span> ' : null )
											.				( $rowGlobal ? '<span class="streamItemGlobalIcon fa fa-bullhorn text-info"' . $globalTooltip . '></span> ' : null )
											.				( $rowReported ? '<span class="streamItemReportedIcon fa fa-warning text-danger"' . $reportedTooltip . '></span> ' : null )
											.				'<span ' . trim( $menuAttr ) . '></span>'
											.			'</div>';
		} elseif ( $rowPinned || $rowGlobal || $rowReported ) {
			$return							.=			'<div class="streamItemMenu activityContainerMenu hidden-xs">'
											.				( $rowPinned ? '<span class="streamItemPinnedIcon fa fa-star text-warning"' . $pinnedTooltip . '></span>' . ( $rowGlobal ? ' ' : null ) : null )
											.				( $rowGlobal ? '<span class="streamItemGlobalIcon fa fa-bullhorn text-info"' . $globalTooltip . '></span>' . ( $rowReported ? ' ' : null ) : null )
											.				( $rowReported ? '<span class="streamItemReportedIcon fa fa-warning text-danger"' . $reportedTooltip . '></span>' : null )
											.			'</div>';
		}

		$return								.=		'</div>'
											.		( $output == 'save' ? CBActivity::reloadHeaders() : null )
											.	'</div>';

		return $return;
	}
}