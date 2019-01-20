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

class AttemptTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var string  */
	public $ip_address		=	null;
	/** @var string  */
	public $type			=	null;
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
	protected $_tbl			=	'#__comprofiler_plugin_antispam_attempts';

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
		if ( $this->get( 'ip_address', null, GetterInterface::STRING ) == '' ) {
			$this->setError( CBTxt::T( 'IP Address not specified!' ) );

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

			$_PLUGINS->trigger( 'antispam_onBeforeUpdateAttempt', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onBeforeCreateAttempt', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'antispam_onAfterUpdateAttempt', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onAfterCreateAttempt', array( $this ) );
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

		$_PLUGINS->trigger( 'antispam_onBeforeDeleteAttempt', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'antispam_onAfterDeleteAttempt', array( $this ) );

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
		$ip			=	$this->get( 'ip_address', null, GetterInterface::STRING );

		if ( ( ! $ip ) || $this->blocked() || $this->whitelisted() ) {
			return true;
		}

		$block		=	new BlockTable();

		$block->load( array( 'type' => 'ip', 'value' => $ip ) );

		if ( $block->get( 'id', 0, GetterInterface::INT ) ) {
			return true;
		}

		$block->set( 'type', 'ip' );
		$block->set( 'value', $ip );

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
		static $cache		=	array();

		$ip					=	$this->get( 'ip_address', null, GetterInterface::STRING );

		if ( ( ! $ip ) || $this->whitelisted() ) {
			return false;
		}

		if ( ! isset( $cache[$ip] ) ) {
			$cache[$ip]		=	CBAntiSpam::checkBlocked( 'ip', $ip );
		}

		return $cache[$ip];
	}

	/**
	 * @return bool
	 */
	public function whitelist()
	{
		$ip				=	$this->get( 'ip_address', null, GetterInterface::STRING );

		if ( ( ! $ip ) || $this->whitelisted() ) {
			return true;
		}

		$whitelist		=	new WhitelistTable();

		$whitelist->load( array( 'type' => 'ip', 'value' => $ip ) );

		if ( $whitelist->get( 'id', 0, GetterInterface::INT ) ) {
			return true;
		}

		$whitelist->set( 'type', 'ip' );
		$whitelist->set( 'value', $ip );

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
		static $cache		=	array();

		$ip					=	$this->get( 'ip_address', null, GetterInterface::STRING );

		if ( ! $ip ) {
			return false;
		}

		if ( ! isset( $cache[$ip] ) ) {
			$cache[$ip]		=	CBAntiSpam::checkWhitelisted( 'ip', $ip );
		}

		return $cache[$ip];
	}
}