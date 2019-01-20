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
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class AcymailingAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $_CB_framework;

		if ( ! $this->installed() ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_ACYMAILING_NOT_INSTALLED', ':: Action [action] :: AcyMailing is not installed', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		$params								=	$this->autoaction()->params()->subTree( 'acymailing' );
		$forUser							=	$params->get( 'for', null, GetterInterface::STRING );

		if ( ! $forUser ) {
			$forUser						=	$user->get( 'id', 0, GetterInterface::INT );
		} else {
			$forUser						=	$this->string( $user, $forUser );
		}

		if ( ( $user->get( 'id', 0, GetterInterface::INT ) != $forUser ) || ( $user->get( 'email', null, GetterInterface::STRING ) != $forUser ) ) {
			if ( is_numeric( $forUser ) ) {
				$actionUser					=	\CBuser::getUserDataInstance( $forUser );

				$forUser					=	$actionUser->get( 'id', 0, GetterInterface::INT );
			} else {
				$actionUser					=	new UserTable();

				$actionUser->loadByEmail( $forUser );
			}
		} else {
			$actionUser						=	$user;

			$forUser						=	$user->get( 'id', 0, GetterInterface::INT );
		}

		if ( ! $forUser ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_ACYMAILING_NO_USER', ':: Action [action] :: AcyMailing skipped due to missing user', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		require_once( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_acymailing/helpers/helper.php' );

		/** @var \subscriberClass $acySubscriberAPI */
		$acySubscriberAPI					=	acymailing_get( 'class.subscriber' );
		$subscriberId						=	$acySubscriberAPI->subid( $forUser );

		if ( ! $subscriberId ) {
			$newSubscriber					=	new \stdClass();

			if ( $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
				$newSubscriber->email		=	$actionUser->get( 'email', null, GetterInterface::STRING );
				$newSubscriber->userid		=	$actionUser->get( 'id', 0, GetterInterface::INT );
				$newSubscriber->name		=	$actionUser->get( 'name', null, GetterInterface::STRING );
				$newSubscriber->created		=	Application::Date( $actionUser->get( 'registerDate', null, GetterInterface::STRING ), 'UTC' )->getTimestamp();
				$newSubscriber->ip			=	$actionUser->get( 'registeripaddr', null, GetterInterface::STRING );
			} else {
				$ipAddresses				=	cbGetIParray();

				$newSubscriber->email		=	$forUser;
				$newSubscriber->created		=	Application::Date( 'now', 'UTC' )->getTimestamp();
				$newSubscriber->ip			=	trim( array_shift( $ipAddresses ) );
			}

			$newSubscriber->confirmed		=	1;
			$newSubscriber->enabled			=	1;
			$newSubscriber->accept			=	1;
			$newSubscriber->html			=	1;

			$subscriberId					=	$acySubscriberAPI->save( $newSubscriber );
		}

		if ( ! $subscriberId ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_ACYMAILING_NO_SUB', ':: Action [action] :: AcyMailing skipped due to missing subscriber id', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		$lists								=	array();

		$subscribe							=	$params->get( 'subscribe', null, GetterInterface::STRING );

		if ( $subscribe ) {
			$subscribe						=	cbToArrayOfInt( explode( '|*|', $subscribe ) );

			foreach ( $subscribe as $listId ) {
				$lists[$listId]			=	array( 'status' => 1 );
			}
		}

		$unsubscribe						=	$params->get( 'unsubscribe', null, GetterInterface::STRING );

		if ( $unsubscribe ) {
			$unsubscribe					=	cbToArrayOfInt( explode( '|*|', $unsubscribe ) );

			foreach ( $unsubscribe as $listId ) {
				$lists[$listId]				=	array( 'status' => -1 );
			}
		}

		$remove								=	$params->get( 'remove', null, GetterInterface::STRING );

		if ( $remove ) {
			$remove							=	cbToArrayOfInt( explode( '|*|', $remove ) );

			foreach ( $remove as $listId ) {
				$lists[$listId]				=	array( 'status' => 0 );
			}
		}

		$pending							=	$params->get( 'pending', null, GetterInterface::STRING );

		if ( $pending ) {
			$pending						=	cbToArrayOfInt( explode( '|*|', $pending ) );

			foreach ( $pending as $listId ) {
				$lists[$listId]				=	array( 'status' => 2 );
			}
		}

		if ( $lists ) {
			$acySubscriberAPI->saveSubscription( $subscriberId, $lists );
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function lists()
	{
		global $_CB_framework;

		$lists					=	array();

		if ( $this->installed() ) {
			require_once( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_acymailing/helpers/helper.php' );

			/** @var \listClass $acyListAPI */
			$acyListAPI			=	acymailing_get( 'class.list' );
			$acyLists			=	$acyListAPI->getLists();

			if ( $acyLists ) {
				foreach ( $acyLists as $acyList ) {
					$lists[]	=	\moscomprofilerHTML::makeOption( (string) $acyList->listid, $acyList->name );
				}
			}
		}

		return $lists;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_CB_framework;

		if ( file_exists( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_acymailing/helpers/helper.php' ) ) {
			return true;
		}

		return false;
	}
}