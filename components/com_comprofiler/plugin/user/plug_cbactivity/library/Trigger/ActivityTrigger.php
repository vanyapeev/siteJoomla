<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Trigger;

use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;

defined('CBLIB') or die();

class ActivityTrigger extends \cbFieldHandler
{

	/**
	 * Handles adjusting parameters for non-core CB Activity entries
	 *
	 * @param ActivityTable[]|NotificationTable[] $rows
	 * @param Activity|Notifications              $stream
	 */
	public function activityLoad( &$rows, $stream )
	{
		CBActivity::getTemplate( 'activity_core', false, false );

		\HTML_cbactivityActivityCore::parseAccess( $rows, $stream );
	}

	/**
	 * Handles loading source for non-core CB Activity objects
	 *
	 * @param string $asset
	 * @param mixed  $source
	 */
	public function assetSource( $asset, &$source )
	{
		CBActivity::getTemplate( 'activity_core', false, false );

		\HTML_cbactivityActivityCore::parseSource( $asset, $source );
	}
}