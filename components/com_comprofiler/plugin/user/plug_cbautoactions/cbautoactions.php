<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

/**
* System Action Language Strings
*
* GENERIC
* CBTxt::T( 'Auto Login' )
* CBTxt::T( 'Automatically logs in a user after registration or confirmation (must be approved and confirmed).' )
*
* PROFILE
* CBTxt::T( 'Profile - Logged In' )
* CBTxt::T( 'Logs activity when a user logs in.' )
* CBTxt::T( 'logged in' )
* CBTxt::T( 'Profile - Logged Out' )
* CBTxt::T( 'Logs activity when a user logs out.' )
* CBTxt::T( 'logged out' )
* CBTxt::T( 'Profile - Register' )
* CBTxt::T( 'Logs activity when a new user registers.' )
* CBTxt::T( 'joined the site' )
* CBTxt::T( 'Profile - Update' )
* CBTxt::T( 'Logs activity for profile updates.' )
* CBTxt::T( 'updated their profile' )
* CBTxt::T( 'Profile - Avatar' )
* CBTxt::T( 'Logs activity for avatar updates.' )
* CBTxt::T( 'updated their profile picture' )
* CBTxt::T( 'Profile - Canvas' )
* CBTxt::T( 'Logs activity for canvas updates.' )
* CBTxt::T( 'updated their canvas photo' )
* CBTxt::T( 'Profile - Add Connection' )
* CBTxt::T( 'Logs activity when a new connection is added.' )
* CBTxt::T( 'is now connected with @[var2]' )
* CBTxt::T( 'Profile - Add Cross Connection' )
* CBTxt::T( 'Logs activity when a new cross (both directions) connection is added.' )
* CBTxt::T( 'Profile - Accept Connection' )
* CBTxt::T( 'Logs activity when a new connection is accepted.' )
* CBTxt::T( 'Profile - Accept Cross Connection' )
* CBTxt::T( 'Logs activity when a new cross (both directions) connection is accepted.' )
* CBTxt::T( 'is now connected with @[var1]' )
* CBTxt::T( 'Profile - Remove Connection' )
* CBTxt::T( 'Deletes activity for removed connections.' )
*
* CB ACTIVITY
* CBTxt::T( 'CB Activity - Comment' )
* CBTxt::T( 'Logs activity for activity comments.' )
* CBTxt::T( 'commented on this' )
* CBTxt::T( 'CB Activity - Tag' )
* CBTxt::T( 'Logs activity for activity tags.' )
* CBTxt::T( 'was tagged in this' )
*
* CB GALLERY
* CBTxt::T( 'CB Gallery - Upload' )
* CBTxt::T( 'Logs activity for uploaded items.' )
* CBTxt::T( 'uploaded a [cb:if var1_type="photos"]photo[/cb:if][cb:if var1_type="videos"]video[/cb:if][cb:if var1_type="files"]file[/cb:if][cb:if var1_type="music"]music[/cb:if]' )
* CBTxt::T( 'CB Gallery - Linked' )
* CBTxt::T( 'Logs activity for linked items.' )
* CBTxt::T( 'linked a [cb:if var1_type="photos"]photo[/cb:if][cb:if var1_type="videos"]video[/cb:if][cb:if var1_type="files"]file[/cb:if][cb:if var1_type="music"]music[/cb:if]' )
* CBTxt::T( 'CB Gallery - Unpublished' )
* CBTxt::T( 'Deletes activity for unpublished items.' )
* CBTxt::T( 'CB Gallery - Deleted' )
* CBTxt::T( 'Deletes activity for deleted items.' )
*
* CB BLOGS
* CBTxt::T( 'CB Blogs - Create' )
* CBTxt::T( 'Logs activity for newly created blog entries.' )
* CBTxt::T( 'published a new blog entry' )
* CBTxt::T( 'Read More...' )
* CBTxt::T( 'CB Blogs - Unpublished' )
* CBTxt::T( 'Deletes activity for unpublished blog entries.' )
* CBTxt::T( 'CB Blogs - Deleted' )
* CBTxt::T( 'Deletes activity for deleted blog entries.' )
*
* KUNENA
* CBTxt::T( 'Kunena - Create' )
* CBTxt::T( 'Logs activity for newly created discussions.' )
* CBTxt::T( 'started a new discussion' )
* CBTxt::T( 'Discuss...' )
* CBTxt::T( 'Kunena - Reply' )
* CBTxt::T( 'Logs activity for discussion replies.' )
* CBTxt::T( 'Kunena - Delete' )
* CBTxt::T( 'Deletes activity for deleted discussions.' )
*
* CB GROUPJIVE
* CBTxt::T( 'CB GroupJive - Group Create' )
* CBTxt::T( 'Logs activity for newly created groups.' )
* CBTxt::T( 'CB GroupJive - Group Deleted' )
* CBTxt::T( 'Deletes activity for deleted groups.' )
* CBTxt::T( 'CB GroupJive - Group Joined' )
* CBTxt::T( 'Logs activity for when joining a group.' )
* CBTxt::T( 'CB GroupJive - Group Leave' )
* CBTxt::T( 'Deletes group join activity on group leave.' )
* CBTxt::T( 'CB GroupJive - Wall Create' )
* CBTxt::T( 'Logs activity for newly created group wall posts.' )
* CBTxt::T( 'CB GroupJive - Wall Delete' )
* CBTxt::T( 'Deletes activity for deleted group wall posts.' )
* CBTxt::T( 'CB GroupJive - Photo Upload' )
* CBTxt::T( 'Logs activity for newly uploaded group photos.' )
* CBTxt::T( 'CB GroupJive - Photo Deleted' )
* CBTxt::T( 'Deletes activity for deleted group photos.' )
* CBTxt::T( 'CB GroupJive - File Upload' )
* CBTxt::T( 'Logs activity for newly uploaded group files.' )
* CBTxt::T( 'CB GroupJive - File Deleted' )
* CBTxt::T( 'Deletes activity for deleted group files.' )
* CBTxt::T( 'CB GroupJive - Video Upload' )
* CBTxt::T( 'Logs activity for newly uploaded group videos.' )
* CBTxt::T( 'CB GroupJive - Video Deleted' )
* CBTxt::T( 'Deletes activity for deleted group videos.' )
* CBTxt::T( 'CB GroupJive - Event Schedule' )
* CBTxt::T( 'Logs activity for newly scheduled events.' )
* CBTxt::T( 'CB GroupJive - Event Deleted' )
* CBTxt::T( 'Deletes activity for deleted group events.' )
*/

use CBLib\Registry\GetterInterface;
use CB\Plugin\AutoActions\Table\AutoActionTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_database, $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'mod_onCBAdminMenu', 'adminMenu', '\CB\Plugin\AutoActions\Trigger\AdminTrigger' );

$plugin									=	$_PLUGINS->getLoadedPlugin( 'user', 'cbautoactions' );

if ( $plugin ) {
	$createFunctionAllowed				=	( version_compare( phpversion(), '7.2.0', '<' ) && function_exists( 'create_function' ) );

	$query								=	'SELECT *'
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'trigger' ) . " != ''"
										.	"\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1"
										.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' ) . " ASC";
	$_CB_database->setQuery( $query );
	$rows								=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AutoActions\Table\AutoActionTable', array( $_CB_database ) );

	/** @var $rows AutoActionTable[] */
	foreach ( $rows as $row ) {
		$triggers						=	explode( '|*|', $row->get( 'trigger', null, GetterInterface::STRING ) );

		foreach ( $triggers as $trigger ) {
			$trigger					=	trim( htmlspecialchars( $trigger ) );

			if ( $trigger && ( ! in_array( $trigger, array( 'internalGeneral', 'internalUsers' ) ) ) ) {
				$references				=	cbToArrayOfInt( explode( '|*|', $row->params()->get( 'references', null, GetterInterface::STRING ) ) );

				if ( $references && $createFunctionAllowed ) {
					// Prepare a list of variables to send to the anonymous function:
					$vars				=	array(	1	=>	'$var1 = null',
													2	=>	'$var2 = null',
													3	=>	'$var3 = null',
													4	=>	'$var4 = null',
													5	=>	'$var5 = null',
													6	=>	'$var6 = null',
													7	=>	'$var7 = null',
													8	=>	'$var8 = null',
													9	=>	'$var9 = null',
													10	=>	'$var10 = null',
													11	=>	'$var11 = null',
													12	=>	'$var12 = null',
													13	=>	'$var13 = null',
													14	=>	'$var14 = null',
													15	=>	'$var15 = null'
												);

					// Change variables to references as needed:
					foreach ( $vars as $i => $var ) {
						if ( in_array( $i, $references ) ) {
							$vars[$i]	=	'&' . $var;
						}
					}

					$function			=	'global $_PLUGINS;'
										.	'$args	=	array( ' . (int) $row->id . ', \'' . $trigger . '\', &$var1, &$var2, &$var3, &$var4, &$var5, &$var6, &$var7, &$var8, &$var9, &$var10, &$var11, &$var12, &$var13, &$var14, &$var15 );'
										.	'return $_PLUGINS->call( ' . (int) $plugin->id . ', \'triggerAction\', \'\CB\Plugin\AutoActions\Trigger\ActionTrigger\', $args );';

					$function			=	create_function( implode( ', ', $vars ), $function );
				} else {
					$function			=	function( $var1 = null, $var2 = null, $var3 = null, $var4 = null, $var5 = null, $var6 = null, $var7 = null, $var8 = null, $var9 = null, $var10 = null, $var11 = null, $var12 = null, $var13 = null, $var14 = null, $var15 = null ) use ( $_PLUGINS, $plugin, $row, $trigger ) {
												$args	=	array( $row, $trigger, &$var1, &$var2, &$var3, &$var4, &$var5, &$var6, &$var7, &$var8, &$var9, &$var10, &$var11, &$var12, &$var13, &$var14, &$var15 );

												return $_PLUGINS->call( $plugin->id, 'triggerAction', '\CB\Plugin\AutoActions\Trigger\ActionTrigger', $args );
											};
				}

				if ( strpos( $trigger, 'joomla_' ) !== false ) {
					JFactory::getApplication()->registerEvent( str_replace( 'joomla_', '', $trigger ), $function );
				} else {
					$_PLUGINS->registerFunction( $trigger, $function );
				}
			}
		}
	}
}