<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AntiSpam\Trigger;

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

		$antispamMenu				=	array();

		$antispamMenu['component']	=	array(	'title' => CBTxt::T( 'AntiSpam' ) );
		$antispamMenu['menu']		=	array(	array(	'title' => CBTxt::T( 'Blocks' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showblocks', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbantispam-blocks',
														'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Block' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'blockbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showblocks' ) ) ), 'icon' => 'cb-new' ) )
												),
												array(	'title' => CBTxt::T( 'Whitelists' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showwhitelists', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbantispam-whitelists',
														'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Whitelist' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'whitelistbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showwhitelists' ) ) ), 'icon' => 'cb-new' ) )
												),
												array(	'title' => CBTxt::T( 'Attempts' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showattempts', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbantispam-attempts' ),
												array(	'title' => CBTxt::T( 'Logs' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showlogs', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbantispam-logs' ),
												array(	'title' => CBTxt::T( 'Configuration' ), 'link' => $_CB_framework->backendViewUrl( 'editrow', true, array( 'table' => 'pluginsbrowser', 'action' => 'editrow', 'cid' => $this->getPluginId(), 'cbprevstate' => base64_encode( 'option=com_comprofiler&view=showPlugins' ) ) ), 'icon' => 'cbantispam-config' )
											);

		$menu['antispam']			=	$antispamMenu;
	}
}