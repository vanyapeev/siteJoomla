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
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\Comments;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\ParamsInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityStreamAttachments
{

	/**
	 * @param ActivityTable|CommentTable|NotificationTable $row
	 * @param UserTable                                    $viewer
	 * @param Activity|Comments|Notifications              $stream
	 * @param cbPluginHandler                              $plugin
	 * @param string                                       $output
	 * @return string
	 */
	static public function showAttachments( $row, $viewer, $stream, $plugin, $output = null )
	{
		global $_PLUGINS;

		$links								=	$row->attachments();

		$_PLUGINS->trigger( 'activity_onDisplayStreamAttachments', array( &$row, &$links, $viewer, $stream, $output ) );

		$count								=	$links->count();

		if ( ! $count ) {
			return null;
		}

		$cbUser								=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false );
		$return								=	null;

		if ( $count > 1 ) {
			$return							.=	'<div class="streamItemScroll">'
											.		'<div class="streamItemScrollLeft">'
											.			'<table>'
											.				'<tr>'
											.					'<td>'
											.						'<span class="streamItemScrollLeftIcon fa fa-chevron-left"></span>'
											.					'</td>'
											.				'</tr>'
											.			'</table>'
											.		'</div>';
		}

		foreach ( $links as $i => $link ) {
			/** @var ParamsInterface $link */
			$type							=	$link->get( 'type', 'url', GetterInterface::STRING );

			$external						=	( ( ! $link->get( 'internal', false, GetterInterface::BOOLEAN ) ) || ( $type == 'image' ) ? ' target="_blank" rel="nofollow noopener"' : null );

			$return							.=		'<div class="streamItemAttachment' . ( $count > 1 ? ' streamItemScrollContent' : null ) . ( $i != 0 ? ' hidden' : null ) . '">'
											.			'<div class="' . ( $type == 'url' ? 'streamMedia media bg-muted' : 'streamPanel panel' ) . '">';

			if ( $link->get( 'media', null, GetterInterface::RAW ) !== false ) {
				/** @var ParamsInterface $media */
				$media						=	$link->subTree( 'media' );

				$return						.=				'<div class="streamItemAttachmentMedia ' . ( $type == 'url' ? 'streamMediaLeft media-left' : 'streamPanelBody panel-body' ) . '">';

				switch ( $type ) {
					case 'custom':
						$return				.=				$cbUser->replaceUserVars( $media->get( 'custom', null, GetterInterface::RAW ), false, false, null, false );
						break;
					case 'video':
						if ( in_array( $media->get( 'mimetype', null, GetterInterface::STRING ), array( 'video/youtube', 'video/x-youtube' ) ) ) {
							if ( preg_match( '%(?:(?:watch\?v=)|(?:embed/)|(?:be/))([A-Za-z0-9_-]+)%', $media->get( 'url', null, GetterInterface::STRING ), $matches ) ) {
								$return		.=					'<iframe width="100%" height="360" src="https://www.youtube.com/embed/' . htmlspecialchars( $matches[1] ) . '" frameborder="0" allowfullscreen class="streamItemVideo"></iframe>';
							}
						} else {
							$return			.=					'<video width="100%" height="100%" style="width: 100%; height: 100%;" src="' . htmlspecialchars( $media->get( 'url', null, GetterInterface::STRING ) ) . '" type="' . htmlspecialchars( $media->get( 'mimetype', null, GetterInterface::STRING ) ) . '" controls="controls" preload="auto" class="streamItemVideo"></video>';
						}
						break;
					case 'audio':
						$return				.=					'<audio style="width: 100%;" src="' . htmlspecialchars( $media->get( 'url', null, GetterInterface::STRING ) )  . '" type="' . htmlspecialchars( $media->get( 'mimetype', null, GetterInterface::STRING ) ) . '" controls="controls" preload="auto" class="streamItemAudio"></audio>';
						break;
					case 'image':
						$return				.=					'<img src="' . htmlspecialchars( $media->get( 'url', null, GetterInterface::STRING ) ) . '" class="img-responsive streamItemImage" />';
						break;
					case 'file':
						$mediaFilename		=	$media->get( 'filename', null, GetterInterface::STRING );

						if ( ! $mediaFilename ) {
							$mediaFilename	=	$link->get( 'url', null, GetterInterface::STRING );
						}

						$return				.=					'<table class="streamItemFile table table-bordered">'
											.						'<tbody>'
											.							'<tr>'
											.								'<th style="width: 125px;">' . CBTxt::T( 'File' ) . '</th>'
											.								'<td><a href="' . htmlspecialchars( $link->get( 'url', null, GetterInterface::STRING ) ) . '" target="_blank" rel="nofollow noopener">' . htmlspecialchars( $mediaFilename ) . '</a></td>'
											.							'</tr>'
											.							'<tr>'
											.								'<th style="width: 125px;">' . CBTxt::T( 'Extension' ) . '</th>'
											.								'<td>' . htmlspecialchars( strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', $media->get( 'extension', null, GetterInterface::STRING ) ) ) ) . '</td>'
											.							'</tr>'
											.							'<tr>'
											.								'<th style="width: 125px;">' . CBTxt::T( 'Size' ) . '</th>'
											.								'<td>' . CBActivity::getFormattedFileSize( $media->get( 'filesize', 0, GetterInterface::INT ) ) . '</td>'
											.							'</tr>'
											.							'<tr>'
											.								'<th style="width: 125px;">' . CBTxt::T( 'Modified' ) . '</th>'
											.								'<td>' . ( $link->get( 'date', null, GetterInterface::STRING ) ? cbFormatDate( $link->get( 'date', null, GetterInterface::STRING ) ) : '-' ) . '</td>'
											.							'</tr>'
											.						'</tbody>'
											.					'</table>';
						break;
					case 'url':
					default:
						if ( ( $output == 'edit' ) && ( $link->get( 'thumbnails', null, GetterInterface::RAW ) !== false ) ) {
							/** @var ParamsInterface $thumbnails */
							$thumbnails		=	$link->subTree( 'thumbnails' );

							if ( $thumbnails->count() > 1 ) {
								$selected	=	$link->get( 'selected', 0, GetterInterface::INT );

								$return		.=					'<div class="streamItemScroll">';

								foreach ( $thumbnails as $t => $thumbnail ) {
									/** @var ParamsInterface $thumbnail */
									$return	.=						'<div class="streamItemScrollContent' . ( $t != $selected ? ' hidden' : null ) . '">'
											.							'<a href="' . htmlspecialchars( $link->get( 'url', null, GetterInterface::STRING ) ) . '"' . $external . '>'
											.								'<img src="' . htmlspecialchars( $thumbnail->get( 'url', null, GetterInterface::STRING ) ) . '" class="img-responsive streamItemImage" />'
											.							'</a>'
											.							'<input type="hidden" name="links[' . $i . '][selected]" value="' . $t . '" class="streamItemScrollDisable"' . ( $t != $selected ? ' disabled="disabled"' : null ) . ' />'
											.						'</div>';
								}

								$return		.=						'<div class="streamItemScroller text-center">'
											.							'<span class="streamItemScrollLeft"><span class="streamItemScrollLeftIcon fa fa-chevron-left"></span></span>'
											.							' <span class="streamItemScrollRight"><span class="streamItemScrollRightIcon fa fa-chevron-right"></span></span>'
											.						'</div>'
											.					'</div>';
							} else {
								$return		.=					'<a href="' . htmlspecialchars( $link->get( 'url', null, GetterInterface::STRING ) ) . '"' . $external . '>'
											.						'<img src="' . htmlspecialchars( $media->get( 'url', null, GetterInterface::STRING ) ) . '" class="img-responsive streamItemImage" />'
											.					'</a>';
							}
						} else {
							$return			.=					'<a href="' . htmlspecialchars( $link->get( 'url', null, GetterInterface::STRING ) ) . '"' . $external . '>'
											.						'<img src="' . htmlspecialchars( $media->get( 'url', null, GetterInterface::STRING ) ) . '" class="img-responsive streamItemImage" />'
											.					'</a>';
						}
						break;
				}

				$return						.=				'</div>';
			}

			if ( $type != 'custom' ) {
				if ( $link->get( 'title', null, GetterInterface::STRING ) || $link->get( 'description', null, GetterInterface::STRING ) ) {
					$return					.=				'<div class="streamPanelFooter streamItemDisplay streamItemAttachmentInfo' . ( $type == 'url' ? ' streamMediaBody media-body' : ' panel-footer bg-muted' ) . '">'
											.					'<div class="cbMoreLess">'
											.						'<div class="cbMoreLessContent">'
											.							( $link->get( 'title', null, GetterInterface::STRING ) ? '<div><strong><a href="' . htmlspecialchars( $link->get( 'url', null, GetterInterface::STRING ) ) . '"' . $external . '>' . htmlspecialchars( $link->get( 'title', null, GetterInterface::STRING ) ) . '</a></strong></div>' : null )
											.							htmlspecialchars( $link->get( 'description', null, GetterInterface::STRING ) )
											.						'</div>'
											.						'<div class="streamItemAttachmentUrl text-small">'
											.							( ( ! $link->get( 'title', null, GetterInterface::STRING ) ) && ( ! in_array( $type, array( 'image', 'file' ) ) ) ? '<div><strong><a href="' . htmlspecialchars( $link->get( 'url', null, GetterInterface::STRING ) ) . '"' . $external . '>' . htmlspecialchars( $link->get( 'url', null, GetterInterface::STRING ) ) . '</a></strong></div>' : ( $count > 1 ? '&nbsp;' : null ) )
											.							( $count > 1 ? '<div class="streamItemAttachmentCount text-muted">' . ( $i + 1 ) . ' - ' . $count . '</div>' : null )
											.						'</div>'
											.						'<div class="cbMoreLessOpen fade-edge hidden">'
											.							'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
											.						'</div>'
											.					'</div>'
											.				'</div>';
				} elseif ( ( ! in_array( $type, array( 'image', 'file' ) ) ) || ( in_array( $type, array( 'image', 'file' ) ) && ( $count > 1 ) ) ) {
					$return					.=				'<div class="streamPanelFooter streamItemDisplay streamItemAttachmentInfo' . ( $type == 'url' ? ' streamMediaBody media-body' : ( ! in_array( $type, array( 'image', 'file' ) ) ? ' panel-footer bg-muted' : null ) ) . '">'
											.					'<div class="streamItemAttachmentUrl text-small">'
											.						( ! in_array( $type, array( 'image', 'file' ) ) ? '<div><strong><a href="' . htmlspecialchars( $link->get( 'url', null, GetterInterface::STRING ) ) . '"' . $external . '>' . htmlspecialchars( $link->get( 'url', null, GetterInterface::STRING ) ) . '</a></strong></div>' : '&nbsp;' )
											.						( $count > 1 ? '<div class="streamItemAttachmentCount text-muted">' . ( $i + 1 ) . ' - ' . $count . '</div>' : null )
											.					'</div>'
											.				'</div>';
				}

				if ( $output == 'edit' ) {
					$return					.=				'<div class="streamPanelFooter streamItemAttachmentInfo' . ( $type == 'url' ? ' streamMediaBody media-body' : ' panel-footer bg-muted' ) . '">'
											.					'<input type="text" name="links[' . $i . '][title]" value="' . htmlspecialchars( $link->get( 'title', null, GetterInterface::STRING ) ) . '" class="streamInput streamInputLinkTitle form-control" placeholder="' . htmlspecialchars( CBTxt::T( 'Title' ) ) . '" />'
											.					'<textarea name="links[' . $i . '][description]" rows="1" class="streamInput streamInputAutosize streamInputLinkDescription form-control" placeholder="' . htmlspecialchars( CBTxt::T( 'Description' ) ) . '">' . htmlspecialchars( $link->get( 'description', null, GetterInterface::STRING ) ) . '</textarea>';

					if ( $type == 'url' ) {
						$return				.=					'<div class="streamInput">'
											.						'<label class="checkbox-inline">'
											.							'<input type="checkbox" name="links[' . $i . '][thumbnail]" value="0"' . ( ! $link->get( 'thumbnail', true, GetterInterface::BOOLEAN ) ? ' checked="checked"' : null ) . ' /> ' . CBTxt::T( 'Do not display thumbnail' )
											.						'</label>'
											.					'</div>';
					}

					if ( $link->get( 'embedded', false, GetterInterface::BOOLEAN )  ) {
						$return				.=					'<input type="hidden" name="links[' . $i . '][embedded]" value="1" />';
					}

					$return					.=					'<input type="hidden" name="links[' . $i . '][parsed]" value="1" />'
											.				'</div>';
				}
			} elseif ( $count > 1 ) {
				$return						.=				'<div class="streamPanelFooter streamItemDisplay streamItemAttachmentInfo' . ( $type == 'url' ? ' streamMediaBody media-body' : null ) . '">'
											.					'<div class="streamItemAttachmentUrl text-small">'
											.						'&nbsp;<div class="streamItemAttachmentCount text-muted">' . ( $i + 1 ) . ' - ' . $count . '</div>'
											.					'</div>'
											.				'</div>';
			}

			$return							.=			'</div>'
											.		'</div>';
		}

		if ( $count > 1 ) {
			$return							.=		'<div class="streamItemScrollRight">'
											.			'<table>'
											.				'<tr>'
											.					'<td>'
											.						'<span class="streamItemScrollRightIcon fa fa-chevron-right"></span>'
											.					'</td>'
											.				'</tr>'
											.			'</table>'
											.		'</div>'
											.	'</div>';
		}

		return $return;
	}
}