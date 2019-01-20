<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Plugin\GroupJiveFile\Table\FileTable;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveFileActivity
{

	/**
	 * render frontend file activity
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
	 * @param FileTable                       $file
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static function showFileActivity( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $file, $plugin, $output )
	{
		global $_CB_framework, $_PLUGINS;

		$user						=	CBuser::getMyUserDataInstance();
		$fileName					=	( $file->get( 'title', null, GetterInterface::STRING ) ? htmlspecialchars( $file->get( 'title', null, GetterInterface::STRING ) ) : $file->name() );
		$groupName					=	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => $file->group()->get( 'id', 0, GetterInterface::INT ) ) ) . '">' . htmlspecialchars( CBTxt::T( $file->group()->get( 'name', null, GetterInterface::STRING ) ) ) . '</a>';

		if ( $stream instanceof NotificationsInterface ) {
			if ( cbutf8_strlen( $fileName ) > 50 ) {
				$fileName			=	trim( cbutf8_substr( $fileName, 0, 50 ) ) . '...';
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) == $file->group()->get( 'user_id', 0, GetterInterface::INT ) ) {
				$title				=	CBTxt::T( 'UPLOADED_FILE_IN_YOUR_GROUP', 'uploaded file [file] in your group [group]', array( '[file]' => '<strong>' . $fileName . '</strong>', '[group]' => $groupName ) );
			} else {
				$title				=	CBTxt::T( 'UPLOADED_FILE_IN_GROUP', 'uploaded file [file] in group [group]', array( '[file]' => '<strong>' . $fileName . '</strong>', '[group]' => $groupName ) );
			}
		} else {
			if ( $stream->get( 'groupjive.ingroup', 0, GetterInterface::INT ) == $file->group()->get( 'id', 0, GetterInterface::INT ) ) {
				$title				=	CBTxt::T( 'uploaded a file' );
			} else {
				$title				=	CBTxt::T( 'UPLOADED_A_FILE_IN_GROUP', 'uploaded a file in group [group]', array( '[group]' => '<strong>' . $groupName . '</strong>' ) );
			}

			$insert					=	'<div class="gjFileActivity">'
									.		'<table class="gjGroupFileRows table table-hover table-responsive">'
									.			'<thead>'
									.				'<tr>'
									.					'<th colspan="2">&nbsp;</th>'
									.					'<th style="width: 15%;" class="text-center">' . CBTxt::T( 'Type' ) . '</th>'
									.					'<th style="width: 15%;" class="text-left">' . CBTxt::T( 'Size' ) . '</th>'
									.				'</tr>'
									.			'</thead>'
									.			'<tbody>';

			$fileExtension			=	null;
			$fileSize				=	0;
			$item					=	$fileName;

			if ( $file->exists() ) {
				$downloadPath		=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'file', 'func' => 'download', 'id' => $file->get( 'id', 0, GetterInterface::INT ) ), 'raw', 0, true );
				$fileExtension		=	$file->extension();
				$fileSize			=	$file->size();

				switch ( $fileExtension ) {
					case 'txt':
					case 'pdf':
					case 'jpg':
					case 'jpeg':
					case 'png':
					case 'gif':
					case 'js':
					case 'css':
					case 'mp4':
					case 'mp3':
					case 'wav':
						$item		=	'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'file', 'func' => 'show', 'id' => $file->get( 'id', 0, GetterInterface::INT ) ), 'raw', 0, true ) . '" target="_blank">'
									.		$item
									.	'</a>';
						break;
					default:
						$item		=	'<a href="' . $downloadPath . '" target="_blank">'
									.		$item
									.	'</a>';
						break;
				}

				$download			=	'<a href="' . $downloadPath . '" target="_blank" title="' . htmlspecialchars( CBTxt::T( 'Click to Download' ) ) . '" class="gjGroupDownloadIcon btn btn-xs btn-default">'
									.		'<span class="fa fa-download"></span>'
									.	'</a>';
			} else {
				$download			=	'<button type="button" class="gjButton gjButtonDownloadFile btn btn-xs btn-default disabled">'
									.		'<span class="fa fa-download"></span>'
									.	'</button>';
			}

			if ( $file->get( 'description', null, GetterInterface::STRING ) ) {
				$item				.=	' ' . cbTooltip( 1, $file->get( 'description', null, GetterInterface::STRING ), $fileName, 400, null, '<span class="fa fa-info-circle text-muted"></span>' );
			}

			$insert					.=				'<tr>'
									.					'<td style="width: 1%;" class="text-center">' . $download . '</td>'
									.					'<td style="width: 45%;" class="gjGroupFileItem text-left">' . $item . '</td>'
									.					'<td style="width: 15%;" class="text-center"><span class="gjGroupFileTypeIcon fa fa-' . $file->icon() . '" title="' . htmlspecialchars( ( $fileExtension ? strtoupper( $fileExtension ) : CBTxt::T( 'Unknown' ) ) ) . '"></span></td>'
									.					'<td style="width: 15%;" class="text-left">' . $fileSize . '</td>'
									.				'</tr>'
									.			'</tbody>'
									.		'</table>'
									.	'</div>';
		}

		$_PLUGINS->trigger( 'gj_onAfterFileActivity', array( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $file, $plugin, $output ) );
	}
}