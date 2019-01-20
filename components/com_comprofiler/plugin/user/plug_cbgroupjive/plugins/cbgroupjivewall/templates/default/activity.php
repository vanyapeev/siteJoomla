<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Plugin\GroupJiveWall\Table\WallTable;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CBLib\Input\Get;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveWallActivity
{

	/**
	 * render frontend wall activity
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
	 * @param WallTable                       $post
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static function showWallActivity( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $post, $plugin, $output )
	{
		global $_CB_framework, $_PLUGINS;

		$user						=	CBuser::getMyUserDataInstance();
		$postMessage				=	$post->post();
		$groupName					=	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => $post->group()->get( 'id', 0, GetterInterface::INT ) ) ) . '">' . htmlspecialchars( CBTxt::T( $post->group()->get( 'name', null, GetterInterface::STRING ) ) ) . '</a>';

		if ( $stream instanceof NotificationsInterface ) {
			$postMessage			=	Get::clean( $postMessage, GetterInterface::STRING );

			if ( cbutf8_strlen( $postMessage ) > 50 ) {
				$postMessage		=	trim( cbutf8_substr( $postMessage, 0, 50 ) ) . '...';
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) == $post->group()->get( 'user_id', 0, GetterInterface::INT ) ) {
				$title				=	CBTxt::T( 'POSTED_POST_IN_YOUR_GROUP', 'posted [post] in your group [group]', array( '[post]' => '<strong>' . $postMessage . '</strong>', '[group]' => $groupName ) );
			} else {
				$title				=	CBTxt::T( 'POSTED_POST_IN_GROUP', 'posted [post] in group [group]', array( '[post]' => '<strong>' . $postMessage . '</strong>', '[group]' => $groupName ) );
			}
		} else {
			if ( $stream->get( 'groupjive.ingroup', 0, GetterInterface::INT ) != $post->group()->get( 'id', 0, GetterInterface::INT ) ) {
				$title				=	CBTxt::T( 'POSTED_IN_GROUP', 'posted in group [group]', array( '[group]' => '<strong>' . $groupName . '</strong>' ) );
			}

			$message				=	$postMessage;
		}

		$_PLUGINS->trigger( 'gj_onAfterWallActivity', array( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $post, $plugin, $output ) );
	}
}