<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Plugin\Activity\Activity;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\CBActivity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityActivityButton
{

	/**
	 * @param UserTable       $viewer
	 * @param Activity        $stream
	 * @param cbPluginHandler $plugin
	 */
	static public function showActivityButton( $viewer, $stream, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$total				=	0;

		if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$total			=	$stream->rows( 'count' );
		}

		static $loaded		=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.activityStreamButton' ).cbactivity();", 'cbactivity' );
		}

		$layout				=	$stream->get( 'layout', 'stream', GetterInterface::STRING );
		$autoUpdate			=	false;

		$attributes			=	' data-cbactivity-stream="' . htmlspecialchars( $stream->id() ) . '"';

		if ( $stream->get( 'auto_update', false, GetterInterface::BOOLEAN ) ) {
			$autoUpdate		=	true;

			$attributes		.=	' data-cbactivity-autoupdate="true"';
		}

		$return				=	'<span class="activityStreamButton streamContainer streamContainerButton stream' . htmlspecialchars( $stream->id() ) . '"' . $attributes . '>'
							.		implode( '', $_PLUGINS->trigger( 'activity_onBeforeDisplayActivityStreamButton', array( $total, $viewer, &$stream ) ) );

		if ( $layout == 'toggle' ) {
			$return			.=		'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'toggle', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="streamItemAction btn btn-default btn-xs text-muted" data-cbactivity-container="stream" data-cbactivity-action-output="replace" data-cbactivity-action-target="stream">';
		} else {
			$return			.=		'<a href="javascript: void(0);" class="cbTooltip streamModal btn btn-default btn-xs text-muted" data-cbactivity-modal-url="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'modal', 'stream' => $stream->id() ), 'raw', 0, true ) . '" data-cbtooltip-open-event="click" data-cbtooltip-close-event="click" data-cbtooltip-button-close="false" data-cbtooltip-width="600px" data-cbtooltip-height="auto" data-cbtooltip-classes="streamModalContainer streamModalContainerLoad activityModalContainer" data-cbtooltip-open-classes="text-primary">';
		}

		$return				.=			'<span class="fa fa-lg fa-globe"></span>'
							.			( $total ? ' ' . CBActivity::getFormattedTotal( $total ) : null )
							.			( $autoUpdate ? ' <a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'button', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="streamItemAction streamRefresh hidden" data-cbactivity-container="stream" data-cbactivity-action-output="replace" data-cbactivity-action-target="stream" data-cbactivity-action-overlay="false" data-cbactivity-action-fade="false"><span class="fa fa-refresh"></span></a>' : null )
							.		'</a>'
							.		implode( '', $_PLUGINS->trigger( 'activity_onAfterDisplayActivityStreamButton', array( $total, $viewer, $stream ) ) )
							.	'</span>';

		echo $return;
	}
}