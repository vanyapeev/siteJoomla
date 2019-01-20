<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveVideo\Trigger;

use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CB\Plugin\Activity\Table\NotificationTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveVideo\CBGroupJiveVideo;
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
		$videoIds				=	array();

		foreach ( $rows as $k => $row ) {
			if ( ! preg_match( '/^groupjive\.group\.(\d+)\.video\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
				continue;
			}

			$videoId			=	(int) $matches[2];

			if ( $videoId ) {
				$videoIds[$k]	=	$videoId;

				if ( ! $notification ) {
					$row->params()->set( 'overrides.tags_asset', 'asset' );
					$row->params()->set( 'overrides.likes_asset', 'asset' );
					$row->params()->set( 'overrides.comments_asset', 'asset' );
				}
			}
		}

		if ( ! $videoIds ) {
			return;
		}

		$query					=	'SELECT v.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_video' ) . " AS v"
								.	"\n WHERE v." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( array_unique( $videoIds ) );
		$_CB_database->setQuery( $query );
		$videos					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveVideo\Table\VideoTable', array( $_CB_database ) );

		if ( ! $videos ) {
			foreach ( $videoIds as $k => $videoId ) {
				unset( $rows[$k] );
			}

			return;
		}

		CBGroupJiveVideo::getVideo( $videos );
		CBGroupJive::preFetchUsers( $videos );

		foreach ( $videoIds as $k => $videoId ) {
			$video				=	CBGroupJiveVideo::getVideo( (int) $videoId );

			if ( ! $video->get( 'id', 0, GetterInterface::INT ) ) {
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
		if ( ( ! $this->isCompatible() ) || ( ! preg_match( '/^groupjive\.group\.(\d+)\.video\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) ) {
			return;
		}

		$video		=	CBGroupJiveVideo::getVideo( (int) $matches[2] );

		if ( ! $video->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		CBGroupJive::getTemplate( 'activity', true, true, $this->element );

		if ( ! $stream instanceof NotificationsInterface ) {
			$row->params()->set( 'overrides.tags_asset', 'asset' );
			$row->params()->set( 'overrides.likes_asset', 'asset' );
			$row->params()->set( 'overrides.comments_asset', 'asset' );
		}

		\HTML_groupjiveVideoActivity::showVideoActivity( $row, $title, $date, $message, $insert, $footer, $menu, $stream, $matches, $video, $this, $output );
	}

	/**
	 * @param string $asset
	 * @param mixed  $source
	 */
	public function assetSource( $asset, &$source )
	{
		if ( ! preg_match( '/^groupjive\.group\.(\d+)\.video\.(\d+)/', $asset, $matches ) ) {
			return;
		}

		$video		=	CBGroupJiveVideo::getVideo( (int) $matches[2] );

		if ( ! $video->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$source		=	$video;
	}
}