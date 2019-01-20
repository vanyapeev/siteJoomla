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

function plug_cbactivity_install()
{
	global $_CB_database;

	// Migrate activity:
	$table								=	'#__comprofiler_plugin_activity';
	$fields								=	$_CB_database->getTableFields( $table );

	if ( isset( $fields[$table]['type'] ) ) {
		// Migrate Profile activity:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'profile' ) . ", IF( " . $_CB_database->NameQuote( 'item' ) . " != '', " . $_CB_database->NameQuote( 'item' ) . ", " . $_CB_database->NameQuote( 'user_id' ) . " ), IF( " . $_CB_database->NameQuote( 'subtype' ) . " != '', " . $_CB_database->NameQuote( 'subtype' ) . ", NULL ) )"
										.	", " . $_CB_database->NameQuote( 'title' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'message' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'params' ) . " = NULL"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'profile' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate Status activity:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'profile' ) . ", IF( " . $_CB_database->NameQuote( 'parent' ) . " != '', " . $_CB_database->NameQuote( 'parent' ) . ", " . $_CB_database->NameQuote( 'user_id' ) . " ) )"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'status' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate Field activity:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'profile' ) . ", IF( " . $_CB_database->NameQuote( 'parent' ) . " != '', " . $_CB_database->NameQuote( 'parent' ) . ", " . $_CB_database->NameQuote( 'user_id' ) . " ), " . $_CB_database->Quote( 'field' ) . ", " . $_CB_database->NameQuote( 'item' ) . " )"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'field' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate Commented/Tagged activity:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'activity' ) . ", " . $_CB_database->NameQuote( 'item' ) . ", " . $_CB_database->NameQuote( 'subtype' ) . " )"
										.	", " . $_CB_database->NameQuote( 'title' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'message' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'params' ) . " = NULL"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'activity' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate GroupJive activity:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'groupjive.group' ) . ", IF( " . $_CB_database->NameQuote( 'parent' ) . " != '', " . $_CB_database->NameQuote( 'parent' ) . ", " . $_CB_database->NameQuote( 'item' ) . " ), IF( " . $_CB_database->NameQuote( 'subtype' ) . " != '', IF( " . $_CB_database->NameQuote( 'subtype' ) . " != " . $_CB_database->Quote( 'group' ) . ", REPLACE( " . $_CB_database->NameQuote( 'subtype' ) . ", " . $_CB_database->Quote( 'group.' ) . ", '' ), " . $_CB_database->Quote( 'create' ) . " ), NULL ), IF( " . $_CB_database->NameQuote( 'parent' ) . " != '', " . $_CB_database->NameQuote( 'item' ) . ", NULL ) )"
										.	", " . $_CB_database->NameQuote( 'title' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'message' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'params' ) . " = NULL"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'groupjive' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate Blog activity:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'blog' ) . ", " . $_CB_database->NameQuote( 'item' ) . " )"
										.	", " . $_CB_database->NameQuote( 'title' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'message' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'params' ) . " = NULL"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'blog' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate Gallery activity:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'gallery' ) . ", " . $_CB_database->NameQuote( 'subtype' ) . ", " . $_CB_database->NameQuote( 'item' ) . " )"
										.	", " . $_CB_database->NameQuote( 'title' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'message' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'params' ) . " = NULL"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'gallery' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate Forum activity:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'kunena' ) . ", " . $_CB_database->NameQuote( 'item' ) . ", " . $_CB_database->NameQuote( 'subtype' ) . " )"
										.	", " . $_CB_database->NameQuote( 'title' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'message' ) . " = NULL"
										.	", " . $_CB_database->NameQuote( 'params' ) . " = NULL"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'kunena' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate activity:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', IF( " . $_CB_database->NameQuote( 'type' ) . " != '', " . $_CB_database->NameQuote( 'type' ) . ", NULL ), IF( " . $_CB_database->NameQuote( 'parent' ) . " != '', " . $_CB_database->NameQuote( 'parent' ) . ", NULL ), IF( " . $_CB_database->NameQuote( 'subtype' ) . " != '', " . $_CB_database->NameQuote( 'subtype' ) . ", NULL ), IF( " . $_CB_database->NameQuote( 'item' ) . " != '', " . $_CB_database->NameQuote( 'item' ) . ", NULL ) )"
										.	"\n WHERE ( " . $_CB_database->NameQuote( 'asset' ) . " IS NULL OR " . $_CB_database->NameQuote( 'asset' ) . " = '' )";
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Remove the old columns:
		$_CB_database->dropColumn( $table, 'type' );
		$_CB_database->dropColumn( $table, 'subtype' );
		$_CB_database->dropColumn( $table, 'item' );
		$_CB_database->dropColumn( $table, 'parent' );
	}

	// Migrate comments:
	$table								=	'#__comprofiler_plugin_activity_comments';
	$fields								=	$_CB_database->getTableFields( $table );

	if ( isset( $fields[$table]['type'] ) ) {
		// Migrate Field comments:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'profile' ) . ", IF( " . $_CB_database->NameQuote( 'parent' ) . " != '', " . $_CB_database->NameQuote( 'parent' ) . ", " . $_CB_database->NameQuote( 'user_id' ) . " ), " . $_CB_database->Quote( 'field' ) . ", " . $_CB_database->NameQuote( 'item' ) . " )"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'field' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate Activity comments:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'activity' ) . ", " . $_CB_database->NameQuote( 'item' ) . ", " . $_CB_database->NameQuote( 'subtype' ) . " )"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'activity' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate GroupJive comments:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'groupjive.group' ) . ", IF( " . $_CB_database->NameQuote( 'parent' ) . " != '', " . $_CB_database->NameQuote( 'parent' ) . ", " . $_CB_database->NameQuote( 'item' ) . " ), IF( " . $_CB_database->NameQuote( 'subtype' ) . " != '', IF( " . $_CB_database->NameQuote( 'subtype' ) . " != " . $_CB_database->Quote( 'group' ) . ", REPLACE( " . $_CB_database->NameQuote( 'subtype' ) . ", " . $_CB_database->Quote( 'group.' ) . ", '' ), " . $_CB_database->Quote( 'create' ) . " ), NULL ), IF( " . $_CB_database->NameQuote( 'parent' ) . " != '', " . $_CB_database->NameQuote( 'item' ) . ", NULL ) )"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'groupjive' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate Blog comments:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'blog' ) . ", " . $_CB_database->NameQuote( 'item' ) . " )"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'blog' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate Gallery comments:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'gallery' ) . ", " . $_CB_database->NameQuote( 'subtype' ) . ", " . $_CB_database->NameQuote( 'item' ) . " )"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'gallery' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate Forum comments:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', " . $_CB_database->Quote( 'kunena' ) . ", " . $_CB_database->NameQuote( 'item' ) . ", " . $_CB_database->NameQuote( 'subtype' ) . " )"
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'kunena' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate comments:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', IF( " . $_CB_database->NameQuote( 'type' ) . " != '', " . $_CB_database->NameQuote( 'type' ) . ", NULL ), IF( " . $_CB_database->NameQuote( 'parent' ) . " != '', " . $_CB_database->NameQuote( 'parent' ) . ", NULL ), IF( " . $_CB_database->NameQuote( 'subtype' ) . " != '', " . $_CB_database->NameQuote( 'subtype' ) . ", NULL ), IF( " . $_CB_database->NameQuote( 'item' ) . " != '', " . $_CB_database->NameQuote( 'item' ) . ", NULL ) )"
										.	"\n WHERE ( " . $_CB_database->NameQuote( 'asset' ) . " IS NULL OR " . $_CB_database->NameQuote( 'asset' ) . " = '' )";
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Remove the old columns:
		$_CB_database->dropColumn( $table, 'type' );
		$_CB_database->dropColumn( $table, 'subtype' );
		$_CB_database->dropColumn( $table, 'item' );
		$_CB_database->dropColumn( $table, 'parent' );
	}

	// Migration notification activity to notifications table:
	$query								=	"INSERT INTO " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_notifications' )
										.	"\n ("
										.		$_CB_database->NameQuote( 'user_id' )
										.		", " . $_CB_database->NameQuote( 'user' )
										.		", " . $_CB_database->NameQuote( 'asset' )
										.		", " . $_CB_database->NameQuote( 'title' )
										.		", " . $_CB_database->NameQuote( 'message' )
										.		", " . $_CB_database->NameQuote( 'published' )
										.		", " . $_CB_database->NameQuote( 'date' )
										.		", " . $_CB_database->NameQuote( 'params' )
										.	")"
										.	"\n SELECT "
										.		$_CB_database->NameQuote( 'user_id' )
										.		", SUBSTRING_INDEX( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'asset' ) . ", '.', 2 ), '.', -1 )"
										.		", REPLACE( REPLACE( " . $_CB_database->NameQuote( 'asset' ) . ", CONCAT( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'asset' ) . ", '.', 2 ), '.' ), '' ), " . $_CB_database->Quote( 'notification.' ) . ", " . $_CB_database->Quote( 'profile.' ) . " )"
										.		", " . $_CB_database->NameQuote( 'title' )
										.		", " . $_CB_database->NameQuote( 'message' )
										.		", " . $_CB_database->NameQuote( 'published' )
										.		", " . $_CB_database->NameQuote( 'date' )
										.		", " . $_CB_database->NameQuote( 'params' )
										.	" FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity' ) . " WHERE " . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( 'notification.%' );
	$_CB_database->setQuery( $query );
	$_CB_database->query();

	$query								=	"DELETE"
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( 'notification.%' );
	$_CB_database->setQuery( $query );
	$_CB_database->query();

	// Migrate tags:
	$table								=	'#__comprofiler_plugin_activity_tags';
	$fields								=	$_CB_database->getTableFields( $table );

	if ( isset( $fields[$table]['type'] ) ) {
		// Migrate tags:
		$query							=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_activity_tags' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT_WS( '.', IF( " . $_CB_database->NameQuote( 'type' ) . " != '', " . $_CB_database->NameQuote( 'type' ) . ", NULL ), IF( " . $_CB_database->NameQuote( 'parent' ) . " != '', " . $_CB_database->NameQuote( 'parent' ) . ", NULL ), IF( " . $_CB_database->NameQuote( 'subtype' ) . " != '', " . $_CB_database->NameQuote( 'subtype' ) . ", NULL ), IF( " . $_CB_database->NameQuote( 'item' ) . " != '', " . $_CB_database->NameQuote( 'item' ) . ", NULL ) )"
										.	", " . $_CB_database->NameQuote( 'tag' ) . " = " . $_CB_database->NameQuote( 'user' )
										.	"\n WHERE ( " . $_CB_database->NameQuote( 'asset' ) . " IS NULL OR " . $_CB_database->NameQuote( 'asset' ) . " = '' )";
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Remove the old columns:
		$_CB_database->dropColumn( $table, 'type' );
		$_CB_database->dropColumn( $table, 'subtype' );
		$_CB_database->dropColumn( $table, 'item' );
		$_CB_database->dropColumn( $table, 'parent' );
		$_CB_database->dropColumn( $table, 'user' );
	}

	// Migrate old global, tab, and field params if they exist:
	$tab								=	new TabTable();

	if ( $tab->load( array( 'pluginclass' => 'cbactivityTab' ) ) ) {
		$tabParams						=	new Registry( $tab->params );
		$tabMigrate						=	false;

		foreach ( $tabParams as $paramName => $paramValue ) {
			if ( strpos( $paramName, 'tab_activity_' ) !== 0 ) {
				continue;
			}

			$newParamName				=	str_replace( 'tab_activity_', 'activity_', $paramName );

			if ( $newParamName == $paramName ) {
				continue;
			}

			$tabParams->set( $newParamName, $paramValue );

			$tabMigrate					=	true;
		}

		if ( $tabMigrate ) {
			plug_cbactivity_migrate_params( $tabParams );

			$tab->set( 'params', $tabParams->asJson() );

			$tab->store();
		}
	}

	$query								=	'SELECT *'
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " IN " . $_CB_database->safeArrayOfStrings( array( 'activity', 'comments' ) );
	$_CB_database->setQuery( $query );
	$fields								=	$_CB_database->loadObjectList( null, '\CB\Database\Table\FieldTable', array( $_CB_database ) );

	/** @var FieldTable[] $fields */
	foreach ( $fields as $field ) {
		$fieldParams					=	new Registry( $field->params );
		$fieldMigrate					=	false;

		foreach ( $fieldParams as $paramName => $paramValue ) {
			if ( ( strpos( $paramName, 'field_activity_' ) !== 0 )  && ( strpos( $paramName, 'field_comments_' ) !== 0 ) ) {
				continue;
			}

			$newParamName				=	str_replace( array( 'field_activity_', 'field_comments_' ), array( 'activity_', 'comments_' ), $paramName );

			if ( $newParamName == $paramName ) {
				continue;
			}

			$fieldParams->set( $newParamName, $paramValue );

			$fieldMigrate				=	true;
		}

		if ( $fieldMigrate ) {
			plug_cbactivity_migrate_params( $fieldParams );

			$field->set( 'params', $fieldParams->asJson() );

			$field->store();
		}
	}

	$plugin								=	new PluginTable();

	if ( $plugin->load( array( 'element' => 'cbactivity' ) ) ) {
		$pluginParams					=	new Registry( $plugin->params );

		if ( plug_cbactivity_migrate_params( $pluginParams ) ) {
			$plugin->set( 'params', $pluginParams->asJson() );

			$plugin->store();
		}
	}
}

/**
 * @param Registry $oldParams
 * @return bool
 */
function plug_cbactivity_migrate_params( &$oldParams )
{
	$params				=	array(	'cleanup_duration' => 'cleanup_activity', 'activity_limit' => array( 'activity_paging_first_limit', 'activity_paging_limit' ),
									'activity_comments_limit' => array( 'activity_comments_paging_first_limit', 'activity_comments_paging_limit' ),
									'activity_comments_replies_limit' => array( 'activity_comments_replies_paging_first_limit', 'activity_comments_replies_paging_limit' )
								);

	$migrated			=	false;

	foreach ( $params as $oldParam => $newParam ) {
		if ( ! $oldParams->has( $oldParam ) ) {
			continue;
		}

		$oldValue		=	$oldParams->get( $oldParam, null, GetterInterface::RAW );

		if ( is_array( $newParam ) ) {
			foreach ( $newParam as $newSubParam ) {
				$oldParams->set( $newSubParam, $oldValue );
			}
		} else {
			$oldParams->set( $newParam, $oldValue );
		}

		$oldParams->unsetEntry( $oldParam );

		$migrated		=	true;
	}

	return $migrated;
}
