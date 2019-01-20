<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\ProfileBook\Table;

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;

defined('CBLIB') or die();

class EntryTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var string  */
	public $mode			=	null;
	/** @var int  */
	public $posterid		=	null;
	/** @var string  */
	public $posterip		=	null;
	/** @var string  */
	public $postername		=	null;
	/** @var string  */
	public $posteremail		=	null;
	/** @var string  */
	public $posterlocation	=	null;
	/** @var string  */
	public $posterurl		=	null;
	/** @var int  */
	public $postervote		=	null;
	/** @var string  */
	public $postertitle		=	null;
	/** @var string  */
	public $postercomment	=	null;
	/** @var string  */
	public $date			=	null;
	/** @var int  */
	public $userid			=	null;
	/** @var string  */
	public $feedback		=	null;
	/** @var string  */
	public $editdate		=	null;
	/** @var int  */
	public $editedbyid		=	null;
	/** @var string  */
	public $editedbyname	=	null;
	/** @var int  */
	public $published		=	null;
	/** @var int  */
	public $status			=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plug_profilebook';

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
		if ( ( ! $this->get( 'posterid', 0, GetterInterface::INT ) ) && ( $this->get( 'postername', null, GetterInterface::STRING ) == '' ) ) {
			$this->setError( CBTxt::T( 'Name not specified!' ) );

			return false;
		} elseif ( ( ! $this->get( 'posterid', 0, GetterInterface::INT ) ) && ( $this->get( 'posteremail', null, GetterInterface::STRING ) == '' ) ) {
			$this->setError( CBTxt::T( 'Email not specified!' ) );

			return false;
		} elseif ( $this->get( 'mode', null, GetterInterface::STRING ) == '' ) {
			$this->setError( CBTxt::T( 'Mode not specified!' ) );

			return false;
		} elseif ( ( $this->get( 'mode', null, GetterInterface::STRING ) == 'b' ) && ( $this->get( 'postertitle', null, GetterInterface::STRING ) == '' ) ) {
			$this->setError( CBTxt::T( 'Title not specified!' ) );

			return false;
		} elseif ( $this->get( 'postercomment', null, GetterInterface::STRING ) == '' ) {
			switch ( $this->get( 'mode', null, GetterInterface::STRING )  ) {
				case 'g':
					$this->setError( CBTxt::T( 'Signature not specified!' ) );
					break;
				case 'b':
					$this->setError( CBTxt::T( 'Blog not specified!' ) );
					break;
				case 'w':
				default:
					$this->setError( CBTxt::T( 'Post not specified!' ) );
					break;
			}

			return false;
		} elseif ( ! $this->get( 'userid', 0, GetterInterface::INT ) ) {
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

		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime(), GetterInterface::STRING ) );

		if ( $this->get( 'mode', null, GetterInterface::STRING ) == 'b' ) {
			$this->set( 'status', 1 );
		}

		if ( ! $new ) {
			$old->load( $this->get( 'id', 0, GetterInterface::INT ) );

			$_PLUGINS->trigger( 'pb_onBeforeUpdateEntry', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'pb_onBeforeCreateEntry', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'pb_onAfterUpdateEntry', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'pb_onAfterCreateEntry', array( $this ) );
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

		$_PLUGINS->trigger( 'pb_onBeforeDeleteEntry', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'pb_onAfterDeleteEntry', array( $this ) );

		return true;
	}
}