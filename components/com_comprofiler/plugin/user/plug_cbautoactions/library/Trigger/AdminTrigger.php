<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Trigger;

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

		$galleryMenu['component']	=	array(	'title' => CBTxt::T( 'Auto Actions' ) );
		$galleryMenu['menu']		=	array(	array(	'title' => CBTxt::T( 'Auto Action' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showautoactions', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbautoactions-autoactions',
														'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Auto Action' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'autoactionsbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showautoactions' ) ) ), 'icon' => 'cb-new' ) )
												),
												array(	'title' => CBTxt::T( 'System Actions' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showsystemactions', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbautoactions-systemactions' ),
												array(	'title' => CBTxt::T( 'Configuration' ), 'link' => $_CB_framework->backendViewUrl( 'editrow', true, array( 'table' => 'pluginsbrowser', 'action' => 'editrow', 'cid' => $this->getPluginId(), 'cbprevstate' => base64_encode( 'option=com_comprofiler&view=showPlugins' ) ) ), 'icon' => 'cbautoactions-config' )
											);

		$menu['autoactions']		=	$galleryMenu;
	}
}