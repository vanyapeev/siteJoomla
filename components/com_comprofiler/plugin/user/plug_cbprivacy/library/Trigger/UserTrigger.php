<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Privacy\Trigger;

use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Privacy\CBPrivacy;
use CB\Plugin\Privacy\Table\PrivacyTable;
use CB\Plugin\Privacy\Table\ClosedTable;
use CBLib\Registry\ParamsInterface;
use CB\Plugin\Privacy\Privacy;
use CBLib\Registry\Registry;
use CB\Database\Table\TabTable;

defined('CBLIB') or die();

class UserTrigger extends \cbPluginHandler
{

	/**
	 * @param int    $uid
	 * @param string $msg
	 */
	public function getProfile( $uid, &$msg )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) ) {
			$user				=	loadComprofilerUser( $uid );

			if ( $user && ( Application::MyUser()->getUserId() != $user->get( 'id', 0, GetterInterface::INT ) ) ) {
				if ( ( ! CBPrivacy::checkProfileDisplayAccess( $user ) ) && ( ! $this->params->get( 'profile_direct_access', false, GetterInterface::BOOLEAN ) ) ) {
					$msg		=	$this->params->get( 'profile_direct_message', null, GetterInterface::STRING );

					if ( $msg ) {
						$msg	=	\CBuser::getInstance( $user->get( 'id', 0, GetterInterface::INT ) )->replaceUserVars( $msg );
					}

					if ( ! $msg) {
						$msg	=	CBTxt::Th( 'UE_NOT_AUTHORIZED', 'You are not authorized to view this page!' );
					}
				}
			}
		}
	}

	/**
	 * @param UserTable $user
	 */
	public function deletePrivacy( $user )
	{
		global $_CB_database;

		$query			=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_privacy' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
		$_CB_database->setQuery( $query );
		$privacy		=	$_CB_database->loadObjectList( null, '\CB\Plugin\Privacy\Table\PrivacyTable', array( $_CB_database ) );

		/** @var PrivacyTable[] $privacy */
		foreach ( $privacy as $rule ) {
			$rule->delete();
		}

		$query			=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_privacy_closed' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
		$_CB_database->setQuery( $query );
		$closed			=	$_CB_database->loadObjectList( null, '\CB\Plugin\Privacy\Table\ClosedTable', array( $_CB_database ) );

		/** @var ClosedTable[] $closed */
		foreach ( $closed as $close ) {
			$close->delete();
		}
	}

	/**
	 * @param UserTable $user
	 * @param int       $ui
	 * @param string    $return
	 * @return null|array
	 */
	public function checkDisabled( &$user, $ui, &$return )
	{
		global $_CB_framework, $_PLUGINS;

		if ( $_PLUGINS->is_errors() || ( ! $user->get( 'block', 0, GetterInterface::INT ) ) || ( ! $user->get( 'approved', 1, GetterInterface::INT ) ) || ( ! $user->get( 'confirmed', 1, GetterInterface::INT ) ) ) {
			return null;
		}

		$closed							=	new ClosedTable();

		$closed->load( array( 'user_id' => $user->get( 'id', 0, GetterInterface::INT ), 'type' => 'disable' ) );

		if ( ! $closed->get( 'id', 0, GetterInterface::INT ) ) {
			$closed->load( array( 'user_id' => $user->get( 'id', 0, GetterInterface::INT ), 'type' => 'pending' ) );

			if ( $closed->get( 'id', 0, GetterInterface::INT ) ) {
				$user->set( 'block', 0 );

				if ( $user->storeBlock() ) {
					$closed->delete();

					$notification		=	new \cbNotification();

					$extra				=	array(	'ip_address'	=>	cbGetIPlist(),
													'reason'		=>	$closed->get( 'reason', null, GetterInterface::STRING ),
													'date'			=>	cbFormatDate( $closed->get( 'date', null, GetterInterface::STRING ) )
												);

					if ( $this->params->get( 'enable_notify', true, GetterInterface::BOOLEAN ) ) {
						$cbUser			=	\CBuser::getInstance( $user->get( 'id', 0, GetterInterface::INT ), false );

						$subject		=	$cbUser->replaceUserVars( CBTxt::T( 'User Account Re-Enabled' ), true, false, $extra, false );
						$body			=	$cbUser->replaceUserVars( CBTxt::T( 'Name: [name]<br />Username: [username]<br />Email: [email]<br />IP Address: [ip_address]<br />Date: [date]<br /><br />' ), false, false, $extra, false );

						if ( $subject && $body ) {
							$notification->sendToModerators( $subject, $body, false, 1 );
						}
					}

					$savedLanguage		=	CBTxt::setLanguage( $user->getUserLanguage() );

					$subject			=	CBTxt::T( 'Your Account has been Re-Enabled' );
					$body				=	CBTxt::T( 'This is a notice that your account [username] on [siteurl] has been re-enabled.' );

					CBTxt::setLanguage( $savedLanguage );

					if ( $subject && $body ) {
						$notification->sendFromSystem( $user, $subject, $body, true, 1, null, null, null, $extra );
					}
				}
			}

			return null;
		}

		$enableUrl						=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'disable', 'func' => 'enable', 'id' => $user->get( 'id', 0, GetterInterface::INT ) ) );
		$reason							=	$closed->get( 'reason', null, GetterInterface::STRING );

		if ( $reason ) {
			$message					=	CBTxt::T( 'PRIVACY_ACCOUNT_DISABLED_REASON', '<div class="alert alert-warning">Your account is disabled. Reason: [reason]. <a href="[enable_url]">Please click here if you would like to re-enable your account.</a></div>', array( '[reason]' => $reason, '[enable_url]' => $enableUrl ) );
		} else {
			$message					=	CBTxt::T( 'PRIVACY_ACCOUNT_DISABLED', '<div class="alert alert-warning">Your account is disabled. <a href="[enable_url]">Please click here if you would like to re-enable your account.</a></div>', array( '[enable_url]' => $enableUrl ) );
		}

		return array( 'messagesToUser' => $message );
	}

	/**
	 * Saves privacy after successful registration
	 *
	 * @param UserTable $user
	 */
	public function saveRegistrationPrivacy( $user )
	{
		$this->savePrivacy( $user, 'register' );
	}

	/**
	 * Saves privacy after successful profile edit
	 *
	 * @param UserTable $user
	 */
	public function saveEditPrivacy( $user )
	{
		$this->savePrivacy( $user, 'edit' );
	}

	/**
	 * Handles saving tab and field privacy after a successful registration or profile edit
	 *
	 * @param UserTable $user
	 * @param string    $reason
	 */
	private function savePrivacy( $user, $reason )
	{
		if ( ! $user->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$cbTabs		=	new \cbTabs( 0, Application::Cms()->getClientId(), null, false );

		foreach ( $cbTabs->_getTabsDb( $user, $reason ) as $tab ) {
			if ( ! $tab instanceof TabTable ) {
				continue;
			}

			if ( ! $tab->params instanceof ParamsInterface ) {
				$tab->params		=	new Registry( $tab->params );
			}

			$display				=	$tab->params->get( 'cbprivacy_display', 0, GetterInterface::INT );

			if ( ( $reason == 'register' ) && ( ! $tab->params->get( 'cbprivacy_display_reg', false, GetterInterface::BOOLEAN ) ) ) {
				$display			=	0;
			}

			if ( ( $display == 1 ) || ( ( $display == 2 ) && Application::MyUser()->isGlobalModerator() ) ) {
				$privacy			=	new Privacy( 'profile.tab.' . $tab->get( 'tabid', 0, GetterInterface::INT ), $user );

				if ( $display == 2 ) {
					$privacy->set( 'options_moderator', true );
				}

				if ( $reason == 'register' ) {
					$privacy->set( 'guest', true );
				}

				$privacy->parse( $tab->params, 'privacy_' );

				$privacy->privacy( 'save' );
			}
		}

		foreach ( $cbTabs->_getTabFieldsDb( null, $user, $reason, null, false ) as $field ) {
			if ( ( ! $field instanceof FieldTable ) || ( ! $field->get( 'profile', 1, GetterInterface::INT ) ) || ( $field->get( 'name', null, GetterInterface::STRING ) == 'privacy_profile' ) ) {
				continue;
			}

			$fieldId				=	$field->get( 'fieldid', 0, GetterInterface::INT );

			if ( ! $field->params instanceof ParamsInterface ) {
				$field->params		=	new Registry( $field->params );
			}

			$display				=	$field->params->get( 'cbprivacy_display', 0, GetterInterface::INT );

			if ( ( $reason == 'register' ) && ( ! $field->params->get( 'cbprivacy_display_reg', false, GetterInterface::BOOLEAN ) ) ) {
				$display			=	0;
			}

			if ( ( $display == 1 ) || ( ( $display == 2 ) && Application::MyUser()->isGlobalModerator() ) ) {
				$privacy			=	new Privacy( 'profile.field.' . $fieldId, $user );

				if ( $display == 2 ) {
					$privacy->set( 'options_moderator', true );
				}

				if ( $reason == 'register' ) {
					$privacy->set( 'guest', true );
				}

				$privacy->parse( $field->params, 'privacy_' );

				$privacy->privacy( 'save' );
			}
		}
	}
}