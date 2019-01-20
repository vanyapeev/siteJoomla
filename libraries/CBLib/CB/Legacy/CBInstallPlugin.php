<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 6/20/14 7:18 PM $
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Xml\SimpleXMLElement;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;

defined('CBLIB') or die();

/**
 * CBInstallPlugin Class implementation
 * 
 * Used for implementing the Model for CB Plugins installation screens of CB
 * for the store() method to install plugins.
 */
class CBInstallPlugin extends Table
{
	/**
	 * @var int
	 */
	public $id = null;
	/**
	 * @var string
	 */
	public $func;
	/**
	 * @var string
	 */
	public $localdirectory;
	/**
	 * @var string
	 */
	public $packageweburl;
	/**
	 * @var string
	 */
	public $packagewebname;
	/**
	 * @var string
	 */
	public $packageurl;
	/**
	 * @var string
	 */
	public $plgfile;

	/**
	 * @var string
	 */
	private $_resultMessage	=	null;

	/**
	 *	Binds an array/hash from database to this object
	 *
	 *	@param  int $oid  optional argument, if not specifed then the value of current key is used
	 *	@return mixed     any result from the database operation
	 */
	public function load( $oid = null )
	{
		return true;
	}

	/**
	 * If table key (id) is NULL : inserts a new row
	 * otherwise updates existing row in the database table
	 *
	 * Can be overridden or overloaded by the child class
	 *
	 * @param  boolean  $updateNulls  TRUE: null object variables are also updated, FALSE: not.
	 * @return boolean                TRUE if successful otherwise FALSE
	 *
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public function store( $updateNulls = false )
	{
		cbimport( 'cb.tabs' );
		cbimport( 'cb.imgtoolbox' );
		cbimport( 'cb.adminfilesystem' );
		cbimport( 'cb.installer' );
		cbimport( 'cb.params' );
		cbimport( 'cb.pagination' );

		cbSpoofCheck( 'plugin' );
		checkCanAdminPlugins( 'core.admin' );

		ob_start();

		switch ( $this->func ) {
			case 'installPluginUpload':
				$success	=	$this->installPluginUpload();
				break;
			case 'installPluginWeb':
				$success	=	$this->installPluginURL( $this->packageweburl, $this->packagewebname );
				break;
			case 'installPluginDir':
				$success	=	$this->installPluginDir( $this->localdirectory );
				break;
			case 'installPluginURL':
				$success	=	$this->installPluginURL( $this->packageurl );
				break;
			case 'installPluginDisc':
				$success	=	$this->installPluginDisc( $this->plgfile );
				break;
			default:
				throw new \InvalidArgumentException( CBTxt::T( 'INVALID_FUNCTION', 'Invalid function' ), 500 );
		}

		$html		=	ob_get_contents();
		ob_end_clean();

		$this->_resultMessage	=	$html;

		if ( ! $success ) {
			$this->setError( 'Installation error' );
		}

		return $success;
	}

	/**
	 * After store() this function may be called to get a result information message to display. Override if it is needed.
	 *
	 * @return string|null  STRING to display or NULL to not display any information message (Default: NULL)
	 */
	public function cbResultOfStore( )
	{
		return $this->_resultMessage;
	}

	/**
	 * returns html for maximum upload file size
	 * Used by Backend XML only
	 * @deprecated Do not use directly, only for XML tabs backend
	 *
	 * @return string
	 */
	public function displayUploadMaxFilesize( )
	{
		return ini_get( 'upload_max_filesize' )
		. ' <small>(upload_max_filesize in '
		. ( is_callable( 'php_ini_loaded_file' ) && php_ini_loaded_file() ? htmlspecialchars( php_ini_loaded_file() ) : 'php.ini' )
		. ')</small>';

	}

	/**
	 * Returns HTML for "install from discovery" tab
	 * Used by Backend XML only
	 * @deprecated Do not use directly, only for XML tabs backend
	 *
	 * @return string
	 */
	public function displayDiscoveries( )
	{
		global $_CB_framework, $_CB_database;

		// Prepare array of discovered plugins (not installed, but exists):
		$allPlgsFolders										=	array();
		$discoveredPlgs										=	array();
		$existingPlgList									=	array();
		$existingPlgFolders									=	array();
		$failingXmlFiles									=	array();

		// Discovers all installed plugins
		$query												=	'SELECT ' . $_CB_database->NameQuote( 'folder' )
			.	', ' . $_CB_database->NameQuote( 'type' )
			.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin' );
		$_CB_database->setQuery( $query );
		$existingPlgs										=	$_CB_database->loadAssocList();

		// Constructs list of installed plugins': 1) folders by type ($existingPlgList) and 2) list of installed folder paths ($existingPlgFolders)
		foreach ( $existingPlgs as $existingPlg ) {
			$plgType										=	$existingPlg['type'];

			$existingPlgList[$plgType][]					=	$existingPlg['folder'];

			$existingPlgFolders[]							=	$existingPlg['type'] . '/' . $existingPlg['folder'];
		}

		// Discovers inside each type all the directories:
		foreach ( $existingPlgList as $plgType => $existingPlgs ) {
			$plgFolders										=	array_filter(
																	cbReadDirectory( $_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/' . $plgType ),
																	function ( $subSubFolder )
																	{
																		return ! in_array( $subSubFolder, array( 'index.html', 'default' ) );
																	}
																);

			// Adds each directory of each type to the list of checks:
			foreach ( $plgFolders as $plgFolder ) {
				$plgFolderAndType							=	$plgType . '/' . $plgFolder;

				$allPlgsFolders[]							=	$plgFolderAndType;

				// Checks for sub-plugins, templates and known folders that might contain plugins:
				foreach ( array( 'plugin', 'templates', 'processors', 'products' ) as $subFolder ) {
					$subfolderPath							=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/' . $plgFolderAndType . '/' . $subFolder;

					if ( file_exists( $subfolderPath ) ) {
						$subPluginsFolders					=	array_map(
							function ( $subSubFolder ) use ( $plgFolderAndType, $subFolder )
							{
								return $plgFolderAndType . '/' . $subFolder . '/' . $subSubFolder;
							},
							array_filter(
								cbReadDirectory( $subfolderPath ),
								function ( $subSubFolder )
								{
									return ! in_array( $subSubFolder, array( 'index.html', 'default' ) );
								}
							)
						);

						// Consolidates sub-folders:
						$allPlgsFolders							=	array_merge( $allPlgsFolders, $subPluginsFolders );
					}
				}
			}
		}

		// As discoveries above might lead to multiple entries depending on database of installed plugins, makes discoveries unique:
		$allPlgsFolders										=	array_unique( $allPlgsFolders );

		// Checks for each discovered folder if there are cbinstall-xml files, and if yes, if they are in the installed plugins list:
		foreach ( $allPlgsFolders as $plgFolderAndType ) {
			$plgFolderDir									=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/' . $plgFolderAndType;

			if ( ( ! is_file( $plgFolderDir ) ) && ( ! in_array( $plgFolderAndType, $existingPlgFolders ) ) ) {
				$plgFiles									=	cbReadDirectory( $plgFolderDir );

				if ( $plgFiles ) foreach ( $plgFiles as $plgFile ) {
					if ( preg_match( '/^.+\.xml$/i', $plgFile ) ) {
						$plgPath							=	$plgFolderDir . ( substr( $plgFolderDir, -1, 1 ) == '/' ? '' : '/' ) . $plgFile;
						try {
							$plgXml							=	@new SimpleXMLElement( trim( file_get_contents( $plgPath ) ) );
							$elements						=	explode( '/', $plgFolderAndType );
							$lastFolder						=	array_pop( $elements );

							if ( ( $plgXml->getName() == 'cbinstall' ) && ( $lastFolder != 'default' ) && ( ! array_key_exists( $plgFolderAndType, $discoveredPlgs ) ) ) {
								$discoveredPlgs[$plgFolderAndType]	=	array( 'name' => ( isset( $plgXml->name ) ? (string) $plgXml->name : $plgFolderAndType ), 'file' => $plgFolderAndType );
							}
						} catch ( \Exception $e ) {
							$failingXmlFiles[]				=	$plgPath;
						}
					}
				}
			}
		}

		$return				=	'';

		if ( count( $failingXmlFiles ) > 0 ) {
			$return			.=	'<div class="col-sm-12">'
							.		'<div class="col-sm-12 alert alert-danger" role="alert">'
							.			'<h4>'
							.				CBTxt::Th( 'Malformed XML files discovered in CB plugin folders:' )
							.			'</h4>';

			foreach ( $failingXmlFiles as $failedFilePath ) {
				$return		.=			'<div class="cbft_text form-group cb_form_line clearfix">'
							.				CBTxt::Th( 'XML_FILE_FILE_IS_MALFORMED', 'XML file [FILE_PATH_AND_NAME] is malformed and should be replaced or fixed, or the plugin should be removed', array( '[FILE_PATH_AND_NAME]' => '<strong>' . htmlspecialchars( $failedFilePath ) . '</strong>' ) )
							.			'</div>';
			}

			$return			.=		'</div>'
							.	'</div>';

		}

		if ( $discoveredPlgs ) {
			foreach ( $discoveredPlgs as $discoveredPlg ) {
				$return		.=		'<div class="cbft_text form-group cb_form_line clearfix">'
							.			'<div class="control-label col-sm-3">'
							.				htmlspecialchars( $discoveredPlg['name'] )
							.			'</div>'
							.			'<div class="cb_field col-sm-9">'
							.				'<input type="button" class="btn btn-primary btn-sm" value="' . htmlspecialchars( CBTxt::T( 'Install Package' ) ) . '" onclick="submitbutton( \'act=apply&amp;func=installPluginDisc&amp;plgfile=' . addslashes( $discoveredPlg['file'] ) . '\' )" />'
							.			'</div>'
							.		'</div>';
			}
		} else {
			$return			.=		'<div class="col-sm-12">'
							.			CBTxt::Th( 'No plugins discovered.' )
							.		'</div>';
		}

		return $return;
	}

	/**
	 * Returns HTML for "install from web" tab
	 * Used by Backend XML only
	 *
	 * @return string
	 */
	public function displayWeb()
	{
		global $_CB_framework;

		$cacheFile										=	$_CB_framework->getCfg( 'absolute_path' ) . '/cache/' . md5( $_CB_framework->getCfg( 'secret' ) . 'cbpluginsweb' ) . '.json';
		$webJSON										=	Application::Input()->get( 'post/webjson', null, GetterInterface::RAW );
		$packagesJSON									=	null;
		$cacheTime										=	null;
		$request										=	false;

		if ( $webJSON ) {
			// Ensure the POST was sent from install from web and ensure Super User permission before storing the JSON; DO NOT REMOVE:
			if ( cbSpoofCheck( 'webstore', 'POST', 2 ) && Application::MyUser()->isSuperAdmin() ) {
				try {
					$packagesJSON						=	new Registry( $webJSON );

					file_put_contents( $cacheFile, $packagesJSON->asJson() );
				} catch( Exception $e ) {}
			}
		} else {
			if ( file_exists( $cacheFile ) ) {
				$cacheTime								=	filemtime( $cacheFile );

				if ( intval( ( $_CB_framework->now() - $cacheTime ) / 3600 ) > 24 ) {
					$request							=	true;
				} else {
					$packagesJSON						=	new Registry( file_get_contents( $cacheFile ) );
				}
			} else {
				$request								=	true;
			}
		}

		if ( Application::Input()->get( 'format', null, GetterInterface::STRING ) != 'raw' ) {
			cbGetRegAntiSpamInputTag();

			$cbGetRegAntiSpams							=	cbGetRegAntiSpams();

			$loading									=	'<div class="cbWebLoading text-center"><span class="fa fa-spinner fa-pulse fa-3x"></span></div>';

			$js											=	"$.cbweb = function( refresh ) {"
														.		"$( '.cbWeb' ).html( '" . addslashes( $loading ) . "' );"
														.		"$.ajax({"
														.			"url: 'https://www.joomlapolis.com/index.php?option=com_comprofiler&view=pluginclass&plugin=cbpackagebuilder&action=web&format=raw' + ( refresh === true ? '&refresh=true' : '' ),"
														.			"type: 'GET',"
														.			"data: { domain: '" . addslashes( base64_encode( $_CB_framework->getCfg( 'live_site' ) ) ) . "' },"
														.			"dataType: 'jsonp',"
														.			"crossDomain: true,"
														.			"cache: false"
														.		"}).fail( function( jqXHR, textStatus, errorThrown ) {"
														.			"$( '.cbWebLoading' ).remove();"
														.			"$( '.cbWeb' ).html( '" . addslashes( CBTxt::T( 'Failed to access install from web data at <a href="https://www.joomlapolis.com/" target="_blank" rel="nofollow">https://www.joomlapolis.com/</a> from your browser. Please check your internet connection or any browser-side Request Policy setting and retry.' ) ) . "' );"
														.		"}).done( function( webJSON, textStatus, jqXHR ) {"
														.			"if ( webJSON ) {"
														.				"$.ajax({"
														.					"url: '" . $_CB_framework->backendViewUrl( 'installfromweb', false, array(), 'raw' ) . "',"
														.					"type: 'POST',"
														.					"data: {"
														.						"webjson: JSON.stringify( webJSON ),"
														.						cbSpoofField() . ": '" . addslashes( cbSpoofString( null, 'webstore' ) ) . "',"
														.						cbGetRegAntiSpamFieldName() . ": '" . addslashes( $cbGetRegAntiSpams[0] ) . "'"
														.					"},"
														.					"dataType: 'html',"
														.					"cache: false"
														.				"}).fail( function( jqXHR, textStatus, errorThrown ) {"
														.					"$( '.cbWebLoading' ).remove();"
														.					"$( '.cbWeb' ).html( '" . addslashes( CBTxt::T( 'Failed to load install from web. Please reload to retry.' ) ) . "' );"
														.				"}).done( function( webHtml, textStatus, jqXHR ) {"
														.					"$( '.cbWebLoading' ).remove();"
														.					"$( '.cbWeb' ).html( webHtml );"
														.					"$( '.cbWeb' ).find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();"
														.				"});"
														.			"} else {"
														.				"$( '.cbWebLoading' ).remove();"
														.				"$( '.cbWeb' ).html( '" . addslashes( CBTxt::T( 'Failed to load install from web data at <a href="https://www.joomlapolis.com/" target="_blank" rel="nofollow">https://www.joomlapolis.com/</a> from your browser. Please check your internet connection or any browser-side Request Policy setting and retry.' ) ) . "' );"
														.			"}"
														.		"});"
														.	"};"
														.	"$( '.cbWeb' ).on( 'click', '.cbWebType > a', function( e ) {"
														.		"e.preventDefault();"
														.		"$( '.cbWebType,.cbWebSubType' ).removeClass( 'active' );"
														.		"$( '.cbWebPackagesType,.cbWebSubTypes' ).addClass( 'hidden' );"
														.		"$( this ).parent().addClass( 'active' );"
														.		"$( this ).siblings( '.cbWebSubTypes' ).removeClass( 'hidden' );"
														.		"$( '.cbWebPackagesType[data-cbweb-type=\"' + $( this ).data( 'cbweb-type' ) + '\"]' ).removeClass( 'hidden' );"
														.		"if ( $( this ).parent().hasClass( 'cbWebTypeEmpty' ) ) {"
														.			"$( this ).parent().find( '.cbWebSubType:first > a' ).click()"
														.		"}"
														.	"}).on( 'click', '.cbWebSubType > a', function( e ) {"
														.		"e.preventDefault();"
														.		"$( '.cbWebSubType' ).removeClass( 'active' );"
														.		"$( '.cbWebPackagesType' ).addClass( 'hidden' );"
														.		"$( this ).parent().addClass( 'active' );"
														.		"$( '.cbWebPackagesType[data-cbweb-type=\"' + $( this ).data( 'cbweb-type' ) + '\"]' ).removeClass( 'hidden' );"
														.	"}).on( 'click', '.cbWebPackageDownload', function( e ) {"
														.		"e.preventDefault();"
														.		"$( '#packagewebname' ).val( $( this ).data( 'cbweb-file' ) );"
														.		"$( '#packageweburl' ).val( $( this ).data( 'cbweb-download' ) );"
														.		"submitbutton( 'act=apply&func=installPluginWeb' );"
														.	"}).on( 'click', '.cbWebRefresh', function( e ) {"
														.		"e.preventDefault();"
														.		"$.cbweb( true );"
														.	"});"
														.	"$( '.cb_packageinstaller' ).on( 'cbpackagebuilder.install.done', function() {"
														.		"$.cbweb( true );"
														.	"});";

			if ( $request ) {
				$js										.=	"$.cbweb();";
			}

			$_CB_framework->outputCbJQuery( $js );

			initToolTip();

			if ( $request ) {
				return '<div class="cbWeb">' . $loading . '</div>';
			}
		}

		if ( $packagesJSON ) {
			$packages									=	$packagesJSON->subTree( 'packages.' . ( checkJversion() >= 2 ? 'j30' : 'j15' ) );
		} else {
			$packages									=	false;
		}

		if ( $packages === false ) {
			return CBTxt::T( 'Failed to load install from web. Please reload to retry.' );
		} elseif ( ! $packages ) {
			return CBTxt::T( 'There are no packages available at this time.' );
		}

		$news											=	$packagesJSON->get( 'news', null, GetterInterface::HTML );
		$menu											=	null;
		$new											=	array();
		$popular										=	array();
		$updates										=	array();
		$items											=	array( 'home' => array(), 'updates' => array() );

		// JSON moved presets to the top as its key is 0. Move it back to the bottom:
		if ( $packages->has( '0' ) ) {
			$presets									=	$packages->subTree( '0' );

			$packages->unsetEntry( '0' );

			$packages->set( '0', $presets );
		}

		// Parse through the packages and build the menu structure from Type > Subtypes:
		foreach ( $packages as $typeId => $type ) {
			/** @var ParamsInterface $type */
			$typeValue									=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', $typeId ) );

			switch ( $typeId ) {
				case '0': // Presets
				case '1': // Joomla Packages
					$icon								=	'cubes';
					break;
				case '3': // Joomla Components
					$icon								=	'gears';
					break;
				case '2': // Joomla Libraries
					$icon								=	'book';
					break;
				case '6': // Joomla Languages
					$icon								=	'comments-o';
					break;
				case '7': // Joomla Templates
					$icon								=	'eye';
					break;
				case '4': // Joomla Plugins
				case '8': // Community Builder Plugins
					$icon								=	'plug';
					break;
				case '5': // Joomla Modules
					$icon								=	'cube';
					break;
				default:
					$icon								=	'puzzle-piece';
					break;
			}

			$typePackages								=	array();

			// We need to keep track of new, popular, and packages with updates; lets also pre-process version check:
			foreach ( $type->subTree( 'packages' ) as $packageId => $typePackage ) {
				/** @var ParamsInterface $typePackage */
				$isLatest								=	$this->checkPackageVersion( $typePackage );

				$typePackage->set( 'latest', $isLatest );

				$pkgTypeId								=	( $typeId == '0' ? 'preset_' : 'package_' ) . $packageId;

				if ( $typePackage->get( 'new', false, GetterInterface::BOOLEAN ) ) {
					$new[$pkgTypeId]					=	$typePackage;
				} elseif ( $typePackage->get( 'popular', false, GetterInterface::BOOLEAN ) ) {
					$popular[$pkgTypeId]				=	$typePackage;
				}

				if ( $isLatest === -1 ) {
					if ( ! in_array( $packageId, $updates ) ) {
						$updates[]						=	$packageId;
					}

					$items['updates'][$pkgTypeId]		=	$typePackage;
				}

				$typePackages[$pkgTypeId]				=	$typePackage;
			}

			$items[$typeValue]							=	$typePackages;

			$menu										.=	'<li class="cbWebType' . ( ! count( $items[$typeValue] ) ? ' cbWebTypeEmpty' : null ) . '">'
														.		'<a href="javascript: void(0);" class="clearfix" style="border-radius: 0; margin: 0 0 2px 0;" data-cbweb-type="' . htmlspecialchars( $typeValue ) . '">'
														.			'<span class="fa fa-' . htmlspecialchars( $icon ) . '" style="width: 20px;"></span> ' . $type->get( 'label', null, GetterInterface::HTML )
														.			'<span class="badge badge-primary pull-right" style="font-size: 75%;">' . $type->get( 'count', 0, GetterInterface::INT ) . '</span>'
														.		'</a>'
														.		'<ul class="cbWebSubTypes nav nav-pills nav-stacked hidden">';

			foreach ( $type->subTree( 'subtypes' ) as $subTypeId => $subType ) {
				/** @var ParamsInterface $subType */
				$subTypeValue							=	$typeValue . '_' . strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', $subTypeId ) );

				$menu									.=			'<li class="cbWebSubType">'
														.				'<a href="javascript: void(0);" class="clearfix" style="border-radius: 0; margin: 0 0 2px 0;" data-cbweb-type="' . htmlspecialchars( $subTypeValue ) . '">'
														.					'<span class="fa fa-level-down" style="width: 20px;"></span>'
														.					'<span class="fa fa-' . htmlspecialchars( $icon ) . '" style="width: 20px;"></span> ' . $subType->get( 'label', null, GetterInterface::HTML )
														.					'<span class="badge badge-primary pull-right" style="font-size: 75%;">' . $subType->get( 'count', 0, GetterInterface::INT ) . '</span>'
														.				'</a>'
														.			'</li>';

				$subTypePackages						=	array();

				// We need to keep track of new, popular, and packages with updates; lets also pre-process version check:
				foreach ( $subType->subTree( 'packages' ) as $packageId => $subTypePackage ) {
					/** @var ParamsInterface $subTypePackage */
					$isLatest							=	$this->checkPackageVersion( $subTypePackage );

					$subTypePackage->set( 'latest', $isLatest );

					$pkgTypeId							=	( $typeId == '0' ? 'preset_' : 'package_' ) . $packageId;

					if ( $subTypePackage->get( 'new', false, GetterInterface::BOOLEAN ) ) {
						$new[$pkgTypeId]				=	$subTypePackage;
					} elseif ( $subTypePackage->get( 'popular', false, GetterInterface::BOOLEAN ) ) {
						$popular[$pkgTypeId]			=	$subTypePackage;
					}

					if ( $isLatest === -1 ) {
						if ( ! in_array( $packageId, $updates ) ) {
							$updates[]					=	$packageId;
						}

						$items['updates'][$pkgTypeId]	=	$subTypePackage;
					}

					$subTypePackages[$pkgTypeId]		=	$subTypePackage;
				}

				$items[$subTypeValue]					=	$subTypePackages;
			}

			$menu										.=		'</ul>'
														.	'</li>';
		}

		// Limit home to just the first 6 packages:
		$items['home']									=	array_slice( ( $new + $popular ), 0, 6, true );

		// Build the update all url if there are updates available:
		$updateAll										=	null;

		if ( $updates ) {
			$updateAll									=	'https://www.joomlapolis.com/index.php?option=com_comprofiler&view=pluginclass&plugin=cbpackagebuilder&action=packages&func=download&packages=' . implode( ',', cbToArrayOfInt( $updates ) ) . '&token=' . urlencode( $packagesJSON->get( 'token', null, GetterInterface::STRING ) ) . '&format=raw';
		}

		$return											=	( ! $request ? '<div class="cbWeb">' : null )
														.	'<div class="cbWebResponse row">'
														.		'<div class="cbWebMenu col-sm-3" style="margin-bottom: 15px;">'
														.			'<ul class="cbWebTypes nav nav-pills nav-stacked bg-muted">';

		if ( $news || $items['home'] ) {
			$return										.=				'<li class="cbWebType active">'
														.					'<a href="javascript: void(0);" style="border-radius: 0; margin: 0 0 2px 0;" data-cbweb-type="home">'
														.						'<span class="fa fa-home" style="width: 20px;"></span> ' . CBTxt::T( 'Home' )
														.						( $items['home'] ? '<span class="badge badge-primary pull-right" style="font-size: 75%;">' . count( $items['home'] ) . '</span>' : null )
														.					'</a>'
														.				'</li>';
		}

		if ( $items['updates'] ) {
			$return										.=				'<li class="cbWebType">'
														.					'<a href="javascript: void(0);" style="border-radius: 0; margin: 0 0 2px 0;" data-cbweb-type="updates">'
														.						'<span class="fa fa-undo" style="width: 20px;"></span> ' . CBTxt::T( 'Updates' )
														.						'<span class="badge badge-primary pull-right" style="font-size: 75%;">' . count( $items['updates'] ) . '</span>'
														.					'</a>'
														.				'</li>';
		}

		$return											.=				$menu
														.			'</ul>'
														.		'</div>'
														.		'<div class="cbWebPackages col-sm-9">';

		if ( $news ) {
			$return										.=			'<div class="cbWebPackagesType row" data-cbweb-type="home" style="margin-bottom: 15px;">'
														.				'<div class="cbWebPackageNews col-sm-12">'
														.					$news // HTML filtered above at definition
														.					( $items['home'] ? '<hr />' : null )
														.				'</div>'
														.			'</div>';
		}

		$n												=	0;

		// Parse through the items to output their package containers:
		foreach ( $items as $type => $typeItems ) {
			$i											=	0;

			$return										.=			'<div class="cbWebPackagesType row' . ( $type !== 'home' ? ' hidden' : null ) . '" data-cbweb-type="' . htmlspecialchars( $type ) . '">';

			if ( $type == 'updates' ) {
				$return									.=				'<div class="cbWebUpdateAll col-sm-12 text-right" style="margin-bottom: 10px;">'
														.					'<a href="javascript: void(0);" data-cbweb-file="' . htmlspecialchars( CBTxt::T( 'Update All' ) ) . '" data-cbweb-download="' . htmlspecialchars( $updateAll ) . '" class="cbWebPackageDownload btn btn-sm btn-primary"><span class="fa fa-download"></span> ' . CBTxt::T( 'Update All' ) . '</a>'
														.				'</div>';
			}

			foreach ( $typeItems as $itemId => $item ) {
				/** @var ParamsInterface $item */
				$return									.=				'<div class="cbWebPackage col-sm-6"' . ( $i % 2 ? null : ' style="clear: both;"' ) . '>'
														.					'<div class="panel panel-default" style="border-radius: 0;">'
														.						'<div class="cbWebPackageHeader panel-heading" style="background-image: none;">'
														.							'<div class="row">'
														.								'<div class="col-md-6 text-wrap">'
														.									'<strong>' . ( $item->get( 'url', null, GetterInterface::STRING ) ? '<a href="' . htmlspecialchars( $item->get( 'url', null, GetterInterface::STRING ) ) . '" target="_blank" rel="nofollow">' . $item->get( 'name', null, GetterInterface::HTML ) . '</a>' : $item->get( 'name', null, GetterInterface::HTML ) ) . '</strong>'
														.									( $item->get( 'popular', false, GetterInterface::BOOLEAN ) ? ' <span class="cbWebPackagePopular label label-danger">' . CBTxt::T( 'Hot' ) . '</span>' : null )
														.									( $item->get( 'new', false, GetterInterface::BOOLEAN ) ? ' <span class="cbWebPackageNew label label-success">' . CBTxt::T( 'New' ) . '</span>' : null )
														.								'</div>'
														.								'<div class="col-md-6 text-right">';

				if ( ! $item->get( 'conditioned', false, GetterInterface::BOOLEAN ) ) {
					if ( $item->get( 'type', null, GetterInterface::INT ) == 12 ) {
						$return							.=									'<a href="' . htmlspecialchars( $item->get( 'download', null, GetterInterface::STRING ) ) . '" target="_blank" rel="nofollow" class="btn btn-xs btn-primary">' . CBTxt::T( 'Download' ) . '</a>';
					} else {
						if ( $item->get( 'latest', 0, GetterInterface::INT ) === 1 ) {
							$return						.=									'<a href="javascript: void(0);" data-cbweb-file="' . htmlspecialchars( $item->get( 'name', null, GetterInterface::STRING ) ) . '" data-cbweb-download="' . htmlspecialchars( $item->get( 'download', null, GetterInterface::STRING ) ) . '" class="cbWebPackageDownload btn btn-xs btn-default">' . CBTxt::T( 'Already Installed' ) . '</a>';
						} elseif ( $item->get( 'latest', 0, GetterInterface::INT ) === -1 ) {
							$return						.=									'<a href="javascript: void(0);" data-cbweb-file="' . htmlspecialchars( $item->get( 'name', null, GetterInterface::STRING ) ) . '" data-cbweb-download="' . htmlspecialchars( $item->get( 'download', null, GetterInterface::STRING ) ) . '" class="cbWebPackageDownload btn btn-xs btn-danger">' . CBTxt::T( 'Download & Update' ) . '</a>';
						} else {
							$return						.=									'<a href="javascript: void(0);" data-cbweb-file="' . htmlspecialchars( $item->get( 'name', null, GetterInterface::STRING ) ) . '" data-cbweb-download="' . htmlspecialchars( $item->get( 'download', null, GetterInterface::STRING ) ) . '" class="cbWebPackageDownload btn btn-xs btn-primary">' . CBTxt::T( 'Download & Install' ) . '</a>';
						}
					}
				} else {
					$button								=	$item->get( 'button', null, GetterInterface::HTML );

					if ( ! $button ) {
						$button							=	CBTxt::T( 'Download' );
					}

					$return								.=									'<a href="' . htmlspecialchars( $item->get( 'download', null, GetterInterface::STRING ) ) . '" target="_blank" rel="nofollow" class="btn btn-xs btn-primary">' . $button . '</a>';
				}

				$return									.=								'</div>'
														.							'</div>'
														.						'</div>'
														.						( $item->get( 'description', null, GetterInterface::HTML ) ? '<div class="cbWebPackageDescription panel-body text-wrap">' . $item->get( 'description', null, GetterInterface::HTML ) . '</div>' : null )
														.						'<div class="cbWebPackageDetails panel-footer bg-default" style="border-radius: 0;">'
														.							'<div class="row">'
														.								'<div class="col-sm-8 text-wrapall">'
														.									htmlspecialchars( $item->get( 'version', null, GetterInterface::STRING ) )
														.								'</div>'
														.								'<div class="col-sm-4 text-wrap text-right">'
														.									htmlspecialchars( $item->get( 'date', null, GetterInterface::STRING ) )
														.								'</div>'
														.							'</div>'
														.						'</div>'
														.					'</div>'
														.				'</div>';

				$i++;
			}

			$return										.=			'</div>';

			$n++;
		}

		$return											.=		'</div>'
														.	'</div>';

		if ( $cacheTime ) {
			$return										.=	'<div class="cbWebCached text-right text-muted">'
														.		CBTxt::T( 'INSTALLFROMWEB_WILL_REFRESH_IN', 'will refresh [in] [refresh]', array( '[in]' => cbFormatDate( Application::Date( $cacheTime, 'UTC' )->modify( '+24 HOURS' )->getTimestamp(), false, 'timeago' ), '[refresh]' => '<a href="javascript: void(0);" class="cbWebRefresh"><span class="fa fa-refresh"></span></a>' ) )
														.	'</div>';
		}

		$return											.=	'<input name="packagewebname" id="packagewebname" type="hidden" value="" />'
														.	'<input name="packageweburl" id="packageweburl" type="hidden" value="" />'
														.	( ! $request ? '</div>' : null );

		return $return;
	}

	/**
	 * Checks an install from web packages version
	 *
	 * @param ParamsInterface $package
	 * @return int
	 */
	private function checkPackageVersion( $package )
	{
		global $_CB_database, $_PLUGINS;

		static $installedPlugins				=	null;
		static $installedExtensions				=	null;

		if ( $installedPlugins === null ) {
			$query								=	'SELECT *'
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin' );
			$_CB_database->setQuery( $query );
			$installedPlugins					=	$_CB_database->loadObjectList();
		}

		if ( $installedExtensions === null ) {
			$query								=	'SELECT *'
												.	"\n FROM " . $_CB_database->NameQuote( '#__extensions' );
			$_CB_database->setQuery( $query );
			$installedExtensions				=	$_CB_database->loadObjectList();
		}

		$isLatest								=	0; // Not Installed

		// Lets see if we can find an existing plugin or extension either by name or by folder/element match so we can compare versions:
		if ( $package->get( 'version', null, GetterInterface::STRING ) ) {
			if ( $package->get( 'type', null, GetterInterface::INT ) == 8 ) {
				foreach ( $installedPlugins as $installedPlugin ) {
					$plgName					=	$package->get( 'name', GetterInterface::STRING );
					$plgFolder					=	trim( str_replace( $package->get( 'version', null, GetterInterface::STRING ), '', pathinfo( $package->get( 'file', null, GetterInterface::STRING ), PATHINFO_FILENAME ) ), '_ -' );

					// Lets try to guess a partial match:
					if ( $installedPlugin->type == 'language' ) {
						if ( $installedPlugin->element == 'cbpaidsubscriptions_language' ) {
							$guessMatch			=	preg_match( '/cbsubs_\w+_' . str_replace( 'cbpaidsubscriptions-', '', $installedPlugin->folder ) . '$/i', $plgFolder );
						} else {
							$guessMatch			=	preg_match( '/language_\w+_' . preg_quote( $installedPlugin->folder ) . '$/i', $plgFolder );
						}
					} else {
						$guessMatch				=	preg_match( '/' . preg_quote( str_replace( 'plug_', '', $installedPlugin->folder ) ) . '$/i', $plgFolder );
					}

					if ( ( $plgName == $installedPlugin->name ) || ( $plgFolder == $installedPlugin->folder ) || $guessMatch ) {
						$currentVersion			=	$_PLUGINS->getPluginVersion( $installedPlugin->id, true, 24 );

						if ( ! $currentVersion ) {
							break;
						}

						$versionCompare			=	str_replace( '+build.', '+', $currentVersion );

						if ( strpos( $package->get( 'version', null, GetterInterface::STRING ), '+build' ) === false ) {
							// Stable doesn't store metadata in XML so we need to remove it before comparing:
							$latestCompare		=	preg_replace( '/\+.*/', '', $package->get( 'version', null, GetterInterface::STRING ) );
						} else {
							$latestCompare		=	str_replace( '+build.', '+', $package->get( 'version', null, GetterInterface::STRING ) );
						}

						if ( $versionCompare == $latestCompare ) {
							$isLatest			=	1; // Up to Date
						} elseif ( version_compare( $latestCompare, $versionCompare, '>' ) ) {
							$isLatest			=	-1; // Out of Date
						} else {
							$isLatest			=	1; // Up to Date
						}

						if ( ! $guessMatch ) {
							// We only want to give up checking IF we know it's an exact match; continue checking for an exact match for guesses:
							break;
						}
					}
				}
			} elseif ( ( $package->get( 'type', null, GetterInterface::INT ) >= 1 ) && ( $package->get( 'type', null, GetterInterface::INT ) <= 7 ) ) {
				foreach ( $installedExtensions as $installedExtension ) {
					switch ( $package->get( 'type', null, GetterInterface::INT ) ) {
						case 1:
							$extType			=	'package';
							break;
						case 2:
							$extType			=	'library';
							break;
						case 3:
							$extType			=	'component';
							break;
						case 4:
							$extType			=	'plugin';
							break;
						case 5:
							$extType			=	'module';
							break;
						case 6:
							$extType			=	'language';
							break;
						case 7:
							$extType			=	'template';
							break;
						default:
							continue 2;
							break;
					}

					if ( $installedExtension->type != $extType ) {
						continue;
					}

					$extName					=	$package->get( 'name', GetterInterface::STRING );
					$extFolder					=	trim( str_replace( $package->get( 'version', null, GetterInterface::STRING ), '', pathinfo( $package->get( 'file', null, GetterInterface::STRING ), PATHINFO_FILENAME ) ), '_ -' );
					$guessMatch					=	preg_match( '/' . preg_quote( $installedExtension->element ) . '$/i', $extFolder );

					if ( ( $extName == $installedExtension->name ) || ( $extFolder == $installedExtension->element ) || $guessMatch ) {
						if ( ! $installedExtension->manifest_cache ) {
							break;
						}

						if ( $extFolder == 'pkg_communitybuilder' ) {
							// CBs version is more accurately checked using CB Core version since we don't give Joomla the +build version:
							$currentVersion		=	$_PLUGINS->getPluginVersion( 1, true, 24 );
						} else {
							$extDetails			=	new Registry( $installedExtension->manifest_cache );
							$currentVersion		=	$extDetails->get( 'version', null, GetterInterface::STRING );
						}

						if ( ! $currentVersion ) {
							break;
						}

						$versionCompare			=	str_replace( '+build.', '+', $currentVersion );

						if ( ( $extFolder == 'pkg_communitybuilder' ) || ( strpos( $currentVersion, '+build' ) !== false ) ) {
							if ( strpos( $package->get( 'version', null, GetterInterface::STRING ), '+build' ) === false ) {
								// Stable doesn't store metadata in XML so we need to remove it before comparing:
								$latestCompare	=	preg_replace( '/\+.*/', '', $package->get( 'version', null, GetterInterface::STRING ) );
							} else {
								$latestCompare	=	str_replace( '+build.', '+', $package->get( 'version', null, GetterInterface::STRING ) );
							}
						} else {
							// Joomla doesn't typically contain metadata so if it doesn't already have metadata then don't compare (e.g. don't check nightly against stable):
							$latestCompare		=	preg_replace( '/\+.*/', '', $package->get( 'version', null, GetterInterface::STRING ) );
						}

						if ( $versionCompare == $latestCompare ) {
							$isLatest			=	1; // Up to Date
						} elseif ( version_compare( $latestCompare, $versionCompare, '>' ) ) {
							$isLatest			=	-1; // Out of Date
						} else {
							$isLatest			=	1; // Up to Date
						}

						if ( ! $guessMatch ) {
							// We only want to give up checking IF we know it's an exact match; continue checking for an exact match for guesses:
							break;
						}
					}
				}
			}
		}

		return $isLatest;
	}

	/**
	 * Installs plugin by upload from URL
	 *
	 * @return boolean
	 */
	private	function installPluginUpload()
	{
		global $_FILES;

		// Try extending time, as unziping/ftping took already quite some... :
		@set_time_limit( 240 );

		_CBsecureAboveForm('showPlugins');

		outputCbTemplate( 2 );
		outputCbJs( 2 );
		initToolTip( 2 );

		$installer	=	new cbInstallerPlugin();

		// Check if file uploads are enabled
		if ( ! (bool) ini_get( 'file_uploads' ) ) {
			cbInstaller::showInstallMessage(
				CBTxt::T('The installer cannot continue before file uploads are enabled. Please use the install from directory method.'),
				CBTxt::T('Installer - Error'),
				false
			);
			return false;
		}

		// Check that the zlib is available
		if( ! extension_loaded( 'zlib' ) ) {
			cbInstaller::showInstallMessage(
				CBTxt::T('The installer cannot continue before zlib is installed'),
				CBTxt::T('Installer - Error'),
				false
			);
			return false;
		}

		$userfile				=	cbGetParam( $_FILES, 'uploadfile', null );

		if ( ! $userfile || ( $userfile == null ) ) {
			cbInstaller::showInstallMessage(
				CBTxt::T('No file selected'),
				CBTxt::T('Upload new plugin - error'),
				false
			);
			return false;
		}

//	$userfile['tmp_name']	=	stripslashes( $userfile['tmp_name'] );
//	$userfile['name']		=	stripslashes( $userfile['name'] );

		$msg		=	'';
		$localName	=	$_FILES['uploadfile']['name'];
		$resultdir	=	$this->uploadFile( $_FILES['uploadfile']['tmp_name'], $localName , $msg );		// $localName is updated here

		if ( $resultdir === false ) {
			cbInstaller::showInstallMessage(
				$msg,
				CBTxt::T( 'UPLOAD_ERROR', 'Upload Error' ),
				false
			);
			return false;
		}

		if ( ! $installer->upload( $localName ) ) {
			if ( $installer->unpackDir() ) {
				$installer->cleanupInstall( $localName, $installer->unpackDir() );
			}
			cbInstaller::showInstallMessage(
				$installer->getError(),
				CBTxt::T( 'UPLOAD_FAILED', 'Upload Failed'),
				false
			);
			return false;
		}

		$ret	=	$installer->install();

		$installer->cleanupInstall( $localName, $installer->unpackDir() );

		cbInstaller::showInstallMessage(
			$installer->getError(),
			( $ret ? CBTxt::T( 'UPLOAD_SUCCESS', 'Upload Success' ) : CBTxt::T( 'UPLOAD_FAILED', 'Upload Failed' ) ),
			$ret
		);

		$installer->cleanupInstall( $localName, $installer->unpackDir() );

		return $ret;
	}

	/**
	 * Changes the permissions of file
	 *
	 * @param  string   $filename  Filename with path
	 * @return boolean             Success
	 */
	private function _cbAdmin_chmod( $filename )
	{
		global $_CB_framework;

		cbimport( 'cb.adminfilesystem' );
		$adminFS			=	cbAdminFileSystem::getInstance();

		$origmask			=	null;
		if ( $_CB_framework->getCfg( 'dirperms' ) == '' ) {
			// rely on umask
			// $mode			=	0777;
			return true;
		} else {
			$origmask		=	@umask( 0 );
			$mode			=	octdec( $_CB_framework->getCfg( 'dirperms' ) );
		}

		$ret				=	$adminFS->chmod( $filename, $mode );

		if ( isset( $origmask ) ) {
			@umask( $origmask );
		}
		return $ret;
	}

	/**
	 * Uploads a file into the filesystem
	 *
	 * @param  string  $filename       Input filename for move_uploaded_file()
	 * @param  string  $userfile_name  INPUT+OUTPUT: Destination filesname
	 * @param  string  $msg            OUTPUT: Message for user
	 * @return boolean                 Success
	 */
	private function uploadFile( $filename, &$userfile_name, &$msg )
	{
		global $_CB_framework;

		cbimport( 'cb.adminfilesystem' );
		$adminFS			=	cbAdminFileSystem::getInstance();

		$baseDir			=	_cbPathName( $_CB_framework->getCfg('tmp_path') );
		$userfile_name		=	$baseDir . $userfile_name;		// WARNING: this parameter is returned !

		if ( $adminFS->file_exists( $baseDir ) ) {
			if ( $adminFS->is_writable( $baseDir ) ) {
				if ( move_uploaded_file( $filename, $userfile_name ) ) {
//			    if ( $this->_cbAdmin_chmod( $userfile_name ) ) {
					return true;
//				} else {
//					$msg = CBTxt::T('Failed to change the permissions of the uploaded file.');
//				}
				} else {
					$msg = sprintf( CBTxt::T('Failed to move uploaded file to %s directory.'), '<code>' . htmlspecialchars( $baseDir ) . '</code>' );
				}
			} else {
				$msg = sprintf( CBTxt::T('Upload failed as %s directory is not writable.'), '<code>' . htmlspecialchars( $baseDir ) . '</code>' );
			}
		} else {
			$msg = sprintf( CBTxt::T('Upload failed as %s directory does not exist.'), '<code>' . htmlspecialchars( $baseDir ) . '</code>' );
		}
		return false;
	}

	/**
	 * Installs the plugin From Directory
	 *
	 * @param  string   $userfile  Filename
	 * @return boolean             Success
	 */
	private function installPluginDir( $userfile )
	{
		// Try extending time, as unziping/ftping took already quite some... :
		@set_time_limit( 240 );

		_CBsecureAboveForm('showPlugins');

		outputCbTemplate( 2 );
		outputCbJs( 2 );
		initToolTip( 2 );

		$installer = new cbInstallerPlugin();

		// Check if file name exists
		if ( ! $userfile ) {
			cbInstaller::showInstallMessage(
				CBTxt::T('No file selected'),
				CBTxt::T('Install new plugin from directory - error'),
				false
			);
			return false;
		}

		$path = _cbPathName( $userfile );
		if (!is_dir( $path )) {
			$path = dirname( $path );
		}

		$ret = $installer->install( $path);

		cbInstaller::showInstallMessage(
			$installer->getError(),
			sprintf( CBTxt::T('Install new plugin from directory %s'), $userfile ) . ' - ' . ( $ret ? CBTxt::T('Success') : CBTxt::T('Failed') ),
			$ret
		);

		return $ret;
	}

	/**
	 * Installs the plugin From URL
	 *
	 * @param  string $userfileURL   Url
	 * @param  string $userfileTitle Title
	 * @return boolean                Success
	 */
	private function installPluginURL( $userfileURL, $userfileTitle = null )
	{
		global $_CB_framework;

		// Try extending time, as unziping/ftping took already quite some... :
		@set_time_limit( 240 );

		_CBsecureAboveForm('showPlugins');

		outputCbTemplate( 2 );
		outputCbJs( 2 );
		initToolTip( 2 );

		$installer = new cbInstallerPlugin();

		// Check that the zlib is available
		if( ! extension_loaded( 'zlib' ) ) {
			cbInstaller::showInstallMessage(
				CBTxt::T('The installer cannot continue before zlib is installed'),
				CBTxt::T('Installer - Error'),
				false
			);
			return false;
		}

		if ( ! $userfileURL ) {
			cbInstaller::showInstallMessage(
				CBTxt::T('No URL selected'),
				CBTxt::T('Upload new plugin - error'),
				false
			);
			return false;
		}


		cbimport( 'cb.adminfilesystem' );
		$adminFS			=	cbAdminFileSystem::getInstance();

		if ( $adminFS->isUsingStandardPHP() ) {
			$baseDir		=	_cbPathName( $_CB_framework->getCfg('tmp_path') );
		} else {
			$baseDir		=	$_CB_framework->getCfg( 'absolute_path' ) . '/tmp/';
		}
		$userfileName		=	$baseDir . 'comprofiler_temp.zip';


		$msg			=	'';

		$resultdir		=	$this->uploadFileURL( $userfileURL, $userfileName, $msg, $userfileTitle );

		if ( $resultdir === false ) {
			cbInstaller::showInstallMessage(
				$msg,
				sprintf(CBTxt::T('Download %s - Download Error'), ( $userfileTitle ? $userfileTitle : $userfileURL )),
				false
			);
			return false;
		}

		if ( ! $installer->upload( $userfileName ) ) {
			cbInstaller::showInstallMessage(
				$installer->getError(),
				sprintf(CBTxt::T('Download %s - Upload Failed'), ( $userfileTitle ? $userfileTitle : $userfileURL )),
				false
			);
			return false;
		}

		$ret = $installer->install();

		cbInstaller::showInstallMessage(
			$installer->getError(),
			sprintf( CBTxt::T('Download %s'), ( $userfileTitle ? $userfileTitle : $userfileURL ) ) . ' - ' . ( $ret ? CBTxt::T('Success') : CBTxt::T('Failed') ),
			$ret
		);

		$installer->cleanupInstall( $userfileName, $installer->unpackDir() );

		return $ret;
	}

	/**
	 * Installs the plugin By in-place Discovery
	 *
	 * @param  string   $plgFile  Directory discovered
	 * @return boolean            Success
	 */
	private function installPluginDisc( $plgFile )
	{
		global $_CB_framework;

		// Try extending time, as unziping/ftping took already quite some... :
		@set_time_limit( 240 );

		_CBsecureAboveForm( 'showPlugins' );

		outputCbTemplate( 2 );
		outputCbJs( 2 );
		initToolTip( 2 );

		$installer	=	new cbInstallerPlugin();

		// Check if file xml exists
		if ( ! $plgFile ) {
			cbInstaller::showInstallMessage(
				CBTxt::T( 'No file selected' ),
				CBTxt::T( 'Install new plugin from discovery - error' ),
				false
			);
			return false;
		}

		$path		=	_cbPathName( $_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/' . $plgFile );

		if ( ! is_dir( $path ) ) {
			$path	=	dirname( $path );
		}

		if ( ! is_dir( $path ) ) {
			cbInstaller::showInstallMessage(
				CBTxt::T( 'FILE_DOES_NOT_EXIST_FILE', 'File does not exist - [file]', array( '[file]' => $path ) ),
				CBTxt::T( 'INSTALL_NEW_PLUGIN_FROM_DISCOVERY_ERROR', 'Install new plugin from discovery - error' ),
				false
			);
			return false;
		}

		$ret		=	$installer->install( $path, true );

		cbInstaller::showInstallMessage(
			$installer->getError(),
			CBTxt::T( 'INSTALL_NEW_PLUGIN_FROM_DISCOVERY_ERROR_FILE_STATUS', 'Install new plugin from discovery - [file] - [status]',
				array( '[file]' => $path, '[status]' => ( $ret ? CBTxt::T( 'Success' ) : CBTxt::T( 'Failed' ) ) )
			),
			$ret
		);

		return $ret;
	}

	/**
	 * Uploads a file from a Url into a file on the filesystem
	 *
	 * @param  string  $userfileURL    Url
	 * @param  string  $userfile_name  INPUT+OUTPUT: Destination filesname
	 * @param  string  $msg            OUTPUT: Message for user
	 * @param  string  $userfileTitle  Title of what is being downloaded
	 * @return boolean                 Success
	 */
	private function uploadFileURL( $userfileURL, $userfile_name, &$msg, $userfileTitle = null )
	{
		global $_CB_framework;

		cbimport( 'cb.adminfilesystem' );
		$adminFS					=	cbAdminFileSystem::getInstance();

		if ( $adminFS->isUsingStandardPHP() ) {
			$baseDir				=	_cbPathName( $_CB_framework->getCfg('tmp_path') );
		} else {
			$baseDir				=	$_CB_framework->getCfg( 'absolute_path' ) . '/tmp';
		}

		if ( file_exists( $baseDir ) ) {
			if ( $adminFS->is_writable( $baseDir ) || ! $adminFS->isUsingStandardPHP() ) {
				$resultError			=	null;

				try {
					$guzzleHttpClient	=	new GuzzleHttp\Client();
					$guzzleRequest		=	$guzzleHttpClient->get( $userfileURL, array( 'timeout' => 90 ) );
				} catch( \GuzzleHttp\Exception\RequestException $e ) {
					if ( $e->hasResponse() ) {
						$resultError	=	htmlspecialchars( $e->getResponse()->getReasonPhrase() ) . ': ' . $e->getResponse()->getStatusCode();
					} else {
						$resultError	=	$e->getMessage();
					}

					$guzzleRequest		=	false;
				}

				if ( $guzzleRequest !== false ) {
					if ( $guzzleRequest->getStatusCode() == 200 ) {
						$adminFS		=	cbAdminFileSystem::getInstance();
						if ( $adminFS->file_put_contents( $userfile_name, (string) $guzzleRequest->getBody() ) ) {
//							if ( $this->_cbAdmin_chmod( $userfile_name ) ) {
								return true;
//							} else {
//								$msg = sprintf(CBTxt::T('Failed to change the permissions of the uploaded file %s'), $userfile_name);
//							}
						} else {
							$msg = sprintf(CBTxt::T('Failed to create and write uploaded file in %s'), $userfile_name);
						}
					} else {
						$msg = sprintf( CBTxt::T('Failed to download package file from <code>%s</code> to webserver due to following status: %s'), ( $userfileTitle ? $userfileTitle : $userfileURL ), htmlspecialchars( $guzzleRequest->getReasonPhrase() ) . ': ' . $guzzleRequest->getStatusCode() );
					}
				} elseif ( $resultError ) {
					$msg = sprintf( CBTxt::T('Failed to download package file from <code>%s</code> to webserver due to following error: %s'), ( $userfileTitle ? $userfileTitle : $userfileURL ), htmlspecialchars( $resultError ) );
				}
			} else {
				$msg = sprintf( CBTxt::T('Upload failed as %s directory is not writable.'), '<code>' . htmlspecialchars( $baseDir ) . '</code>' );
			}
		} else {
			$msg = sprintf( CBTxt::T('Upload failed as %s directory does not exist.'), '<code>' . htmlspecialchars( $baseDir ) . '</code>' );
		}
		return false;
	}
}
