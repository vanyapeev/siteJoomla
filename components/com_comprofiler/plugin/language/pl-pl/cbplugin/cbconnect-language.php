<?php
/**
* Community Builder (TM) cbconnect Polish (Poland) language file Frontend
* @version $Id:$
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

/**
* WARNING:
* Do not make changes to this file as it will be over-written when you upgrade CB.
* To localize you need to create your own CB language plugin and make changes there.
*/

defined('CBLIB') or die();

return	array(
// 5 language strings from file plug_cbconnect/cbconnect.php
'UNLINK_PROVIDER_ACCOUNT'	=>	'Rozłącz powiązane konto [provider]',
'PROVIDER_PROFILE'	=>	'Profil [provider]',
'PROVIDER_PROFILE_ID'	=>	'ID profilu [provider_id] [provider]',
'PROVIDER_PROFILE_LINKED_TO_ACCOUNT'	=>	'Twój profil [provider_profile] zostanie powiązany z tym kontem.',
'VIEW_PROVIDER_PROFILE'	=>	'Wyświetl Profil [provider]',
// 111 language strings from file plug_cbconnect/cbconnect.xml
'ENABLE_OR_DISABLE_THIS_PROVIDER_THIS_ALLOWS_QUICK__e09ec3'	=>	'Włącz lub wyłącz tego dostawcę. To ustawienie pozwala na szybkie włączenie lub wyłączenie dostawcy, bez konieczności zmiany parametrów.',
'INPUT_APPLICATION_ID_OR_APPLICATION_KEY_2c2860'	=>	'Podaj identyfikator id aplikacji lub klucz aplikacji.',
'APP_ID_81ee9e'	=>	'App ID',
'INPUT_APPLICATION_SECRET_ac0f26'	=>	'Podaj poufny klucz aplikacji.',
'APP_SECRET_61be04'	=>	'Poufny Klucz App',
'OPTIONALLY_SELECT_ADDITIONAL_PERMISSIONS_TO_REQUES_102a4f'	=>	'Opcjonalnie wybierz dodatkowe uprawnienia, które będą wymagane. Uwaga - w tym momencie wymagane są uprawnienia do zalogowania się i rejestracji. Z opcji żądania dodatkowych uprawnień należy korzystać tylko, gdy jest to niezbędnie konieczne.',
'PERMISSIONS_d08ccf'	=>	'Uprawnienia',
'SELECT_PERMISSIONS_721073'	=>	'- Wybierz Uprawnienia -',
'THE_PROVIDER_CALLBACK_URL_THIS_URL_SHOULD_BE_SUPPL_71c608'	=>	'Adres URL odpowiedzi zwrotnej dostawcy. Niniejszy adres powinien zostać podany w ustawieniach danego dostawcy, zgodnie z jego wymaganiami. Inna nazwa to adres URL przekierowania. Uwaga - nie wszystkie aplikacje wymagają tego ustawienia i czasem wystarczające jest jedynie podanie domeny strony.',
'CALLBACK_URL_971aa9'	=>	'URL Wsteczy',
'ENABLE_OR_DISABLE_DEBUGGING_OF_THIS_PROVIDER_THIS__9e0efb'	=>	'Włącz lub wyłącz śledzenie błędów obsługi tego dostawcy. Opcja ta wyświetli var_dump odpowiedzi na zapytanie http dostawcy (podczas uwierzytelnienia i zapytań API).',
'DEBUG_a60390'	=>	'Debuguj',
'ENABLE_OR_DISABLE_ACCOUNT_REGISTRATION_REGISTER_AL_9767ca'	=>	'Włącz lub wyłącz rejestrację konta. Rejestracja pozwala osobom nie posiadającym konta na stronie na zarejestrowanie się za pośrednictwem danych logowania wybranego dostawcy.',
'REGISTER_0ba758'	=>	'Zarejestruj się',
'SELECT_HOW_REGISTRATION_BUTTON_IS_DISPLAYED_b21e54'	=>	'Wybierz sposób wyświetlania przycisku rejestracji.',
'BUTTON_STYLE_190f98'	=>	'Styl Przycisku',
'SELECT_IF_USERS_SHOULD_BE_REGISTERED_IMMEDIATELY_W_823dfb'	=>	'Wybierz, czy użytkownik ma być zarejestrowany podczas pojedyńczego logowania lub czy ma zostać wyświetlony wstępnie wypełniony formularz rejestracyjny strony. Uwaga - w trybie wypełniania formularza pole danego dostawcy powiny być wyświetlane podczas rejestracji.',
'OPTIONALLY_INPUT_SUBSTITUTION_SUPPORTED_USERNAME_F_619999'	=>	'Opcjonalnie nadpisz format nazwy użytkownika - użycie ziennych jest wspierane.',
'USERNAME_FORMAT_08bb89'	=>	'Format Nazwy Użytkownika',
'THE_ADDITIONAL_SUPPORTED_SUBSTITUTIONS_FOR_USERNAM_7dcc39'	=>	'Dodatkowe, wspierane zmienne w formacie nazwy użytkownika.',
'USERNAME_SUBSTITUTIONS_380e8d'	=>	'Zmienne Nazwy Użytkownika',
'OPTIONALLY_SELECT_REGISTRATION_USERGROUPS_OF_USERS_19373c'	=>	'Opcjonalnie wybierz grupy użytkowników przypisywane podczas rejestracji.',
'USERGROUPS_6ad0aa'	=>	'Grupy Użytkowników',
'SELECT_REGISTRATION_TO_REQUIRE_ADMIN_APPROVAL_807f79'	=>	'Wybierz rejestrację z wymaganym zatwierdzeniem przez Administratora.',
'SELECT_REGISTRATION_TO_REQUIRE_EMAIL_CONFIRMATION_1e7a18'	=>	'Wybierz rejestrację z wymaganym potwierdzeniem adresu email.',
'CONFIRMATION_f4d1ea'	=>	'Potwierdzenie',
'SELECT_AVATAR_TO_REQUIRE_ADMIN_APPROVAL_7215ef'	=>	'Wybierz opcję wymagania zatwierdzenia awatara przez Administratora.',
'AVATAR_APPROVAL_30980b'	=>	'Zatwierdzenie Awatara',
'SELECT_CANVAS_TO_REQUIRE_ADMIN_APPROVAL_887bbf'	=>	'Wybierz ',
'CANVAS_APPROVAL_18e724'	=>	'Zatwierdzenie Obrazu w Tle',
'FIELDS_a4ca5e'	=>	'Pola',
'SELECT_A_FIELD_TO_SYNCHRONIZE_FROM_ON_REGISTRATION_f0034f'	=>	'Wybierz pole, które będzie podstawą synchronizacji podczas rejestracji. Uwaga - format wartości pola nie jest gwarantowany i może wymagać dodatkowych uprawnień. Wartości są wstawiane bez modyfikacji.',
'FROM_FIELD_b7c383'	=>	'Pole Źródłowe',
'SELECT_PROVIDER_FIELD_fa0518'	=>	'- Wybierz Pole Dostawcy -',
'SELECT_A_FIELD_TO_SYNCHRONIZE_TO_ON_REGISTRATION_N_2ec5a1'	=>	'Wybierz pola, które mają być podstawą synchronizacji podczas rejestracji. Uwaga podstawowe pola: nazwa użytkownika, nazwa, pierwsze imię, drugie imię, nazwisko, awatar, obraz w tle oraz email są synchronizowane.',
'TO_FIELD_eeb0a6'	=>	'Pole Docelowe',
'ENABLE_OR_DISABLE_ACCOUNT_LINKING_LINKING_ALLOWS_E_c9b748'	=>	'Włącz lub wyłącz powiązania kont. Powiązanie kont pozwala istniejącym użytkownikom Community Builder na powiązanie swojego konta z danymi z innego portalu podczas logowania.',
'LINKING_4f0929'	=>	'Powiązanie',
'SELECT_HOW_THE_LINK_BUTTON_IS_DISPLAYED_5b29a3'	=>	'Wybierz sposób wyświetlania przycisku powiązania konta.',
'ENABLE_OR_DISABLE_RESYNCHRONIZING_OF_PROVIDER_PROF_da4ff2'	=>	'Włącz lub wyłącz ponowną synchronizację danych z powiązanego profilu dostawcy.',
'RESYNCHRONIZE_1b0f89'	=>	'Synchronizuj Ponownie',
'SELECT_HOW_THE_LOGIN_BUTTON_IS_DISPLAYED_f7ced5'	=>	'Wybierz sposób wyświetlania przycisku logowania.',
'INPUT_OPTIONAL_FIRST_TIME_LOGIN_REDIRECT_URL_EG_IN_372d5b'	=>	'Wprowadź opcjonalny adres URL wyświetlany po pierwszym zalogowaniu (np. index.php?option=com_comprofiler).',
'FIRST_REDIRECT_2cc28b'	=>	'Pierwsze Przekierowanie',
'INPUT_OPTIONAL_LOGIN_REDIRECT_URL_EG_INDEXPHPOPTIO_a16181'	=>	'Wprowadź opcjonalny adres URL przekierowania po zalogowaniu (np. index.php?option=com_comprofiler).',
'REDIRECT_4202ef'	=>	'Przekieruj',
'ENABLE_OR_DISABLE_RESYNCHRONIZING_OF_PROVIDER_PROF_a7617c'	=>	'Włącz lub wyłącz ponowną synchronizację danych z powiązanego profilu podczas każdego udanego logowania.',
'INPUT_APPLICATION_CONSUMER_KEY_191627'	=>	'Podaj klucz użytkownika aplikacji.',
'CONSUMER_KEY_ce7a07'	=>	'Klucz Użytkownika',
'INPUT_APPLICATION_CONSUMER_SECRET_5fe0d8'	=>	'Podaj poufny klucz użytkownika aplikacji.',
'CONSUMER_SECRET_2ce81a'	=>	'Poufny Klucz Użytkownika',
'SELECT_HOW_THE_REGISTRATION_BUTTON_IS_DISPLAYED_10fb21'	=>	'Wybierz sposób wyświetlania przycisku rejestracji.',
'INPUT_APPLICATION_CLIENT_ID_81b124'	=>	'Podaj ID klienta aplikacji.',
'CLIENT_ID_76525f'	=>	'ID Klienta',
'INPUT_APPLICATION_CLIENT_SECRET_3dea79'	=>	'Podaj poufny klucz aplikacji klienta.',
'CLIENT_SECRET_734082'	=>	'Poufny Klucz Klienta',
'INPUT_APPLICATION_ID_a1250d'	=>	'Podaj ID aplikacji',
'APPLICATION_ID_e500e9'	=>	'ID Aplikacji',
'INPUT_APPLICATION_SECRET_KEY_98c733'	=>	'Podaj poufny klucz aplikacji.',
'SELECT_HOW_THIS_REGISTRATION_BUTTON_IS_DISPLAYED_f61498'	=>	'Wybierz sposób wyświetlania przycisku rejestracji.',
'ENABLE_OR_DISABLE_THIS_THIS_ALLOWS_QUICK_ENABLE_OR_f0a61b'	=>	'Włącz lub wyłącz. Przełączenie pozwala na szybkie zmiany bez konieczności usuwania ustawionych parametrów.',
'INPUT_WEB_API_KEY_26de4a'	=>	'Podaj klucz aplikacji sieciowej',
'API_KEY_d876ff'	=>	'Klucz API',
'INPUT_APPLICATION_KEY_0d07cc'	=>	'Podaj klucz aplikacji',
'KEY_897356'	=>	'Klucz',
'ENABLE_OR_DISABLE_SANDBOX_USAGE_SANDBOX_URL_ENDPOI_4ff8cb'	=>	'Włącz lub wyłącz użycie piaskownicy - środowiska testowego. Adresy docelowe URL piaskownicy różnią się od adresów na działającej stronie. W przypadku testów z użyciem piaskownicy, konieczne jest włączenie tej opcji.',
'SANDBOX_2652ee'	=>	'Piaskownica - środowisko testowe',
'INPUT_APPLICATION_API_KEY_258382'	=>	'Podaj klucz api aplikacji.',
'API_ID_17d66f'	=>	'ID API',
'INPUT_APPLICATION_API_SECRET_e15606'	=>	'Podaj poufne klucz api aplikacji.',
'API_SECRET_1ddec0'	=>	'Poufny Klucz API',
'YOUR_FACEBOOK_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZE_c1c65b'	=>	'Twój identyfikator Facebook ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'FACEBOOK_ID_4336c5'	=>	'Facebook ID',
'YOUR_TWITTER_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_21c14d'	=>	'Twój identyfikator Twitter ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'TWITTER_ID_b80446'	=>	'Twitter ID',
'YOUR_GOOGLE_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__8d7e0a'	=>	'Twój identyfikator Google ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'GOOGLE_ID_81d3e3'	=>	'Google ID',
'YOUR_LINKEDIN_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZE_06e793'	=>	'Twój identyfikator Linkedin ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'LINKEDIN_ID_1a58fc'	=>	'LinkedIn ID',
'YOUR_WINDOWS_LIVE_ID_ALLOWING_API_CALLS_IF_UNAUTHO_d6d424'	=>	'Twój identyfikator Windows Live ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'WINDOWS_LIVE_ID_8b94b8'	=>	'Windows Live ID',
'YOUR_INSTAGRAM_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_c8826b'	=>	'Twój identyfikator Instagram ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'INSTAGRAM_ID_c3820e'	=>	'Instagram ID',
'YOUR_FOURSQUARE_ID_ALLOWING_API_CALLS_IF_UNAUTHORI_178cb0'	=>	'Twój identyfikator Foursquare ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'FOURSQUARE_ID_b0f614'	=>	'Foursquare ID',
'YOUR_GITHUB_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__4c4eba'	=>	'Twój identyfikator GitHub ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'GITHUB_ID_2b7b73'	=>	'GitHub ID',
'YOUR_VKONTAKTE_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_757fdb'	=>	'Twój identyfikator VKontakte ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'VKONTAKTE_ID_a9b255'	=>	'VKontakte ID',
'YOUR_STEAM_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_O_d8e5a6'	=>	'Twój identyfikator Steam ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'STEAM_ID_00d10b'	=>	'Steam ID',
'YOUR_REDDIT_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__4b32d6'	=>	'Twój identyfikator Reddit ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'REDDIT_ID_811839'	=>	'Reddit ID',
'YOUR_TWITCH_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__6b312b'	=>	'Twój identyfikator Twitch ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'TWITCH_ID_9e5063'	=>	'Twitch ID',
'YOUR_STACK_EXCHANGE_ID_ALLOWING_API_CALLS_IF_UNAUT_480ac2'	=>	'Twój identyfikator Stack Exchange ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'STACK_EXCHANGE_ID_c1859a'	=>	'Stack Exchange ID',
'YOUR_PINTEREST_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_f4af23'	=>	'Twój identyfikator Pinterest ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'PINTEREST_ID_cffb54'	=>	'Pinterest ID',
'YOUR_AMAZON_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__a3a895'	=>	'Twój identyfikator Amazon ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'AMAZON_ID_d5694c'	=>	'Amazon ID',
'YOUR_YAHOO_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_O_0f4702'	=>	'Twój identyfikator Yahoo ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'YAHOO_ID_59fc7f'	=>	'Yahoo ID',
'YOUR_PAYPAL_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__bacd9d'	=>	'Twój identyfikator PayPal ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'PAYPAL_ID_4a3287'	=>	'PayPal ID',
'YOUR_DISQUS_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__2954fe'	=>	'Twój identyfikator Disqus ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'DISQUS_ID_614174'	=>	'Disqus ID',
'YOUR_WORDPRESS_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_4776cc'	=>	'Twój identyfikator WordPress ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'WORDPRESS_ID_9d3a7b'	=>	'WordPress ID',
'YOUR_MEETUP_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__29b49f'	=>	'Twój identyfikator Meetup ID pozwala na zapytania API; przy braku dostępu zostaną wykonane jedynie zapytania o ogólno dostępne dane.',
'MEETUP_ID_629f66'	=>	'Meetup ID',
// 12 language strings from file plug_cbconnect/component.cbconnect.php
'PROVIDER_NOT_AVAILABLE'	=>	'[provider] nie jest dostępny.',
'PROVIDER_FAILED_TO_AUTHENTICATE'	=>	'[provider] odrzucił autoryzację. Błąd: [error]',
'PROVIDER_PROFILE_MISSING'	=>	'Nie można odnaleźć profilu [provider].',
'LINKING_FOR_PROVIDER_NOT_PERMITTED'	=>	'[provider] nie dopuszcza powiązania profilu.',
'PROVIDER_ALREADY_LINKED'	=>	'Konto [provider] jest już powiązane z innym użytkownikiem.',
'PROVIDER_FAILED_TO_LINK'	=>	'Nie udało się powiązać konta z kontem [provider]. Błąd: [error]',
'PROVIDER_LINKED_SUCCESSFULLY'	=>	'Powiązano z kontem [provider]!',
'ALREADY_LINKED_TO_PROVIDER'	=>	'Twoje konto jest już powiązane z kontem na [provider].',
'SIGN_UP_WITH_PROVIDER_NOT_PERMITTED'	=>	'Logowanie za pomocą konta [provider] nie jest możliwe.',
'PROVIDER_SIGN_UP_INCOMPLETE'	=>	'Twoje logowanie do [provider] jest niepełne. Proszę uzupełnij poniższe.',
'SIGN_UP_WITH_PROVIDER_FAILED'	=>	'Logowanie za pośrednictwem [provider] nie powiodło się. Błąd: [error]',
'SIGN_UP_INCOMPLETE_c37e7a'	=>	'Logowanie jest niepełne',
// 17 language strings from file plug_cbconnect/library/CBConnect.php
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
// 6 language strings from file plug_cbconnect/library/Connect.php
'LINK_YOUR_PROVIDER_ACCOUNT'	=>	'Powiąż Twoje konto [provider]',
'LINK_WITH_PROVIDER'	=>	'Powiąż z [provider]',
'SIGN_UP_WITH_PROVIDER'	=>	'Zaloguj się przez [provider]',
'SIGN_UP_WITH_YOUR_PROVIDER_ACCOUNT'	=>	'Zaloguj się za pomocą konta [provider]',
'SIGN_IN_WITH_PROVIDER'	=>	'Zaloguj się za pomocą [provider]',
'SIGN_IN_WITH_YOUR_PROVIDER_ACCOUNT'	=>	'Zaloguj się za pomocą konta [provider]',
// 3 language strings from file plug_cbconnect/library/Provider/AmazonProvider.php
'FAILED_EXCHANGE_CODE_ERROR'	=>	'Nieudane przekazanie kodu. Błąd: [error]',
'FAILED_TO_RETRIEVE_ACCESS_TOKEN_275f75'	=>	'Nieudane pozyskanie tokena dostępu.',
'FAILED_API_REQUEST_ERROR'	=>	'Nieudane zapytanie API [api]. Błąd: [error]',
// 1 language strings from file plug_cbconnect/library/Provider/SteamProvider.php
'FAILED_TO_AUTHENTICATE_IDENTITY_d02393'	=>	'Nieudane potwierdzenie autoryzacji.',
// 4 language strings from file plug_cbconnect/library/Provider/TwitterProvider.php
'FAILED_EXCHANGE_TOKEN_ERROR'	=>	'Nieudana wymiana tokena. Błąd: [error]',
'FAILED_REQUEST_TOKEN_ERROR'	=>	'Nieudane żądanie tokena. Błąd: [error]',
'CALLBACK_FAILED_TO_CONFIRM_bb8d55'	=>	'Nieudane potwierdzenie zwrotne.',
'FAILED_TO_REQUEST_CALLBACK_2a0309'	=>	'Nieudane żądanie potwierdzenia zwrotnego.',
);
