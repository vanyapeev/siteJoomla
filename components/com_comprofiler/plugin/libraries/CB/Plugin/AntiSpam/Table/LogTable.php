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

class LogTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var string  */
	public $ip_address		=	null;
	/** @var int  */
	public $count			=	null;
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
	protected $_tbl			=	'#__comprofiler_plugin_antispam_log';

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
		if ( ( ! $this->get( 'user_id', 0, GetterInterface::INT ) ) && ( $this->get( 'ip_address', null, GetterInterface::STRING ) == '' ) ) {
			$this->setError( CBTxt::T( 'User ID or IP Address not specified!' ) );

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

			$_PLUGINS->trigger( 'antispam_onBeforeUpdateLog', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onBeforeCreateLog', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'antispam_onAfterUpdateLog', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onAfterCreateLog', array( $this ) );
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

		$_PLUGINS->trigger( 'antispam_onBeforeDeleteLog', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'antispam_onAfterDeleteLog', array( $this ) );

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
	 * @return bool
	 */
	public function block()
	{
		$type			=	'user';
		$value			=	$this->get( 'user_id', 0, GetterInterface::INT );

		if ( ! $value ) {
			$type		=	'ip';
			$value		=	$this->get( 'ip_address', null, GetterInterface::STRING );
		}

		if ( ( ! $value ) || $this->blocked() || $this->whitelisted() ) {
			return true;
		}

		$block			=	new BlockTable();

		$block->load( array( 'type' => $type, 'value' => $value ) );

		if ( $block->get( 'id', 0, GetterInterface::INT ) ) {
			return true;
		}

		$block->set( 'type', $type );
		$block->set( 'value', $value );

		if ( ! $block->store() ) {
			$this->setError( $block->getError() );

			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function blocked()
	{
		static $cache				=	array();

		$type						=	'user';
		$value						=	$this->get( 'user_id', 0, GetterInterface::INT );

		if ( ! $value ) {
			$type					=	'ip';
			$value					=	$this->get( 'ip_address', null, GetterInterface::STRING );
		}

		if ( ( ! $value ) || $this->whitelisted() ) {
			return false;
		}

		if ( ! isset( $cache[$type][$value] ) ) {
			$cache[$type][$value]	=	CBAntiSpam::checkBlocked( $type, $value );
		}

		return $cache[$type][$value];
	}

	/**
	 * @return bool
	 */
	public function whitelist()
	{
		$type			=	'user';
		$value			=	$this->get( 'user_id', 0, GetterInterface::INT );

		if ( ! $value ) {
			$type		=	'ip';
			$value		=	$this->get( 'ip_address', null, GetterInterface::STRING );
		}

		if ( ( ! $value ) || $this->whitelisted() ) {
			return true;
		}

		$whitelist		=	new WhitelistTable();

		$whitelist->load( array( 'type' => $type, 'value' => $value ) );

		if ( $whitelist->get( 'id', 0, GetterInterface::INT ) ) {
			return true;
		}

		$whitelist->set( 'type', $type );
		$whitelist->set( 'value', $value );

		if ( ! $whitelist->store() ) {
			$this->setError( $whitelist->getError() );

			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function whitelisted()
	{
		static $cache				=	array();

		$type						=	'user';
		$value						=	$this->get( 'user_id', 0, GetterInterface::INT );

		if ( ! $value ) {
			$type					=	'ip';
			$value					=	$this->get( 'ip_address', null, GetterInterface::STRING );
		}

		if ( ! $value ) {
			return false;
		}

		if ( ! isset( $cache[$type][$value] ) ) {
			$cache[$type][$value]	=	CBAntiSpam::checkWhitelisted( $type, $value );
		}

		return $cache[$type][$value];
	}
}