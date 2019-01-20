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
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJive\Table\GroupTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class GroupjiveAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $_CB_database;

		if ( ! $this->installed() ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NOT_INSTALLED', ':: Action [action] :: CB GroupJive is not installed', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		foreach ( $this->autoaction()->params()->subTree( 'groupjive' ) as $row ) {
			/** @var ParamsInterface $row */
			$owner									=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner								=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$owner								=	(int) $this->string( $user, $owner );
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $owner ) {
				$actionOwner							=	\CBuser::getUserDataInstance( $owner );
			} else {
				$actionOwner							=	$user;
			}

			$userId									=	$row->get( 'user', null, GetterInterface::STRING );

			if ( ! $userId ) {
				$userId								=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$userId								=	(int) $this->string( $actionOwner, $userId );
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $userId ) {
				$actionUser							=	\CBuser::getUserDataInstance( $userId );
			} else {
				$actionUser							=	$user;
			}

			switch( $row->get( 'mode', 1, GetterInterface::INT ) ) {
				case 3:
					if ( ! $actionOwner->get( 'id', 0, GetterInterface::INT ) ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_OWNER', ':: Action [action] :: CB GroupJive skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$name							=	$this->string( $actionOwner, $row->get( 'name', null, GetterInterface::STRING ) );

					if ( ! $name ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_NAME', ':: Action [action] :: CB GroupJive skipped due to missing name', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$category						=	new CategoryTable();

					$category->load( array( 'name' => $name ) );

					if ( $category->get( 'id', 0, GetterInterface::INT ) ) {
						continue;
					}

					$category->set( 'published', 1 );
					$category->set( 'name', $name );
					$category->set( 'description', $this->string( $actionOwner, $row->get( 'description', null, GetterInterface::STRING ) ) );
					$category->set( 'access', 1 );
					$category->set( 'create_access', 0 );
					$category->set( 'types', $row->get( 'types', '1|*|2|*|3', GetterInterface::STRING ) );
					$category->set( 'ordering', 1 );

					if ( ! $category->store() ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_FAILED', ':: Action [action] :: CB GroupJive failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $category->getError() ) ) );
						continue;
					}
					break;
				case 2:
					if ( ! $actionOwner->get( 'id', 0, GetterInterface::INT ) ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_OWNER', ':: Action [action] :: CB GroupJive skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$categoryId						=	$row->get( 'category', -1, GetterInterface::INT );

					$category						=	new CategoryTable();

					if ( $categoryId == -1 ) {
						$name						=	$this->string( $actionOwner, $row->get( 'category_name', null, GetterInterface::STRING ) );

						if ( ! $name ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_CAT_NAME', ':: Action [action] :: CB GroupJive skipped due to missing category name', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						$category->load( array( 'name' => $name ) );

						if ( ! $category->get( 'id', 0, GetterInterface::INT ) ) {
							$category->set( 'published', 1 );
							$category->set( 'name', $name );
							$category->set( 'description', $this->string( $actionOwner, $row->get( 'category_description', null, GetterInterface::STRING ) ) );
							$category->set( 'access', 1 );
							$category->set( 'create_access', 0 );
							$category->set( 'types', $row->get( 'category_types', '1|*|2|*|3', GetterInterface::STRING ) );
							$category->set( 'ordering', 1 );

							if ( ! $category->store() ) {
								$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_FAILED', ':: Action [action] :: CB GroupJive failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $category->getError() ) ) );
								continue;
							}
						}
					} else {
						$category->load( $categoryId );
					}

					if ( ! $category->get( 'id', 0, GetterInterface::INT ) ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_CATEGORY', ':: Action [action] :: CB GroupJive skipped due to missing category', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$name							=	$this->string( $actionOwner, $row->get( 'name', null, GetterInterface::STRING ) );

					if ( ! $name ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_NAME', ':: Action [action] :: CB GroupJive skipped due to missing name', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$group							=	new GroupTable();
					$join							=	false;

					if ( $row->get( 'unique', 1, GetterInterface::BOOLEAN ) ) {
						$group->load( array( 'category' => $category->get( 'id', 0, GetterInterface::INT ), 'user_id' => $actionOwner->get( 'id', 0, GetterInterface::INT ), 'name' => $name ) );
					} else {
						$group->load( array( 'category' => $category->get( 'id', 0, GetterInterface::INT ), 'name' => $name ) );

						if ( $row->get( 'autojoin', 1, GetterInterface::BOOLEAN ) ) {
							$join					=	true;
						}
					}

					if ( ! $group->get( 'id', 0, GetterInterface::INT ) ) {
						$group->set( 'published', 1 );
						$group->set( 'category', $category->get( 'id', 0, GetterInterface::INT ) );
						$group->set( 'user_id', $actionOwner->get( 'id', 0, GetterInterface::INT ) );
						$group->set( 'name', $name );
						$group->set( 'description', $this->string( $actionOwner, $row->get( 'description', null, GetterInterface::STRING ) ) );
						$group->set( 'type', $row->get( 'type', 1, GetterInterface::INT ) );
						$group->set( 'ordering', 1 );

						if ( ! $group->store() ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_FAILED', ':: Action [action] :: CB GroupJive failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $group->getError() ) ) );
							continue;
						}
					} elseif ( $join ) {
						$userId						=	$row->get( 'group_user', null, GetterInterface::STRING );

						if ( ! $userId ) {
							$userId					=	$user->get( 'id', 0, GetterInterface::INT );
						} else {
							$userId					=	(int) $this->string( $actionOwner, $userId );
						}

						if ( ! $userId ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_OWNER', ':: Action [action] :: CB GroupJive skipped due to missing user', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						$groupUser					=	new \CB\Plugin\GroupJive\Table\UserTable( $_CB_database );

						$groupUser->load( array( 'group' => $group->get( 'id', 0, GetterInterface::INT ), 'user_id' => $userId ) );

						if ( $groupUser->get( 'id', 0, GetterInterface::INT ) ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_GROUP', ':: Action [action] :: CB GroupJive skipped due to missing group', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						$groupUser->set( 'user_id', $userId );
						$groupUser->set( 'group', $group->get( 'id', 0, GetterInterface::INT ) );
						$groupUser->set( 'status', $row->get( 'group_status', 1, GetterInterface::INT ) );

						if ( ! $groupUser->store() ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_FAILED', ':: Action [action] :: CB GroupJive failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $groupUser->getError() ) ) );
							continue;
						}
					}
					break;
				case 4:
					if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_OWNER', ':: Action [action] :: CB GroupJive skipped due to missing user', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$groups							=	$row->get( 'groups', null, GetterInterface::STRING );

					if ( ! $groups ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_GROUPS', ':: Action [action] :: CB GroupJive skipped due to missing groups', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					if ( $groups == 'all' ) {
						$query						=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_users' )
													.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $actionUser->get( 'id', 0, GetterInterface::INT )
													.	"\n AND " . $_CB_database->NameQuote( 'status' ) . " != 4";
						$_CB_database->setQuery( $query );
						$groups						=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\UserTable', array( $_CB_database ) );
					} else {
						$groups						=	cbToArrayOfInt( explode( '|*|', $groups ) );
					}

					foreach ( $groups as $groupId ) {
						if ( $groupId instanceof \CB\Plugin\GroupJive\Table\UserTable ) {
							$groupUser				=	$groupId;
						} else {
							$group					=	new GroupTable();

							$group->load( (int) $groupId );

							if ( ! $group->get( 'id', 0, GetterInterface::INT ) ) {
								continue;
							}

							$groupUser				=	new \CB\Plugin\GroupJive\Table\UserTable( $_CB_database );

							$groupUser->load( array( 'group' => $group->get( 'id', 0, GetterInterface::INT ), 'user_id' => $actionUser->get( 'id', 0, GetterInterface::INT ) ) );
						}

						if ( ( ! $groupUser->get( 'id', 0, GetterInterface::INT ) ) || ( $groupUser->get( 'status', 0, GetterInterface::INT ) == 4 ) ) {
							continue;
						}

						$groupUser->delete();
					}
					break;
				case 1:
				default:
					if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_OWNER', ':: Action [action] :: CB GroupJive skipped due to missing user', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$groups							=	$row->get( 'groups', null, GetterInterface::STRING );

					if ( ! $groups ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_GROUPS', ':: Action [action] :: CB GroupJive skipped due to missing groups', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					if ( $groups == 'all' ) {
						$query						=	'SELECT g.*'
													.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
													.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
													.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT )
													.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
													.	"\n WHERE g." . $_CB_database->NameQuote( 'published' ) . " = 1"
													.	"\n AND g." . $_CB_database->NameQuote( 'user_id' ) . " != " . $actionUser->get( 'id', 0, GetterInterface::INT )
													.	"\n AND u." . $_CB_database->NameQuote( 'id' ) . " IS NULL";
						$_CB_database->setQuery( $query );
						$groups						=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $_CB_database ) );
					} else {
						$groups						=	cbToArrayOfInt( explode( '|*|', $groups ) );
					}

					foreach ( $groups as $groupId ) {
						$groupUser					=	new \CB\Plugin\GroupJive\Table\UserTable( $_CB_database );

						if ( $groupId instanceof GroupTable ) {
							$group					=	$groupId;
						} else {
							$group					=	new GroupTable();

							$group->load( (int) $groupId );

							if ( ! $group->get( 'id', 0, GetterInterface::INT ) ) {
								continue;
							}
						}

						$groupUser->load( array( 'group' => $group->get( 'id', 0, GetterInterface::INT ), 'user_id' => $actionUser->get( 'id', 0, GetterInterface::INT ) ) );

						if ( ! $groupUser->get( 'id', 0, GetterInterface::INT ) ) {
							$groupUser->set( 'user_id', $actionUser->get( 'id', 0, GetterInterface::INT ) );
							$groupUser->set( 'group', $group->get( 'id', 0, GetterInterface::INT ) );
						} elseif ( ( $groupUser->get( 'status', 1, GetterInterface::INT ) == $row->get( 'status', 1, GetterInterface::INT ) ) || ( $groupUser->get( 'status', 1, GetterInterface::INT ) == 4 ) ) {
							continue;
						}

						$groupUser->set( 'status', $row->get( 'status', 1, GetterInterface::INT ) );

						if ( ! $groupUser->store() ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_FAILED', ':: Action [action] :: CB GroupJive failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $groupUser->getError() ) ) );
							continue;
						}
					}
					break;
			}
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function categories()
	{
		$options		=	array();

		if ( $this->installed() ) {
			$options	=	CBGroupJive::getCategoryOptions();
		}

		return $options;
	}

	/**
	 * @return array
	 */
	public function groups()
	{
		$options		=	array();

		if ( $this->installed() ) {
			$options	=	CBGroupJive::getGroupOptions();
		}

		return $options;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_CB_framework, $_PLUGINS;

		static $installed			=	null;

		if ( $installed === null ) {
			if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' ) ) {
				if ( file_exists( $_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/user/plug_cbgroupjive/cbgroupjive.class.php' ) ) {
					$installed		=	false;
				} else {
					$installed		=	true;
				}
			} else {
				$installed			=	false;
			}
		}

		return $installed;
	}
}