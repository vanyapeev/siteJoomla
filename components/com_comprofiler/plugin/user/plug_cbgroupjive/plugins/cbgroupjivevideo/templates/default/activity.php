<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Plugin\GroupJiveVideo\Table\VideoTable;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveVideoActivity
{

	/**
	 * render frontend video activity
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
	 * @param VideoTable                      $video
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static function showVideoActivity( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $video, $plugin, $output )
	{
		global $_CB_framework, $_PLUGINS;

		$user					=	CBuser::getMyUserDataInstance();
		$videoTitle				=	( $video->get( 'title', null, GetterInterface::STRING ) ? htmlspecialchars( $video->get( 'title', null, GetterInterface::STRING ) ) : $video->name() );
		$groupName				=	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => $video->group()->get( 'id', 0, GetterInterface::INT ) ) ) . '">' . htmlspecialchars( CBTxt::T( $video->group()->get( 'name', null, GetterInterface::STRING ) ) ) . '</a>';

		if ( $stream instanceof NotificationsInterface ) {
			if ( cbutf8_strlen( $videoTitle ) > 50 ) {
				$videoTitle		=	trim( cbutf8_substr( $videoTitle, 0, 50 ) ) . '...';
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) == $video->group()->get( 'user_id', 0, GetterInterface::INT ) ) {
				$title			=	CBTxt::T( 'PUBLISHED_VIDEO_IN_YOUR_GROUP', 'published video [video] in your group [group]', array( '[video]' => '<strong>' . $videoTitle . '</strong>', '[group]' => $groupName ) );
			} else {
				$title			=	CBTxt::T( 'PUBLISHED_VIDEO_IN_GROUP', 'published video [video] in group [group]', array( '[video]' => '<strong>' . $videoTitle . '</strong>', '[group]' => $groupName ) );
			}
		} else {
			if ( $stream->get( 'groupjive.ingroup', 0, GetterInterface::INT ) == $video->group()->get( 'id', 0, GetterInterface::INT ) ) {
				$title			=	CBTxt::T( 'published a video' );
			} else {
				$title			=	CBTxt::T( 'PUBLISHED_A_VIDEO_IN_GROUP', 'published a video in group [group]', array( '[group]' => '<strong>' . $groupName . '</strong>' ) );
			}

			$insert				=	'<div class="gjVideoActivity">'
								.		'<div class="gjGroupVideoRow gjCanvasBox cbCanvasBox cbCanvasBoxInline">'
								.			'<div class="gjCanvasBoxTop cbCanvasBoxTop">';

			if ( $video->mimeType() == 'video/youtube' ) {
				if ( preg_match( '%(?:(?:watch\?v=)|(?:embed/)|(?:be/))([A-Za-z0-9_-]+)%', $video->get( 'url', null, GetterInterface::STRING ), $matches ) ) {
					$insert		.=				'<iframe width="100%" height="360" src="https://www.youtube.com/embed/' . htmlspecialchars( $matches[1] ) . '" frameborder="0" allowfullscreen class="gjVideoPlayer"></iframe>';
				}
			} else {
				$insert			.=				'<video width="100%" height="100%" style="width: 100%; height: 100%;" src="' . htmlspecialchars( $video->get( 'url', null, GetterInterface::STRING ) ) . '" type="' . htmlspecialchars( $video->mimeType() ) . '" controls="controls" preload="auto" class="gjVideoPlayer"></video>';
			}

			$insert				.=			'</div>'
								.		'</div>'
								.	'</div>';
		}

		$_PLUGINS->trigger( 'gj_onAfterVideoActivity', array( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $video, $plugin, $output ) );
	}
}