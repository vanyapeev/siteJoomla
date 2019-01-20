<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Trigger;

use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Notifications;

defined('CBLIB') or die();

class MenuTrigger extends \cbPluginHandler
{

	/**
	 * Displays frontend notifications icon on cb menu bar
	 *
	 * @param UserTable $user
	 */
	public function getNotifications( $user )
	{
		if ( ( ! $this->params->get( 'general_notifications', true, GetterInterface::BOOLEAN ) ) || ( Application::MyUser()->getUserId() != $user->get( 'id', 0, GetterInterface::INT ) ) ) {
			return;
		}

		$notifications			=	new Notifications( null, $user );

		$notifications->set( 'read', 'unread' );

		$return					=	$notifications->notifications( 'button' );

		if ( ! $return ) {
			return;
		}

		$menu					=	array();
		$menu['arrayPos']		=	array( '_UE_MENU_ACTIVITY_NOTIFICATIONS' => null );
		$menu['position']		=	'menuBar';
		$menu['caption']		=	'';
		$menu['url']			=	'<div class="cbActivityNotifications navbar-default">' . $return . '</div>';
		$menu['target']			=	'';
		$menu['img']			=	'';
		$menu['tooltip']		=	'';

		$this->addMenu( $menu );
	}
}