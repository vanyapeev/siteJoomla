<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Trigger;

use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Table\HiddenTable;
use CB\Plugin\Activity\CBActivity;

defined('CBLIB') or die();

class UserTrigger extends \cbPluginHandler
{

	/**
	 * Deletes items when the user is deleted
	 *
	 * @param  UserTable $user
	 * @param  int       $status
	 */
	public function deleteActivity( $user, $status )
	{
		global $_CB_database;

		$params				=	CBActivity::getGlobalParams();

		if ( $params->get( 'general_delete', true, GetterInterface::BOOLEAN ) ) {
			$query			=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
			$_CB_database->setQuery( $query );
			$activities		=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\ActivityTable', array( $_CB_database ) );

			/** @var ActivityTable[] $activities */
			foreach ( $activities as $activity ) {
				$activity->delete();
			}

			$query			=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_hidden' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
			$_CB_database->setQuery( $query );
			$hidden			=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\HiddenTable', array( $_CB_database ) );

			/** @var HiddenTable[] $hidden */
			foreach ( $hidden as $hide ) {
				$hide->delete();
			}

			$query			=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
			$_CB_database->setQuery( $query );
			$comments		=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\CommentTable', array( $_CB_database ) );

			/** @var CommentTable[] $comments */
			foreach ( $comments as $comment ) {
				$comment->delete();
			}

			$query			=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_tags' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
			$_CB_database->setQuery( $query );
			$tags			=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\TagTable', array( $_CB_database ) );

			/** @var TagTable[] $tags */
			foreach ( $tags as $tag ) {
				$tag->delete();
			}
		}
	}
}