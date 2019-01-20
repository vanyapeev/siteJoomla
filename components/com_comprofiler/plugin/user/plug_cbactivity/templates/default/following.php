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
use CB\Plugin\Activity\Table\FollowTable;
use CB\Plugin\Activity\Following;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityFollowing
{

	/**
	 * @param FollowTable[]   $rows
	 * @param cbPageNav       $pageNav
	 * @param UserTable       $viewer
	 * @param Following       $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 */
	static public function showFollowing( $rows, $pageNav, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$integrations			=	$_PLUGINS->trigger( 'activity_onBeforeDisplayFollowingStream', array( &$rows, &$pageNav, $viewer, &$stream, $output ) );

		if ( ( ! $rows ) && ( in_array( $output, array( 'load', 'update', 'modal' ) ) || ( $stream->get( 'inline', false, GetterInterface::BOOLEAN ) && ( ! CBActivity::canCreate( 'follow', $stream, $viewer ) ) ) ) ) {
			return;
		}

		static $loaded			=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.followingStream' ).cbactivity();", 'cbactivity' );
		}

		$return					=	null;

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return				.=	'<div class="followingStream streamContainer stream' . htmlspecialchars( $stream->id() ) . ' clearfix" data-cbactivity-stream="' . htmlspecialchars( $stream->id() ) . '">'
								.		implode( '', $integrations );

			if ( $output != 'modal' ) {
				$count			=	$stream->get( 'count', true, GetterInterface::BOOLEAN );

				$stream->set( 'inline', true );
				$stream->set( 'count', false );

				$return			.=		'<div class="followingStreamToolbar streamToolbar clearfix">';

				if ( $count ) {
					$total		=	$stream->rows( 'count' );

					$return		.=			'<span class="badge badge-muted pull-left">' . CBTxt::T( 'FOLLOWING_COUNT', '[followers] Follower|[followers] Followers|%%COUNT%%', array( '%%COUNT%%' => $total, '[followers]' => CBActivity::getFormattedTotal( $total ) ) ) . '</span>';
				}

				$return			.=			'<span class="pull-right">' . $stream->following( 'button' ) . '</span>'
								.		'</div>';
			}

			$return				.=		'<div class="followingStreamItems streamItems">';
		}

		foreach ( $rows as $row ) {
			$cbUser				=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false );

			$return				.=				'<div class="streamItem followerContainer border-default">'
								.					'<div class="streamItemInner streamMedia media clearfix">'
								.						'<div class="streamMediaLeft followerContainerLogo media-left">'
								.							$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
								.						'</div>'
								.						'<div class="streamMediaBody streamItemDisplay followerContainerContent media-body">'
								.							'<div class="followerContainerContentInner text-small">'
								.								'<div class="streamItemContent">'
								.									'<strong>' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</strong>'
								.								'</div>'
								.							'</div>'
								.							'<div class="followerContainerContentDate text-muted text-small">'
								.								cbTooltip( null, cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ) ), null, 'auto', null, cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, 'timeago' ), null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' )
								.							'</div>'
								.						'</div>'
								.					'</div>'
								.				'</div>';
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return				.=		'</div>';
		}

		if ( $stream->get( 'paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limitstart ) ) {
			$return				.=		'<a href="' . $pageNav->limitstartUrl( $pageNav->limitstart ) . '" class="followingButton followingButtonMore streamMore btn btn-primary btn-sm btn-block">' . CBTxt::T( 'More' ) . '</a>';
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return				.=		implode( '', $_PLUGINS->trigger( 'activity_onAfterDisplayFollowingStream', array( $rows, $pageNav, $viewer, $stream, $output ) ) )
								.		( $output == 'modal' ? CBActivity::reloadHeaders() : null )
								.	'</div>';
		} else {
			$return				.=	CBActivity::reloadHeaders();
		}

		echo $return;
	}
}