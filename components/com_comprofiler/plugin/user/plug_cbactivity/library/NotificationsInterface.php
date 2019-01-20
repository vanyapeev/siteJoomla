<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CB\Plugin\Activity\Table\NotificationTable;

defined('CBLIB') or die();

interface NotificationsInterface extends StreamInterface
{
	/**
	 * Retrieves notifications rows or row count
	 *
	 * @param string $output
	 * @return NotificationTable[]|int
	 */
	public function rows( $output = null );

	/**
	 * Retrieves notifications row
	 *
	 * @param int $id
	 * @return NotificationTable
	 */
	public function row( $id );

	/**
	 * Outputs notifications HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function notifications( $view = null );
}