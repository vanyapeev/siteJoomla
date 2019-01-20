<?php
/**
* Community Builder (TM) cbgallery Slovak (Slovakia) language file Frontend
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
// 23 language strings from file plug_cbgallery/cbgallery.xml
'OPTIONALLY_INPUT_THE_COMMA_SEPARATED_ASSETS_FOR_TH_33126d'	=>	'Voliteľne môžete zadať čiarkou oddeľovaný zoznam zdrojov tejto galérie. Zdroj určuje umiestnenie galérie (napr. global.cars, profile.[user_id], profile.%). Nechajte pole prázdne pre vyplnenie poľa podľa zobrazovaného používateľa (napr. profile.[user_id].field.[field_id]). Voliteľne môžete zadať podporované zástupné reťazce (napr. profile.[user_id].cars.[field_id]) v spojení s vlastnými premennými [displayed_id] a [viwer_id]. Taktiež môžu byť použité nasledovné vlastné zdroje: profile, uploads, connections, connectionsonly, self, self.uploads, self.connections, self.connectionsonly, user, user.uploads, user.connections, user.connectionsonly, displayed, displayed.uploads, displayed.connections a displayed.connectionsonly.',
'ASSET_26e905'	=>	'Zdroj',
'GALLERY_5c9331'	=>	'Galéria',
'OPTIONALLY_INPUT_THE_COMMA_SEPARATED_ASSETS_FOR_TH_1824fa'	=>	'Voliteľne môžete zadať čiarkou oddeľovaný zoznam zdrojov tejto galérie. Zdroj určuje umiestnenie galérie (napr. global.cars, profile.[user_id], profile.%). Nechajte pole prázdne pre vyplnenie poľa podľa zobrazovaného používateľa (napr. profile.[user_id]). Voliteľne môžete zadať podporované zástupné reťazce (napr. profile.[user_id].cars) v spojení s vlastnými premennými [displayed_id] a [viwer_id]. Taktiež môžu byť použité nasledovné vlastné zdroje: profile, uploads, connections, connectionsonly, self, self.uploads, self.connections, self.connectionsonly, user, user.uploads, user.connections, user.connectionsonly, displayed, displayed.uploads, displayed.connections a displayed.connectionsonly.',
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_GALLERY_I_c47ff6'	=>	'Zvoľte šablónu, ktorá bude použitá pre všetky galérie v CB. Ak je šablóna neúplná, tak chýbajúce súbory budú použité z predvolenej šablóny. Súbory šablóny môžu byť umiestnené na tomto mieste: components/com_comprofiler/plugin/user/plug_cbgallery/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_79b347'	=>	'Voliteľne môžete pridať predponu tried štýlov pre okolité DIV zahŕňajúce Galériu CB.',
'ENABLE_OR_DISABLE_AUTOMATIC_DELETION_OF_ITEMS_WHEN_468e68'	=>	'Zapnúť alebo vypnúť automatické mazanie položiek, ak bude používateľ vymazaný.',
'ENABLE_OR_DISABLE_USAGE_OF_BACKEND_ADMINISTRATOR_M_1dbdc5'	=>	'Zapnúť alebo vypnúť používanie backendového menu administrácie.',
'ADMIN_MENU_3d31a7'	=>	'Menu administrácie',
'ENABLE_OR_DISABLE_USAGE_OF_WORKFLOWS_MENU_TO_ALBUM_bde6fb'	=>	'Zapnúť alebo vypnúť používanie menu toku činností na schvaľovanie albumov a médií.',
'WORKFLOWS_MENU_0efede'	=>	'Menu toku činností',
'ENABLE_OR_DISABLE_DISPLAY_OF_CUSTOM_THUMBNAILS_FOR_f12912'	=>	'Zapnúť alebo vypnúť vytváranie vlastných náhľadov videí, hudobných súborov a súborov s médiami.',
'THUMBNAILS_acc66e'	=>	'Náhľady',
'ENABLE_OR_DISABLE_UPLOADING_OF_THUMBNAILS_940ccc'	=>	'Zapnúť alebo vypnúť nahrávanie náhľadov.',
'UPLOAD_914124'	=>	'Nahrať',
'ENABLE_OR_DISABLE_LINKING_OF_THUMBNAILS_LINKING_AL_3fbaaa'	=>	'Zapnúť alebo vypnúť odkazy na náhľady. Týmto sa umožní zobrazovanie súborov s náhľadom z externých zdrojov v galériách.',
'LINK_97e7c9'	=>	'Odkaz',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_ALWAYS_BE_RESAMPL_b7b0e2'	=>	'Zvoľte, či náhrávané obrázky budú vždy prepočítavané. Prepočítavanie zvyšuje bezpečnosť, avšak animácie zachováva len ImageMagick.',
'INPUT_THE_MAXIMUM_HEIGHT_IN_PIXELS_THAT_THE_IMAGE__e0ce78'	=>	'Zadajte maximálnu výšku v pixeloch, na ktorú budú obrázky upravené.',
'INPUT_THE_MAXIMUM_WIDTH_IN_PIXELS_THAT_THE_IMAGE_W_75174f'	=>	'Zadajte maximálnu šírku v pixeloch, na ktorú budú obrázky upravené.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_MAINTAIN_THEIR_AS_d23fff'	=>	'Nastavte, či nahrávané obrázky majú počas zmeny veľkosti zachovávať svoj pomer strán. Pri nastavení na Nie bude obrázok vždy zmenený na zadanú výšku a šírku. Pri nastavení na Áno bude dodržaný pomer strán tak, aby bola čo najviac dodržaná zadaná výška a šírka náhľadu. Pri nastavení na Áno s orezaním bude obrázok vždy upravený na zadanú maximálnu šírku a výšku so zachovaním pomeru strán a zvyšok sa oreže; je to užitočné najmä pri štvorcových obrázkoch.',
'INPUT_THE_MINIMUM_ITEM_FILE_SIZE_IN_KBS_fcd110'	=>	'Zadajte minimálnu veľkosť súboru v KB.',
'INPUT_THE_MAXIMUM_ITEM_FILE_SIZE_IN_KBS_SET_TO_0_F_8ff57c'	=>	'Zadajte maximálnu veľkosť súboru v KB. Nastavte na 0 pre neobmedzenú veľkosť.',
// 63 language strings from file plug_cbgallery/component.cbgallery.php
'ALL_b1c94c'	=>	'Všetko',
'SEARCH_ALBUM_640997'	=>	'Prehľadávať album...',
'SEARCH_GALLERY_9432ef'	=>	'Prehľadávať galériu...',
'SEARCH_TYPE'	=>	'Prehľadávať [type]...',
'SELECT_PUBLISH_STATUS_OF_THE_FILE_IF_UNPUBLISHED_T_e75692'	=>	'Vyberte stav uverejnenia tohto súboru. Nepublikované položky nebudú viditeľné pre ostatných.',
'OPTIONALLY_INPUT_A_TITLE_IF_NO_TITLE_IS_PROVIDED_T_a49e71'	=>	'Voliteľne môžete zadať názov. Ak názov nie je zadaný, zobrazí sa v názve názov súboru.',
'SELECT_THE_ALBUM_FOR_THIS_FILE_b74b56'	=>	'Vyberte album pre tento súbor.',
'OPTIONALLY_INPUT_A_DESCRIPTION_83fc3f'	=>	'Voliteľne môžete zadať popis.',
'DESCRIPTION_b5a7ad'	=>	'Popis',
'NO_CHANGE_bb84f3'	=>	'Bez zmeny',
'SELECT_THE_FILE_TO_UPLOAD_739b2a'	=>	'Vyberte nahrávaný súbor.',
'FILE_MUST_BE_EXTS'	=>	'Váš súbor musí mať príponu [extensions].',
'FILE_SHOULD_EXCEED_SIZE'	=>	'Váš súbor nesmie presiahnuť [size].',
'FILE_SHOUND_NOT_EXCEED_SIZE'	=>	'Váš obrázok nesmie presiahnuť [size].',
'INPUT_THE_URL_TO_THE_FILE_TO_LINK_1ec2e8'	=>	'Zadajte URL adresu súboru, na ktorý odkazujete.',
'LINK_MUST_BE_EXTS'	=>	'Váš odkaz musí mať príponu [extensions].',
'OPTIONALLY_SELECT_THE_THUMBNAIL_FILE_TO_UPLOAD_19d180'	=>	'Voliteľne môžete vybrať náhľadový súbor, ktorý bude nahratý.',
'THUMBNAIL_MUST_BE_EXTS'	=>	'Váš súbor s náhľadom musí mať príponu [extensions].',
'THUMBNAIL_SHOULD_EXCEED_SIZE'	=>	'Váš súbor s náhľadom musí presiahnuť [size].',
'THUMBNAIL_SHOUND_NOT_EXCEED_SIZE'	=>	'Váš náhľadový obrázok nesmie byť väčší ako [size].',
'OPTIONALLY_INPUT_THE_URL_TO_THE_THUMBNAIL_FILE_TO__72ce7b'	=>	'Voliteľne môžete zadať URL adresu náhľadového obrázku, na ktorý odkazujete.',
'THUMBNAIL_LINK_MUST_BE_EXTS'	=>	'Váš odkaz na súbor s náhľadom musí mať príponu [extensions].',
'INPUT_OWNER_AS_SINGLE_INTEGER_USERID_169965'	=>	'Zadajte vlastníka ako celé číslo reprezentujúce user_id.',
'YOU_DO_NOT_HAVE_PERMISSION_TO_EDIT_THIS_FILE_af2b2d'	=>	'Nemáte dostatočné oprávnenia na úpravu tohto súboru.',
'YOU_CANNOT_UPLOAD_ANYMORE_TYPES'	=>	'Už nemôžete nahrávať ďalšie [types]. Dosiahli ste váš povolený limit.',
'YOU_CANNOT_LINK_ANYMORE_TYPES'	=>	'You can not link anymore [types]. You have reached your quota.',
'FILE_UPLOAD_INVALID_UPLOAD_ONLY_EXTS'	=>	'Nesprávny súbor. Nahrávajte len súbory s príponou [extensions]!',
'FILE_LINK_INVALID_LINK_ONLY_EXTS'	=>	'Nesprávna URL súboru. Odkazujte len na súbory s príponou [extensions]!',
'NO_PERMISSION_TO_CREATE_TYPES'	=>	'Nemáte dostatočné oprávnenia na vytváranie [types] v tejto galérii.',
'CUSTOM_THUMBNAIL_FILES_ARE_NOT_ALLOWED_IN_THIS_GAL_daec99'	=>	'Súbory s vlastným náhľadom v tejto galérii nie sú povolené.',
'CUSTOM_THUMBNAIL_LINKS_ARE_NOT_ALLOWED_IN_THIS_GAL_200bf1'	=>	'Odkazy na vlastné náhľady v tejto galérii nie sú povolené.',
'UPLOADS_0f3113'	=>	'Nahrávania',
'TYPE_FAILED_TO_SAVE'	=>	'Zlyhalo ukladanie typu [type]! Chyba: [error]',
'GALLERY_NEW_TYPE_CREATED'	=>	'Galéria - Nový [type] bol vytvorený!',
'TYPE_PENDING_APPROVAL'	=>	'Používateľ <a href="[user_url]">[formatname]</a> vytvoril [type] <a href="[item_url]">[item_title]</a> a žiada <a href="[gallery_location]">o schválenie</a>!',
'TYPE_SAVED_SUCCESSFULLY_AND_AWAITING_APPROVAL'	=>	'[type] bol úspešne uložený a čaká na schválenie!',
'TYPE_SAVED_SUCCESSFULLY'	=>	'[type] úspešne uložený!',
'NOTHING_TO_ROTATE_2bea34'	=>	'Nie je tu nič na otočenie.',
'PHOTO_FAILED_TO_ROTATE'	=>	'Otáčanie fotografie zlyhalo! Chyba: [error]',
'PROFILE_CANVAS_FAILED_TO_UPDATE'	=>	'Zlyhala aktualizácia titulnej fotografie profilu! Chyba: [error]',
'PROFILE_AVATAR_FAILED_TO_UPDATE'	=>	'Zlyhala aktualizácia profilovej fotografie! Chyba: [error]',
'PROFILE_CANVAS_SUCCESSFULLY_SET_TO_THIS_PHOTO_beea25'	=>	'Fotografia bola úspešne nastavená ako titulná fotografia profilu!',
'PROFILE_AVATAR_SUCCESSFULLY_SET_TO_THIS_PHOTO_5e72c9'	=>	'Fotografia bola úspešne nastavená ako profilová!',
'TYPE_STATE_FAILED_TO_SAVE'	=>	'Zlyhalo uloženie stavu pre [type]! Chyba [error]',
'TYPE_STATE_SAVED_SUCCESSFULLY'	=>	'Stav pre [type] bol úspešne uložený!',
'NOTHING_TO_DELETE_aea31f'	=>	'Nie je tu nič na vymazanie',
'TYPE_FAILED_TO_DELETE'	=>	'Zlyhalo vymazanie [type]! Chyba [error]',
'TYPE_DELETED_SUCCESSFULLY'	=>	'[type] úspešne vymazaný!',
'SELECT_PUBLISH_STATUS_OF_THE_ALBUM_IF_UNPUBLISHED__842c75'	=>	'Vyberte stav uverejnenia albumu. Nepublikovaný album nie je viditeľný pre ostatných.',
'OPTIONALLY_INPUT_A_TITLE_IF_NO_TITLE_IS_PROVIDED_T_72a8cd'	=>	'Voliteľne môžete zadať názov. Ak názov nie je zadaný, zobrazí sa v názve dátum.',
'ALBUM_FAILED_TO_SAVE'	=>	'Zlyhalo uloženie albumu! Chyba: [error]',
'GALLERY_NEW_ALBUM_CREATED_36552c'	=>	'Galéria - Nový album bol vytvorený!',
'ALBUM_PENDING_APPROVAL'	=>	'Používateľ <a href="[user_url]">[formatname]</a> vytvoril album <a href="[folder_url]">[folder_title]</a> a žiada o jeho schválenie!',
'ALBUM_SAVED_SUCCESSFULLY_AND_AWAITING_APPROVAL_d6fc5e'	=>	'Album bol úspešne uložený a čaká na schválenie!',
'ALBUM_SAVED_SUCCESSFULLY_096683'	=>	'Album bol úspešne uložený!',
'ALBUM_DOES_NOT_EXIST_716b0b'	=>	'Album neexistuje.',
'FILE_DOES_NOT_EXIST_50db35'	=>	'Súbor neexistuje.',
'ALBUM_COVER_FAILED_TO_SAVE'	=>	'Zlyhalo uloženie obálky albumu! Chyba: [error]',
'ALBUM_COVER_SAVED_SUCCESSFULLY_2726ef'	=>	'Obal albumu bol úspešne uložený!',
'ALBUM_STATE_FAILED_TO_SAVE'	=>	'Zlyhalo uloženie stavu albumu! Chyba: [error]',
'ALBUM_STATE_SAVED_SUCCESSFULLY_486ce8'	=>	'Stav albumu bol úspešne uložený!',
'ALBUM_FAILED_TO_DELETE'	=>	'Zlyhalo vymazanie albumu! Chyba: [error]',
'ALBUM_DELETED_SUCCESSFULLY_e3df95'	=>	'Album bol úspešne vymazaný!',
// 7 language strings from file plug_cbgallery/library/CBGallery.php
'PHOTOS_5daaf2'	=>	'Fotografie',
'PHOTO_c03d53'	=>	'Fotografia',
'VIDEOS_554cfa'	=>	'Videá',
'VIDEO_34e2d1'	=>	'Video',
'MUSIC_47dcbd'	=>	'Hudba',
'FILES_91f3a2'	=>	'Súbory',
'FILE_0b2791'	=>	'Súbor',
// 1 language strings from file plug_cbgallery/library/Table/FolderTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Vlastník nie je definovaný!',
// 13 language strings from file plug_cbgallery/library/Table/ItemTable.php
'TYPE_NOT_SPECIFIED_7f3675'	=>	'Typ nie je definovaný!',
'NOTHING_TO_UPLOAD_OR_LINK_26c420'	=>	'Nie je tu nič na nahrávanie alebo odkazovanie!',
'FILE_UPLOAD_INVALID_EXT'	=>	'Nesprávna prípona súboru [extension]. Nahrávajte len súbory s príponou [extensions]!',
'FILE_UPLOAD_TOO_SMALL'	=>	'Súbor je príliš malý. Povolené minimum je [size]!',
'FILE_UPLOAD_TOO_LARGE'	=>	'Súbor je príliš veľký. Povolené maximum je [size]!',
'FILE_LINK_INVALID_URL'	=>	'Nesprávna URL adresa súboru. Uistite sa, prosím, že URL existuje!',
'FILE_LINK_INVALID_EXT'	=>	'Nesprávna prípona URL súboru [extension]. Nahrávajte len súbory s príponou [extensions]!',
'THUMBNAIL_UPLOAD_INVALID_EXT'	=>	'Nesprávna prípona náhľadového súboru [extension]. Nahrávajte len súbory s príponou [extensions]!',
'THUMBNAIL_UPLOAD_TOO_SMALL'	=>	'Súbor s náhľadom je príliš malý. Povolené minimum je [size]!',
'THUMBNAIL_UPLOAD_TOO_LARGE'	=>	'Súbor s náhľadom je príliš veľký. Povolené maximum je [size]!',
'THUMBNAIL_LINK_INVALID_URL'	=>	'Nesprávna URL adresa náhľadového súboru. Uistite sa, prosím, že URL existuje!',
'THUMBNAIL_LINK_INVALID_EXT'	=>	'Nesprávna prípona URL náhľadového súboru [extension]. Nahrávajte len súbory s príponou [extensions]!',
'FILE_FAILED_TO_UPLOAD'	=>	'Zlyhalo nahrávanie súboru [file]!',
// 5 language strings from file plug_cbgallery/library/Trigger/AdminTrigger.php
'ALBUMS_15bf55'	=>	'Albumy',
'ADD_NEW_ALBUM_327f75'	=>	'Pridať nový album',
'MEDIA_3b5635'	=>	'Médium',
'ADD_NEW_MEDIA_7d388e'	=>	'Pridať nové médium',
'CONFIGURATION_254f64'	=>	'Nastavenie',
// 2 language strings from file plug_cbgallery/library/Trigger/WorkflowTrigger.php
'GALLERY_ALBUM_APPROVALS'	=>	'%%COUNT%% Schválený album%%COUNT%% schválených albumov',
'GALLERY_MEDIA_APPROVALS'	=>	'%%COUNT%% schválené médium%%COUNT%% schválených médií',
// 26 language strings from file plug_cbgallery/templates/default/activity.php
'COUNT_TYPES'	=>	'%%COUNT%% [types]',
'GALLERY_SHORT_DATE_FORMAT'	=>	'M j, Y',
'EXTENSION_63e4e9'	=>	'Prípona',
'SIZE_6f6cb7'	=>	'Veľkosť',
'MODIFIED_35e0c8'	=>	'Upravené',
'MD5_CHECKSUM_fe243a'	=>	'Kontrolný súčet MD5',
'SHA1_CHECKSUM_c9c95c'	=>	'Kontrolný súčet SHA1',
'LIKED_YOUR_TYPE_TITLE_IN_ALBUM'	=>	'sa páčii vaša [type] [title] v albume [album]',
'LIKED_YOUR_TYPE_TITLE'	=>	'sa páči váš [type] [title]',
'LIKED_TYPE_TITLE_IN_ALBUM'	=>	'sa páči [type] [title] v albume [album]',
'LIKED_TYPE_TITLE'	=>	'sa páčii [type] [title]',
'TAGGED_YOU_IN_TYPE_TITLE_IN_ALBUM'	=>	'vás označil na [type] [title] v albume [album]',
'TAGGED_YOU_IN_TYPE_TITLE'	=>	'vás označil na [type] [title]',
'TAGGED_IN_TYPE_TITLE_IN_ALBUM'	=>	'vás označil [type] [title] v albume [album]',
'TAGGED_IN_TYPE_TITLE'	=>	'označil v [type] [title]',
'COMMENTED_ON_YOUR_TYPE_TITLE_IN_ALBUM'	=>	'komentoval [type] [title] v albume [album]',
'COMMENTED_ON_YOUR_TYPE_TITLE'	=>	'komentoval [type] [title]',
'COMMENTED_ON_TYPE_TITLE_IN_ALBUM'	=>	'komentoval [type] [title] v albume [album]',
'COMMENTED_ON_TYPE_TITLE'	=>	'komentoval [type] [title]',
'SHARED_TYPE_TITLE_IN_ALBUM'	=>	'zdieľa [type] [title] v albume [album]',
'SHARED_ON_TYPE_TITLE'	=>	'zdieľa [type] [title]',
'SHARED_COUNT_TYPES_IN_ALBUM'	=>	'zdieľané [types] v albume [album]',
'SHARED_A_TYPE_IN_ALBUM'	=>	'zdieľa [type] v albume [album]',
'SHARED_COUNT_TYPES'	=>	'Zdieľané [types]',
'SHARED_A_TYPE'	=>	'zdieľa [type]',
'SHARED_TYPE'	=>	'zdieľa [type]',
// 2 language strings from file plug_cbgallery/templates/default/activity_new.php
'HAVE_A_TYPES_TO_SHARE'	=>	'Má [types] na zdieľaniie?',
'CLICK_OR_DRAG_DROP_TO_UPLOAD_edcc07'	=>	'Kliknite alebo potiahnite & pustite pre začatie nahrávania',
// 4 language strings from file plug_cbgallery/templates/default/folder.php
'GALLERY_LONG_DATE_FORMAT'	=>	'F j, Y',
'APPROVE_6f7351'	=>	'Schváliť',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_ALBUM_53e72b'	=>	'Skutočne chcete odpublikovať tento album?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ALBUM_AND_ALL_6a52d2'	=>	'Skutočne chcete vymazať tento album a všetky súbory v ňom?',
// 2 language strings from file plug_cbgallery/templates/default/folder_container.php
'COUNT_FILES'	=>	'%%COUNT%% súbor|%%COUNT%% súborov|{0}Prázdne',
'PENDING_APPROVAL_017e8b'	=>	'Čaká na schválenie',
// 4 language strings from file plug_cbgallery/templates/default/folder_edit.php
'EDIT_ALBUM_NAME'	=>	'Upraviť album: [name]',
'NEW_ALBUM_aee35f'	=>	'Nový album',
'UPDATE_ALBUM_f6fd0f'	=>	'Aktualizovať album',
'CREATE_ALBUM_3912d2'	=>	'Vytvoriť album',
// 4 language strings from file plug_cbgallery/templates/default/gallery.php
'NEW_UPLOAD_LINK_dc24ba'	=>	'Nové nahrávanie / Odkaz',
'NEW_UPLOAD_86b5e3'	=>	'Nové nahrávanie',
'NEW_LINK_95b461'	=>	'Nový odkaz',
'BACK_0557fa'	=>	'Späť',
// 10 language strings from file plug_cbgallery/templates/default/item.php
'IN_FOLDER'	=>	'v adresári [folder]',
'MAKE_ALBUM_COVER_a51507'	=>	'Vytvoriť obálku albumu',
'ROTATE_LEFT_d82752'	=>	'Otočiť vľavo',
'ROTATE_RIGHT_f757fd'	=>	'Otočiť vpravo',
'ARE_YOU_SURE_YOU_WANT_TO_MAKE_THIS_PHOTO_YOUR_PROF_0c6da8'	=>	'Skutočne chcete z tejto fotografie vytvoriť profilovú fotografiu?',
'MAKE_PROFILE_AVATAR_9a8fbc'	=>	'Vytvoriť profilovú fotografiu',
'ARE_YOU_SURE_YOU_WANT_TO_MAKE_THIS_PHOTO_YOUR_PROF_35618e'	=>	'Skutočne chcete z tejto fotografie vytvoriť titulnú fotografiu profilu?',
'MAKE_PROFILE_CANVAS_e3d3c7'	=>	'Vytvoriť titulnú fotografiu profilu',
'ARE_YOU_SURE_UNPUBLISH_TYPE'	=>	'Skutočne chcete odpublikovať tento [type]?',
'ARE_YOU_SURE_DELETE_TYPE'	=>	'Skutočne chcete vymazať tento [type]?',
// 6 language strings from file plug_cbgallery/templates/default/item_edit.php
'ALBUM_c4f839'	=>	'Album',
'THUMBNAIL_b7c161'	=>	'Náhľad',
'UPDATE_TYPE'	=>	'Aktualizovať [type]',
'CREATE_UPLOAD_LINK_842e6d'	=>	'Vytvoriť nahrávanie / odkaz',
'CREATE_UPLOAD_ae5001'	=>	'Vytvoriť nahrávanie',
'CREATE_LINK_e6ae26'	=>	'Vytvoriť odkaz',
// 3 language strings from file plug_cbgallery/templates/default/items.php
'NO_GALLERY_SEARCH_RESULTS_FOUND_10f76f'	=>	'Neboli nájdené žiadne výsledky vo vyhľadávaní galérie.',
'THIS_ALBUM_IS_CURRENTLY_EMPTY_edd10a'	=>	'Tento album je momentálne prázdny.',
'THIS_GALLERY_IS_CURRENTLY_EMPTY_f8e6be'	=>	'Táto galéria je momentálne prázdna.',
// 5 language strings from file plug_cbgallery/templates/default/items_new.php
'OR_1d00e7'	=>	'ALEBO',
'HAVE_A_MEDIA_LINK_TO_SHARE_9d6f04'	=>	'Máte odkaz na médium na zdieľanie?',
'SHARE_5a95a4'	=>	'Zdieľať',
'ARE_YOU_SURE_YOU_ARE_DONE_ALL_UNSAVED_DATA_WILL_BE_2dff72'	=>	'Skutočne máte dokončené? Všetky neuložené údaje budú stratené!',
'DONE_f92965'	=>	'Dokončené',
// 1 language strings from file plug_cbgallery/xml/views/view.com_comprofiler.cbgalleryglobals.xml
'CUSTOM_8b9035'	=>	'vlastný',
);