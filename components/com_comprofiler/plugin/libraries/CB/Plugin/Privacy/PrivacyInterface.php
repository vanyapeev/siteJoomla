<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Privacy;

use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CB\Plugin\Privacy\Table\PrivacyTable;

defined('CBLIB') or die();

interface PrivacyInterface extends ParamsInterface
{
	/**
	 * Reloads the privacy from session by id optionally exclude id, asset, or user
	 *
	 * @param string $id
	 * @param array  $exclude
	 * @return bool
	 */
	public function load( $id, $exclude = array() );

	/**
	 * Parses parameters into the privacy
	 *
	 * @param ParamsInterface|array $params
	 * @param null|string           $prefix
	 * @return self
	 */
	public function parse( $params, $prefix = null );

	/**
	 * Gets the privacy id
	 *
	 * @return string
	 */
	public function id();

	/**
	 * Gets or sets the privacy asset
	 *
	 * @param null|string $asset
	 * @return null|string
	 */
	public function asset( $asset = null );

	/**
	/**
	 * Gets or sets the privacy target user (owner)
	 *
	 * @param null|UserTable|int $user
	 * @return UserTable|int|null
	 */
	public function user( $user = null );

	/**
	 * Returns the available privacy rules
	 *
	 * @return array
	 */
	public function rules();

	/**
	 * Clears the data cache
	 *
	 * @return self
	 */
	public function clear();

	/**
	 * Resets the privacy filters
	 *
	 * @return self
	 */
	public function reset();

	/**
	 * Retrieves privacy rows or row count
	 *
	 * @param string $output
	 * @return PrivacyTable[]|int
	 */
	public function rows( $output = null );

	/**
	 * Retrieves privacy row
	 *
	 * @param int $id
	 * @return PrivacyTable
	 */
	public function row( $id );

	/**
	 * Checks if supplied user is authorized to view this privacy row
	 *
	 * @param null|int|UserTable $user
	 * @return bool
	 */
	public function authorized( $user = null );

	/**
	 * Outputs privacy HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function privacy( $view = null );

	/**
	 * Caches the privacy into session; this is normally only done on creation or parse to preserve parameters between loads
	 * It is not advised to call this manually unless privacy parameters have changed after creation and desired result is for them to persist
	 *
	 * @return self
	 */
	public function cache();
}