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
use CB\Plugin\Activity\Following;
use CB\Plugin\Activity\CBActivity;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityFollow
{

	/**
	 * @param bool            $following
	 * @param UserTable       $viewer
	 * @param Following       $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 * @return string
	 */
	static public function showFollow( $following, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$total								=	0;

		if ( $stream->get( 'count', true, GetterInterface::BOOLEAN ) && $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$total							=	$stream->rows( 'count' );
		}

		$canCreate							=	CBActivity::canCreate( 'follow', $stream, $viewer );

		if ( ( ! $total ) && ( ! $canCreate ) ) {
			return null;
		}

		static $loaded						=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.followStream' ).cbactivity();", 'cbactivity' );
		}

		$layout								=	$stream->get( 'layout', 'button', GetterInterface::STRING );

		$return								=	'<span class="followStream' . ( in_array( $layout, array( 'simple', 'extended' ) ) ? ' followStreamSimple' : null ) . ' streamContainer stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-stream="' . htmlspecialchars( $stream->id() ) . '">'
											.		implode( '', $_PLUGINS->trigger( 'activity_onBeforeDisplayStreamFollow', array( &$following, $total, $viewer, &$stream, $output ) ) );

		if ( $canCreate ) {
			if ( in_array( $layout, array( 'simple', 'extended' ) ) ) {
				if ( $following ) {
					$return					.=			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'following', 'func' => 'unfollow', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="followStreamLink followStreamUnfollow streamItemAction" data-cbactivity-container=".stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-container-filter="false" data-cbactivity-action-fade="false"><span class="fa fa-feed"></span> ' . CBTxt::T( 'Unfollow' ) . '</a>';
				} else {
					$return					.=			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'following', 'func' => 'follow', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="followStreamLink followStreamFollow streamItemAction" data-cbactivity-container=".stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-container-filter="false" data-cbactivity-action-fade="false"><span class="fa fa-feed"></span> ' . CBTxt::T( 'Follow' ) . '</a>';
				}
			} else {
				$return						.=		'<span class="followStreamButtons">';

				if ( $following ) {
					$return					.=			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'following', 'func' => 'unfollow', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="followStreamButton followStreamFollowed streamItemAction btn btn-info btn-xs active" data-cbactivity-container=".stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-container-filter="false" data-cbactivity-action-fade="false"><span class="fa fa-feed"></span> ' . CBTxt::T( 'Following' ) . '</a>'
											.			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'following', 'func' => 'unfollow', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="followStreamButton followStreamUnfollow streamItemAction btn btn-danger btn-xs" data-cbactivity-container=".stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-container-filter="false" data-cbactivity-action-fade="false"><span class="fa fa-feed"></span> ' . CBTxt::T( 'Unfollow' ) . '</a>';
				} else {
					$return					.=			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'following', 'func' => 'follow', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="followStreamButton followStreamFollow streamItemAction btn btn-info btn-xs" data-cbactivity-container=".stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-container-filter="false" data-cbactivity-action-fade="false"><span class="fa fa-feed"></span> ' . CBTxt::T( 'Follow' ) . '</a>';
				}

				$return						.=		'</span>';
			}
		}

		if ( $total ) {
			$title							=	CBTxt::T( 'FOLLOWING_COUNT', '[followers] Follower|[followers] Followers|%%COUNT%%', array( '%%COUNT%%' => $total, '[followers]' => CBActivity::getFormattedTotal( $total ) ) );

			if ( in_array( $layout, array( 'simple', 'extended' ) ) ) {
				$return						.=		' <span class="followStreamCount streamCount text-small">'
											.			'<a href="javascript: void(0);" class="cbTooltip streamModal' . ( $canCreate ? ' text-muted' : null ) . '" data-cbactivity-modal-url="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'following', 'func' => 'modal', 'stream' => $stream->id() ), 'raw', 0, true ) . '" data-cbtooltip-title="' . htmlspecialchars( $title ) . '" data-cbtooltip-modal="true" data-cbtooltip-open-solo=".followingModalContainer" data-cbtooltip-width="300px" data-cbtooltip-height="400px" data-cbtooltip-classes="streamModalContainer streamModalContainerLoading followingModalContainer">'
											.				( ! $canCreate ? '<span class="fa fa-feed"></span> ' : null );

				if ( $layout == 'extended' ) {
					$stream->set( 'paging', true );
					$stream->set( 'paging_first_limit', 3 );
					$stream->set( 'paging_limit', 3 );
					$stream->set( 'paging_limitstart', 0 );

					$follows				=	array();

					foreach ( $stream->rows() as $row ) {
						$name				=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false )->getField( 'formatname', null, 'html', 'none', 'profile', 0, true );

						if ( ! $name ) {
							continue;
						}

						$follows[]			=	'<span class="followStreamFollowed">'
											.		$name
											.	'</span>';
					}

					if ( $follows ) {
						$followCount		=	count( $follows );
						$followOne			=	array_shift( $follows );
						$followTwo			=	array_shift( $follows );

						if ( $followCount > 2 ) {
							$return			.=				CBTxt::T( 'FOLLOWING_MORE_THAN_TWO', '[follow_1], [follow_2], and [more] other followed this|[follow_1], [follow_2], and [more] others followed this|%%COUNT%%', array( '%%COUNT%%' => ( $total - 2 ), '[follow_1]' => $followOne, '[follow_2]' => $followTwo, '[more]' => CBActivity::getFormattedTotal( ( $total - 2 ) ) ) );
						} elseif ( $followCount > 1 ) {
							$return			.=				CBTxt::T( 'FOLLOWING_TWO', '[follow_1] and [follow_2] followed this', array( '[follow_1]' => $followOne, '[follow_2]' => $followTwo ) );
						} else {
							$return			.=				CBTxt::T( 'FOLLOWING_ONE', '[follow_1] followed this', array( '[follow_1]' => $followOne ) );
						}
					} else {
						$return				.=				CBActivity::getFormattedTotal( $total );
					}
				} else {
					$return					.=				CBActivity::getFormattedTotal( $total );
				}

				$return						.=			'</a>'
											.		'</span>';
			} else {
				$return						.=		'<span class="followStreamCount streamCount streamCountLabel text-small">'
											.			( $canCreate ? '<span class="followStreamCountArrow streamCountLabelArrow border-default"></span>' : null )
											.			'<span class="followStreamCountContent streamCountLabelContent text-center border-default bg-default text-default">'
											.				'<a href="javascript: void(0);" class="cbTooltip streamModal' . ( $canCreate ? ' text-muted' : null ) . '" data-cbactivity-modal-url="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'following', 'func' => 'modal', 'stream' => $stream->id() ), 'raw', 0, true ) . '" data-cbtooltip-title="' . htmlspecialchars( $title ) . '" data-cbtooltip-modal="true" data-cbtooltip-open-solo=".followingModalContainer" data-cbtooltip-width="300px" data-cbtooltip-height="400px" data-cbtooltip-classes="streamModalContainer streamModalContainerLoading followingModalContainer">'
											.					( ! $canCreate ? '<span class="fa fa-feed"></span> ' : null )
											.					CBActivity::getFormattedTotal( $total )
											.				'</a>'
											.			'</span>'
											.		'</span>';
			}
		}

		$return								.=		implode( '', $_PLUGINS->trigger( 'activity_onAfterDisplayStreamFollow', array( $following, $total, $viewer, $stream, $output ) ) )
											.		( $output == 'save' ? CBActivity::reloadHeaders() : null )
											.	'</span>';

		return $return;
	}
}