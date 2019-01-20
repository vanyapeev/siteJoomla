<?php
/**
* Community Builder (TM) cb.pulog Polish (Poland) language file Frontend
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
// 23 language strings from file plug_cbprofileupdatelog/cb.pulog.xml
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_PROFILE_U_05b692'	=>	'Wybierz szablon, który będzie używany dla zakładki CB Profile Update Logger. W przypadku, gdy szablon nie jest kompletny, brakujące pliki zostaną uzupełnione z domyślnego szablonu. Pliki szablonu można znaleźć w następującym miejscu: components/com_comprofiler/plugin/user/plug_cbprofileupdatelogger/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_559f2f'	=>	'Opcjonalnie dodaj suffix klasy CSS do elementu DIV obejmującego wszystkie elementy CB Profile Update Logger.',
'ENABLE_OR_DISABLE_AUTOMATIC_DELETION_OF_LOGS_WHEN__c527b5'	=>	'Włącz lub wyłącz automatycznie kasowanie rejestru zmian przy usunięciu konta użytkownika.',
'ENABLE_OR_DISABLE_USAGE_OF_BACKEND_ADMINISTRATOR_M_1dbdc5'	=>	'Włącz lub wyłącz użycie menu administratora na zapleczu strony.',
'ADMIN_MENU_3d31a7'	=>	'Menu Administracyjne',
'ENABLE_OR_DISABLE_LOGGING_OF_BACKEND_PROFILE_CHANG_b8e524'	=>	'Włącz lub wyłącz zapisywanie zmian profilu wprowadzonych z poziomu zaplecza strony.',
'BACKEND_2e427c'	=>	'Zaplecze',
'OPTIONALLY_INPUT_A_COMMA_SEPARATED_LIST_OF_USER_ID_340263'	=>	'Opcjonalnie podaj oddzieloną przecinkami listę id użytkowników, którzy będą pomijani przy sprawdzeniu zmian profilu.',
'EXCLUDE_USERS_f9804a'	=>	'Wyklucz Użytkowników',
'OPTIONALLY_SELECT_FIELDS_TO_IGNORE_WHEN_CHECKING_F_05f34d'	=>	'Opcjonalnie wybierz pola, które będą pomijane przy sprawdzaniu zmian profilu. Uwaga: pole hasła jest zawsze pomijane.',
'EXCLUDE_FIELDS_922895'	=>	'Wyklucz Pola',
'SELECT_FIELDS_b7951c'	=>	'- Wybierz Pola -',
'OPTIONALLY_SELECT_TYPES_OF_FIELDS_TO_IGNORE_WHEN_C_720812'	=>	'Opcjonalnie wybierz typy pól, które będą pomijane przy sprawdzaniu zmian profilu. Uwaga: pole hasła jest zawsze pomijane.',
'EXCLUDE_FIELD_TYPES_43180b'	=>	'Wyklucz Typy Pól',
'SELECT_FIELD_TYPES_21878c'	=>	'- Wybierz Typy Pól -',
'ENABLE_OR_DISABLE_NOTIFYING_MODERATORS_OF_FRONTEND_685ca6'	=>	'Włącz lub wyłącz powiadomienia moderatorów o zmianach profilu z poziomu strony.',
'THIS_TAB_CONTAINS_A_LOG_OF_PROFILE_UPDATES_MADE_BY_483741'	=>	'Ta zakładka zawiera rejestr zmian profilu wprowadzonych przez Użytkownika lub Moderatorów.',
'UPDATE_LOG_cbc070'	=>	'Rejestr Zmian',
'ENABLE_OR_DISABLE_DISPLAY_OF_THE_PROFILE_UPDATE_LO_2681f5'	=>	'Włącz lub wyłącz wyświetlanie listy zmian profilu jego właścicielowi poza standardową widocznością dla moderatorów.',
'PROFILE_OWNER_06447f'	=>	'Właściciel Profilu',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Włącz lub wyłącz podział na strony.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_CH_fe62d5'	=>	'Podaj długość strony. Długość strony określa ilość zmian w profilu wyświetlanych na jednej stronie.',
'LIMIT_80d267'	=>	'Limit',
// 2 language strings from file plug_cbprofileupdatelog/library/Table/UpdateLogTable.php
'FIELD_NOT_SPECIFIED_f8ddb4'	=>	'Nie wskazano pola!',
'VALUE_IS_UNCHANGED_9d5852'	=>	'Wartość się nie zmieniła!',
// 3 language strings from file plug_cbprofileupdatelog/library/Trigger/AdminTrigger.php
'PROFILE_UPDATE_LOG_46898e'	=>	'Rejestr Zmian Profilu',
'LOG_ce0be7'	=>	'Logi',
'CONFIGURATION_254f64'	=>	'Konfiguracja',
// 5 language strings from file plug_cbprofileupdatelog/library/Trigger/UserTrigger.php
'EMPTY_9e65b5'	=>	'(pusty)',
'FIELD_CHANGED_OLD_TO_NEW'	=>	'<p><strong>[field]:</strong> "[old]" na "[new]"</p>',
'A_PROFILE_HAS_BEEN_UPDATED_86d910'	=>	'Profil został zmieniony!',
'USERNAME_HAS_UPDATED_THEIR_PROFILE_CHANGED_CHANGED_0b1ef6'	=>	'<a href="[url]">[username]</a> zmienił/a swój profil. Zmiany: [changed]. Oczekujące Zmiany: [pending].<br /><br />[changes]',
'USER_HAS_UPDATED_THE_PROFILE_OF_USERNAME_CHANGED_C_d64443'	=>	'[user] zmienił/a profil użytkownika <a href="[url]">[username]</a>. Zmiany: [changed]. Oczekujace Zmiany: [pending].<br /><br />[changes]',
// 9 language strings from file plug_cbprofileupdatelog/templates/default/tab.php
'FIELD_6f16a5'	=>	'Pole',
'OLD_VALUE_56f05f'	=>	'Stara Wartość',
'NEW_VALUE_943f33'	=>	'Nowa Wartość',
'BY_53e5aa'	=>	'Autorstwa',
'SELF_ad6e76'	=>	'Self',
'BACKEND_USER'	=>	'Zaplecze: [user]',
'FRONTEND_USER'	=>	'Strona: [user]',
'YOU_CURRENTLY_HAVE_NO_CHANGES_c7ea23'	=>	'Nie masz obecnie listy zmian profilu.',
'THIS_USER_CURRENTLY_HAS_NO_CHANGES_6f157c'	=>	'Ten użytkownik nie posiada obecnie listy zmian profilu.',
);
