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
use CB\Plugin\Activity\Table\LikeTable;
use CB\Plugin\Activity\Likes;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityLikes
{

	/**
	 * @param LikeTable[]     $rows
	 * @param cbPageNav       $pageNav
	 * @param UserTable       $viewer
	 * @param Likes           $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 */
	static public function showLikes( $rows, $pageNav, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$integrations			=	$_PLUGINS->trigger( 'activity_onBeforeDisplayLikesStream', array( &$rows, &$pageNav, $viewer, &$stream, $output ) );

		if ( ( ! $rows ) && ( in_array( $output, array( 'load', 'update', 'modal' ) ) || ( $stream->get( 'inline', false, GetterInterface::BOOLEAN ) && ( ! CBActivity::canCreate( 'like', $stream, $viewer ) ) ) ) ) {
			return;
		}

		static $loaded			=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.likesStream' ).cbactivity();", 'cbactivity' );
		}

		$return					=	null;

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return				.=	'<div class="likesStream streamContainer stream' . htmlspecialchars( $stream->id() ) . ' clearfix" data-cbactivity-stream="' . htmlspecialchars( $stream->id() ) . '">'
								.		implode( '', $integrations );

			if ( $output != 'modal' ) {
				$count			=	$stream->get( 'count', true, GetterInterface::BOOLEAN );

				$stream->set( 'inline', true );
				$stream->set( 'count', false );

				$return			.=		'<div class="likesStreamToolbar streamToolbar clearfix">';

				if ( $count ) {
					$total		=	$stream->rows( 'count' );

					$return		.=			'<span class="badge badge-muted pull-left">' . CBTxt::T( 'LIKES_COUNT', '[likes] Like|[likes] Likes|%%COUNT%%', array( '%%COUNT%%' => $total, '[likes]' => CBActivity::getFormattedTotal( $total ) ) ) . '</span>';
				}

				$return			.=			'<span class="pull-right">' . $stream->likes( 'button' ) . '</span>'
								.		'</div>';
			}

			$return				.=		'<div class="likesStreamItems streamItems">';
		}

		foreach ( $rows as $row ) {
			$cbUser				=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false );

			$return				.=				'<div class="streamItem likeContainer border-default">'
								.					'<div class="streamItemInner streamMedia media clearfix">'
								.						'<div class="streamMediaLeft likeContainerLogo media-left">'
								.							$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
								.						'</div>'
								.						'<div class="streamMediaBody streamItemDisplay likeContainerContent media-body">'
								.							'<div class="row">'
								.								'<div class="col-xs-8">'
								.									'<div class="likeContainerContentInner text-small">'
								.										'<div class="streamItemContent">'
								.											'<strong>' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</strong>'
								.										'</div>'
								.									'</div>'
								.									'<div class="likeContainerContentDate text-muted text-small">'
								.										cbTooltip( null, cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ) ), null, 'auto', null, cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, 'timeago' ), null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' )
								.									'</div>'
								.								'</div>'
								.								'<div class="col-xs-4 text-right">'
								.									$row->type()->icon()
								.								'</div>'
								.							'</div>'
								.						'</div>'
								.					'</div>'
								.				'</div>';
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return				.=		'</div>';
		}

		if ( $stream->get( 'paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limitstart ) ) {
			$return				.=		'<a href="' . $pageNav->limitstartUrl( $pageNav->limitstart ) . '" class="likesButton likesButtonMore streamMore btn btn-primary btn-sm btn-block">' . CBTxt::T( 'More' ) . '</a>';
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return				.=		implode( '', $_PLUGINS->trigger( 'activity_onAfterDisplayLikesStream', array( $rows, $pageNav, $viewer, $stream, $output ) ) )
								.		( $output == 'modal' ? CBActivity::reloadHeaders() : null )
								.	'</div>';
		} else {
			$return				.=	CBActivity::reloadHeaders();
		}

		echo $return;
	}
}