<?php
/**
* Community Builder (TM) cb.pulog Swedish (Sweden) language file Frontend
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
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_PROFILE_U_05b692'	=>	'Välj mall som ska användas för hela CB Profile Update Logger. Om mallen är ofullständig kommer saknade filer att användas från standardmallen. Mallfiler hittas här: components/com_comprofiler/plugin/user/plug_cbprofileupdatelogger/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_559f2f'	=>	'Valfritt, lägg till ett klass suffix till alla omgivande DIV som används av CB CB Profile Update Logger.',
'ENABLE_OR_DISABLE_AUTOMATIC_DELETION_OF_LOGS_WHEN__c527b5'	=>	'Aktivera/Inaktivera automatisk radering av loggar när en användare raderas. ',
'ENABLE_OR_DISABLE_USAGE_OF_BACKEND_ADMINISTRATOR_M_1dbdc5'	=>	'Aktivera/Inaktivera användning av adminmeny i backend.',
'ADMIN_MENU_3d31a7'	=>	'Adminmeny',
'ENABLE_OR_DISABLE_LOGGING_OF_BACKEND_PROFILE_CHANG_b8e524'	=>	'Aktivera/Inaktivera loggning av profiländringar via backend.',
'BACKEND_2e427c'	=>	'Backend',
'OPTIONALLY_INPUT_A_COMMA_SEPARATED_LIST_OF_USER_ID_340263'	=>	'Valfritt, ange en kommaseparerad lista med användar-ID att ignorera vid loggning av ändringar.',
'EXCLUDE_USERS_f9804a'	=>	'Exkludera användare',
'OPTIONALLY_SELECT_FIELDS_TO_IGNORE_WHEN_CHECKING_F_05f34d'	=>	'Valfritt, välj fält att ignorera vid loggning av ändringar. Observera att lösenordsfältet alltid ignoreras.',
'EXCLUDE_FIELDS_922895'	=>	'Ignorera fält',
'SELECT_FIELDS_b7951c'	=>	'- Välj fält -',
'OPTIONALLY_SELECT_TYPES_OF_FIELDS_TO_IGNORE_WHEN_C_720812'	=>	'Valfritt, välj fälttyper att ignorera vid loggning av ändringar. Observera att fälttyp för lösenord alltid ignoreras. ',
'EXCLUDE_FIELD_TYPES_43180b'	=>	'Ignorera fälttyper',
'SELECT_FIELD_TYPES_21878c'	=>	'- Välj fälttyper -',
'ENABLE_OR_DISABLE_NOTIFYING_MODERATORS_OF_FRONTEND_685ca6'	=>	'Aktivera/Inaktivera avisering till moderatorer vid profiluppdateringar via frontend. ',
'THIS_TAB_CONTAINS_A_LOG_OF_PROFILE_UPDATES_MADE_BY_483741'	=>	'Denna flik innehåller en logg över profiluppdateringar',
'UPDATE_LOG_cbc070'	=>	'Uppdateringslogg',
'ENABLE_OR_DISABLE_DISPLAY_OF_THE_PROFILE_UPDATE_LO_2681f5'	=>	'Aktivera/Inaktivera visning av uppdateringsloggen för profilägaren, utöver moderatorer.',
'PROFILE_OWNER_06447f'	=>	'Profilägare',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Aktivera/Inaktivera användning av sidbrytning.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_CH_fe62d5'	=>	'Ange sidgräns. Gränsen avgör hur många ändringar som visas per sida.',
'LIMIT_80d267'	=>	'Begränsning',
// 2 language strings from file plug_cbprofileupdatelog/library/Table/UpdateLogTable.php
'FIELD_NOT_SPECIFIED_f8ddb4'	=>	'Fält ej angivet.',
'VALUE_IS_UNCHANGED_9d5852'	=>	'Värdet har inte ändrats.',
// 3 language strings from file plug_cbprofileupdatelog/library/Trigger/AdminTrigger.php
'PROFILE_UPDATE_LOG_46898e'	=>	'Profil Uppdateringslogg',
'LOG_ce0be7'	=>	'Logg',
'CONFIGURATION_254f64'	=>	'Inställningar',
// 5 language strings from file plug_cbprofileupdatelog/library/Trigger/UserTrigger.php
'EMPTY_9e65b5'	=>	'(tomt)',
'FIELD_CHANGED_OLD_TO_NEW'	=>	'<p><strong>[field]:</strong> "[old]" till "[new]"</p>',
'A_PROFILE_HAS_BEEN_UPDATED_86d910'	=>	'En profil \'r uppdaterad',
'USERNAME_HAS_UPDATED_THEIR_PROFILE_CHANGED_CHANGED_0b1ef6'	=>	'<a href="[url]">[username]</a> har uppdaterad profil. Ändringar: [changed]. Väntande ändringar: [pending].<br /><br />[changes]',
'USER_HAS_UPDATED_THE_PROFILE_OF_USERNAME_CHANGED_C_d64443'	=>	'[user] har uppdaterat medlemsprofilen för <a href="[url]">[username]</a>. Ändringar: [changed]. Väntande ändringar: [pending].<br /><br />[changes]',
// 9 language strings from file plug_cbprofileupdatelog/templates/default/tab.php
'FIELD_6f16a5'	=>	'Fält',
'OLD_VALUE_56f05f'	=>	'Gammalt värde',
'NEW_VALUE_943f33'	=>	'Nytt värde',
'BY_53e5aa'	=>	'Av',
'SELF_ad6e76'	=>	'Medlemmen själv',
'BACKEND_USER'	=>	'Backend: [user]',
'FRONTEND_USER'	=>	'Frontend: [user]',
'YOU_CURRENTLY_HAVE_NO_CHANGES_c7ea23'	=>	'Din medlemsprofil har aldrig uppdaterats.',
'THIS_USER_CURRENTLY_HAS_NO_CHANGES_6f157c'	=>	'Den här medlemsprofilen har aldrig uppdaterats.',
);
