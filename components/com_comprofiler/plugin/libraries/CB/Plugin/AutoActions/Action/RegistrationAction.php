<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Action;

use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class RegistrationAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $_CB_framework, $_PLUGINS, $ueConfig;

		$params						=	$this->autoaction()->params()->subTree( 'registration' );

		$approve					=	$params->get( 'approve', 0, GetterInterface::INT );
		$confirm					=	$params->get( 'confirm', 0, GetterInterface::INT );
		$approval					=	( $approve == 2 ? $ueConfig['reg_admin_approval'] : $approve );
		$confirmation				=	( $confirm == 2 ? $ueConfig['reg_confirmation'] : $confirm );
		$usergroup					=	$params->get( 'usergroup', null, GetterInterface::STRING );
		$password					=	$this->string( $user, $params->get( 'password', null, GetterInterface::STRING ) );
		$name						=	array();

		if ( ! $usergroup ) {
			$gids					=	array( $_CB_framework->getCfg( 'new_usertype' ) );
		} else {
			$gids					=	explode( '|*|', $usergroup );
		}

		cbArrayToInts( $gids );

		$newUser					=	new UserTable();

		$newUser->set( 'gids', $gids );
		$newUser->set( 'sendEmail', 0 );
		$newUser->set( 'registerDate', $_CB_framework->getUTCDate() );
		$newUser->set( 'username', $this->string( $user, $params->get( 'username', null, GetterInterface::STRING ) ) );
		$newUser->set( 'firstname', $this->string( $user, $params->get( 'firstname', null, GetterInterface::STRING ) ) );
		$newUser->set( 'middlename', $this->string( $user, $params->get( 'middlename', null, GetterInterface::STRING ) ) );
		$newUser->set( 'lastname', $this->string( $user, $params->get( 'lastname', null, GetterInterface::STRING ) ) );

		if ( $newUser->get( 'firstname', null, GetterInterface::STRING ) ) {
			$name[]					=	$newUser->get( 'firstname', null, GetterInterface::STRING );
		}

		if ( $newUser->get( 'middlename', null, GetterInterface::STRING ) ) {
			$name[]					=	$newUser->get( 'middlename', null, GetterInterface::STRING );
		}

		if ( $newUser->get( 'lastname', null, GetterInterface::STRING ) ) {
			$name[]					=	$newUser->get( 'lastname', null, GetterInterface::STRING );
		}

		$newUser->set( 'name', implode( ' ', $name ) );
		$newUser->set( 'email', $this->string( $user, $params->get( 'email', null, GetterInterface::STRING ) ) );

		if ( $password ) {
			$newUser->set( 'password', $newUser->hashAndSaltPassword( $password ) );
		} else {
			$newUser->setRandomPassword();

			$newUser->set( 'password', $newUser->hashAndSaltPassword( $newUser->get( 'password', null, GetterInterface::STRING ) ) );
		}

		$newUser->set( 'registeripaddr', cbGetIPlist() );

		if ( $approval == 0 ) {
			$newUser->set( 'approved', 1 );
		} else {
			$newUser->set( 'approved', 0 );
		}

		if ( $confirmation == 0 ) {
			$newUser->set( 'confirmed', 1 );
		} else {
			$newUser->set( 'confirmed', 0 );
		}

		if ( ( $newUser->get( 'confirmed', 1, GetterInterface::INT ) == 1 ) && ( $newUser->get( 'approved', 1, GetterInterface::INT ) == 1 ) ) {
			$newUser->set( 'block', 0 );
		} else {
			$newUser->set( 'block', 1 );
		}

		foreach ( $params->subTree( 'fields' ) as $row ) {
			/** @var ParamsInterface $row */
			$field					=	$row->get( 'field', null, GetterInterface::STRING );

			if ( $field ) {
				$newUser->set( $field, $this->string( $user, $row->get( 'value', null, GetterInterface::RAW ), false, $row->get( 'translate', false, GetterInterface::BOOLEAN ) ) );
			}
		}

		$_PLUGINS->trigger( 'onBeforeUserRegistration', array( &$newUser, &$newUser ) );

		if ( ! $newUser->store() ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_REGISTRATION_FAILED', ':: Action [action] :: Registration failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $newUser->getError() ) ) );
			return null;
		}

		if ( ( $newUser->get( 'confirmed', 1, GetterInterface::INT ) == 0 ) && ( $confirmation != 0 ) ) {
			if ( ! $newUser->store() ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_REGISTRATION_FAILED', ':: Action [action] :: Registration failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $newUser->getError() ) ) );
				return null;
			}
		}

		if ( $params->get( 'supress', 1, GetterInterface::BOOLEAN ) ) {
			$emails					=	false;
		} else {
			$emails					=	true;
		}

		activateUser( $newUser, 1, 'UserRegistration', $emails, $emails );

		$_PLUGINS->trigger( 'onAfterUserRegistration', array( &$newUser, &$newUser, true ) );

		return null;
	}
}