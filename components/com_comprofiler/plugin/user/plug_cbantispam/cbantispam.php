<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\AntiSpam\CBAntiSpam;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'mod_onCBAdminMenu', 'adminMenu', '\CB\Plugin\AntiSpam\Trigger\AdminTrigger' );

$_PLUGINS->registerFunction( 'onPrepareMenus', 'getNotifications','\CB\Plugin\AntiSpam\Trigger\MenuTrigger' );

// Login
$_PLUGINS->registerFunction( 'onAfterLoginForm', 'displayCaptcha', '\CB\Plugin\AntiSpam\Trigger\LoginTrigger' );
$_PLUGINS->registerFunction( 'onBeforeLogin', 'beforeLogin', '\CB\Plugin\AntiSpam\Trigger\LoginTrigger' );
$_PLUGINS->registerFunction( 'onDuringLogin', 'duringLogin', '\CB\Plugin\AntiSpam\Trigger\LoginTrigger' );
$_PLUGINS->registerFunction( 'onAfterLogin', 'afterLogin', '\CB\Plugin\AntiSpam\Trigger\LoginTrigger' );

// Forgot Login
$_PLUGINS->registerFunction( 'onLostPassForm', 'displayCaptcha', '\CB\Plugin\AntiSpam\Trigger\ForgotLoginTrigger' );
$_PLUGINS->registerFunction( 'onStartNewPassword', 'beforeForgotLogin', '\CB\Plugin\AntiSpam\Trigger\ForgotLoginTrigger' );
$_PLUGINS->registerFunction( 'onAfterUsernameReminder', 'afterForgotLoginUsername', '\CB\Plugin\AntiSpam\Trigger\ForgotLoginTrigger' );
$_PLUGINS->registerFunction( 'onAfterPasswordReminder', 'afterForgotLoginPassword', '\CB\Plugin\AntiSpam\Trigger\ForgotLoginTrigger' );

// Registration
$_PLUGINS->registerFunction( 'onStartSaveUserRegistration', 'beforeRegistration', '\CB\Plugin\AntiSpam\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onBeforeUserRegistration', 'duringRegistration', '\CB\Plugin\AntiSpam\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterSaveUserRegistration', 'afterRegistration', '\CB\Plugin\AntiSpam\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterEmailUserForm', 'displayEmailCaptcha', '\CB\Plugin\AntiSpam\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onBeforeEmailUser', 'validateEmailCaptcha', '\CB\Plugin\AntiSpam\Trigger\UserTrigger' );

// Legacy Captcha
$_PLUGINS->registerFunction( 'onGetCaptchaHtmlElements', 'displayCaptcha', '\CB\Plugin\AntiSpam\Trigger\CaptchaTrigger' );
$_PLUGINS->registerFunction( 'onCheckCaptchaHtmlElements', 'validateCaptcha', '\CB\Plugin\AntiSpam\Trigger\CaptchaTrigger' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array(	'antispam_ipaddress'	=>	'\CB\Plugin\AntiSpam\Field\IPAddressField',
											'antispam_captcha'		=>	'\CB\Plugin\AntiSpam\Field\CaptchaField'
										));

class cbantispamTab extends cbTabHandler
{

	/**
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @return null|string
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		global $_CB_framework, $_CB_database;

		if ( ( ! Application::MyUser()->isGlobalModerator() ) || Application::User( $user->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) {
			return null;
		}

		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params			=	new Registry( $tab->params );
		}

		$blocksEnabled				=	( $this->params->get( 'general_block', true, GetterInterface::BOOLEAN ) && $tab->params->get( 'tab_block', ( $this instanceof cbantispamTabBlocks ? true : false ), GetterInterface::BOOLEAN ) );
		$whitelistsEnabled			=	( $this->params->get( 'general_whitelist', true, GetterInterface::BOOLEAN ) && $tab->params->get( 'tab_whitelist', ( $this instanceof cbantispamTabWhitelists ? true : false ), GetterInterface::BOOLEAN ) );
		$attemptsEnabled			=	( $this->params->get( 'general_attempts', true, GetterInterface::BOOLEAN ) && $tab->params->get( 'tab_attempts', ( $this instanceof cbantispamTabAttempts ? true : false ), GetterInterface::BOOLEAN ) );
		$logsEnabled				=	( $this->params->get( 'general_log', true, GetterInterface::BOOLEAN ) && $tab->params->get( 'tab_logs', ( $this instanceof cbantispamTabLog ? true : false ), GetterInterface::BOOLEAN ) );

		if ( ( ! $blocksEnabled ) && ( ! $whitelistsEnabled ) && ( ! $attemptsEnabled ) && ( ! $logsEnabled ) ) {
			return null;
		}

		$tabPrefix				=	'tab_' . $tab->get( 'tabid', 0, GetterInterface::INT ) . '_';
		$viewer					=	CBuser::getMyUserDataInstance();

		outputCbJs();
		outputCbTemplate();

		CBAntiSpam::getTemplate( 'tab' );

		$ipAddress				=	CBAntiSpam::getUserIP( $user );
		$emailDomain			=	CBAntiSpam::getEmailDomain( $user );

		$blocks					=	null;

		if ( $blocksEnabled ) {
			CBAntiSpam::getTemplate( 'blocks' );

			$blocksPrefix		=	$tabPrefix . 'blocks_';
			$limit				=	$tab->params->get( 'tab_limit', 15, GetterInterface::INT );
			$limitstart			=	$_CB_framework->getUserStateFromRequest( $blocksPrefix . 'limitstart{com_comprofiler}', $blocksPrefix . 'limitstart' );

			$query				=	"SELECT COUNT(*)"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_block' )
								.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
								.	" AND " . $_CB_database->NameQuote( 'value' ) . " = " . $user->get( 'id', 0, GetterInterface::INT ) . " )"
								.	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'email' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " ) )";
			if ( $ipAddress ) {
				$query			.=	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $ipAddress ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $ipAddress ) . " ) )"
								.	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip_range' )
								.	" AND ( INET_ATON( " . $_CB_database->Quote( $ipAddress ) . " ) BETWEEN INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', 1 ) ) AND INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', -1 ) ) ) )";
			}
			if ( $emailDomain ) {
				$query			.=	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'domain' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $emailDomain ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $emailDomain ) . " ) )";
			}
			$_CB_database->setQuery( $query );
			$total				=	$_CB_database->loadResult();

			if ( $total <= $limitstart ) {
				$limitstart		=	0;
			}

			$pageNav			=	new cbPageNav( $total, $limitstart, $limit );

			$pageNav->setInputNamePrefix( $blocksPrefix );

			$rows				=	array();

			if ( $total ) {
				$query			=	"SELECT *"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_block' )
								.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
								.	" AND " . $_CB_database->NameQuote( 'value' ) . " = " . $user->get( 'id', 0, GetterInterface::INT ) . " )"
								.	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'email' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " ) )";
				if ( $ipAddress ) {
					$query		.=	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $ipAddress ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $ipAddress ) . " ) )"
								.	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip_range' )
								.	" AND ( INET_ATON( " . $_CB_database->Quote( $ipAddress ) . " ) BETWEEN INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', 1 ) ) AND INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', -1 ) ) ) )";
				}
				if ( $emailDomain ) {
					$query		.=	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'domain' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $emailDomain ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $emailDomain ) . " ) )";
				}
				$query			.=	"\n ORDER BY " . $_CB_database->NameQuote( 'id' ) . " ASC";
				if ( $tab->params->get( 'tab_paging', true, GetterInterface::BOOLEAN ) ) {
					$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
				} else {
					$_CB_database->setQuery( $query );
				}
				$rows			=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AntiSpam\Table\BlockTable', array( $_CB_database ) );
			}

			$blocks				=	HTML_cbantispamBlocks::showBlocks( $rows, $pageNav, $viewer, $user, $tab, $this );
		}

		$whitelists				=	null;

		if ( $whitelistsEnabled ) {
			CBAntiSpam::getTemplate( 'whitelists' );

			$whitelistsPrefix	=	$tabPrefix . 'whitelists_';
			$limit				=	$tab->params->get( 'tab_limit', 15, GetterInterface::INT );
			$limitstart			=	$_CB_framework->getUserStateFromRequest( $whitelistsPrefix . 'limitstart{com_comprofiler}', $whitelistsPrefix . 'limitstart' );

			$query				=	"SELECT COUNT(*)"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_whitelist' )
								.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
								.	" AND " . $_CB_database->NameQuote( 'value' ) . " = " . $user->get( 'id', 0, GetterInterface::INT ) . " )"
								.	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'email' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " ) )";
			if ( $ipAddress ) {
				$query			.=	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $ipAddress ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $ipAddress ) . " ) )"
								.	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip_range' )
								.	" AND ( INET_ATON( " . $_CB_database->Quote( $ipAddress ) . " ) BETWEEN INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', 1 ) ) AND INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', -1 ) ) ) )";
			}
			if ( $emailDomain ) {
				$query			.=	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'domain' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $emailDomain ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $emailDomain ) . " ) )";
			}
			$_CB_database->setQuery( $query );
			$total				=	$_CB_database->loadResult();

			if ( $total <= $limitstart ) {
				$limitstart		=	0;
			}

			$pageNav			=	new cbPageNav( $total, $limitstart, $limit );

			$pageNav->setInputNamePrefix( $whitelistsPrefix );

			$rows				=	array();

			if ( $total ) {
				$query			=	"SELECT *"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_whitelist' )
								.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
								.	" AND " . $_CB_database->NameQuote( 'value' ) . " = " . $user->get( 'id', 0, GetterInterface::INT ) . " )"
								.	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'email' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " ) )";
				if ( $ipAddress ) {
					$query		.=	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $ipAddress ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $ipAddress ) . " ) )"
								.	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip_range' )
								.	" AND ( INET_ATON( " . $_CB_database->Quote( $ipAddress ) . " ) BETWEEN INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', 1 ) ) AND INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', -1 ) ) ) )";
				}
				if ( $emailDomain ) {
					$query		.=	" OR ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'domain' )
								.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
								.	" AND " . $_CB_database->Quote( $emailDomain ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
								.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $emailDomain ) . " ) )";
				}
				$query			.=	"\n ORDER BY " . $_CB_database->NameQuote( 'id' ) . " ASC";
				if ( $tab->params->get( 'tab_paging', true, GetterInterface::BOOLEAN ) ) {
					$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
				} else {
					$_CB_database->setQuery( $query );
				}
				$rows			=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AntiSpam\Table\WhitelistTable', array( $_CB_database ) );
			}

			$whitelists			=	HTML_cbantispamWhitelists::showWhitelists( $rows, $pageNav, $viewer, $user, $tab, $this );
		}

		$attempts				=	null;

		if ( $attemptsEnabled ) {
			CBAntiSpam::getTemplate( 'attempts' );

			$attemptsPrefix		=	$tabPrefix . 'attempts_';
			$limit				=	$tab->params->get( 'tab_limit', 15, GetterInterface::INT );
			$limitstart			=	$_CB_framework->getUserStateFromRequest( $attemptsPrefix . 'limitstart{com_comprofiler}', $attemptsPrefix . 'limitstart' );
			$total				=	0;

			if ( $ipAddress ) {
				$query			=	"SELECT COUNT(*)"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
				$_CB_database->setQuery( $query );
				$total			=	$_CB_database->loadResult();
			}

			if ( $total <= $limitstart ) {
				$limitstart		=	0;
			}

			$pageNav			=	new cbPageNav( $total, $limitstart, $limit );

			$pageNav->setInputNamePrefix( $attemptsPrefix );

			$rows				=	array();

			if ( $ipAddress && $total ) {
				$query			=	"SELECT *"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress )
								.	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
				if ( $tab->params->get( 'tab_paging', true, GetterInterface::BOOLEAN ) ) {
					$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
				} else {
					$_CB_database->setQuery( $query );
				}
				$rows			=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AntiSpam\Table\AttemptTable', array( $_CB_database ) );
			}

			$attempts			=	HTML_cbantispamAttempts::showAttempts( $rows, $pageNav, $viewer, $user, $tab, $this );
		}

		$logs					=	null;

		if ( $logsEnabled ) {
			CBAntiSpam::getTemplate( 'logs' );

			$logsPrefix			=	$tabPrefix . 'logs_';
			$limit				=	$tab->params->get( 'tab_limit', 15, GetterInterface::INT );
			$limitstart			=	$_CB_framework->getUserStateFromRequest( $logsPrefix . 'limitstart{com_comprofiler}', $logsPrefix . 'limitstart' );

			$query				=	"SELECT COUNT(*)"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
			$_CB_database->setQuery( $query );
			$total				=	$_CB_database->loadResult();

			if ( $total <= $limitstart ) {
				$limitstart		=	0;
			}

			$pageNav			=	new cbPageNav( $total, $limitstart, $limit );

			$pageNav->setInputNamePrefix( $logsPrefix );

			$rows				=	array();

			if ( $total ) {
				$query			=	"SELECT *"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
								.	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
				if ( $tab->params->get( 'tab_paging', true, GetterInterface::BOOLEAN ) ) {
					$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
				} else {
					$_CB_database->setQuery( $query );
				}
				$rows			=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AntiSpam\Table\LogTable', array( $_CB_database ) );
			}

			$logs				=	HTML_cbantispamLogs::showLogs( $rows, $pageNav, $viewer, $user, $tab, $this );
		}

		$class					=	$this->params->get( 'general_class', null );

		$return					=	'<div class="cbAntiSpam' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
								.		HTML_cbantispamTab::showTab( $blocks, $whitelists, $attempts, $logs, $viewer, $user, $tab, $this )
								.	'</div>';

		return $return;
	}
}

class cbantispamTabBlocks extends cbantispamTab {}

class cbantispamTabWhitelists extends cbantispamTab {}

class cbantispamTabAttempts extends cbantispamTab {}

class cbantispamTabLog extends cbantispamTab {}