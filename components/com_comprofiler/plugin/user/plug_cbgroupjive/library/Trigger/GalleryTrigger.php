<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJive\Trigger;

use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Database\Table\UserTable;
use CB\Plugin\Gallery\Gallery;

defined('CBLIB') or die();

class GalleryTrigger extends \cbPluginHandler
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
	 * @param bool      $access
	 * @param UserTable $user
	 * @param Gallery   $gallery
	 */
	public function createFolderAccess( &$access, $user, $gallery )
	{
		if ( ( ! $this->isCompatible() ) || ( ! preg_match( '/^groupjive\.group\.(\d+)(?:\.([a-zA-Z]+))?/', $gallery->asset(), $matches ) ) ) {
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
	 * @param bool      $access
	 * @param string    $type
	 * @param string    $method
	 * @param UserTable $user
	 * @param Gallery   $gallery
	 */
	public function createItemAccess( &$access, $type, $method, $user, $gallery )
	{
		if ( ( ! $this->isCompatible() ) || ( ! preg_match( '/^groupjive\.group\.(\d+)(?:\.([a-zA-Z]+))?/', $gallery->asset(), $matches ) ) ) {
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
	 * @param bool      $access
	 * @param UserTable $user
	 * @param Gallery   $gallery
	 */
	public function moderateAccess( &$access, $user, $gallery )
	{
		if ( ( ! $this->isCompatible() ) || ( ! preg_match( '/^groupjive\.group\.(\d+)(?:\.([a-zA-Z]+))?/', $gallery->asset(), $matches ) ) ) {
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
	 * @param string $asset
	 * @param mixed  $source
	 */
	public function assetSource( $asset, &$source )
	{
		if ( ! preg_match( '/^(?:notification\.(\d+)\.)?groupjive\.group\.(\d+)(?:\.([a-zA-Z]+))?/', $asset, $matches ) ) {
			return;
		} elseif ( isset( $matches[3] ) && ( ! in_array( $matches[3], array( 'join', 'leave', 'create', 'invite' ) ) ) ) {
			return;
		}

		$group		=	CBGroupJive::getGroup( (int) $matches[2] );

		if ( ! $group->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$source		=	$group;
	}
}