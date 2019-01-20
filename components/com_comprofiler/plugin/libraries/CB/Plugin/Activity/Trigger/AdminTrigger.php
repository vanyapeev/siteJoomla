<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Trigger;

use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class AdminTrigger extends \cbPluginHandler
{

	/**
	 * Displays backend menu items
	 *
	 * @param array $menu
	 * @param bool  $disabled
	 */
	public function adminMenu( &$menu, $disabled )
	{
		global $_CB_framework;

		if ( ! $this->params->get( 'general_menu', true, GetterInterface::BOOLEAN ) ) {
			return;
		}

		$prevStateBase				=	'option=com_comprofiler&view=editPlugin&pluginid=' . $this->getPluginId();

		$galleryMenu				=	array();

		$galleryMenu['component']	=	array(	'title' => CBTxt::T( 'Activity' ) );
		$galleryMenu['menu']		=	array(	array(	'title' => CBTxt::T( 'Activity' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showactivity', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbactivity-activity' ),
												array(	'title' => CBTxt::T( 'Notifications' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showactivitynotifications', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbactivity-notifications' ),
												array(	'title' => CBTxt::T( 'Comments' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showactivitycomments', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbactivity-comments' ),
												array(	'title' => CBTxt::T( 'Hidden' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showhiddenactivity', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbactivity-hidden' ),
												array(	'title' => CBTxt::T( 'Following' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showactivityfollowing', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbactivity-following' ),
												array(	'title' => CBTxt::T( 'Likes' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showactivitylikes', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbactivity-likes' ),
												array(	'title' => CBTxt::T( 'Tags' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showactivitytags', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbactivity-tags' ),
												array(	'title' => CBTxt::T( 'Actions' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showactivityactions', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbactivity-actions',
														'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Action' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'activityactionsbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showactivityactions' ) ) ), 'icon' => 'cb-new' ) )
												),
												array(	'title' => CBTxt::T( 'Locations' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showactivitylocations', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbactivity-locations',
														'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Location' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'activitylocationsbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showactivitylocations' ) ) ), 'icon' => 'cb-new' ) )
												),
												array(	'title' => CBTxt::T( 'Emotes' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showactivityemotes', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbactivity-emotes',
														'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Emote' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'activityemotesbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showactivityemotes' ) ) ), 'icon' => 'cb-new' ) )
												),
												array(	'title' => CBTxt::T( 'Configuration' ), 'link' => $_CB_framework->backendViewUrl( 'editrow', true, array( 'table' => 'pluginsbrowser', 'action' => 'editrow', 'cid' => $this->getPluginId(), 'cbprevstate' => base64_encode( 'option=com_comprofiler&view=showPlugins' ) ) ), 'icon' => 'cbactivity-config' )
											);

		$menu['activity']			=	$galleryMenu;
	}
}