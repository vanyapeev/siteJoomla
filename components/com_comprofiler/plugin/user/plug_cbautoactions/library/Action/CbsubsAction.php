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
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CbsubsAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		if ( ! $this->installed() ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_CBSUBS_NOT_INSTALLED', ':: Action [action] :: CB Paid Subscriptions is not installed', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		foreach ( $this->autoaction()->params()->subTree( 'cbsubs' ) as $row ) {
			/** @var ParamsInterface $row */
			$userId										=	$row->get( 'user', null, GetterInterface::STRING );

			if ( ! $userId ) {
				$userId									=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$userId									=	(int) $this->string( $user, $userId );
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $userId ) {
				$actionUser								=	\CBuser::getUserDataInstance( $userId );
			} else {
				$actionUser								=	$user;
			}

			$mode										=	$row->get( 'mode', 1, GetterInterface::INT );

			if ( $mode == 5 ) {
				if ( ! $this->promotions() ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_CBSUBSPROMO_NOT_INSTALLED', ':: Action [action] :: CBSubs Promotion is not installed', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
					continue;
				}

				$promotion								=	$row->subTree( 'promotion' );

				foreach ( $promotion->asArray() as $k => $v ) {
					$promotion->set( $k, $this->string( $user, $v, false ) );
				}

				$object									=	new \cbpaidpromotionTotalizertype();

				switch ( $row->get( 'create_by', 'name', GetterInterface::STRING ) ) {
					case 'name':
						$object->load( array( 'name' => $promotion->get( 'name', null, GetterInterface::STRING ) ) );
						break;
					case 'coupon':
						$object->load( array( 'coupon_code' => $promotion->get( 'coupon_code', null, GetterInterface::STRING ) ) );
						break;
					case 'name_coupon':
						$object->load( array( 'name' => $promotion->get( 'name', null, GetterInterface::STRING ), 'coupon_code' => $promotion->get( 'coupon_code', null, GetterInterface::STRING ) ) );
						break;
				}

				$object->bind( $promotion->asArray() );

				if ( $object->getError() || ( ! $object->check() ) ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_CBSUBS_ERROR', ':: Action [action] :: CB Paid Subscriptions skipped due to error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $object->getError() ) ) );
					continue;
				}

				if ( $object->getError() || ( ! $object->store() ) ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_CBSUBS_ERROR', ':: Action [action] :: CB Paid Subscriptions skipped due to error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $object->getError() ) ) );
					continue;
				}
			} else {
				if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_CBSUBS_NO_USER', ':: Action [action] :: CB Paid Subscriptions skipped due to missing user', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
					continue;
				}

				$plans									=	$row->get( 'plans', null, GetterInterface::STRING );

				if ( ! $plans ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_CBSUBS_NO_PLANS', ':: Action [action] :: CB Paid Subscriptions skipped due to missing plans', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
					continue;
				}

				$plans									=	cbToArrayOfInt( explode( '|*|', $plans ) );
				$subscriptions							=	\cbpaidUserExtension::getInstance( $actionUser->get( 'id', 0, GetterInterface::INT ) )->getUserSubscriptions( null, true );
				$activePlans							=	array();

				foreach ( $subscriptions as $subscription ) {
					$subscriptionStatus					=	$subscription->get( 'status', null, GetterInterface::STRING );
					$planId								=	$subscription->get( 'plan_id', 0, GetterInterface::INT );

					if ( ! in_array( $planId, $plans ) ) {
						continue;
					}

					if ( ( $mode == 1 ) && ( $subscriptionStatus == 'A' ) && $row->get( 'renew', false, GetterInterface::BOOLEAN ) ) {
						$mode							=	2;
					}

					switch ( $mode ) {
						case 2:
							$subscription->activate( $actionUser, Application::Date( 'now', 'UTC' )->getTimestamp(), true, 'R' );
							break;
						case 3:
							$cancel						=	$subscription->stopAutoRecurringPayments();

							if ( is_string( $cancel ) ) {
								$cancel					=	false;
							}

							if ( $cancel !== false ) {
								$subscription->deactivate( $actionUser, 'C' );
							}
							break;
						case 4:
							if ( $subscription->canDelete() ) {
								$subscription->revert( $actionUser, 'Denied' );
								$subscription->delete();
							}
							break;
						case 1:
						default:
							if ( ( $subscriptionStatus == 'A' ) && ( ! in_array( $planId, $activePlans ) ) ) {
								$activePlans[]			=	$planId;
							}
							break;
					}
				}

				if ( $mode == 1 ) {
					$plansMgr							=	\cbpaidPlansMgr::getInstance();
					$postData							=	array();
					$chosenPlans						=	array();

					foreach ( $plans as $planId ) {
						if ( ! in_array( $planId, $activePlans ) ) {
							$chosenPlans[$planId]		=	$plansMgr->loadPlan( $planId );
						}
					}

					if ( $chosenPlans ) {
						\cbpaidControllerOrder::createSubscriptionsAndPayment( $actionUser, $chosenPlans, $postData, null, null, 'A', null, 'U', 'free' );
					}
				}
			}
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function plans()
	{
		$plansList					=	array();

		if ( $this->installed() ) {
			$plansMgr				=	\cbpaidPlansMgr::getInstance();
			$plans					=	$plansMgr->loadPublishedPlans( null, true, 'any', null );

			if ( $plans ) {
				$plansList			=	array();

				foreach ( $plans as $k => $plan ) {
					$plansList[]	=	\moscomprofilerHTML::makeOption( (string) $k, $plan->get( 'alias', null, GetterInterface::STRING ) );
				}
			}
		}

		return $plansList;
	}

	/**
	 * @return bool
	 */
	public function promotions()
	{
		global $_PLUGINS;

		$_PLUGINS->loadPluginGroup( 'user/plug_cbpaidsubscriptions/plugin' );

		if ( $_PLUGINS->getLoadedPlugin( 'user/plug_cbpaidsubscriptions/plugin', 'cbsubs.promotion' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbpaidsubscriptions' ) ) {
			return true;
		}

		return false;
	}
}