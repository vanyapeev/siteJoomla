<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveEvents\Table\EventTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Notifications;
use CB\Plugin\Activity\NotificationsInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveEventActivity
{

	/**
	 * render frontend event activity
	 *
	 * @param ActivityTable|NotificationTable $row
	 * @param null|string                     $title
	 * @param null|string                     $date
	 * @param null|string                     $message
	 * @param null|string                     $insert
	 * @param null|string                     $footer
	 * @param array                           $menu
	 * @param Activity|Notifications          $stream
	 * @param array                           $assetMatches
	 * @param EventTable                      $event
	 * @param cbPluginHandler                 $plugin
	 * @param string                          $output
	 */
	static function showEventActivity( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $event, $plugin, $output )
	{
		global $_CB_framework, $_PLUGINS;

		$user						=	CBuser::getMyUserDataInstance();
		$eventTitle					=	htmlspecialchars( $event->get( 'title', null, GetterInterface::STRING ) );
		$groupName					=	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => $event->group()->get( 'id', 0, GetterInterface::INT ) ) ) . '">' . htmlspecialchars( CBTxt::T( $event->group()->get( 'name', null, GetterInterface::STRING ) ) ) . '</a>';

		if ( $stream instanceof NotificationsInterface ) {
			if ( cbutf8_strlen( $eventTitle ) > 50 ) {
				$eventTitle			=	trim( cbutf8_substr( $eventTitle, 0, 50 ) ) . '...';
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) == $event->group()->get( 'user_id', 0, GetterInterface::INT ) ) {
				$title				=	CBTxt::T( 'SCHEDULED_EVENT_IN_YOUR_GROUP', 'scheduled event [event] in your group [group]', array( '[event]' => '<strong>' . $eventTitle . '</strong>', '[group]' => $groupName ) );
			} else {
				$title				=	CBTxt::T( 'SCHEDULED_EVENT_IN_GROUP', 'scheduled event [event] in group [group]', array( '[event]' => '<strong>' . $eventTitle . '</strong>', '[group]' => $groupName ) );
			}
		} else {
			$userStatus				=	CBGroupJive::getGroupStatus( $user, $event->group() );
			$eventOwner				=	( $user->get( 'id', 0, GetterInterface::INT ) == $event->get( 'user_id', 0, GetterInterface::INT ) );
			$showAddress			=	$plugin->params->get( 'groups_events_address', 1, GetterInterface::INT );
			$address				=	htmlspecialchars( $event->get( 'location', null, GetterInterface::STRING ) );

			if ( $stream->get( 'groupjive.ingroup', 0, GetterInterface::INT ) == $event->group()->get( 'id', 0, GetterInterface::INT ) ) {
				$title				=	CBTxt::T( 'scheduled an event' );
			} else {
				$title				=	CBTxt::T( 'SCHEDULED_AN_EVENT_IN_GROUP', 'scheduled an event in group [group]', array( '[group]' => '<strong>' . $groupName . '</strong>' ) );
			}

			if ( $showAddress ) {
				if ( $event->get( 'address', null, GetterInterface::STRING ) ) {
					$mapUrl			=	CBTxt::T( 'GROUP_EVENT_ADDRESS_MAP_URL', 'https://www.google.com/maps/place/[address]', array( '[location]' => urlencode( $event->get( 'location', null, GetterInterface::STRING ) ), '[address]' => urlencode( $event->get( 'address', null, GetterInterface::STRING ) ) ) );
				} else {
					$mapUrl			=	CBTxt::T( 'GROUP_EVENT_LOCATION_MAP_URL', 'https://www.google.com/maps/search/[location]', array( '[location]' => urlencode( $event->get( 'location', null, GetterInterface::STRING ) ), '[address]' => urlencode( $event->get( 'address', null, GetterInterface::STRING ) ) ) );
				}

				if ( $mapUrl ) {
					$address		=	'<a href="' . htmlspecialchars( $mapUrl ) . '" target="_blank" rel="nofollow">' . $address . '</a>';
				}
			}

			$canAttend				=	( ( ! $eventOwner ) && ( $event->status() != 1 ) && ( ! $event->get( '_attending', 0, GetterInterface::INT ) ) && ( $userStatus >= 1 ) && ( ( ! $event->get( 'limit', 0, GetterInterface::INT ) ) || ( $event->get( 'limit', 0, GetterInterface::INT ) && ( $event->get( '_guests', 0, GetterInterface::INT ) < $event->get( 'limit', 0, GetterInterface::INT ) ) ) ) );

			$insert					=	'<div class="gjEventActivity">'
									.		'<div class="gjGroupEventsRow row' . ( $event->status() == 1 ? ' gjGroupEventExpired' : ( $event->status() == 2 ? ' gjGroupEventActive' : null ) ) . '">'
									.			'<div class="gjGroupEventCalendar col-md-2 hidden-sm hidden-xs">'
									.				'<div class="panel panel-default text-center">'
									.					'<div class="gjGroupEventMonth panel-body">' . cbFormatDate( $event->get( 'start', null, GetterInterface::STRING ), true, false, 'M' ) . '</div>'
									.					'<div class="gjGroupEventDay panel-footer">' . cbFormatDate( $event->get( 'start', null, GetterInterface::STRING ), true, false, 'j' ) . '</div>'
									.				'</div>'
									.			'</div>'
									.			'<div class="gjGroupEventContainer col-md-10 col-sm-12 col-xs-12">'
									.				'<div class="panel ' . ( $event->status() == 1 ? 'panel-warning' : ( $event->status() == 2 ? 'panel-primary' : 'panel-default' ) ) . '">'
									.					'<div class="gjGroupEventHeader panel-heading">'
									.						'<div class="row">'
									.							'<div class="gjGroupEventTitle ' . ( $canAttend ? 'col-sm-8' : 'col-sm-12' ) . '">' . $eventTitle . '</div>';

			if ( $canAttend ) {
				$insert				.=							'<div class="gjGroupEventMenu col-sm-4 text-right">'
									.								'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'attend', 'id' => $event->get( 'id', 0, GetterInterface::INT ) ) ) . '\';" class="gjButton gjButtonAttend btn btn-xs btn-success">' . CBTxt::T( 'Attend' ) . '</button>'
									.							'</div>';
			}

			$insert					.=						'</div>'
									.					'</div>'
									.					'<div class="gjGroupEventDetails panel-body small">';

			if ( $event->status() == 1 ) {
				$insert				.=						'<div class="gjGroupEventNotice text-warning text-right">' . CBTxt::T( 'This event has ended.' ) . '</div>';
			} elseif ( $event->status() == 2 ) {
				if ( $event->get( 'end', null, GetterInterface::STRING ) ) {
					$insert			.=						'<div class="gjGroupEventNotice text-primary text-right">' . CBTxt::T( 'GROUP_EVENT_ENDS_IN', 'This event is currently in progress and ends in [timeago].', array( '[timeago]' => cbFormatDate( $event->get( 'end', null, GetterInterface::STRING ), true, 'exacttimeago' ) ) ) . '</div>';
				} else {
					$insert			.=						'<div class="gjGroupEventNotice text-primary text-right">' . CBTxt::T( 'This event is currently in progress.' ) . '</div>';
				}
			} else {
				$insert				.=						'<div class="gjGroupEventNotice text-right">' . CBTxt::T( 'GROUP_EVENT_STARTS_IN', 'This event starts in [timeago].', array( '[timeago]' => cbFormatDate( $event->get( 'start', null, GetterInterface::STRING ), true, 'exacttimeago' ) ) ) . '</div>';
			}

			$insert					.=						'<div class="gjGroupEventDate">'
									.							'<span class="gjGroupEventIcon fa fa-clock-o text-center"></span> ' . $event->date()
									.						'</div>'
									.						'<div class="gjGroupEventLocation">'
									.							'<span class="gjGroupEventIcon fa fa-map-marker text-center"></span> ' . $address
									.						'</div>'
									.						'<div class="gjGroupEventAttending">'
									.							'<div class="gjGroupEventGuests">'
									.								'<span class="gjGroupEventIcon fa fa-users text-center"></span> '
									.								'<a href="' . htmlspecialchars( $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'events', 'func' => 'attending', 'id' =>  $event->get( 'id', 0, GetterInterface::INT ), 'return' => CBGroupJive::getReturn() ) ) ) . '">'
									.									( $event->get( 'limit', 0, GetterInterface::INT ) ? CBTxt::T( 'GROUP_GUESTS_COUNT_LIMITED', '%%COUNT%% of [limit] Guest|%%COUNT%% of [limit] Guests', array( '%%COUNT%%' => $event->get( '_guests', 0, GetterInterface::INT ), '[limit]' => $event->get( 'limit', 0, GetterInterface::INT ) ) ) : CBTxt::T( 'GROUP_GUESTS_COUNT', '%%COUNT%% Guest|%%COUNT%% Guests', array( '%%COUNT%%' => $event->get( '_guests', 0, GetterInterface::INT ) ) ) )
									.								'</a>'
									.							'</div>'
									.						'</div>'
									.					'</div>'
									.					'<div class="gjGroupEventDescription panel-footer">'
									.						'<div class="cbMoreLess">'
									.							'<div class="cbMoreLessContent">'
									.								( $plugin->params->get( 'groups_events_content_plugins', false, GetterInterface::BOOLEAN ) ? Application::Cms()->prepareHtmlContentPlugins( $event->get( 'event', null, GetterInterface::HTML ), 'groupjive.event', $event->get( 'user_id', 0, GetterInterface::INT ) ) : $event->get( 'event', null, GetterInterface::HTML ) )
									.							'</div>'
									.							'<div class="cbMoreLessOpen fade-edge hidden">'
									.								'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
									.							'</div>'
									.						'</div>'
									.					'</div>'
									.				'</div>'
									.			'</div>'
									.		'</div>'
									.	'</div>';
		}

		$_PLUGINS->trigger( 'gj_onAfterEventActivity', array( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, $stream, $assetMatches, $event, $plugin, $output ) );
	}
}