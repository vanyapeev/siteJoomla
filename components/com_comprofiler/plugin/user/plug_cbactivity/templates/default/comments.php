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
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Comments;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityComments
{

	/**
	 * @param CommentTable[]  $rows
	 * @param cbPageNav       $pageNav
	 * @param UserTable       $viewer
	 * @param Comments        $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 */
	static public function showComments( $rows, $pageNav, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$integrations			=	$_PLUGINS->trigger( 'activity_onBeforeDisplayCommentsStream', array( &$rows, &$pageNav, $viewer, &$stream, $output ) );
		$canCreate				=	( ( ! $stream->get( 'id', null, GetterInterface::RAW ) ) && CBActivity::canCreate( 'comment', $stream ) );

		if ( ( ! $rows ) && ( in_array( $output, array( 'load', 'update' ) ) || ( $stream->get( 'inline', false, GetterInterface::BOOLEAN ) && ( ! $canCreate ) ) ) ) {
			return;
		}

		static $loaded			=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.commentsStream' ).cbactivity();", 'cbactivity' );
		}

		$direction				=	$stream->get( 'direction', 'down', GetterInterface::STRING );
		$return					=	null;

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

			$return				.=	'<div class="commentsStream streamContainer streamContainer' . htmlspecialchars( ucfirst( $direction ) ) . ( $output == 'modal' ? ' streamContainerModal' : null ) . ' stream' . htmlspecialchars( $stream->id() ) . ' clearfix"' . $attributes . '>'
								.		implode( '', $integrations );

			if ( $output == 'modal' ) {
				$return			.=		'<div class="commentsStreamToolbar streamToolbar clearfix">'
								.			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'show', 'stream' => $stream->id() ) ) . '" class="commentsButton commentsButtonSeeAll btn btn-default btn-xs pull-left">' . CBTxt::T( 'See All' ) . '</a>'
								.			'<a href="javascript:void(0);" class="cbTooltipClose commentsButton commentsButtonClose btn btn-danger btn-xs pull-right"><span class="fa fa-times"></span></a>'
								.		'</div>';
			}

			if ( ( $direction == 'down' ) && $canCreate ) {
				$return			.=		HTML_cbactivityCommentNew::showCommentNew( $viewer, $stream, $plugin, $output );
			}

			if ( $autoUpdate ) {
				$return				.=		'<div class="commentsStreamToolbar streamToolbar hidden clearfix">'
									.			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'comments', 'func' => 'update', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="commentsButton commentsButtonUpdate streamUpdate btn btn-primary btn-xs hidden"><span class="fa fa-refresh"></span></a>'
									.		'</div>';
			}
		}

		if ( ( $direction == 'up' ) && $stream->get( 'paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limitstart ) ) {
			$return				.=		'<a href="' . $pageNav->limitstartUrl( $pageNav->limitstart ) . '" class="commentsButton commentsButtonMore streamMore">' . CBTxt::T( 'Show older comments' ) . '</a>';
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			if ( $rows && ( ! preg_match( '/^comment\.(\d+)/', $stream->asset() ) ) && ( $pageNav->total > $pageNav->limit ) ) {
				$return			.=		'<div class="commentsStreamCount streamCount pull-right label label-default"><span class="commentsStreamCountIcon streamCountIcon fa fa-comments"></span> ' . CBTxt::T( 'COMMENTS_COUNT', '[comments] Comment|[comments] Comments|%%COUNT%%', array( '%%COUNT%%' => $pageNav->total, '[comments]' => CBActivity::getFormattedTotal( $pageNav->total ) ) ) . '</div>';
			}

			$return				.=		'<div class="commentsStreamItems streamItems">';
		}

		if ( $rows ) {
			foreach ( $rows as $row ) {
				$return			.=			HTML_cbactivityCommentContainer::showCommentContainer( $row, $viewer, $stream, $plugin, $output );
			}
		} elseif ( ( ! $stream->get( 'inline', false, GetterInterface::BOOLEAN ) ) && ( ! $canCreate ) ) {
			$return				.=			'<div class="commentsStreamEmpty streamEmpty">';

			if ( preg_match( '/^comment\.(\d+)/', $stream->asset() ) ) {
				$return			.=				CBTxt::T( 'No replies to display.' );
			} else {
				$return			.=				CBTxt::T( 'No comments to display.' );
			}

			$return				.=			'</div>';
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return				.=		'</div>';

			if ( ( $direction == 'up' ) && $canCreate ) {
				$return			.=		HTML_cbactivityCommentNew::showCommentNew( $viewer, $stream, $plugin, $output );
			}
		}

		if ( ( $direction == 'down' ) && $stream->get( 'paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limitstart ) ) {
			$return				.=		'<a href="' . $pageNav->limitstartUrl( $pageNav->limitstart ) . '" class="commentsButton commentsButtonMore streamMore">' . CBTxt::T( 'Show more comments' ) . '</a>';
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return				.=		implode( '', $_PLUGINS->trigger( 'activity_onAfterDisplayCommentsStream', array( $rows, $pageNav, $viewer, $stream, $output ) ) )
								.		( in_array( $output, array( 'modal', 'toggle' ) ) ? CBActivity::reloadHeaders() : null )
								.	'</div>';
		} else {
			$return				.=	CBActivity::reloadHeaders();
		}

		echo $return;
	}
}