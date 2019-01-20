<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Table;

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\Activity\CBActivity;

defined('CBLIB') or die();

class LikeTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var string  */
	public $asset			=	null;
	/** @var int  */
	public $type			=	null;
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
	protected $_tbl			=	'#__comprofiler_plugin_activity_likes';

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
		} elseif ( $this->get( 'asset', null, GetterInterface::STRING ) == '' ) {
			$this->setError( CBTxt::T( 'Asset not specified!' ) );

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

		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime(), GetterInterface::STRING ) );

		if ( ! $new ) {
			$old->load( $this->get( 'id', 0, GetterInterface::INT ) );

			$_PLUGINS->trigger( 'activity_onBeforeUpdateLike', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateLike', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateLike', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateLike', array( $this ) );
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

		$_PLUGINS->trigger( 'activity_onBeforeDeleteLike', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		// Deletes activity about this like:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity' )
						.	"\n WHERE " . $this->getDbo()->NameQuote( 'asset' ) . " = " . $this->getDbo()->Quote( 'like.' . $this->get( 'id', 0, GetterInterface::INT ) );
		$this->getDbo()->setQuery( $query );
		$activities		=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\ActivityTable', array( $this->getDbo() ) );

		/** @var ActivityTable[] $activities */
		foreach ( $activities as $activity ) {
			$activity->delete();
		}

		// Deletes comments about this like:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity_comments' )
						.	"\n WHERE " . $this->getDbo()->NameQuote( 'asset' ) . " = " . $this->getDbo()->Quote( 'like.' . $this->get( 'id', 0, GetterInterface::INT ) );
		$this->getDbo()->setQuery( $query );
		$comments		=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\CommentTable', array( $this->getDbo() ) );

		/** @var CommentTable[] $comments */
		foreach ( $comments as $comment ) {
			$comment->delete();
		}

		// Deletes like specific notifications:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity_notifications' )
						.	"\n WHERE " . $this->getDbo()->NameQuote( 'asset' ) . " = " . $this->getDbo()->Quote( 'like.' . $this->get( 'id', 0, GetterInterface::INT ) );
		$this->getDbo()->setQuery( $query );
		$notifications	=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\NotificationTable', array( $this->getDbo() ) );

		/** @var NotificationTable[] $notifications */
		foreach ( $notifications as $notification ) {
			$notification->delete();
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteLike', array( $this ) );

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
	 * @return UserTable|ActivityTable|CommentTable|TagTable|FollowTable|LikeTable|NotificationTable|null
	 */
	public function source()
	{
		global $_PLUGINS;

		static $cache		=	array();

		$id					=	$this->get( 'asset', null, GetterInterface::STRING );

		if ( ! isset( $cache[$id] ) ) {
			$source			=	CBActivity::getSource( $id );

			$_PLUGINS->trigger( 'activity_onLikeSource', array( $this, &$source ) );

			$cache[$id]		=	$source;
		}

		return $cache[$id];
	}

	/**
	 * @return LikeTypeTable
	 */
	public function type()
	{
		static $cache		=	array();

		$id					=	$this->get( 'type', 0, GetterInterface::INT );

		if ( ! isset( $cache[$id] ) ) {
			$type			=	new LikeTypeTable();

			$type->load( $id );

			$cache[$id]		=	$type;
		}

		return $cache[$id];
	}
}