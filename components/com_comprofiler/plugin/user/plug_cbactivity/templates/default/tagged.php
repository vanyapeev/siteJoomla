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
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Tags;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\CBActivity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityTagged
{

	/**
	 * @param TagTable[]      $rows
	 * @param UserTable       $viewer
	 * @param Tags            $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 */
	static public function showTagged( $rows, $viewer, $stream, $plugin, $output )
	{
		global $_CB_framework, $_PLUGINS;

		$integrations			=	$_PLUGINS->trigger( 'activity_onBeforeDisplayStreamTagged', array( &$rows, $viewer, &$stream, $output ) );

		if ( ! $rows ) {
			return;
		}

		static $loaded			=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.taggedStream' ).cbactivity();", 'cbactivity' );
		}

		$tags					=	array();

		foreach ( $rows as $row ) {
			if ( is_numeric( $row->get( 'tag', null, GetterInterface::STRING ) ) ) {
				$name			=	CBuser::getInstance( $row->get( 'tag', 0, GetterInterface::INT ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true );
			} else {
				$name			=	htmlspecialchars( $row->get( 'tag', null, GetterInterface::STRING ) );
			}

			if ( ! $name ) {
				continue;
			}

			$tags[]				=	'<span class="taggedStreamTag">'
								.		$name
								.	'</span>';
		}

		if ( ! $tags ) {
			return;
		}

		$return					=	'<span class="taggedStream streamContainer stream' . htmlspecialchars( $stream->id() ) . '" data-cbactivity-stream="' . htmlspecialchars( $stream->id() ) . '">'
								.		implode( '', $integrations );

		if ( count( $tags ) > 2 ) {
			$tagOne				=	array_shift( $tags );
			$tagTwo				=	array_shift( $tags );

			$total				=	$stream->rows( 'count' );
			$title				=	CBTxt::T( 'TAGGED_COUNT', '[tagged] Tag|[tagged] Tagged|%%COUNT%%', array( '%%COUNT%%' => $total, '[tagged]' => CBActivity::getFormattedTotal( $total ) ) );

			$more				=	'<a href="javascript: void(0);" class="cbTooltip streamModal" data-cbactivity-modal-url="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'tags', 'func' => 'modal', 'stream' => $stream->id() ), 'raw', 0, true ) . '" data-cbtooltip-title="' . htmlspecialchars( $title ) . '" data-cbtooltip-modal="true" data-cbtooltip-open-solo=".taggedModalContainer" data-cbtooltip-width="300px" data-cbtooltip-height="400px" data-cbtooltip-classes="streamModalContainer streamModalContainerLoading taggedModalContainer">'
								.		CBTxt::T( 'TAGGED_MORE', '[tagged] more', array( '%%COUNT%%' => ( $total - 2 ), '[tagged]' => CBActivity::getFormattedTotal( ( $total - 2 ) ) ) )
								.	'</a>';

			$return				.=		CBTxt::T( 'TAGS_MORE_THAN_TWO', '[tag_1], [tag_2], and [more]', array( '[tag_1]' => $tagOne, '[tag_2]' => $tagTwo, '[more]' => $more ) );
		} elseif ( count( $tags ) > 1 ) {
			$return				.=		CBTxt::T( 'TAGS_TWO', '[tag_1] and [tag_2]', array( '[tag_1]' => $tags[0], '[tag_2]' => $tags[1] ) );
		} else {
			$return				.=		$tags[0];
		}

		$return					.=		implode( '', $_PLUGINS->trigger( 'activity_onAfterDisplayStreamTagged', array( $rows, $viewer, $stream, $output ) ) )
								.	'</span>';

		echo $return;
	}
}