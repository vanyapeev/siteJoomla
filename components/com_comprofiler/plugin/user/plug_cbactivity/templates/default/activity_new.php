<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;
use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Activity;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Tags;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityActivityNew
{

	/**
	 * @param UserTable       $viewer
	 * @param Activity        $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 * @return string
	 */
	static public function showActivityNew( $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$canModerate			=	CBActivity::canModerate( $stream );
		$messageLimit			=	( $canModerate ? 0 : $stream->get( 'message_limit', 400, GetterInterface::INT ) );
		$showActions			=	$stream->get( 'actions', true, GetterInterface::BOOLEAN );
		$actionLimit			=	( $canModerate ? 0 : $stream->get( 'actions_message_limit', 100, GetterInterface::INT ) );
		$showLocations			=	$stream->get( 'locations', true, GetterInterface::BOOLEAN );
		$locationLimit			=	( $canModerate ? 0 : $stream->get( 'locations_address_limit', 200, GetterInterface::INT ) );
		$showLinks				=	$stream->get( 'links', true, GetterInterface::BOOLEAN );
		$linkLimit				=	( $canModerate ? 0 : $stream->get( 'links_link_limit', 5, GetterInterface::INT ) );
		$showTags				=	$stream->get( 'tags', true, GetterInterface::BOOLEAN );
		$collapsed				=	$stream->get( 'collapsed', true, GetterInterface::BOOLEAN );

		$actionTooltip			=	cbTooltip( null, CBTxt::T( 'What are you doing or feeling?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$locationTooltip		=	cbTooltip( null, CBTxt::T( 'Share your location.' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$findLocationTooltip	=	cbTooltip( null, CBTxt::T( 'Click to try and find your location.' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$tagTooltip				=	cbTooltip( null, CBTxt::T( 'Are you with anyone?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$linkTooltip			=	cbTooltip( null, CBTxt::T( 'Have a link to share?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$globalTooltip			=	cbTooltip( null, CBTxt::T( 'Is this a global announcement?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$actionOptions			=	( $showActions ? CBActivity::loadActionOptions( false, $stream ) : array() );
		$locationOptions		=	( $showLocations ? CBActivity::loadLocationOptions( false, $stream ) : array() );

		$rowId					=	md5( $stream->id() . '_new' );
		$buttons				=	array( 'left' => array(), 'right' => array() );

		$return					=	'<div class="streamItem streamPanel activityContainer activityContainerNew panel panel-default streamItem' . $rowId . '">'
								.		'<div class="streamItemInner">'
								.			'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'save', 'stream' => $stream->id() ), 'raw', 0, true ) . '" method="post" enctype="multipart/form-data" name="streamItemForm' . $rowId . '" class="cb_form streamItemForm form">'
								.				'<div class="streamItemNew">'
								.					'<textarea name="message" rows="' . ( $collapsed ? '1' : '3' ) . '" class="streamInput streamInputAutosize streamInputMessage' . ( $collapsed ? ' streamInputMessageCollapse' : null ) . ' form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What's on your mind?" ) ) . '"' . ( $collapsed ? ' data-cbactivity-input-size="3"' : null ) . ( $messageLimit ? ' data-cbactivity-input-limit="' . (int) $messageLimit . '" maxlength="' . (int) $messageLimit . '"' : null ) . '></textarea>';

		if ( $showLinks ) {
			if ( ( ! $linkLimit ) || ( $linkLimit > 1 ) ) {
				$return			.=					'<div class="streamItemInputGroup streamInputLinkContainer cbRepeat border-default clearfix hidden" data-cbrepeat-sortable="false"' . ( $linkLimit ? ' data-cbrepeat-max="' . (int) $linkLimit . '"' : null ) . '>'
								.						'<div class="streamItemInputGroupRow cbRepeatRow border-default">'
								.							'<span class="streamItemInputGroupLabel form-control">'
								.								'<button type="button" class="cbRepeatRowAdd btn btn-xs btn-success"><span class="fa fa-plus "></span></button>'
								.								'<button type="button" class="cbRepeatRowRemove btn btn-xs btn-danger"><span class="fa fa-minus"></span></button>'
								.							'</span>'
								.							'<div class="streamItemInputGroupInput border-default">'
								.								'<input name="links[0][url]" class="streamInput streamInputLinkURL form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What link would you like to share?" ) ) . '" disabled="disabled" />'
								.							'</div>'
								.						'</div>'
								.					'</div>';
			} else {
				$return			.=					'<div class="streamItemInputGroup streamInputLinkContainer border-default clearfix hidden">'
								.						'<input type="text" name="links[0][url]" class="streamInput streamInputLinkURL form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What link would you like to share?" ) ) . '" disabled="disabled" />'
								.					'</div>';
			}
		}

		if ( $actionOptions ) {
			$emoteOptions		=	CBActivity::loadEmoteOptions();

			$return				.=					'<div class="streamItemInputGroup streamInputActionContainer border-default clearfix hidden">'
								.						'<span class="streamItemInputGroupLabel streamInputSelectToggleLabel form-control"></span>'
								.						'<div class="streamItemInputGroupInput border-default">'
								.							'<input type="text" name="actions[message]" class="streamInput streamInputActionMessage streamInputSelectTogglePlaceholder form-control no-border"' . ( $actionLimit ? ' maxlength="' . (int) $actionLimit . '"' : null ) . ' disabled="disabled" />'
								.							( $emoteOptions ? str_replace( 'actions__emote', md5( $stream->id() . '_actions_emote_new_' . rand() ), moscomprofilerHTML::selectList( $emoteOptions, 'actions[emote]', 'class="streamInputSelect streamInputEmote" data-cbselect-width="auto" data-cbselect-height="100%" data-cbselect-dropdown-css-class="streamEmoteOptions" disabled="disabled"', 'value', 'text', null, 0, false, false, false ) ) : null )
								.						'</div>'
								.					'</div>';
		}

		if ( $locationOptions ) {
			$return				.=					'<div class="streamItemInputGroup streamInputLocationContainer border-default clearfix hidden">'
								.						'<span class="streamItemInputGroupLabel streamInputSelectToggleLabel form-control"></span>'
								.						'<div class="streamItemInputGroupInput border-default">'
								.							'<input type="text" name="location[place]" class="streamInput streamInputLocationPlace form-control no-border" placeholder="' . CBTxt::T( 'Where are you?' ) . '"' . ( $locationLimit ? ' maxlength="' . (int) $locationLimit . '"' : null ) . ' disabled="disabled" />';

			if ( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) ) {
				$return			.=							'<div class="streamFindLocation fa fa-map-marker fa-lg" data-cbactivity-location-target=".streamInputLocationAddress" data-cbactivity-location-error="' . htmlspecialchars( CBTxt::T( 'Sorry, unable to find your location.' ) ) . '"' . $findLocationTooltip . '></div>';
			}

			$return				.=							'<input type="text" name="location[address]" class="streamInput streamInputLocationAddress form-control no-border" placeholder="' . CBTxt::T( 'Have the address to share?' ) . '"' . ( $locationLimit ? ' maxlength="' . (int) $locationLimit . '"' : null ) . ' disabled="disabled" />'
								.						'</div>'
								.					'</div>';
		}

		if ( $showTags ) {
			$tagsStream			=	new Tags( 'activity.0', $viewer->get( 'user_id', 0, GetterInterface::INT ) );

			$tagsStream->set( 'query', false );
			$tagsStream->set( 'moderators', $stream->get( 'moderators', array(), GetterInterface::RAW ) );
			$tagsStream->set( 'inline', true );
			$tagsStream->set( 'placeholder', CBTxt::T( 'Who are you with?' ) );

			$return				.=					'<div class="streamItemInputGroup streamInputTagContainer border-default clearfix hidden">'
								.						$tagsStream->tags( 'edit' )
								.					'</div>';
		}

		$menu					=	array();

		if ( $actionOptions ) {
			$menu[]				=	str_replace( 'actions__id', md5( $stream->id() . '_actions_id_new_' . rand() ), moscomprofilerHTML::selectList( $actionOptions, 'actions[id]', 'class="streamInputSelect streamInputSelectToggle streamInputAction btn btn-xs btn-default" data-cbactivity-toggle-target=".streamInputActionContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default" data-cbactivity-toggle-icon="fa fa-smile-o" data-cbselect-dropdown-css-class="streamSelectOptions"' . $actionTooltip, 'value', 'text', null, 0, false, false, false ) );
		}

		if ( $locationOptions ) {
			$menu[]				=	str_replace( 'location__id', md5( $stream->id() . '_location_id_new_' . rand() ), moscomprofilerHTML::selectList( $locationOptions, 'location[id]', 'class="streamInputSelect streamInputSelectToggle streamInputLocation btn btn-xs btn-default" data-cbactivity-toggle-target=".streamInputLocationContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default" data-cbactivity-toggle-icon="fa fa-map-marker" data-cbselect-dropdown-css-class="streamSelectOptions"' . $locationTooltip, 'value', 'text', null, 0, false, false, false ) );
		}

		if ( $showTags ) {
			$menu[]				=	'<button type="button" class="streamToggle streamInputTag btn btn-default btn-xs" data-cbactivity-toggle-target=".streamInputTagContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default"' . $tagTooltip . '><span class="fa fa-user"></span></button>';
		}

		if ( $showLinks ) {
			$menu[]				=	'<button type="button" class="streamToggle streamInputLink btn btn-default btn-xs" data-cbactivity-toggle-target=".streamInputLinkContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default"' . $linkTooltip . '><span class="fa fa-link"></span></button>';
		}

		$return					.=					implode( '', $_PLUGINS->trigger( 'activity_onDisplayStreamActivityNew', array( &$buttons, $viewer, $stream, $output ) ) )
								.				'</div>'
								.				'<div class="streamPanelFooter streamItemDisplay activityContainerFooter panel-footer' . ( $collapsed ? ' hidden' : null ) . '">'
								.					'<div class="activityContainerFooterRow clearfix">'
								.						( $menu || $buttons['left'] ? '<div class="activityContainerFooterRowLeft pull-left">' . ( $menu ? implode( ' ', $menu ) : null ) . ( $buttons['left'] ? ( $menu ? ' ' : null ) . implode( ' ', $buttons['left'] ) : null ) . '</div>' : null )
								.						'<div class="activityContainerFooterRowRight pull-right text-right">';

		if ( $messageLimit ) {
			$return				.=							'<div class="streamInputMessageLimit text-small">'
								.								'<span class="streamInputMessageLimitCurrent">0</span> / <span class="streamInputMessageLimitMax">' . (int) $messageLimit . '</span>'
								.							'</div> ';
		}

		$return					.=							( $buttons['right'] ? implode( ' ', $buttons['right'] ) . ' ' : null );

		if ( Application::MyUser()->isGlobalModerator() && ( in_array( 'all', $stream->assets() ) || in_array( 'global', $stream->assets() ) ) ) {
			$return				.=							'<span class="streamToggle streamInputCheckbox streamInputGlobal btn btn-default btn-xs" data-cbactivity-toggle-active-classes="btn-info" data-cbactivity-toggle-inactive-classes="btn-default"' . $globalTooltip . '>'
								.								'<input type="checkbox" name="global" class="hidden" /><span class="fa fa-bullhorn"></span>'
								.							'</span> ';
		}

		$return					.=							'<button type="submit" class="activityButton activityButtonNewSave streamItemNewSave btn btn-primary btn-xs">' . CBTxt::T( 'Post' ) . '</button>'
								.							( $collapsed ? ' <button type="button" class="activityButton activityButtonNewCancel streamItemNewCancel btn btn-default btn-xs">' . CBTxt::T( 'Cancel' ) . '</button>' : null )
								.						'</div>'
								.					'</div>'
								.				'</div>'
								.			'</form>'
								.		'</div>'
								.	'</div>';

		return $return;
	}
}