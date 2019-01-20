<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveWall\Trigger;

use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CB\Plugin\Activity\Table\NotificationTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveWall\CBGroupJiveWall;
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
		$postIds				=	array();

		foreach ( $rows as $k => $row ) {
			if ( ! preg_match( '/^groupjive\.group\.(\d+)\.wall\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
				continue;
			}

			$postId				=	(int) $matches[2];

			if ( $postId ) {
				$postIds[$k]	=	$postId;

				if ( ! $notification ) {
					$row->params()->set( 'overrides.tags_asset', 'asset' );
					$row->params()->set( 'overrides.likes_asset', 'asset' );
					$row->params()->set( 'overrides.comments_asset', 'groupjive.group.' . (int) $matches[1] . '.wall.' . (int) $matches[2] . ',groupjive.group.' . (int) $matches[1] . '.wall.' . (int) $matches[2] . '.reply.%' );
				}
			}
		}

		if ( ! $postIds ) {
			return;
		}

		$replies				=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_wall' ) . " AS r"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS rcb"
								.	' ON rcb.' . $_CB_database->NameQuote( 'id' ) . ' = r.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS rj"
								.	' ON rj.' . $_CB_database->NameQuote( 'id' ) . ' = rcb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE r." . $_CB_database->NameQuote( 'reply' ) . " = p." . $_CB_database->NameQuote( 'id' )
								.	"\n AND rcb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND rcb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND rj." . $_CB_database->NameQuote( 'block' ) . " = 0";

		$query					=	'SELECT p.*'
								.	', ( ' . $replies . ' ) AS _replies'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_wall' ) . " AS p"
								.	"\n WHERE p." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( array_unique( $postIds ) );
		$_CB_database->setQuery( $query );
		$posts					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveWall\Table\WallTable', array( $_CB_database ) );

		if ( ! $posts ) {
			foreach ( $postIds as $k => $postId ) {
				unset( $rows[$k] );
			}

			return;
		}

		CBGroupJiveWall::getPost( $posts );
		CBGroupJive::preFetchUsers( $posts );

		foreach ( $postIds as $k => $postId ) {
			$post				=	CBGroupJiveWall::getPost( (int) $postId );

			if ( ! $post->get( 'id', 0, GetterInterface::INT ) ) {
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
		if ( ( ! $this->isCompatible() ) || ( ! preg_match( '/^groupjive\.group\.(\d+)\.wall\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) ) {
			return;
		}

		$post		=	CBGroupJiveWall::getPost( (int) $matches[2] );

		if ( ! $post->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		CBGroupJive::getTemplate( 'activity', true, true, $this->element );

		if ( ! $stream instanceof NotificationsInterface ) {
			$row->params()->set( 'overrides.edit', false );
			$row->params()->set( 'overrides.actions', false );
			$row->params()->set( 'overrides.locations', false );
			$row->params()->set( 'overrides.links', false );
			$row->params()->set( 'overrides.tags', false );
			$row->params()->set( 'overrides.tags_asset', 'asset' );
			$row->params()->set( 'overrides.likes_asset', 'asset' );
			$row->params()->set( 'overrides.comments_asset', 'groupjive.group.' . $post->group()->get( 'id', 0, GetterInterface::INT ) . '.wall.' . $post->get( 'id', 0, GetterInterface::INT ) . ',groupjive.group.' . $post->group()->get( 'id', 0, GetterInterface::INT ) . '.wall.' . $post->get( 'id', 0, GetterInterface::INT ) . '.reply.%' );
		}

		\HTML_groupjiveWallActivity::showWallActivity( $row, $title, $date, $message, $insert, $footer, $menu, $stream, $matches, $post, $this, $output );
	}

	/**
	 * @param string $asset
	 * @param mixed  $source
	 */
	public function assetSource( $asset, &$source )
	{
		if ( ! preg_match( '/^groupjive\.group\.(\d+)\.wall\.(\d+)/', $asset, $matches ) ) {
			return;
		}

		$post		=	CBGroupJiveWall::getPost( (int) $matches[2] );

		if ( ! $post->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$source		=	$post;
	}
}