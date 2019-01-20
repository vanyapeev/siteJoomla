<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Privacy\Field;

use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CB\Plugin\Privacy\Privacy;

defined('CBLIB') or die();

class PrivacyField extends \cbFieldHandler
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

		if ( ( $output == 'htmledit' ) && in_array( $reason, array( 'edit', 'register' ) ) ) {
			$field->set( 'profile', 0 );
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
		$return			=	null;

		if ( ( $output == 'htmledit' ) && in_array( $reason, array( 'edit', 'register' ) ) ) {
			$privacy	=	new Privacy( 'profile', $user );

			$privacy->parse( $field->params, 'privacy_' );

			$return		=	$this->formatFieldValueLayout( $privacy->privacy( 'edit' ), $reason, $field, $user, false )
						.	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, 'html', null, null, null, true, 0 );
		}

		return $return;
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param array      $postdata
	 * @param string     $reason
	 */
	public function commitFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		if ( in_array( $reason, array( 'edit', 'register' ) ) ) {
			$privacy	=	new Privacy( 'profile', $user );

			if ( $reason == 'register' ) {
				$privacy->set( 'guest', true );
			}

			$privacy->parse( $field->params, 'privacy_' );

			$privacy->privacy( 'save' );
		}
	}
}