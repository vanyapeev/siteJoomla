<?php
/**
* Community Builder (TM) cb.pulog Danish (Denmark) language file Frontend
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
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_PROFILE_U_05b692'	=>	'Vælg skabelonen der skal anvendes til hele CB Profile Update Logger. Hvis skabelonen er ufuldstændig, så vil manglende filer blive hentet fra standard skabelonen. Skabelonfiler kan findes på følgende sted: components/com_comprofiler/plugin/user/plug_cbprofileupdatelogger/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_559f2f'	=>	'Tilføj eventuelt et klassesuffiks til den DIV der omkranser hele CB Profile Update Logger.',
'ENABLE_OR_DISABLE_AUTOMATIC_DELETION_OF_LOGS_WHEN__c527b5'	=>	'Aktiver eller deaktiver automatisk sletning af logposter når en bruger slettes.',
'ENABLE_OR_DISABLE_USAGE_OF_BACKEND_ADMINISTRATOR_M_1dbdc5'	=>	'Aktiver eller deaktiver anvendelse af backend administrator menu.',
'ADMIN_MENU_3d31a7'	=>	'Admin menu',
'ENABLE_OR_DISABLE_LOGGING_OF_BACKEND_PROFILE_CHANG_b8e524'	=>	'Aktiver eller deaktiver logning af backend profilændringer.',
'BACKEND_2e427c'	=>	'Backend',
'OPTIONALLY_INPUT_A_COMMA_SEPARATED_LIST_OF_USER_ID_340263'	=>	'Indtast eventuelt en komma separeret liste med bruger id\'er der skal ignoreres når der kontrolleres for ændringer.',
'EXCLUDE_USERS_f9804a'	=>	'Ekskluder brugere',
'OPTIONALLY_SELECT_FIELDS_TO_IGNORE_WHEN_CHECKING_F_05f34d'	=>	'Vælg eventuelt felter der skal ignoreres når der kontrolleres for ændringer. Bemærk at adgangskode feltet altid ignoreres.',
'EXCLUDE_FIELDS_922895'	=>	'Ekskluder felter',
'SELECT_FIELDS_b7951c'	=>	'- Vælg felter -',
'OPTIONALLY_SELECT_TYPES_OF_FIELDS_TO_IGNORE_WHEN_C_720812'	=>	'Vælg eventuelt felttyper der skal ignoreres når der kontrolleres for ændringer. Bemærk at felttypen adgangskode altid ignoreres.',
'EXCLUDE_FIELD_TYPES_43180b'	=>	'Ekskluder felttyper',
'SELECT_FIELD_TYPES_21878c'	=>	'- Vælg felttyper -',
'ENABLE_OR_DISABLE_NOTIFYING_MODERATORS_OF_FRONTEND_685ca6'	=>	'Aktiver elle deaktiver underretning af moderatorer ved frontend profilændringer.',
'THIS_TAB_CONTAINS_A_LOG_OF_PROFILE_UPDATES_MADE_BY_483741'	=>	'Denne fane indeholder en log over profilopdateringer foretager af bruger eller moderatorer',
'UPDATE_LOG_cbc070'	=>	'Opdateringslog',
'ENABLE_OR_DISABLE_DISPLAY_OF_THE_PROFILE_UPDATE_LO_2681f5'	=>	'Aktiver eller deaktiver visning af profil opdateringsloggen til profilejeren udover moderatorerne.',
'PROFILE_OWNER_06447f'	=>	'Profilejer',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Aktiver eller deaktiver anvendelse af sideinddeling.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_CH_fe62d5'	=>	'Indtast sidegrænse. Sidegrænsen bestemmer hvor mange ændringer der vises per side.',
'LIMIT_80d267'	=>	'Grænse',
// 2 language strings from file plug_cbprofileupdatelog/library/Table/UpdateLogTable.php
'FIELD_NOT_SPECIFIED_f8ddb4'	=>	'Felt ikke specificeret!',
'VALUE_IS_UNCHANGED_9d5852'	=>	'Værdi er uændret!',
// 3 language strings from file plug_cbprofileupdatelog/library/Trigger/AdminTrigger.php
'PROFILE_UPDATE_LOG_46898e'	=>	'Profil opdaterings log',
'LOG_ce0be7'	=>	'Log',
'CONFIGURATION_254f64'	=>	'Konfiguration',
// 5 language strings from file plug_cbprofileupdatelog/library/Trigger/UserTrigger.php
'EMPTY_9e65b5'	=>	'(tom)',
'FIELD_CHANGED_OLD_TO_NEW'	=>	'<p><strong>[field]:</strong> "[old]" til "[new]"</p>',
'A_PROFILE_HAS_BEEN_UPDATED_86d910'	=>	'En profil er blevet opdateret!',
'USERNAME_HAS_UPDATED_THEIR_PROFILE_CHANGED_CHANGED_0b1ef6'	=>	'<a href="[url]">[username]</a> har opdateret sin profil. Ændret: [changed]. Afventende ændringer: [pending].<br /><br />[changes]',
'USER_HAS_UPDATED_THE_PROFILE_OF_USERNAME_CHANGED_C_d64443'	=>	'[user] har opdateret profil for <a href="[url]">[username]</a>. Ændret: [changed]. Afventende ændringer: [pending].<br /><br />[changes]',
// 9 language strings from file plug_cbprofileupdatelog/templates/default/tab.php
'FIELD_6f16a5'	=>	'Felt',
'OLD_VALUE_56f05f'	=>	'Gammel værdi',
'NEW_VALUE_943f33'	=>	'Ny værdi',
'BY_53e5aa'	=>	'Af',
'SELF_ad6e76'	=>	'Selv',
'BACKEND_USER'	=>	'Backend: [user]',
'FRONTEND_USER'	=>	'Frontend: [user]',
'YOU_CURRENTLY_HAVE_NO_CHANGES_c7ea23'	=>	'Du har pt ingen ændringer.',
'THIS_USER_CURRENTLY_HAS_NO_CHANGES_6f157c'	=>	'Denne bruger har pt ingen ændringer.',
);
