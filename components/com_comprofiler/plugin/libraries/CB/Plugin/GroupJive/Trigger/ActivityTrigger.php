<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJive\Trigger;

use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CB\Plugin\GroupJive\Table\NotificationTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Comments;
use CB\Database\Table\UserTable;

defined('CBLIB') or die();

class ActivityTrigger extends \cbPluginHandler
{

	/**
	 * @return bool
	 */
	private function isCompatible()
	{
		global $_PLUGINS;

		static $compatible		=	null;

		if ( $compatible === null ) {
			$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' );

			if ( $plugin ) {
				$pluginVersion	=	str_replace( '+build.', '+', $_PLUGINS->getPluginVersion( $plugin, true ) );

				$compatible		=	( version_compare( $pluginVersion, '4.0.0', '>=' ) && version_compare( $pluginVersion, '5.0.0', '<' ) );
			}
		}

		return $compatible;
	}

	/**
	 * @param null|string|array      $assets
	 * @param null|int|UserTable     $user
	 * @param array                  $defaults
	 * @param Activity|Notifications $stream
	 */
	public function activityBuild( &$assets, &$user, &$defaults, &$stream )
	{
		if ( ( ! $this->isCompatible() ) || ( ! $assets ) ) {
			return;
		}

		$asset			=	( is_array( $assets ) ? $assets[0] : $assets );

		if ( strpos( $asset, 'groupjive.group' ) !== 0 ) {
			return;
		}

		if ( ( $this->input( 'plugin', null, GetterInterface::STRING ) == 'cbgroupjive' ) && ( $this->input( 'action', null, GetterInterface::STRING ) == 'groups' ) ) {
			$group		=	$this->input( 'group', 0, GetterInterface::INT );

			if ( ! $group ) {
				$group	=	$this->input( 'id', 0, GetterInterface::INT );
			}

			$stream->set( 'groupjive.ingroup', $group );
		}
	}

	/**
	 * @param bool              $access
	 * @param string            $type
	 * @param UserTable         $user
	 * @param Activity|Comments $stream
	 */
	public function createAccess( &$access, $type, $user, $stream )
	{
		if ( ( ! $this->isCompatible() ) || ( ! preg_match( '/^groupjive\.group\.(\d+)(?:\.([a-zA-Z]+))?/', $stream->asset(), $matches ) ) ) {
			return;
		}

		$group			=	CBGroupJive::getGroup( (int) $matches[1] );

		if ( ! $group->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		if ( $group->get( 'published', 0, GetterInterface::INT ) == -1 ) {
			$access		=	false;
		} else {
			$access		=	( ( $group->get( 'user_id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) ) || ( CBGroupJive::getGroupStatus( $user, $group ) >= 1 ) );
		}
	}

	/**
	 * @param bool              $access
	 * @param UserTable         $user
	 * @param Activity|Comments $stream
	 */
	public function moderateAccess( &$access, $user, $stream )
	{
		if ( ( ! $this->isCompatible() ) || ( ! preg_match( '/^groupjive\.group\.(\d+)(?:\.([a-zA-Z]+))?/', $stream->asset(), $matches ) ) ) {
			return;
		}

		$group			=	CBGroupJive::getGroup( (int) $matches[1] );

		if ( ! $group->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		if ( $group->get( 'published', 0, GetterInterface::INT ) == -1 ) {
			$access		=	false;
		} else {
			$access		=	( ( $group->get( 'user_id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) ) || ( CBGroupJive::getGroupStatus( $user, $group ) >= 2 ) );
		}
	}

	/**
	 * @param string                 $output
	 * @param array                  $select
	 * @param array                  $join
	 * @param array                  $where
	 * @param Activity|Notifications $stream
	 */
	public function activityQuery( $output, &$select, &$join, &$where, &$stream )
	{
		global $_CB_database;

		if ( ! $this->isCompatible() ) {
			return;
		}

		$group			=	"SELECT COUNT(*)"
						.	" FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS gj_g";

		if ( ! CBGroupJive::isModerator() ) {
			$user		=	\CBuser::getMyUserDataInstance();

			$group		.=	" LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_categories' ) . " AS gj_c"
						.	" ON gj_c." . $_CB_database->NameQuote( 'id' ) . " = gj_g." . $_CB_database->NameQuote( 'category' )
						.	" LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS gj_u"
						.	" ON gj_u." . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
						.	" AND gj_u." . $_CB_database->NameQuote( 'group' ) . " = gj_g." . $_CB_database->NameQuote( 'id' )
						.	" LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS gj_i"
						.	" ON gj_i." . $_CB_database->NameQuote( 'group' ) . " = gj_g." . $_CB_database->NameQuote( 'id' )
						.	" AND gj_i." . $_CB_database->NameQuote( 'accepted' ) . " = " . $_CB_database->Quote( '0000-00-00 00:00:00' )
						.	" AND ( ( gj_i." . $_CB_database->NameQuote( 'email' ) . " = " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) )
						.	" AND gj_i." . $_CB_database->NameQuote( 'email' ) . " != '' )"
						.	" OR ( gj_i." . $_CB_database->NameQuote( 'user' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
						.	" AND gj_i." . $_CB_database->NameQuote( 'user' ) . " > 0 ) )"
						.	" WHERE gj_g." . $_CB_database->NameQuote( 'id' ) . " = SUBSTRING_INDEX( REPLACE( REPLACE( a." . $_CB_database->NameQuote( 'asset' ) . ", 'notification.groupjive.group.', '' ), 'groupjive.group.', '' ), '.', 1 )"
						.	" AND ( gj_g." . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
						.	" OR ( gj_g." . $_CB_database->NameQuote( 'published' ) . " = 1"
						.	" AND ( gj_g." . $_CB_database->NameQuote( 'type' ) . " IN ( 1, 2 )"
						.	" OR gj_u." . $_CB_database->NameQuote( 'status' ) . " IN ( 0, 1, 2, 3 )"
						.	" OR gj_i." . $_CB_database->NameQuote( 'id' ) . " IS NOT NULL ) ) )"
						.	" AND ( ( gj_c." . $_CB_database->NameQuote( 'published' ) . " = 1"
						.	" AND gj_c." . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( $user->get( 'id', 0, GetterInterface::INT ) ) ) . " )"
						.	( $this->params->get( 'groups_uncategorized', true, GetterInterface::BOOLEAN ) ? " OR gj_g." . $_CB_database->NameQuote( 'category' ) . " = 0" : null ) . " )";
		} else {
			$group		.=	" WHERE gj_g." . $_CB_database->NameQuote( 'id' ) . " = SUBSTRING_INDEX( REPLACE( REPLACE( a." . $_CB_database->NameQuote( 'asset' ) . ", 'notification.groupjive.group.', '' ), 'groupjive.group.', '' ), '.', 1 )";
		}

		$where[]		=	"( ( a." . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( 'groupjive.group.%' )
						.	" AND ( " . $group . " ) > 0 )"
						.	" OR ( a." . $_CB_database->NameQuote( 'asset' ) . " NOT LIKE " . $_CB_database->Quote( 'groupjive.group.%' ) . " ) )";
	}

	/**
	 * @param ActivityTable[]|NotificationTable[] $rows
	 * @param Activity|Notifications              $stream
	 */
	public function activityPrefetch( &$rows, $stream )
	{
		global $_CB_database;

		if ( ! $this->isCompatible() ) {
			return;
		}

		$groupIds				=	array();

		foreach ( $rows as $k => $row ) {
			if ( ! preg_match( '/^groupjive\.group\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
				continue;
			}

			$groupId			=	(int) $matches[1];

			if ( $groupId ) {
				$groupIds[$k]	=	$groupId;
			}
		}

		if ( ! $groupIds ) {
			return;
		}

		$user					=	\CBuser::getMyUserDataInstance();

		$users					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS uc"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS uccb"
								.	' ON uccb.' . $_CB_database->NameQuote( 'id' ) . ' = uc.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS ucj"
								.	' ON ucj.' . $_CB_database->NameQuote( 'id' ) . ' = uccb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE uc." . $_CB_database->NameQuote( 'group' ) . " = g." . $_CB_database->NameQuote( 'id' )
								.	"\n AND uccb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND uccb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND ucj." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $this->params->get( 'groups_users_owner', true, GetterInterface::BOOLEAN ) ) {
			$users				.=	"\n AND uc." . $_CB_database->NameQuote( 'status' ) . " != 4";
		}

		$query					=	'SELECT g.*'
								.	', c.' . $_CB_database->NameQuote( 'name' ) . ' AS _category_name'
								.	', u.' . $_CB_database->NameQuote( 'status' ) . ' AS _user_status'
								.	', i.' . $_CB_database->NameQuote( 'id' ) . ' AS _invite_id'
								.	', ( ' . $users . ' ) AS _users'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_categories' ) . " AS c"
								.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'category' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
								.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
								.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS i"
								.	' ON i.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	' AND i.' . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
								.	' AND ( ( i.' . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) )
								.	' AND i.' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
								.	' OR ( i.' . $_CB_database->NameQuote( 'user' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
								.	' AND i.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )'
								.	"\n WHERE g." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( array_unique( $groupIds ) );
		$_CB_database->setQuery( $query );
		$groups					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $_CB_database ) );

		if ( ! $groups ) {
			foreach ( $groupIds as $k => $groupId ) {
				unset( $rows[$k] );
			}

			return;
		}

		CBGroupJive::getGroup( $groups );
		CBGroupJive::preFetchUsers( $groups );

		foreach ( $groupIds as $k => $groupId ) {
			$group				=	CBGroupJive::getGroup( (int) $groupId );

			if ( ( ! $group->get( 'id', 0, GetterInterface::INT ) ) || ( ! CBGroupJive::canAccessGroup( $group, $user ) ) ) {
				unset( $rows[$k] );
			}
		}
	}

	/**
	 * @param ActivityTable|NotificationTable $row
	 * @param null|string                     $title
	 * @param null|string                     $date
	 * @param null|string                     $message
	 * @param null|string                     $insert
	 * @param null|string                     $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param string                          $output
	 */
	public function activityDisplay( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $output )
	{
		if ( ( ! $this->isCompatible() ) || ( ! preg_match( '/^groupjive\.group\.(\d+)(?:\.([a-zA-Z]+))?/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) ) {
			return;
		} elseif ( isset( $matches[2] ) && ( ! in_array( $matches[2], array( 'join', 'leave', 'create', 'invite', 'message' ) ) ) ) {
			return;
		}

		$group		=	CBGroupJive::getGroup( (int) $matches[1] );

		if ( ! $group->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		CBGroupJive::getTemplate( 'activity' );

		if ( ! $stream instanceof NotificationsInterface ) {
			if ( ( isset( $matches[2] ) ? $matches[2] : '' ) == 'leave' ) {
				$row->params()->set( 'overrides.edit', false );
			}
		}

		\HTML_groupjiveActivity::showActivity( $row, $title, $date, $message, $insert, $footer, $menu, $stream, $matches, $group, $this, $output );
	}

	/**
	 * @param string $asset
	 * @param mixed  $source
	 */
	public function assetSource( $asset, &$source )
	{
		if ( ! preg_match( '/^groupjive\.group\.(\d+)(?:\.([a-zA-Z]+))?/', $asset, $matches ) ) {
			return;
		} elseif ( isset( $matches[2] ) && ( ! in_array( $matches[2], array( 'join', 'leave', 'create', 'invite' ) ) ) ) {
			return;
		}

		$group		=	CBGroupJive::getGroup( (int) $matches[1] );

		if ( ! $group->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$source		=	$group;
	}
}