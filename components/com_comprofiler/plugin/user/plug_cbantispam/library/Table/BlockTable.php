<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AntiSpam\Table;

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CB\Plugin\AntiSpam\CBAntiSpam;

defined('CBLIB') or die();

class BlockTable extends Table
{
	/** @var int  */
	public $id						=	null;
	/** @var string  */
	public $type					=	null;
	/** @var string  */
	public $value					=	null;
	/** @var string  */
	public $duration				=	null;
	/** @var string  */
	public $reason					=	null;
	/** @var string  */
	public $date					=	null;
	/** @var string  */
	public $params					=	null;

	/** @var string  */
	protected $_custom_duration		=	null;
	/** @var Registry  */
	protected $_params				=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl					=	'#__comprofiler_plugin_antispam_block';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key				=	'id';

	/**
	 * Copy the named array or object content into this object as vars
	 * only existing vars of object are filled.
	 * When undefined in array, object variables are kept.
	 *
	 * WARNING: DOES addslashes / escape BY DEFAULT
	 *
	 * Can be overridden or overloaded.
	 *
	 * @param  array|object  $array         The input array or object
	 * @param  string        $ignore        Fields to ignore
	 * @param  string        $prefix        Prefix for the array keys
	 * @return boolean                      TRUE: ok, FALSE: error on array binding
	 */
	public function bind( $array, $ignore = '', $prefix = null )
	{
		$bind								=	parent::bind( $array, $ignore, $prefix );

		// Bind the custom duration to duration if it exists
		if ( $bind ) {
			if ( is_array( $array ) && isset( $array['_custom_duration'] ) ) {
				$this->_custom_duration		=	$array['_custom_duration'];
			} elseif ( isset( $array->_custom_duration ) ) {
				$this->_custom_duration		=	$array->_custom_duration;
			}

			if ( $this->_custom_duration != '' ) {
				$this->duration				=	$this->_custom_duration;
				$this->_custom_duration		=	null;
			}
		}

		return $bind;
	}

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( $this->get( 'type', null, GetterInterface::STRING ) == '' ) {
			$this->setError( CBTxt::T( 'Type not specified!' ) );

			return false;
		} elseif ( $this->get( 'value', null, GetterInterface::STRING ) == '' ) {
			$this->setError( CBTxt::T( 'Value not specified!' ) );

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

			$_PLUGINS->trigger( 'antispam_onBeforeUpdateBlock', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onBeforeCreateBlock', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'antispam_onAfterUpdateBlock', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onAfterCreateBlock', array( $this ) );
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

		$_PLUGINS->trigger( 'antispam_onBeforeDeleteBlock', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'antispam_onAfterDeleteBlock', array( $this ) );

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
	 * @return string
	 */
	public function expiry()
	{
		static $cache		=	array();

		$id					=	$this->get( 'id', 0, GetterInterface::INT );

		if ( ! isset( $cache[$id] ) ) {
			if ( $this->get( 'duration', null, GetterInterface::STRING ) ) {
				$expiry		=	Application::Date( $this->get( 'date', null, GetterInterface::STRING ), 'UTC' )->modify( strtoupper( $this->get( 'duration', null, GetterInterface::STRING ) ) )->format( 'Y-m-d H:i:s' );
			} else {
				$expiry		=	'0000-00-00 00:00:00';
			}

			$cache[$id]		=	$expiry;
		}

		return $cache[$id];
	}

	/**
	 * @return string
	 */
	public function expired()
	{
		static $cache		=	array();

		$id					=	$this->get( 'id', 0, GetterInterface::INT );

		if ( ! isset( $cache[$id] ) ) {
			if ( $this->get( 'duration', null, GetterInterface::STRING ) ) {
				$expired	=	( Application::Date( 'now', 'UTC' )->getTimestamp() >= Application::Date( $this->get( 'date', null, GetterInterface::STRING ), 'UTC' )->modify( strtoupper( $this->get( 'duration', null, GetterInterface::STRING ) ) )->getTimestamp() );
			} else {
				$expired	=	false;
			}

			$cache[$id]		=	$expired;
		}

		return $cache[$id];
	}

	/**
	 * @return bool
	 */
	public function blocked()
	{
		if ( $this->whitelisted() ) {
			return false;
		}

		return ( ! $this->expired() );
	}

	/**
	 * @return bool
	 */
	public function whitelisted()
	{
		static $cache				=	array();

		$type						=	$this->get( 'type', null, GetterInterface::STRING );
		$value						=	$this->get( 'value', null, GetterInterface::STRING );

		if ( ! isset( $cache[$type][$value] ) ) {
			$cache[$type][$value]	=	CBAntiSpam::checkWhitelisted( $type, $value );
		}

		return $cache[$type][$value];
	}
}