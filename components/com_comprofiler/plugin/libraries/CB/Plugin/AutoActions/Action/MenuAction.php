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

class MenuAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $_PLUGINS;

		foreach ( $this->autoaction()->params()->subTree( 'menu' ) as $row ) {
			/** @var ParamsInterface $row */
			$menuTitle					=	$this->string( $user, $row->get( 'title', null, GetterInterface::STRING ) );

			if ( ! $menuTitle ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_MENU_NO_TITLE', ':: Action [action] :: CB Menu skipped due to missing title', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			$menuType					=	$this->string( $user, $row->get( 'type', null, GetterInterface::STRING ), true, false );
			$menuClass					=	$this->string( $user, $row->get( 'class', null, GetterInterface::STRING ), true, false );
			$menuTarget					=	$row->get( 'target', null, GetterInterface::STRING );
			$menuImg					=	$this->string( $user, $row->get( 'image', null, GetterInterface::STRING ), false );

			$menuItem					=	array();

			if ( ! $menuType )  {
				$menuItem['arrayPos']	=	array( $menuClass => null );
			} else {
				$menuItem['arrayPos']	=	array( $menuType => array( $menuClass => null ) );
			}

			$menuItem['position']		=	$row->get( 'position', 'menuBar', GetterInterface::STRING );
			$menuItem['caption']		=	htmlspecialchars( $menuTitle );
			$menuItem['url']			=	$this->string( $user, $row->get( 'url', null, GetterInterface::STRING ), false );
			$menuItem['target']			=	( $menuTarget ? htmlspecialchars( $menuTarget ) : null );
			$menuItem['img']			=	( $menuImg ? ( $menuImg[0] == '<' ? $menuImg : '<img src="' . htmlspecialchars( $menuImg ) . '" />' ) : null );
			$menuItem['tooltip']		=	htmlspecialchars( $this->string( $user, $row->get( 'tooltip', null, GetterInterface::STRING ), false ) );

			$_PLUGINS->addMenu( $menuItem );
		}

		return null;
	}
}