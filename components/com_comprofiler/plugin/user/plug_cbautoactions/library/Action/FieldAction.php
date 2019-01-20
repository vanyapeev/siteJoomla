<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Action;

use CBLib\Application\Application;
use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class FieldAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $_CB_database;

		foreach ( $this->autoaction()->params()->subTree( 'field' ) as $row ) {
			/** @var ParamsInterface $row */
			$userId					=	$row->get( 'user', null, GetterInterface::STRING );

			if ( ! $userId ) {
				$userId				=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$userId				=	(int) $this->string( $user, $userId );
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $userId ) {
				$actionUser			=	\CBuser::getUserDataInstance( $userId );
			} else {
				$actionUser			=	$user;
			}

			if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_FIELD_NO_USER', ':: Action [action] :: Field skipped due to missing user', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			$fieldId				=	$row->get( 'field', 0, GetterInterface::INT );

			if ( ! $fieldId ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_FIELD_NO_FIELD', ':: Action [action] :: Field skipped due to missing field', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			/** @var FieldTable[] $fields */
			static $fields			=	array();

			if ( ! isset( $fields[$fieldId] ) ) {
				$field				=	new FieldTable();

				$field->load( (int) $fieldId );

				$field->set( 'params', new Registry( $field->get( 'params', null, GetterInterface::RAW ) ) );

				$fields[$fieldId]	=	$field;
			}

			if ( ! $fields[$fieldId] ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_FIELD_DOES_NOT_EXIST', ':: Action [action] :: Field skipped due to field [field_id] does not exist', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[field_id]' => (int) $fieldId ) ) );
				continue;
			}

			$value					=	$this->string( $actionUser, $row->get( 'value', null, GetterInterface::RAW ), false, $row->get( 'translate', false, GetterInterface::BOOLEAN ) );
			$fieldName				=	$fields[$fieldId]->get( 'name', null, GetterInterface::STRING );

			switch ( $row->get( 'operator', 'set', GetterInterface::STRING ) ) {
				case 'prefix':
					$fieldValue		=	( $value . $actionUser->get( $fieldName, null, GetterInterface::RAW ) );
					break;
				case 'suffix':
					$fieldValue		=	( $actionUser->get( $fieldName, null, GetterInterface::RAW ) . $value );
					break;
				case 'add':
					$fieldValue		=	( $actionUser->get( $fieldName, 0, GetterInterface::FLOAT ) + (float) $value );
					break;
				case 'subtract':
					$fieldValue		=	( $actionUser->get( $fieldName, 0, GetterInterface::FLOAT ) - (float) $value );
					break;
				case 'divide':
					$fieldValue		=	( $actionUser->get( $fieldName, 0, GetterInterface::FLOAT ) / (float) $value );
					break;
				case 'multiply':
					$fieldValue		=	( $actionUser->get( $fieldName, 0, GetterInterface::FLOAT ) * (float) $value );
					break;
				case 'set':
				default:
					$fieldValue		=	$value;
					break;
			}

			if ( ( $fieldName == 'alias' ) && ( ! $this->fieldAlias( $actionUser, $fieldValue ) ) ) {
				continue;
			}

			if ( $row->get( 'direct', true, GetterInterface::BOOLEAN ) ) {
				$query				=	'UPDATE ' . $_CB_database->NameQuote( $fields[$fieldId]->get( 'table', null, GetterInterface::STRING ) )
									.	"\n SET " . $_CB_database->NameQuote( $fieldName ) . " = " . $_CB_database->Quote( $fieldValue )
									.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " = " . $actionUser->get( 'id', 0, GetterInterface::INT );
				$_CB_database->setQuery( $query );
				$_CB_database->query();
			} else {
				$actionUser->storeDatabaseValue( $fieldName, $fieldValue );
			}

			$actionUser->set( $fieldName, $fieldValue );
		}

		return null;
	}

	/**
	 * @param UserTable $user
	 * @param mixed     $alias
	 * @return bool
	 */
	private function fieldAlias( $user, &$alias )
	{
		global $_CB_database;

		$alias				=	trim( Application::Router()->stringToAlias( $alias ) );

		if ( cbutf8_strlen( $alias ) < 2 ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_FIELD_ALIAS_TOO_SHORT', ':: Action [action] :: Field skipped due to profile alias "[alias]" being less than 2 characters', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[alias]' => $alias ) ) );
			return false;
		}

		$query				=	'SELECT u.' . $_CB_database->NameQuote( 'id' )
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
							.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
							.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
							.	"\n WHERE c." . $_CB_database->NameQuote( 'id' ) . " != " . $user->get( 'id', 0, GetterInterface::INT )
							.	"\n AND ( u." . $_CB_database->NameQuote( 'username' ) . " = " . $_CB_database->Quote( $alias )
							.	' OR c.' . $_CB_database->NameQuote( 'alias' ) . ' = ' . $_CB_database->Quote( $alias ) . ' )';
		$_CB_database->setQuery( $query, 0, 1 );
		$exists				=	$_CB_database->loadResult();

		if ( $exists ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_FIELD_ALIAS_EXISTS', ':: Action [action] :: Field skipped due to profile alias "[alias]" already exists', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[alias]' => $alias ) ) );
			return false;
		}

		if ( in_array( $alias, Application::Router()->getViews() ) ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_FIELD_ALIAS_VIEW', ':: Action [action] :: Field skipped due to profile alias "[alias]" matching a core view', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[alias]' => $alias ) ) );
			return false;
		}

		return true;
	}
}