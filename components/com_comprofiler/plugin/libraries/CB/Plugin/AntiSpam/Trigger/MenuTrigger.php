<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AntiSpam\Trigger;

use CB\Database\Table\UserTable;
use CB\Plugin\AntiSpam\CBAntiSpam;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class MenuTrigger extends \cbPluginHandler
{

	/**
	 * Displays frontend block and whitelist menu items on cb menu bar
	 *
	 * @param UserTable $user
	 */
	public function getNotifications( $user )
	{
		global $_CB_framework;

		if ( ( ! CBAntiSpam::checkBlockable( $user ) ) || ( ! Application::MyUser()->isGlobalModerator() ) ) {
			return;
		}

		$returnUrl						=	base64_encode( $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), false ) );

		if ( $this->params->get( 'general_block', true, GetterInterface::BOOLEAN ) ) {
			if ( $this->params->get( 'menu_block_account', true, GetterInterface::BOOLEAN ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_BLOCKACCOUNT' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Block Account' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'block', 'func' => 'account', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-ban"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Block this users account' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_block_user', true, GetterInterface::BOOLEAN ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_BLOCKUSER' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Block User' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'block', 'func' => 'user', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-ban"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Block this users id' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_block_ip', true, GetterInterface::BOOLEAN ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_BLOCKIP' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Block IP Address' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'block', 'func' => 'ip', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-ban"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Block this users IP Address' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_block_email', false, GetterInterface::BOOLEAN ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_BLOCKEMAIL' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Block Email Address' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'block', 'func' => 'email', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-ban"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Block this users Email Address' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_block_domain', false, GetterInterface::BOOLEAN ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_BLOCKDOMAIN' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Block Email Domain' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'block', 'func' => 'domain', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-ban"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Block this users Email Domain' ) );

				$this->addMenu( $menu );
			}
		}

		if ( $this->params->get( 'general_whitelist', true, GetterInterface::BOOLEAN ) ) {
			if ( $this->params->get( 'menu_whitelist_user', true, GetterInterface::BOOLEAN ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_WHITELISTUSER' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Whitelist User' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'whitelist', 'func' => 'user', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-shield"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Whitelist this users account' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_whitelist_ip', true, GetterInterface::BOOLEAN ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_WHITELISTIP' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Whitelist IP Address' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'whitelist', 'func' => 'ip', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-shield"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Whitelist this users IP Address' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_whitelist_email', false, GetterInterface::BOOLEAN ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_WHITELISTEMAIL' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Whitelist Email Address' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'whitelist', 'func' => 'email', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-shield"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Whitelist this users Email Address' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_whitelist_domain', false, GetterInterface::BOOLEAN ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_WHITELISTDOMAIN' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Whitelist Email Domain' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'whitelist', 'func' => 'domain', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-shield"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Whitelist this users Email Domain' ) );

				$this->addMenu( $menu );
			}
		}
	}
}