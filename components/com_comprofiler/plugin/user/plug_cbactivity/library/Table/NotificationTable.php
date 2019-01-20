<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Table;

use CB\Plugin\Activity\CBActivity;
use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;

defined('CBLIB') or die();

class NotificationTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var int  */
	public $user			=	null;
	/** @var string  */
	public $asset			=	null;
	/** @var string  */
	public $title			=	null;
	/** @var string  */
	public $message			=	null;
	/** @var int  */
	public $published		=	null;
	/** @var int  */
	public $pinned			=	null;
	/** @var string  */
	public $date			=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_activity_notifications';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( ! $this->get( 'user_id', 0, GetterInterface::INT ) ) {
			$this->setError( CBTxt::T( 'Owner not specified!' ) );

			return false;
		} elseif ( ! $this->get( 'user', 0, GetterInterface::INT ) ) {
			$this->setError( CBTxt::T( 'User not specified!' ) );

			return false;
		}

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new	=	( $this->get( 'id', 0, GetterInterface::INT ) ? false : true );
		$old	=	new self();

		if ( ! $this->get( 'asset', null, GetterInterface::STRING ) ) {
			$this->set( 'asset', 'profile.' . $this->get( 'user', 0, GetterInterface::INT ) );
		}

		$this->set( 'published', $this->get( 'published', 1, GetterInterface::INT ) );
		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime(), GetterInterface::STRING ) );

		if ( ! $new ) {
			$old->load( $this->get( 'id', 0, GetterInterface::INT ) );

			$_PLUGINS->trigger( 'activity_onBeforeUpdateNotification', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateNotification', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateNotification', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateNotification', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$_PLUGINS->trigger( 'activity_onBeforeDeleteNotification', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteNotification', array( $this ) );

		return true;
	}

	/**
	 * @return Registry
	 */
	public function params()
	{
		if ( ! ( $this->get( '_params' ) instanceof Registry ) ) {
			$this->set( '_params', new Registry( $this->get( 'params' ) ) );
		}

		return $this->get( '_params' );
	}

	/**
	 * @return ParamsInterface
	 */
	public function attachments()
	{
		static $cache		=	array();

		$id					=	$this->get( 'id', 0, GetterInterface::INT );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]		=	CBActivity::prepareAttachments( $this->params()->subTree( 'links' ) );
		}

		return $cache[$id];
	}

	/**
	 * @return UserTable|ActivityTable|CommentTable|TagTable|FollowTable|LikeTable|NotificationTable|null
	 */
	public function source()
	{
		global $_PLUGINS;

		static $cache		=	array();

		$id					=	$this->get( 'asset', null, GetterInterface::STRING );

		if ( ! isset( $cache[$id] ) ) {
			$source			=	CBActivity::getSource( $id );

			$_PLUGINS->trigger( 'activity_onNotificationSource', array( $this, &$source ) );

			$cache[$id]		=	$source;
		}

		return $cache[$id];
	}
}