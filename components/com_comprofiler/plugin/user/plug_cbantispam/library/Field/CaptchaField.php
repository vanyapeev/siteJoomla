<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AntiSpam\Field;

use CB\Plugin\AntiSpam\Captcha;
use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class CaptchaField extends \cbFieldHandler
{

	/**
	 * formats variable array into data attribute string
	 *
	 * @param  FieldTable $field
	 * @param  UserTable  $user
	 * @param  string     $output
	 * @param  string     $reason
	 * @param  array      $attributeArray
	 * @return null|string
	 */
	protected function getDataAttributes( $field, $user, $output, $reason, $attributeArray = array() )
	{
		if ( $field->params->get( 'cbantispam_captcha_ajax_valid', false, GetterInterface::BOOLEAN ) ) {
			$attributeArray[]	=	\cbValidator::getRuleHtmlAttributes( 'cbfield', array( 'user' => $user->get( 'id', GetterInterface::INT ), 'field' => htmlspecialchars( $field->get( 'name', null, GetterInterface::STRING ) ), 'reason' => htmlspecialchars( $reason ) ) );
		}

		return parent::getDataAttributes( $field, $user, $output, $reason, $attributeArray );
	}

	/**
	 * Formatter:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $formatting           'tr', 'td', 'div', 'span', 'none',   'table'??
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		$return			=	null;

		if ( ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) && ( $output == 'htmledit' ) && in_array( $reason, array( 'register', 'edit' ) ) ) {
			$field->set( 'searchable', 0 );
			$field->set( 'profile', 0 );
			$field->set( 'readonly', 0 );
			$field->set( 'required', 1 );

			$return		=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
		}

		return $return;
	}

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
		$return				=	null;

		if ( ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) && ( $output == 'htmledit' ) && in_array( $reason, array( 'register', 'edit' ) ) ) {
			$captcha		=	new Captcha( $field->get( 'name', null, GetterInterface::STRING ), $field->params->get( 'cbantispam_captcha_mode', '-1', GetterInterface::STRING ) );

			if ( $captcha->get( 'mode', 'internal', GetterInterface::STRING ) == 'honeypot' ) {
				$field->set( 'cssclass', 'hidden' );

				$return		=	$captcha->input();
			} else {
				$return		=	$captcha->captcha()
							.	$captcha->input( $this->getDataAttributes( $field, $user, $output, $reason ) );
			}

			$return			=	$this->formatFieldValueLayout( $return, $reason, $field, $user )
							.	$this->_fieldIconsHtml( $field, $user, 'htmledit', $reason, null, null, null, null, null, true, true );
		}

		return $return;
	}

	/**
	 * Direct access to field for custom operations, like for Ajax
	 *
	 * WARNING: direct unchecked access, except if $user is set, then check well for the $reason ...
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable    $user
	 * @param  array                 $postdata
	 * @param  string                $reason     'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @return string                            Expected output.
	 */
	public function fieldClass( &$field, &$user, &$postdata, $reason )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) && in_array( $reason, array( 'register', 'edit' ) ) ) {
			parent::fieldClass( $field, $user, $postdata, $reason );

			$function			=	cbGetParam( $_GET, 'function', null );

			if ( $function == 'checkvalue' ) {
				$captcha		=	new Captcha( $field->get( 'name', null, GetterInterface::STRING ) );

				$value			=	stripslashes( cbGetParam( $postdata, 'value', null ) );

				if ( $value && $captcha->load() && $captcha->validate( $value ) ) {
					$valid		=	true;
					$message	=	CBTxt::T( 'Captcha code is valid.' );
				} else {
					$valid		=	false;
					$message	=	( $captcha->error() ? $captcha->error() : CBTxt::T( 'Captcha code not valid.' ) );
				}

				return json_encode( array( 'valid' => $valid, 'message' => $message ) );
			}
		}

		return null;
	}

	/**
	 * Mutator:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function prepareFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) && in_array( $reason, array( 'register', 'edit' ) ) ) {
			$captcha	=	new Captcha( $field->get( 'name', null, GetterInterface::STRING ) );

			$captcha->load();

			$value		=	$captcha->value();

			$this->validate( $field, $user, null, $value, $postdata, $reason );
		}
	}

	/**
	 * Validator:
	 * Validates $value for $field->required and other rules
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user        RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  string      $columnName  Column to validate
	 * @param  string      $value       (RETURNED:) Value to validate, Returned Modified if needed !
	 * @param  array       $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason      'edit' for save user edit, 'register' for save registration
	 * @return boolean                  True if validate, $this->_setErrorMSG if False
	 */
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) && in_array( $reason, array( 'register', 'edit' ) ) ) {
			if ( parent::validate( $field, $user, $columnName, $value, $postdata, $reason ) ) {
				$captcha	=	new Captcha( $field->get( 'name', null, GetterInterface::STRING ) );

				if ( ( ! $captcha->load() ) || ( ! $captcha->validate() ) ) {
					$this->_setValidationError( $field, $user, $reason, ( $captcha->error() ? $captcha->error() : CBTxt::T( 'Invalid Captcha Code' ) ) );

					return false;
				}
			}
		}

		return true;
	}
}