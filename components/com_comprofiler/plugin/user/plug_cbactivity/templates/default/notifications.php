<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;
use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Notifications;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityNotifications
{

	/**
	 * @param NotificationTable[] $rows
	 * @param cbPageNav           $pageNav
	 * @param UserTable           $viewer
	 * @param Notifications       $stream
	 * @param cbPluginHandler     $plugin
	 * @param string              $output
	 */
	static public function showNotifications( $rows, $pageNav, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$integrations				=	$_PLUGINS->trigger( 'activity_onBeforeDisplayNotificationsStream', array( &$rows, &$pageNav, $viewer, &$stream, $output ) );

		if ( ( ! $rows ) && ( in_array( $output, array( 'load', 'update' ) ) || $stream->get( 'inline', false, GetterInterface::BOOLEAN ) ) ) {
			return;
		}

		static $loaded				=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.notificationsStream' ).cbactivity();", 'cbactivity' );
		}

		$direction					=	$stream->get( 'direction', 'down', GetterInterface::STRING );
		$return						=	null;

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$autoUpdate				=	false;
			$attributes				=	' data-cbactivity-stream="' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-direction="' . htmlspecialchars( $direction ) . '"';

			if ( $output != 'modal' ) {
				if ( $rows && $stream->get( 'auto_update', false, GetterInterface::BOOLEAN ) ) {
					$autoUpdate		=	true;

					$attributes		.=	' data-cbactivity-autoupdate="true"';
				}

				if ( $stream->get( 'auto_load', false, GetterInterface::BOOLEAN ) ) {
					$attributes		.=	' data-cbactivity-autoload="true"';
				}
			}

			$return					.=	'<div class="notificationsStream streamContainer streamContainer' . htmlspecialchars( ucfirst( $direction ) ) . ( $output == 'modal' ? ' streamContainerModal' : null ) . ' stream' . htmlspecialchars( $stream->id() ) . ' clearfix"' . $attributes . '>'
									.		implode( '', $integrations );

			if ( $autoUpdate || $rows ) {
				$return				.=		'<div class="notificationsStreamToolbar streamToolbar clearfix">'
									.			( ( $output == 'modal' ) && $rows ? '<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'notifications', 'func' => 'show', 'stream' => $stream->id() ) ) . '" class="notificationsButton notificationsButtonSeeAll btn btn-default btn-xs pull-left">' . CBTxt::T( 'See All' ) . '</a>' : null )
									.			( $autoUpdate ? '<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'notifications', 'func' => 'update', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="notificationsButton notificationsButtonUpdate streamUpdate btn btn-primary btn-xs hidden"><span class="fa fa-refresh"></span></a>' : null )
									.			( $rows && ( $stream->get( 'hidden', 'visible', GetterInterface::STRING ) != 'hidden' ) ? '<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'notifications', 'func' => 'delete', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="notificationsButton notificationsButtonDeleteAll streamItemAction btn btn-primary btn-xs pull-right" data-cbactivity-container="stream" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to delete all Notifications?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Delete All Notifications' ) ) . '">' . CBTxt::T( 'Delete All' ) . '</a>' : null )
									.		'</div>';
			}
		}

		if ( ( $direction == 'up' ) && ( $output != 'update' ) ) {
			if ( $stream->get( 'paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limitstart ) ) {
				$return				.=		'<a href="' . $pageNav->limitstartUrl( $pageNav->limitstart ) . '" class="notificationsButton notificationsButtonMore streamMore btn btn-primary btn-sm btn-block">' . CBTxt::T( 'Previous' ) . '</a>';
			}
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return					.=		'<div class="notificationsStreamItems streamItems">';
		}

		if ( $rows ) {
			foreach ( $rows as $row ) {
				$return				.=			HTML_cbactivityNotificationContainer::showNotificationContainer( $row, $viewer, $stream, $plugin, $output );
			}
		} elseif ( ! $stream->get( 'inline', false, GetterInterface::BOOLEAN ) ) {
			$return					.=			'<div class="notificationsStreamEmpty streamEmpty">'
									.				CBTxt::T( 'No notifications to display.' )
									.			'</div>';
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return					.=		'</div>';
		}

		if ( ( $direction == 'down' ) && ( $output != 'update' ) ) {
			if ( $stream->get( 'paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limitstart ) ) {
				$return				.=		'<a href="' . $pageNav->limitstartUrl( $pageNav->limitstart ) . '" class="notificationsButton notificationsButtonMore streamMore btn btn-primary btn-sm btn-block">' . CBTxt::T( 'More' ) . '</a>';
			}
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return					.=		implode( '', $_PLUGINS->trigger( 'activity_onAfterDisplayNotificationsStream', array( $rows, $pageNav, $viewer, $stream, $output ) ) )
									.		( in_array( $output, array( 'modal', 'toggle' ) ) ? CBActivity::reloadHeaders() : null )
									.	'</div>';
		} else {
			$return					.=	CBActivity::reloadHeaders();
		}

		echo $return;
	}
}