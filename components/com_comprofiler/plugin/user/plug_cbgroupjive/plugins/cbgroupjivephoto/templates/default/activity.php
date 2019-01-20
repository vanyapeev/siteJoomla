<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Plugin\GroupJivePhoto\Table\PhotoTable;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjivePhotoActivity
{

	/**
	 * render frontend photo activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param null|string                     $title
	 * @param null|string                     $date
	 * @param null|string                     $message
	 * @param null|string                     $insert
	 * @param null|string                     $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param PhotoTable                      $photo
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static function showPhotoActivity( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $photo, $plugin, $output )
	{
		global $_CB_framework, $_PLUGINS;

		$user					=	CBuser::getMyUserDataInstance();
		$photoTitle				=	( $photo->get( 'title', null, GetterInterface::STRING ) ? htmlspecialchars( $photo->get( 'title', null, GetterInterface::STRING ) ) : $photo->name() );
		$groupName				=	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => $photo->group()->get( 'id', 0, GetterInterface::INT ) ) ) . '">' . htmlspecialchars( CBTxt::T( $photo->group()->get( 'name', null, GetterInterface::STRING ) ) ) . '</a>';

		if ( $stream instanceof NotificationsInterface ) {
			if ( cbutf8_strlen( $photoTitle ) > 50 ) {
				$photoTitle		=	trim( cbutf8_substr( $photoTitle, 0, 50 ) ) . '...';
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) == $photo->group()->get( 'user_id', 0, GetterInterface::INT ) ) {
				$title			=	CBTxt::T( 'UPLOADED_PHOTO_IN_YOUR_GROUP', 'uploaded photo [photo] in your group [group]', array( '[photo]' => '<strong>' . $photoTitle . '</strong>', '[group]' => $groupName ) );
			} else {
				$title			=	CBTxt::T( 'UPLOADED_PHOTO_IN_GROUP', 'uploaded photo [photo] in group [group]', array( '[photo]' => '<strong>' . $photoTitle . '</strong>', '[group]' => $groupName ) );
			}
		} else {
			static $loaded		=	0;

			if ( ! $loaded++ ) {
				$js				=	"$( '.gjGroupPhotoItem.cbTooltip,.gjGroupPhotoLogo.cbTooltip' ).on( 'cbtooltip.move', function( e, cbtooltip, event, api ) {"
								.		"if ( api.elements.tooltip ) {"
								.			"api.elements.content.find( '.gjGroupPhotoImage' ).css( 'line-height', api.elements.content.css( 'max-height' ) );"
								.		"}"
								.	"});";

				$_CB_framework->outputCbJQuery( $js );
			}

			if ( $stream->get( 'groupjive.ingroup', 0, GetterInterface::INT ) == $photo->group()->get( 'id', 0, GetterInterface::INT ) ) {
				$title			=	CBTxt::T( 'uploaded a photo' );
			} else {
				$title			=	CBTxt::T( 'UPLOADED_A_PHOTO_IN_GROUP', 'uploaded a photo in group [group]', array( '[group]' => '<strong>' . $groupName . '</strong>' ) );
			}

			$logo				=	null;

			if ( $photo->exists() ) {
				$showPath		=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'show', 'id' => $photo->get( 'id', 0, GetterInterface::INT ) ), 'raw', 0, true );

				$image			=	'<div class="gjGroupPhotoImageContainer">'
								.		'<div class="gjGroupPhotoImage text-center">'
								.			'<img alt="' . htmlspecialchars( $title ) . '" src="' . htmlspecialchars( $showPath ) . '" class="cbImgPict cbFullPict img-thumbnail" />'
								.		'</div>'
								.		'<div class="gjGroupPhotoImageInfo">'
								.			'<div class="gjGroupPhotoImageInfoRow">'
								.				'<div class="gjGroupPhotoImageInfoTitle col-sm-8 text-left"><strong>' . $photoTitle . '</strong></div>'
								.				'<div class="gjGroupPhotoImageInfoOriginal col-sm-4 text-right">'
								.					'<a href="' . $showPath . '" target="_blank">'
								.						CBTxt::T( 'Original' )
								.					'</a>'
								.				'</div>'
								.			'</div>';

				if ( $photo->get( 'description', null, GetterInterface::STRING ) ) {
					$image		.=			'<div class="gjGroupPhotoImageInfoRow">'
								.				'<div class="gjGroupPhotoImageInfoDescription col-sm-8 text-left">' . htmlspecialchars( $photo->get( 'description', null, GetterInterface::STRING ) ) . '</div>'
								.				'<div class="gjGroupPhotoImageInfoDownload col-sm-4 text-right">'
								.				'</div>'
								.			'</div>';
				}

				$image			.=		'</div>'
								.	'</div>';

				$logo			=	cbTooltip( 1, $image, null, array( '80%', '80%' ), null, '<img src="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'preview', 'id' => $photo->get( 'id', 0, GetterInterface::INT ) ), 'raw', 0, true ) . '" class="cbImgPict cbThumbPict img-thumbnail" />', 'javascript: void(0);', 'class="gjGroupPhotoLogo" data-cbtooltip-modal="true" data-cbtooltip-classes="gjGroupPhotoImageModal"' );
			}

			$insert				=	'<div class="gjPhotoActivity">'
								.		'<div class="gjGroupPhotoRow gjCanvasBox cbCanvasBox cbCanvasBoxInline">'
								.			'<div class="gjCanvasBoxTop cbCanvasBoxTop">'
								.				'<div class="gjCanvasBoxPhoto cbCanvasBoxPhoto cbCanvasBoxPhotoCenter">'
								.					$logo
								.				'</div>'
								.			'</div>'
								.		'</div>'
								.	'</div>';
		}

		$_PLUGINS->trigger( 'gj_onAfterPhotoActivity', array( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $photo, $plugin, $output ) );
	}
}