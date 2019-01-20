<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Field;

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Notifications;

defined('CBLIB') or die();

class NotificationsField extends \cbFieldHandler
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
		if ( ( ! ( ( $output == 'html' ) && ( $reason == 'profile' ) ) ) || ( Application::MyUser()->getUserId() != $user->get( 'id', 0, GetterInterface::INT ) ) ) {
			return null;
		}

		$notifications		=	new Notifications( str_replace( '[field_id]', $field->get( 'fieldid', 0, GetterInterface::INT ), $field->params->get( 'notifications_asset', null, GetterInterface::STRING ) ), $user );

		$notifications->parse( $field->params, 'notifications_' );

		$notifications->set( 'field', $field->get( 'fieldid', 0, GetterInterface::INT ) );

		$layout				=	$field->params->get( 'notifications_layout', 'button', GetterInterface::STRING );

		switch ( $field->params->get( 'notifications_state', 'unread', GetterInterface::STRING ) ) {
			case 'read':
				if ( in_array( $layout, array( 'button', 'toggle' ) ) ) {
					$notifications->set( 'read', 'read' );
				} else {
					$notifications->set( 'read', 'readonly' );
				}
				break;
			case 'unread':
				if ( in_array( $layout, array( 'button', 'toggle' ) ) ) {
					$notifications->set( 'read', 'unread' );
				} else {
					$notifications->set( 'read', 'unreadonly' );
				}
				break;
			case 'all':
				$notifications->set( 'read', 'status' );
				break;
		}

		if ( in_array( $layout, array( 'button', 'toggle' ) ) ) {
			$return			=	$notifications->notifications( 'button' );
		} else {
			if ( ( ! Application::Config()->get( 'showEmptyFields', 1, GetterInterface::INT ) ) && ( ! $notifications->rows( 'count' ) ) ) {
				return cbReplaceVars( Application::Config()->get( 'emptyFieldsText', null, GetterInterface::STRING ), $user );
			}

			$return			=	$notifications->notifications();
		}

		if ( ! $return ) {
			return null;
		}

		return $this->formatFieldValueLayout( $this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $return, $output, false ), $reason, $field, $user );
	}
}