<?php
/**
* Community Builder (TM) cb.pulog Turkish (Turkey) language file Frontend
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
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_PROFILE_U_05b692'	=>	'Tüm CB Profil Güncelleme Günlüklerinde kullanılacak temayı seçin. Temada eksik dosyalar varsa, onların yerine varsayılan temanın dosyaları kullanılır. Tema dosyaları şu konumda bulunur: components/com_comprofiler/plugin/user/plug_cbprofileupdatelogger/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_559f2f'	=>	'İsteğe bağlı, tüm CB Profile Güncelleme Günlükleri için DIV bölümünde kullanılacak sınıf son eki.',
'ENABLE_OR_DISABLE_AUTOMATIC_DELETION_OF_LOGS_WHEN__c527b5'	=>	'Bu seçenek etkinleştirildiğinde, bir kullanıcı silindiğinde kullanıcının günlükleri de otomatik olarak silinir.',
'ENABLE_OR_DISABLE_USAGE_OF_BACKEND_ADMINISTRATOR_M_1dbdc5'	=>	'Bu seçenek etkinleştirildiğinde, yönetim bölümü menüsü kullanılır.',
'ADMIN_MENU_3d31a7'	=>	'Yönetim Menüsü',
'ENABLE_OR_DISABLE_LOGGING_OF_BACKEND_PROFILE_CHANG_b8e524'	=>	'Bu seçenek etkinleştirildiğinde, yönetim bölümünde yapılan profil değişikliklerinin günlüğü tutulur.',
'BACKEND_2e427c'	=>	'Yönetim Bölümü',
'OPTIONALLY_INPUT_A_COMMA_SEPARATED_LIST_OF_USER_ID_340263'	=>	'Günlük kayıtlarının tutulması istenmeyen kullanıcı kodlarını virgül ile ayrılarak yazın.',
'EXCLUDE_USERS_f9804a'	=>	'Katılmayacak Kullanıcılar',
'OPTIONALLY_SELECT_FIELDS_TO_IGNORE_WHEN_CHECKING_F_05f34d'	=>	'Değişiklikler denetlenirken yok sayılacak alanları seçin. Parola alanının her zaman yok sayılacağını unutmayın.',
'EXCLUDE_FIELDS_922895'	=>	'Katılmayacak Alanlar',
'SELECT_FIELDS_b7951c'	=>	'- Alanları Seçin -',
'OPTIONALLY_SELECT_TYPES_OF_FIELDS_TO_IGNORE_WHEN_C_720812'	=>	'Değişiklikler denetlenirken yok sayılacak alan türlerini seçin. Parola alanını türünün her zaman yok sayılacağını unutmayın.',
'EXCLUDE_FIELD_TYPES_43180b'	=>	'Katılmayacak Alan Türleri',
'SELECT_FIELD_TYPES_21878c'	=>	'- Alan Türlerini Seçin -',
'ENABLE_OR_DISABLE_NOTIFYING_MODERATORS_OF_FRONTEND_685ca6'	=>	'Bu seçenek etkinleştirildiğinde, ön yüzden  yapılan profil değişiklikleri sorumlulara bildirilir.',
'THIS_TAB_CONTAINS_A_LOG_OF_PROFILE_UPDATES_MADE_BY_483741'	=>	'Bu sekmede kullanıcı ya da sorumluların yaptığı profil güncellemelerinin günlüğü bulunur',
'UPDATE_LOG_cbc070'	=>	'Güncelleme Günlüğü',
'ENABLE_OR_DISABLE_DISPLAY_OF_THE_PROFILE_UPDATE_LO_2681f5'	=>	'Bu seçenek etkinleştirildiğinde, profil güncelleme günlüğü sorumlulara ek olarak profil sahibine de görüntülenir.',
'PROFILE_OWNER_06447f'	=>	'Profil Sahibi',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Bu seçenek etkinleştirildiğinde, sayfalandırma kullanılır.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_CH_fe62d5'	=>	'Bir sayfada görüntülenecek değişiklik sayısını yazın. ',
'LIMIT_80d267'	=>	'Sayı',
// 2 language strings from file plug_cbprofileupdatelog/library/Table/UpdateLogTable.php
'FIELD_NOT_SPECIFIED_f8ddb4'	=>	'Alan belirtilmemiş!',
'VALUE_IS_UNCHANGED_9d5852'	=>	'Değer değiştirilmemiş!',
// 3 language strings from file plug_cbprofileupdatelog/library/Trigger/AdminTrigger.php
'PROFILE_UPDATE_LOG_46898e'	=>	'Profil Güncelleme Günlüğü',
'LOG_ce0be7'	=>	'Günlük',
'CONFIGURATION_254f64'	=>	'Ayarlar',
// 5 language strings from file plug_cbprofileupdatelog/library/Trigger/UserTrigger.php
'EMPTY_9e65b5'	=>	'(boş)',
'FIELD_CHANGED_OLD_TO_NEW'	=>	'<p><strong>[field]:</strong> "[old]" -> "[new]"</p>',
'A_PROFILE_HAS_BEEN_UPDATED_86d910'	=>	'Bir profil güncellendi!',
'USERNAME_HAS_UPDATED_THEIR_PROFILE_CHANGED_CHANGED_0b1ef6'	=>	'<a href="[url]">[username]</a> profilini güncelledi. Değişiklik: [changed]. Bekleyen Değişiklikler: [pending].<br /><br />[changes]',
'USER_HAS_UPDATED_THE_PROFILE_OF_USERNAME_CHANGED_C_d64443'	=>	'[user], <a href="[url]">[username]</a> kullanıcısının profilini güncelledi. Değişiklik: [changed]. Bekleyen Değişiklik: [pending].<br /><br />[changes]',
// 9 language strings from file plug_cbprofileupdatelog/templates/default/tab.php
'FIELD_6f16a5'	=>	'Alan',
'OLD_VALUE_56f05f'	=>	'Eski Değer',
'NEW_VALUE_943f33'	=>	'Yeni Değer',
'BY_53e5aa'	=>	'Gönderen',
'SELF_ad6e76'	=>	'Kendi',
'BACKEND_USER'	=>	'Yönetim Bölümü: [user]',
'FRONTEND_USER'	=>	'Ön Yüz: [user]',
'YOU_CURRENTLY_HAVE_NO_CHANGES_c7ea23'	=>	'Henüz herhangi bir değişiklik yapmamışsınız.',
'THIS_USER_CURRENTLY_HAS_NO_CHANGES_6f157c'	=>	'Bu kullanıcıda henüz bir değişiklik yapılmamış.',
);
