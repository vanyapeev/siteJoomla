<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveFile\Trigger;

use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;
use CB\Plugin\Activity\Table\NotificationTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveFile\CBGroupJiveFile;
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
		$fileIds				=	array();

		foreach ( $rows as $k => $row ) {
			if ( ! preg_match( '/^groupjive\.group\.(\d+)\.file\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
				continue;
			}

			$fileId				=	(int) $matches[2];

			if ( $fileId ) {
				$fileIds[$k]	=	$fileId;

				if ( $notification ) {
					$row->params()->set( 'overrides.tags_asset', 'asset' );
					$row->params()->set( 'overrides.likes_asset', 'asset' );
					$row->params()->set( 'overrides.comments_asset', 'asset' );
				}
			}
		}

		if ( ! $fileIds ) {
			return;
		}

		$query					=	'SELECT f.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_file' ) . " AS f"
								.	"\n WHERE f." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( array_unique( $fileIds ) );
		$_CB_database->setQuery( $query );
		$files					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveFile\Table\FileTable', array( $_CB_database ) );

		if ( ! $files ) {
			foreach ( $fileIds as $k => $fileId ) {
				unset( $rows[$k] );
			}

			return;
		}

		CBGroupJiveFile::getFile( $files );
		CBGroupJive::preFetchUsers( $files );

		foreach ( $fileIds as $k => $fileId ) {
			$file				=	CBGroupJiveFile::getFile( (int) $fileId );

			if ( ! $file->get( 'id', 0, GetterInterface::INT ) ) {
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
		if ( ( ! $this->isCompatible() ) || ( ! preg_match( '/^groupjive\.group\.(\d+)\.file\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) ) {
			return;
		}

		$file		=	CBGroupJiveFile::getFile( (int) $matches[2] );

		if ( ! $file->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		CBGroupJive::getTemplate( 'activity', true, true, $this->element );

		if ( ! $stream instanceof NotificationsInterface ) {
			$row->params()->set( 'overrides.tags_asset', 'asset' );
			$row->params()->set( 'overrides.likes_asset', 'asset' );
			$row->params()->set( 'overrides.comments_asset', 'asset' );
		}

		\HTML_groupjiveFileActivity::showFileActivity( $row, $title, $date, $message, $insert, $footer, $menu, $stream, $matches, $file, $this, $output );
	}

	/**
	 * @param string $asset
	 * @param mixed  $source
	 */
	public function assetSource( $asset, &$source )
	{
		if ( ! preg_match( '/^groupjive\.group\.(\d+)\.file\.(\d+)/', $asset, $matches ) ) {
			return;
		}

		$file		=	CBGroupJiveFile::getFile( (int) $matches[2] );

		if ( ! $file->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$source		=	$file;
	}
}