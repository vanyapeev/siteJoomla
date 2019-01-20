<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Registry\Registry;
use CB\Database\Table\PluginTable;
use CB\Database\Table\TabTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

function plug_cbconditional_install()
{
	global $_CB_database;

	// Migrate plugin params if they exist and determine if we even need to migrate
	$plugin										=	new PluginTable();
	$debug										=	0;

	if ( $plugin->load( array( 'element' => 'cbconditional' ) ) ) {
		$pluginParams							=	new Registry( $plugin->params );

		if ( $pluginParams->has( 'cond_backend' ) || $pluginParams->has( 'cond_reset' ) || $pluginParams->has( 'cond_debug' ) ) {
			$debug								=	$pluginParams->get( 'cond_debug', 0, GetterInterface::INT );

			$pluginParams->set( 'conditions_backend', $pluginParams->get( 'cond_backend', 0, GetterInterface::INT ) );
			$pluginParams->set( 'conditions_reset', $pluginParams->get( 'cond_reset', 1, GetterInterface::INT ) );

			$pluginParams->unsetEntry( 'cond_backend' );
			$pluginParams->unsetEntry( 'cond_reset' );
			$pluginParams->unsetEntry( 'cond_debug' );

			$plugin->set( 'params', $pluginParams->asJson() );

			$plugin->store();
		}
	}

	// Migrate tab conditions
	$query										=	'SELECT *'
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_tabs' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'params' ) . " LIKE " . $_CB_database->Quote( '%cbconditional_display%' );
	$_CB_database->setQuery( $query );
	$tabs										=	$_CB_database->loadObjectList( null, '\CB\Database\Table\TabTable', array( $_CB_database ) );

	/** @var TabTable[] $tabs */
	foreach ( $tabs as $tab ) {
		$tabParams								=	new Registry( $tab->params );
		$tabParamsOld							=	$tabParams->asJson();

		$tabCondition							=	0;
		$tabConditions							=	array();

		for ( $i = 1; $i <= 5; $i++ ) {
			$conditional						=	( $i > 1 ? $i : null );
			$display							=	$tabParams->get( 'cbconditional_display' . $conditional, 0, GetterInterface::INT );

			if ( ! $display ) {
				plug_cbconditional_remove_params( $tabParams, $conditional );

				continue;
			}

			$mode								=	( $tabParams->get( 'cbconditional_mode' . $conditional, 0, GetterInterface::INT ) ? 1 : 2 );

			if ( ! $tabCondition ) {
				$tabCondition					=	$mode;
			}

			$conditionField						=	$tabParams->get( 'cbconditional_field' . $conditional, null, GetterInterface::STRING );

			if ( in_array( $conditionField, array( 'customviewaccesslevels', 'customusergroups' ) ) ) {
				$operator						=	0;
			} else {
				$operator						=	$tabParams->get( 'cbconditional_operator' . $conditional, 0, GetterInterface::INT );
			}

			if ( $tabCondition != $mode ) {
				$operator						=	plug_cbconditional_invert_operator( $operator );
			}

			$tabConditions[]					=	array(	'field'						=>	$conditionField,
															'field_custom'				=>	$tabParams->get( 'cbconditional_customvalue' . $conditional, null, GetterInterface::RAW ),
															'field_custom_translate'	=>	$tabParams->get( 'cbconditional_customvalue_translate' . $conditional, 0, GetterInterface::INT ),
															'field_viewaccesslevels'	=>	$tabParams->get( 'cbconditional_customviewaccesslevels' . $conditional, null, GetterInterface::STRING ),
															'field_usergroups'			=>	$tabParams->get( 'cbconditional_customusergroups' . $conditional, null, GetterInterface::STRING ),
															'operator_viewaccesslevels'	=>	( in_array( $operator, array( 0, 1 ) ) ? $operator : 0 ),
															'operator_usergroups'		=>	( in_array( $operator, array( 0, 1 ) ) ? $operator : 0 ),
															'operator'					=>	$operator,
															'value'						=>	$tabParams->get( 'cbconditional_value' . $conditional, null, GetterInterface::RAW ),
															'value_translate'			=>	$tabParams->get( 'cbconditional_value_translate' . $conditional, 0, GetterInterface::INT ),
															'location_registration'		=>	$tabParams->get( 'cbconditional_target_reg' . $conditional, 0, GetterInterface::INT ),
															'location_profile_edit'		=>	$tabParams->get( 'cbconditional_target_edit' . $conditional, 0, GetterInterface::INT ),
															'location_profile_view'		=>	$tabParams->get( 'cbconditional_target_view' . $conditional, 1, GetterInterface::INT ),
															'location_userlist_search'	=>	0,
															'location_userlist_view'	=>	1
														);

			plug_cbconditional_remove_params( $tabParams, $conditional );
		}

		if ( $tabConditions ) {
			$tabParams->set( 'cbconditional_conditioned', $tabCondition );

			$tabParams->set( 'cbconditional_conditions', array( array( 'condition' => $tabConditions ) ) );

			$tabParams->set( 'cbconditional_debug', $debug );
		}

		if ( $tabParamsOld != $tabParams->asJson() ) {
			$tab->set( 'params', $tabParams->asJson() );

			$tab->store();
		}
	}

	// Migrate field conditions:
	$query										=	'SELECT *'
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'params' ) . " LIKE " . $_CB_database->Quote( '%cbconditional_display%' );
	$_CB_database->setQuery( $query );
	$fields										=	$_CB_database->loadObjectList( null, '\CB\Database\Table\FieldTable', array( $_CB_database ) );

	$fieldOthers								=	array();

	/** @var FieldTable[] $fields */
	foreach ( $fields as $field ) {
		$fieldParams							=	new Registry( $field->params );
		$fieldParamsOld							=	$fieldParams->asJson();

		$fieldCondition							=	0;
		$fieldConditions						=	array();

		for ( $i = 1; $i <= 5; $i++ ) {
			$conditional						=	( $i > 1 ? $i : null );
			$display							=	$fieldParams->get( 'cbconditional_display' . $conditional, 0, GetterInterface::INT );

			if ( ! $display ) {
				plug_cbconditional_remove_params( $fieldParams, $conditional );

				continue;
			}

			if ( $display == 1 ) {
				$fieldsShow						=	explode( '|*|', $fieldParams->get( 'cbconditional_show' . $conditional, null, GetterInterface::STRING ) );

				foreach ( $fieldsShow as $fieldId ) {
					$fieldOthers[$fieldId][]	=	array(	'mode'						=>	1,
															'field'						=>	$field->get( 'fieldid', 0, GetterInterface::INT ) . ',' . $field->get( 'name', null, GetterInterface::STRING ),
															'field_custom'				=>	$fieldParams->get( 'cbconditional_customvalue' . $conditional, null, GetterInterface::RAW ),
															'field_custom_translate'	=>	$fieldParams->get( 'cbconditional_customvalue_translate' . $conditional, 0, GetterInterface::INT ),
															'field_viewaccesslevels'	=>	$fieldParams->get( 'cbconditional_customviewaccesslevels' . $conditional, null, GetterInterface::STRING ),
															'field_usergroups'			=>	$fieldParams->get( 'cbconditional_customusergroups' . $conditional, null, GetterInterface::STRING ),
															'operator_viewaccesslevels'	=>	0,
															'operator_usergroups'		=>	0,
															'operator'					=>	$fieldParams->get( 'cbconditional_operator' . $conditional, 0, GetterInterface::INT ),
															'value'						=>	$fieldParams->get( 'cbconditional_value' . $conditional, null, GetterInterface::RAW ),
															'value_translate'			=>	$fieldParams->get( 'cbconditional_value_translate' . $conditional, 0, GetterInterface::INT ),
															'location_reg'				=>	$fieldParams->get( 'cbconditional_target_reg' . $conditional, 1, GetterInterface::INT ),
															'location_edit'				=>	$fieldParams->get( 'cbconditional_target_edit' . $conditional, 1, GetterInterface::INT ),
															'location_view'				=>	$fieldParams->get( 'cbconditional_target_view' . $conditional, 1, GetterInterface::INT ),
															'location_search'			=>	$fieldParams->get( 'cbconditional_target_search' . $conditional, 0, GetterInterface::INT ),
															'location_list'				=>	$fieldParams->get( 'cbconditional_target_list' . $conditional, 1, GetterInterface::INT )
														);
				}

				$fieldsHide						=	explode( '|*|', $fieldParams->get( 'cbconditional_hide' . $conditional, null, GetterInterface::STRING ) );

				foreach ( $fieldsHide as $fieldId ) {
					$fieldOthers[$fieldId][]	=	array(	'mode'						=>	2,
															'field'						=>	$field->get( 'fieldid', 0, GetterInterface::INT ) . ',' . $field->get( 'name', null, GetterInterface::STRING ),
															'field_custom'				=>	$fieldParams->get( 'cbconditional_customvalue' . $conditional, null, GetterInterface::RAW ),
															'field_custom_translate'	=>	$fieldParams->get( 'cbconditional_customvalue_translate' . $conditional, 0, GetterInterface::INT ),
															'field_viewaccesslevels'	=>	$fieldParams->get( 'cbconditional_customviewaccesslevels' . $conditional, null, GetterInterface::STRING ),
															'field_usergroups'			=>	$fieldParams->get( 'cbconditional_customusergroups' . $conditional, null, GetterInterface::STRING ),
															'operator_viewaccesslevels'	=>	0,
															'operator_usergroups'		=>	0,
															'operator'					=>	$fieldParams->get( 'cbconditional_operator' . $conditional, 0, GetterInterface::INT ),
															'value'						=>	$fieldParams->get( 'cbconditional_value' . $conditional, null, GetterInterface::RAW ),
															'value_translate'			=>	$fieldParams->get( 'cbconditional_value_translate' . $conditional, 0, GetterInterface::INT ),
															'location_reg'				=>	$fieldParams->get( 'cbconditional_target_reg' . $conditional, 1, GetterInterface::INT ),
															'location_edit'				=>	$fieldParams->get( 'cbconditional_target_edit' . $conditional, 1, GetterInterface::INT ),
															'location_view'				=>	$fieldParams->get( 'cbconditional_target_view' . $conditional, 1, GetterInterface::INT ),
															'location_search'			=>	$fieldParams->get( 'cbconditional_target_search' . $conditional, 0, GetterInterface::INT ),
															'location_list'				=>	$fieldParams->get( 'cbconditional_target_list' . $conditional, 1, GetterInterface::INT )
														);
				}
			} else {
				$mode							=	( $fieldParams->get( 'cbconditional_mode' . $conditional, 0, GetterInterface::INT ) ? 1 : 2 );

				if ( ! $fieldCondition ) {
					$fieldCondition				=	$mode;
				}

				$conditionField					=	$fieldParams->get( 'cbconditional_field' . $conditional, null, GetterInterface::STRING );

				if ( in_array( $conditionField, array( 'customviewaccesslevels', 'customusergroups' ) ) ) {
					$operator					=	0;
				} else {
					$operator					=	$fieldParams->get( 'cbconditional_operator' . $conditional, 0, GetterInterface::INT );
				}

				if ( $fieldCondition != $mode ) {
					$operator					=	plug_cbconditional_invert_operator( $operator );
				}

				$fieldConditions[]				=	array(	'field'						=>	$conditionField,
															'field_custom'				=>	$fieldParams->get( 'cbconditional_customvalue' . $conditional, null, GetterInterface::RAW ),
															'field_custom_translate'	=>	$fieldParams->get( 'cbconditional_customvalue_translate' . $conditional, 0, GetterInterface::INT ),
															'field_viewaccesslevels'	=>	$fieldParams->get( 'cbconditional_customviewaccesslevels' . $conditional, null, GetterInterface::STRING ),
															'field_usergroups'			=>	$fieldParams->get( 'cbconditional_customusergroups' . $conditional, null, GetterInterface::STRING ),
															'operator_viewaccesslevels'	=>	( in_array( $operator, array( 0, 1 ) ) ? $operator : 0 ),
															'operator_usergroups'		=>	( in_array( $operator, array( 0, 1 ) ) ? $operator : 0 ),
															'operator'					=>	$operator,
															'value'						=>	$fieldParams->get( 'cbconditional_value' . $conditional, null, GetterInterface::RAW ),
															'value_translate'			=>	$fieldParams->get( 'cbconditional_value_translate' . $conditional, 0, GetterInterface::INT ),
															'location_registration'		=>	$fieldParams->get( 'cbconditional_target_reg' . $conditional, 1, GetterInterface::INT ),
															'location_profile_edit'		=>	$fieldParams->get( 'cbconditional_target_edit' . $conditional, 1, GetterInterface::INT ),
															'location_profile_view'		=>	$fieldParams->get( 'cbconditional_target_view' . $conditional, 1, GetterInterface::INT ),
															'location_userlist_search'	=>	$fieldParams->get( 'cbconditional_target_search' . $conditional, 0, GetterInterface::INT ),
															'location_userlist_view'	=>	$fieldParams->get( 'cbconditional_target_list' . $conditional, 1, GetterInterface::INT )
														);
			}

			plug_cbconditional_remove_params( $fieldParams, $conditional );
		}

		if ( $fieldConditions ) {
			$fieldParams->set( 'cbconditional_conditioned', $fieldCondition );

			$fieldParams->set( 'cbconditional_conditions', array( array( 'condition' => $fieldConditions ) ) );

			$fieldParams->set( 'cbconditional_debug', $debug );
		}

		if ( $fieldParamsOld != $fieldParams->asJson() ) {
			$field->set( 'params', $fieldParams->asJson() );

			$field->store();
		}
	}

	// Migration field conditional others:
	foreach ( $fieldOthers as $fieldId => $conditions ) {
		$field									=	new FieldTable();

		$field->load( $fieldId );

		if ( ! $field->get( 'fieldid', 0, GetterInterface::INT ) ) {
			continue;
		}

		$fieldParams							=	new Registry( $field->params );
		$fieldParamsOld							=	$fieldParams->asJson();

		$fieldCondition							=	$fieldParams->get( 'cbconditional_conditioned', 0, GetterInterface::INT );
		$fieldConditions						=	array();

		foreach ( $conditions as $condition ) {
			$mode								=	$condition['mode'];

			if ( ! $fieldCondition ) {
				$fieldCondition					=	$mode;
			}

			$conditionField						=	$condition['field'];

			if ( $conditionField == 'customviewaccesslevels' ) {
				$operator						=	$condition['operator_viewaccesslevels'];
			} elseif ( $conditionField == 'customusergroups' ) {
				$operator						=	$condition['operator_usergroups'];
			} else {
				$operator						=	$condition['operator'];
			}

			if ( $fieldCondition != $mode ) {
				$operator						=	plug_cbconditional_invert_operator( $operator );
			}

			$fieldConditions[]					=	array(	'field'						=>	$condition['field'],
															'field_custom'				=>	$condition['field_custom'],
															'field_custom_translate'	=>	$condition['field_custom_translate'],
															'field_viewaccesslevels'	=>	$condition['field_viewaccesslevels'],
															'field_usergroups'			=>	$condition['field_usergroups'],
															'operator_viewaccesslevels'	=>	( in_array( $operator, array( 0, 1 ) ) ? $operator : 0 ),
															'operator_usergroups'		=>	( in_array( $operator, array( 0, 1 ) ) ? $operator : 0 ),
															'operator'					=>	$operator,
															'value'						=>	$condition['value'],
															'value_translate'			=>	$condition['value_translate'],
															'location_registration'		=>	$condition['location_reg'],
															'location_profile_edit'		=>	$condition['location_edit'],
															'location_profile_view'		=>	$condition['location_view'],
															'location_userlist_search'	=>	$condition['location_search'],
															'location_userlist_view'	=>	$condition['location_list']
														);
		}

		if ( $fieldConditions ) {
			$fieldParams->set( 'cbconditional_conditioned', $fieldCondition );

			// We need to add the conditional others usages as OR cases to existing conditions:
			$existingConditions					=	$fieldParams->subTree( 'cbconditional_conditions' )->asArray();

			foreach ( $fieldConditions as $newCondition ) {
				$existingConditions[]			=	array( 'condition' => array( $newCondition ) );
			}

			$fieldParams->set( 'cbconditional_conditions', $existingConditions );

			$fieldParams->set( 'cbconditional_debug', $debug );
		}

		if ( $fieldParamsOld != $fieldParams->asJson() ) {
			$field->set( 'params', $fieldParams->asJson() );

			$field->store();
		}
	}
}

/**
 * @param int $operator
 * @return int
 */
function plug_cbconditional_invert_operator( $operator )
{
	switch ( $operator ) {
		case 1:
			$operator	=	0;
			break;
		case 2:
			$operator	=	3;
			break;
		case 3:
			$operator	=	2;
			break;
		case 4:
			$operator	=	5;
			break;
		case 5:
			$operator	=	4;
			break;
		case 6:
			$operator	=	7;
			break;
		case 7:
			$operator	=	6;
			break;
		case 8:
			$operator	=	9;
			break;
		case 9:
			$operator	=	8;
			break;
		case 10:
			$operator	=	11;
			break;
		case 11:
			$operator	=	10;
			break;
		case 0:
		default:
			$operator	=	1;
			break;
	}

	return $operator;
}

/**
 * @param Registry $oldParams
 * @param int      $conditional
 */
function plug_cbconditional_remove_params( &$oldParams, $conditional = 0 )
{
	$oldParams->unsetEntry( 'cbconditional_display' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_field' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_customvalue' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_customvalue_translate' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_customviewaccesslevels' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_customusergroups' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_operator' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_value' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_value_translate' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_show' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_hide' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_options_show' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_options_hide' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_mode' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_target_reg' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_target_edit' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_target_view' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_target_search' . $conditional );
	$oldParams->unsetEntry( 'cbconditional_target_list' . $conditional );
}