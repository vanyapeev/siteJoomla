<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\ProfileUpdateLogger\Trigger;

use CB\Database\Table\FieldTable;
use CB\Plugin\ProfileUpdateLogger\CBProfileUpdateLogger;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\ProfileUpdateLogger\Table\UpdateLogTable;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class UserTrigger extends \cbPluginHandler
{

	/**
	 * Deletes update log when the user is deleted
	 *
	 * @param  UserTable $user
	 * @param  int       $status
	 */
	public function deleteLog( $user, $status )
	{
		global $_CB_database;

		if ( CBProfileUpdateLogger::getGlobalParams()->get( 'general_delete', true, GetterInterface::BOOLEAN ) ) {
			// Delete all profile update logs for this user:
			$query		=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_pulogger' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'profileid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
			$_CB_database->setQuery( $query );
			$updates	=	$_CB_database->loadObjectList( null, '\CB\Plugin\ProfileUpdateLogger\Table\UpdateLogTable', array( $_CB_database ) );

			/** @var UpdateLogTable[] $updates */
			foreach ( $updates as $update ) {
				$update->delete();
			}
		}
	}

	/**
	 * Deletes update log when the user is deleted
	 *
	 * @param  UserTable $new
	 * @param  UserTable $user
	 * @param  UserTable $old
	 */
	public function logUpdates( $new, $user, $old )
	{
		global $_CB_framework, $_CB_database;

		static $fields				=	array();

		$params						=	CBProfileUpdateLogger::getGlobalParams();

		if ( ( ! $new->get( 'id', 0, GetterInterface::INT ) )
			 || ( ! $old->get( 'id', 0, GetterInterface::INT ) )
			 || ( in_array( $new->get( 'id', 0, GetterInterface::INT ), cbToArrayOfInt( explode( ',', $params->get( 'logging_exclude_users', null, GetterInterface::STRING ) ) ) ) )
			 || ( Application::Cms()->getClientId() && ( ! $params->get( 'pulBackEndLogging', true, GetterInterface::BOOLEAN ) ) )
		) {
			return;
		}

		$ignoreFields				=	array_merge( explode( '|*|', $params->get( 'logging_exclude_fields', null, GetterInterface::STRING ) ), array( 'id', 'password', 'params' ) );
		$ignoreTypes				=	array_merge( explode( '|*|', $params->get( 'logging_exclude_types', null, GetterInterface::STRING ) ), array( 'password' ) );

		$fieldNames					=	array_unique( array_merge( array_keys( get_object_vars( $new ) ), array_keys( get_object_vars( $old ) ) ) );

		$changed					=	array();
		$pending					=	0;

		foreach ( $fieldNames as $k => $fieldName ) {
			if ( ( substr( $fieldName, 0, 1 ) == '_' ) || in_array( $fieldName, $ignoreFields ) ) {
				unset( $fieldNames[$k] );
				continue;
			}

			$newValue				=	$new->get( $fieldName, null, GetterInterface::RAW );
			$oldValue				=	$old->get( $fieldName, null, GetterInterface::RAW );

			// Lets see if there was a change of any kind:
			if ( $newValue == $oldValue ) {
				continue;
			}

			// Lets make sure what we're checking against is even a field:
			if ( ! isset( $fields[$fieldName] ) ) {
				$field				=	new FieldTable();

				$field->load( array( 'name' => $fieldName ) );

				$fields[$fieldName]	=	$field;
			}

			$field					=	$fields[$fieldName];

			if ( ( ! $field->get( 'fieldid', 0, GetterInterface::INT ) ) || in_array( $field->get( 'type', null, GetterInterface::STRING ), $ignoreTypes ) ) {
				continue;
			}

			// Now lets do fieldtype specific matching:
			switch ( $field->get( 'type', null, GetterInterface::STRING ) ) {
				case 'checkbox':
				case 'integer':
				case 'terms':
				case 'points':
				case 'counter':
					$newValue		=	$new->get( $fieldName, 0, GetterInterface::INT );
					$oldValue		=	$old->get( $fieldName, 0, GetterInterface::INT );
					break;
				case 'rating':
					$newValue		=	$new->get( $fieldName, (float) 0, GetterInterface::FLOAT );
					$oldValue		=	$old->get( $fieldName, (float) 0, GetterInterface::FLOAT );
					break;
				case 'date':
					$newValue		=	$new->get( $fieldName, '0000-00-00', GetterInterface::STRING );

					if ( ! $newValue ) {
						$newValue	=	'0000-00-00';
					}

					$oldValue		=	$old->get( $fieldName, '0000-00-00', GetterInterface::STRING );

					if ( ! $oldValue ) {
						$oldValue	=	'0000-00-00';
					}
					break;
				case 'datetime':
					$newValue		=	$new->get( $fieldName, '0000-00-00 00:00:00', GetterInterface::STRING );

					if ( ! $newValue ) {
						$newValue	=	'0000-00-00 00:00:00';
					}

					$oldValue		=	$old->get( $fieldName, '0000-00-00 00:00:00', GetterInterface::STRING );

					if ( ! $oldValue ) {
						$oldValue	=	'0000-00-00 00:00:00';
					}
					break;
			}

			if ( $newValue != $oldValue ) {
				$changed[]			=	array(	'[field]'	=>	$fieldName,
												'[old]'		=>	( ( $oldValue === null ) || ( $oldValue === '' ) ? CBTxt::T( '(empty)' ) : htmlspecialchars( $oldValue ) ),
												'[new]'		=>	( ( $newValue === null ) || ( $newValue === '' ) ? CBTxt::T( '(empty)' ) : htmlspecialchars( $newValue ) )
											);

				if ( ! $new->get( $fieldName . 'approved', 1, GetterInterface::INT ) ) {
					$pending++;
				}

				$log				=	new UpdateLogTable();

				$log->set( 'profileid', $new->get( 'id', 0, GetterInterface::INT ) );
				$log->set( 'fieldname', $fieldName );
				$log->set( 'oldvalue', $oldValue );
				$log->set( 'newvalue', $newValue );

				$log->store();
			}
		}

		if ( $changed && $params->get( 'pulEnableNotifications', true, GetterInterface::BOOLEAN ) && ( ( ! Application::Cms()->getClientId() ) || ( Application::Cms()->getClientId() && $params->get( 'pulBackendNotifications', false, GetterInterface::BOOLEAN ) ) ) ) {
			$changes				=	null;

			foreach ( $changed as $change ) {
				$changes			.=	CBTxt::T( 'FIELD_CHANGED_OLD_TO_NEW', '<p><strong>[field]:</strong> "[old]" to "[new]"</p>', $change );
			}

			$myCbUser				=	\CBuser::getMyInstance();
			$myUser					=	$myCbUser->getUserData();

			if ( $myUser->get( 'id', 0, GetterInterface::INT ) == $new->get( 'id', 0, GetterInterface::INT ) ) {
				$subject			=	CBTxt::T( '[username] has updated their profile!' );
				$body				=	CBTxt::T( '<a href="[url]">[username]</a> has updated their profile. Changed: [changed]. Pending Changes: [pending].<br /><br />[changes]' );
			} else {
				$subject			=	CBTxt::T( 'USER_HAS_UPDATED_PROFILE_OF_USERNAME', '[user] has updated the profile of [username]!', array( '[user]' => $myUser->get( 'username', null, GetterInterface::STRING ) ) );
				$body				=	CBTxt::T( '[user] has updated the profile of <a href="[url]">[username]</a>. Changed: [changed]. Pending Changes: [pending].<br /><br />[changes]' );
			}

			$cbUser					=	\CBuser::getInstance( $new->get( 'id', 0, GetterInterface::INT ), false );

			$extras					=	array(	'user'		=>	$myCbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ),
												'url'		=>	$_CB_framework->viewUrl( 'userprofile', true, array( 'user' => $new->get( 'id', 0, GetterInterface::INT ), 'tab' => 'getcbpuloggerTab' ) ),
												'changes'	=>	$changes,
												'changed'	=>	count( $changed ),
												'pending'	=>	$pending,
												'date'		=>	cbFormatDate( Application::Database()->getUtcDateTime() )
											);

			$cbNotification			=	new \cbNotification( );

			switch ( $params->get( 'pulNotificationList', 0, GetterInterface::INT ) ) {
				case 2: // Specific Users
					$recipients		=	$params->get( 'pulNotificationRecipientList', null, GetterInterface::STRING );

					if ( ! $recipients ) {
						return;
					}

					$recipients		=	cbToArrayOfInt( explode( ',', $recipients ) );

					foreach ( $recipients as $recipient ) {
						$cbNotification->sendFromSystem( $recipient, $cbUser->replaceUserVars( $subject, false, false, $extras, false ), $cbUser->replaceUserVars( $body, false, false, $extras, false ), false, true );
					}
					break;
				case 1: // View Access Level
					$usergroups		=	Application::CmsPermissions()->getGroupsOfViewAccessLevel( $params->get( 'pulNotificationAclList', 6, GetterInterface::INT ), true );

					if ( ! $usergroups ) {
						return;
					}

					$query			=	'SELECT DISTINCT u.' . $_CB_database->NameQuote( 'id' )
									.	"\n FROM " . $_CB_database->NameQuote( '#__users' ) . " AS u"
									.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
									.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = u.' . $_CB_database->NameQuote( 'id' )
									.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__user_usergroup_map' ) . " AS g"
									.	' ON g.' . $_CB_database->NameQuote( 'user_id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
									.	"\n WHERE g." . $_CB_database->NameQuote( 'group_id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $usergroups )
									.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
									.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
									.	"\n AND c." . $_CB_database->NameQuote( 'approved' ) . " = 1";
					$_CB_database->setQuery( $query );
					$recipients		=	$_CB_database->loadResultArray();

					if ( ! $recipients ) {
						return;
					}

					foreach ( $recipients as $recipient ) {
						$cbNotification->sendFromSystem( $recipient, $cbUser->replaceUserVars( $subject, false, false, $extras, false ), $cbUser->replaceUserVars( $body, false, false, $extras, false ), false, true );
					}
					break;
				case 0: // Moderators
				default:
					$cbNotification->sendToModerators( $cbUser->replaceUserVars( $subject, false, false, $extras, false ), $cbUser->replaceUserVars( $body, false, false, $extras, false ), false, true );
					break;
			}
		}
	}
}