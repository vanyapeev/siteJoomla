<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Action;

use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class PmsAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $_CB_PMS;

		foreach ( $this->autoaction()->params()->subTree( 'pms' ) as $row ) {
			/** @var ParamsInterface $row */
			$pmFrom			=	$row->get( 'from', null, GetterInterface::STRING );

			if ( ! $pmFrom ) {
				$pmFrom		=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$pmFrom		=	(int) $this->string( $user, $pmFrom );
			}

			if ( ! $pmFrom ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_PMS_NO_FROM', ':: Action [action] :: Private Message skipped due to missing from', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $pmFrom ) {
				$actionUser	=	\CBuser::getUserDataInstance( $pmFrom );
			} else {
				$actionUser	=	$user;
			}

			$pmTo			=	$this->string( $actionUser, $row->get( 'to', null, GetterInterface::STRING ) );

			if ( ! $pmTo ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_PMS_NO_TO', ':: Action [action] :: Private Message skipped due to missing to', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			$pmMessage		=	$this->pmString( $pmTo, $actionUser, $row->get( 'message', null, GetterInterface::RAW ), false );

			if ( ! $pmMessage ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_PMS_NO_MSG', ':: Action [action] :: Private Message skipped due to missing message', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			$pmSubject		=	$this->pmString( $pmTo, $actionUser, $row->get( 'subject', null, GetterInterface::STRING ) );

			$_CB_PMS->sendPMSMSG( $pmTo, $pmFrom, $pmSubject, $pmMessage, true );
		}

		return null;
	}

	/**
	 * Parses a string through action substitutions
	 *
	 * @param int             $recipient
	 * @param null|UserTable  $user
	 * @param string          $string
	 * @param bool            $htmlspecialchars
	 * @param null|array|bool $translate
	 * @param null|bool       $format
	 * @param null|bool       $prepare
	 * @param null|bool       $substitutions
	 * @return string
	 */
	private function pmString( $recipient, $user, $string, $htmlspecialchars = true, $translate = null, $format = null, $prepare = null, $substitutions = null )
	{
		$string		=	str_ireplace( '[recipient]', $recipient, $string );

		return parent::string( $user, $string, $htmlspecialchars, $translate, $format, $prepare, $substitutions );
	}
}