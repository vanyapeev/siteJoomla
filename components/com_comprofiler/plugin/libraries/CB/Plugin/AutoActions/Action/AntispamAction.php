<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Action;

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CB\Plugin\AntiSpam\Table\BlockTable;
use CB\Plugin\AntiSpam\Table\WhitelistTable;
use CB\Plugin\AntiSpam\CBAntiSpam;
use CB\Plugin\AntiSpam\Captcha;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class AntispamAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null|string|bool
	 */
	public function execute( $user )
	{
		global $_PLUGINS;

		if ( ! $this->installed() ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_ANTISPAM_NOT_INSTALLED', ':: Action [action] :: CB AntiSpam is not installed', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		$return							=	null;

		foreach ( $this->autoaction()->params()->subTree( 'antispam' ) as $row ) {
			/** @var ParamsInterface $row */
			$mode						=	$row->get( 'mode', 'block', GetterInterface::STRING );

			if ( $mode == 'captcha' ) {
				$method					=	$row->get( 'method', 'display', GetterInterface::STRING );
				$name					=	$this->string( $user, $row->get( 'name', null, GetterInterface::STRING ) );

				if ( ! $name ) {
					$name				=	'autoaction' . $this->autoaction()->get( 'id', 0, GetterInterface::INT );
				}

				$error					=	$this->string( $user, $row->get( 'error', null, GetterInterface::STRING ) );

				if ( ! $error ) {
					$error				=	CBTxt::T( 'Invalid Captcha Code' );
				}

				$captcha				=	new Captcha( $name, $row->get( 'captcha', '-1', GetterInterface::STRING ) );

				if ( $method == 'validate' ) {
					if ( ( ! $captcha->load() ) || ( ! $captcha->validate() ) ) {
						$error			=	( $captcha->error() ? $captcha->error() : $error );

						foreach ( $this->variables() as $variable ) {
							if ( ! is_object( $variable ) ) {
								continue;
							}

							if ( method_exists( $variable, 'setError' ) ) {
								$variable->setError( $error );
							}
						}

						$_PLUGINS->_setErrorMSG( $error );
						$_PLUGINS->raiseError();

						$return			=	false;
					} else {
						$return			=	true;
					}
				} else {
					$return				=	$captcha->captcha()
										.	$captcha->input();
				}
			} else {
				$type					=	$row->get( 'type', 'user', GetterInterface::STRING );
				$value					=	$row->get( 'value', null, GetterInterface::STRING );

				if ( ! $value ) {
					switch ( $type ) {
						case 'account':
						case 'user':
							$value		=	$user->get( 'id', 0, GetterInterface::INT );
							break;
						case 'ip':
							$value		=	CBAntiSpam::getUserIP( $user );
							break;
						case 'ip_range':
							$value		=	CBAntiSpam::getUserIP( $user ) . ':' . CBAntiSpam::getUserIP( $user );
							break;
						case 'email':
							$value		=	$user->get( 'email', null, GetterInterface::STRING );
							break;
						case 'domain':
							$value		=	CBAntiSpam::getEmailDomain( $user );
							break;
					}
				} else {
					$value				=	$this->string( $user, $value );
				}

				if ( ! $value ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_ANTISPAM_NO_VALUE', ':: Action [action] :: CB AntiSpam skipped due to missing value', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
					continue;
				}

				if ( $mode == 'block' ) {
					$duration			=	$row->get( 'duration', '+1 MONTH', GetterInterface::STRING );

					if ( $duration == 'custom' ) {
						$duration		=	$this->string( $user, $row->get( 'custom_duration', null, GetterInterface::STRING ) );
					}

					$entry				=	new BlockTable();

					$entry->set( 'date', Application::Database()->getUtcDateTime() );
					$entry->set( 'duration', $duration );
				} else {
					$entry				=	new WhitelistTable();
				}

				$entry->set( 'type', $type );
				$entry->set( 'value', $value );
				$entry->set( 'reason', $this->string( $user, $row->get( 'reason', null, GetterInterface::STRING ) ) );

				if ( ! $entry->store() ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_ANTISPAM_FAILED', ':: Action [action] :: CB AntiSpam failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $entry->getError() ) ) );
				}
			}
		}

		return $return;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );

		if ( ! $plugin ) {
			return false;
		}

		$pluginVersion		=	str_replace( '+build.', '+', $_PLUGINS->getPluginVersion( $plugin, true ) );

		if ( version_compare( $pluginVersion, '3.0.0', '>=' ) && version_compare( $pluginVersion, '4.0.0', '<' ) ) {
			return true;
		}

		return false;
	}
}