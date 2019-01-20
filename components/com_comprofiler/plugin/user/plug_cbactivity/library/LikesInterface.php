<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CB\Plugin\Activity\Table\LikeTable;
use CB\Plugin\Activity\Table\LikeTypeTable;

defined('CBLIB') or die();

interface LikesInterface extends StreamInterface
{
	/**
	 * Returns an array of types for likes
	 *
	 * @return LikeTypeTable[]
	 */
	public function types();

	/**
	 * Retrieves likes rows or row count
	 *
	 * @param string $output
	 * @return LikeTable[]|int
	 */
	public function rows( $output = null );

	/**
	 * Retrieves likes row
	 *
	 * @param int $id
	 * @return LikeTable
	 */
	public function row( $id );

	/**
	 * Outputs likes HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function likes( $view = null );
}