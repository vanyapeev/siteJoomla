<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Database\Table\Table;
use CB\Database\Table\FieldTable;
use CB\Database\Table\TabTable;
use CB\Database\Table\PluginTable;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

function plug_cbprivacy_install()
{
	global $_CB_database;

	// Migrate privacy rules:
	$table					=	'#__comprofiler_plugin_privacy';
	$fields					=	$_CB_database->getTableFields( $table );

	if ( isset( $fields[$table]['type'] ) ) {
		// Migrate type, subtype, and item to asset
		$query				=	"UPDATE ". $_CB_database->NameQuote( '#__comprofiler_plugin_privacy' )
							.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', IF( " . $_CB_database->NameQuote( 'type' ) . " != '', " . $_CB_database->NameQuote( 'type' ) . ", NULL ), IF( " . $_CB_database->NameQuote( 'subtype' ) . " != '', " . $_CB_database->NameQuote( 'subtype' ) . ", NULL ), IF( " . $_CB_database->NameQuote( 'item' ) . " != '', " . $_CB_database->NameQuote( 'item' ) . ", NULL ) )"
							.	"\n WHERE ( " . $_CB_database->NameQuote( 'asset' ) . " IS NULL OR " . $_CB_database->NameQuote( 'asset' ) . " = '' )";
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate multiple rule rows to single rule per row
		$query				=	"SELECT *"
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_privacy' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'rule' ) . " LIKE " . $_CB_database->Quote( '%|*|%' );
		$_CB_database->setQuery( $query );
		$rows				=	$_CB_database->loadObjectList( null, '\CBLib\Database\Table\Table', array( $_CB_database, '#__comprofiler_plugin_privacy', 'id' ) );

		/** @var $rows Table[] */
		foreach ( $rows as $row ) {
			$rules			=	explode( '|*|', $row->get( 'rule', null, GetterInterface::STRING ) );

			if ( count( $rules ) <= 1 ) {
				continue;
			}

			foreach ( $rules as $i => $rule ) {
				if ( $i != 0 ) {
					$row->set( 'id', 0 );
				}

				$row->set( 'rule', $rule );

				$row->store();
			}
		}

		// Remove the old columns:
		$_CB_database->dropColumn( $table, 'type' );
		$_CB_database->dropColumn( $table, 'subtype' );
		$_CB_database->dropColumn( $table, 'item' );
	}

	// Migrate old field param values
	$query					=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'params' ) . " LIKE " . $_CB_database->Quote( '%cbprivacy_default%' );
	$_CB_database->setQuery( $query );
	$fields					=	$_CB_database->loadObjectList( null, '\CB\Database\Table\FieldTable', array( $_CB_database ) );

	/** @var FieldTable[] $fields */
	foreach ( $fields as $field ) {
		$fieldParams		=	new Registry( $field->params );
		$migrated			=	false;

		if ( $fieldParams->has( 'cbprivacy_default' ) ) {
			$fieldParams->set( 'privacy_options_default', $fieldParams->get( 'cbprivacy_default', null, GetterInterface::STRING ) );
			$fieldParams->unsetEntry( 'cbprivacy_default' );

			$migrated		=	true;
		}

		if ( $fieldParams->get( 'cbprivacy_edit', 0, GetterInterface::INT ) === 1 ) {
			$fieldParams->set( 'cbprivacy_edit', 1 );
			$field->set( 'edit', 0 );

			$migrated		=	true;
		}

		if ( $migrated ) {
			$field->set( 'params', $fieldParams->asJson() );

			$field->store();
		}
	}

	// Migrate old tab param values
	$query					=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_tabs' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'params' ) . " LIKE " . $_CB_database->Quote( '%cbprivacy_default%' );
	$_CB_database->setQuery( $query );
	$tabs					=	$_CB_database->loadObjectList( null, '\CB\Database\Table\TabTable', array( $_CB_database ) );

	/** @var TabTable[] $tabs */
	foreach ( $tabs as $tab ) {
		$tabParams			=	new Registry( $tab->params );
		$migrated			=	false;

		if ( $tabParams->has( 'cbprivacy_default' ) ) {
			$tabParams->set( 'privacy_options_default', $tabParams->get( 'cbprivacy_default', null, GetterInterface::STRING ) );
			$tabParams->unsetEntry( 'cbprivacy_default' );

			$migrated		=	true;
		}

		if ( $migrated ) {
			$tab->set( 'params', $tabParams->asJson() );

			$tab->store();
		}
	}

	// Migrate old param values
	$plugin					=	new PluginTable();

	if ( $plugin->load( array( 'element' => 'cbprivacy' ) ) ) {
		$pluginParams		=	new Registry( $plugin->params );
		$migrated			=	false;

		if ( $pluginParams->get( 'privacy_options_conntypes', null, GetterInterface::STRING ) === '' ) {
			$pluginParams->set( 'privacy_options_conntype', 0 );
			$pluginParams->set( 'privacy_options_conntypes', 0 );

			$migrated		=	true;
		}

		if ( $pluginParams->get( 'privacy_options_viewaccesslevels', null, GetterInterface::STRING ) === '' ) {
			$pluginParams->set( 'privacy_options_viewaccesslevel', 0 );
			$pluginParams->set( 'privacy_options_viewaccesslevels', 0 );

			$migrated		=	true;
		}

		if ( $pluginParams->get( 'privacy_options_usergroups', null, GetterInterface::STRING ) === '' ) {
			$pluginParams->set( 'privacy_options_usergroup', 0 );
			$pluginParams->set( 'privacy_options_usergroups', 0 );

			$migrated		=	true;
		}

		if ( $migrated ) {
			$plugin->set( 'params', $pluginParams->asJson() );

			$plugin->store();
		}
	}
}