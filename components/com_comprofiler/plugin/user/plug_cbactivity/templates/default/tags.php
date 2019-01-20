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
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Tags;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityTags
{

	/**
	 * @param TagTable[]      $rows
	 * @param cbPageNav       $pageNav
	 * @param UserTable       $viewer
	 * @param Tags            $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 */
	static public function showTags( $rows, $pageNav, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$integrations			=	$_PLUGINS->trigger( 'activity_onBeforeDisplayTagsStream', array( &$rows, &$pageNav, $viewer, &$stream, $output ) );

		if ( ! $rows ) {
			return;
		}

		static $loaded			=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.tagsStream' ).cbactivity();", 'cbactivity' );
		}

		$return					=	null;

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return				.=	'<div class="tagsStream streamContainer stream' . htmlspecialchars( $stream->id() ) . ' clearfix" data-cbactivity-stream="' . htmlspecialchars( $stream->id() ) . '">'
								.		implode( '', $integrations )
								.		'<div class="tagsStreamItems streamItems">';
		}

		foreach ( $rows as $row ) {
			if ( is_numeric( $row->get( 'tag', null, GetterInterface::STRING ) ) ) {
				$cbUser			=	CBuser::getInstance( $row->get( 'tag', null, GetterInterface::STRING ), false );

				$avatar			=	$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true );
				$name			=	$cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true );
			} else {
				$avatar			=	null;
				$name			=	htmlspecialchars( $row->get( 'tag', null, GetterInterface::STRING ) );
			}

			if ( ! $name ) {
				continue;
			}

			$return				.=				'<div class="streamItem tagContainer border-default">'
								.					'<div class="streamItemInner streamMedia media clearfix">'
								.						'<div class="streamMediaLeft tagContainerLogo media-left">'
								.							$avatar
								.						'</div>'
								.						'<div class="streamMediaBody streamItemDisplay tagContainerContent media-body">'
								.							'<div class="tagContainerContentInner text-small">'
								.								'<div class="streamItemContent">'
								.									'<strong>' . $name . '</strong>'
								.								'</div>'
								.							'</div>'
								.							'<div class="tagContainerContentDate text-muted text-small">'
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
			$return				.=		'<a href="' . $pageNav->limitstartUrl( $pageNav->limitstart ) . '" class="tagsButton tagsButtonMore streamMore btn btn-primary btn-sm btn-block">' . CBTxt::T( 'More' ) . '</a>';
		}

		if ( ! in_array( $output, array( 'load', 'update' ) ) ) {
			$return				.=		implode( '', $_PLUGINS->trigger( 'activity_onAfterDisplayTagsStream', array( $rows, $pageNav, $viewer, $stream, $output ) ) )
								.		( $output == 'modal' ? CBActivity::reloadHeaders() : null )
								.	'</div>';
		} else {
			$return				.=	CBActivity::reloadHeaders();
		}

		echo $return;
	}
}