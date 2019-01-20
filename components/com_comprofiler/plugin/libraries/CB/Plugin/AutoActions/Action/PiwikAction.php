<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Action;

use CB\Database\Table\UserTable;
use CB\Plugin\AutoActions\CBAutoActions;
use CBLib\Application\Application;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class PiwikAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $_CB_framework;

		$userAgent							=	Application::Input()->getNamespaceRegistry( 'server' )->get( 'HTTP_USER_AGENT', null, GetterInterface::STRING );
		$userLanguage						=	$user->getUserLanguage();

		if ( ! $userLanguage ) {
			$userLanguage					=	Application::Cms()->getLanguageTag();
		}

		$ipAddresses						=	cbGetIParray();
		$ipAddress							=	null;

		if ( $ipAddresses ) {
			$ipAddress						=	trim( array_shift( $ipAddresses ) );
		}

		foreach ( $this->autoaction()->params()->subTree( 'piwik' ) as $row ) {
			/** @var ParamsInterface $row */
			$installation					=	$this->string( $user, $row->get( 'installation', null, GetterInterface::STRING ), ( preg_match( '/^\[[a-zA-Z0-9-_]+\]$/', $row->get( 'installation', null, GetterInterface::STRING ) ) ? false : array( '\CB\Plugin\AutoActions\CBAutoActions', 'escapeURL' ) ) );

			if ( ! $installation ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_PIWIK_NO_INSTALLATION', ':: Action [action] :: Tracking skipped due to missing installation url', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			if ( strpos( $installation, '/' ) === 0 ) {
				$installation				=	$_CB_framework->getCfg( 'live_site' ) . $installation;
			}

			$installation					=	$installation . '/piwik.php';
			$site							=	$this->string( $user, $row->get( 'site', null, GetterInterface::STRING ) );

			if ( ! $site ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_PIWIK_NO_SITE', ':: Action [action] :: Tracking skipped due to missing site id', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			$url							=	$this->string( $user, $row->get( 'url', null, GetterInterface::STRING ), ( preg_match( '/^\[[a-zA-Z0-9-_]+\]$/', $row->get( 'url', null, GetterInterface::STRING ) ) ? false : array( '\CB\Plugin\AutoActions\CBAutoActions', 'escapeURL' ) ) );

			if ( ! $url ) {
				$url						=	CBAutoActions::getCurrentURL();
			}

			if ( ! $url ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_PIWIK_NO_URL', ':: Action [action] :: Tracking skipped due to missing url', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			$token							=	$this->string( $user, $row->get( 'token', null, GetterInterface::STRING ) );
			$client							=	new \GuzzleHttp\Client();

			try {
				$uid						=	$this->string( $user, $row->get( 'user', null, GetterInterface::STRING ) );

				if ( ! $uid ) {
					$uid					=	md5( $user->get( 'id', 0, GetterInterface::INT ) . $_CB_framework->getCfg( 'live_site' ) );
				}

				$body						=	array( 'idsite' => $site, 'rec' => 1, 'url' => $url, 'uid' => $uid );

				$visitor					=	$this->string( $user, $row->get( 'visitor', null, GetterInterface::STRING ) );

				if ( $visitor ) {
					$body['_id']			=	$visitor;
				}

				$action						=	$this->string( $user, $row->get( 'action.name', null, GetterInterface::STRING ) );

				if ( $action ) {
					$body['action_name']	=	$action;
				}

				$link						=	$this->string( $user, $row->get( 'action.link', null, GetterInterface::STRING ), ( preg_match( '/^\[[a-zA-Z0-9-_]+\]$/', $row->get( 'action.link', null, GetterInterface::STRING ) ) ? false : array( '\CB\Plugin\AutoActions\CBAutoActions', 'escapeURL' ) ) );

				if ( $link ) {
					$body['link']			=	str_replace( '[current_url]', CBAutoActions::getCurrentURL(), $link );
				}

				$download					=	$this->string( $user, $row->get( 'action.download', null, GetterInterface::STRING ), ( preg_match( '/^\[[a-zA-Z0-9-_]+\]$/', $row->get( 'action.download', null, GetterInterface::STRING ) ) ? false : array( '\CB\Plugin\AutoActions\CBAutoActions', 'escapeURL' ) ) );

				if ( $download ) {
					$body['download']		=	str_replace( '[current_url]', CBAutoActions::getCurrentURL(), $download );
				}

				$campaign					=	$this->string( $user, $row->get( 'campaign.name', null, GetterInterface::STRING ) );

				if ( $campaign ) {
					$body['_rcn']			=	$campaign;
				}

				$campaignKeyword			=	$this->string( $user, $row->get( 'campaign.keyword', null, GetterInterface::STRING ) );

				if ( $campaignKeyword ) {
					$body['_rck']			=	$campaignKeyword;
				}

				$eventCategory				=	$this->string( $user, $row->get( 'event.category', null, GetterInterface::STRING ) );
				$eventAction				=	$this->string( $user, $row->get( 'event.action', null, GetterInterface::STRING ) );

				if ( $eventCategory && $eventAction ) {
					$body['e_c']			=	$eventCategory;
					$body['e_a']			=	$eventAction;

					$eventName				=	$this->string( $user, $row->get( 'event.name', null, GetterInterface::STRING ) );

					if ( $eventName ) {
						$body['e_n']		=	$eventName;
					}

					$eventValue				=	$this->string( $user, $row->get( 'event.value', null, GetterInterface::STRING ) );

					if ( $eventValue ) {
						$body['e_v']		=	$eventValue;
					}
				}

				$contentName				=	$this->string( $user, $row->get( 'content.name', null, GetterInterface::STRING ) );

				if ( $contentName ) {
					$body['c_n']			=	$contentName;
				}

				$contentPiece				=	$this->string( $user, $row->get( 'content.piece', null, GetterInterface::STRING ) );

				if ( $contentPiece ) {
					$body['c_p']			=	$contentPiece;
				}

				$contentTarget				=	$this->string( $user, $row->get( 'content.target', null, GetterInterface::STRING ) );

				if ( $contentTarget ) {
					$body['c_t']			=	$contentTarget;
				}

				$contentInteraction			=	$this->string( $user, $row->get( 'content.interaction', null, GetterInterface::STRING ) );

				if ( $contentInteraction ) {
					$body['c_i']			=	$contentInteraction;
				}

				$ecommerceOrder				=	$this->string( $user, $row->get( 'ecommerce.order', null, GetterInterface::STRING ) );

				if ( $ecommerceOrder ) {
					$body['idgoal']			=	0;
					$body['ec_id']			=	$ecommerceOrder;

					$ecommerceTax			=	$this->string( $user, $row->get( 'ecommerce.tax', null, GetterInterface::STRING ) );

					if ( $ecommerceTax ) {
						$body['ec_tx']		=	$ecommerceTax;
					}

					$ecommerceShipping		=	$this->string( $user, $row->get( 'ecommerce.shipping', null, GetterInterface::STRING ) );

					if ( $ecommerceShipping ) {
						$body['ec_sh']		=	$ecommerceShipping;
					}

					$ecommerceDiscount		=	$this->string( $user, $row->get( 'ecommerce.discount', null, GetterInterface::STRING ) );

					if ( $ecommerceDiscount ) {
						$body['ec_dt']		=	$ecommerceDiscount;
					}

					$ecommerceSubtotal		=	$this->string( $user, $row->get( 'ecommerce.subtotal', null, GetterInterface::STRING ) );

					if ( $ecommerceSubtotal ) {
						$body['ec_st']		=	$ecommerceSubtotal;
					}

					$ecommerceRevenue		=	$this->string( $user, $row->get( 'ecommerce.revenue', null, GetterInterface::STRING ) );

					if ( $ecommerceRevenue ) {
						$body['revenue']	=	$ecommerceRevenue;
					}

					$items					=	array();
					$in						=	1;

					foreach ( $row->subTree( 'ecommerce.items' ) as $item ) {
						/** @var ParamsInterface $item */
						$sku				=	$item->get( 'sku', null, GetterInterface::STRING );

						if ( $sku ) {
							$items[$in]		=	array(	$sku,
														$this->string( $user, $item->get( 'name', null, GetterInterface::STRING ) ),
														$this->string( $user, $item->get( 'category', null, GetterInterface::STRING ) ),
														$this->string( $user, $item->get( 'price', null, GetterInterface::STRING ) ),
														$this->string( $user, $item->get( 'quantity', null, GetterInterface::STRING ) )
													);

							$in++;
						}
					}

					if ( $items ) {
						$body['ec_items']	=	json_encode( $items );
					}
				} else {
					$goal					=	$this->string( $user, $row->get( 'goal', null, GetterInterface::STRING ) );

					if ( $goal ) {
						$body['idgoal']		=	$goal;
					}
				}

				$custom						=	array();
				$cn							=	1;

				foreach ( $row->subTree( 'custom' ) as $customVariable ) {
					/** @var ParamsInterface $customVariable */
					$key					=	$customVariable->get( 'key', null, GetterInterface::STRING );

					if ( $key ) {
						$custom[$cn]		=	array( $key, $this->string( $user, $customVariable->get( 'value', null, GetterInterface::STRING ) ) );

						$cn++;
					}
				}

				if ( $custom ) {
					$body['_cvar']			=	json_encode( $custom );
				}

				$agent						=	$this->string( $user, $row->get( 'location.useragent', null, GetterInterface::STRING ) );

				if ( ! $agent ) {
					$agent					=	$userAgent;
				}

				if ( $agent ) {
					$body['ua']				=	$agent;
				}

				$language					=	$this->string( $user, $row->get( 'location.language', null, GetterInterface::STRING ) );

				if ( ! $language ) {
					$language				=	$userLanguage;
				}

				if ( $agent ) {
					$body['lang']			=	$language;
				}

				if ( $token ) {
					$ip						=	$this->string( $user, $row->get( 'location.ipaddress', null, GetterInterface::STRING ) );

					if ( ! $ip ) {
						$ip					=	$ipAddress;
					}

					if ( $ip ) {
						$body['cip']		=	$ip;
					}

					$country				=	cbutf8_strtolower( $this->string( $user, $row->get( 'location.country', null, GetterInterface::STRING ) ) );

					if ( $country ) {
						$body['country']	=	$country;
					}

					$region					=	cbutf8_strtoupper( $this->string( $user, $row->get( 'location.region', null, GetterInterface::STRING ) ) );

					if ( $region ) {
						$body['region']		=	$region;
					}

					$city					=	$this->string( $user, $row->get( 'location.city', null, GetterInterface::STRING ) );

					if ( $city ) {
						$body['city']		=	$city;
					}

					$latitude				=	$this->string( $user, $row->get( 'location.latitude', null, GetterInterface::STRING ) );

					if ( $latitude ) {
						$body['lat']		=	$latitude;
					}

					$longitude				=	$this->string( $user, $row->get( 'location.longitude', null, GetterInterface::STRING ) );

					if ( $longitude ) {
						$body['long']		=	$longitude;
					}

					$body['token_auth']		=	$token;
				}

				$result						=	$client->get( $installation, array( 'query' => $body ) );

				if ( $result->getStatusCode() != 200 ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_PIWIK_FAILED', ':: Action [action] :: Tracking failed. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $result->getStatusCode() ) ) );
					continue;
				}
			} catch ( \Exception $e ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_PIWIK_FAILED', ':: Action [action] :: Tracking failed. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $e->getMessage() ) ) );
				continue;
			}
		}

		return null;
	}
}