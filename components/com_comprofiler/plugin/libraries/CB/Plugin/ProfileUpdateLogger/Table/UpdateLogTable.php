<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\ProfileUpdateLogger\Table;

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;

defined('CBLIB') or die();

class UpdateLogTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var string  */
	public $changedate		=	null;
	/** @var int  */
	public $profileid		=	null;
	/** @var string  */
	public $editedbyip		=	null;
	/** @var int  */
	public $editedbyid		=	null;
	/** @var string  */
	public $mode			=	null;
	/** @var string  */
	public $fieldname		=	null;
	/** @var string  */
	public $oldvalue		=	null;
	/** @var int  */
	public $newvalue		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plug_pulogger';

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
		if ( ! $this->get( 'profileid', 0, GetterInterface::INT ) ) {
			$this->setError( CBTxt::T( 'User not specified!' ) );

			return false;
		} elseif ( $this->get( 'fieldname', null, GetterInterface::STRING ) == '' ) {
			$this->setError( CBTxt::T( 'Field not specified!' ) );

			return false;
		} elseif ( $this->get( 'oldvalue', null, GetterInterface::RAW ) === $this->get( 'newvalue', null, GetterInterface::RAW ) ) {
			$this->setError( CBTxt::T( 'Value is unchanged!' ) );

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

		$ipAddresses	=	cbGetIParray();
		$new			=	( $this->get( 'id', 0, GetterInterface::INT ) ? false : true );
		$old			=	new self();

		$this->set( 'changedate', $this->get( 'changedate', Application::Database()->getUtcDateTime(), GetterInterface::STRING ) );
		$this->set( 'editedbyip', $this->get( 'editedbyip', trim( array_shift( $ipAddresses ) ), GetterInterface::STRING ) );
		$this->set( 'editedbyid', $this->get( 'editedbyid', Application::MyUser()->getUserId(), GetterInterface::INT ) );
		$this->set( 'mode', $this->get( 'mode', Application::Cms()->getClientId(), GetterInterface::INT ) );

		if ( ! $new ) {
			$old->load( $this->get( 'id', 0, GetterInterface::INT ) );

			$_PLUGINS->trigger( 'pu_onBeforeUpdateLog', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'pu_onBeforeCreateLog', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'pu_onAfterUpdateLog', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'pu_onAfterCreateLog', array( $this ) );
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

		$_PLUGINS->trigger( 'pu_onBeforeDeleteLog', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'pu_onAfterDeleteLog', array( $this ) );

		return true;
	}
}