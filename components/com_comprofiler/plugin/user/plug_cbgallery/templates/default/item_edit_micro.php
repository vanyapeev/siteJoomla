<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Table\ItemTable;
use CB\Plugin\Gallery\Gallery;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryItemEditMicro
{

	/**
	 * @param ItemTable       $row
	 * @param UserTable       $viewer
	 * @param Gallery         $gallery
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 * @return string
	 */
	static public function showItemEditMicro( $row, $viewer, $gallery, $plugin, $output = null )
	{
		global $_CB_framework;

		static $JS_LOADED	=	0;

		if ( ! $JS_LOADED++ ) {
			$_CB_framework->outputCbJQuery( "$( '.galleryItemEditMicro' ).cbgallery({ mode: 'edit' });", 'cbgallery' );
		}

		$type				=	$row->get( 'type', null, GetterInterface::STRING );

		$return				=	'<div class="galleryEdit galleryItemEditMicro">'
							.		HTML_cbgalleryItemContainer::showItemContainer( $row, $viewer, $gallery, $plugin, 'compact' )
									// CBTxt::T( 'ARE_YOU_SURE_DELETE_TYPE', 'Are you sure you want to delete this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) )
							.		'<button type="button" class="galleryEditDelete galleryButton galleryButtonDelete btn btn-xs btn-danger" data-cbgallery-delete-message="' . htmlspecialchars( CBTxt::T( 'ARE_YOU_SURE_DELETE_TYPE ARE_YOU_SURE_DELETE_' . strtoupper( $type ), 'Are you sure you want to delete this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) ) ) . '" data-cbgallery-delete-url="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true ) . '" ' . cbValidator::getSubmitBtnHtmlAttributes() . '>' . CBTxt::T( 'Delete' ) . '</button>'
							.		'<input type="hidden" name="items[]" value="' . $row->get( 'id', 0, GetterInterface::INT ) . '" />'
							.		'<div class="galleryEditLoading text-center hidden"><span class="fa fa-spinner fa-pulse fa-3x"></span></div>'
							.		( $output == 'ajax' ? CBGallery::reloadHeaders() : null )
							.	'</div>';

		return $return;
	}
}