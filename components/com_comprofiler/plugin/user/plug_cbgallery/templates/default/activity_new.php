<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Comments;
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryActivityNew
{

	/**
	 * render frontend activity gallery upload
	 *
	 * @param array             $buttons
	 * @param UserTable         $viewer
	 * @param Activity|Comments $stream
	 * @param Gallery           $gallery
	 * @param cbPluginHandler   $plugin
	 * @param string            $output
	 * @return mixed
	 */
	static function showActivityNew( &$buttons, $viewer, $stream, $gallery, $plugin, $output )
	{
		global $_CB_framework;

		if ( ! CBGallery::canCreateItems( 'all', 'upload', $gallery ) ) {
			return null;
		}

		static $JS_LOADED				=	0;

		if ( ! $JS_LOADED++ ) {
			$imageHeight				=	$gallery->get( 'photos_image_height', 640, GetterInterface::INT );
			$imageWidth					=	$gallery->get( 'photos_image_width', 1280, GetterInterface::INT );

			if ( ! $imageHeight ) {
				$imageHeight			=	640;
			}

			if ( ! $imageWidth ) {
				$imageWidth				=	1280;
			}

			$uploadRow					=	'<tr class="galleryShareUploadProgressRow galleryItemsNewUploadFile">'
										.		"<td class=\"galleryItemsNewUploadFileName\">' + ( typeof file.name != 'undefined' ? file.name : Date.now() ) + '</td>"
										.		'<td class="galleryItemsNewUploadFileProgress" style="width: 20%;">'
										.			'<div class="progress">'
										.				'<div class="progress-bar progress-bar-striped active" style="width: 0;"></div>'
										.			'</div>'
										.		'</td>'
										.		'<td class="galleryItemsNewUploadFileActions" style="width: 1%;">'
										.			'<button type="button" class="galleryShareUploadProgressCancel galleryItemsNewUploadFileCancel btn btn-xs btn-danger">' . addslashes( CBTxt::T( 'Cancel' ) ) . '</button>'
										.			'<button type="button" class="galleryShareUploadProgressClear galleryItemsNewUploadFileClear btn btn-xs btn-danger hidden"><span class="fa fa-times"></span></button>'
										.		'</td>'
										.	'</tr>';

			$uploadError				=	'<tr class="galleryShareUploadProgressError galleryItemsNewUploadFileAlert danger text-danger">'
										.		"<td colspan=\"3\">' + response.message + '</td>"
										.	'</tr>';

			$linkError					=	'<span class="galleryShareLinkError help-block">'
										.		"' + response.message + '"
										.	'</span>';

			$js							=	"$( '.galleryActivityNew' ).cbgallery({"
										.		"mode: 'share',"
										.		"clientResize: " . (int) $gallery->get( 'photos_client_resize', true, GetterInterface::BOOLEAN ) . ","
										.		"maxWidth: " . $imageWidth . ","
										.		"maxHeight: " . $imageHeight . ","
										.		"aspectRatio: " . $gallery->get( 'photos_maintain_aspect_ratio', 1, GetterInterface::INT ) . ","
										.		"url: '" . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'item', 'func' => 'save', 'folder' => '-1', 'gallery' => $gallery->id(), cbSpoofField() => cbSpoofString( null, 'plugin' ) ), 'raw', 0, true ) ) . "',"
										.		"callback: {"
										.			"upload: {"
										.				"add: function ( cbgallery, data, file ) {"
										.					"return $( '" . $uploadRow . "' );"
										.				"},"
										.				"error: function ( cbgallery, response ) {"
										.					"return $( '" . $uploadError . "' );"
										.				"}"
										.			"},"
										.			"link: {"
										.				"error: function ( cbgallery, response ) {"
										.					"return $( '" . $linkError . "' );"
										.				"}"
										.			"}"
										.		"}"
										.	"});"
										.	"$( '.galleryActivityNew' ).closest( '.activityStream,.commentsStream' ).on( 'cbactivity.save.success cbactivity.new.cancel', function() {"
										.		"$( this ).find( '.galleryShareUploadProgressRows,.galleryShareEdit' ).html( '' );"
										.	"});";

			$_CB_framework->outputCbJQuery( $js, 'cbgallery' );
		}

		$icon					=	null;
		$types					=	null;

		if ( CBGallery::canCreateItems( 'photos', 'upload', $gallery ) ) {
			$types[]			=	CBGallery::translateType( 'photos', false, true );
			$icon				=	CBGallery::getTypeIcon( 'photos' );
		}

		if ( CBGallery::canCreateItems( 'videos', 'upload', $gallery ) ) {
			$types[]			=	CBGallery::translateType( 'videos', false, true );

			if ( ! $icon ) {
				$icon			=	CBGallery::getTypeIcon( 'videos' );
			}
		}

		if ( CBGallery::canCreateItems( 'files', 'upload', $gallery ) ) {
			$types[]			=	CBGallery::translateType( 'files', false, true );

			if ( ! $icon ) {
				$icon			=	CBGallery::getTypeIcon( 'files' );
			}
		}

		if ( CBGallery::canCreateItems( 'music', 'upload', $gallery ) ) {
			$types[]			=	CBGallery::translateType( 'music', false, true );

			if ( ! $icon ) {
				$icon			=	CBGallery::getTypeIcon( 'music' );
			}
		}

		$uploadTooltip			=	cbTooltip( null, CBTxt::T( 'HAVE_A_TYPES_TO_SHARE', 'Have a [types] to share?', array( '[types]' => implode( ', ', $types ) ) ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );

		$buttons['left'][]		=	'<button type="button" class="streamToggle streamInputUpload btn btn-default btn-xs" data-cbactivity-toggle-target=".streamInputUploadContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default"' . $uploadTooltip . '><span class="fa ' . htmlspecialchars( $icon ) . '"></span></button>';

		$class					=	$plugin->params->get( 'general_class', null, GetterInterface::STRING );

		$return					=	'<div class="streamItemInputGroup streamInputUploadContainer border-default' . ( $stream instanceof Comments ? ' bg-default' : null ) . ' clearfix hidden">'
								.		'<div class="streamItemInputGroupInput border-default">'
								.			'<div class="cbGallery' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
								.				'<div class="galleryActivityNew galleryItemsNew">'
								.					'<div class="galleryItemsNewUpload">'
								.						'<div class="galleryShareUploadDropZone galleryItemsNewUploadArea well well-lg text-center">'
								.							'<strong><span class="fa fa-upload"></span> ' . CBTxt::T( 'Click or Drag & Drop to Upload' ) . '</strong>'
								.							'<input type="file" name="upload" class="galleryShareUpload hidden" multiple />'
								.						'</div>'
								.						'<div class="galleryShareUploadProgress galleryItemsNewUploadFiles hidden">'
								.							'<table class="table table-bordered">'
								.								'<tbody class="galleryShareUploadProgressRows">'
								.								'</tbody>'
								.							'</table>'
								.						'</div>'
								.					'</div>'
								.					'<div class="galleryShareEdit galleryItemsNewEdit"></div>'
								.				'</div>'
								.			'</div>'
								.		'</div>'
								.	'</div>';

		return $return;
	}
}