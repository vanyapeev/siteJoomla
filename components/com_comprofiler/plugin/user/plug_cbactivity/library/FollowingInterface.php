<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CB\Plugin\Activity\Table\FollowTable;

defined('CBLIB') or die();

interface FollowingInterface extends StreamInterface
{
	/**
	 * Retrieves following rows or row count
	 *
	 * @param string $output
	 * @return FollowTable[]|int
	 */
	public function rows( $output = null );

	/**
	 * Retrieves following row
	 *
	 * @param int $id
	 * @return FollowTable
	 */
	public function row( $id );

	/**
	 * Outputs following HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function following( $view = null );
}