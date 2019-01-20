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
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityActivityEdit
{

	/**
	 * @param ActivityTable   $row
	 * @param UserTable       $viewer
	 * @param Activity        $stream
	 * @param cbPluginHandler $plugin
	 * @param string          $output
	 * @return string
	 */
	static public function showActivityEdit( $row, $viewer, $stream, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$canModerate				=	CBActivity::canModerate( $stream );
		$rowId						=	md5( $stream->id() . '_edit_' . $row->get( 'id', 0, GetterInterface::INT ) );
		$buttons					=	array( 'left' => array(), 'right' => array() );

		$integrations				=	$_PLUGINS->trigger( 'activity_onDisplayStreamActivityEdit', array( &$row, &$buttons, $viewer, $stream, $output ) );

		$messageLimit				=	( $canModerate ? 0 : $stream->get( 'message_limit', 400, GetterInterface::INT ) );
		$showActions				=	CBActivity::findParamOverrde( $row, 'actions', true, $stream );
		$actionLimit				=	( $canModerate ? 0 : $stream->get( 'actions_message_limit', 100, GetterInterface::INT ) );
		$showLocations				=	CBActivity::findParamOverrde( $row, 'locations', true, $stream );
		$locationLimit				=	( $canModerate ? 0 : $stream->get( 'locations_address_limit', 200, GetterInterface::INT ) );
		$showLinks					=	CBActivity::findParamOverrde( $row, 'links', true, $stream );
		$linkLimit					=	( $canModerate ? 0 : $stream->get( 'links_link_limit', 5, GetterInterface::INT ) );
		$showTags					=	CBActivity::findParamOverrde( $row, 'tags', true, $stream );

		$actionTooltip				=	cbTooltip( null, CBTxt::T( 'What are you doing or feeling?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$locationTooltip			=	cbTooltip( null, CBTxt::T( 'Share your location.' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$findLocationTooltip		=	cbTooltip( null, CBTxt::T( 'Click to try and find your location.' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$tagTooltip					=	cbTooltip( null, CBTxt::T( 'Are you with anyone?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$linkTooltip				=	cbTooltip( null, CBTxt::T( 'Have a link to share?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$globalTooltip				=	cbTooltip( null, CBTxt::T( 'Is this a global announcement?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$actionOptions				=	array();
		$locationOptions			=	array();

		$actionId					=	null;
		$locationId					=	null;
		$tags						=	null;
		$links						=	null;

		$return						=	'<div class="streamItemEdit activityContainerContent">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'activity', 'func' => 'save', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'stream' => $stream->id() ), 'raw', 0, true ) . '" method="post" enctype="multipart/form-data" name="streamItemForm' . $rowId . '" class="cb_form streamItemForm form">'
									.			'<textarea name="message" rows="3" class="streamInput streamInputAutosize streamInputMessage form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What's on your mind?" ) ) . '" data-cbactivity-input-size="3"' . ( $messageLimit ? ' data-cbactivity-input-limit="' . (int) $messageLimit . '" maxlength="' . (int) $messageLimit . '"' : null ) . '>' . htmlspecialchars( $row->get( 'message', null, GetterInterface::STRING ) ) . '</textarea>';

		if ( $showLinks ) {
			$links					=	$row->attachments();

			if ( ! $links->count() ) {
				$links				=	null;
			}

			if ( $links ) {
				$return				.=			'<div class="streamPanelBody streamItemInputGroup streamInputAttachmentsContainer border-default clearfix">'
									.				HTML_cbactivityStreamAttachments::showAttachments( $row, $viewer, $stream, $plugin, 'edit' )
									.			'</div>';
			}

			if ( ( ! $linkLimit ) || ( $linkLimit > 1 ) ) {
				$return				.=			'<div class="streamItemInputGroup streamInputLinkContainer cbRepeat border-default clearfix' . ( ! $links ? ' hidden' : null ) . '" data-cbrepeat-sortable="false"' . ( $linkLimit ? ' data-cbrepeat-max="' . (int) $linkLimit . '"' : null ) . '>';

				if ( $links ) {
					foreach ( $links as $i => $link ) {
						if ( $link->get( 'type', 'url', GetterInterface::STRING ) == 'custom' ) {
							continue;
						}

						$return		.=				'<div class="streamItemInputGroupRow cbRepeatRow border-default">'
									.					'<span class="streamItemInputGroupLabel form-control">'
									.						'<button type="button" class="cbRepeatRowAdd btn btn-xs btn-success"><span class="fa fa-plus "></span></button>'
									.						'<button type="button" class="cbRepeatRowRemove btn btn-xs btn-danger"><span class="fa fa-minus"></span></button>'
									.					'</span>'
									.					'<div class="streamItemInputGroupInput border-default">'
									.						'<input type="text" name="links[' . $i . '][url]" value="' . htmlspecialchars( $link->get( 'url', null, GetterInterface::STRING ) ) . '" class="streamInput streamInputLinkURL form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What link would you like to share?" ) ) . '" />'
									.					'</div>'
									.				'</div>';
					}
				} else {
					$return			.=				'<div class="streamItemInputGroupRow cbRepeatRow border-default">'
									.					'<span class="streamItemInputGroupLabel form-control">'
									.						'<button type="button" class="cbRepeatRowAdd btn btn-xs btn-success"><span class="fa fa-plus "></span></button>'
									.						'<button type="button" class="cbRepeatRowRemove btn btn-xs btn-danger"><span class="fa fa-minus"></span></button>'
									.					'</span>'
									.					'<div class="streamItemInputGroupInput border-default">'
									.						'<input type="text" name="links[0][url]" class="streamInput streamInputLinkURL form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What link would you like to share?" ) ) . '" disabled="disabled" />'
									.					'</div>'
									.				'</div>';
				}

				$return				.=			'</div>';
			} else {
				$return				.=			'<div class="streamItemInputGroup streamInputLinkContainer border-default clearfix' . ( ! $links ? ' hidden' : null ) . '">'
									.				'<input type="text" name="links[0][url]" value="' . htmlspecialchars( ( $links ? $links[0]['url'] : null ) ) . '" class="streamInput streamInputLinkURL form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What link would you like to share?" ) ) . '"' . ( ! $links ? ' disabled="disabled"' : null ) . ' />'
									.			'</div>';
			}
		}

		if ( $showActions ) {
			$action					=	$row->params()->subTree( 'action' );
			$actionId				=	$action->get( 'id', 0, GetterInterface::INT );
			$actionOptions			=	CBActivity::loadActionOptions( false, $stream, $actionId );

			if ( $actionOptions ) {
				$emoteOptions		=	CBActivity::loadEmoteOptions();

				$return				.=			'<div class="streamItemInputGroup streamInputActionContainer border-default clearfix' . ( ! $actionId ? ' hidden' : null ) . '">'
									.				'<span class="streamItemInputGroupLabel streamInputSelectToggleLabel form-control"></span>'
									.				'<div class="streamItemInputGroupInput border-default">'
									.					'<input type="text" name="actions[message]" value="' . htmlspecialchars( $action->get( 'message' ) ) . '" class="streamInput streamInputActionMessage streamInputSelectTogglePlaceholder form-control no-border"' . ( $actionLimit ? ' maxlength="' . (int) $actionLimit . '"' : null ) . ( ! $actionId ? ' disabled="disabled"' : null ) . ' />'
									.					( $emoteOptions ? str_replace( 'action__emote', md5( $stream->id() . '_actions_emote_edit_' . $row->get( 'id', 0, GetterInterface::INT ) ), moscomprofilerHTML::selectList( $emoteOptions, 'actions[emote]', 'class="streamInputSelect streamInputEmote" data-cbselect-width="auto" data-cbselect-height="100%" data-cbselect-dropdown-css-class="streamEmoteOptions"' . ( ! $actionId ? ' disabled="disabled"' : null ), 'value', 'text', $action->get( 'emote' ), 0, false, false, false ) ) : null )
									.				'</div>'
									.			'</div>';
			}
		}

		if ( $showLocations ) {
			$location				=	$row->params()->subTree( 'location' );
			$locationId				=	$location->get( 'id', 0, GetterInterface::INT );
			$locationOptions		=	CBActivity::loadLocationOptions( false, $stream, $locationId );

			if ( $locationOptions ) {
				$return				.=			'<div class="streamItemInputGroup streamInputLocationContainer border-default clearfix' . ( ! $locationId ? ' hidden' : null ) . '">'
									.				'<span class="streamItemInputGroupLabel streamInputSelectToggleLabel form-control"></span>'
									.				'<div class="streamItemInputGroupInput border-default">'
									.					'<input type="text" name="location[place]" value="' . htmlspecialchars( $location->get( 'place' ) ) . '" class="streamInput streamInputLocationPlace form-control no-border" placeholder="' . CBTxt::T( 'Where are you?' ) . '"' . ( $locationLimit ? ' maxlength="' . (int) $locationLimit . '"' : null ) . ( ! $locationId ? ' disabled="disabled"' : null ) . ' />';

				if ( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) ) {
					$return			.=					'<div class="streamFindLocation fa fa-map-marker fa-lg" data-cbactivity-location-target=".streamInputLocationAddress" data-cbactivity-location-error="' . htmlspecialchars( CBTxt::T( 'Sorry, unable to find your location.' ) ) . '"' . $findLocationTooltip . '></div>';
				}

				$return				.=					'<input type="text" name="location[address]" value="' . htmlspecialchars( $location->get( 'address' ) ) . '" class="streamInput streamInputLocationAddress form-control no-border" placeholder="' . CBTxt::T( 'Have the address to share?' ) . '"' . ( $locationLimit ? ' maxlength="' . (int) $locationLimit . '"' : null ) . ( ! $locationId ? ' disabled="disabled"' : null ) . ' />'
									.				'</div>'
									.			'</div>';
			}
		}

		if ( $showTags ) {
			$tagsStream				=	$row->tags( $stream );
			$tags					=	$tagsStream->rows( 'count' );

			$return					.=			'<div class="streamItemInputGroup streamInputTagContainer border-default clearfix' . ( ! $tags ? ' hidden' : null ) . '">'
									.				$tagsStream->tags( 'edit' )
									.			'</div>';
		}

		$menu						=	array();

		if ( $actionOptions ) {
			$menu[]					=	str_replace( 'actions__id', md5( $stream->id() . '_actions_id_edit_' . $row->get( 'id', 0, GetterInterface::INT ) . '_' . rand() ), moscomprofilerHTML::selectList( $actionOptions, 'actions[id]', 'class="streamInputSelect streamInputSelectToggle streamInputAction btn btn-xs ' . ( $actionId ? 'btn-primary' : 'btn-default' ) . '" data-cbactivity-toggle-target=".streamInputActionContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default" data-cbactivity-toggle-icon="fa fa-smile-o" data-cbselect-dropdown-css-class="streamSelectOptions"' . $actionTooltip, 'value', 'text', $actionId, 0, false, false, false ) );
		}

		if ( $locationOptions ) {
			$menu[]					=	str_replace( 'location__id', md5( $stream->id() . '_location_id_edit_' . $row->get( 'id', 0, GetterInterface::INT ) . '_' . rand() ), moscomprofilerHTML::selectList( $locationOptions, 'location[id]', 'class="streamInputSelect streamInputSelectToggle streamInputLocation btn btn-xs ' . ( $locationId ? 'btn-primary' : 'btn-default' ) . '" data-cbactivity-toggle-target=".streamInputLocationContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default" data-cbactivity-toggle-icon="fa fa-map-marker" data-cbselect-dropdown-css-class="streamSelectOptions"' . $locationTooltip, 'value', 'text', $locationId, 0, false, false, false ) );
		}

		if ( $showTags ) {
			$menu[]					=	'<button type="button" class="streamToggle streamInputTag btn btn-xs' . ( $tags ? ' btn-primary streamToggleOpen' : ' btn-default' ) . '" data-cbactivity-toggle-target=".streamInputTagContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default"' . $tagTooltip . '><span class="fa fa-user"></span></button>';
		}

		if ( $showLinks ) {
			$menu[]					=	'<button type="button" class="streamToggle streamInputLink btn btn-xs' . ( $links ? ' btn-primary streamToggleOpen' : ' btn-default' ) . '" data-cbactivity-toggle-target=".streamInputLinkContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default"' . $linkTooltip . '><span class="fa fa-link"></span></button>';
		}

		$return						.=			implode( '', $integrations )
									.			'<div class="streamPanelFooter activityContainerFooter panel-footer">'
									.				'<div class="activityContainerFooterRow clearfix">'
									.					( $menu || $buttons['left'] ? '<div class="activityContainerFooterRowLeft pull-left">' . ( $menu ? implode( ' ', $menu ) : null ) . ( $buttons['left'] ? ( $menu ? ' ' : null ) . implode( ' ', $buttons['left'] ) : null ) . '</div>' : null )
									.					'<div class="activityContainerFooterRowRight pull-right text-right">';

		if ( $messageLimit ) {
			$return					.=						'<div class="streamInputMessageLimit text-small">'
									.							'<span class="streamInputMessageLimitCurrent">' . cbutf8_strlen( $row->get( 'message', null, GetterInterface::STRING ) ) . '</span> / <span class="streamInputMessageLimitMax">' . (int) $messageLimit . '</span>'
									.						'</div> ';
		}

		$return						.=						( $buttons['right'] ? implode( ' ', $buttons['right'] ) . ' ' : null );

		if ( Application::MyUser()->isGlobalModerator() && ( in_array( 'all', $stream->assets() ) || in_array( 'global', $stream->assets() ) ) ) {
			$rowGlobal				=	( $row->get( 'asset', null, GetterInterface::STRING ) == 'global' );

			$return					.=						'<span class="streamToggle streamInputCheckbox streamInputGlobal btn btn-xs' . ( $rowGlobal ? ' btn-info streamToggleOpen' : ' btn-default' ) . '" data-cbactivity-toggle-active-classes="btn-info" data-cbactivity-toggle-inactive-classes="btn-default"' . $globalTooltip . '>'
									.							'<input type="checkbox" name="global" value="1" class="hidden"' . ( $rowGlobal ? ' checked' : null ) . ' /><span class="fa fa-bullhorn"></span>'
									.						'</span> ';
		}

		$return						.=						'<button type="submit" class="activityButton activityButtonEditSave streamItemEditSave btn btn-primary btn-xs">' . CBTxt::T( 'Done Editing' ) . '</button>'
									.						' <button type="button" class="activityButton activityButtonEditCancel streamItemEditCancel streamItemActionResponsesRevert btn btn-default btn-xs">' . CBTxt::T( 'Cancel' ) . '</button>'
									.					'</div>'
									.				'</div>'
									.			'</div>'
									.		'</form>'
									.		CBActivity::reloadHeaders()
									.	'</div>';

		return $return;
	}
}