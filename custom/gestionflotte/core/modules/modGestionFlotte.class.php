<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2024  Frédéric France         <frederic.france@free.fr>
 * Copyright (C) 2025 SuperAdmin <oasouleycouly@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   gestionflotte     Module GestionFlotte
 *  \brief      GestionFlotte module descriptor.
 *
 *  \file       htdocs/gestionflotte/core/modules/modGestionFlotte.class.php
 *  \ingroup    gestionflotte
 *  \brief      Description and activation file for module GestionFlotte
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module GestionFlotte
 */
class modGestionFlotte extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 800000; // TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve an id number for your module

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'gestionflotte';

		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = "other";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
		// Module label (no space allowed), used if translation string 'ModuleGestionFlotteName' not found (GestionFlotte is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// DESCRIPTION_FLAG
		// Module description, used if translation string 'ModuleGestionFlotteDesc' not found (GestionFlotte is name of module).
		$this->description = "GestionFlotteDescription";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "GestionFlotteDescription";

		// Author
		$this->editor_name = 'IBS';
		$this->editor_url = 'ibs-mali.com';		// Must be an external online web site
		$this->editor_squarred_logo = '';					// Must be image filename into the module/img directory followed with @modulename. Example: 'myimage.png@gestionflotte'

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated', 'experimental_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';
		// Url to the file with your last numberversion of this module
		//$this->url_last_version = 'http://www.example.com/versionmodule.txt';

		// Key used in llx_const table to save module status enabled/disabled (where GESTIONFLOTTE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
		$this->picto = 'fa-file-o';

		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 0,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 0,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => array(
				//    '/gestionflotte/css/gestionflotte.css.php',
			),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				//   '/gestionflotte/js/gestionflotte.js.php',
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			/* BEGIN MODULEBUILDER HOOKSCONTEXTS */
			'hooks' => array(
				//   'data' => array(
				//       'hookcontext1',
				//       'hookcontext2',
				//   ),
				//   'entity' => '0',
			),
			/* END MODULEBUILDER HOOKSCONTEXTS */
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
			// Set this to 1 if the module provides a website template into doctemplates/websites/website_template-mytemplate
			'websitetemplates' => 0
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/gestionflotte/temp","/gestionflotte/subdir");
		$this->dirs = array("/gestionflotte/temp");

		// Config pages. Put here list of php page, stored into gestionflotte/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@gestionflotte");

		// Dependencies
		// A condition to hide module
		$this->hidden = getDolGlobalInt('MODULE_GESTIONFLOTTE_DISABLED'); // A condition to disable module;
		// List of module class names that must be enabled if this module is enabled. Example: array('always'=>array('modModuleToEnable1','modModuleToEnable2'), 'FR'=>array('modModuleToEnableFR')...)
		$this->depends = array();
		// List of module class names to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->requiredby = array();
		// List of module class names this module is in conflict with. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = array();

		// The language file dedicated to your module
		$this->langfiles = array("gestionflotte@gestionflotte");

		// Prerequisites
		$this->phpmin = array(7, 1); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(19, -3); // Minimum version of Dolibarr required by module
		$this->need_javascript_ajax = 0;

		// Messages at activation
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		//$this->automatic_activation = array('FR'=>'GestionFlotteWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(1 => array('GESTIONFLOTTE_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
		//                             2 => array('GESTIONFLOTTE_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
		// );
		$this->const = array();

		// Some keys to add into the overwriting translation tables
		/*$this->overwrite_translation = array(
			'en_US:ParentCompany'=>'Parent company or reseller',
			'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
		)*/

		if (!isModEnabled("gestionflotte")) {
			$conf->gestionflotte = new stdClass();
			$conf->gestionflotte->enabled = 0;
		}

		// Array to add new pages in new tabs
		/* BEGIN MODULEBUILDER TABS */
		$this->tabs = array();
		/* END MODULEBUILDER TABS */
		// Example:
		// To add a new tab identified by code tabname1
		// $this->tabs[] = array('data'=>'objecttype:+tabname1:Title1:mylangfile@gestionflotte:$user->hasRight('gestionflotte', 'read'):/gestionflotte/mynewtab1.php?id=__ID__');
		// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
		// $this->tabs[] = array('data'=>'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@gestionflotte:$user->hasRight('othermodule', 'read'):/gestionflotte/mynewtab2.php?id=__ID__',
		// To remove an existing tab identified by code tabname
		// $this->tabs[] = array('data'=>'objecttype:-tabname:NU:conditiontoremove');
		//
		// Where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in foundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in sale order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view


		// Dictionaries
		/* Example:
		 $this->dictionaries=array(
		 'langs'=>'gestionflotte@gestionflotte',
		 // List of tables we want to see into dictonnary editor
		 'tabname'=>array("table1", "table2", "table3"),
		 // Label of tables
		 'tablib'=>array("Table1", "Table2", "Table3"),
		 // Request to select fields
		 'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),
		 // Sort order
		 'tabsqlsort'=>array("label ASC", "label ASC", "label ASC"),
		 // List of fields (result of select to show dictionary)
		 'tabfield'=>array("code,label", "code,label", "code,label"),
		 // List of fields (list of fields to edit a record)
		 'tabfieldvalue'=>array("code,label", "code,label", "code,label"),
		 // List of fields (list of fields for insert)
		 'tabfieldinsert'=>array("code,label", "code,label", "code,label"),
		 // Name of columns with primary key (try to always name it 'rowid')
		 'tabrowid'=>array("rowid", "rowid", "rowid"),
		 // Condition to show each dictionary
		 'tabcond'=>array(isModEnabled('gestionflotte'), isModEnabled('gestionflotte'), isModEnabled('gestionflotte')),
		 // Tooltip for every fields of dictionaries: DO NOT PUT AN EMPTY ARRAY
		 'tabhelp'=>array(array('code'=>$langs->trans('CodeTooltipHelp'), 'field2' => 'field2tooltip'), array('code'=>$langs->trans('CodeTooltipHelp'), 'field2' => 'field2tooltip'), ...),
		 );
		 */
		/* BEGIN MODULEBUILDER DICTIONARIES */
		$this->dictionaries = array();
		/* END MODULEBUILDER DICTIONARIES */

		// Boxes/Widgets
		// Add here list of php file(s) stored in gestionflotte/core/boxes that contains a class to show a widget.
		/* BEGIN MODULEBUILDER WIDGETS */
		$this->boxes = array(
			//  0 => array(
			//      'file' => 'gestionflottewidget1.php@gestionflotte',
			//      'note' => 'Widget provided by GestionFlotte',
			//      'enabledbydefaulton' => 'Home',
			//  ),
			//  ...
		);
		/* END MODULEBUILDER WIDGETS */

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		/* BEGIN MODULEBUILDER CRON */
		$this->cronjobs = array(
			//  0 => array(
			//      'label' => 'MyJob label',
			//      'jobtype' => 'method',
			//      'class' => '/gestionflotte/class/myobject.class.php',
			//      'objectname' => 'MyObject',
			//      'method' => 'doScheduledJob',
			//      'parameters' => '',
			//      'comment' => 'Comment',
			//      'frequency' => 2,
			//      'unitfrequency' => 3600,
			//      'status' => 0,
			//      'test' => 'isModEnabled("gestionflotte")',
			//      'priority' => 50,
			//  ),
		);
		/* END MODULEBUILDER CRON */
		// Example: $this->cronjobs=array(
		//    0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>'isModEnabled("gestionflotte")', 'priority'=>50),
		//    1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>'isModEnabled("gestionflotte")', 'priority'=>50)
		// );

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		// Add here entries to declare new permissions
		/* BEGIN MODULEBUILDER PERMISSIONS */
		
		$this->rights[$r][0] = $this->numero . sprintf("%02d", ($r * 10) + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Acceder à GestionFlotte'; // Permission label
		$this->rights[$r][4] = 'gestionflotte';
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->hasRight('gestionflotte', 'myobject', 'read'))
		$r++;

		$this->rights[$r][0] = $this->numero . sprintf("%02d", ($r * 10) + 2); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Acceder aux vehicules'; // Permission label
		$this->rights[$r][4] = 'gestionvehicule';
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->hasRight('gestionflotte', 'myobject', 'write'))
		$r++;

		$this->rights[$r][0] = $this->numero . sprintf("%02d", ($r * 10) + 2); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Créer/Modifier les vehicules'; // Permission label
		$this->rights[$r][4] = 'gestionvehicule';
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->hasRight('gestionflotte', 'myobject', 'write'))
		$r++;

		$this->rights[$r][0] = $this->numero . sprintf("%02d", ($r * 10) + 2); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Acceder à la configuration'; // Permission label
		$this->rights[$r][4] = 'configuration';
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->hasRight('gestionflotte', 'configuration', 'write'))
		$r++;

		$this->rights[$r][0] = $this->numero . sprintf("%02d", ($r * 10) + 2); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Modifier la configuration'; // Permission label
		$this->rights[$r][4] = 'configuration';
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->hasRight('gestionflotte', 'configuration', 'write'))
		$r++;
		
		/* END MODULEBUILDER PERMISSIONS */


		// Main menu entries to add
		$this->menu = array();
		$r = 0;
		// Add here entries to declare new menus
		/* BEGIN MODULEBUILDER TOPMENU */
		$this->menu[$r++] = array(
			'fk_menu'=>'', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'top', // This is a Top menu entry
			'titre'=>'Gestion de Flotte',
			'prefix' => img_picto('', $this->picto, 'class="pictofixedwidth valignmiddle"'),
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'',
			'url'=>'/gestionflotte/gestionflotte.php',
			'langs'=>'gestionflotte@gestionflotte', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000 + $r,
			'enabled'=>'isModEnabled("gestionflotte")', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled.
			'perms'=>'1', // Use 'perms'=>'$user->hasRight("gestionflotte", "myobject", "read")' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2, // 0=Menu for internal users, 1=external users, 2=both
		);
		/* END MODULEBUILDER TOPMENU */
		/* BEGIN MODULEBUILDER LEFTMENU MYOBJECT */
		
		//Gestion vehicule
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'Gestion Vehicules',
			'prefix' => img_picto('', 'vehicule', 'class="pictofixedwidth valignmiddle paddingright"'),
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'gestionvehicule',
			'url'=>'/gestionflotte/gestionvehicule_garde.php?mainmenu=gestionflotte&leftmenu=gestionvehicule',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("gestionflotte")', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=gestionvehicule',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Nouveau Vehicule',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'creationvehicule',
			'url'=>'/gestionflotte/creation_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="gestionvehicule" || $leftmenu=="creationvehicule" || $leftmenu=="listevehicule" || $leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=gestionvehicule',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Liste Vehicule',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listevehicule',
			'url'=>'/gestionflotte/creation_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="gestionvehicule" || $leftmenu=="creationvehicule" || $leftmenu=="listevehicule" || $leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=listevehicule',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'En bon état',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'vehiculeenbonetat',
			'url'=>'/gestionflotte/creation_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&etat=bonetat',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="listevehicule"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=listevehicule',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'En Panne',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'vehiculeenpanne',
			'url'=>'/gestionflotte/creation_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&etat=panne',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="listevehicule"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=listevehicule',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Stationnés',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'vehiculestationne',
			'url'=>'/gestionflotte/vehicule_stationne.php?mainmenu=gestionflotte&leftmenu=listevehicule&etat=stationne',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="listevehicule"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		//stationnement
		/*$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=gestionvehicule',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'stationnement',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'stationnement',
			'url'=>'/gestionflotte/creation_vehicule.php?mainmenu=gestionflotte&leftmenu=stationnement&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="gestionvehicule" || $leftmenu=="creationvehicule" || $leftmenu=="listevehicule" || $leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=stationnement',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'stationner un vehicule',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'vehiculestationne',
			'url'=>'/gestionflotte/creation_vehicule.php?mainmenu=gestionflotte&leftmenu=stationnement&action=stationne',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=stationnement',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Liste des vehicules stationnés',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listetypevehicule',
			'url'=>'/gestionflotte/creation_vehicule.php?mainmenu=gestionflotte&leftmenu=stationnement&action=non_stationne',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);*/

		//Gestion documentation
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'Gestion documents',
			'prefix' => img_picto('', 'papier', 'class="pictofixedwidth valignmiddle paddingright"'),
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'gestiondocument',
			'url'=>'/gestionflotte/gestion_document_garde.php?mainmenu=gestionflotte&leftmenu=gestiondocument',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("gestionflotte")', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=gestiondocument',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Liste complète',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'liste_documentation_complete',
			'url'=>'/gestionflotte/liste_documentation_complete.php?mainmenu=gestionflotte&leftmenu=gestiondocument&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="gestiondocument"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);


		//Gestion carburant
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'Gestion carburant',
			'prefix' => img_picto('', 'carburant', 'class="pictofixedwidth valignmiddle paddingright"'),
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'carburant',
			'url'=>'/gestionflotte/gestion_carburant_garde.php?mainmenu=gestionflotte&leftmenu=carburant',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("gestionflotte")', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		//sous menu carburant
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=carburant',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Demande carburant',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'nouveaucarburant',
			'url'=>'/gestionflotte/carburant.php?mainmenu=gestionflotte&leftmenu=carburant&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="carburant"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=carburant',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Liste des demandes',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listecarburant',
			'url'=>'/gestionflotte/carburant.php?mainmenu=gestionflotte&leftmenu=carburant&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="carburant"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=carburant',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Statistiques',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'statistique_carburant',
			'url'=>'/gestionflotte/statistique_carburant.php?mainmenu=gestionflotte&leftmenu=carburant',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="carburant"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		//Maintenance
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'Maintenances',
			'prefix' => img_picto('', 'maintenance', 'class="pictofixedwidth valignmiddle paddingright"'),
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'maintenance',
			'url'=>'/gestionflotte/maintenance_garde.php?mainmenu=gestionflotte&leftmenu=maintenance',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("gestionflotte")', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=maintenance',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Maintenances à vénir',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'maintenance_a_venir',
			'url'=>'/gestionflotte/liste_maintenance_a_venir.php?mainmenu=gestionflotte&leftmenu=maintenance&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="maintenance"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=maintenance',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Maintenances éffectuées',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'liste_maintenance_complete',
			'url'=>'/gestionflotte/liste_maintenance_complete.php?mainmenu=gestionflotte&leftmenu=maintenance&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="maintenance"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=maintenance',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Réparations éffectuées',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'liste_reparation_complete',
			'url'=>'/gestionflotte/liste_reparation_complete.php?mainmenu=gestionflotte&leftmenu=maintenance&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="maintenance"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);


		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=maintenance',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Statistiques',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'statistique_maintenance',
			'url'=>'/gestionflotte/statistique_maintenance.php?mainmenu=gestionflotte&leftmenu=maintenance',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="maintenance"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		//Historique
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'Historique',
			'prefix' => img_picto('', 'archiver', 'class="pictofixedwidth valignmiddle paddingright"'),
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'historique',
			'url'=>'/gestionflotte/historique_garde.php?mainmenu=gestionflotte&leftmenu=historique',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("gestionflotte")', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);
	
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=historique',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Assignation',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'assignation',
			'url'=>'/gestionflotte/assignation.php?mainmenu=gestionflotte&leftmenu=historique&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="historique"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		/*$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=historique',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'stationnement',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'stationnement',
			'url'=>'/gestionflotte/creation_vehicule.php?mainmenu=gestionflotte&leftmenu=historique&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="historique"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);*/

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=historique',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Maintenance',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'maintenance',
			'url'=>'/gestionflotte/historique_maintenance.php?mainmenu=gestionflotte&leftmenu=historique',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="historique"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=historique',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Incident',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'incident',
			'url'=>'/gestionflotte/creation_vehicule.php?mainmenu=gestionflotte&leftmenu=historique&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="historique"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=historique',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Documention',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'documentation',
			'url'=>'/gestionflotte/liste_documentation_complete.php?mainmenu=gestionflotte&leftmenu=historique&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="historique"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);


		//Configuration
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'Configuration',
			'prefix' => img_picto('', 'setup', 'class="pictofixedwidth valignmiddle paddingright"'),
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'configuration',
			'url'=>'/gestionflotte/configuration.php?mainmenu=gestionflotte&leftmenu=configuration',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("gestionflotte")', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		//sous menu type vehicule
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=configuration',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Type véhicule',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'type_vehicule',
			'url'=>'/gestionflotte/creation_type_vehicule.php?mainmenu=gestionflotte&leftmenu=type_vehicule&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="configuration" || $leftmenu=="equipement" || $leftmenu=="type_carburant" || $leftmenu=="type_maintenance" || $leftmenu=="type_document" || $leftmenu=="type_vehicule" || $leftmenu=="panne" || $leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=type_vehicule',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Nouveau',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'nouveautypevehicule',
			'url'=>'/gestionflotte/creation_type_vehicule.php?mainmenu=gestionflotte&leftmenu=type_vehicule&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="type_vehicule"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=type_vehicule',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Liste',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listetypevehicule',
			'url'=>'/gestionflotte/creation_type_vehicule.php?mainmenu=gestionflotte&leftmenu=type_vehicule&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="type_vehicule"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);



		//sous menu documentation
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=configuration',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Type document',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'type_document',
			'url'=>'/gestionflotte/creation_type_document.php?mainmenu=gestionflotte&leftmenu=type_document&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="configuration" || $leftmenu=="equipement" || $leftmenu=="type_carburant" || $leftmenu=="type_maintenance" || $leftmenu=="type_document" || $leftmenu=="type_vehicule" || $leftmenu=="panne" || $leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "gestionvehicule", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=type_document',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Nouveau',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'nouveautypedocument',
			'url'=>'/gestionflotte/creation_type_document.php?mainmenu=gestionflotte&leftmenu=type_document&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="type_document"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=type_document',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Liste',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listetypedocument',
			'url'=>'/gestionflotte/creation_type_document.php?mainmenu=gestionflotte&leftmenu=type_document&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="type_document"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);


		//sous menu Maintenance
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=configuration',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Type maintenance',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'type_maintenance',
			'url'=>'/gestionflotte/creation_type_maintenance.php?mainmenu=gestionflotte&leftmenu=type_maintenance&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="configuration" || $leftmenu=="equipement" || $leftmenu=="type_carburant" || $leftmenu=="type_maintenance" || $leftmenu=="type_document" || $leftmenu=="type_vehicule" || $leftmenu=="panne" || $leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=type_maintenance',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Nouveau',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'nouveautypemaintenance',
			'url'=>'/gestionflotte/creation_type_maintenance.php?mainmenu=gestionflotte&leftmenu=type_maintenance&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="type_maintenance"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=type_maintenance',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Liste',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listetypemaintenance',
			'url'=>'/gestionflotte/creation_type_maintenance.php?mainmenu=gestionflotte&leftmenu=type_maintenance&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="type_maintenance"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		//sous carburant type
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=configuration',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Type carburant',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'type_carburant',
			'url'=>'/gestionflotte/creation_type_carburant.php?mainmenu=gestionflotte&leftmenu=type_carburant&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="configuration" || $leftmenu=="equipement" || $leftmenu=="type_carburant" || $leftmenu=="type_maintenance" || $leftmenu=="type_document" || $leftmenu=="type_vehicule" || $leftmenu=="panne" || $leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=type_carburant',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Nouveau',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'nouveautypecarburant',
			'url'=>'/gestionflotte/creation_type_carburant.php?mainmenu=gestionflotte&leftmenu=type_carburant&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="type_carburant"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=type_carburant',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Liste',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listetypecarburant',
			'url'=>'/gestionflotte/creation_type_carburant.php?mainmenu=gestionflotte&leftmenu=type_carburant&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="type_carburant"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);


		//sous menu Panne
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=configuration',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Pièces de remplacement',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'panne',
			'url'=>'/gestionflotte/creation_piece.php?mainmenu=gestionflotte&leftmenu=panne&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="configuration" || $leftmenu=="equipement" || $leftmenu=="type_carburant" || $leftmenu=="type_maintenance" || $leftmenu=="type_document" || $leftmenu=="type_vehicule" || $leftmenu=="panne" || $leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=panne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Nouvelle pièces',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listetypevehicule',
			'url'=>'/gestionflotte/creation_piece.php?mainmenu=gestionflotte&leftmenu=panne&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="panne"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=panne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Liste',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listetypevehicule',
			'url'=>'/gestionflotte/creation_piece.php?mainmenu=gestionflotte&leftmenu=panne&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="panne"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);


		//sous menu équipement
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=configuration',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Équipement de bord',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'equipement',
			'url'=>'/gestionflotte/creation_equipement.php?mainmenu=gestionflotte&leftmenu=equipement&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="configuration" || $leftmenu=="equipement" || $leftmenu=="type_carburant" || $leftmenu=="type_maintenance" || $leftmenu=="type_document" || $leftmenu=="type_vehicule" || $leftmenu=="panne" || $leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=equipement',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Nouvel équipement',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listetypevehicule',
			'url'=>'/gestionflotte/creation_equipement.php?mainmenu=gestionflotte&leftmenu=equipement&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="equipement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=equipement',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Liste',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listetypevehicule',
			'url'=>'/gestionflotte/creation_equipement.php?mainmenu=gestionflotte&leftmenu=equipement&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="equipement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);


		//sous menu stationnement
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=configuration',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Lieu de stationnement',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'stationnement',
			'url'=>'/gestionflotte/creation_stationnement.php?mainmenu=gestionflotte&leftmenu=stationnement&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="configuration" || $leftmenu=="equipement" || $leftmenu=="type_carburant" || $leftmenu=="type_maintenance" || $leftmenu=="type_document" || $leftmenu=="type_vehicule" || $leftmenu=="panne" || $leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "write")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=stationnement',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Nouveau lieu',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listetypevehicule',
			'url'=>'/gestionflotte/creation_stationnement.php?mainmenu=gestionflotte&leftmenu=stationnement&action=creation',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=stationnement',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'Liste',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'listetypevehicule',
			'url'=>'/gestionflotte/creation_stationnement.php?mainmenu=gestionflotte&leftmenu=stationnement&action=liste',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);


		$this->menu[$r++] = array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=configuration', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'',
			'titre'=>'A propos',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'apropos',
			'url'=>'/gestionflotte/apropos.php?mainmenu=gestionflotte&leftmenu=configuration',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$leftmenu=="configuration" || $leftmenu=="equipement" || $leftmenu=="type_carburant" || $leftmenu=="type_maintenance" || $leftmenu=="type_document" || $leftmenu=="type_vehicule" || $leftmenu=="panne" || $leftmenu=="stationnement"', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("gestionflotte", "configuration", "read")',
			'target'=>'',
			'user'=>2,			                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);

		/*$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=gestionflotte,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'List_MyObject',
			'mainmenu'=>'gestionflotte',
			'leftmenu'=>'gestionflotte_myobject_list',
			'url'=>'/gestionflotte/myobject_list.php',
			'langs'=>'gestionflotte@gestionflotte',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("gestionflotte")', // Define condition to show or hide menu entry. Use 'isModEnabled("gestionflotte")' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("gestionflotte", "myobject", "read")'
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);
		
		/* END MODULEBUILDER LEFTMENU MYOBJECT */


		// Exports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER EXPORT MYOBJECT */
		/*
		$langs->load("gestionflotte@gestionflotte");
		$this->export_code[$r] = $this->rights_class.'_'.$r;
		$this->export_label[$r] = 'MyObjectLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_icon[$r] = $this->picto;
		// Define $this->export_fields_array, $this->export_TypeFields_array and $this->export_entities_array
		$keyforclass = 'MyObject'; $keyforclassfile='/gestionflotte/class/myobject.class.php'; $keyforelement='myobject@gestionflotte';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		//$this->export_fields_array[$r]['t.fieldtoadd']='FieldToAdd'; $this->export_TypeFields_array[$r]['t.fieldtoadd']='Text';
		//unset($this->export_fields_array[$r]['t.fieldtoremove']);
		//$keyforclass = 'MyObjectLine'; $keyforclassfile='/gestionflotte/class/myobject.class.php'; $keyforelement='myobjectline@gestionflotte'; $keyforalias='tl';
		//include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		$keyforselect='myobject'; $keyforaliasextra='extra'; $keyforelement='myobject@gestionflotte';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$keyforselect='myobjectline'; $keyforaliasextra='extraline'; $keyforelement='myobjectline@gestionflotte';
		//include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$this->export_dependencies_array[$r] = array('myobjectline'=>array('tl.rowid','tl.ref')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
		//$this->export_special_array[$r] = array('t.field'=>'...');
		//$this->export_examplevalues_array[$r] = array('t.field'=>'Example');
		//$this->export_help_array[$r] = array('t.field'=>'FieldDescHelp');
		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'gestionflotte_myobject as t';
		//$this->export_sql_end[$r]  .=' LEFT JOIN '.MAIN_DB_PREFIX.'gestionflotte_myobject_line as tl ON tl.fk_myobject = t.rowid';
		$this->export_sql_end[$r] .=' WHERE 1 = 1';
		$this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('myobject').')';
		$r++; */
		/* END MODULEBUILDER EXPORT MYOBJECT */

		// Imports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER IMPORT MYOBJECT */
		/*
		$langs->load("gestionflotte@gestionflotte");
		$this->import_code[$r] = $this->rights_class.'_'.$r;
		$this->import_label[$r] = 'MyObjectLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->import_icon[$r] = $this->picto;
		$this->import_tables_array[$r] = array('t' => MAIN_DB_PREFIX.'gestionflotte_myobject', 'extra' => MAIN_DB_PREFIX.'gestionflotte_myobject_extrafields');
		$this->import_tables_creator_array[$r] = array('t' => 'fk_user_author'); // Fields to store import user id
		$import_sample = array();
		$keyforclass = 'MyObject'; $keyforclassfile='/gestionflotte/class/myobject.class.php'; $keyforelement='myobject@gestionflotte';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinimport.inc.php';
		$import_extrafield_sample = array();
		$keyforselect='myobject'; $keyforaliasextra='extra'; $keyforelement='myobject@gestionflotte';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinimport.inc.php';
		$this->import_fieldshidden_array[$r] = array('extra.fk_object' => 'lastrowid-'.MAIN_DB_PREFIX.'gestionflotte_myobject');
		$this->import_regex_array[$r] = array();
		$this->import_examplevalues_array[$r] = array_merge($import_sample, $import_extrafield_sample);
		$this->import_updatekeys_array[$r] = array('t.ref' => 'Ref');
		$this->import_convertvalue_array[$r] = array(
			't.ref' => array(
				'rule'=>'getrefifauto',
				'class'=>(!getDolGlobalString('GESTIONFLOTTE_MYOBJECT_ADDON') ? 'mod_myobject_standard' : getDolGlobalString('GESTIONFLOTTE_MYOBJECT_ADDON')),
				'path'=>"/core/modules/gestionflotte/".(!getDolGlobalString('GESTIONFLOTTE_MYOBJECT_ADDON') ? 'mod_myobject_standard' : getDolGlobalString('GESTIONFLOTTE_MYOBJECT_ADDON')).'.php',
				'classobject'=>'MyObject',
				'pathobject'=>'/gestionflotte/class/myobject.class.php',
			),
			't.fk_soc' => array('rule' => 'fetchidfromref', 'file' => '/societe/class/societe.class.php', 'class' => 'Societe', 'method' => 'fetch', 'element' => 'ThirdParty'),
			't.fk_user_valid' => array('rule' => 'fetchidfromref', 'file' => '/user/class/user.class.php', 'class' => 'User', 'method' => 'fetch', 'element' => 'user'),
			't.fk_mode_reglement' => array('rule' => 'fetchidfromcodeorlabel', 'file' => '/compta/paiement/class/cpaiement.class.php', 'class' => 'Cpaiement', 'method' => 'fetch', 'element' => 'cpayment'),
		);
		$this->import_run_sql_after_array[$r] = array();
		$r++; */
		/* END MODULEBUILDER IMPORT MYOBJECT */
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		//$result = $this->_load_tables('/install/mysql/', 'gestionflotte');
		$result = $this->_load_tables('/gestionflotte/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		// Create extrafields during init
		//include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		//$extrafields = new ExtraFields($this->db);
		//$result0=$extrafields->addExtraField('gestionflotte_separator1', "Separator 1", 'separator', 1,  0, 'thirdparty',   0, 0, '', array('options'=>array(1=>1)), 1, '', 1, 0, '', '', 'gestionflotte@gestionflotte', 'isModEnabled("gestionflotte")');
		//$result1=$extrafields->addExtraField('gestionflotte_myattr1', "New Attr 1 label", 'boolean', 1,  3, 'thirdparty',   0, 0, '', '', 1, '', -1, 0, '', '', 'gestionflotte@gestionflotte', 'isModEnabled("gestionflotte")');
		//$result2=$extrafields->addExtraField('gestionflotte_myattr2', "New Attr 2 label", 'varchar', 1, 10, 'project',      0, 0, '', '', 1, '', -1, 0, '', '', 'gestionflotte@gestionflotte', 'isModEnabled("gestionflotte")');
		//$result3=$extrafields->addExtraField('gestionflotte_myattr3', "New Attr 3 label", 'varchar', 1, 10, 'bank_account', 0, 0, '', '', 1, '', -1, 0, '', '', 'gestionflotte@gestionflotte', 'isModEnabled("gestionflotte")');
		//$result4=$extrafields->addExtraField('gestionflotte_myattr4', "New Attr 4 label", 'select',  1,  3, 'thirdparty',   0, 1, '', array('options'=>array('code1'=>'Val1','code2'=>'Val2','code3'=>'Val3')), 1,'', -1, 0, '', '', 'gestionflotte@gestionflotte', 'isModEnabled("gestionflotte")');
		//$result5=$extrafields->addExtraField('gestionflotte_myattr5', "New Attr 5 label", 'text',    1, 10, 'user',         0, 0, '', '', 1, '', -1, 0, '', '', 'gestionflotte@gestionflotte', 'isModEnabled("gestionflotte")');

		// Permissions
		$this->remove($options);

		$sql = array();

		// Document templates
		$moduledir = dol_sanitizeFileName('gestionflotte');
		$myTmpObjects = array();
		$myTmpObjects['MyObject'] = array('includerefgeneration'=>0, 'includedocgeneration'=>0);

		foreach ($myTmpObjects as $myTmpObjectKey => $myTmpObjectArray) {
			if ($myTmpObjectKey == 'MyObject') {
				continue;
			}
			if ($myTmpObjectArray['includerefgeneration']) {
				$src = DOL_DOCUMENT_ROOT.'/install/doctemplates/'.$moduledir.'/template_myobjects.odt';
				$dirodt = DOL_DATA_ROOT.'/doctemplates/'.$moduledir;
				$dest = $dirodt.'/template_myobjects.odt';

				if (file_exists($src) && !file_exists($dest)) {
					require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
					dol_mkdir($dirodt);
					$result = dol_copy($src, $dest, 0, 0);
					if ($result < 0) {
						$langs->load("errors");
						$this->error = $langs->trans('ErrorFailToCopyFile', $src, $dest);
						return 0;
					}
				}

				$sql = array_merge($sql, array(
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'standard_".strtolower($myTmpObjectKey)."' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('standard_".strtolower($myTmpObjectKey)."', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")",
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'generic_".strtolower($myTmpObjectKey)."_odt' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('generic_".strtolower($myTmpObjectKey)."_odt', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")"
				));
			}
		}

		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
}
