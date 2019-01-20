<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Connect\Trigger;

use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CB\Plugin\Connect\CBConnect;
use CB\Plugin\Connect\Connect;

defined('CBLIB') or die();

class ButtonTrigger extends \cbPluginHandler
{

	/**
	 * Outputs the provider buttons to the login/logout form
	 *
	 * @param int      $nameLenght
	 * @param int      $passLenght
	 * @param int      $horizontal
	 * @param string   $classSfx
	 * @param Registry $params
	 * @return array|null|string
	 */
	public function getLoginButtons( $nameLenght, $passLenght, $horizontal, $classSfx, $params )
	{
		global $_CB_framework;

		$return				=	null;

		foreach ( CBConnect::getProviders() as $id => $provider ) {
			$connect		=	new Connect( $id );

			$return			.=	$connect->button( $horizontal );
		}

		if ( $return ) {
			static $CSS		=	0;

			if ( ! $CSS++ ) {
				$_CB_framework->document->addHeadStyleSheet( $_CB_framework->getCfg( 'live_site' ) . '/components/com_comprofiler/plugin/user/plug_cbconnect/css/cbconnect.css' );
			}

			$return			=	'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . ' cbConnectButtons">'
							.		$return
							.	'</div>';

			return array( 'afterButton' => $return );
		}

		return null;
	}

	/**
	 * Outputs the provider buttons to the registration form
	 *
	 * @param UserTable $user
	 * @param string    $regErrorMSG
	 * @return null|string
	 */
	public function getRegistrationButtons( $user, $regErrorMSG )
	{
		global $_CB_framework;

		if ( $user->get( 'id', 0, GetterInterface::INT ) ) {
			return null;
		}

		$return				=	null;

		foreach ( CBConnect::getProviders() as $id => $provider ) {
			$connect		=	new Connect( $id );

			$return			.=	$connect->button( 1, true );
		}

		if ( $return ) {
			static $CSS		=	0;

			if ( ! $CSS++ ) {
				$_CB_framework->document->addHeadStyleSheet( $_CB_framework->getCfg( 'live_site' ) . '/components/com_comprofiler/plugin/user/plug_cbconnect/css/cbconnect.css' );
			}

			$return			=	'<div class="content-spacer text-center cb_template cb_template_' . selectTemplate( 'dir' ) . ' cbConnectButtons">'
							.		$return
							.	'</div>';

			return $return;
		}

		return null;
	}
}