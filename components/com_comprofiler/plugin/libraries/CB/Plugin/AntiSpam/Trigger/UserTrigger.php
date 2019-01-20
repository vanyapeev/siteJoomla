<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AntiSpam\Trigger;

use CBLib\Application\Application;
use CB\Plugin\AntiSpam\CBAntiSpam;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\AntiSpam\Table\BlockTable;
use CBLib\Language\CBTxt;
use CB\Plugin\AntiSpam\Captcha;

defined('CBLIB') or die();

class UserTrigger extends \cbPluginHandler
{

	/**
	 * Blocks a registration attempt
	 *
	 * @param string|BlockTable $block
	 */
	private function blockRegistration( $block )
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

		$extras				=	array_merge( $extras, array( '[reason]' => CBTxt::T( 'REG_BLOCK_REDIRECT_REASON', $reason, $extras ) ) );

		$redirect			=	$this->params->get( 'reg_block_redirect', null, GetterInterface::STRING );
		$redirectMsg		=	CBTxt::T( 'REG_BLOCK_REDIRECT_MSG', $this->params->get( 'reg_block_redirect_msg', 'Your registration attempt has been blocked. Reason: [reason]', GetterInterface::STRING ), $extras );
		$redirectType		=	$this->params->get( 'reg_block_redirect_type', 'error', GetterInterface::STRING );

		if ( ! $redirect ) {
			$redirect		=	'index.php';
		}

		cbRedirect( $redirect, $redirectMsg, $redirectType );

		$_PLUGINS->_setErrorMSG( $redirectMsg );
		$_PLUGINS->raiseError();
	}

	/**
	 * Displays legacy user email form captcha
	 *
	 * @param UserTable $rowFrom
	 * @param UserTable $rowTo
	 * @param string    $warning
	 * @param int       $ui
	 * @param int       $allowPublic
	 * @param string    $name
	 * @param string    $email
	 * @param string    $subject
	 * @param string    $message
	 * @return null|string
	 */
	public function displayEmailCaptcha( &$rowFrom, &$rowTo, &$warning, $ui, &$allowPublic, &$name, &$email, &$subject, &$message )
	{
		if ( ! $this->params->get( 'captcha_legacy_email', false, GetterInterface::BOOLEAN ) ) {
			return null;
		}

		$captcha	=	new Captcha( 'email', $this->params->get( 'captcha_legacy_email_mode', null, GetterInterface::STRING ) );

		$return		=	$captcha->captcha()
					.	$captcha->input();

		return $return;
	}

	/**
	 * Validates legacy user email form captcha
	 *
	 * @param UserTable $rowFrom
	 * @param UserTable $rowTo
	 * @param int $ui
	 * @param string $emailName
	 * @param string $emailAddress
	 * @param string $subject
	 * @param string $message
	 */
	public function validateEmailCaptcha( &$rowFrom, &$rowTo, $ui, &$emailName, &$emailAddress, &$subject, &$message )
	{
		global $_PLUGINS;

		if ( ! $this->params->get( 'captcha_legacy_email', false, GetterInterface::BOOLEAN ) ) {
			return;
		}

		$captcha	=	new Captcha( 'email' );

		if ( ( ! $captcha->load() ) || ( ! $captcha->validate() ) ) {
			$_PLUGINS->_setErrorMSG( ( $captcha->error() ? $captcha->error() : CBTxt::T( 'Invalid Captcha Code' ) ) );
			$_PLUGINS->raiseError();
		}
	}

	/**
	 * Handles registration blocking (only ip address) and attempts log
	 */
	public function beforeRegistration()
	{
		$blocked		=	CBAntiSpam::getBlock();

		if ( $blocked ) {
			$this->blockRegistration( $blocked );
			return;
		}

		$blocked		=	CBAntiSpam::logAttempt( 'reg' );

		if ( $blocked ) {
			$this->blockRegistration( $blocked );
			return;
		}
	}

	/**
	 * Handles registration blocking (user and ip address) and duplicate registration check
	 *
	 * @param UserTable $user
	 * @param UserTable $userDuplicate
	 */
	public function duringRegistration( &$user, &$userDuplicate )
	{
		global $_CB_database;

		$ipAddress					=	CBAntiSpam::getCurrentIP();

		if ( ! CBAntiSpam::checkBlockable( $user, $ipAddress ) ) {
			return;
		}

		$blocked					=	CBAntiSpam::getBlock( $user, $ipAddress );

		if ( $blocked ) {
			$this->blockRegistration( $blocked );
			return;
		}

		if ( $this->params->get( 'reg_duplicate', false, GetterInterface::BOOLEAN ) ) {
			$timeframe				=	$this->params->get( 'reg_duplicate_timeframe', '-1 YEAR', GetterInterface::STRING );

			$query					=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' ) . " AS l"
									.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
									.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = l.' . $_CB_database->NameQuote( 'user_id' )
									.	"\n WHERE l." . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
			if ( $timeframe ) {
				$query				.=	"\n AND l." . $_CB_database->NameQuote( 'date' ) . " >= " . $_CB_database->Quote( Application::Date( 'now', 'UTC' )->modify( strtoupper( $timeframe ) )->format( 'Y-m-d H:i:s' ) );
			}
			$_CB_database->setQuery( $query );
			$accounts				=	$_CB_database->loadResult();

			$count					=	$this->params->get( 'reg_duplicate_count', 1, GetterInterface::INT );

			if ( ! $count ) {
				$count				=	1;
			}

			if ( $accounts >= $count ) {
				$method				=	$this->params->get( 'reg_duplicate_method', 0, GetterInterface::INT );
				$reason				=	$this->params->get( 'reg_duplicate_reason', 'Already registered.', GetterInterface::STRING );

				if ( $method == 1 ) {
					$row			=	new BlockTable();

					$row->set( 'type', 'ip' );
					$row->set( 'value', $ipAddress );
					$row->set( 'date', Application::Database()->getUtcDateTime() );
					$row->set( 'duration', $this->params->get( 'reg_duplicate_dur', '+1 HOUR', GetterInterface::STRING ) );
					$row->set( 'reason', $reason );

					$row->store();

					$this->blockRegistration( $row );
				} else {
					$this->blockRegistration( $reason );
				}
			}
		}
	}

	/**
	 * Updates users ip address log and clears attempts
	 *
	 * @param UserTable $userComplete
	 * @param array     $messagesToUser
	 * @param int       $ui
	 */
	public function afterRegistration( &$userComplete, &$messagesToUser, $ui )
	{
		CBAntiSpam::logIPAddress( $userComplete, 'reg' );
	}
}