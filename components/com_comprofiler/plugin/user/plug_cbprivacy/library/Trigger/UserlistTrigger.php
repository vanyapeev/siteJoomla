<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Privacy\Trigger;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\Privacy\CBPrivacy;
use CB\Database\Table\ListTable;
use CB\Database\Table\FieldTable;

defined('CBLIB') or die();

class UserlistTrigger extends \cbPluginHandler
{

	/**
	 * @param ListTable    $row
	 * @param UserTable[]  $users
	 * @param array        $columns
	 * @param FieldTable[] $fields
	 * @param array        $input
	 * @param int          $listid
	 * @param string|null  $search
	 * @param int          $Itemid
	 * @param int          $ui
	 */
	public function getList( &$row, &$users, &$columns, &$fields, &$input, $listid, &$search, &$Itemid, $ui )
	{
		if ( Application::Cms()->getClientId() || Application::MyUser()->isGlobalModerator() || ( ! $users ) ) {
			return;
		}

		foreach( $users as $k => $user ) {
			if ( isset( $users[$k] ) && ( Application::MyUser()->getUserId() != $user->get( 'id', 0, GetterInterface::INT ) ) ) {
				if ( ( ! CBPrivacy::checkProfileDisplayAccess( $user ) ) && ( ! $this->params->get( 'profile_direct_access', false, GetterInterface::BOOLEAN ) ) ) {
					unset( $users[$k] );
				} else {
					foreach ( $fields as $field ) {
						if ( ( $search !== null ) && cbGetParam( $_REQUEST, $field->get( 'name', null, GetterInterface::STRING ), null ) && ( ! CBPrivacy::checkFieldDisplayAccess( $field, $user ) ) ) {
							unset( $users[$k] );
						}
					}
				}
			}
		}
	}
}