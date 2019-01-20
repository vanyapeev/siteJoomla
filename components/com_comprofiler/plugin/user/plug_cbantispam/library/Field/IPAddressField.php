<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AntiSpam\Field;

use CB\Plugin\AntiSpam\CBAntiSpam;
use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\GetterInterface;

defined('CBLIB') or die();

class IPAddressField extends \cbFieldHandler
{

	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		if ( $field->params->get( 'cbantispam_ipaddress_display', true, GetterInterface::BOOLEAN ) && ( ! Application::MyUser()->isGlobalModerator() ) && ( ! Application::Cms()->getClientId() ) ) {
			return null;
		}

		$ipAddress		=	CBAntiSpam::getUserIP( $user );

		switch ( $output ) {
			case 'html':
			case 'rss':
				return $this->formatFieldValueLayout( $this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $ipAddress, $output, true ), $reason, $field, $user );
				break;
			case 'htmledit':
				if ( ( $reason != 'search' ) && Application::Cms()->getClientId() ) {
					return $ipAddress;
				}

				return null;
				break;
			default:
				return $this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $ipAddress, $output, false );
				break;
		}
	}
}