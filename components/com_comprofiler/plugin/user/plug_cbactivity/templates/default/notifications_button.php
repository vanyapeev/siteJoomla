<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Plugin\Activity\Notifications;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\CBActivity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityNotificationsButton
{

	/**
	 * @param UserTable       $viewer
	 * @param Notifications   $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 */
	static public function showNotificationsButton( $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$unread						=	false;
		$total						=	0;

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			switch ( $stream->get( 'read', null, GetterInterface::STRING ) ) {
				case 'read':
					$total			=	$stream->reset()->setRead( 'readonly' )->rows( 'count' );
					break;
				case 'unread':
					$total			=	$stream->reset()->setRead( 'unreadonly' )->rows( 'count' );

					if ( $total ) {
						$unread		=	true;
					}
					break;
				default:
					$total			=	$stream->rows( 'count' );
					break;
			}
		}

		static $loaded				=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.notificationsStreamButton' ).cbactivity();", 'cbactivity' );
		}

		$layout						=	$stream->get( 'layout', 'stream', GetterInterface::STRING );
		$autoUpdate					=	false;

		$attributes					=	' data-cbactivity-stream="' . htmlspecialchars( $stream->id() ) . '"';

		if ( $stream->get( 'auto_update', false, GetterInterface::BOOLEAN ) ) {
			$autoUpdate				=	true;

			$attributes				.=	' data-cbactivity-autoupdate="true"';
		}

		$return						=	'<span class="notificationsStreamButton streamContainer streamContainerButton stream' . htmlspecialchars( $stream->id() ) . '"' . $attributes . '>'
									.		implode( '', $_PLUGINS->trigger( 'activity_onBeforeDisplayNotificationsStreamButton', array( $total, $viewer, &$stream ) ) );

		if ( $layout == 'toggle' ) {
			$return					.=		'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'notifications', 'func' => 'toggle', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="streamItemAction' . ( ! $unread ? ' text-muted' : null ) . '" data-cbactivity-container="stream" data-cbactivity-action-output="replace" data-cbactivity-action-target="stream">';
		} else {
			$return					.=		'<a href="javascript: void(0);" class="cbTooltip streamModal' . ( ! $unread ? ' text-muted' : null ) . '" data-cbactivity-modal-url="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'notifications', 'func' => 'modal', 'stream' => $stream->id() ), 'raw', 0, true ) . '" data-cbtooltip-open-event="click" data-cbtooltip-close-event="unfocus" data-cbtooltip-button-close="false" data-cbtooltip-width="450px" data-cbtooltip-height="auto" data-cbtooltip-position-my="top-right" data-cbtooltip-position-at="bottom-right" data-cbtooltip-classes="streamModalContainer streamModalContainerLoad notificationsModalContainer" data-cbtooltip-open-classes="text-primary">';
		}

		$return						.=			'<span class="fa fa-lg fa-globe"></span>'
									.			( $total ? ' ' . CBActivity::getFormattedTotal( $total ) : null )
									.			( $autoUpdate ? ' <a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'notifications', 'func' => 'button', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="streamItemAction streamRefresh hidden" data-cbactivity-container="stream" data-cbactivity-action-output="replace" data-cbactivity-action-target="stream" data-cbactivity-action-overlay="false" data-cbactivity-action-fade="false"><span class="fa fa-refresh"></span></a>' : null )
									.		'</a>'
									.		implode( '', $_PLUGINS->trigger( 'activity_onAfterDisplayNotificationsStreamButton', array( $total, $viewer, $stream ) ) )
									.		( $output == 'refresh' ? CBActivity::reloadHeaders() : null )
									.	'</span>';

		echo $return;
	}
}