<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CB\Plugin\Activity\Table\CommentTable;

defined('CBLIB') or die();

interface CommentsInterface extends StreamInterface
{
	/**
	 * Retrieves comments rows or row count
	 *
	 * @param string $output
	 * @return CommentTable[]|int
	 */
	public function rows( $output = null );

	/**
	 * Retrieves comments row
	 *
	 * @param int $id
	 * @return CommentTable
	 */
	public function row( $id );

	/**
	 * Outputs comments HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function comments( $view = null );
}