<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveVideo\Trigger;

use CBLib\Language\CBTxt;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveVideo\CBGroupJiveVideo;

defined('CBLIB') or die();

class RouterTrigger extends \cbPluginHandler
{

	/**
	 * @param \ComprofilerRouter $router
	 * @param string             $plugin
	 * @param array              $segments
	 * @param array              $query
	 * @param \JMenuSite         $menuItem
	 */
	public function build( $router, $plugin, &$segments, &$query, &$menuItem )
	{
		if ( ( $plugin != 'cbgroupjivevideo' ) || ( ! $query ) || ( ! isset( $query['action'] ) ) || ( $query['action'] != 'video' ) ) {
			return;
		}

		unset( $query['action'] );

		$group						=	null;
		$video						=	null;

		if ( isset( $query['group'] ) ) {
			$group					=	CBGroupJive::getGroup( $query['group'] );

			unset( $query['group'] );
		} elseif ( isset( $query['id'] ) ) {
			$video					=	CBGroupJiveVideo::getVideo( $query['id'] );
			$group					=	$video->group();

			unset( $query['id'] );
		}

		if ( $group ) {
			$groupId				=	$group->get( 'id', 0, GetterInterface::INT );
			$segments[]				=	$groupId . '-' . Application::Router()->stringToAlias( CBTxt::T( $group->get( 'name', $groupId, GetterInterface::STRING ) ) );

			if ( $video ) {
				$videoId			=	$video->get( 'id', 0, GetterInterface::INT );
				$segments[]			=	$videoId . '-' . Application::Router()->stringToAlias( ( $video->get( 'title' ) ? $video->get( 'title', $videoId, GetterInterface::STRING ) : $video->name() ) );
			}

			if ( isset( $query['func'] ) ) {
				$segments[]			=	$query['func'];

				unset( $query['func'] );
			}
		} else {
			if ( isset( $query['func'] ) ) {
				$segments[]			=	$query['func'];

				unset( $query['func'] );

				if ( isset( $query['id'] ) ) {
					$segments[]		=	$query['id'];

					unset( $query['id'] );
				}
			}
		}
	}

	/**
	 * @param \ComprofilerRouter $router
	 * @param string             $plugin
	 * @param array              $segments
	 * @param array              $vars
	 * @param \JMenuSite         $menuItem
	 */
	public function parse( $router, $plugin, $segments, &$vars, $menuItem )
	{
		if ( ( $plugin != 'cbgroupjivevideo' ) || ( ! $segments ) ) {
			return;
		}

		$count									=	count( $segments );
		$groupId								=	( isset( $segments[0] ) ? preg_replace( '/-/', ':', $segments[0], 1 ) : null );

		if ( strpos( $groupId, ':' ) !== false ) {
			list( $groupId, $groupAlias )		=	explode( ':', $groupId, 2 );
		} else {
			$groupAlias							=	null;
		}

		$vars['action']							=	'video';

		if ( is_numeric( $groupId ) ) {
			$videoId							=	( isset( $segments[1] ) ? preg_replace( '/-/', ':', $segments[1], 1 ) : null );

			if ( strpos( $videoId, ':' ) !== false ) {
				list( $videoId, $videoAlias )	=	explode( ':', $videoId, 2 );
			} else {
				$videoAlias						=	null;
			}

			if ( is_numeric( $videoId ) ) {
				if ( $count > 2 ) {
					$vars['func']				=	$segments[2];
				}

				$vars['id']						=	$videoId;
			} else {
				$vars['group']					=	$groupId;
				$vars['func']					=	$segments[1];
			}
		} elseif ( $count > 0 ) {
			$vars['func']						=	$segments[0];

			if ( $count > 1 ) {
				$vars['id']						=	$segments[1];
			}
		}
	}
}