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
use CB\Plugin\Activity\Likes;
use CB\Plugin\Activity\CBActivity;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityLike
{

	/**
	 * @param bool            $liked
	 * @param UserTable       $viewer
	 * @param Likes           $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 * @return string
	 */
	static public function showLike( $liked, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$total								=	0;

		if ( $stream->get( 'count', true, GetterInterface::BOOLEAN ) && $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$total							=	$stream->rows( 'count' );
		}

		$canCreate							=	CBActivity::canCreate( 'like', $stream, $viewer );

		if ( ( ! $total ) && ( ! $canCreate ) ) {
			return null;
		}

		static $loaded						=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.likeStream' ).cbactivity();", 'cbactivity' );
		}

		$layout								=	$stream->get( 'layout', 'button', GetterInterface::STRING );

		$return								=	'<span class="likeStream' . ( in_array( $layout, array( 'simple', 'extended' ) ) ? ' likeStreamSimple' : null ) . ' streamContainer stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-stream="' . htmlspecialchars( $stream->id() ) . '">'
											.		implode( '', $_PLUGINS->trigger( 'activity_onBeforeDisplayStreamLike', array( &$liked, $total, $viewer, &$stream, $output ) ) );

		if ( $canCreate ) {
			$likeMenu						=	null;

			if ( ( ! $liked ) && ( count( $stream->types() ) > 1 ) ) {
				$likeTypes					=	'<div class="likesStreamMenu">';

				foreach ( $stream->types() as $type ) {
					$likeTypes				.=		'<div class="likesStreamMenuItem text-center">'
											.			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'likes', 'func' => 'like', 'type' => $type->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="likeStreamButton likeStreamLike streamItemAction" data-cbactivity-container=".stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-container-filter="false" data-cbactivity-action-fade="false">'
											.				'<div class="likesStreamMenuIcon">' . $type->icon() . '</div>'
											.				'<div class="likesStreamMenuTitle">' . CBTxt::T( $type->get( 'value', null, GetterInterface::STRING ) ) . '</div>'
											.			'</a>'
											.		'</div>';
				}

				$likeTypes					.=	'</div>';

				$likeMenu					=	cbTooltip( null, $likeTypes, null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-menu="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="likeStreamSelector" data-cbtooltip-adjust-y="0" data-cbtooltip-tip-hide="0"' );
			}

			if ( in_array( $layout, array( 'simple', 'extended' ) ) ) {
				if ( $liked ) {
					$return					.=			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'likes', 'func' => 'unlike', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="likeStreamLink likeStreamUnlike streamItemAction" data-cbactivity-container=".stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-container-filter="false" data-cbactivity-action-fade="false"><span class="fa fa-thumbs-o-up"></span> ' . CBTxt::T( 'Unlike' ) . '</a>';
				} else {
					if ( $likeMenu ) {
						$return				.=			'<a href="javascript: void(0);" class="likeStreamLink likeStreamLike"' . $likeMenu . '><span class="fa fa-thumbs-o-up"></span> ' . CBTxt::T( 'Like' ) . '</a>';
					} else {
						$return				.=			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'likes', 'func' => 'like', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="likeStreamLink likeStreamLike streamItemAction" data-cbactivity-container=".stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-container-filter="false" data-cbactivity-action-fade="false"><span class="fa fa-thumbs-o-up"></span> ' . CBTxt::T( 'Like' ) . '</a>';
					}
				}
			} else {
				$return						.=		'<span class="likeStreamButtons">';

				if ( $liked ) {
					$return					.=			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'likes', 'func' => 'unlike', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="likeStreamButton likeStreamLiked streamItemAction btn btn-info btn-xs active" data-cbactivity-container=".stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-container-filter="false" data-cbactivity-action-fade="false"><span class="fa fa-thumbs-o-up"></span> ' . CBTxt::T( 'Liked' ) . '</a>'
											.			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'likes', 'func' => 'unlike', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="likeStreamButton likeStreamUnlike streamItemAction btn btn-danger btn-xs" data-cbactivity-container=".stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-container-filter="false" data-cbactivity-action-fade="false"><span class="fa fa-thumbs-o-up"></span> ' . CBTxt::T( 'Unlike' ) . '</a>';
				} else {
					if ( $likeMenu ) {
						$return				.=			'<a href="javascript: void(0);" class="likeStreamButton likeStreamLike btn btn-info btn-xs"' . $likeMenu . '><span class="fa fa-thumbs-o-up"></span> ' . CBTxt::T( 'Like' ) . '</a>';
					} else {
						$return				.=			'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'likes', 'func' => 'like', 'stream' => $stream->id() ), 'raw', 0, true ) . '" class="likeStreamButton likeStreamLike streamItemAction btn btn-info btn-xs" data-cbactivity-container=".stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-container-filter="false" data-cbactivity-action-fade="false"><span class="fa fa-thumbs-o-up"></span> ' . CBTxt::T( 'Like' ) . '</a>';
					}
				}

				$return						.=		'</span>';
			}
		}

		if ( $total ) {
			$title							=	CBTxt::T( 'LIKES_COUNT', '[likes] Like|[likes] Likes|%%COUNT%%', array( '%%COUNT%%' => $total, '[likes]' => CBActivity::getFormattedTotal( $total ) ) );

			if ( in_array( $layout, array( 'simple', 'extended' ) ) ) {
				$return						.=		' <span class="likeStreamCount streamCount text-small">'
											.			'<a href="javascript: void(0);" class="cbTooltip streamModal' . ( $canCreate ? ' text-muted' : null ) . '" data-cbactivity-modal-url="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'likes', 'func' => 'modal', 'stream' => $stream->id() ), 'raw', 0, true ) . '" data-cbtooltip-title="' . htmlspecialchars( $title ) . '" data-cbtooltip-modal="true" data-cbtooltip-open-solo=".likesModalContainer" data-cbtooltip-width="300px" data-cbtooltip-height="400px" data-cbtooltip-classes="streamModalContainer streamModalContainerLoading likesModalContainer">'
											.				( ! $canCreate ? '<span class="fa fa-thumbs-o-up"></span> ' : null );

				if ( $layout == 'extended' ) {
					$stream->set( 'paging', true );
					$stream->set( 'paging_first_limit', 3 );
					$stream->set( 'paging_limit', 3 );
					$stream->set( 'paging_limitstart', 0 );

					$likes					=	array();

					foreach ( $stream->rows() as $row ) {
						$name				=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false )->getField( 'formatname', null, 'html', 'none', 'profile', 0, true );

						if ( ! $name ) {
							continue;
						}

						$likes[]			=	'<span class="likeStreamLiked">'
											.		$name
											.	'</span>';
					}

					if ( $likes ) {
						$likeCount			=	count( $likes );
						$likeOne			=	array_shift( $likes );
						$likeTwo			=	array_shift( $likes );

						if ( $likeCount > 2 ) {
							$return			.=				CBTxt::T( 'LIKES_MORE_THAN_TWO', '[like_1], [like_2], and [more] other liked this|[like_1], [like_2], and [more] others liked this|%%COUNT%%', array( '%%COUNT%%' => ( $total - 2 ), '[like_1]' => $likeOne, '[like_2]' => $likeTwo, '[more]' => CBActivity::getFormattedTotal( ( $total - 2 ) ) ) );
						} elseif ( $likeCount > 1 ) {
							$return			.=				CBTxt::T( 'LIKES_TWO', '[like_1] and [like_2] liked this', array( '[like_1]' => $likeOne, '[like_2]' => $likeTwo ) );
						} else {
							$return			.=				CBTxt::T( 'LIKES_ONE', '[like_1] liked this', array( '[like_1]' => $likeOne ) );
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
				$return						.=		'<span class="likeStreamCount streamCount streamCountLabel text-small">'
											.			( $canCreate ? '<span class="likeStreamCountArrow streamCountLabelArrow border-default"></span>' : null )
											.			'<span class="likeStreamCountContent streamCountLabelContent text-center border-default bg-default text-default">'
											.				'<a href="javascript: void(0);" class="cbTooltip streamModal' . ( $canCreate ? ' text-muted' : null ) . '" data-cbactivity-modal-url="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'likes', 'func' => 'modal', 'stream' => $stream->id() ), 'raw', 0, true ) . '" data-cbtooltip-title="' . htmlspecialchars( $title ) . '" data-cbtooltip-modal="true" data-cbtooltip-open-solo=".likesModalContainer" data-cbtooltip-width="300px" data-cbtooltip-height="400px" data-cbtooltip-classes="streamModalContainer streamModalContainerLoading likesModalContainer">'
											.					( ! $canCreate ? '<span class="fa fa-thumbs-o-up"></span> ' : null )
											.					CBActivity::getFormattedTotal( $total )
											.				'</a>'
											.			'</span>'
											.		'</span>';
			}
		}

		$return								.=		implode( '', $_PLUGINS->trigger( 'activity_onAfterDisplayStreamLike', array( $liked, $total, $viewer, $stream, $output ) ) )
											.		( $output == 'save' ? CBActivity::reloadHeaders() : null )
											.	'</span>';

		return $return;
	}
}