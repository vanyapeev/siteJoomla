<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Tags;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\CBActivity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityTagsEdit
{

	/**
	 * @param TagTable[]      $rows
	 * @param UserTable       $viewer
	 * @param Tags            $stream
	 * @param cbPluginHandler $plugin
	 */
	static public function showTagsEdit( $rows, $viewer, $stream, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$_PLUGINS->trigger( 'activity_onDisplayStreamTagsEdit', array( &$rows, $viewer, $stream ) );

		if ( $stream->get( 'inline', false, GetterInterface::BOOLEAN ) ) {
			$classes			=	'tagsStreamEdit streamInputSelect streamInputTags form-control no-border';
		} else {
			static $loaded		=	0;

			if ( ! $loaded++ ) {
				$_CB_framework->outputCbJQuery( "$( '.tagsStreamEdit' ).cbselect();", 'cbselect' );
			}

			$classes			=	'tagsStreamEdit form-control';
		}

		$tags					=	array();

		foreach ( $rows as $row ) {
			$tags[]				=	$row->get( 'tag', null, GetterInterface::STRING );
		}

		$tagOptions				=	CBActivity::loadTagOptions( $stream );
		$placeholder			=	$stream->get( 'placeholder', null, GetterInterface::STRING );
		$name					=	md5( 'tags_' . $stream->asset() );

		echo str_replace( $name . '__', md5( $stream->id() . '_tags_' . rand() ), moscomprofilerHTML::selectList( $tagOptions, $name . '[]', 'multiple="multiple" class="' . htmlspecialchars( $classes ) . '"' . ( $placeholder ? ' data-cbselect-placeholder="' . htmlspecialchars( $placeholder ) . '"' : null ) . ' data-cbselect-tags="true" data-cbselect-width="100%" data-cbselect-height="100%" data-cbselect-dropdown-css-class="streamTagsOptions"', 'value', 'text', $tags, 0, true, false, false ) );
	}
}