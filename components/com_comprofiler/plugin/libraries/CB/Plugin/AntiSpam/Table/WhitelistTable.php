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

defined('CBLIB') or die();

class WhitelistTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var string  */
	public $type			=	null;
	/** @var string  */
	public $value			=	null;
	/** @var string  */
	public $reason			=	null;
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
	protected $_tbl			=	'#__comprofiler_plugin_antispam_whitelist';

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

			$_PLUGINS->trigger( 'antispam_onBeforeUpdateWhitelist', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onBeforeCreateWhitelist', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'antispam_onAfterUpdateWhitelist', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onAfterCreateWhitelist', array( $this ) );
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

		$_PLUGINS->trigger( 'antispam_onBeforeDeleteWhitelist', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'antispam_onAfterDeleteWhitelist', array( $this ) );

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
}