<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CB\Plugin\Activity\Table\ActivityTable;

defined('CBLIB') or die();

interface ActivityInterface extends StreamInterface
{
	/**
	 * Retrieves activity rows or row count
	 *
	 * @param string $output
	 * @return ActivityTable[]|int
	 */
	public function rows( $output = null );

	/**
	 * Retrieves activity row
	 *
	 * @param int $id
	 * @return ActivityTable
	 */
	public function row( $id );

	/**
	 * Outputs activity HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function activity( $view = null );
}