<?php
/* Copyright (C) 2025 SuperAdmin <oasouleycouly@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    gestionflotte/lib/gestionflotte.lib.php
 * \ingroup gestionflotte
 * \brief   Library files with common functions for GestionFlotte
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function gestionflotteAdminPrepareHead()
{
	global $langs, $conf;

	// global $db;
	// $extrafields = new ExtraFields($db);
	// $extrafields->fetch_name_optionals_label('myobject');

	$langs->load("gestionflotte@gestionflotte");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/gestionflotte/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	/*
	$head[$h][0] = dol_buildpath("/gestionflotte/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$nbExtrafields = is_countable($extrafields->attributes['myobject']['label']) ? count($extrafields->attributes['myobject']['label']) : 0;
	if ($nbExtrafields > 0) {
		$head[$h][1] .= ' <span class="badge">' . $nbExtrafields . '</span>';
	}
	$head[$h][2] = 'myobject_extrafields';
	$h++;
	*/

	$head[$h][0] = dol_buildpath("/gestionflotte/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@gestionflotte:/gestionflotte/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@gestionflotte:/gestionflotte/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'gestionflotte@gestionflotte');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'gestionflotte@gestionflotte', 'remove');

	return $head;
}


function vehicule_Head($id_vehicule)
{
	global $langs, $conf;

	$langs->load("gestionflotte@gestionflotte");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/gestionflotte/onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&id_vehicule=".$id_vehicule, 1);
	$head[$h][1] = $langs->trans("Détails");
	$head[$h][2] = 'detail';
	$h++;

	$head[$h][0] = dol_buildpath("/gestionflotte/onglets/assignation_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&id_vehicule=".$id_vehicule, 1);
	$head[$h][1] = $langs->trans("Assignations");
	$head[$h][2] = 'assignation';
	$h++;

	$head[$h][0] = dol_buildpath("/gestionflotte/onglets/document_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&id_vehicule=".$id_vehicule, 1);
	$head[$h][1] = "Documents";
	$head[$h][2] = 'document';
	$h++;

	$head[$h][0] = dol_buildpath("/gestionflotte/onglets/carburant_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&id_vehicule=".$id_vehicule, 1);
	$head[$h][1] = $langs->trans("Carburants");
	$head[$h][2] = 'carburant';
	$h++;

	$head[$h][0] = dol_buildpath("/gestionflotte/onglets/vidange_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&id_vehicule=".$id_vehicule, 1);
	$head[$h][1] = $langs->trans("Maintenances & Réparation");
	$head[$h][2] = 'vidange';
	$h++;

	$head[$h][0] = dol_buildpath("/gestionflotte/onglets/incident_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&id_vehicule=".$id_vehicule, 1);
	$head[$h][1] = $langs->trans("Incidents & Sinistres");
	$head[$h][2] = 'incident';
	$h++;

	$head[$h][0] = dol_buildpath("/gestionflotte/onglets/image_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&id_vehicule=".$id_vehicule, 1);
	$head[$h][1] = $langs->trans("Images");
	$head[$h][2] = 'images';
	$h++;

	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'gestionflotte@gestionflotte');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'gestionflotte@gestionflotte', 'remove');

	return $head;
}



function maintenanceHead($id_vehicule)
{
	global $langs, $conf;

	// global $db;
	// $extrafields = new ExtraFields($db);
	// $extrafields->fetch_name_optionals_label('myobject');

	$langs->load("gestionflotte@gestionflotte");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/gestionflotte/onglets/vidange_vehicule.php?mainmenu=gestionflotte&leftmanu=listevehicule&id_vehicule=".$id_vehicule, 1);
	$head[$h][1] = $langs->trans("Vidanges");
	$head[$h][2] = 'vidange';
	$h++;

	$head[$h][0] = dol_buildpath("/gestionflotte/onglets/maintenance_vehicule.php?mainmenu=gestionflotte&leftmanu=listevehicule&id_vehicule=".$id_vehicule, 1);
	$head[$h][1] = $langs->trans("Maintenances");
	$head[$h][2] = 'maintenance';
	$h++;

	$head[$h][0] = dol_buildpath("/gestionflotte/onglets/reparation_vehicule.php?mainmenu=gestionflotte&leftmanu=listevehicule&id_vehicule=".$id_vehicule, 1);
	$head[$h][1] = $langs->trans("Réparations");
	$head[$h][2] = 'reparation';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@gestionflotte:/gestionflotte/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@gestionflotte:/gestionflotte/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'gestionflotte@gestionflotte');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'gestionflotte@gestionflotte', 'remove');

	return $head;
}

/**
 *	Get title line of an array
 *
 *	@param	string	$name        		Translation key of field to show or complete HTML string to show
 *	@param	int		$thead		 		0=To use with standard table format, 1=To use inside <thead><tr>, 2=To use with <div>
 *	@param	string	$file        		Url used when we click on sort picto
 *	@param	string	$field       		Field to use for new sorting. Empty if this field is not sortable. Example "t.abc" or "t.abc,t.def"
 *	@param	string	$begin       		("" by defaut)
 *	@param	string	$moreparam   		Add more parameters on sort url links ("" by default)
 *	@param  string	$moreattrib  		Add more attributes on th ("" by defaut, example: 'align="center"'). To add more css class, use param $prefix.
 *	@param  string	$sortfield   		Current field used to sort (Ex: 'd.datep,d.id')
 *	@param  string	$sortorder   		Current sort order (Ex: 'asc,desc')
 *  @param	string	$prefix		 		Prefix for css. Use space after prefix to add your own CSS tag, for example 'mycss '.
 *  @param	string	$disablesortlink	1=Disable sort link
 *  @param	string	$tooltip	 		Tooltip
 *  @param	string	$forcenowrapcolumntitle		No need for use 'wrapcolumntitle' css style
 *	@return	string
 */
function affiche_long_texte($image="", $name, $thead, $file, $field = "", $begin = "", $moreparam = "", $moreattrib = "", $sortfield = "", $sortorder = "", $prefix = "", $disablesortlink = 0, $tooltip = "", $forcenowrapcolumntitle = 0)
{
	global $conf, $langs, $form;
	//print "$name, $file, $field, $begin, $options, $moreattrib, $sortfield, $sortorder<br>\n";

	if ($moreattrib == 'class="right"') {
		$prefix .= 'right '; // For backward compatibility
	}

	$sortorder = strtoupper($sortorder);
	$out = '';
	$sortimg = '';

	$tag = 'td';
	if ($thead == 2) {
		$tag = 'div';
	}

	$tmpsortfield = explode(',', $sortfield);
	$sortfield1 = trim($tmpsortfield[0]); // If $sortfield is 'd.datep,d.id', it becomes 'd.datep'
	$tmpfield = explode(',', $field);
	$field1 = trim($tmpfield[0]); // If $field is 'd.datep,d.id', it becomes 'd.datep'

	if (empty($conf->global->MAIN_DISABLE_WRAPPING_ON_COLUMN_TITLE) && empty($forcenowrapcolumntitle)) {
		$prefix = 'wrapcolumntitle '.$prefix;
	}

	//var_dump('field='.$field.' field1='.$field1.' sortfield='.$sortfield.' sortfield1='.$sortfield1);
	// If field is used as sort criteria we use a specific css class liste_titre_sel
	// Example if (sortfield,field)=("nom","xxx.nom") or (sortfield,field)=("nom","nom")
	$liste_titre = 'liste_titre';
	if ($field1 && ($sortfield1 == $field1 || $sortfield1 == preg_replace("/^[^\.]+\./", "", $field1))) {
		$liste_titre = 'liste_titre_sel';
	}

	$out .= '<'.$tag.' class="'.$prefix.$liste_titre.'" '.$moreattrib;
	//$out .= (($field && empty($conf->global->MAIN_DISABLE_WRAPPING_ON_COLUMN_TITLE) && preg_match('/^[a-zA-Z_0-9\s\.\-:&;]*$/', $name)) ? ' title="'.dol_escape_htmltag($langs->trans($name)).'"' : '');
	$out .= ($name && empty($conf->global->MAIN_DISABLE_WRAPPING_ON_COLUMN_TITLE) && empty($forcenowrapcolumntitle) && !dol_textishtml($name)) ? ' title="'.dol_escape_htmltag($langs->trans($name)).'"' : '';
	$out .= '>';

	if (empty($thead) && $field && empty($disablesortlink)) {    // If this is a sort field
		$options = preg_replace('/sortfield=([a-zA-Z0-9,\s\.]+)/i', '', (is_scalar($moreparam) ? $moreparam : ''));
		$options = preg_replace('/sortorder=([a-zA-Z0-9,\s\.]+)/i', '', $options);
		$options = preg_replace('/&+/i', '&', $options);
		if (!preg_match('/^&/', $options)) {
			$options = '&'.$options;
		}

		$sortordertouseinlink = '';
		if ($field1 != $sortfield1) { // We are on another field than current sorted field
			if (preg_match('/^DESC/i', $sortorder)) {
				$sortordertouseinlink .= str_repeat('desc,', count(explode(',', $field)));
			} else // We reverse the var $sortordertouseinlink
			{
				$sortordertouseinlink .= str_repeat('asc,', count(explode(',', $field)));
			}
		} else // We are on field that is the first current sorting criteria
		{
			if (preg_match('/^ASC/i', $sortorder)) {	// We reverse the var $sortordertouseinlink
				$sortordertouseinlink .= str_repeat('desc,', count(explode(',', $field)));
			} else {
				$sortordertouseinlink .= str_repeat('asc,', count(explode(',', $field)));
			}
		}
		$sortordertouseinlink = preg_replace('/,$/', '', $sortordertouseinlink);
		$out .= $image." ";
		if(!empty($file)){
			$out .=' <a class="reposition" href="'.$file.'"';
			//$out .= (empty($conf->global->MAIN_DISABLE_WRAPPING_ON_COLUMN_TITLE) ? ' title="'.dol_escape_htmltag($langs->trans($name)).'"' : '');
			$out .= '>';
		}
	}
	if ($tooltip) {
		// You can also use 'TranslationString:keyfortooltiponlick' for a tooltip on click.
		$tmptooltip = explode(':', $tooltip);
		$out .= $form->textwithpicto($langs->trans($name), $langs->trans($tmptooltip[0]), 1, 'help', '', 0, 3, (empty($tmptooltip[1]) ? '' : 'extra_'.str_replace('.', '_', $field).'_'.$tmptooltip[1]));
	} else {
		$out .= $langs->trans($name);
	}

	if (empty($thead) && $field && empty($disablesortlink)) {    // If this is a sort field
		$out .= '</a>';
	}

	if (empty($thead) && $field) {    // If this is a sort field
		$options = preg_replace('/sortfield=([a-zA-Z0-9,\s\.]+)/i', '', (is_scalar($moreparam) ? $moreparam : ''));
		$options = preg_replace('/sortorder=([a-zA-Z0-9,\s\.]+)/i', '', $options);
		$options = preg_replace('/&+/i', '&', $options);
		if (!preg_match('/^&/', $options)) {
			$options = '&'.$options;
		}

		if (!$sortorder || $field1 != $sortfield1) {
			//$out.= '<a href="'.$file.'?sortfield='.$field.'&sortorder=asc&begin='.$begin.$options.'">'.img_down("A-Z",0).'</a>';
			//$out.= '<a href="'.$file.'?sortfield='.$field.'&sortorder=desc&begin='.$begin.$options.'">'.img_up("Z-A",0).'</a>';
		} else {
			if (preg_match('/^DESC/', $sortorder)) {
				//$out.= '<a href="'.$file.'?sortfield='.$field.'&sortorder=asc&begin='.$begin.$options.'">'.img_down("A-Z",0).'</a>';
				//$out.= '<a href="'.$file.'?sortfield='.$field.'&sortorder=desc&begin='.$begin.$options.'">'.img_up("Z-A",1).'</a>';
				$sortimg .= '<span class="nowrap">'.img_up("Z-A", 0, 'paddingleft').'</span>';
			}
			if (preg_match('/^ASC/', $sortorder)) {
				//$out.= '<a href="'.$file.'?sortfield='.$field.'&sortorder=asc&begin='.$begin.$options.'">'.img_down("A-Z",1).'</a>';
				//$out.= '<a href="'.$file.'?sortfield='.$field.'&sortorder=desc&begin='.$begin.$options.'">'.img_up("Z-A",0).'</a>';
				$sortimg .= '<span class="nowrap">'.img_down("A-Z", 0, 'paddingleft').'</span>';
			}
		}
	}

	$out .= $sortimg;

	$out .= '</'.$tag.'>';

	return $out;
}


/**
	 *    Return a HTML area with the reference of object and a navigation bar for a business object
	 *    Note: To complete search with a particular filter on select, you can set $object->next_prev_filter set to define SQL criterias.
	 *
	 *    @param	object	$object			Object to show.
	 *    @param	string	$paramid   		Name of parameter to use to name the id into the URL next/previous link.
	 *    @param	string	$morehtml  		More html content to output just before the nav bar.
	 *    @param	int		$shownav	  	Show Condition (navigation is shown if value is 1).
	 *    @param	string	$fieldid   		Name of field id into database to use for select next and previous (we make the select max and min on object field compared to $object->ref). Use 'none' to disable next/prev.
	 *    @param	string	$fieldref   	Name of field ref of object (object->ref) to show or 'none' to not show ref.
	 *    @param	string	$morehtmlref  	More html to show after ref.
	 *    @param	string	$moreparam  	More param to add in nav link url. Must start with '&...'.
	 *	  @param	int		$nodbprefix		Do not include DB prefix to forge table name.
	 *	  @param	string	$morehtmlleft	More html code to show before ref.
	 *	  @param	string	$morehtmlstatus	More html code to show under navigation arrows (status place).
	 *	  @param	string	$morehtmlright	More html code to show after ref.
	 * 	  @return	string    				Portion HTML with ref + navigation buttons
	 */
	function afficher($object, $paramid, $morehtml = '', $shownav = 1, $fieldid = 'rowid', $fieldref = 'ref', $morehtmlref = '', $moreparam = '', $nodbprefix = 0, $morehtmlleft = '', $morehtmlstatus = '', $morehtmlright = '')
	{
		global $conf, $langs, $hookmanager, $extralanguages;

		$ret = '';
		if (empty($fieldid)) {
			$fieldid = 'rowid';
		}
		if (empty($fieldref)) {
			$fieldref = 'ref';
		}

		// Preparing gender's display if there is one
		$addgendertxt = '';
		if (property_exists($object, 'gender') && !empty($object->gender)) {
			$addgendertxt = ' ';
			switch ($object->gender) {
				case 'man':
					$addgendertxt .= '<i class="fas fa-mars"></i>';
					break;
				case 'woman':
					$addgendertxt .= '<i class="fas fa-venus"></i>';
					break;
				case 'other':
					$addgendertxt .= '<i class="fas fa-genderless"></i>';
					break;
			}
		}
		/*
		$addadmin = '';
		if (property_exists($object, 'admin')) {
			if (!empty($conf->multicompany->enabled) && !empty($object->admin) && empty($object->entity)) {
				$addadmin .= img_picto($langs->trans("SuperAdministratorDesc"), "redstar", 'class="paddingleft"');
			} elseif (!empty($object->admin)) {
				$addadmin .= img_picto($langs->trans("AdministratorDesc"), "star", 'class="paddingleft"');
			}
		}*/

		// Add where from hooks
		if (is_object($hookmanager)) {
			$parameters = array();
			$reshook = $hookmanager->executeHooks('printFieldListWhere', $parameters, $object); // Note that $action and $object may have been modified by hook
			$object->next_prev_filter .= $hookmanager->resPrint;
		}
		$previous_ref = $next_ref = '';
		if ($shownav) {
			//print "paramid=$paramid,morehtml=$morehtml,shownav=$shownav,$fieldid,$fieldref,$morehtmlref,$moreparam";
			//$object->load_previous_next_ref((isset($object->next_prev_filter) ? $object->next_prev_filter : ''), $fieldid, $nodbprefix);

			$navurl = $object->retour;

			// Special case for project/task page
			if ($paramid == 'project_ref') {
				if (preg_match('/\/tasks\/(task|contact|note|document)\.php/', $navurl)) {     // TODO Remove object when nav with project_ref on task pages are ok
					$navurl = preg_replace('/\/tasks\/(task|contact|time|note|document)\.php/', '/tasks.php', $navurl);
					$paramid = 'ref';
				}
			}

			// accesskey is for Windows or Linux:  ALT + key for chrome, ALT + SHIFT + KEY for firefox
			// accesskey is for Mac:               CTRL + key for all browsers
			$stringforfirstkey = $langs->trans("KeyboardShortcut");
			if ($conf->browser->name == 'chrome') {
				$stringforfirstkey .= ' ALT +';
			} elseif ($conf->browser->name == 'firefox') {
				$stringforfirstkey .= ' ALT + SHIFT +';
			} else {
				$stringforfirstkey .= ' CTL +';
			}

			$previous_ref = $object->retour ? '<a accesskey="p" title="'.$stringforfirstkey.' p" class="classfortooltip" href="'.$navurl.'"><b>Retour liste</a></b>' : '<span class="inactive">Retour liste</span>';
			$previous_ref .= $object->ref_previous ?'<a title="'.$object->nom_precedent.'" href="'.$object->ref_previous.'"><i class="fa fa-chevron-left"></i></a>' : '<span class="inactive"><i class="fa fa-chevron-left opacitymedium"></i></span>';
			$next_ref     = $object->ref_next ? '<a title="'.$object->nom_suivant.'" href="'.$object->ref_next.'" ><i class="fa fa-chevron-right"></i></a>' : '<span class="inactive"><i class="fa fa-chevron-right opacitymedium"></i></span>';

			//$next_ref     = $object->ref_next ? '<a accesskey="n" title="'.$stringforfirstkey.' n" class="classfortooltip" href="'.$navurl.'?'.$paramid.'='.urlencode($object->ref_next).$moreparam.'"><i class="fa fa-chevron-right"></i></a>' : '<span class="inactive"><i class="fa fa-chevron-right opacitymedium"></i></span>';
		}

		//print "xx".$previous_ref."x".$next_ref;
		$ret .= '<!-- Start banner content --><div style="vertical-align: middle">';

		// Right part of banner
		if ($morehtmlright) {
			$ret .= '<div class="inline-block floatleft">'.$morehtmlright.'</div>';
		}

		if ($previous_ref || $next_ref || $morehtml) {
			$ret .= '<div class="pagination paginationref"><ul class="right">';
		}
		if ($morehtml) {
			$ret .= '<li class="noborder litext'.(($shownav && $previous_ref && $next_ref) ? ' clearbothonsmartphone' : '').'">'.$morehtml.'</li>';
		}
		if ($shownav && ($previous_ref || $next_ref)) {
			$ret .= '<li class="pagination">'.$previous_ref.'</li>';
			$ret .= '<li class="pagination">'.$next_ref.'</li>';
		}
		if ($previous_ref || $next_ref || $morehtml) {
			$ret .= '</ul></div>';
		}

		$parameters = array();
		$reshook = $hookmanager->executeHooks('moreHtmlStatus', $parameters, $object); // Note that $action and $object may have been modified by hook
		if (empty($reshook)) {
			$morehtmlstatus .= $hookmanager->resPrint;
		} else {
			$morehtmlstatus = $hookmanager->resPrint;
		}
		if ($morehtmlstatus) {
			$ret .= '<div class="statusref">'.$morehtmlstatus.'</div>';
		}

		$parameters = array();
		$reshook = $hookmanager->executeHooks('moreHtmlRef', $parameters, $object); // Note that $action and $object may have been modified by hook
		if (empty($reshook)) {
			$morehtmlref .= $hookmanager->resPrint;
		} elseif ($reshook > 0) {
			$morehtmlref = $hookmanager->resPrint;
		}

		// Left part of banner
		if ($morehtmlleft) {
			if ($conf->browser->layout == 'phone') {
				$ret .= '<!-- morehtmlleft --><div class="floatleft">'.$morehtmlleft.'</div>'; // class="center" to have photo in middle
			} else {
				$ret .= '<!-- morehtmlleft --><div class="inline-block floatleft">'.$morehtmlleft.'</div>';
			}
		}

		//if ($conf->browser->layout == 'phone') $ret.='<div class="clearboth"></div>';
		$ret .= '<div class="inline-block floatleft valignmiddle maxwidth750 marginbottomonly refid'.(($shownav && ($previous_ref || $next_ref)) ? ' refidpadding' : '').'">';

		// For thirdparty, contact, user, member, the ref is the id, so we show something else
		if ($object->element == 'societe') { //Affichage du nom de la du vehicule si on est dans du vehicule
			$ret .= "<a title='Voir les informations de la du vehicule dans Tiers' href='../../societe/card.php?socid=".$object->rowid."'>".dol_htmlentities($object->nom)."</a>";
			// List of extra languages
			$arrayoflangcode = array();
			if (!empty($conf->global->PDF_USE_ALSO_LANGUAGE_CODE)) {
				$arrayoflangcode[] = $conf->global->PDF_USE_ALSO_LANGUAGE_CODE;
			}

			if (is_array($arrayoflangcode) && count($arrayoflangcode)) {
				if (!is_object($extralanguages)) {
					include_once DOL_DOCUMENT_ROOT.'/core/class/extralanguages.class.php';
					$extralanguages = new ExtraLanguages($object->db);
				}
				$extralanguages->fetch_name_extralanguages('societe');

				if (!empty($extralanguages->attributes['societe']['name'])) {
					$object->fetchValuesForExtraLanguages();

					$htmltext = '';
					// If there is extra languages
					foreach ($arrayoflangcode as $extralangcode) {
						$htmltext .= picto_from_langcode($extralangcode, 'class="pictoforlang paddingright"');
						if ($object->array_languages['name'][$extralangcode]) {
							$htmltext .= $object->array_languages['name'][$extralangcode];
						} else {
							$htmltext .= '<span class="opacitymedium">'.$langs->trans("SwitchInEditModeToAddTranslation").'</span>';
						}
					}
					$ret .= '<!-- Show translations of name -->'."\n";
					$ret .= $object->textwithpicto('', $htmltext, -1, 'language', 'opacitymedium paddingleft');
				}
			}
		} elseif ($object->element == 'member') {
			$ret .= $object->ref.'<br>';
			$fullname = $object->getFullName($langs);
			if ($object->morphy == 'mor' && $object->societe) {
				$ret .= dol_htmlentities($object->societe).((!empty($fullname) && $object->societe != $fullname) ? ' ('.dol_htmlentities($fullname).$addgendertxt.')' : '');
			} else {
				$ret .= dol_htmlentities($fullname).$addgendertxt.((!empty($object->societe) && $object->societe != $fullname) ? ' ('.dol_htmlentities($object->societe).')' : '');
			}
		} elseif (in_array($object->element, array('contact', 'usergroup'))) {
			$ret .= dol_htmlentities($object->getFullName($langs));
		}
		elseif($object->element=='commande'){//Affichage du nom de la du vehicule dans salarié
			$ret .= dol_htmlentities($object->name);
		} elseif (in_array($object->element, array('action', 'agenda'))) {
			$ret .= $object->ref.'<br>'.$object->label;
		} elseif (in_array($object->element, array('adherent_type'))) {
			$ret .= $object->label;
		} elseif ($object->element == 'ecm_directories') {
			$ret .= '';
		} elseif ($fieldref != 'none') {
			$ret .= dol_htmlentities($object->$fieldref);
		}

		if ($morehtmlref) {
			// don't add a additional space, when "$morehtmlref" starts with a HTML div tag
			if (substr($morehtmlref, 0, 4) != '<div') {
				$ret .= ' ';
			}

			$ret .= $morehtmlref;
		}

		$ret .= '</div>';

		$ret .= '</div><!-- End banner content -->';

		return $ret;
	}

	/**
	 *$object   doit avoir la propriété address
	 */

function getAdressComplet($object){
		$out = '';

		$outdone = 0;
		$coords = $object->address;
		if ($coords) {
			if (!empty($conf->use_javascript_ajax)) {
				// Add picto with tooltip on map
				$namecoords = '';
				if ($object->element == 'contact' && !empty($conf->global->MAIN_SHOW_COMPANY_NAME_IN_BANNER_ADDRESS)) {
					$namecoords .= $object->name.'<br>';
				}
				$namecoords .= $object->getFullName($langs, 1).'<br>'.$coords;
				// hideonsmatphone because copyToClipboard call jquery dialog that does not work with jmobile
				$out .= '<a href="#" class="hideonsmartphone" onclick="return copyToClipboard(\''.dol_escape_js($namecoords).'\',\''.dol_escape_js($langs->trans("HelpCopyToClipboard")).'\');">';
				$out .= img_picto($langs->trans("Address"), 'map-marker-alt');
				$out .= '</a> ';
			}
			$out .= dol_print_address($coords, 'address_'.$htmlkey.'_'.$object->id, $object->element, $object->id, 1, ', ');
			$outdone++;
			$outdone++;

			// List of extra languages
			$arrayoflangcode = array();
			if (!empty($conf->global->PDF_USE_ALSO_LANGUAGE_CODE)) {
				$arrayoflangcode[] = $conf->global->PDF_USE_ALSO_LANGUAGE_CODE;
			}

			if (is_array($arrayoflangcode) && count($arrayoflangcode)) {
				if (!is_object($extralanguages)) {
					include_once DOL_DOCUMENT_ROOT.'/core/class/extralanguages.class.php';
					$extralanguages = new ExtraLanguages($object->db);
				}
				$extralanguages->fetch_name_extralanguages($elementforaltlanguage);

				if (!empty($extralanguages->attributes[$elementforaltlanguage]['address']) || !empty($extralanguages->attributes[$elementforaltlanguage]['town'])) {
					$out .= "<!-- alternatelanguage for '".$elementforaltlanguage."' set to fields '".join(',', $extralanguages->attributes[$elementforaltlanguage])."' -->\n";
					$object->fetchValuesForExtraLanguages();
					if (!is_object($form)) {
						$form = new Form($object->db);
					}
					$htmltext = '';
					// If there is extra languages
					foreach ($arrayoflangcode as $extralangcode) {
						$s = picto_from_langcode($extralangcode, 'class="pictoforlang paddingright"');
						$coords = $object->getFullAddress(1, ', ', $conf->global->MAIN_SHOW_REGION_IN_STATE_SELECT, $extralangcode);
						$htmltext .= $s.dol_print_address($coords, 'address_'.$htmlkey.'_'.$object->id, $object->element, $object->id, 1, ', ');
					}
					$out .= $form->textwithpicto('', $htmltext, -1, 'language', 'opacitymedium paddingleft');
				}
			}
		}

			if (!empty($conf->global->MAIN_SHOW_REGION_IN_STATE_SELECT) && $conf->global->MAIN_SHOW_REGION_IN_STATE_SELECT == 1 && $object->region) {
				$out .= ($outdone ? ' - ' : '').$object->region.' - '.$object->state;
			} else {
				$out .= ($outdone ? ' - ' : '').$object->state;
			}
			$outdone++;


		if (!empty($object->phone) || !empty($object->phone_pro) || !empty($object->phone_mobile) || !empty($object->phone_perso) || !empty($object->fax) || !empty($object->office_phone) || !empty($object->user_mobile) || !empty($object->office_fax)) {
			$out .= ($outdone ? '<br>' : '');
		}
		if (!empty($object->phone) && empty($object->phone_pro)) {		// For objects that store pro phone into ->phone
			$out .= dol_print_phone($object->phone, $object->country_code, $contactid, $thirdpartyid, 'AC_TEL', '&nbsp;', 'phone', "");
			$outdone++;
		}
		if (!empty($object->phone_pro)) {
			$out .= dol_print_phone($object->phone_pro, $object->country_code, $contactid, $thirdpartyid, 'AC_TEL', '&nbsp;', 'phone', "");
			$outdone++;
		}
		if (!empty($object->phone_mobile)) {
			$out .= dol_print_phone($object->phone_mobile, $object->country_code, $contactid, $thirdpartyid, 'AC_TEL', '&nbsp;', 'mobile', "");
			$outdone++;
		}
		if (!empty($object->phone_perso)) {
			$out .= dol_print_phone($object->phone_perso, $object->country_code, $contactid, $thirdpartyid, 'AC_TEL', '&nbsp;', 'phone', "");
			$outdone++;
		}
		if (!empty($object->office_phone)) {
			$out .= dol_print_phone($object->office_phone, $object->country_code, $contactid, $thirdpartyid, 'AC_TEL', '&nbsp;', 'phone', "");
			$outdone++;
		}
		if (!empty($object->user_mobile)) {
			$out .= dol_print_phone($object->user_mobile, $object->country_code, $contactid, $thirdpartyid, 'AC_TEL', '&nbsp;', 'mobile', "");
			$outdone++;
		}
		if (!empty($object->fax)) {
			$out .= dol_print_phone($object->fax, $object->country_code, $contactid, $thirdpartyid, 'AC_FAX', '&nbsp;', 'fax', "");
			$outdone++;
		}
		if (!empty($object->office_fax)) {
			$out .= dol_print_phone($object->office_fax, $object->country_code, $contactid, $thirdpartyid, 'AC_FAX', '&nbsp;', 'fax', "");
			$outdone++;
		}

		if ($out) {
			$out .= '<div style="clear: both;"></div>';
		}
		$outdone = 0;
		if (!empty($object->email)) {
			$out .= dol_print_email($object->email, $object->id, $object->id, 'AC_EMAIL', 0, 0, 1);
			$outdone++;
		}
		if (!empty($object->url)) {
			//$out.=dol_print_url($object->url,'_goout',0,1);//steve changed to blank
			$out .= dol_print_url($object->url, '_blank', 0, 1);
	}
	if($out)
		return $out;
	else return '';

}

/**
 * cette fonction prepare l'objet entête salarié a afficher.
 * Et après cet objet sera donnée à la fonction entete_societe
 */
function prepare_objet_entete($id_vehicule, $db){

	
	$soc_sql = "SELECT * FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$id_vehicule;
	$soc_res = $db->query($soc_sql);//= $db->query($covSql);
	$obj_vehicule = $db->fetch_object($soc_res);

	//Assignateur
	$soc_sql = "SELECT u.firstname, u.lastname, av.rowid FROM ".MAIN_DB_PREFIX."user as u";
	$soc_sql .= " LEFT JOIN ".MAIN_DB_PREFIX."assignation_vehicule as av on u.rowid = av.fk_user WHERE fk_vehicule=".$id_vehicule;
	$soc_sql .= " ORDER BY av.rowid DESC";
	$soc_res_user = $db->query($soc_sql);//= $db->query($covSql);
	$obj_user_assign = $db->fetch_object($soc_res_user);

	//type
	$soc_sql_type = "SELECT nom FROM ".MAIN_DB_PREFIX."type_vehicule WHERE rowid=".$obj_vehicule->fk_type_vehicule;
	$soc_res_type = $db->query($soc_sql_type);//= $db->query($covSql);
	$obj_type_vehicule = $db->fetch_object($soc_res_type);

	$desc = "Réference interne : ".$obj_vehicule->reference_interne."<br>";
	$desc .= "Plaque immatriculation : ".$obj_vehicule->plaque_immatriculation."<br>";
	$etat = img_picto('', 'tick');
	if($obj_vehicule->panne)
		$etat = '❌';

	$desc .= "Etat : ".$etat."<br>";
	$desc .= "Assigné à : ".$obj_user_assign->lastname." ".$obj_user_assign->firstname;


	//$desc .= "Crée par : ".$obj_soc->lastname." ".$obj_soc->firstname."<br>";

	$obj_vehicule->name = $obj_vehicule->nom;
	$obj_vehicule->element = "commande";
	$obj_vehicule->socid = $obj_vehicule->rowid; //id de la du vehicule
	//$info = $obj_vehicule->designation;//."  ".$obj_vehicule->lastname."<br> Fonction : ".$obj_vehicule->job."".($obj_vehicule->office_phone?("<br> Tel : ".$obj_vehicule->office_phone):($obj_vehicule->office_fax?("<br> Tel : ".$obj_vehicule->office_fax) : ($obj_vehicule->user_mobile? ("<br> Tel : ".$obj_vehicule->user_mobile):"")))."";//"Matricule : ".$existSalarie->identifiant."<br>Catégorie : ".$catSalarie->code_categorie."".($echelon?" ==> ".$echelon:"");
	$obj_vehicule->address = $desc;

	if($obj_vehicule->actif == 1)
		$obj_vehicule->retour = '../creation_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&etat=active';
	else
		$obj_vehicule->retour = '../creation_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&etat=inactive';
	
	//preparation du véhicule suivant et precedant
	//Precedant
		if($obj_vehicule->actif){
			$sql_prev = "SELECT rowid, nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE actif = 1 AND rowid<".$id_vehicule;
		}else{
			$sql_prev = "SELECT rowid, nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE actif = 0 AND rowid<".$id_vehicule;
		}

	$sql_prev .= " ORDER BY rowid DESC";

	$soc_res_prev = $db->query($sql_prev);//= $db->query($covSql);
	$nom_identifiant_prev = "";
	if($soc_res_prev)
		if($db->num_rows($soc_res_prev)>0){
			$obj_prev = $db->fetch_object($soc_res_prev);
				$obj_vehicule->ref_previous = './detail_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&id_vehicule='.$obj_prev->rowid;
			
			$nom_identifiant_prev = $obj_prev->nom.' '.$obj_prev->reference_interne;

		}
	
	//Suivant
		if($obj_vehicule->actif){
				$sql_next = "SELECT rowid, nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE actif = 1 AND rowid>".$id_vehicule;
		}else{
			$sql_next = "SELECT rowid, nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE actif = 0 AND rowid>".$id_vehicule;
		}

		$sql_prev .= " ORDER BY rowid";
	$soc_res_next = $db->query($sql_next);//= $db->query($covSql);
	$nom_identifiant_next = "";
	if($soc_res_next)
		if($db->num_rows($soc_res_next)>0){
			$obj_next = $db->fetch_object($soc_res_next);

				$obj_vehicule->ref_next = './detail_vehicule.php?mainmenu=gestionflotte&leftmenu=gestionvehicule&id_vehicule='.$obj_next->rowid;
			
			$nom_identifiant_next = $obj_next->nom.' '.$obj_next->reference_interne;
		}


	$obj_vehicule->nom_precedent = $nom_identifiant_prev;
	$obj_vehicule->nom_suivant = $nom_identifiant_next;

	return $obj_vehicule;
}




//---------------------------------------------------------------------------------------------
/**
 *  Show tab footer of a card.
 *  Note: $object->next_prev_filter can be set to restrict select to find next or previous record by $form->afficher.
 *
 *  @param	Object	$object			Object to show
 *  @param	string	$paramid   		Name of parameter to use to name the id into the URL next/previous link
 *  @param	string	$morehtml  		More html content to output just before the nav bar
 *  @param	int		$shownav	  	Show Condition (navigation is shown if value is 1)
 *  @param	string	$fieldid   		Nom du champ en base a utiliser pour select next et previous (we make the select max and min on object field). Use 'none' for no prev/next search.
 *  @param	string	$fieldref   	Nom du champ objet ref (object->ref) a utiliser pour select next et previous
 *  @param	string	$morehtmlref  	More html to show after the ref (see $morehtmlleft for before)
 *  @param	string	$moreparam  	More param to add in nav link url.
 *	@param	int		$nodbprefix		Do not include DB prefix to forge table name
 *	@param	string	$morehtmlleft	More html code to show before the ref (see $morehtmlref for after)
 *	@param	string	$morehtmlstatus	More html code to show under navigation arrows
 *  @param  int     $onlybanner     Put object to 1, if the card will contains only a banner (object add css 'arearefnobottom' on div)
 *	@param	string	$morehtmlright	More html code to show before navigation arrows
 *  @return	void
 */
function entete_vehicule($object, $paramid, $morehtml = '', $shownav = 1, $fieldid = 'rowid', $fieldref = 'ref', $morehtmlref = '', $moreparam = '', $nodbprefix = 0, $morehtmlleft = '', $morehtmlstatus = '', $onlybanner = 0, $morehtmlright = '')
{
	global $conf, $form, $user, $langs, $hookmanager, $action;

	$error = 0;

	$maxvisiblephotos = 1;
	$showimage = 1;
	$entity = (empty($object->entity) ? $conf->entity : $object->entity);
	$showbarcode = empty($conf->barcode->enabled) ? 0 : (empty($object->barcode) ? 0 : 1);
	if (!empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->barcode->lire_advance)) {
		$showbarcode = 0;
	}
	$modulepart = 'unknown';
	if ($object->element == 'societe' || $object->element == 'contact' || $object->element == 'product' || $object->element == 'ticket') {
		$modulepart = $object->element;
	} elseif ($object->element == 'member') {
		$modulepart = 'memberphoto';
	} elseif ($object->element == 'user') {
		$modulepart = 'userphoto';
	}else{
		$modulepart = 'commande';
	}


	if (class_exists("Imagick")) {
		
		if ($object->element == 'expensereport' || $object->element == 'propal' || $object->element == 'commande' || $object->element == 'facture' || $object->element == 'supplier_proposal') {
			$modulepart = $object->element;
		} elseif ($object->element == 'fichinter') {
			$modulepart = 'ficheinter';
		} elseif ($object->element == 'contrat') {
			$modulepart = 'contract';
		} elseif ($object->element == 'order_supplier') {
			$modulepart = 'supplier_order';
		} elseif ($object->element == 'invoice_supplier') {
			$modulepart = 'supplier_invoice';
		}
	}
	/*$img = './../config/logo_societe/'.$object->rowid;
		if(file_exists($img.'.png')){
			$img .= '.png';
		}elseif(file_exists($img.'.jpg')){
			$img .= '.jpg';
		}else{
			$img .= '.jpeg';
		}*/

	if(is_readable($img)){
		$phototoshow = '<div class="photoref">';
		$phototoshow .= '<img height="60" class="photo photowithborder" src='.$img.'>';
		$phototoshow .= '</div>';
		$morehtmlleft .= '<div class="floatleft inline-block valignmiddle divphotoref">';
		$morehtmlleft .= $phototoshow;
		$morehtmlleft .= '</div>';

	}elseif ($showimage) {
		
			if ($modulepart != 'unknown') {
				$phototoshow = '';
				// Check if a preview file is available
				if (in_array($modulepart, array('propal', 'commande', 'facture', 'ficheinter', 'contract', 'supplier_order', 'supplier_proposal', 'supplier_invoice', 'expensereport')) && class_exists("Imagick")) {
					$objectref = dol_sanitizeFileName($object->ref);
					$dir_output = (empty($conf->$modulepart->multidir_output[$entity]) ? $conf->$modulepart->dir_output : $conf->$modulepart->multidir_output[$entity])."/";
					if (in_array($modulepart, array('invoice_supplier', 'supplier_invoice'))) {
						$subdir = get_exdir($object->id, 2, 0, 1, $object, $modulepart);
						$subdir .= ((!empty($subdir) && !preg_match('/\/$/', $subdir)) ? '/' : '').$objectref; // the objectref dir is not included into get_exdir when used with level=2, so we add it at end
					} else {
						$subdir = get_exdir($object->id, 0, 0, 1, $object, $modulepart);
					}
					if (empty($subdir)) {
						$subdir = 'errorgettingsubdirofobject'; // Protection to avoid to return empty path
					}

					$filepath = $dir_output.$subdir."/";

					$filepdf = $filepath.$objectref.".pdf";
					$relativepath = $subdir.'/'.$objectref.'.pdf';

					// Define path to preview pdf file (preview precompiled "file.ext" are "file.ext_preview.png")
					$fileimage = $filepdf.'_preview.png';
					$relativepathimage = $relativepath.'_preview.png';

					$pdfexists = file_exists($filepdf);

					// If PDF file exists
					if ($pdfexists) {
						// Conversion du PDF en image png si fichier png non existant
						if (!file_exists($fileimage) || (filemtime($fileimage) < filemtime($filepdf))) {
							if (empty($conf->global->MAIN_DISABLE_PDF_THUMBS)) {		// If you experience trouble with pdf thumb generation and imagick, you can disable here.
								include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
								$ret = dol_convert_file($filepdf, 'png', $fileimage, '0'); // Convert first page of PDF into a file _preview.png
								if ($ret < 0) {
									$error++;
								}
							}
						}
					}

					if ($pdfexists && !$error) {
						$heightforphotref = 80;
						if (!empty($conf->dol_optimize_smallscreen)) {
							$heightforphotref = 60;
						}
						// If the preview file is found
						if (file_exists($fileimage)) {
							$phototoshow = '<div class="photoref">';
							$phototoshow .= '<img height="'.$heightforphotref.'" class="photo photowithmargin photowithborder" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=apercu'.$modulepart.'&amp;file='.urlencode($relativepathimage).'">';
							$phototoshow .= '</div>';
						}
					}
				} elseif (!$phototoshow) { // example if modulepart = 'societe' or 'photo'
					$phototoshow .= $form->showphoto($modulepart, $object, 0, 0, 0, 'photowithmargin photoref', 'small', 1, 0, $maxvisiblephotos);

					print $phototoshow;
				}

				if ($phototoshow) {
					$morehtmlleft .= '<div class="floatleft inline-block valignmiddle divphotoref">';
					$morehtmlleft .= $phototoshow;
					$morehtmlleft .= '</div>';
				}
			}

			if (empty($phototoshow)) {      // Show No photo link (picto of object)
				if ($object->element == 'action') {
					$width = 80;
					$cssclass = 'photorefcenter';
					$nophoto = img_picto('No photo', 'title_agenda');
				} else {
					$width = 14;
					$cssclass = 'photorefcenter';
					$picto = $object->picto;
					if ($object->element == 'project' && !$object->public) {
						$picto = 'project'; // instead of projectpub
					}
					//-----------------------------------------------------------
					if($object->img)
						$nophoto = img_picto('No photo', 'image_vehicule/'.$object->img, 'style="width:90px; height:auto;"');
					else $nophoto = img_picto('No photo', '');
					//-----------------------------------------------------------
				}
				$morehtmlleft .= '<!-- No photo to show -->';
				$morehtmlleft .= '<div class="floatleft inline-block valignmiddle divphotoref"><div class="photoref">';
				$morehtmlleft .= $nophoto;
				$morehtmlleft .= '</div></div>';
			}
		}

		$moreaddress = getAdressComplet($object);
		if ($moreaddress) {
			if($object->element=='user')
			 $moreaddress = explode("-", $moreaddress)[0];

			$morehtmlref .= '<div class="refidno">';
			$morehtmlref .= $moreaddress;
			$morehtmlref .= '</div>';
		}

	print '<div class="'.($onlybanner ? 'arearefnobottom ' : 'arearef ').'heightref valignmiddle centpercent">';
	print afficher($object, $paramid, $morehtml, $shownav, $fieldid, $fieldref, $morehtmlref, $moreparam, $nodbprefix, $morehtmlleft, $morehtmlstatus, $morehtmlright);
	print '</div>';
	print '<div class="underrefbanner clearboth"></div>';
}

/**
 * cette fonction prepare l'objet entête salarié a afficher.
 * Et après cet objet sera donnée à la fonction entete_societe
 */
function prepare_objet_entete_carburant($id_carburant, $db){

	
	$soc_sql = "SELECT * FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE rowid=".$id_carburant;
	$soc_res = $db->query($soc_sql);//= $db->query($covSql);
	$obj_main_carburant = $db->fetch_object($soc_res);

	//Véhicule
	$soc_sql = "SELECT nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$obj_main_carburant->fk_vehicule;
	$soc_res_veh = $db->query($soc_sql);//= $db->query($covSql);
	$obj_veh = $db->fetch_object($soc_res_veh);
	
	//Assignateur
	$soc_sql = "SELECT u.firstname, u.lastname, av.rowid FROM ".MAIN_DB_PREFIX."user as u";
	$soc_sql .= " LEFT JOIN ".MAIN_DB_PREFIX."assignation_vehicule as av on u.rowid = av.fk_user WHERE fk_vehicule=".$obj_main_carburant->fk_vehicule;
	$soc_sql .= " ORDER BY av.rowid DESC";
	$soc_res_user = $db->query($soc_sql);//= $db->query($covSql);
	$obj_user_assign = $db->fetch_object($soc_res_user);

	$sql_user = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid = ".$obj_main_carburant->fk_user_creation;
	$res_user = $db->query($sql_user);
	if ($res_user) {
		$num_user = $db->num_rows($res_user);
		$xy = 0;
		if (0 < $num_user) {
			$obj_user = $db->fetch_object($res_user);
		}

	}

	$desc = "Véhicule : ".$obj_veh->nom." | ".$obj_veh->reference_interne."<br>";

	$desc .= "Assigné à : ".$obj_user_assign->lastname." ".$obj_user_assign->firstname."<br>";

	$desc .= "Crée par : ".$obj_user->lastname." ".$obj_user->firstname."<br>";

	//Valider ou rejeter par
	if($obj_main_carburant->fk_user_valider_rejeter){
		$soc_sql = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$obj_main_carburant->fk_user_valider_rejeter;
		$soc_res = $db->query($soc_sql);//= $db->query($covSql);
		$obj_soc = $db->fetch_object($soc_res);

		if($obj_main_carburant->valider)
			$desc .= "Validé par : ".$obj_soc->lastname." ".$obj_soc->firstname."<br>";

		if($obj_main_carburant->rejeter)
			$desc .= "Rejeté par : ".$obj_soc->lastname." ".$obj_soc->firstname;
	}

	//Approuver par
	if($obj_main_carburant->fk_user_approuver){
		$soc_sql = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$obj_main_carburant->fk_user_approuver;
		$soc_res = $db->query($soc_sql);//= $db->query($covSql);
		$obj_soc = $db->fetch_object($soc_res);
			$desc .= "Approuver par : ".$obj_soc->lastname." ".$obj_soc->firstname."<br>";
	}

	$obj_main_carburant->name = $obj_main_carburant->libelle;
	$obj_main_carburant->element = "commande";
	$obj_main_carburant->socid = $obj_main_carburant->rowid; //id du besoin

	//$info = $obj_main_carburant->designation;//."  ".$obj_main_carburant->lastname."<br> Fonction : ".$obj_main_carburant->job."".($obj_main_carburant->office_phone?("<br> Tel : ".$obj_main_carburant->office_phone):($obj_main_carburant->office_fax?("<br> Tel : ".$obj_main_carburant->office_fax) : ($obj_main_carburant->user_mobile? ("<br> Tel : ".$obj_main_carburant->user_mobile):"")))."";//"Matricule : ".$existSalarie->identifiant."<br>Catégorie : ".$catSalarie->code_categorie."".($echelon?" ==> ".$echelon:"");
	if ($obj_main_carburant->valider == 0 && $obj_main_carburant->rejeter == 0) {
		$obj_main_carburant->address = $desc."<br>";
		$obj_main_carburant->address .= "État : non soumis";
	}else $obj_main_carburant->address = $desc;

	if($obj_main_carburant->valider)
		$obj_main_carburant->retour = './carburant.php?mainmenu=gestionflotte&leftmenu=carburant&action=valider';
	elseif($obj_main_carburant->rejeter)
		$obj_main_carburant->retour = './carburant.php?mainmenu=gestionflotte&leftmenu=carburant&action=rejeter';
	elseif($obj_main_carburant->approuver)
		$obj_main_carburant->retour = './carburant.php?mainmenu=gestionflotte&leftmenu=carburant&action=approuver';
	else
		$obj_main_carburant->retour = './carburant.php?mainmenu=gestionflotte&leftmenu=carburant&action=soumis';

	//preparation du besoin suivant et precedant
	//Precedant
		if($obj_main_carburant->valider){
			$sql_prev = "SELECT rowid, libelle FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE valider != 0 AND rowid < ".$id_carburant;
		}elseif($obj_main_carburant->rejeter){
			$sql_prev = "SELECT rowid, libelle FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE rejeter != 0 AND rowid < ".$id_carburant;
		}elseif($obj_main_carburant->approuver){
			$sql_prev = "SELECT rowid, libelle FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE approuver != 0 AND rowid < ".$id_carburant;
		}else{
			$sql_prev = "SELECT rowid, libelle FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE valider = 0 AND rejeter = 0 AND rowid < ".$id_carburant;
		}

		$sql_prev .= " ORDER BY rowid DESC";
		$soc_res_prev = $db->query($sql_prev);//= $db->query($covSql);
		$nom_prenom_prev = "";
		if($soc_res_prev)
			if($db->num_rows($soc_res_prev)>0){
				$obj_prev = $db->fetch_object($soc_res_prev);
				$obj_main_carburant->ref_previous = './carburant.php?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_prev->rowid.'&action=detail';
				
				$nom_prenom_prev = $obj_prev->libelle;

			}
	
	//Suivant
		if($obj_main_carburant->valider){
				$sql_next = "SELECT rowid, libelle FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE valider != 0 AND rowid > ".$id_carburant;
		}elseif($obj_main_carburant->rejeter){
			$sql_next = "SELECT rowid, libelle FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE rejeter != 0 AND rowid > ".$id_carburant;
		}elseif($obj_main_carburant->approuver){
			$sql_next = "SELECT rowid, libelle FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE approuver != 0 AND rowid > ".$id_carburant;
		}else{
			$sql_next = "SELECT rowid, libelle FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE valider = 0 AND rejeter = 0 AND rowid > ".$id_carburant;
		}

		$soc_res_next = $db->query($sql_next);//= $db->query($covSql);
		
	$nom_prenom_next = "";
	if($soc_res_next)
		if($db->num_rows($soc_res_next)>0){
			$obj_next = $db->fetch_object($soc_res_next);
			$obj_main_carburant->ref_next = './carburant.php?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_next->rowid.'&action=detail';
			
			$nom_prenom_next = $obj_next->libelle;
		}


	$obj_main_carburant->nom_precedent = $nom_prenom_prev;
	$obj_main_carburant->nom_suivant = $nom_prenom_next;

	return $obj_main_carburant;
}




//---------------------------------------------------------------------------------------------
/**
 *  Show tab footer of a card.
 *  Note: $object->next_prev_filter can be set to restrict select to find next or previous record by $form->afficher.
 *
 *  @param	Object	$object			Object to show
 *  @param	string	$paramid   		Name of parameter to use to name the id into the URL next/previous link
 *  @param	string	$morehtml  		More html content to output just before the nav bar
 *  @param	int		$shownav	  	Show Condition (navigation is shown if value is 1)
 *  @param	string	$fieldid   		Nom du champ en base a utiliser pour select next et previous (we make the select max and min on object field). Use 'none' for no prev/next search.
 *  @param	string	$fieldref   	Nom du champ objet ref (object->ref) a utiliser pour select next et previous
 *  @param	string	$morehtmlref  	More html to show after the ref (see $morehtmlleft for before)
 *  @param	string	$moreparam  	More param to add in nav link url.
 *	@param	int		$nodbprefix		Do not include DB prefix to forge table name
 *	@param	string	$morehtmlleft	More html code to show before the ref (see $morehtmlref for after)
 *	@param	string	$morehtmlstatus	More html code to show under navigation arrows
 *  @param  int     $onlybanner     Put object to 1, if the card will contains only a banner (object add css 'arearefnobottom' on div)
 *	@param	string	$morehtmlright	More html code to show before navigation arrows
 *  @return	void
 */
function entete_carburant($object, $paramid, $morehtml = '', $shownav = 1, $fieldid = 'rowid', $fieldref = 'ref', $morehtmlref = '', $moreparam = '', $nodbprefix = 0, $morehtmlleft = '', $morehtmlstatus = '', $onlybanner = 0, $morehtmlright = '')
{
	global $conf, $form, $user, $langs, $hookmanager, $action;

	$error = 0;

	$maxvisiblephotos = 1;
	$showimage = 1;
	$entity = (empty($object->entity) ? $conf->entity : $object->entity);
	$showbarcode = empty($conf->barcode->enabled) ? 0 : (empty($object->barcode) ? 0 : 1);
	if (!empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->barcode->lire_advance)) {
		$showbarcode = 0;
	}
	$modulepart = 'unknown';
	if ($object->element == 'societe' || $object->element == 'contact' || $object->element == 'product' || $object->element == 'ticket') {
		$modulepart = $object->element;
	} elseif ($object->element == 'member') {
		$modulepart = 'memberphoto';
	} elseif ($object->element == 'user') {
		$modulepart = 'userphoto';
	}else{
		$modulepart = 'commande';
	}


	if (class_exists("Imagick")) {
		
		if ($object->element == 'expensereport' || $object->element == 'propal' || $object->element == 'commande' || $object->element == 'facture' || $object->element == 'supplier_proposal') {
			$modulepart = $object->element;
		} elseif ($object->element == 'fichinter') {
			$modulepart = 'ficheinter';
		} elseif ($object->element == 'contrat') {
			$modulepart = 'contract';
		} elseif ($object->element == 'order_supplier') {
			$modulepart = 'supplier_order';
		} elseif ($object->element == 'invoice_supplier') {
			$modulepart = 'supplier_invoice';
		}
	}
	/*$img = './../config/logo_societe/'.$object->rowid;
		if(file_exists($img.'.png')){
			$img .= '.png';
		}elseif(file_exists($img.'.jpg')){
			$img .= '.jpg';
		}else{
			$img .= '.jpeg';
		}*/

	if(is_readable($img)){
		$phototoshow = '<div class="photoref">';
		$phototoshow .= '<img height="60" class="photo photowithborder" src='.$img.'>';
		$phototoshow .= '</div>';
		$morehtmlleft .= '<div class="floatleft inline-block valignmiddle divphotoref">';
		$morehtmlleft .= $phototoshow;
		$morehtmlleft .= '</div>';

	}elseif ($showimage) {
		
			if ($modulepart != 'unknown') {
				$phototoshow = '';
				// Check if a preview file is available
				if (in_array($modulepart, array('propal', 'commande', 'facture', 'ficheinter', 'contract', 'supplier_order', 'supplier_proposal', 'supplier_invoice', 'expensereport')) && class_exists("Imagick")) {
					$objectref = dol_sanitizeFileName($object->ref);
					$dir_output = (empty($conf->$modulepart->multidir_output[$entity]) ? $conf->$modulepart->dir_output : $conf->$modulepart->multidir_output[$entity])."/";
					if (in_array($modulepart, array('invoice_supplier', 'supplier_invoice'))) {
						$subdir = get_exdir($object->id, 2, 0, 1, $object, $modulepart);
						$subdir .= ((!empty($subdir) && !preg_match('/\/$/', $subdir)) ? '/' : '').$objectref; // the objectref dir is not included into get_exdir when used with level=2, so we add it at end
					} else {
						$subdir = get_exdir($object->id, 0, 0, 1, $object, $modulepart);
					}
					if (empty($subdir)) {
						$subdir = 'errorgettingsubdirofobject'; // Protection to avoid to return empty path
					}

					$filepath = $dir_output.$subdir."/";

					$filepdf = $filepath.$objectref.".pdf";
					$relativepath = $subdir.'/'.$objectref.'.pdf';

					// Define path to preview pdf file (preview precompiled "file.ext" are "file.ext_preview.png")
					$fileimage = $filepdf.'_preview.png';
					$relativepathimage = $relativepath.'_preview.png';

					$pdfexists = file_exists($filepdf);

					// If PDF file exists
					if ($pdfexists) {
						// Conversion du PDF en image png si fichier png non existant
						if (!file_exists($fileimage) || (filemtime($fileimage) < filemtime($filepdf))) {
							if (empty($conf->global->MAIN_DISABLE_PDF_THUMBS)) {		// If you experience trouble with pdf thumb generation and imagick, you can disable here.
								include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
								$ret = dol_convert_file($filepdf, 'png', $fileimage, '0'); // Convert first page of PDF into a file _preview.png
								if ($ret < 0) {
									$error++;
								}
							}
						}
					}

					if ($pdfexists && !$error) {
						$heightforphotref = 80;
						if (!empty($conf->dol_optimize_smallscreen)) {
							$heightforphotref = 60;
						}
						// If the preview file is found
						if (file_exists($fileimage)) {
							$phototoshow = '<div class="photoref">';
							$phototoshow .= '<img height="'.$heightforphotref.'" class="photo photowithmargin photowithborder" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=apercu'.$modulepart.'&amp;file='.urlencode($relativepathimage).'">';
							$phototoshow .= '</div>';
						}
					}
				} elseif (!$phototoshow) { // example if modulepart = 'societe' or 'photo'
					$phototoshow .= $form->showphoto($modulepart, $object, 0, 0, 0, 'photowithmargin photoref', 'small', 1, 0, $maxvisiblephotos);

					print $phototoshow;
				}

				if ($phototoshow) {
					$morehtmlleft .= '<div class="floatleft inline-block valignmiddle divphotoref">';
					$morehtmlleft .= $phototoshow;
					$morehtmlleft .= '</div>';
				}
			}

			if (empty($phototoshow)) {      // Show No photo link (picto of object)
				if ($object->element == 'action') {
					$width = 80;
					$cssclass = 'photorefcenter';
					$nophoto = img_picto('No photo', 'title_agenda');
				} else {
					$width = 14;
					$cssclass = 'photorefcenter';
					$picto = $object->picto;
					if ($object->element == 'project' && !$object->public) {
						$picto = 'project'; // instead of projectpub
					}
					$nophoto = img_picto('', 'carburant', 'class="paddingright pictofixedwidth"');
				}
				$morehtmlleft .= '<!-- No photo to show -->';
				$morehtmlleft .= '<div class="floatleft inline-block valignmiddle divphotoref"><div class="photoref">';
				$morehtmlleft .= $nophoto;
				$morehtmlleft .= '</div></div>';
			}
		}

		$moreaddress = getAdressComplet($object);
		if ($moreaddress) {
			if($object->element=='user')
			 $moreaddress = explode("-", $moreaddress)[0];

			$morehtmlref .= '<div class="refidno">';
			$morehtmlref .= $moreaddress;
			$morehtmlref .= '</div>';
		}

	print '<div class="'.($onlybanner ? 'arearefnobottom ' : 'arearef ').'heightref valignmiddle centpercent">';
	print afficher($object, $paramid, $morehtml, $shownav, $fieldid, $fieldref, $morehtmlref, $moreparam, $nodbprefix, $morehtmlleft, $morehtmlstatus, $morehtmlright);
	print '</div>';
	print '<div class="underrefbanner clearboth"></div>';
}

//Fonction semaine dans mois
function getWeeksOfMonth($year, $month) {
    $weeks = [];

    $start = new DateTime("$year-$month-01");
    $end = (clone $start)->modify('last day of this month');

    // 1) Première semaine : du 1er jusqu'au premier dimanche (inclus)
    $firstWeekStart = clone $start;
    $firstWeekEnd = (clone $start)->modify('sunday this week'); // donne le dimanche de la semaine du 1er (peut être le même jour)
    if ($firstWeekEnd > $end) {
        $firstWeekEnd = clone $end;
    }

    $weeks[] = [
        'debut' => $firstWeekStart->format('Y-m-d'),
        'fin'   => $firstWeekEnd->format('Y-m-d'),
    ];

    // 2) Semaines suivantes : démarrent le jour après ce dimanche et vont jusqu'au dimanche suivant
    $current = (clone $firstWeekEnd)->modify('+1 day');

    while ($current <= $end) {
        $weekStart = clone $current;
        $weekEnd = (clone $current)->modify('sunday this week');
        if ($weekEnd > $end) {
            $weekEnd = clone $end;
        }

        $weeks[] = [
            'debut' => $weekStart->format('Y-m-d'),
            'fin'   => $weekEnd->format('Y-m-d'),
        ];

        // passer au jour après ce dimanche
        $current = (clone $weekEnd)->modify('+1 day');
    }

    return $weeks;
}


	/**
 * Return an HTML table that contains a pie chart of the number of customers or supplier invoices
 *
 * @param 	string 	$mode 		Can be 'customers' or 'suppliers'
 * @return 	string 				A HTML table that contains a pie chart of customers or supplier invoices
 */
function getNumberCarburantPieChart()
{
	global $conf, $db, $langs, $user;

		include DOL_DOCUMENT_ROOT.'/theme/'.$conf->theme.'/theme_vars.inc.php';

		$sql = "SELECT";
		$sql .= " COUNT(*) as nbTotal";
		$sql .= ", sum(soumis = 1 AND valider = 0 AND rejeter = 0) as nbSoumis";
		$sql .= ", sum(soumis = 1 AND valider = 1 AND rejeter = 0 AND approuver = 0) as nbValider";
		$sql .= ", sum(soumis = 1 AND valider = 0 AND rejeter = 1 AND approuver = 0) as nbRejeter";
		$sql .= ", sum(approuver = 1) as nbApprouver";
		$sql .= " FROM ".MAIN_DB_PREFIX."carburant_vehicule";
		
		$sql .= " WHERE YEAR(date_demande) = ".(int)date('Y');

		$resql = $db->query($sql);
		if ($resql) {
			$num = $db->num_rows($resql);
			$i = 0;
			$total = 0;
			$dataseries = array();

			while ($i < $num) {
				$obj = $db->fetch_object($resql);
				
				$dataseries[$i] = array($langs->transnoentitiesnoconv('Demande de carburant ('.$obj->nbTotal.')'), $obj->nbSoumis, $obj->nbValider, $obj->nbRejeter, $obj->nbApprouver);
				$i++;
			}
			if (!empty($dataseries[0])) {
				foreach ($dataseries[0] as $key => $value) {
					if (is_numeric($value)) {
						$total += $value;
					}
				}
			}
			$legend = array(
				$langs->trans('Demandes soumises'),
				$langs->trans('Demandes validées'),
				$langs->trans('Demandes rejetées'),
				$langs->trans("Demandes approuvés")
			);

			$colorseries = array($badgeStatus1, $badgeStatus4, $badgeStatus8, '#0d7304ff', $badgeStatus11);

			$result = '<div class="div-table-responsive-no-min">';
			$result .= '<table class="noborder nohover centpercent">';
			$result .= '<tr class="liste_titre">';
			$result .= '<td>'.$langs->trans("Statistiques - Carburants ".date('Y'));
			$result .= '</td>';
			$result .= '</tr>';

			if ($conf->use_javascript_ajax) {
				//var_dump($dataseries);
				$dolgraph = new DolGraph();
				$dolgraph->SetData($dataseries);

				$dolgraph->setLegend($legend);

				$dolgraph->SetDataColor(array_values($colorseries));
				$dolgraph->setShowLegend(2);
				$dolgraph->setShowPercent(1);
				$dolgraph->SetType(array('bars', 'bars', 'bars', 'bars'));
				//$dolgraph->SetType(array('pie'));
				$dolgraph->setHeight('160');	/* 160 min is required to show the 6 lines of legend */
				$dolgraph->setWidth('450');
				$dolgraph->setHideXValues(true);
				
				$dolgraph->draw('idgraphcustomerinvoices');

				$result .= '<tr maxwidth="255">';
				$result .= '<td class="center">'.$dolgraph->show($total ? 0 : $langs->trans("NoOpenInvoice")).'</td>';
				$result .= '</tr>';
			} else {
				// Print text lines
			}

			$result .= '</table>';
			$result .= '</div>';

			return $result;
		} else {
			dol_print_error($db);
		}

}


	/**
 * Return an HTML table that contains a pie chart of the number of customers or supplier invoices
 *
 * @param 	string 	$mode 		Can be 'customers' or 'suppliers'
 * @return 	string 				A HTML table that contains a pie chart of customers or supplier invoices
 */
function getNumberDocumentPieChart()
{
	global $conf, $db, $langs, $user;

		include DOL_DOCUMENT_ROOT.'/theme/'.$conf->theme.'/theme_vars.inc.php';

		$interval30days = date_interval_create_from_date_string('30 days');

		$sql  = "SELECT";
		$sql .= " COUNT(*) AS nbTotal";
		$sql .= ", SUM(date_expiration >= CURDATE()) AS nbEncours";               // encore valides
		$sql .= ", SUM(date_expiration < CURDATE()) AS nbExpire";                 // expirées
		$sql .= ", SUM(DATEDIFF(date_expiration, CURDATE()) <= 90 AND date_expiration >= CURDATE() AND DATEDIFF(date_expiration, CURDATE()) > 30) AS nb3mois";  // expire dans 3 mois
		$sql .= ", SUM(DATEDIFF(date_expiration, CURDATE()) <= 30 AND date_expiration >= CURDATE()) AS nbmois";   // expire dans 1 mois
		$sql .= " FROM ".MAIN_DB_PREFIX."document_vehicule";
		$sql .= " WHERE YEAR(date_expiration) = ".(int)date('Y');


		$resql = $db->query($sql);
		if ($resql) {
			$num = $db->num_rows($resql);
			$i = 0;
			$total = 0;
			$dataseries = array();

			while ($i < $num) {
				$obj = $db->fetch_object($resql);
				
				$dataseries[$i] = array($langs->transnoentitiesnoconv('Documents véhicules ('.$obj->nbTotal.')' ), $obj->nbEncours, $obj->nb3mois, $obj->nbmois, $obj->nbExpire);
				$i++;
			}
			if (!empty($dataseries[0])) {
				foreach ($dataseries[0] as $key => $value) {
					if (is_numeric($value)) {
						$total += $value;
					}
				}
			}
			$legend = array(
				$langs->trans("Documents expirés"),
				$langs->trans('Expires dans 30 jours'),
				$langs->trans('Expires dans 90 jours'),
				$langs->trans('Documents valides')
			);

			$colorseries = array($badgeStatus8, $badgeStatus1, $badgeStatus4, '#0d7304ff', $badgeStatus11);

			$result = '<div class="div-table-responsive-no-min">';
			$result .= '<table class="noborder nohover centpercent">';
			$result .= '<tr class="liste_titre">';
			$result .= '<td>'.$langs->trans("Satistiques - Documents ".date('Y'));
			$result .= '</td>';
			$result .= '</tr>';

			if ($conf->use_javascript_ajax) {
				//var_dump($dataseries);
				$dolgraph = new DolGraph();
				$dolgraph->SetData($dataseries);

				$dolgraph->setLegend($legend);

				$dolgraph->SetDataColor(array_values($colorseries));
				$dolgraph->setShowLegend(2);
				$dolgraph->setShowPercent(1);
				$dolgraph->SetType(array('bars', 'bars', 'bars', 'bars'));
				//$dolgraph->SetType(array('pie'));
				$dolgraph->setHeight('160');	/* 160 min is required to show the 6 lines of legend */
				$dolgraph->setWidth('450');
				$dolgraph->setHideXValues(true);
				
				$dolgraph->draw('idgraphfourninvoices');

				$result .= '<tr maxwidth="255">';
				$result .= '<td class="center">'.$dolgraph->show($total ? 0 : $langs->trans("NoOpenInvoice")).'</td>';
				$result .= '</tr>';
			} else {
				// Print text lines
			}

			$result .= '</table>';
			$result .= '</div>';

			return $result;
		} else {
			dol_print_error($db);
		}

}


function avertissement($db, $id_vehicule){
	$nb_kilometre = 0;
	$nb_total_presume  = 0;
	$km_config = 0;
	$covSql = "SELECT valeur FROM ".MAIN_DB_PREFIX."gestion_flotte_alerte WHERE nom='kilometrage'";
	$res = $db->query($covSql);
	if($res){      
		$obj_alerte = $db->fetch_object($res);
		$km_config = $obj_alerte->valeur;
	}


	$sql = "SELECT rowid, date_maintenance, prochain_kilometrage FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE fk_type_maintenance = 1 AND maintenance_reparation = 1 AND fk_vehicule = ".$id_vehicule." ORDER BY date_maintenance DESC";
	$res = $db->query($sql);
	if($res){
		$obj_v = $db->fetch_object($res);
		$nb_total_presume = $obj_v->prochain_kilometrage;
		$sql_vidage = "SELECT rowid, kilometre FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE valider = 1 AND date_demande >=".$obj_v->date_maintenance;
		$result = $db->query($sql_vidage);
		if($result){
			while($obj_verif = $db->fetch_object($result)){
				$nb_kilometre += $obj_verif->kilometre;
			}
		}
	}

	if($nb_kilometre > 0 && $nb_total_presume > 0 && ($nb_kilometre + $km_config) >= $nb_total_presume){
		$info = "<h3 style='color:red;'>".img_picto('Attention', 'warning')." Le prochain vidange est dans ". ($nb_kilometre - $nb_total_presume).' kilomètres</h3>';
	}

	print $info;
}