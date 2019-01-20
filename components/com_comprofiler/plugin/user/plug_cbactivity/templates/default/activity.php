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
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityActivity
{

	/**
	 * @param ActivityTable[] $rows
	 * @param cbPageNav       $pageNav
	 * @param string          $searching
	 * @param UserTable       $viewer
	 * @param Activity        $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 */
	static public function showActivity( $rows, $pageNav, $searching, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$integrations				=	$_PLUGINS->trigger( 'activity_onBeforeDisplayActivityStream', array( &$rows, &$pageNav, &$searching, $viewer, &$stream, $output ) );
		$canCreate					=	( ( ! $stream->get( 'id', null, GetterInterface::RAW ) ) && CBActivity::canCreate( 'activity', $stream ) );

		if ( ( ! $rows ) && ( in_array( $output, array( 'load', 'update' ) ) || ( $stream->get( 'inline', false, GetterInterface::BOOLEAN ) && ( ! $canCreate ) ) ) ) {
			return;
		}

		static $loaded				=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.activityStream' ).cbactivity();", 'cbactivity' );
		}

		$direction					=	$stream->get( 'direction', 'down', GetterInterface::STRING );
		$return						=	null;

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$autoUpdate				=	false;
			$attributes				=	' data-cbactivity-stream="' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-direction="' . htmlspecialchars( $direction ) . '"';

			if ( $output != 'modal' ) {
				if ( ( ! $searching ) && $rows && $stream->get( 'auto_update', false, GetterInterface::BOOLEAN ) ) {
					$autoUpdate		=	true;

					$attributes		.=	' data-cbactivity-autoupdate="true"';
				}

				if ( $stream->get( 'auto_load', false, GetterInterface::BOOLEAN ) ) {
					$attributes		.=	' data-cbactivity-autoload="true"';
				}
			}

			$return					.=	'<div class="activityStream streamContainer streamContainer' . htmlspecialchars( ucfirst( $direction ) ) . ( $output == 'modal' ? ' streamContainerModal' : null ) . ' stream' . htmlspecialchars( $stream->id() ) . ' clearfix"' . $attributes . '>'
									.		implode( '', $integrations );

			if ( $output == 'modal' ) {
				$return				.=		'<div class="activityStreamToolbar streamToolbar clearfix">'
									.			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'show', 'stream' => $stream->id() ) ) . '" class="activityButton activityButtonSeeAll btn btn-default btn-xs pull-left">' . CBTxt::T( 'See All' ) . '</a>'
									.			'<a href="javascript:void(0);" class="cbTooltipClose activityButton activityButtonClose btn btn-danger btn-xs pull-right"><span class="fa fa-times"></span></a>'
									.		'</div>';
			}

			if ( ( $direction == 'down' ) && $canCreate ) {
				$return				.=		HTML_cbactivityActivityNew::showActivityNew( $viewer, $stream, $plugin, $output );
			}

			if ( $autoUpdate ) {
				$return				.=		'<div class="activityStreamToolbar streamToolbar hidden clearfix">'
									.			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'update', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="activityButton activityButtonUpdate streamUpdate btn btn-primary btn-xs hidden"><span class="fa fa-refresh"></span></a>'
									.		'</div>';
			}
		}

		if ( ( $direction == 'up' ) && ( $output != 'update' ) ) {
			if ( $stream->get( 'paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limitstart ) ) {
				$return				.=		'<a href="' . $pageNav->limitstartUrl( $pageNav->limitstart ) . '" class="activityButton activityButtonMore streamMore btn btn-primary btn-sm btn-block">' . CBTxt::T( 'Previous' ) . '</a>';
			}
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return					.=		'<div class="activityStreamItems streamItems">';
		}

		if ( $rows ) {
			foreach ( $rows as $row ) {
				$return				.=			HTML_cbactivityActivityContainer::showActivityContainer( $row, $viewer, $stream, $plugin, $output );
			}
		} elseif ( ( ! $stream->get( 'inline', false, GetterInterface::BOOLEAN ) ) && ( ! $canCreate ) ) {
			$return					.=			'<div class="activityStreamEmpty streamEmpty">';

			if ( $searching ) {
				$return				.=				CBTxt::T( 'No activity search results found.' );
			} else {
				$return				.=				CBTxt::T( 'No activity to display.' );
			}

			$return					.=			'</div>';
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return					.=		'</div>';

			if ( ( $direction == 'up' ) && $canCreate ) {
				$return				.=		HTML_cbactivityActivityNew::showActivityNew( $viewer, $stream, $plugin, $output );
			}
		}

		if ( ( $direction == 'down' ) && ( $output != 'update' ) ) {
			if ( $stream->get( 'paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limitstart ) ) {
				$return				.=		'<a href="' . $pageNav->limitstartUrl( $pageNav->limitstart ) . '" class="activityButton activityButtonMore streamMore btn btn-primary btn-sm btn-block">' . CBTxt::T( 'More' ) . '</a>';
			}
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return					.=		implode( '', $_PLUGINS->trigger( 'activity_onAfterDisplayActivityStream', array( $rows, $pageNav, $searching, $viewer, $stream, $output ) ) )
									.		( in_array( $output, array( 'modal', 'toggle' ) ) ? CBActivity::reloadHeaders() : null )
									.	'</div>';
		} else {
			$return					.=	CBActivity::reloadHeaders();
		}

		echo $return;
	}
}