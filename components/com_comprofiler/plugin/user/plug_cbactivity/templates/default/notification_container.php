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
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Notifications;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityNotificationContainer
{

	/**
	 * @param NotificationTable $row
	 * @param UserTable         $viewer
	 * @param Notifications     $stream
	 * @param cbPluginHandler   $plugin
	 * @param string            $output
	 * @return string
	 */
	static public function showNotificationContainer( $row, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$rowId								=	md5( $stream->id() . '_' . $row->get( 'id', 0, GetterInterface::INT ) );
		$rowGlobal							=	false;
		$rowPinned							=	false;
		$rowRead							=	true;

		$pinnedTooltip						=	cbTooltip( null, CBTxt::T( 'Pinned' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$globalTooltip						=	cbTooltip( null, CBTxt::T( 'Announcement' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );

		$cbUser								=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false );
		$title								=	$row->get( 'title', null, GetterInterface::STRING );
		$message							=	$row->get( 'message', null, GetterInterface::STRING );
		$date								=	null;
		$insert								=	null;
		$footer								=	null;
		$menu								=	array();

		HTML_cbactivityActivityCore::parseDisplay( $row, $title, $message, $date, $insert, $footer, $menu, $stream, $plugin, $output );

		if ( CBActivity::findParamOverrde( $row, 'global', true ) !== false ) {
			$rowGlobal						=	( $row->get( 'asset', null, GetterInterface::STRING ) == 'global' );
		}

		if ( CBActivity::findParamOverrde( $row, 'pinned', true ) !== false ) {
			$rowPinned						=	( $stream->get( 'pinned', true, GetterInterface::BOOLEAN ) && $row->get( 'pinned', false, GetterInterface::BOOLEAN ) );
		}

		if ( CBActivity::findParamOverrde( $row, 'read', true ) !== false ) {
			if ( $stream->get( 'read', null, GetterInterface::STRING ) !== null ) {
				$rowRead					=	$row->get( '_read', null, GetterInterface::INT );
			}
		}

		$compact							=	CBActivity::findParamOverrde( $row, 'compact', false );
		$showLinks							=	CBActivity::findParamOverrde( $row, 'links', true );

		if ( $row->get( '_hidden', null, GetterInterface::STRING ) ) {
			$rowPinned						=	false;
		}

		$integrations						=	implode( '', $_PLUGINS->trigger( 'activity_onDisplayStreamNotification', array( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $output ) ) );

		if ( $integrations ) {
			$footer							=	$integrations . $footer;
		}

		$title								=	$stream->parser( $title )->parse( array( 'linebreaks' ) );
		$message							=	$stream->parser( $message )->parse();

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

		$return								=	'<div class="streamItem notificationContainer' . ( $compact ? ' streamItemCompact' : null ) . ( $rowGlobal ? ' streamItemGlobal' : null ) . ( $rowPinned ? ' streamItemPinned' : null ) . ( ! $rowRead ? ' streamItemUnread' : null ) . ' streamPanel panel ' . ( ! $rowRead ? 'panel-primary' : ( $rowGlobal ? 'panel-info' : ( $rowPinned ? 'panel-warning' : 'panel-default' ) ) ) . ' streamItem' . $rowId . '" data-cbactivity-id="' . $row->get( 'id', 0, GetterInterface::INT ) . '" data-cbactivity-timestamp="' . Application::Date( $row->get( 'date', null, GetterInterface::STRING ), 'UTC' )->getTimestamp() . '">'
											.		'<div class="streamItemInner">'
											.			'<div class="streamMedia streamPanelHeading notificationContainerHeader' . ( ! $compact ? ' media' : null ) . ' panel-heading clearfix">';

		if ( ! $compact ) {
			$return							.=				'<div class="streamMediaLeft notificationContainerLogo media-left">'
											.					$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
											.				'</div>';
		}

		$return								.=				'<div class="streamMediaBody notificationContainerTitle' . ( ! $compact ? ' media-body' : null ) . '">'
											.					'<div class="notificationContainerTitleTop text-muted clearfix">'
											.						$name
											.						( $title ? ' ' . $title : null );

		if ( ! $compact ) {
			$return							.=					'</div>'
											.					'<div class="notificationContainerTitleBottom text-muted' . ( ! $compact ? ' text-small' : null ) . ' clearfix">';
		} else {
			$return							.=					' ';
		}

		$return								.=						cbTooltip( null, cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ) ), null, 'auto', null, cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, 'timeago' ), null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' )
											.						( $date ? ' ' . $date : null )
											.					'</div>'
											.				'</div>'
											.			'</div>';

		if ( $message ) {
			$return							.=			'<div class="streamPanelBody streamItemDisplay notificationContainerContent panel-body">'
											.				'<div class="notificationContainerContentInner cbMoreLess">'
											.					'<div class="streamItemContent cbMoreLessContent clearfix">'
											.						$message
											.					'</div>'
											.					'<div class="cbMoreLessOpen fade-edge hidden">'
											.						'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
											.					'</div>'
											.				'</div>'
											.			'</div>';
		}

		$return								.=			( $insert ? '<div class="streamItemDisplay notificationContainerInsert clearfix">' . $insert . '</div>' : null );

		if ( $showLinks && $row->attachments()->count() ) {
			$return							.=			'<div class="streamPanelBody streamItemDisplay notificationContainerAttachments panel-body">'
											.				'<div class="notificationContainerAttachmentsInner clearfix">'
											.					HTML_cbactivityStreamAttachments::showAttachments( $row, $viewer, $stream, $plugin, $output )
											.				'</div>'
											.			'</div>';
		}

		$return								.=			( $footer ? '<div class="streamPanelFooter streamItemDisplay notificationContainerFooter panel-footer clearfix">' . $footer . '</div>' : null )
											.			'<div class="streamItemMenu notificationContainerMenu">'
											.				( $rowPinned ? '<span class="streamItemPinnedIcon fa fa-star text-warning hidden-xs"' . $pinnedTooltip . '></span> ' : null )
											.				( $rowGlobal ? '<span class="streamItemGlobalIcon fa fa-bullhorn text-info hidden-xs"' . $globalTooltip . '></span> ' : null );

		if ( $row->get( '_hidden', null, GetterInterface::STRING ) ) {
			$return							.=				'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'notifications', 'func' => 'unhide', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="notificationItemActionUnhide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '">' . CBTxt::T( 'Unhide' ) . '</a>';
		} elseif ( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user', 0, GetterInterface::INT ) ) {
			$return							.=				'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'notifications', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="notificationItemActionDelete streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-times"></span></a>';
		} else {
			$return							.=				'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'notifications', 'func' => 'hide', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="notificationItemActionHide streamItemAction" data-cbactivity-container=".streamItem' . $rowId . '"><span class="fa fa-times"></span></a>';
		}

		$return								.=			'</div>'
											.		'</div>'
											.		( $output == 'save' ? CBActivity::reloadHeaders() : null )
											.	'</div>';

		return $return;
	}
}