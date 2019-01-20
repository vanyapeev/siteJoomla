<?php
/**
* Community Builder (TM) cb.pulog Slovak (Slovakia) language file Frontend
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
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_PROFILE_U_05b692'	=>	'Zvoľte šablónu, ktorá bude použitá vo všetkých záznamoch aktualizácie profilu CB. Ak je šablóna neúplná, tak chýbajúce súbory budú použité z predvolenej šablóny. Súbory šablóny môžu byť uložené na tomto mieste: components/com_comprofiler/plugin/user/plug_cbprofileupdatelogger/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_559f2f'	=>	'Voliteľne môžete pridať predponu tried štýlov pre okolité DIV zahŕňajúce záznam aktualizácie profilu CB.',
'ENABLE_OR_DISABLE_AUTOMATIC_DELETION_OF_LOGS_WHEN__c527b5'	=>	'Zapnúť alebo vypnúť automatické mazanie záznamov, ak bude používateľ vymazaný.',
'ENABLE_OR_DISABLE_USAGE_OF_BACKEND_ADMINISTRATOR_M_1dbdc5'	=>	'Zapnúť alebo vypnúť používanie backendového menu administrácie.',
'ADMIN_MENU_3d31a7'	=>	'Menu administrácie',
'ENABLE_OR_DISABLE_LOGGING_OF_BACKEND_PROFILE_CHANG_b8e524'	=>	'Zapnúť alebo vypnúť zaznamenávanie zmien profilu cez administrátorské rozhranie.',
'BACKEND_2e427c'	=>	'Administrácia',
'OPTIONALLY_INPUT_A_COMMA_SEPARATED_LIST_OF_USER_ID_340263'	=>	'Voliteľne môžete zadať čiarkou oddeľovaný zoznam používateľských id, ktoré budú vynechané zo zaznamenávania zmien.',
'EXCLUDE_USERS_f9804a'	=>	'Vynechať používateľov',
'OPTIONALLY_SELECT_FIELDS_TO_IGNORE_WHEN_CHECKING_F_05f34d'	=>	'Voliteľne môžete zvoliť polia, ktoré budú ignorované pri kontrolách zmien. Pozn.: pole s heslom je ignorované vždy.',
'EXCLUDE_FIELDS_922895'	=>	'Vynechat polia',
'SELECT_FIELDS_b7951c'	=>	'- Vyberte polia -',
'OPTIONALLY_SELECT_TYPES_OF_FIELDS_TO_IGNORE_WHEN_C_720812'	=>	'Voliteľne môžete zvoliť typy alebo polia, ktoré budú ignorované pri kontrolách zmien. Pozn.: pole s heslom je ignorované vždy.',
'EXCLUDE_FIELD_TYPES_43180b'	=>	'Vynechať typy polí',
'SELECT_FIELD_TYPES_21878c'	=>	'- Vyberte typy polí -',
'ENABLE_OR_DISABLE_NOTIFYING_MODERATORS_OF_FRONTEND_685ca6'	=>	'Zapnúť alebo vypnúť upozornenia pre moderátorov o zmenách profilov na webe.',
'THIS_TAB_CONTAINS_A_LOG_OF_PROFILE_UPDATES_MADE_BY_483741'	=>	'Táto záložka obsahuje záznamy o aktualizáciách profilov vykonaných používateľom alebo moderátormi',
'UPDATE_LOG_cbc070'	=>	'Aktualizačný log',
'ENABLE_OR_DISABLE_DISPLAY_OF_THE_PROFILE_UPDATE_LO_2681f5'	=>	'Zapnúť alebo vypnúť zobrazovanie záznamov o aktualizácii profilu vlastníkovi profilu aj moderátorom.',
'PROFILE_OWNER_06447f'	=>	'Vlastník profilu',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Zapnúť alebo vypnúť použitie stránkovania.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_CH_fe62d5'	=>	'Zadajte limit stránkovania. Tento limit určuje počet zmien zobrazených na jednej stránke.',
'LIMIT_80d267'	=>	'Limit',
// 2 language strings from file plug_cbprofileupdatelog/library/Table/UpdateLogTable.php
'FIELD_NOT_SPECIFIED_f8ddb4'	=>	'Pole nie je defionované!',
'VALUE_IS_UNCHANGED_9d5852'	=>	'Hodnota nebola zmenená!',
// 3 language strings from file plug_cbprofileupdatelog/library/Trigger/AdminTrigger.php
'PROFILE_UPDATE_LOG_46898e'	=>	'Záznam aktualizácie profilu',
'LOG_ce0be7'	=>	'Logy',
'CONFIGURATION_254f64'	=>	'Nastavenie',
// 5 language strings from file plug_cbprofileupdatelog/library/Trigger/UserTrigger.php
'EMPTY_9e65b5'	=>	'(prázdne)',
'FIELD_CHANGED_OLD_TO_NEW'	=>	'<p><strong>[field]:</strong> "[old]" na "[new]"</p>',
'A_PROFILE_HAS_BEEN_UPDATED_86d910'	=>	'Profil bol aktualizovaný!',
'USERNAME_HAS_UPDATED_THEIR_PROFILE_CHANGED_CHANGED_0b1ef6'	=>	'Používateľ <a href="[url]">[username]</a> aktualizoval svoj profil. Zmena: [changed]. Čakajúce zmeny: [pending].<br /><br />[changes]',
'USER_HAS_UPDATED_THE_PROFILE_OF_USERNAME_CHANGED_C_d64443'	=>	'Používateľ [user] aktualizoval profil používateľa <a href="[url]">[username]</a>. Zmena: [changed]. Čakajúce zmeny: [pending].<br /><br />[changes]',
// 9 language strings from file plug_cbprofileupdatelog/templates/default/tab.php
'FIELD_6f16a5'	=>	'Pole',
'OLD_VALUE_56f05f'	=>	'Stará hodnota',
'NEW_VALUE_943f33'	=>	'Nová hodnota',
'BY_53e5aa'	=>	'Od',
'SELF_ad6e76'	=>	'Vlastné',
'BACKEND_USER'	=>	'Administrácia: [user]',
'FRONTEND_USER'	=>	'Web: [user]',
'YOU_CURRENTLY_HAVE_NO_CHANGES_c7ea23'	=>	'V súčasnosti nie sú žiadne zmeny.',
'THIS_USER_CURRENTLY_HAS_NO_CHANGES_6f157c'	=>	'Tento používateľ v súčasnosti nemá žiadne zmeny.',
);
