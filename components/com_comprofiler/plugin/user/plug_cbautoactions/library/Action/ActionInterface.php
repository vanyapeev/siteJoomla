<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Action;

use CB\Database\Table\UserTable;
use CB\Plugin\AutoActions\Table\AutoActionTable;

defined('CBLIB') or die();

interface ActionInterface
{
	
	/**
	 * Triggers the action checking access and conditions then returning its results
	 *
	 * @param UserTable $user
	 * @return mixed
	 */
	public function trigger( $user );

	/**
	 * Executes the action directly and returns its results
	 *
	 * @param UserTable $user
	 * @return mixed
	 */
	public function execute( $user );

	/**
	 * Gets the auto action associated with this action
	 *
	 * @return AutoActionTable
	 */
	public function autoaction();

	/**
	 * Gets or sets substitution variables for this action
	 *
	 * @param null|array $substitutions
	 * @return array
	 */
	public function substitutions( $substitutions = null );

	/**
	 * Gets or sets action variables
	 *
	 * @param null|array $variables
	 * @return array
	 */
	public function variables( $variables = null );

	/**
	 * Parses a string through action substitutions
	 *
	 * @param null|UserTable  $user
	 * @param string          $string
	 * @param bool            $htmlspecialchars
	 * @param null|array|bool $translate
	 * @param null|bool       $format
	 * @param null|bool       $prepare
	 * @param null|bool       $substitutions
	 * @return string
	 */
	public function string( $user, $string, $htmlspecialchars = true, $translate = null, $format = null, $prepare = null, $substitutions = null );

	/**
	 * Checks if the actions dependency is installed
	 *
	 * @return bool
	 */
	public function installed();

	/**
	 * Gets or sets action errors
	 *
	 * @param string $error
	 * @return array
	 */
	public function error( $error = null );
}