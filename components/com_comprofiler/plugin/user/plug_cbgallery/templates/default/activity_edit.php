<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Comments;
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Gallery\Table\ItemTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryActivityEdit
{

	/**
	 * render frontend activity gallery edit
	 *
	 * @param ActivityTable|CommentTable $row
	 * @param ItemTable[]                $items
	 * @param array                      $buttons
	 * @param UserTable                  $viewer
	 * @param Activity|Comments          $stream
	 * @param Gallery                    $gallery
	 * @param cbPluginHandler            $plugin
	 * @param string                     $output
	 * @return mixed
	 */
	static function showActivityEdit( $row, $items, &$buttons, $viewer, $stream, $gallery, $plugin, $output )
	{
		if ( ! CBGallery::canCreateItems( 'all', 'upload', $gallery ) ) {
			return null;
		}

		$class			=	$plugin->params->get( 'general_class', null, GetterInterface::STRING );

		$return			=	'<div class="streamItemInputGroup streamInputUploadContainer border-default' . ( $stream instanceof Comments ? ' bg-default' : null ) . ' clearfix">'
						.		'<div class="streamItemInputGroupInput border-default">'
						.			'<div class="cbGallery' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
						.				'<div class="galleryActivityEdit galleryItemsEdit">';

		foreach ( $items as $item ) {
			$return		.=					HTML_cbgalleryItemEditMicro::showItemEditMicro( $item, $viewer, $gallery, $plugin, $output );
		}

		$return			.=				'</div>'
						.			'</div>'
						.		'</div>'
						.	'</div>';

		return $return;
	}
}