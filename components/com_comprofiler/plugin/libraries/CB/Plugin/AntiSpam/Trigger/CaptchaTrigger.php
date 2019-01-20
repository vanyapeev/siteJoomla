<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AntiSpam\Trigger;

use CBLib\Language\CBTxt;
use CB\Plugin\AntiSpam\Captcha;

defined('CBLIB') or die();

class CaptchaTrigger extends \cbPluginHandler
{

	/**
	 * Displays legacy captcha
	 *
	 * @param bool   $html
	 * @return array|string
	 */
	public function displayCaptcha( $html = true )
	{
		$captcha		=	new Captcha( 'legacy' );

		if ( $html ) {
			$return		=	$captcha->captcha()
						.	$captcha->input();
		} else {
			$return		=	array( $captcha->captcha(), $captcha->input() );
		}

		return $return;
	}

	/**
	 * Validates legacy captcha
	 *
	 * @return bool
	 */
	public function validateCaptcha()
	{
		global $_PLUGINS;

		$captcha	=	new Captcha( 'legacy' );

		if ( ( ! $captcha->load() ) || ( ! $captcha->validate() ) ) {
			$_PLUGINS->_setErrorMSG( ( $captcha->error() ? $captcha->error() : CBTxt::T( 'Invalid Captcha Code' ) ) );
			$_PLUGINS->raiseError();

			return false;
		}

		return true;
	}
}