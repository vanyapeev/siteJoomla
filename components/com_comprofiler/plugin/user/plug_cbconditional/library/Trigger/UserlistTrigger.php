<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Conditional\Trigger;

use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CB\Database\Table\ListTable;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Conditional\CBConditional;

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
	public function listDisplay( &$row, &$users, &$columns, &$fields, &$input, $listid, &$search, &$Itemid, $ui )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( $search !== null ) ) {
			if ( $users ) foreach( $users as $k => $user ) {
				if ( ! isset( $users[$k] ) ) {
					continue;
				}

				foreach ( $fields as $field ) {
					// We're only removing users if the field being searched has been conditioned away for that user:
					if ( $this->input( $field->get( 'name', null, GetterInterface::STRING ), null, GetterInterface::RAW ) == '' ) {
						continue;
					}

					$conditioned			=	CBConditional::getTabConditional( $field->get( 'tabid', 0, GetterInterface::INT ), 'list', $user->get( 'id', 0, GetterInterface::INT ) );

					if ( ! $conditioned ) {
						$conditioned		=	CBConditional::getFieldConditional( $field, 'list', $user->get( 'id', 0, GetterInterface::INT ) );
					}

					if ( $conditioned ) {
						unset( $users[$k] );
					}
				}
			}
		}
	}
}