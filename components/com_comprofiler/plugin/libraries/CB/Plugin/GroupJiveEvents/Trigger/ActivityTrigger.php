<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveEvents\Trigger;

use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CB\Plugin\Activity\Table\NotificationTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveEvents\CBGroupJiveEvents;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;

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
	 * @param ActivityTable[]|NotificationTable[] $rows
	 * @param Activity|Notifications              $stream
	 */
	public function activityPrefetch( &$rows, $stream )
	{
		global $_CB_database;

		if ( ! $this->isCompatible() ) {
			return;
		}

		$notification			=	( $stream instanceof NotificationsInterface );
		$eventIds				=	array();

		foreach ( $rows as $k => $row ) {
			if ( ! preg_match( '/^groupjive\.group\.(\d+)\.event\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
				continue;
			}

			$eventId			=	(int) $matches[2];

			if ( $eventId ) {
				$eventIds[$k]	=	$eventId;

				if ( ! $notification ) {
					$row->params()->set( 'overrides.tags_asset', 'asset' );
					$row->params()->set( 'overrides.likes_asset', 'asset' );
					$row->params()->set( 'overrides.comments_asset', 'asset' );
				}
			}
		}

		if ( ! $eventIds ) {
			return;
		}

		$user					=	\CBuser::getMyUserDataInstance();

		$guests					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_events_attendance' ) . " AS ea"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS eacb"
								.	' ON eacb.' . $_CB_database->NameQuote( 'id' ) . ' = ea.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS eaj"
								.	' ON eaj.' . $_CB_database->NameQuote( 'id' ) . ' = eacb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE ea." . $_CB_database->NameQuote( 'event' ) . " = e." . $_CB_database->NameQuote( 'id' )
								.	"\n AND eacb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND eacb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND eaj." . $_CB_database->NameQuote( 'block' ) . " = 0";

		$query					=	'SELECT e.*'
								.	', a.' . $_CB_database->NameQuote( 'id' ) . ' AS _attending'
								.	', ( ' . $guests . ' ) AS _guests'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_events' ) . " AS e"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_plugin_events_attendance' ) . " AS a"
								.	' ON a.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
								.	' AND a.' . $_CB_database->NameQuote( 'event' ) . ' = e.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE e." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( array_unique( $eventIds ) );
		$_CB_database->setQuery( $query );
		$events					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveEvents\Table\EventTable', array( $_CB_database ) );

		if ( ! $events ) {
			foreach ( $eventIds as $k => $eventId ) {
				unset( $rows[$k] );
			}

			return;
		}

		CBGroupJiveEvents::getEvent( $events );
		CBGroupJive::preFetchUsers( $events );

		foreach ( $eventIds as $k => $eventId ) {
			$event				=	CBGroupJiveEvents::getEvent( (int) $eventId );

			if ( ! $event->get( 'id', 0, GetterInterface::INT ) ) {
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
		if ( ( ! $this->isCompatible() ) || ( ! preg_match( '/^groupjive\.group\.(\d+)\.event\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) ) {
			return;
		}

		$event		=	CBGroupJiveEvents::getEvent( (int) $matches[2] );

		if ( ! $event->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		CBGroupJive::getTemplate( 'activity', true, true, $this->element );

		if ( ! $stream instanceof NotificationsInterface ) {
			$row->params()->set( 'overrides.tags_asset', 'asset' );
			$row->params()->set( 'overrides.likes_asset', 'asset' );
			$row->params()->set( 'overrides.comments_asset', 'asset' );
		}

		\HTML_groupjiveEventActivity::showEventActivity( $row, $title, $date, $message, $insert, $footer, $menu, $stream, $matches, $event, $this, $output );
	}

	/**
	 * @param string $asset
	 * @param mixed  $source
	 */
	public function assetSource( $asset, &$source )
	{
		if ( ! preg_match( '/^groupjive\.group\.(\d+)\.event\.(\d+)/', $asset, $matches ) ) {
			return;
		}

		$event		=	CBGroupJiveEvents::getEvent( (int) $matches[2] );

		if ( ! $event->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$source		=	$event;
	}
}