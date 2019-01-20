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

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class UsergroupAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		$session									=	\JFactory::getSession();
		$jUser										=	$session->get( 'user' );

		foreach ( $this->autoaction()->params()->subTree( 'usergroup' ) as $row ) {
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

			$mode									=	$row->get( 'mode', 'add', GetterInterface::STRING );

			if ( ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) && ( $mode != 'create' ) ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_USERGROUP_NO_USER', ':: Action [action] :: Usergroup skipped due to missing user', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			$isMe									=	( $jUser ? ( $jUser->id == $actionUser->get( 'id', 0, GetterInterface::INT ) ) : false );
			$myGroups								=	$actionUser->get( 'gids', array(), GetterInterface::RAW );

			if ( ! $myGroups ) {
				$myGroups							=	Application::User( $actionUser->get( 'id', 0, GetterInterface::INT ) )->getAuthorisedGroups( false );
			}

			$groups									=	cbToArrayOfInt( explode( '|*|', $row->get( 'groups', null, GetterInterface::STRING ) ) );

			switch ( $mode ) {
				case 'create':
					$title							=	$this->string( $actionUser, $row->get( 'title', null, GetterInterface::STRING ) );

					if ( ! $title ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_USERGROUP_NO_TITLE', ':: Action [action] :: Usergroup skipped due to missing title', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					/** @var \JTableUsergroup $usergroup */
					$usergroup						=	\JTable::getInstance( 'usergroup' );

					$usergroup->load( array( 'title' => $title ) );

					if ( ! $usergroup->id ) {
						$usergroup->parent_id		=	$row->get( 'parent', 0, GetterInterface::INT );
						$usergroup->title			=	$title;

						if ( ! $usergroup->store() ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_USERGROUP_CREATE_FAILED', ':: Action [action] :: Usergroup failed to create', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}
					}

					if ( $row->get( 'add', 1, GetterInterface::BOOLEAN ) && $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
						if ( ! in_array( $usergroup->id, $myGroups ) ) {
							$myGroups[]				=	$usergroup->id;

							$actionUser->set( 'gids', array_unique( $myGroups ) );

							if ( ! $actionUser->store() ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_USERGROUP_FAILED', ':: Action [action] :: Usergroup failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $actionUser->getError() ) ) );
								continue;
							}

							if ( $isMe ) {
								\JAccess::clearStatics();

								$session->set( 'user', new \JUser( $actionUser->get( 'id', 0, GetterInterface::INT ) ) );
							}
						}
					}
					break;
				case 'replace':
					if ( ! $groups ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_USERGROUP_NO_GROUPS', ':: Action [action] :: Usergroup skipped due to missing groups', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$actionUser->set( 'gids', $groups );

					if ( ! $actionUser->store() ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_USERGROUP_FAILED', ':: Action [action] :: Usergroup failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $actionUser->getError() ) ) );
						continue;
					}

					if ( $isMe ) {
						\JAccess::clearStatics();

						$session->set( 'user', new \JUser( $actionUser->get( 'id', 0, GetterInterface::INT ) ) );
					}
					break;
				case 'remove':
					if ( ! $groups ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_USERGROUP_NO_GROUPS', ':: Action [action] :: Usergroup skipped due to missing groups', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$usergroups						=	array();
					$removed						=	false;

					foreach( $myGroups as $gid ) {
						if ( in_array( $gid, $groups ) ) {
							$removed				=	true;

							continue;
						}

						$usergroups[]				=	$gid;
					}

					if ( $removed ) {
						$actionUser->set( 'gids', $usergroups );

						if ( ! $actionUser->store() ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_USERGROUP_FAILED', ':: Action [action] :: Usergroup failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $actionUser->getError() ) ) );
							continue;
						}

						if ( $isMe ) {
							\JAccess::clearStatics();

							$session->set( 'user', new \JUser( $actionUser->get( 'id', 0, GetterInterface::INT ) ) );
						}
					}
					break;
				case 'add':
				default:
					if ( ! $groups ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_USERGROUP_NO_GROUPS', ':: Action [action] :: Usergroup skipped due to missing groups', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$usergroups						=	$groups;

					foreach( $usergroups as $k => $usergroup ) {
						if ( in_array( $usergroup, $myGroups ) ) {
							unset( $usergroups[$k] );
						}
					}

					if ( $usergroups ) {
						$actionUser->set( 'gids', array_unique( array_merge( $myGroups, $groups ) ) );

						if ( ! $actionUser->store() ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_USERGROUP_FAILED', ':: Action [action] :: Usergroup failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $actionUser->getError() ) ) );
							continue;
						}

						if ( $isMe ) {
							\JAccess::clearStatics();

							$session->set( 'user', new \JUser( $actionUser->get( 'id', 0, GetterInterface::INT ) ) );
						}
					}
					break;
			}
		}

		return null;
	}
}