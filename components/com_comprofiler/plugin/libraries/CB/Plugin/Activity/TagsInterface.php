<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CB\Plugin\Activity\Table\TagTable;

defined('CBLIB') or die();

interface TagsInterface extends ParamsInterface
{
	/**
	 * Reloads the tags from session by id optionally exclude id, asset, or user
	 *
	 * @param string $id
	 * @param array  $exclude
	 * @return bool
	 */
	public function load( $id, $exclude = array() );

	/**
	 * Parses parameters into the tags
	 *
	 * @param ParamsInterface|array $params
	 * @param null|string           $prefix
	 * @return self
	 */
	public function parse( $params, $prefix = null );

	/**
	 * Gets the tags id
	 *
	 * @return string
	 */
	public function id();

	/**
	 * Gets or sets the tags asset
	 *
	 * @param null|string $asset
	 * @return null|string
	 */
	public function asset( $asset = null );

	/**
	/**
	 * Gets or sets the tags target user (owner)
	 *
	 * @param null|UserTable|int $user
	 * @return UserTable|int|null
	 */
	public function user( $user = null );

	/**
	 * Clears the data cache
	 *
	 * @return self
	 */
	public function clear();

	/**
	 * Resets the tags filters
	 *
	 * @return self
	 */
	public function reset();

	/**
	 * Retrieves tags rows or row count
	 *
	 * @param string $output
	 * @return TagTable[]|int
	 */
	public function rows( $output = null );

	/**
	 * Retrieves tags row
	 *
	 * @param int $id
	 * @return TagTable
	 */
	public function row( $id );

	/**
	 * Outputs tags HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function tags( $view = null );

	/**
	 * Caches the tags into session; this is normally only done on creation or parse to preserve parameters between loads
	 * It is not advised to call this manually unless tags parameters have changed after creation and desired result is for them to persist
	 *
	 * @return self
	 */
	public function cache();
}