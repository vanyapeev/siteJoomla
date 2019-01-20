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

defined('CBLIB') or die();

interface StreamInterface extends ParamsInterface
{
	/**
	 * Reloads the stream from session by id optionally exclude id, assets, or user
	 *
	 * @param string $id
	 * @param array  $exclude
	 * @return bool
	 */
	public function load( $id, $exclude = array() );

	/**
	 * Resets the stream filters
	 *
	 * @return static
	 */
	public function reset();

	/**
	 * Parses parameters into the stream
	 *
	 * @param ParamsInterface|array|string $params
	 * @param null|string                  $namespace
	 * @return static
	 */
	public function parse( $params, $namespace = null );

	/**
	 * Gets the stream id
	 *
	 * @return string
	 */
	public function id();

	/**
	 * Gets the primary stream asset
	 *
	 * @return string
	 */
	public function asset();

	/**
	 * Gets or sets the raw stream assets
	 *
	 * @param null|array|string|bool $assets null|true: get with wildcards; false: get without wildcards; string: set assets
	 * @return array
	 */
	public function assets( $assets = null );

	/**
	 * Gets or sets the stream target user (owner)
	 *
	 * @param null|UserTable|int $user
	 * @return UserTable|int|null
	 */
	public function user( $user = null );

	/**
	 * Clears the data cache
	 *
	 * @return static
	 */
	public function clear();

	/**
	 * Returns a parser object for parsing stream content
	 *
	 * @param string $string
	 * @return Parser
	 */
	public function parser( $string = '' );

	/**
	 * Caches the stream into session; this is normally only done on creation or parse to preserve parameters between loads
	 * It is not advised to call this manually unless stream parameters have changed after creation and desired result is for them to persist
	 *
	 * @return static
	 */
	public function cache();
}