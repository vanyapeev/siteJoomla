<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\Table\ItemTable;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CB\Plugin\Activity\Comments;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryActivity
{

	/**
	 * render frontend gallery activity
	 *
	 * @param ActivityTable|CommentTable|NotificationTable $row
	 * @param null|string                                  $title
	 * @param null|string                                  $date
	 * @param null|string                                  $message
	 * @param null|string                                  $insert
	 * @param null|string                                  $footer
	 * @param array                                        $menu
	 * @param Activity|Comments|Notifications              $stream
	 * @param array                                        $assetMatches
	 * @param ItemTable[]                                  $items
	 * @param Gallery                                      $gallery
	 * @param cbPluginHandler                              $plugin
	 * @param string                                       $output
	 */
	static function showActivity( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $items, $gallery, $plugin, $output )
	{
		global $_CB_framework;

		static $JS_LOADED				=	0;

		if ( ! $JS_LOADED++ ) {
			$js							=	"$( '.galleryModalToggle' ).cbgallery();"
										.	"$( '.galleryActivityModalToggle' ).on( 'click', function () {"
										.		"$( this ).closest( '.streamItem' ).find( '.galleryModalToggle:first' ).click();"
										.	"});"
										.	"$( '.galleryToggleMedia' ).on( 'click', function () {"
										.		"if ( $( this ).hasClass( 'galleryToggleMediaMusic' ) ) {"
										.			"$( this ).find( '.galleryToggleMediaIcon' ).addClass( 'hidden' );"
										.		"} else {"
										.			"$( this ).find( '.galleryToggleMediaPreview' ).addClass( 'hidden' );"
										.		"}"
										.		"$( this ).find( 'iframe,video,audio' ).attr( 'src', $( this ).data( 'cbgallery-video' ) ).removeClass( 'hidden' );"
										.		"$( this ).find( 'video,audio' ).attr( 'autoplay', 'autoplay' );"
										.	"})";

			$_CB_framework->outputCbJQuery( $js, 'cbgallery' );
		}

		$notification					=	( $stream instanceof NotificationsInterface );
		$type							=	( isset( $assetMatches[1] ) ? $assetMatches[1] : null );

		// Check if this is a mixed type output:
		if ( $type ) {
			foreach ( $items as $i => $item ) {
				if ( $type != $item->get( 'type', null, GetterInterface::STRING ) ) {
					$type				=	'files';
					break;
				}
			}
		}

		$typeTranslated					=	CBGallery::translateType( $type, false, true );

		if ( count( $items ) > 1 ) {
			$typeTranslated				=	CBTxt::T( 'COUNT_TYPES', '%%COUNT%% [types]', array( '%%COUNT%%' => count( $items ), '[types]' => CBGallery::translateType( $type, true, true ) ) );
		}

		$viewer							=	CBuser::getMyUserDataInstance();
		$typeModal						=	null;
		$titleModal						=	null;
		$folder							=	null;
		$owner							=	false;
		$class							=	$plugin->params->get( 'general_class', null, GetterInterface::STRING );

		$return							=	'<div class="cbGallery' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
										.		'<div class="galleryActivity clearfix">';

		$items							=	array_values( $items );

		/** @var ItemTable[] $items */
		foreach ( $items as $i => $item ) {
			if ( ! $item->exists() ) {
				continue;
			}

			if ( count( $items ) > 1 ) {
				$previous				=	( $i == 0 ? ( count( $items ) - 1 ) : ( $i - 1 ) );

				if ( isset( $items[$previous] ) ) {
					$item->set( '_previous', '.galleryContainer' . md5( $gallery->id() . '_' . $items[$previous]->get( 'id', 0, GetterInterface::INT ) ) );
				}

				$next					=	( ( $i + 1 ) <= ( count( $items ) - 1 )  ? ( $i + 1 ) : 0 );

				if ( isset( $items[$next] ) ) {
					$item->set( '_next', '.galleryContainer' . md5( $gallery->id() . '_' . $items[$next]->get( 'id', 0, GetterInterface::INT ) ) );
				}
			}

			$displayPath				=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'display', 'id' => $item->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn() ), 'raw', 0, true );
			$data						=	array();

			if ( $item->get( '_previous', null, GetterInterface::STRING ) ) {
				$data['previous']		=	$item->get( '_previous', null, GetterInterface::STRING );
			}

			if ( $item->get( '_next', null, GetterInterface::STRING ) ) {
				$data['next']			=	$item->get( '_next', null, GetterInterface::STRING );
			}

			if ( $item->domain() ) {
				$showPath				=	htmlspecialchars( $item->path() );
				$downloadPath			=	null;
			} else {
				$showPath				=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'show', 'id' => $item->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true );
				$downloadPath			=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'download', 'id' => $item->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true );
			}

			$modal						=	cbTooltip( null, null, null, array( '90%', '90%' ), null, null, null, 'data-hascbtooltip="true" data-cbtooltip-modal="true" data-cbtooltip-open-solo=".galleryModal" data-cbtooltip-classes="galleryModal" data-cbgallery-url="' . $displayPath . '" data-cbgallery-request="' . htmlspecialchars( json_encode( $data ) ) . '"' . ( $item->get( 'type', null, GetterInterface::STRING ) == 'photos' ? ' data-cbgallery-preload="' . $showPath . '"' : null ) );

			if ( ( ! $notification ) && ( ( count( $items ) > 1 ) || ( $item->get( 'type', null, GetterInterface::STRING ) == 'photos' ) ) ) {
				if ( ! $typeModal ) {
					$typeModal			=	'<a href="javascript: void(0);" class="galleryActivityModalToggle">' . $typeTranslated . '</a>';
				}

				if ( ! $titleModal ) {
					$titleModal			=	'<a href="javascript: void(0);" class="galleryActivityModalToggle">' . ( $item->get( 'title', null, GetterInterface::STRING ) ? $item->get( 'title', null, GetterInterface::STRING ) : $item->name() ) . '</a>';
				}
			} else {
				if ( ! $typeModal ) {
					$typeModal			=	'<a href="javascript: void(0);" class="galleryItemName galleryModalToggle"' . $modal . '>' . $typeTranslated . '</a>';
				}

				if ( ! $titleModal ) {
					$titleModal			=	'<a href="javascript: void(0);" class="galleryItemName galleryModalToggle"' . $modal . '>' . ( $item->get( 'title', null, GetterInterface::STRING ) ? $item->get( 'title', null, GetterInterface::STRING ) : $item->name() ) . '</a>';
				}
			}

			if ( ! $owner ) {
				$owner					=	( $viewer->get( 'id', 0, GetterInterface::INT ) == $item->get( 'user_id', 0, GetterInterface::INT ) );
			}

			if ( ( ! $folder ) && $item->get( 'folder', 0, GetterInterface::INT ) ) {
				$itemFolder				=	$item->folder( $gallery );

				if ( $itemFolder->get('id', 0, GetterInterface::INT ) && ( ( $itemFolder->get( 'published', 1, GetterInterface::INT ) ) || ( ( Application::MyUser()->getUserId() == $itemFolder->get( 'user_id', 0, GetterInterface::INT ) ) || CBGallery::canModerate( $gallery ) ) ) ) {
					$folder				=	'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'show', 'id' => $itemFolder->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn( true ) ) ) . '">'
										.		( $itemFolder->get( 'title', null, GetterInterface::STRING ) ? $itemFolder->get( 'title', null, GetterInterface::STRING ) : cbFormatDate( $itemFolder->get( 'date', null, GetterInterface::STRING ), true, false, CBTxt::T( 'GALLERY_SHORT_DATE_FORMAT', 'M j, Y' ) ) )
										.	'</a>';
				}
			}

			if ( ( ! $message ) && ( count( $items ) == 1 ) ) {
				$message				=	( $item->get( 'description', null, GetterInterface::STRING ) ? $item->get( 'description', null, GetterInterface::STRING ) : null );
			}

			$width						=	$item->get( '_width', 0, GetterInterface::INT );

			if ( ! $width ) {
				$width					=	$gallery->get( 'items_width', 200, GetterInterface::INT );
			}

			if ( ! $width ) {
				$width					=	200;
			} elseif ( $width < 100 ) {
				$width					=	100;
			}

			$orgWidth					=	$width;

			if ( $i == 0 ) {
				$width					=	( $width * 2 );
			}

			$itemId						=	md5( $gallery->id() . '_' . $item->get( 'id', 0, GetterInterface::INT ) );

			if ( count( $items ) == 1 ) {
				switch ( $item->get( 'type', null, GetterInterface::STRING ) ) {
					case 'photos':
						$return			.=			'<div class="galleryItemContainer galleryContainer galleryContainerPhotos galleryContainer' . $itemId . ( $i > 5 ? ' hidden' : null ) . '">'
										.				'<div class="galleryContainerInner">'
										.					'<div class="galleryContainerTop">'
										.						'<a href="javascript: void(0);" class="galleryItemEmbed galleryModalToggle"' . $modal . '>' . $item->thumbnail( $gallery, 0, false ) . '</a>'
										.					'</div>'
										.				'</div>'
										.			'</div>';
						break;
					case 'files':
						$return			.=			'<table class="galleryMediaFileTable table table-bordered">'
										.				'<tbody>'
										.					'<tr>'
										.						'<th style="width: 150px;">' . CBTxt::T( 'File' ) . '</th>'
										.						'<td><a href="' . $showPath . '" target="_blank">' . $item->name() . '</a></td>'
										.					'</tr>'
										.					'<tr>'
										.						'<th style="width: 150px;">' . CBTxt::T( 'Extension' ) . '</th>'
										.						'<td>' . $item->extension() . '</td>'
										.					'</tr>'
										.					'<tr>'
										.						'<th style="width: 150px;">' . CBTxt::T( 'Size' ) . '</th>'
										.						'<td>' . $item->size() . '</td>'
										.					'</tr>'
										.					'<tr>'
										.						'<th style="width: 150px;">' . CBTxt::T( 'Modified' ) . '</th>'
										.						'<td>' . cbFormatDate( $item->modified() ) . '</td>'
										.					'</tr>';

						if ( $gallery->get( 'files_md5', false, GetterInterface::BOOLEAN ) && $item->params()->get( 'checksum.md5', null, GetterInterface::STRING ) ) {
							$return		.=					'<tr>'
										.						'<th style="width: 150px;">' . CBTxt::T( 'MD5 Checksum' ) . '</th>'
										.						'<td>' . $item->params()->get( 'checksum.md5', null, GetterInterface::STRING ) . '</td>'
										.					'</tr>';
						}

						if ( $gallery->get( 'files_sha1', false, GetterInterface::BOOLEAN ) && $item->params()->get( 'checksum.sha1', null, GetterInterface::STRING ) ) {
							$return		.=					'<tr>'
										.						'<th style="width: 150px;">' . CBTxt::T( 'SHA1 Checksum' ) . '</th>'
										.						'<td>' . $item->params()->get( 'checksum.sha1', null, GetterInterface::STRING ) . '</td>'
										.					'</tr>';
						}

						$return			.=					'<tr>'
										.						'<td colspan="2" class="text-right"><a href="' . $downloadPath . '" target="_blank" class="btn btn-sm btn-primary">' . CBTxt::T( 'Download' ) . '</a></td>'
										.					'</tr>'
										.				'</tbody>'
										.			'</table>';
						break;
					case 'videos':
						if ( $item->mimeType() == 'video/x-youtube' ) {
							if ( preg_match( '%(?:(?:watch\?v=)|(?:embed/)|(?:be/))([A-Za-z0-9_-]+)%', $showPath, $matches ) ) {
								$return	.=			'<div class="galleryToggleMedia galleryToggleMediaVideo" data-cbgallery-video="https://www.youtube.com/embed/' . htmlspecialchars( $matches[1] ) . '?autoplay=1">'
										.				'<div class="galleryToggleMediaPreview text-center" style="height: 360px;">'
										.					'<div class="galleryToggleMediaIcon fa fa-play" style="line-height: 360px; font-size: 100px;"></div>'
										.					$item->thumbnail( $gallery, 0, true )
										.				'</div>'
										.				'<iframe width="100%" height="360" frameborder="0" allowfullscreen class="galleryVideoPlayer hidden"></iframe>'
										.			'</div>';
							}
						} else {
							if ( $item->get( 'thumbnail', null, GetterInterface::STRING ) ) {
								$return	.=			'<div class="galleryToggleMedia galleryToggleMediaVideo" data-cbgallery-video="' . $showPath . '">'
										.				'<div class="galleryToggleMediaPreview text-center" style="height: 360px;">'
										.					'<div class="galleryToggleMediaIcon fa fa-play" style="line-height: 360px; font-size: 100px;"></div>'
										.					$item->thumbnail( $gallery, 0, true )
										.				'</div>'
										.				'<video width="100%" height="100%" style="width: 100%; height: 100%;" type="' . htmlspecialchars( $item->mimeType() ) . '" controls="controls" preload="none" class="galleryVideoPlayer hidden"></video>'
										.			'</div>';
							} else {
								$return	.=			'<video width="100%" height="100%" style="width: 100%; height: 100%;" src="' . $showPath . '" type="' . htmlspecialchars( $item->mimeType() ) . '" controls="controls" preload="auto" class="galleryVideoPlayer"></video>';
							}
						}
						break;
					case 'music':
						if ( $item->get( 'thumbnail', null, GetterInterface::STRING ) ) {
							$return		.=			'<div class="galleryToggleMedia galleryToggleMediaMusic" data-cbgallery-video="' . $showPath . '">'
										.				'<div class="galleryToggleMediaPreview text-center" style="height: 360px;">'
										.					'<div class="galleryToggleMediaIcon fa fa-play" style="line-height: 360px; font-size: 100px;"></div>'
										.					$item->thumbnail( $gallery, 0, true )
										.				'</div>'
										.				'<audio style="width: 100%;" src="' . $showPath . '" type="' . htmlspecialchars( $item->mimeType() ) . '" controls="controls" preload="none" class="galleryAudioPlayer hidden"></audio>'
										.			'</div>';
						} else {
							$return		.=			'<audio style="width: 100%;" src="' . $showPath . '" type="' . htmlspecialchars( $item->mimeType() ) . '" controls="controls" preload="auto" class="galleryAudioPlayer"></audio>';
						}
						break;
				}
			} else {
				$embed					=	$item->thumbnail( $gallery, $width, ( ( $item->width( true ) >= $width ) || ( $item->height( true ) >= $width ) || ( $item->mimeType() == 'video/x-youtube' ) ) );
				$solid					=	( ( ! in_array( $item->get( 'type', null, GetterInterface::STRING ), array( 'photos', 'videos' ) ) ) && ( ! $item->get( 'thumbnail', null, GetterInterface::STRING ) ) );

				if ( ( $i == 5 ) && ( count( $items ) > 6 ) ) {
					$embed				.=	'<div class="galleryContainerMore" style="font-size: ' . ( $width * 0.25 ) . 'px; vertical-align: middle;">+' . ( count( $items ) - 5 ) . '</div>';

					$solid				=	false;
				}

				$return					.=			'<div class="galleryItemContainer galleryContainer galleryContainer' . htmlspecialchars( ucfirst( $item->get( 'type', null, GetterInterface::STRING ) ) ) . ( $i == 0 ? ' galleryContainerFirst' : null ) . ' galleryContainer' . $itemId . ( $solid ? ' galleryContainerSolid' : null ) . ( $i > 5 ? ' hidden' : null ) . '">'
										.				'<div class="galleryContainerInner" style="width: ' . $orgWidth . 'px;' . ( $i == 0 ? ' line-height: ' . $orgWidth . 'px; min-width: ' . $width . 'px;' : null ) . '">'
										.					'<div class="galleryContainerTop" style="height: ' . $orgWidth . 'px; line-height: ' . $width . 'px;' . ( $i == 0 ? ' min-height: ' . $width . 'px;' : null ) . '">'
										.						'<a href="javascript: void(0);" class="galleryItemEmbed galleryModalToggle"' . $modal . '>' . $embed . '</a>'
										.					'</div>';

				if ( $solid ) {
					$itemTitle			=	( $item->get( 'title', null, GetterInterface::STRING ) ? $item->get( 'title', null, GetterInterface::STRING ) : $item->name() );

					$return				.=					'<div class="galleryContainerBottom"' . ( $i == 0 ? ' style="line-height: initial;"' : null ) . '>'
										.						'<div class="galleryContainerContent bg-default">'
										.							'<div class="galleryContainerContentRow text-nowrap text-overflow small">'
										.								'<strong>'
										.									'<a href="javascript: void(0);" class="galleryItemName galleryModalToggle"' . $modal . '>' . $itemTitle . '</a>'
										.								'</strong>'
										.							'</div>'
										.						'</div>'
										.					'</div>';
				}

				$return					.=				'</div>'
										.			'</div>';
			}
		}

		$return							.=		'</div>'
										.	'</div>';

		if ( ! $type ) {
			$insert						.=	$return;
		} elseif ( $notification ) {
			switch ( ( isset( $assetMatches[3] ) ? $assetMatches[3] : '' ) ) {
				case 'like':
					if ( $owner ) {
						if ( $folder ) {
							$title		=	CBTxt::T( 'LIKED_YOUR_TYPE_TITLE_IN_ALBUM', 'liked your [type] [title] in album [album]', array( '[type]' => $typeTranslated, '[title]' => $titleModal, '[album]' => $folder ) );
						} else {
							$title		=	CBTxt::T( 'LIKED_YOUR_TYPE_TITLE', 'liked your [type] [title]', array( '[type]' => $typeTranslated, '[title]' => $titleModal ) );
						}
					} else {
						if ( $folder ) {
							$title		=	CBTxt::T( 'LIKED_TYPE_TITLE_IN_ALBUM', 'liked [type] [title] in album [album]', array( '[type]' => $typeTranslated, '[title]' => $titleModal, '[album]' => $folder ) );
						} else {
							$title		=	CBTxt::T( 'LIKED_TYPE_TITLE', 'liked [type] [title]', array( '[type]' => $typeTranslated, '[title]' => $titleModal ) );
						}
					}
					break;
				case 'tag':
					if ( ! $owner ) {
						if ( $folder ) {
							$title		=	CBTxt::T( 'TAGGED_YOU_IN_TYPE_TITLE_IN_ALBUM', 'tagged you in [type] [title] in album [album]', array( '[type]' => $typeTranslated, '[title]' => $titleModal, '[album]' => $folder ) );
						} else {
							$title		=	CBTxt::T( 'TAGGED_YOU_IN_TYPE_TITLE', 'tagged you in [type] [title]', array( '[type]' => $typeTranslated, '[title]' => $titleModal ) );
						}
					} else {
						if ( $folder ) {
							$title		=	CBTxt::T( 'TAGGED_IN_TYPE_TITLE_IN_ALBUM', 'tagged in [type] [title] in album [album]', array( '[type]' => $typeTranslated, '[title]' => $titleModal, '[album]' => $folder ) );
						} else {
							$title		=	CBTxt::T( 'TAGGED_IN_TYPE_TITLE', 'tagged in [type] [title]', array( '[type]' => $typeTranslated, '[title]' => $titleModal ) );
						}
					}
					break;
				case 'comment':
					if ( $owner ) {
						if ( $folder ) {
							$title		=	CBTxt::T( 'COMMENTED_ON_YOUR_TYPE_TITLE_IN_ALBUM', 'commented on your [type] [title] in album [album]', array( '[type]' => $typeTranslated, '[title]' => $titleModal, '[album]' => $folder ) );
						} else {
							$title		=	CBTxt::T( 'COMMENTED_ON_YOUR_TYPE_TITLE', 'commented on your [type] [title]', array( '[type]' => $typeTranslated, '[title]' => $titleModal ) );
						}
					} else {
						if ( $folder ) {
							$title		=	CBTxt::T( 'COMMENTED_ON_TYPE_TITLE_IN_ALBUM', 'commented on [type] [title] in album [album]', array( '[type]' => $typeTranslated, '[title]' => $titleModal, '[album]' => $folder ) );
						} else {
							$title		=	CBTxt::T( 'COMMENTED_ON_TYPE_TITLE', 'commented on [type] [title]', array( '[type]' => $typeTranslated, '[title]' => $titleModal ) );
						}
					}
					break;
				default:
					if ( $folder ) {
						$title			=	CBTxt::T( 'SHARED_TYPE_TITLE_IN_ALBUM', 'shared [type] [title] in album [album]', array( '[type]' => $typeTranslated, '[title]' => $titleModal, '[album]' => $folder ) );
					} else {
						$title			=	CBTxt::T( 'SHARED_ON_TYPE_TITLE', 'shared [type] [title]', array( '[type]' => $typeTranslated, '[title]' => $titleModal ) );
					}
					break;
			}
		} else {
			switch ( $type ) {
				case 'photos':
				case 'files':
				case 'videos':
					if ( $folder ) {
						if ( count( $items ) > 1 ) {
							$title		=	CBTxt::T( 'SHARED_COUNT_TYPES_IN_ALBUM', 'shared [types] in album [album]', array( '[types]' => $typeModal, '[album]' => $folder ) );
						} else {
							$title		=	CBTxt::T( 'SHARED_A_TYPE_IN_ALBUM', 'shared a [type] in album [album]', array( '[type]' => $typeModal, '[album]' => $folder ) );
						}
					} else {
						if ( count( $items ) > 1 ) {
							$title		=	CBTxt::T( 'SHARED_COUNT_TYPES', 'shared [types]', array( '[types]' => $typeModal ) );
						} else {
							$title		=	CBTxt::T( 'SHARED_A_TYPE', 'shared a [type]', array( '[type]' => $typeModal ) );
						}
					}
					break;
				case 'music':
					$title				=	CBTxt::T( 'SHARED_TYPE', 'shared [type]', array( '[type]' => $typeModal ) );
					break;
			}

			$insert						=	$return;
		}
	}
}