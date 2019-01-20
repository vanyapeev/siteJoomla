<?php
/**
* Community Builder (TM) cbconnect French (France) language file Frontend
* @version $Id:$
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

/**
* WARNING:
* Do not make changes to this file as it will be over-written when you upgrade CB.
* To localize you need to create your own CB language plugin and make changes there.
*/

defined('CBLIB') or die();

return	array(
// 128 language strings from file plug_cbconnect/cbconnect.xml
'ENABLE_OR_DISABLE_ACCOUNT_LINKING_IN_PROFILE_EDIT__afe4a2'	=>	'Enable or disable account linking in profile edit. Note linking for the provider must also be enabled.',
'LINKING_4f0929'	=>	'Lié',
'ENABLE_OR_DISABLE_THIS_PROVIDER_THIS_ALLOWS_QUICK__e09ec3'	=>	'Enable or disable this provider. This allows quick enable or disable without having to clear parameters.',
'INPUT_APPLICATION_ID_OR_APPLICATION_KEY_2c2860'	=>	'Input application id or application key.',
'APP_ID_81ee9e'	=>	'App ID',
'INPUT_APPLICATION_SECRET_ac0f26'	=>	'Input application secret.',
'APP_SECRET_61be04'	=>	'App Secrete',
'OPTIONALLY_SELECT_ADDITIONAL_PERMISSIONS_TO_REQUES_102a4f'	=>	'Optionally select additional permissions to request. Note the necessary permissions for login and registration are already requested. Do not request additional permissions unless absolutely needed.',
'PERMISSIONS_d08ccf'	=>	'Autorisations',
'SELECT_PERMISSIONS_721073'	=>	'Choisir les authorisations',
'THE_PROVIDER_CALLBACK_URL_THIS_URL_SHOULD_BE_SUPPL_71c608'	=>	'The provider callback url. This url should be supplied to the provider configuration as needed. This is sometimes also known as the redirect url. Note not all applications require this and may only require the domain.',
'CALLBACK_URL_971aa9'	=>	'URL de retour',
'ENABLE_OR_DISABLE_DEBUGGING_OF_THIS_PROVIDER_THIS__9e0efb'	=>	'Enable or disable debugging of this provider. This will output a var_dump of provider http request responses (during authentication and API calls).',
'DEBUG_a60390'	=>	'Débogage',
'ENABLE_OR_DISABLE_ACCOUNT_REGISTRATION_REGISTER_AL_9767ca'	=>	'Enable or disable account registration. Register allows for non-existing Community Builder users to register with their provider account credentials.',
'REGISTER_0ba758'	=>	'S\'inscrire',
'SELECT_HOW_REGISTRATION_BUTTON_IS_DISPLAYED_b21e54'	=>	'Choisir comment le bouton d\'inscription est affiché.',
'BUTTON_STYLE_190f98'	=>	'Style de bouton',
'SELECT_IF_USERS_SHOULD_BE_REGISTERED_IMMEDIATELY_W_823dfb'	=>	'Sélectionnez si les utilisateurs doivent être enregistrés immédiatement avec seulement la connexion ou si le formulaire d\'inscription CB devrait être pré- rempli avec les données du profil du réseau social. Remarque : pour le mode pré-rempli ce réseau social doit être configuré pour s\'afficher sur l\'enregistrement .',
'OPTIONALLY_INPUT_SUBSTITUTION_SUPPORTED_USERNAME_F_619999'	=>	'Saisir éventuellement une substitution pour la surcharge du format du nom d\'utilisateur',
'USERNAME_FORMAT_08bb89'	=>	'Format du nom d\'utilisateur',
'THE_ADDITIONAL_SUPPORTED_SUBSTITUTIONS_FOR_USERNAM_7dcc39'	=>	'Les substitutions additionnelles supportées pour le format du nom d\'utilisateur.',
'USERNAME_SUBSTITUTIONS_380e8d'	=>	'Substitution du nom d\'utilisateur',
'OPTIONALLY_SELECT_REGISTRATION_USERGROUPS_OF_USERS_19373c'	=>	'Optionally select registration usergroups of users.',
'USERGROUPS_6ad0aa'	=>	'Groupe d\'utilisateurs',
'SELECT_REGISTRATION_TO_REQUIRE_ADMIN_APPROVAL_807f79'	=>	'Select registration to require admin approval.',
'SELECT_REGISTRATION_TO_REQUIRE_EMAIL_CONFIRMATION_1e7a18'	=>	'Select registration to require email confirmation.',
'CONFIRMATION_f4d1ea'	=>	'Confirmation',
'SELECT_AVATAR_TO_REQUIRE_ADMIN_APPROVAL_7215ef'	=>	'Select avatar to require admin approval.',
'AVATAR_APPROVAL_30980b'	=>	'Avatar Approval',
'SELECT_CANVAS_TO_REQUIRE_ADMIN_APPROVAL_887bbf'	=>	'Select canvas to require admin approval.',
'CANVAS_APPROVAL_18e724'	=>	'Canvas Approval',
'FIELDS_a4ca5e'	=>	'Champs',
'SELECT_A_FIELD_TO_SYNCHRONIZE_FROM_ON_REGISTRATION_f0034f'	=>	'Select a field to synchronize from on registration. Note field value format is not guaranteed and may require additional permissions. Values are provided as is.',
'FROM_FIELD_b7c383'	=>	'Du champ',
'SELECT_PROVIDER_FIELD_fa0518'	=>	'- Select Provider Field -',
'SELECT_A_FIELD_TO_SYNCHRONIZE_TO_ON_REGISTRATION_N_2ec5a1'	=>	'Select a field to synchronize to on registration. Note the core username, name, first name, middle name, last name, avatar, canvas, and email are already synchronized.',
'TO_FIELD_eeb0a6'	=>	'Au champ',
'ENABLE_OR_DISABLE_ACCOUNT_LINKING_LINKING_ALLOWS_E_c9b748'	=>	'Enable or disable account linking. Linking allows existing Community Builder users while logged in to link their provider account to their existing Community Builder account.',
'SELECT_HOW_THE_LINK_BUTTON_IS_DISPLAYED_5b29a3'	=>	'Select how the link button is displayed.',
'ENABLE_OR_DISABLE_RESYNCHRONIZING_OF_PROVIDER_PROF_da4ff2'	=>	'Enable or disable resynchronizing of provider profile data on link.',
'RESYNCHRONIZE_1b0f89'	=>	'Resynchronize',
'SELECT_HOW_THE_LOGIN_BUTTON_IS_DISPLAYED_f7ced5'	=>	'Select how the login button is displayed.',
'INPUT_OPTIONAL_FIRST_TIME_LOGIN_REDIRECT_URL_EG_IN_372d5b'	=>	'Input optional first time login redirect URL (e.g. index.php?option=com_comprofiler).',
'FIRST_REDIRECT_2cc28b'	=>	'Première redirection',
'INPUT_OPTIONAL_LOGIN_REDIRECT_URL_EG_INDEXPHPOPTIO_a16181'	=>	'Input optional login redirect URL (e.g. index.php?option=com_comprofiler).',
'REDIRECT_4202ef'	=>	'Redirection',
'ENABLE_OR_DISABLE_RESYNCHRONIZING_OF_PROVIDER_PROF_a7617c'	=>	'Enable or disable resynchronizing of provider profile data on every successful login.',
'INPUT_APPLICATION_CONSUMER_KEY_191627'	=>	'Input application consumer key.',
'CONSUMER_KEY_ce7a07'	=>	'Clé de l\'usager',
'INPUT_APPLICATION_CONSUMER_SECRET_5fe0d8'	=>	'Input application consumer secret.',
'CONSUMER_SECRET_2ce81a'	=>	'Usager secret',
'SELECT_HOW_THE_REGISTRATION_BUTTON_IS_DISPLAYED_10fb21'	=>	'Select how the registration button is displayed.',
'INPUT_APPLICATION_CLIENT_ID_81b124'	=>	'Input application client id.',
'CLIENT_ID_76525f'	=>	'ID du client',
'INPUT_APPLICATION_CLIENT_SECRET_3dea79'	=>	'Input application client secret.',
'CLIENT_SECRET_734082'	=>	'Client secret',
'INPUT_APPLICATION_ID_a1250d'	=>	'Input application id.',
'APPLICATION_ID_e500e9'	=>	'Application ID',
'INPUT_APPLICATION_SECRET_KEY_98c733'	=>	'Input application secret key.',
'SELECT_HOW_THIS_REGISTRATION_BUTTON_IS_DISPLAYED_f61498'	=>	'Select how this registration button is displayed.',
'ENABLE_OR_DISABLE_THIS_THIS_ALLOWS_QUICK_ENABLE_OR_f0a61b'	=>	'Enable or disable this. This allows quick enable or disable without having to clear parameters.',
'INPUT_WEB_API_KEY_26de4a'	=>	'Input web api key.',
'API_KEY_d876ff'	=>	'Clé API',
'INPUT_APPLICATION_KEY_0d07cc'	=>	'Input application key.',
'KEY_897356'	=>	'Clé',
'ENABLE_OR_DISABLE_SANDBOX_USAGE_SANDBOX_URL_ENDPOI_4ff8cb'	=>	'Enable or disable sandbox usage. Sandbox URL endpoints are different from live. If testing with sandbox credentials this must be enabled.',
'SANDBOX_2652ee'	=>	'Sandbox',
'INPUT_APPLICATION_API_KEY_258382'	=>	'Input application api key.',
'API_ID_17d66f'	=>	'API ID',
'INPUT_APPLICATION_API_SECRET_e15606'	=>	'Input application api secret.',
'API_SECRET_1ddec0'	=>	'API Secret',
'APPLICATION_KEY_b54d8e'	=>	'Application Key',
'APPLICATION_SECRET_392844'	=>	'Application Secret',
'INPUT_APPLICATION_CHANNEL_ID_2b6783'	=>	'Input application channel id.',
'CHANNEL_ID_36b283'	=>	'ID Canal',
'INPUT_APPLICATION_CHANNEL_SECRET_0cdb2e'	=>	'Input application channel secret.',
'CHANNEL_SECRET_1f2f39'	=>	'Channel Secret',
'YOUR_FACEBOOK_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZE_c1c65b'	=>	'Votre ID Facebook permet les appels API; si cela n\'est pas autorisé, seulement les appels publics seront validés.',
'FACEBOOK_ID_4336c5'	=>	'ID Facebook',
'YOUR_TWITTER_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_21c14d'	=>	'Votre ID Twitter permet les appels API; si cela n\'est pas autorisé, seulement les appels publics seront validés.',
'TWITTER_ID_b80446'	=>	'ID Twitter',
'YOUR_GOOGLE_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__8d7e0a'	=>	'Votre ID Google permet les appels API; si cela n\'est pas autorisé, seulement les appels publics seront validés.',
'GOOGLE_ID_81d3e3'	=>	'ID Google',
'YOUR_LINKEDIN_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZE_06e793'	=>	'Votre ID LinkedIn permet les appels API; si cela n\'est pas autorisé, seulement les appels publics seront validés.',
'LINKEDIN_ID_1a58fc'	=>	'ID LinkedIn',
'YOUR_WINDOWS_LIVE_ID_ALLOWING_API_CALLS_IF_UNAUTHO_d6d424'	=>	'Votre ID Windows Live permet les appels API; si cela n\'est pas autorisé, seulement les appels publics seront validés.',
'WINDOWS_LIVE_ID_8b94b8'	=>	'ID Windows Live',
'YOUR_INSTAGRAM_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_c8826b'	=>	'Votre ID Instagram permet les appels API; si cela n\'est pas autorisé, seulement les appels publics seront validés.',
'INSTAGRAM_ID_c3820e'	=>	'ID Instagram',
'YOUR_FOURSQUARE_ID_ALLOWING_API_CALLS_IF_UNAUTHORI_178cb0'	=>	'Votre ID Foursquare permet les appels API; si cela n\'est pas autorisé, seulement les appels publics seront validés.',
'FOURSQUARE_ID_b0f614'	=>	'ID Foursquare',
'YOUR_GITHUB_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__4c4eba'	=>	'Votre ID GitHub permet les appels API; si cela n\'est pas autorisé, seulement les appels publics seront validés.',
'GITHUB_ID_2b7b73'	=>	'ID GitHub',
'YOUR_VKONTAKTE_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_757fdb'	=>	'Votre ID VKontakte permet les appels API; si cela n\'est pas autorisé, seulement les appels publics seront validés.',
'VKONTAKTE_ID_a9b255'	=>	'ID VKontakte',
'YOUR_STEAM_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_O_d8e5a6'	=>	'Votre ID Steam permet les appels API; si cela n\'est pas autorisé, seulement les appels publics seront validés.',
'STEAM_ID_00d10b'	=>	'ID Steam',
'YOUR_REDDIT_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__4b32d6'	=>	'Your Reddit ID allowing API calls; if unauthorized only public calls will validate.',
'REDDIT_ID_811839'	=>	'Reddit ID',
'YOUR_TWITCH_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__6b312b'	=>	'Your Twitch ID allowing API calls; if unauthorized only public calls will validate.',
'TWITCH_ID_9e5063'	=>	'Twitch ID',
'YOUR_STACK_EXCHANGE_ID_ALLOWING_API_CALLS_IF_UNAUT_480ac2'	=>	'Your Stack Exchange ID allowing API calls; if unauthorized only public calls will validate.',
'STACK_EXCHANGE_ID_c1859a'	=>	'Stack Exchange ID',
'YOUR_PINTEREST_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_f4af23'	=>	'Your Pinterest ID allowing API calls; if unauthorized only public calls will validate.',
'PINTEREST_ID_cffb54'	=>	'Pinterest ID',
'YOUR_AMAZON_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__a3a895'	=>	'Your Amazon ID allowing API calls; if unauthorized only public calls will validate.',
'AMAZON_ID_d5694c'	=>	'ID Amazon',
'YOUR_YAHOO_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_O_0f4702'	=>	'Your Yahoo ID allowing API calls; if unauthorized only public calls will validate.',
'YAHOO_ID_59fc7f'	=>	'ID Yahoo',
'YOUR_PAYPAL_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__bacd9d'	=>	'Your PayPal ID allowing API calls; if unauthorized only public calls will validate.',
'PAYPAL_ID_4a3287'	=>	'ID PayPal',
'YOUR_DISQUS_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__2954fe'	=>	'Your Disqus ID allowing API calls; if unauthorized only public calls will validate.',
'DISQUS_ID_614174'	=>	'Disqus ID',
'YOUR_WORDPRESS_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_4776cc'	=>	'Your WordPress ID allowing API calls; if unauthorized only public calls will validate.',
'WORDPRESS_ID_9d3a7b'	=>	'WordPress ID',
'YOUR_MEETUP_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__29b49f'	=>	'Your Meetup ID allowing API calls; if unauthorized only public calls will validate.',
'MEETUP_ID_629f66'	=>	'Meetup ID',
'YOUR_FLICKR_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__c8ac13'	=>	'Your Flickr ID allowing API calls; if unauthorized only public calls will validate.',
'FLICKR_ID_db6c44'	=>	'Flickr ID',
'YOUR_VIMEO_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_O_b1b214'	=>	'Your Vimeo ID allowing API calls; if unauthorized only public calls will validate.',
'VIMEO_ID_402a2f'	=>	'Vimeo ID',
'YOUR_LINE_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_ON_24635e'	=>	'Your LINE ID allowing API calls; if unauthorized only public calls will validate.',
'LINE_ID_4e297d'	=>	'LINE ID',
'YOUR_SPOTIFY_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_3fc980'	=>	'Your Spotify ID allowing API calls; if unauthorized only public calls will validate.',
'SPOTIFY_ID_8d0088'	=>	'Spotify ID',
'YOUR_SOUNDCLOUD_ID_ALLOWING_API_CALLS_IF_UNAUTHORI_e05b5e'	=>	'Your SoundCloud ID allowing API calls; if unauthorized only public calls will validate.',
'SOUNDCLOUD_ID_c23d9d'	=>	'SoundCloud ID',
// 14 language strings from file plug_cbconnect/component.cbconnect.php
'PROVIDER_NOT_AVAILABLE'	=>	'[provider] n\'est pas disponible.',
'PROVIDER_FAILED_TO_AUTHENTICATE'	=>	'[provider] failed to authenticate. Error: [error]',
'PROVIDER_PROFILE_MISSING'	=>	'[provider] le profil ne peut être trouvé.',
'LINKING_FOR_PROVIDER_NOT_PERMITTED'	=>	'La jonction avec [provider] n\'est pas permise.',
'PROVIDER_ALREADY_LINKED'	=>	'[provider] le compte est déjà relié à un autre utilisateur.',
'PROVIDER_FAILED_TO_LINK'	=>	'Le lien du compte [provider] ne fonctionne pas. Erreur: [error]',
'PROVIDER_LINKED_SUCCESSFULLY'	=>	'[provider] le compte lié avec succès!',
'ALREADY_LINKED_TO_PROVIDER'	=>	'Vous êtes déjà lié à un compte [provider].',
'SIGN_UP_WITH_PROVIDER_NOT_PERMITTED'	=>	'S\'inscrire avec [provider] n\'est pas permis.',
'USERNAME_IN_USE_LOGIN_LINK'	=>	'The username \'[username]\' is already in use. Please login to link your account.',
'EMAIL_IN_USE_LOGIN_LINK'	=>	'The email \'[email]\' is already in use. Please login to link your account.',
'PROVIDER_SIGN_UP_INCOMPLETE'	=>	'[provider] n\'a pas retourné toutes les informations nécessaires. Merci de compléter.',
'SIGN_UP_WITH_PROVIDER_FAILED'	=>	'S\'inscrire avec [provider] raté.  Erreur  : [error]',
'SIGN_UP_INCOMPLETE_c37e7a'	=>	'Inscription incomplète',
// 21 language strings from file plug_cbconnect/library/CBConnect.php
'GOOGLE_8b36e9'	=>	'Google',
'WINDOWS_LIVE_37160d'	=>	'Windows Live',
'INSTAGRAM_55f015'	=>	'Instagram',
'FOURSQUARE_938a83'	=>	'Foursquare',
'GITHUB_d3b7c9'	=>	'GitHub',
'VKONTAKTE_c39fa7'	=>	'VKontakte',
'STEAM_4db456'	=>	'Steam',
'REDDIT_b632c5'	=>	'Reddit',
'TWITCH_6a057e'	=>	'Twitch',
'STACK_EXCHANGE_28d479'	=>	'Stack Exchange',
'PINTEREST_86709a'	=>	'Pinterest',
'AMAZON_b3b3a6'	=>	'Amazon',
'YAHOO_1334b6'	=>	'Yahoo',
'PAYPAL_ad69e7'	=>	'PayPal',
'DISQUS_2df055'	=>	'Disqus',
'WORDPRESS_fde316'	=>	'WordPress',
'MEETUP_b30887'	=>	'Meetup',
'VIMEO_15db59'	=>	'Vimeo',
'LINE_17b8ef'	=>	'LINE',
'SPOTIFY_fd539c'	=>	'Spotify',
'SOUNDCLOUD_88b992'	=>	'SoundCloud',
// 6 language strings from file plug_cbconnect/library/Connect.php
'LINK_YOUR_PROVIDER_ACCOUNT'	=>	'Link your [provider] account',
'LINK_WITH_PROVIDER'	=>	'Link with [provider]',
'SIGN_UP_WITH_PROVIDER'	=>	'Sign up with [provider]',
'SIGN_UP_WITH_YOUR_PROVIDER_ACCOUNT'	=>	'Sign up with your [provider] account',
'SIGN_IN_WITH_PROVIDER'	=>	'Sign in with [provider]',
'SIGN_IN_WITH_YOUR_PROVIDER_ACCOUNT'	=>	'Sign in with your [provider] account',
// 5 language strings from file plug_cbconnect/library/Field/SocialField.php
'UNLINK_PROVIDER_ACCOUNT'	=>	'Désactiver le lien avec votre compte [provider]',
'PROVIDER_PROFILE'	=>	'Profil [provider]',
'PROVIDER_PROFILE_ID'	=>	'[provider] Identifiant [provider_id]',
'PROVIDER_PROFILE_LINKED_TO_ACCOUNT'	=>	'Votre profil [provider_profile] sera lié à votre compte.',
'VIEW_PROVIDER_PROFILE'	=>	'Voir le profil [provider]',
// 3 language strings from file plug_cbconnect/library/Provider/AmazonProvider.php
'FAILED_EXCHANGE_CODE_ERROR'	=>	'Failed to exchange code. Error: [error]',
'FAILED_TO_RETRIEVE_ACCESS_TOKEN_275f75'	=>	'Failed to retrieve access token.',
'FAILED_API_REQUEST_ERROR'	=>	'Failed API request [api]. Error: [error]',
// 4 language strings from file plug_cbconnect/library/Provider/FlickrProvider.php
'FAILED_EXCHANGE_TOKEN_ERROR'	=>	'Failed to exchange token. Error: [error]',
'FAILED_REQUEST_TOKEN_ERROR'	=>	'Failed to request token. Error: [error]',
'CALLBACK_FAILED_TO_CONFIRM_bb8d55'	=>	'Callback failed to confirm.',
'FAILED_TO_REQUEST_CALLBACK_2a0309'	=>	'Failed to request callback.',
// 1 language strings from file plug_cbconnect/library/Provider/SteamProvider.php
'FAILED_TO_AUTHENTICATE_IDENTITY_d02393'	=>	'Failed to authenticate identity.',
);
