<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2015 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015-2020 Juanjo Menent	<jmenent@2byte.es>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
 * Copyright (C) 2015      Raphaël Doursenaud   <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) 2016      Marcos García        <marcosgdf@gmail.com>
 * Copyright (C) 2019      Nicolas ZABOURI      <info@inovea-conseil.com>
 * Copyright (C) 2020      Tobias Sekan         <tobias.sekan@startmail.com>
 * Copyright (C) 2020      Josep Lluís Amador   <joseplluis@lliuretic.cat>
 * Copyright (C) 2021      Frédéric France		<frederic.france@netlogic.fr>
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
 *	\file       htdocs/compta/index.php
 *	\ingroup    compta
 *	\brief      Main page of accountancy area
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/gestionflotte/lib/gestionflotte.lib.php';
include_once DOL_DOCUMENT_ROOT.'/core/class/dolgraph.class.php';

llxHeader('', "Dépenses | Imputations");

print load_fiche_titre("Demande de carburant", '', 'carburant');

$aujourdhui = date('Y-m-d');

print '<div class="fichecenter"><div class="fichethirdleft">';
if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
	$carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule";
else $carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE cb.fk_user_creation=".$user->id;
	$res_carburant = $db->query($carburant);
	if($res_carburant){
		$num_num_carburant = $db->num_rows($res_carburant);
	}
	//$dataseries[] = array("Nombre total de vehicule", $num_num_carburant);

	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE soumis = 1 AND valider = 0 AND rejeter = 0";
	else $carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE soumis = 1 AND valider = 0 AND rejeter = 0 AND fk_user_creation=".$user->id;

	$res_carburant = $db->query($carburant);
	if($res_carburant){
		$num_num_carburant_bon_etat = $db->num_rows($res_carburant);
	}
	$dataseries[] = array("Demandes soumises", $num_num_carburant_bon_etat);

	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE rejeter = 1 AND approuver = 0";
	else $carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE  rejeter = 1 AND approuver = 0 AND fk_user_creation=".$user->id;

	$res_carburant = $db->query($carburant);
	if($res_carburant){
		$num_num_carburant_panne = $db->num_rows($res_carburant);
	}
	$dataseries[] = array("Demandes rejetées", $num_num_carburant_panne);

	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE valider = 1 AND approuver = 0";
	else $carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE  valider = 1 AND approuver = 0 AND fk_user_creation=".$user->id;

	$res_carburant = $db->query($carburant);
	if($res_carburant){
		$num_num_carburant_panne = $db->num_rows($res_carburant);
	}
	$dataseries[] = array("Demandes validées", $num_num_carburant_panne);

	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE approuver = 1";
	else $carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE  approuver = 1 AND fk_user_creation=".$user->id;

	$res_carburant = $db->query($carburant);
	if($res_carburant){
		$num_num_carburant_panne = $db->num_rows($res_carburant);
	}
	$dataseries[] = array("Demandes approuvées", $num_num_carburant_panne);

	$salarie_societe_graph = '<div class="div-table-responsive-no-min">';
	$salarie_societe_graph .= '<table class="noborder nohover centpercent">'."\n";
	$salarie_societe_graph .= '<tr class="liste_titre"><th colspan="2">'.$langs->trans("Statistique - Carburant").'</th></tr>';
	$dolgraph = new DolGraph();
	$dolgraph->SetData($dataseries);
	$dolgraph->setShowLegend(2);
	$dolgraph->setShowPercent(1);
	$dolgraph->SetType(array('pie'));
	$dolgraph->setHeight('200');

	// --- IMPORTANT : couleurs dans le même ordre que $dataseries
	$colors = array(
		'#3f473eff',//soumises
		'#920000ff', //rejetées
		'#949906ff', //validées
		'#06612cff', //approuvées
	);

$dolgraph->SetDataColor($colors);
	$dolgraph->draw('idgraphsalariesociete');
	$salarie_societe_graph .= '<tr><td>'.$dolgraph->show();
	$salarie_societe_graph .= '</td></tr>';
	$salarie_societe_graph .= '<tr class="liste_total"><td>Nombre total de demande</td><td class="right">';
	$salarie_societe_graph .= $num_num_carburant;
	$salarie_societe_graph .= '</td></tr>';
	$salarie_societe_graph .= '</table>';
	$salarie_societe_graph .= '</div>';

	print $salarie_societe_graph;
	print '<br>';
/*
 * Draft vehicule
 */
	$carburant  = 'SELECT cb.*, vh.rowid as vhrowid, vh.nom as vhnom, vh.img, vh.reference_interne, vh.fk_type_carburant, tc.nom AS nom_type_carburant';
		$carburant .= ' FROM '.MAIN_DB_PREFIX.'carburant_vehicule AS cb';
		$carburant .= ' LEFT JOIN '.MAIN_DB_PREFIX.'vehicule AS vh ON cb.fk_vehicule = vh.rowid';
		$carburant .= ' LEFT JOIN '.MAIN_DB_PREFIX.'type_carburant AS tc ON vh.fk_type_carburant = tc.rowid';

		if($user->id == 1 || $user->hasRight("gestionflotte", "gestionvehicule", "read"))
			$carburant .= ' WHERE 1 = 1';
		else $carburant .= ' WHERE cb.fk_user_creation='.$user->id;	

		$carburant .= " AND soumis = 1 AND valider = 0 AND rejeter = 0";
	$carburant .= " ORDER BY cb.date_demande DESC";

	$res_carburant = $db->query($carburant);
	print $db->error();
	if ($res_carburant) {
		print '<div class="div-table-responsive-no-min">';
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">Demandes soumises</th>';
		print '<th>Date demande</th></tr>';
		$langs->load("orders");
		$num = $db->num_rows($res_carburant);
		if ($num) {
			$i = 0;
			while ($i < $num) {
				$obj = $db->fetch_object($res_carburant);

				print '<tr class="oddeven">';
				print '<td class="nowrap">';
				print "<a title ='".$obj->vhnom." (".$obj->reference_interne.")' href='./onglets/carburant_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$obj->vhrowid."'>".$obj->libelle."</a>";
				print "</td>";
				print '<td class="nowrap">';
				$nom_prenon = "";
				$sql = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$obj->fk_user_creation;
				$res = $db->query($sql);
				if($res){
					$obj_user = $db->fetch_object($res);
				$nom_prenon .= $obj_user->lastname." ".$obj_user->firstname;

				}
		
				print "<a href='../user/card.php?id=".$obj->fk_user_creation."'>".$nom_prenon."</a>";
				print '</td>';
				print '<td>';
				print $obj->date_demande;
				print '</td></tr>';
				$i++;
			}
		} else {
			print '<tr class="oddeven"><td colspan="3">'.$langs->trans("Aucune demande trouvée").'</td></tr>';
		}
		print "</table></div><br>";
	}


print '</div><div class="fichetwothirdright">';


/*
 * Demande
 */

$sql_last_modif  = 'SELECT cb.*, vh.rowid as vhrowid, vh.nom as vhnom, vh.img, vh.reference_interne, vh.fk_type_carburant, tc.nom AS nom_type_carburant';
		$sql_last_modif .= ' FROM '.MAIN_DB_PREFIX.'carburant_vehicule AS cb';
		$sql_last_modif .= ' LEFT JOIN '.MAIN_DB_PREFIX.'vehicule AS vh ON cb.fk_vehicule = vh.rowid';
		$sql_last_modif .= ' LEFT JOIN '.MAIN_DB_PREFIX.'type_carburant AS tc ON vh.fk_type_carburant = tc.rowid';

		if($user->id == 1 || $user->hasRight("gestionflotte", "gestionvehicule", "read"))
			$sql_last_modif .= ' WHERE 1 = 1';
		else $sql_last_modif .= ' WHERE cb.fk_user_creation = '.$user->id;	
	$sql_last_modif .= " ORDER BY cb.date_demande DESC";


$resql = $db->query($sql_last_modif);
	print $db->error();

if ($resql) {
	$num = $db->num_rows($resql);

	startSimpleTable($langs->trans("Les 3 dernières demandes modifiées"), "./gestionflotte/carburant.php?mainmenu=gestionflotte&leftmenu=carburant", "", 2, -1, 'order');

	if ($num) {
		$i = 0;
		$max = 3;
		while ($i < $num && $i < $max) {
			$obj = $db->fetch_object($resql);

			print '<tr class="oddeven">';
			print '<td width="20%" class="nowrap">';

			print '<table class="nobordernopadding"><tr class="nocellnopadd">';
			print '<td width="96" class="nobordernopadding nowrap">';
			print "<a title ='".$obj->vhnom." (".$obj->reference_interne.")' href='./onglets/document_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$obj->vhrowid."'>".$obj->libelle."</a>";
			print '</td>';

			print '<td width="16" class="nobordernopadding nowrap">';
			print '&nbsp;';
			print '</td>';

			print '<td width="16" class="nobordernopadding hideonsmartphone right">';
			print '</td></tr></table>';
			print '</td>';

			print '<td class="nowrap">';
			$nom_prenon = "";
			$sql = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$obj->fk_user_creation;
            $res = $db->query($sql);
            if($res){
                $obj_user = $db->fetch_object($res);
               $nom_prenon .= $obj_user->lastname." ".$obj_user->firstname;

            }
			print "<a href='../user/card.php?id=".$obj->fk_user_creation."'>".$nom_prenon."</a>";			
			print '</td>';

			print '<td class="center">';
			print $obj->date_demande;
			print '</td>';

			// --- IMPORTANT : couleurs dans le même ordre que $dataseries
	$colors = array(
		'#3f473eff',//soumises
		'#920000ff', //rejetées
		'#949906ff', //validées
		'#06612cff', //approuvées
	);
			//----------------------------
            $status = '<span style="background-color: #106e05ff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 

            if ($obj->soumis = 1 && $obj->valider = 0 && $obj->rejeter = 0) {
                $status = '<span style="background-color: #3f473eff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 
            }

			if ($obj->rejeter = 1) {
                $status = '<span style="background-color: #920000ff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 
            }

			if ($obj->valider = 1 && $obj->approuver = 0) {
                $status = '<span style="background-color: #949906ff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 
            }

			if ($obj->approuver = 1) {
                $status = '<span style="background-color: #06612cff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 
            }

			print '<td class="right">'.$status.'</td>';
			print '</tr>';
			$i++;
		}
	}
	finishSimpleTable(true);
} else {
	//dol_print_error($db);
}


/*
 * vehicule to process
 */

$carburant  = 'SELECT cb.*, vh.rowid as vhrowid, vh.nom as vhnom, vh.img, vh.reference_interne, vh.fk_type_carburant, tc.nom AS nom_type_carburant';
		$carburant .= ' FROM '.MAIN_DB_PREFIX.'carburant_vehicule AS cb';
		$carburant .= ' LEFT JOIN '.MAIN_DB_PREFIX.'vehicule AS vh ON cb.fk_vehicule = vh.rowid';
		$carburant .= ' LEFT JOIN '.MAIN_DB_PREFIX.'type_carburant AS tc ON vh.fk_type_carburant = tc.rowid';

		if($user->id == 1 || $user->hasRight("gestionflotte", "gestionvehicule", "read"))
			$carburant .= ' WHERE 1 = 1';
		else $carburant .= ' WHERE cb.fk_user_creation='.$user->id;	
	
$carburant .= " AND valider = 1 AND approuver = 0";
	$carburant .= " ORDER BY cb.date_demande DESC";

$resql = $db->query($carburant);
	print $db->error();

	if ($resql) {
		$num = $db->num_rows($resql);

		print '<div class="div-table-responsive-no-min">';
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("Demandes validées").' <a href="./creation_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule"><span class="badge">'.$num.'</span></a></th></tr>';

		if ($num) {
			$i = 0;
			$max = 3;
			while ($i < $num && $i < $max) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven">';
				print '<td class="nowrap" width="20%">';

				print '<table class="nobordernopadding"><tr class="nocellnopadd">';
				print '<td width="96" class="nobordernopadding nowrap">';
				print img_picto("", "carburant", 'class="paddingright pictofixedwidth"')."<a href='./onglets/carburant_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$obj->vhrowid."'>".$obj->libelle."</a>";
				print '</td>';

				print '<td width="16" class="nobordernopadding nowrap">';
				print '&nbsp;';
				print '</td>';

				print '<td width="16" class="nobordernopadding hideonsmartphone right">';
				print '</td></tr></table>';

				print '</td>';

				print '<td class="nowrap">';
				$nom_prenon = "";
				$sql = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$obj->fk_user_creation;
				$res = $db->query($sql);
				if($res){
					$obj_user = $db->fetch_object($res);
				$nom_prenon .= $obj_user->lastname." ".$obj_user->firstname;

				}
				print "<a href='../user/card.php?id=".$obj->fk_user_creation."'>".$nom_prenon."</a>";
				print '</td>';

				print '<td class="right">'.$obj->date_demande.'</td>'."\n";

				//----------------------------

			//----------------------------
            $status = '<span style="background-color: #106e05ff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 

            if ($obj->panne) {
                $status = '<span style="background-color: #920000ff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 
            }

				print '<td class="right">'.$status.'</td>';
				print '</tr>';
				$i++;
			}
			if ($i < $num) {
				print '<tr><td><span class="opacitymedium">'.$langs->trans("More").'...</span></td><td></td><td></td><td></td></tr>';
			}
		}

		print "</table></div><br>";
	} else {
		//dol_print_error($db);
	}

/*
 * Orders that are in process
 */
$carburant  = 'SELECT cb.*, vh.rowid as vhrowid, vh.nom as vhnom, vh.img, vh.reference_interne, vh.fk_type_carburant, tc.nom AS nom_type_carburant';
		$carburant .= ' FROM '.MAIN_DB_PREFIX.'carburant_vehicule AS cb';
		$carburant .= ' LEFT JOIN '.MAIN_DB_PREFIX.'vehicule AS vh ON cb.fk_vehicule = vh.rowid';
		$carburant .= ' LEFT JOIN '.MAIN_DB_PREFIX.'type_carburant AS tc ON vh.fk_type_carburant = tc.rowid';

		if($user->id == 1 || $user->hasRight("gestionflotte", "gestionvehicule", "read"))
			$carburant .= ' WHERE 1 = 1';
		else $carburant .= ' WHERE cb.fk_user_creation='.$user->id;	
	
$carburant .= " AND rejeter = 1 AND approuver = 0";
	$carburant .= " ORDER BY cb.date_demande DESC";
	$resql = $db->query($carburant);
		print $db->error();

	if ($resql) {
		$num = $db->num_rows($resql);

		print '<div class="div-table-responsive-no-min">';
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("Demandes rejetées").' <a href="./creation_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&action=panne"><span class="badge">'.$num.'</span></a></th></tr>';

		if ($num) {
			$i = 0;
			$max = 3;
			while ($i < $num && $i < $max) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven">';
				print '<td class="nowrap" width="20%">';

				print '<table class="nobordernopadding"><tr class="nocellnopadd">';
				print '<td width="96" class="nobordernopadding nowrap">';
				print img_picto("", "carburant", 'class="paddingright pictofixedwidth"')."<a href='./onglets/carburant_vehicule.php?mainmenu=gestionflotte&leftmenu=&leftmenu=listevehicule&id_vehicule=".$obj->vhrowid."'>".$obj->libelle."</a>";
				print '</td>';

				print '<td width="16" class="nobordernopadding nowrap">';
				print '&nbsp;';
				print '</td>';

				print '<td width="16" class="nobordernopadding hideonsmartphone right">';
				print '</td></tr></table>';

				print '</td>';

				print '<td class="nowrap">';
				$nom_prenon = "";
				$sql = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$obj->fk_user_creation;
				$res = $db->query($sql);
				if($res){
					$obj_user = $db->fetch_object($res);
				$nom_prenon .= $obj_user->lastname." ".$obj_user->firstname;

				}
				print "<a href='../user/card.php?id=".$obj->fk_user_creation."'>".$nom_prenon."</a>";
				print '</td>';

				print '<td class="right">'.$obj->date_creation.'</td>'."\n";

				//----------------------------

            $status = '<span style="background-color: #920000ff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 

			print '<td class="right">'.$status.'</td>';
				print '</tr>';
				$i++;
			}
			if ($i < $num) {
				print '<tr><td><span class="opacitymedium">'.$langs->trans("More").'...</span></td><td></td><td></td><td></td></tr>';
			}
		}

		print "</table></div><br>";
	} else {
		//dol_print_error($db);
	}


print '</div></div>';

//-------------------------------------------------------------------------------------------------------------------------------------------
//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}