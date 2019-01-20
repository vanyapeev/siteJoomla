<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Privacy\Field;

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class DisableField extends \cbFieldHandler
{

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $formatting
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null
	 */
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		$return			=	null;

		if ( ( ! Application::Cms()->getClientId() ) && ( $output == 'htmledit' ) && ( $reason == 'edit' ) && $user->get( 'id', 0, GetterInterface::INT ) && ( ! Application::User( $user->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) ) {
			$field->set( 'registration', 0 );
			$field->set( 'profile', 0 );
			$field->set( 'required', 0 );
			$field->set( 'readonly', 0 );

			$return		=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
		}

		return $return;
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null|string
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		global $_CB_framework;

		$return			=	null;

		if ( ( ! Application::Cms()->getClientId() ) && ( $output == 'htmledit' ) && ( $reason == 'edit' ) && $user->get( 'id', 0, GetterInterface::INT ) && ( ! Application::User( $user->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) ) {
			initToolTip();

			$value		=	'<a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to disable your account?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'disable', 'id' => $user->get( 'id', 0, GetterInterface::INT ) ) ) ) . '\'; })">'
						.		CBTxt::T( 'Disable account, user profile, and related features.' )
						.	'</a>';

			$return		=	$this->formatFieldValueLayout( $value, $reason, $field, $user, false )
						.	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, 'html', $value, null, null, true, 0 );
		}

		return $return;
	}
}