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
use CB\Database\Table\UserTable;
use CB\Plugin\AntiSpam\CBAntiSpam;
use CB\Plugin\AntiSpam\Table\BlockTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CB\Plugin\AntiSpam\Captcha;

defined('CBLIB') or die();

class LoginTrigger extends \cbPluginHandler
{

	/**
	 * Blocks a login attempt
	 *
	 * @param string|BlockTable $block
	 */
	private function blockLogin( $block )
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

		$extras				=	array_merge( $extras, array( '[reason]' => CBTxt::T( 'LOGIN_BLOCK_REDIRECT_REASON', $reason, $extras ) ) );

		$redirect			=	$this->params->get( 'login_block_redirect', null, GetterInterface::STRING );
		$redirectMsg		=	CBTxt::T( 'LOGIN_BLOCK_REDIRECT_MSG', $this->params->get( 'login_block_redirect_msg', 'Your login attempt has been blocked. Reason: [reason]', GetterInterface::STRING ), $extras );
		$redirectType		=	$this->params->get( 'login_block_redirect_type', 'error', GetterInterface::STRING );

		if ( ! $redirect ) {
			$redirect		=	'index.php';
		}

		cbRedirect( $redirect, $redirectMsg, $redirectType );

		$_PLUGINS->_setErrorMSG( $redirectMsg );
		$_PLUGINS->raiseError();
	}

	/**
	 * Displays legacy login captcha
	 *
	 * @return null|string
	 */
	public function displayCaptcha()
	{
		if ( ! $this->params->get( 'captcha_legacy_login', false, GetterInterface::BOOLEAN ) ) {
			return null;
		}

		$captcha	=	new Captcha( 'login', $this->params->get( 'captcha_legacy_login_mode', null, GetterInterface::STRING ) );

		$return		=	'<div class="cbLegacyLoginCaptcha cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
					.		$captcha->captcha()
					.		$captcha->input()
					.	'</div>';

		return $return;
	}

	/**
	 * Handles login blocking (only ip address), attempts log, and captcha validation
	 *
	 * @param string      $username
	 * @param string|bool $password
	 * @param  string     $secretKey
	 */
	public function beforeLogin( &$username, &$password, &$secretKey )
	{
		global $_PLUGINS;

		$blocked		=	CBAntiSpam::getBlock();

		if ( $blocked ) {
			$this->blockLogin( $blocked );
			return;
		}

		$blocked		=	CBAntiSpam::logAttempt();

		if ( $blocked ) {
			$this->blockLogin( $blocked );
			return;
		}

		if ( $this->params->get( 'captcha_legacy_login', false, GetterInterface::BOOLEAN ) && $username && $password ) {
			$captcha	=	new Captcha( 'login' );

			if ( ( ! $captcha->load() ) || ( ! $captcha->validate() ) ) {
				$_PLUGINS->_setErrorMSG( ( $captcha->error() ? $captcha->error() : CBTxt::T( 'Invalid Captcha Code' ) ) );
				$_PLUGINS->raiseError();
			}
		}
	}

	/**
	 * Handles login blocking (user and ip address), duplicate login check, and login share check
	 *
	 * @param UserTable   $user
	 * @param int         $foundUser
	 * @param null|string $returnPluginsOverrides
	 */
	public function duringLogin( &$user, $foundUser, &$returnPluginsOverrides )
	{
		global $_CB_database;

		$ipAddress					=	CBAntiSpam::getCurrentIP();

		if ( ! CBAntiSpam::checkBlockable( $user, $ipAddress ) ) {
			return;
		}

		$blocked					=	CBAntiSpam::getBlock( $user, $ipAddress );

		if ( $blocked ) {
			$this->blockLogin( $blocked );
			return;
		}

		if ( $this->params->get( 'login_duplicate', false, GetterInterface::BOOLEAN ) ) {
			$sessions				=	CBAntiSpam::countUserSessions( $user->get( 'id', 0, GetterInterface::INT ) );
			$count					=	$this->params->get( 'login_duplicate_count', 0, GetterInterface::INT );

			if ( ! $count ) {
				$count				=	1;
			}

			if ( $sessions >= $count ) {
				$method				=	$this->params->get( 'login_duplicate_method', 1, GetterInterface::INT );

				if ( $method > 0 ) {
					$reason			=	$this->params->get( 'login_duplicate_reason', 'Already logged in.', GetterInterface::STRING );

					if ( $method > 1 ) {
						$row		=	new BlockTable();

						if ( $method == 1 ) {
							$row->set( 'type', 'ip' );
							$row->set( 'value', $ipAddress );
						} else {
							$row->set( 'type', 'user' );
							$row->set( 'value', $user->get( 'id', 0, GetterInterface::INT ) );
						}

						$row->set( 'date', Application::Database()->getUtcDateTime() );
						$row->set( 'duration', $this->params->get( 'login_duplicate_dur', '+1 HOUR', GetterInterface::STRING ) );
						$row->set( 'reason', $reason );

						$row->store();

						$this->blockLogin( $row );
					} else {
						$this->blockLogin( $reason );
					}
				} else {
					$query			=	'DELETE'
									.	"\n FROM " . $_CB_database->NameQuote( '#__session' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
					$_CB_database->setQuery( $query );
					$_CB_database->query();
				}
			}
		}

		if ( $this->params->get( 'login_share', false, GetterInterface::BOOLEAN ) ) {
			$timeframe				=	$this->params->get( 'login_share_timeframe', '-1 MONTH', GetterInterface::STRING );

			$query					=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
			if ( $timeframe ) {
				$query				.=	"\n AND " . $_CB_database->NameQuote( 'date' ) . " >= " . $_CB_database->Quote( Application::Date( 'now', 'UTC' )->modify( strtoupper( $timeframe ) )->format( 'Y-m-d H:i:s' ) );
			}
			$_CB_database->setQuery( $query );
			$logins					=	$_CB_database->loadResult();

			$count					=	$this->params->get( 'login_share_count', 10, GetterInterface::INT  );

			if ( ! $count ) {
				$count				=	10;
			}

			if ( $logins > $count ) {
				$method				=	$this->params->get( 'login_share_method', 0, GetterInterface::INT  );
				$reason				=	$this->params->get( 'login_share_reason', 'Login sharing.', GetterInterface::STRING );

				if ( $method > 0 ) {
					$row			=	new BlockTable();

					if ( $method == 1 ) {
						$row->set( 'type', 'ip' );
						$row->set( 'value', $ipAddress );
					} else {
						$row->set( 'type', 'user' );
						$row->set( 'value', $user->get( 'id', 0, GetterInterface::INT ) );
					}

					$row->set( 'date', Application::Database()->getUtcDateTime() );
					$row->set( 'duration', $this->params->get( 'login_share_dur', '+1 HOUR', GetterInterface::STRING ) );
					$row->set( 'reason', $reason );

					$row->store();

					$this->blockLogin( $row );
				} else {
					$this->blockLogin( $reason );
				}
			}
		}
	}

	/**
	 * Updates users ip address log and clears attempts
	 *
	 * @param UserTable $row
	 * @param bool      $loggedIn
	 * @param bool      $firstLogin
	 * @param array     $messagesToUser
	 * @param array     $alertMessages
	 * @param string    $return
	 */
	public function afterLogin( &$row, $loggedIn, $firstLogin, &$messagesToUser, &$alertMessages, &$return )
	{
		if ( ! $loggedIn ) {
			return;
		}

		CBAntiSpam::logIPAddress( $row, array( 'login', 'forgot' ) );
	}
}