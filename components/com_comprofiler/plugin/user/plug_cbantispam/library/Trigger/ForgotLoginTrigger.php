<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AntiSpam\Trigger;

use CB\Plugin\AntiSpam\CBAntiSpam;
use CB\Plugin\AntiSpam\Table\BlockTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CB\Plugin\AntiSpam\Captcha;
use CB\Database\Table\UserTable;

defined('CBLIB') or die();

class ForgotLoginTrigger extends \cbPluginHandler
{

	/**
	 * Blocks a forgot login attempt
	 *
	 * @param string|BlockTable $block
	 */
	private function blockForgotLogin( $block )
	{
		global $_PLUGINS;

		if ( is_object( $block ) ) {
			if ( $block instanceof BlockTable ) {
				if ( $block->expired() ) {
					return;
				}

				$reason		=	$block->get( 'reason', null, GetterInterface::STRING );
				$duration	=	$block->get( 'duration', null, GetterInterface::STRING );
				$date		=	$block->get( 'date', null, GetterInterface::STRING );
				$expire		=	$block->expiry();
			} else {
				return;
			}
		} else {
			$reason			=	$block;
			$duration		=	null;
			$date			=	null;
			$expire			=	null;
		}

		if ( ! $reason ) {
			$reason			=	'Spam.';
		}

		$extras				=	array(	'[duration]' => ucwords( strtolower( str_replace( array( '+', '-' ), '', $duration ) ) ),
										'[date]' => $date . ' UTC',
										'[expire]' => $expire . ( $duration ? ' UTC' : null )
									);

		$extras				=	array_merge( $extras, array( '[reason]' => CBTxt::T( 'FORGOT_BLOCK_REDIRECT_REASON', $reason, $extras ) ) );

		$redirect			=	$this->params->get( 'forgot_block_redirect', null, GetterInterface::STRING );
		$redirectMsg		=	CBTxt::T( 'FORGOT_BLOCK_REDIRECT_MSG', $this->params->get( 'forgot_block_redirect_msg', 'Your forgot login attempt has been blocked. Reason: [reason]', GetterInterface::STRING ), $extras );
		$redirectType		=	$this->params->get( 'forgot_block_redirect_type', 'error', GetterInterface::STRING );

		if ( ! $redirect ) {
			$redirect		=	'index.php';
		}

		cbRedirect( $redirect, $redirectMsg, $redirectType );

		$_PLUGINS->_setErrorMSG( $redirectMsg );
		$_PLUGINS->raiseError();
	}

	/**
	 * Displays legacy forgot login captcha
	 *
	 * @return array|null
	 */
	public function displayCaptcha()
	{
		if ( ! $this->params->get( 'captcha_legacy_forgot', false, GetterInterface::BOOLEAN ) ) {
			return null;
		}

		$captcha	=	new Captcha( 'login.forgot', $this->params->get( 'captcha_legacy_forgot_mode', null, GetterInterface::STRING ) );

		return array( null, $captcha->captcha() . $captcha->input() );
	}

	/**
	 * Handles forgot login blocking, attempts log, and captcha validation
	 */
	public function beforeForgotLogin()
	{
		global $_PLUGINS;

		$blocked		=	CBAntiSpam::getBlock();

		if ( $blocked ) {
			$this->blockForgotLogin( $blocked );
			return;
		}

		$blocked		=	CBAntiSpam::logAttempt( 'forgot' );

		if ( $blocked ) {
			$this->blockForgotLogin( $blocked );
			return;
		}

		if ( $this->params->get( 'captcha_legacy_forgot', false, GetterInterface::BOOLEAN ) ) {
			$captcha	=	new Captcha( 'login.forgot' );

			if ( ( ! $captcha->load() ) || ! ( $captcha->validate() ) ) {
				$_PLUGINS->_setErrorMSG( ( $captcha->error() ? $captcha->error() : CBTxt::T( 'Invalid Captcha Code' ) ) );
				$_PLUGINS->raiseError();
			}
		}
	}

	/**
	 * Updates users ip address log and clears attempts
	 *
	 * @param UserTable $user
	 * @param bool      $res
	 */
	public function afterForgotLoginUsername( $user, &$res )
	{
		if ( ! $res ) {
			return;
		}

		CBAntiSpam::logIPAddress( $user, 'forgot' );
	}

	/**
	 * Updates users ip address log and clears attempts
	 *
	 * @param UserTable $user
	 * @param string    $newpass
	 * @param bool      $res
	 */
	public function afterForgotLoginPassword( $user, $newpass, &$res )
	{
		if ( ! $res ) {
			return;
		}

		CBAntiSpam::logIPAddress( $user, 'forgot' );
	}
}