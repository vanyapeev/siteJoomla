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

class BlogAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		if ( ! $this->installed() ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_BLOGS_NOT_INSTALLED', ':: Action [action] :: CB Blogs is not installed', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		foreach ( $this->autoaction()->params()->subTree( 'blog' ) as $row ) {
			/** @var ParamsInterface $row */
			$blog			=	new \cbblogsBlogTable();

			$owner			=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner		=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$owner		=	(int) $this->string( $user, $owner );
			}

			if ( ! $owner ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_BLOGS_NO_OWNER', ':: Action [action] :: CB Blogs skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $owner ) {
				$actionUser	=	\CBuser::getUserDataInstance( $owner );
			} else {
				$actionUser	=	$user;
			}

			$blogData		=	array(	'user'			=>	$actionUser->get( 'id', 0, GetterInterface::INT ),
										'title'			=>	$this->string( $actionUser, $row->get( 'title', null, GetterInterface::STRING ) ),
										'blog_intro'	=>	$this->string( $actionUser, $row->get( 'intro', null, GetterInterface::RAW ), false ),
										'blog_full'		=>	$this->string( $actionUser, $row->get( 'full', null, GetterInterface::RAW ), false ),
										'category'		=>	$row->get( 'category', null, GetterInterface::STRING ),
										'published'		=>	$row->get( 'published', 1, GetterInterface::INT ),
										'access'		=>	$row->get( 'access', 1, GetterInterface::INT )
									);

			if ( ! $blog->bind( $blogData ) ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_BLOGS_BIND_FAILED', ':: Action [action] :: CB Blogs failed to bind. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $blog->getError() ) ) );
				continue;
			}

			if ( ! $blog->store() ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_BLOGS_FAILED', ':: Action [action] :: CB Blogs failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $blog->getError() ) ) );
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
			$options	=	\cbblogsModel::getCategoriesList();
		}

		return $options;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbblogs' ) ) {
			return true;
		}

		return false;
	}
}