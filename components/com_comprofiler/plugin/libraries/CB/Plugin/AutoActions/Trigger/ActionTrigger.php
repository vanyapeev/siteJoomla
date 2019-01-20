<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Trigger;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\AutoActions\Table\AutoActionTable;
use CB\Plugin\AutoActions\CBAutoActions;

defined('CBLIB') or die();

class ActionTrigger extends \cbPluginHandler
{

	/**
	 * Prepares the action to be executed from trigger
	 *
	 * @param AutoActionTable|int $autoaction
	 * @param string              $event
	 * @param mixed               $var1
	 * @param mixed               $var2
	 * @param mixed               $var3
	 * @param mixed               $var4
	 * @param mixed               $var5
	 * @param mixed               $var6
	 * @param mixed               $var7
	 * @param mixed               $var8
	 * @param mixed               $var9
	 * @param mixed               $var10
	 * @param mixed               $var11
	 * @param mixed               $var12
	 * @param mixed               $var13
	 * @param mixed               $var14
	 * @param mixed               $var15
	 * @return mixed
	 */
	static public function triggerAction( $autoaction, $event, &$var1 = null, &$var2 = null, &$var3 = null, &$var4 = null, &$var5 = null, &$var6 = null, &$var7 = null, &$var8 = null, &$var9 = null, &$var10 = null, &$var11 = null, &$var12 = null, &$var13 = null, &$var14 = null, &$var15 = null )
	{
		if ( is_integer( $autoaction ) ) {
			$autoactionId		=	$autoaction;

			$autoaction			=	new AutoActionTable();

			$autoaction->load( $autoactionId );
		}

		if ( ! $autoaction->get( 'id', 0, GetterInterface::INT ) ) {
			return null;
		}

		$variables				=	array(	'trigger'	=>	$event,
											'loop_key'	=>	null,
											'loop'		=>	null,
											'var1'		=>	&$var1,
											'var2'		=>	&$var2,
											'var3'		=>	&$var3,
											'var4'		=>	&$var4,
											'var5'		=>	&$var5,
											'var6'		=>	&$var6,
											'var7'		=>	&$var7,
											'var8'		=>	&$var8,
											'var9'		=>	&$var9,
											'var10'		=>	&$var10,
											'var11'		=>	&$var11,
											'var12'		=>	&$var12,
											'var13'		=>	&$var13,
											'var14'		=>	&$var14,
											'var15'		=>	&$var15
										);

		return $autoaction->run( $variables );
	}
}