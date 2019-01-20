<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\ProfileBook\Trigger;

use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\ProfileBook\CBProfileBook;
use CB\Plugin\ProfileBook\Table\EntryTable;
use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class UserTrigger extends \cbPluginHandler
{

	/**
	 * Deletes items when the user is deleted
	 *
	 * @param  UserTable $user
	 * @param  int       $status
	 */
	public function deleteEntries( $user, $status )
	{
		global $_CB_database;

		if ( CBProfileBook::getGlobalParams()->get( 'general_delete', true, GetterInterface::BOOLEAN ) ) {
			// Delete all entries to and from this user:
			$query		=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
						.	"\n WHERE ( " . $_CB_database->NameQuote( 'posterid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
						.	" OR " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT ) . " )";
			$_CB_database->setQuery( $query );
			$entries	=	$_CB_database->loadObjectList( null, '\CB\Plugin\ProfileBook\Table\EntryTable', array( $_CB_database ) );

			/** @var EntryTable[] $entries */
			foreach ( $entries as $entry ) {
				$entry->delete();
			}
		}
	}

	/**
	 * Outputs the new entries notification
	 *
	 * @param int      $nameLenght
	 * @param int      $passLenght
	 * @param int      $horizontal
	 * @param string   $classSfx
	 * @param Registry $params
	 * @return array|null|string
	 */
	public function getEntriesNotification( $nameLenght, $passLenght, $horizontal, $classSfx, $params )
	{
		global $_CB_framework, $_CB_database;

		$user					=	\CBuser::getMyUserDataInstance();
		$guestbook				=	CBProfileBook::getTab( 'getprofilebookTab', $user->get( 'id', 0, GetterInterface::INT ) );
		$return					=	null;

		if ( $guestbook && ( $user->get( 'cb_pb_enable', null, GetterInterface::STRING ) != '_UE_NO' ) ) {
			$query				=	"SELECT COUNT(*)"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'mode' ) . " = " . $_CB_database->Quote( 'g' )
								.	"\n AND " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
								.	"\n AND " . $_CB_database->NameQuote( 'status' ) . " = 0";
			$_CB_database->setQuery( $query );
			$unread				=	$_CB_database->loadResult();

			if ( $unread ) {
				$return			.=	'<div class="pbUnreadSignatures"><a href="' . $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), true, $guestbook->get( 'tabid', 0, GetterInterface::INT ) ) . '">' . CBTxt::T( 'YOU_HAVE_UNREAD_SIGNATURES', 'You have %%COUNT%% unread signature|You have %%COUNT%% unread signatures', array( '%%COUNT%%' => $unread ) ) . '</a></div>';
			}
		}

		$wall					=	CBProfileBook::getTab( 'getprofilebookwallTab', $user->get( 'id', 0, GetterInterface::INT ) );

		if ( $wall && ( $user->get( 'cb_pb_enable_wall', null, GetterInterface::STRING ) != '_UE_NO' ) ) {
			$query				=	"SELECT COUNT(*)"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'mode' ) . " = " . $_CB_database->Quote( 'w' )
								.	"\n AND " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
								.	"\n AND " . $_CB_database->NameQuote( 'status' ) . " = 0";
			$_CB_database->setQuery( $query );
			$unread				=	$_CB_database->loadResult();

			if ( $unread ) {
				$return			.=	'<div class="pbUnreadPosts"><a href="' . $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), true, $wall->get( 'tabid', 0, GetterInterface::INT ) ) . '">' . CBTxt::T( 'YOU_HAVE_UNREAD_POSTS', 'You have %%COUNT%% unread post|You have %%COUNT%% unread posts', array( '%%COUNT%%' => $unread ) ) . '</a></div>';
			}
		}

		if ( ! $return ) {
			return null;
		}

		return array( 'afterForm' => $return );
	}
}