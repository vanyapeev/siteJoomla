<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Registry\GetterInterface;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CBLib\Database\Table\Table;
use CB\Plugin\AutoActions\CBAutoActions;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

function plug_cbautoactions_install()
{
	global $_CB_database;

	$table									=	'#__comprofiler_plugin_autoactions';
	$fields									=	$_CB_database->getTableFields( $table );

	if ( isset( $fields[$table]['field'] ) ) {
		$translateExists					=	isset( $fields[$table]['translate'] );
		$excludeExists						=	isset( $fields[$table]['exclude'] );
		$debugExists						=	isset( $fields[$table]['debug'] );

		$query								=	"SELECT *"
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' );
		$_CB_database->setQuery( $query );
		$rows								=	$_CB_database->loadObjectList( null, '\CBLib\Database\Table\Table', array( $_CB_database, '#__comprofiler_plugin_autoactions', 'id' ) );

		/** @var $rows Table[] */
		foreach ( $rows as $row ) {
			$row->set( 'system', '' );
			$row->set( 'trigger', str_replace( ',', '|*|', $row->get( 'trigger' ) ) );

			$newParams						=	new Registry( $row->get( 'params' ) );

			if ( $row->get( 'field' ) ) {
				$fields						=	new Registry( $row->get( 'field' ) );
				$operators					=	new Registry( $row->get( 'operator' ) );
				$values						=	new Registry( $row->get( 'value' ) );

				if ( $translateExists ) {
					$translates				=	new Registry( $row->get( 'translate' ) );
				} else {
					$translates				=	null;
				}

				$conditionals				=	count( $fields );

				if ( $conditionals ) {
					$conditions				=	array();

					for ( $i = 0, $n = $conditionals; $i < $n; $i++ ) {
						$field				=	$fields->get( "field$i" );
						$operator			=	$operators->get( "operator$i" );
						$value				=	$values->get( "value$i" );

						if ( $translateExists ) {
							$translate		=	$translates->get( "translate$i" );
						} else {
							$translate		=	0;
						}

						if ( $operator !== '' ) {
							$conditions[]	=	array( 'field' => $field, 'operator' => $operator, 'value' => $value, 'translate' => $translate );
						}
					}

					if ( $conditions ) {
						$newConditionals	=	new Registry( $conditions );

						$row->set( 'conditions', $newConditionals->asJson() );
					}
				}

				$row->set( 'field', null );
				$row->set( 'operator', null );
				$row->set( 'value', null );

				if ( $translateExists ) {
					$row->set( 'translate', null );
				}
			}

			if ( $excludeExists ) {
				$exclude					=	$row->get( 'exclude' );

				if ( $exclude ) {
					$newParams->set( 'exclude', $exclude );
					$row->set( 'exclude', null );
				}
			}

			if ( $debugExists ) {
				$debug						=	$row->get( 'debug' );

				if ( $debug ) {
					$newParams->set( 'debug', $debug );
					$row->set( 'debug', null );
				}
			}

			if ( method_exists( 'cbautoactionsMigrate', $row->get( 'type' ) ) ) {
				call_user_func_array( array( 'cbautoactionsMigrate', $row->get( 'type' ) ), array( &$row, &$newParams ) );
			}

			$row->set( 'params', $newParams->asJson() );

			$row->store( true );
		}

		$_CB_database->dropColumn( $table, 'field' );
		$_CB_database->dropColumn( $table, 'operator' );
		$_CB_database->dropColumn( $table, 'value' );

		if ( $translateExists ) {
			$_CB_database->dropColumn( $table, 'translate' );
		}

		if ( $excludeExists ) {
			$_CB_database->dropColumn( $table, 'exclude' );
		}

		if ( $debugExists ) {
			$_CB_database->dropColumn( $table, 'debug' );
		}
	} else {
		// Set old non-system int usages to empty string:
		$query								=	"UPDATE " . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' )
											.	"\n SET " . $_CB_database->NameQuote( 'system' ) . " = ''"
											.	"\n WHERE " . $_CB_database->NameQuote( 'system' ) . " = '0'";
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate actions:
		$query								=	"SELECT *"
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' )
											.	"\n WHERE " . $_CB_database->NameQuote( 'system' ) . " = ''";
		$_CB_database->setQuery( $query );
		$rows								=	$_CB_database->loadObjectList( null, '\CBLib\Database\Table\Table', array( $_CB_database, '#__comprofiler_plugin_autoactions', 'id' ) );

		/** @var $rows Table[] */
		foreach ( $rows as $row ) {
			$migrated						=	false;
			$oldTriggers					=	$row->get( 'triggers', null, GetterInterface::STRING );
			$newTriggers					=	str_replace( ',', '|*|', $oldTriggers );

			if ( $oldTriggers != $newTriggers ) {
				$row->set( 'triggers', $newTriggers );

				$migrated					=	true;
			}

			$rowParams						=	new Registry( $row->get( 'params', null, GetterInterface::RAW ) );
			$rowConditions					=	new Registry( $row->get( 'conditions', null, GetterInterface::RAW ) );
			$oldConditions					=	$rowConditions->asJson();

			foreach ( $rowConditions as $i => $condition ) {
				/** @var ParamsInterface $condition */
				if ( $condition->get( 'format', null, GetterInterface::BOOLEAN ) === null ) {
					$rowConditions->set( $i . '.format', 1 );
				}

				if ( $condition->get( 'content_plugins', null, GetterInterface::BOOLEAN ) === null ) {
					$rowConditions->set( $i . '.content_plugins', $rowParams->get( 'content_plugins', 0, GetterInterface::INT ) );
				}
			}

			$newConditions					=	$rowConditions->asJson();

			if ( $oldConditions != $newConditions ) {
				$row->set( 'conditions', $newConditions );

				$migrated					=	true;
			}

			if ( method_exists( 'cbautoactionsMigrate', $row->get( 'type', null, GetterInterface::STRING ) ) ) {
				$oldParams					=	$rowParams->asJson();

				call_user_func_array( array( 'cbautoactionsMigrate', $row->get( 'type', null, GetterInterface::STRING ) ), array( &$row, &$rowParams ) );

				$newParams					=	$rowParams->asJson();

				if ( $oldParams != $newParams ) {
					$row->set( 'params', $newParams );

					$migrated				=	true;
				}
			}

			if ( $migrated ) {
				$row->store();
			}
		}
	}

	// Migrate old system int usages to new string usages (this helps maintain published state):
	$legacyMap								=	array(	1 => 'autologin', 100 => 'activityprofilelogin', 101 => 'activityprofilelogout', 102 => 'activityprofileregister',
														103 => 'activityprofileupdate', 104 => 'activityprofileavatar', 105 => 'activityprofilecanvas', 106 => 'activityaddconn',
														110 => 'activityremoveconn', 200 => 'activitycomment', 201 => 'activitytag', 300 => 'activitygallerycreate', 301 => 'activitygallerycomments',
														303 => 'activitygallerydelete', 400 => 'activityblogcreate', 402 => 'activityblogdelete', 500 => 'activitykunenacreate',
														501 => 'activitykunenareply', 502 => 'activitykunenadelete', 601 => 'activitygjgrpcreate', 604 => 'activitygjgrpdelete',
														606 => 'activitygjgrpjoin', 607 => 'activitygjgrpleave', 608 => 'activitygjwallcreate', 611 => 'activitygjwalldelete',
														612 => 'activitygjphotocreate', 614 => 'activitygjphotodelete', 615 => 'activitygjfilecreate', 617 => 'activitygjfiledelete',
														618 => 'activitygjvideocreate', 620 => 'activitygjvideodelete', 621 => 'activitygjeventscreate', 623 => 'activitygjeventsdelete'
													);

	foreach ( $legacyMap as $systemFrom => $systemTo ) {
		$query								=	"UPDATE " . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' )
											.	"\n SET " . $_CB_database->NameQuote( 'system' ) . " = " . $_CB_database->Quote( $systemTo )
											.	"\n WHERE " . $_CB_database->NameQuote( 'system' ) . " = " . $_CB_database->Quote( $systemFrom );
		$_CB_database->setQuery( $query );
		$_CB_database->query();
	}

	CBAutoActions::installSystemActions();
}

class cbautoactionsMigrate
{

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function activity( &$trigger, &$params )
	{
		// Legacy Usage:
		$activityCount							=	substr_count( $params->asJson(), 'activity_owner' );

		if ( $activityCount ) {
			$newParams							=	array();
			$newParams['activity']				=	array();

			$paramsMap							=	array(	'activity_owner' => 'owner', 'activity_user' => 'user', 'activity_type' => 'type',
															'activity_subtype' => 'subtype', 'activity_item' => 'item', 'activity_from' => 'from',
															'activity_to' => 'to', 'activity_title' => 'title', 'activity_message' => 'message',
															'activity_icon' => 'icon', 'activity_class' => 'class'
														);

			for ( $i = 0, $n = $activityCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i							=	null;
				}

				$activity						=	array();

				foreach ( $paramsMap as $old => $new ) {
					$activity[$new]				=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['activity'][]		=	$activity;
			}

			$params->load( $newParams );
		}

		// CB Activity 4.0.0+ Usage:
		$newParams								=	array();

		foreach ( $params->subTree( 'activity' ) as $i => $activityRow ) {
			/** @var ParamsInterface $activityRow */
			if ( ! $activityRow instanceof ParamsInterface ) {
				$activityRow					=	new Registry( $activityRow );
			}

			$type								=	$activityRow->get( 'type', null, GetterInterface::STRING );
			$subtype							=	$activityRow->get( 'subtype', null, GetterInterface::STRING );
			$item								=	$activityRow->get( 'item', null, GetterInterface::STRING );
			$parent								=	$activityRow->get( 'parent', null, GetterInterface::STRING );
			$output								=	$activityRow->get( 'output', null, GetterInterface::STRING );

			if ( $type ) {
				$asset							=	$type;

				if ( $parent ) {
					$asset						.=	'.' . $parent;
				}

				if ( $subtype ) {
					$asset						.=	'.' . $subtype;
				}

				if ( $item ) {
					$asset						.=	'.' . $item;
				}

				$activityRow->set( 'asset', $asset );

				$activityRow->unsetEntry( 'type' );
				$activityRow->unsetEntry( 'subtype' );
				$activityRow->unsetEntry( 'item' );
				$activityRow->unsetEntry( 'parent' );

				$newParams['activity'][$i]		=	$activityRow->asArray();
			} else {
				$source							=	$activityRow->get( 'source', null, GetterInterface::STRING );

				if ( $source == 'profile' ) {
					$activityRow->set( 'asset', 'user.connections,following,global' );

					$activityRow->unsetEntry( 'source' );

					$newParams['activity'][$i]	=	$activityRow->asArray();
				} elseif ( $source == 'recent' ) {
					$activityRow->set( 'asset', 'all' );

					$activityRow->unsetEntry( 'source' );

					$newParams['activity'][$i]	=	$activityRow->asArray();
				}
			}

			if ( $output ) {
				$newParams['display']			=	strtolower( $output );

				$activityRow->unsetEntry( 'output' );

				$newParams['activity'][$i]		=	$activityRow->asArray();
			}
		}

		if ( $newParams ) {
			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function acymailing( &$trigger, &$params )
	{
		if ( $params->has( 'acymailing_subscribe' ) ) {
			$newParams							=	array();
			$newParams['acymailing']			=	array();

			$paramsMap							=	array(	'acymailing_subscribe' => 'subscribe', 'acymailing_unsubscribe' => 'unsubscribe',
															'acymailing_remove' => 'remove', 'acymailing_pending' => 'pending'
														);

			foreach ( $paramsMap as $old => $new ) {
				$newParams['acymailing'][$new]	=	$params->get( $old, null, GetterInterface::RAW );
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function antispam( &$trigger, &$params )
	{
		$antispamCount						=	substr_count( $params->asJson(), 'antispam_value' );

		if ( $antispamCount ) {
			$newParams						=	array();
			$newParams['antispam']			=	array();

			$paramsMap						=	array(	'antispam_mode' => 'mode', 'antispam_type' => 'type', 'antispam_value' => 'value',
														'antispam_duration' => 'duration', 'antispam_reason' => 'reason'
													);

			for ( $i = 0, $n = $antispamCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$antispam					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$antispam[$new]			=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['antispam'][]	=	$antispam;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function cbsubs30( &$trigger, &$params )
	{
		$cbsubsCount						=	substr_count( $params->asJson(), 'cbsubs30_plans' );

		if ( $cbsubsCount ) {
			$trigger->set( 'type', 'cbsubs' );

			$newParams						=	array();
			$newParams['cbsubs']			=	array();

			$paramsMap						=	array( 'cbsubs30_plans' => 'plans', 'cbsubs30_mode' => 'mode' );

			for ( $i = 0, $n = $cbsubsCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$cbsubs						=	array();

				foreach ( $paramsMap as $old => $new ) {
					$cbsubs[$new]			=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['cbsubs'][]		=	$cbsubs;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function code( &$trigger, &$params )
	{
		// Legacy Usage:
		$codeCount							=	substr_count( $params->asJson(), 'code_method' );

		if ( $codeCount ) {
			$newParams						=	array();
			$newParams['code']				=	array();

			$paramsMap						=	array(	'code_method' => 'method', 'code_code' => 'code', 'code_pluginurls' => 'pluginurls',
														'code_plugins' => 'plugins', 'code_url' => 'url', 'code_return' => 'return'
													);

			for ( $i = 0, $n = $codeCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$code						=	array();

				foreach ( $paramsMap as $old => $new ) {
					$code[$new]				=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['code'][]		=	$code;
			}

			$params->load( $newParams );
		}

		// 7.0.0 Usage:
		$newParams							=	array();

		foreach ( $params->subTree( 'code' ) as $i => $codeRow ) {
			/** @var ParamsInterface $codeRow */
			if ( ! $codeRow instanceof ParamsInterface ) {
				$codeRow					=	new Registry( $codeRow );
			}

			$output							=	$codeRow->get( 'return', null, GetterInterface::STRING );

			if ( $output ) {
				if ( $output == 'SILENT' ) {
					$output					=	'none';
				}

				$newParams['display']		=	strtolower( $output );

				$codeRow->unsetEntry( 'output' );

				$newParams['code'][$i]		=	$codeRow->asArray();
			}
		}

		if ( $newParams ) {
			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function connection( &$trigger, &$params )
	{
		$connectionCount					=	substr_count( $params->asJson(), 'connection_users' );

		if ( $connectionCount ) {
			$newParams						=	array();
			$newParams['connection']		=	array();

			$paramsMap						=	array( 'connection_users' => 'users', 'connection_message' => 'message', 'connection_direction' => 'direction' );

			for ( $i = 0, $n = $connectionCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$connection					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$connection[$new]		=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['connection'][]	=	$connection;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function content( &$trigger, &$params )
	{
		$contentCount					=	substr_count( $params->asJson(), 'content_title' );

		if ( $contentCount ) {
			$newParams					=	array();
			$newParams['content']		=	array();

			$paramsMap					=	array(	'content_mode' => 'mode', 'content_title' => 'title', 'content_alias' => 'alias',
													'content_category_j' => 'category_j', 'content_category_k' => 'category_k', 'content_introtext' => 'introtext',
													'content_fulltext' => 'fulltext', 'content_metadesc' => 'metadesc', 'content_metakey' => 'metakey',
													'content_access' => 'access', 'content_published' => 'published', 'content_featured' => 'featured',
													'content_language' => 'language', 'content_owner' => 'owner'
												);

			for ( $i = 0, $n = $contentCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$content				=	array();

				foreach ( $paramsMap as $old => $new ) {
					$content[$new]		=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['content'][]	=	$content;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function email( &$trigger, &$params )
	{
		$emailCount						=	substr_count( $params->asJson(), 'email_to' );

		if ( $emailCount ) {
			$newParams					=	array();
			$newParams['email']			=	array();

			$paramsMap					=	array(	'email_to' => 'to', 'email_subject' => 'subject', 'email_body' => 'body',
													'email_mode' => 'mode', 'email_cc' => 'cc', 'email_bcc' => 'bcc',
													'email_attachment' => 'attachment', 'email_replyto_address' => 'replyto_address', 'email_replyto_name' => 'replyto_name',
													'email_address' => 'from_address', 'email_name' => 'from_name', 'email_mailer' => 'mailer',
													'email_mailer_sendmail' => 'mailer_sendmail', 'email_mailer_smtpauth' => 'mailer_smtpauth', 'email_mailer_smtpsecure' => 'mailer_smtpsecure',
													'email_mailer_smtpport' => 'mailer_smtpport', 'email_mailer_smtpuser' => 'mailer_smtpuser', 'email_mailer_smtppass' => 'mailer_smtppass',
													'email_mailer_smtphost' => 'mailer_smtphost'
												);

			for ( $i = 0, $n = $emailCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$email					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$email[$new]		=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['email'][]	=	$email;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function field( &$trigger, &$params )
	{
		$fieldsCount					=	substr_count( $params->asJson(), 'field_id' );

		if ( $fieldsCount ) {
			$newParams					=	array();
			$newParams['field']			=	array();

			$paramsMap					=	array(	'field_id' => 'field', 'field_operator' => 'operator',
													'field_value' => 'value', 'field_translate' => 'translate'
												);

			for ( $i = 0, $n = $fieldsCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$field					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$field[$new]		=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['field'][]	=	$field;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function groupjive20( &$trigger, &$params )
	{
		if ( $params->has( 'gj20_auto' ) ) {
			$trigger->set( 'type', 'groupjive' );

			$newParams									=	array();
			$newParams['groupjive']						=	array();

			$paramsMap									=	array(	'gj20_auto' => 'mode', 'gj20_groups' => 'groups', 'gj20_grp_parent' => 'group_parent',
																	'gj20_category' => 'category', 'gj20_cat_parent' => array( 'parent', 'category_parent' ),
																	'gj20_status' => array( 'status', 'group_status' ), 'gj20_cat_unique' => 'category_unique',
																	'gj20_types' => array( 'types', 'category_types' ), 'gj20_grp_autojoin' => 'autojoin',
																	'gj20_type' => 'type', 'gj20_cat_description' => '', 'gj20_cat_owner' => '',
																	'gj20_cat_name' => '', 'gj20_grp_name' => '', 'gj20_grp_description' => '',
																	'gj20_grp_unique' => '', 'gj20_grp_owner' => ''
																);

			switch ( (int) $params->get( 'gj20_auto', 1, GetterInterface::INT ) ) {
				case 3:
					$paramsMap['gj20_cat_name']			=	'name';
					$paramsMap['gj20_cat_description']	=	'description';
					$paramsMap['gj20_cat_unique']		=	'unique';
					$paramsMap['gj20_cat_owner']		=	'owner';
					break;
				case 2:
					$paramsMap['gj20_cat_name']			=	'category_name';
					$paramsMap['gj20_cat_description']	=	'category_description';
					$paramsMap['gj20_grp_name']			=	'name';
					$paramsMap['gj20_grp_description']	=	'description';
					$paramsMap['gj20_grp_unique']		=	'unique';
					$paramsMap['gj20_grp_owner']		=	'owner';
					break;
			}

			$groupJive									=	array();

			foreach ( $paramsMap as $old => $new ) {
				if ( $new ) {
					if ( is_array( $new ) ) {
						foreach ( $new as $n ) {
							$groupJive[$n]				=	$params->get( $old, null, GetterInterface::RAW );
						}
					} else {
						$groupJive[$new]				=	$params->get( $old, null, GetterInterface::RAW );
					}
				}
			}

			$newParams['groupjive'][]					=	$groupJive;

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function k2( &$trigger, &$params )
	{
		if ( $params->has( 'k2_mode' ) ) {
			$newParams					=	array();
			$newParams['k2']			=	array();

			$paramsMap					=	array(	'k2_mode' => 'mode', 'k2_user_group' => 'group', 'k2_gender' => 'gender',
													'k2_description' => 'description', 'k2_url' => 'url', 'k2_notes' => 'notes'
												);

			foreach ( $paramsMap as $old => $new ) {
				$newParams['k2'][$new]	=	$params->get( $old, null, GetterInterface::RAW );
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function kunena17( &$trigger, &$params )
	{
		$kunenaCount						=	substr_count( $params->asJson(), 'kunena17_name' );

		if ( $kunenaCount ) {
			$trigger->set( 'type', 'kunena' );

			$newParams						=	array();
			$newParams['kunena']			=	array();

			$paramsMap						=	array( 'kunena17_name' => 'name', 'kunena17_parent' => 'parent', 'kunena17_description' => 'description' );

			for ( $i = 0, $n = $kunenaCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$kunena						=	array( 'mode' => 'category' );

				foreach ( $paramsMap as $old => $new ) {
					$kunena[$new]			=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['kunena'][]		=	$kunena;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function kunena20( &$trigger, &$params )
	{
		$kunenaCount						=	substr_count( $params->asJson(), 'kunena20_name' );

		if ( $kunenaCount ) {
			$trigger->set( 'type', 'kunena' );

			$newParams						=	array();
			$newParams['kunena']			=	array();

			$paramsMap						=	array( 'kunena20_name' => 'name', 'kunena20_parent' => 'parent', 'kunena20_description' => 'description' );

			for ( $i = 0, $n = $kunenaCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$kunena						=	array( 'mode' => 'category' );

				foreach ( $paramsMap as $old => $new ) {
					$kunena[$new]			=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['kunena'][]		=	$kunena;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function loginlogout( &$trigger, &$params )
	{
		if ( $params->has( 'loginlogout_mode' ) ) {
			$newParams							=	array();
			$newParams['loginlogout']			=	array();

			$paramsMap							=	array(	'loginlogout_mode' => 'mode', 'loginlogout_method' => 'method', 'loginlogout_username' => 'username',
															'loginlogout_email' => 'email', 'loginlogout_redirect' => 'redirect', 'loginlogout_message' => 'message'
														);

			foreach ( $paramsMap as $old => $new ) {
				$newParams['loginlogout'][$new]	=	$params->get( $old, null, GetterInterface::RAW );
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function menu( &$trigger, &$params )
	{
		$menuCount						=	substr_count( $params->asJson(), 'menu_title' );

		if ( $menuCount ) {
			$newParams					=	array();
			$newParams['menu']			=	array();

			$paramsMap					=	array(	'menu_title' => 'title', 'menu_type' => 'type', 'menu_class' => 'class',
													'menu_position' => 'position', 'menu_url' => 'url', 'menu_target' => 'target',
													'menu_tooltip' => 'tooltip', 'menu_img' => 'image'
												);

			for ( $i = 0, $n = $menuCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$menu					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$menu[$new]			=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['menu'][]	=	$menu;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function pms( &$trigger, &$params )
	{
		$pmsCount						=	substr_count( $params->asJson(), 'pms_from' );

		if ( $pmsCount ) {
			$newParams					=	array();
			$newParams['pms']			=	array();

			$paramsMap					=	array(	'pms_from' => 'from', 'pms_to' => 'to',
													'pms_subject' => 'subject', 'pms_message' => 'body'
												);

			for ( $i = 0, $n = $pmsCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$pms					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$pms[$new]			=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['pms'][]		=	$pms;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function privacy( &$trigger, &$params )
	{
		// Legacy Usage:
		$privacyCount							=	substr_count( $params->asJson(), 'privacy_user' );

		if ( $privacyCount ) {
			$newParams							=	array();
			$newParams['privacy']				=	array();

			$paramsMap							=	array(	'privacy_user' => 'owner', 'privacy_type' => 'type', 'privacy_subtype' => 'subtype',
															'privacy_item' => 'item', 'privacy_rule' => 'rule'
														);

			for ( $i = 0, $n = $privacyCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i							=	null;
				}

				$privacy						=	array();

				foreach ( $paramsMap as $old => $new ) {
					$privacy[$new]				=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['privacy'][]			=	$privacy;
			}

			$params->load( $newParams );
		}

		// CB Privacy 5.0.0+ Usage:
		$newParams								=	array();

		foreach ( $params->subTree( 'privacy' ) as $i => $privacyRow ) {
			$migrate							=	false;

			/** @var ParamsInterface $privacyRow */
			if ( ! $privacyRow instanceof ParamsInterface ) {
				$privacyRow						=	new Registry( $privacyRow );
			}

			$method								=	$privacyRow->get( 'method', null, GetterInterface::STRING );

			if ( ! $method ) {
				$privacyRow->set( 'method', 'create' );

				$migrate						=	true;
			}

			$type								=	$privacyRow->get( 'type', null, GetterInterface::STRING );
			$subtype							=	$privacyRow->get( 'subtype', null, GetterInterface::STRING );
			$item								=	$privacyRow->get( 'item', null, GetterInterface::STRING );

			if ( $type ) {
				$asset							=	$type;

				if ( $subtype ) {
					$asset						.=	'.' . $subtype;
				}

				if ( $item ) {
					$asset						.=	'.' . $item;
				}

				$privacyRow->set( 'asset', $asset );

				$privacyRow->unsetEntry( 'type' );
				$privacyRow->unsetEntry( 'subtype' );
				$privacyRow->unsetEntry( 'item' );

				$migrate						=	true;
			}

			$rule								=	$privacyRow->get( 'rule', null, GetterInterface::STRING );

			if ( $rule ) {
				$privacyRow->set( 'rules', $rule );

				$privacyRow->unsetEntry( 'rule' );

				$migrate						=	true;
			}

			if ( $migrate ) {
				$newParams['privacy'][$i]		=	$privacyRow->asArray();
			}
		}

		if ( $newParams ) {
			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function query( &$trigger, &$params )
	{
		if ( $params->has( 'query_sql' ) ) {
			$newParams					=	array();
			$newParams['query']			=	array();

			$paramsMap					=	array(	'query_sql' => 'sql', 'query_mode' => 'mode', 'query_host' => 'host',
													'query_username' => 'username', 'query_password' => 'password', 'query_database' => 'database',
													'query_charset' => 'charset', 'query_prefix' => 'prefix'
												);

			$query						=	array();

			foreach ( $paramsMap as $old => $new ) {
				$query[$new]			=	$params->get( $old, null, GetterInterface::RAW );
			}

			$newParams['query'][]		=	$query;

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function redirect( &$trigger, &$params )
	{
		if ( $params->has( 'redirect_url' ) ) {
			$newParams							=	array();
			$newParams['redirect']				=	array();

			$paramsMap							=	array( 'redirect_url' => 'url', 'redirect_message' => 'message', 'redirect_type' => 'type' );

			foreach ( $paramsMap as $old => $new ) {
				$newParams['redirect'][$new]	=	$params->get( $old, null, GetterInterface::RAW );
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function registration( &$trigger, &$params )
	{
		if ( $params->has( 'registration_username' ) ) {
			$newParams									=	array();
			$newParams['registration']					=	array();

			$paramsMap									=	array(	'registration_approve' => 'approve', 'registration_confirm' => 'confirm', 'registration_usergroup' => 'usergroup',
																	'registration_username' => 'username', 'registration_password' => 'password', 'registration_email' => 'email',
																	'registration_firstname' => 'firstname', 'registration_middlename' => 'middlename', 'registration_lastname' => 'lastname',
																	'registration_supress' => 'supress', 'registration_fields' => ''
																);

			$fields										=	$params->get( 'registration_fields', null, GetterInterface::RAW );
			$newFields									=	array();

			if ( $fields ) {
				$fields									=	explode( "\n", $fields );

				foreach ( $fields as $pair ) {
					$field								=	explode( '=', trim( $pair ), 2 );

					if ( count( $field ) == 2 ) {
						$newFields[]					=	array( 'field' => trim( $field[0] ), 'value' => trim( $field[1] ), 'translate' => '1' );
					}
				}
			}

			$newParams['registration']['fields']		=	$newFields;

			foreach ( $paramsMap as $old => $new ) {
				if ( $new ) {
					$newParams['registration'][$new]	=	$params->get( $old, null, GetterInterface::RAW );
				}
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function request( &$trigger, &$params )
	{
		// Legacy Usage:
		$requestCount						=	substr_count( $params->asJson(), 'request_url' );

		if ( $requestCount ) {
			$newParams						=	array();
			$newParams['request']			=	array();

			$paramsMap						=	array(	'request_url' => 'url', 'request_method' => 'method', 'request_request' => '',
														'request_return' => '', 'request_error' => '', 'request_debug' => ''
													);

			for ( $i = 0, $n = $requestCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$request					=	array();

				$data						=	$params->get( "request_request$i", null, GetterInterface::RAW );
				$newData					=	array();

				if ( $data ) {
					$data					=	explode( "\n", $data );

					foreach ( $data as $pair ) {
						$dataPair			=	explode( '=', trim( $pair ), 2 );

						if ( count( $dataPair ) == 2 ) {
							$newData[]		=	array( 'key' => trim( $dataPair[0] ), 'value' => trim( $dataPair[1] ), 'translate' => '1' );
						}
					}
				}

				$request['request']			=	$newData;

				foreach ( $paramsMap as $old => $new ) {
					if ( $new ) {
						$request[$new]		=	$params->get( $old . $i, null, GetterInterface::RAW );
					}
				}

				$newParams['request'][]		=	$request;
			}

			$params->load( $newParams );
		}

		// 7.0.0 Usage:
		$newParams							=	array();

		foreach ( $params->subTree( 'request' ) as $i => $requestRow ) {
			/** @var ParamsInterface $requestRow */
			if ( ! $requestRow instanceof ParamsInterface ) {
				$requestRow					=	new Registry( $requestRow );
			}

			$output							=	$requestRow->get( 'return', null, GetterInterface::STRING );

			if ( $output ) {
				if ( $output == 'SILENT' ) {
					$output					=	'none';
				}

				$newParams['display']		=	strtolower( $output );

				$requestRow->unsetEntry( 'output' );

				$newParams['request'][$i]	=	$requestRow->asArray();
			}
		}

		if ( $newParams ) {
			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function usergroup( &$trigger, &$params )
	{
		$usergroupCount						=	substr_count( $params->asJson(), 'usergroup_mode' );

		if ( $usergroupCount ) {
			$newParams						=	array();
			$newParams['usergroup']			=	array();

			$paramsMap						=	array(	'usergroup_mode' => 'mode', 'usergroup_parent' => 'parent', 'usergroup_title' => 'title',
														'usergroup_add' => 'add', 'usergroup_groups' => 'groups'
													);

			for ( $i = 0, $n = $usergroupCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$usergroup					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$usergroup[$new]		=	$params->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['usergroup'][]	=	$usergroup;
			}

			$params->load( $newParams );
		}
	}
}