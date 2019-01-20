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

class ConnectionAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $ueConfig;

		foreach ( $this->autoaction()->params()->subTree( 'connection' ) as $row ) {
			/** @var ParamsInterface $row */
			$userId									=	$row->get( 'user', null, GetterInterface::STRING );

			if ( ! $userId ) {
				$userId								=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$userId								=	(int) $this->string( $user, $userId );
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $userId ) {
				$actionUser							=	\CBuser::getUserDataInstance( $userId );
			} else {
				$actionUser							=	$user;
			}

			if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_CONNECTION_NO_USER', ':: Action [action] :: Connection skipped due to missing user', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			$users									=	$this->string( $actionUser, $row->get( 'users', null, GetterInterface::STRING ) );

			if ( ! $users ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_CONNECTION_NO_USERS', ':: Action [action] :: Connection skipped due to missing users', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			$users									=	cbToArrayOfInt( explode( ',', $users ) );
			$message								=	$this->string( $actionUser, $row->get( 'message', null, GetterInterface::RAW ), false );
			$mutual									=	$row->get( 'mutual', 2, GetterInterface::INT );
			$cross									=	$row->get( 'cross', 1, GetterInterface::INT );
			$notify									=	$row->get( 'notify', 0, GetterInterface::BOOLEAN );

			$oldMutual								=	Application::Config()->get( 'useMutualConnections', 1, GetterInterface::INT );
			$oldCross								=	Application::Config()->get( 'autoAddConnections', 1, GetterInterface::INT );

			if ( $mutual ) {
				$ueConfig['useMutualConnections']	=	( $mutual == 1 ? '1' : '0' );
			}

			if ( $cross ) {
				$ueConfig['autoAddConnections']		=	( $cross == 1 ? '1' : '0' );
			}

			if ( $row->get( 'direction', 0, GetterInterface::BOOLEAN ) ) {
				foreach ( $users as $connectionId ) {
					if ( $connectionId != $userId ) {
						$connections				=	new \cbConnection( $connectionId );

						if ( ! $connections->getConnectionDetails( $connectionId, $userId ) ) {
							$connections->addConnection( $userId, $message, $notify );
						}
					}
				}
			} else {
				$connections						=	new \cbConnection( $userId );

				foreach ( $users as $connectionId ) {
					if ( $connectionId != $userId ) {
						if (  ! $connections->getConnectionDetails( $userId, $connectionId ) ) {
							$connections->addConnection( $connectionId, $message, $notify );
						}
					}
				}
			}

			if ( $mutual ) {
				$ueConfig['useMutualConnections']	=	$oldMutual;
			}

			if ( $cross ) {
				$ueConfig['autoAddConnections']		=	$oldCross;
			}
		}

		return null;
	}
}