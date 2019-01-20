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

class KunenaAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $_CB_database;

		if ( ! $this->installed() ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_KUNENA_NOT_INSTALLED', ':: Action [action] :: Kunena is not installed', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		foreach ( $this->autoaction()->params()->subTree( 'kunena' ) as $row ) {
			/** @var ParamsInterface $row */
			$owner								=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner							=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$owner							=	(int) $this->string( $user, $owner );
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $owner ) {
				$actionUser						=	\CBuser::getUserDataInstance( $owner );
			} else {
				$actionUser						=	$user;
			}

			switch ( $row->get( 'mode', 'category', GetterInterface::STRING ) ) {
				case 'sync':
					if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_USER', ':: Action [action] :: Kunena skipped due to missing user', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$kunenaUser					=	\KunenaUserHelper::get( $actionUser->get( 'id', 0, GetterInterface::INT ) );

					$kunenaUser->set( 'name', $actionUser->get( 'name', null, GetterInterface::STRING ) );
					$kunenaUser->set( 'username', $actionUser->get( 'username', null, GetterInterface::STRING ) );
					$kunenaUser->set( 'email', $actionUser->get( 'email', null, GetterInterface::STRING ) );

					foreach ( $row->subTree( 'fields' ) as $r ) {
						/** @var ParamsInterface $r */
						$field					=	$r->get( 'field', null, GetterInterface::STRING );

						if ( $field ) {
							$kunenaUser->set( $field, $this->string( $actionUser, $r->get( 'value', null, GetterInterface::RAW ), false, $r->get( 'translate', false, GetterInterface::BOOLEAN ) ) );
						}
					}

					$kunenaUser->save();
					break;
				case 'reply':
					if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_OWNER', ':: Action [action] :: Kunena skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$message					=	$this->string( $actionUser, $row->get( 'message', null, GetterInterface::RAW ), false );

					if ( ! $message ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_MSG', ':: Action [action] :: Kunena skipped due to missing message', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$topicId					=	$row->get( 'topic', 0, GetterInterface::INT );

					if ( ! $topicId ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_TOPIC', ':: Action [action] :: Kunena skipped due to missing topic', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$subject					=	$this->string( $actionUser, $row->get( 'subject', null, GetterInterface::STRING ) );

					$topic						=	\KunenaForumMessageHelper::get( $topicId );

					$fields						=	array( 'message' => $message );

					if ( $subject ) {
						$fields['subject']		=	$subject;
					}

					$topic->newReply( $fields, $actionUser->get( 'id', 0, GetterInterface::INT ) );
					break;
				case 'topic':
					if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_OWNER', ':: Action [action] :: Kunena skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$subject					=	$this->string( $actionUser, $row->get( 'subject', null, GetterInterface::STRING ) );

					if ( ! $subject ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_SUBJ', ':: Action [action] :: Kunena skipped due to missing subject', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$message					=	$this->string( $actionUser, $row->get( 'message', null, GetterInterface::RAW ), false );

					if ( ! $message ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_MSG', ':: Action [action] :: Kunena skipped due to missing message', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$categoryId					=	$row->get( 'category', 0, GetterInterface::INT );

					if ( ! $categoryId ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_CAT', ':: Action [action] :: Kunena skipped due to missing category', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$category					=	\KunenaForumCategoryHelper::get( $categoryId );

					$fields						=	array(	'catid' => $categoryId,
															'subject' => $subject,
															'message' => $message
														);

					$category->newTopic( $fields, $actionUser->get( 'id', 0, GetterInterface::INT ) );
					break;
				case 'category':
				default:
					$name						=	$this->string( $actionUser, $row->get( 'name', null, GetterInterface::STRING ) );

					if ( ! $name ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_NAME', ':: Action [action] :: Kunena skipped due to missing name', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$query						=	'SELECT ' . $_CB_database->NameQuote( 'id' )
												.	"\n FROM " . $_CB_database->NameQuote( '#__kunena_categories' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'name' ) . " = " . $_CB_database->Quote( $name );
					$_CB_database->setQuery( $query );
					if ( ! $_CB_database->loadResult() ) {
						$category				=	\KunenaForumCategoryHelper::get();

						$category->set( 'parent_id', $row->get( 'parent', 0, GetterInterface::INT ) );
						$category->set( 'name', $name );
						$category->set( 'alias', \KunenaRoute::stringURLSafe( $name ) );
						$category->set( 'accesstype', 'joomla.group' );
						$category->set( 'access', $row->get( 'access', 1, GetterInterface::INT ) );
						$category->set( 'published', $row->get( 'published', 1, GetterInterface::INT ) );
						$category->set( 'description', $this->string( $actionUser, $row->get( 'description', null, GetterInterface::STRING ) ) );

						if ( $category->save() && $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
							$category->addModerator( $actionUser->get( 'id', 0, GetterInterface::INT ) );
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
		$options			=	array();

		if ( $this->installed() ) {
			$rows			=	\KunenaForumCategoryHelper::getChildren( 0, 10 );

			if ( $rows ) foreach ( $rows as $row ) {
				$options[]	=	\moscomprofilerHTML::makeOption( (string) $row->id, str_repeat( '- ', $row->level + 1  ) . ' ' . $row->name );
			}
		}

		return $options;
	}

	/**
	 * @return array
	 */
	public function topics()
	{
		$options			=	array();

		if ( $this->installed() ) {
			$rows			=	\KunenaForumTopicHelper::getLatestTopics();

			if ( $rows[1] ) foreach ( $rows[1] as $row ) {
				$options[]	=	\moscomprofilerHTML::makeOption( (string) $row->id, $row->subject );
			}
		}

		return $options;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_CB_framework;

		$api	=	$_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_kunena/api.php';

		if ( file_exists( $api ) ) {
			require_once( $api );

			if ( class_exists( 'KunenaForum' ) && class_exists( 'KunenaForumCategoryHelper' ) && class_exists( 'KunenaForumTopicHelper' ) && class_exists( 'KunenaUserHelper' ) ) {
				return true;
			}
		}

		return false;
	}
}