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
//require_once DOL_DOCUMENT_ROOT.'/core/lib/invoice.lib.php';

llxHeader('', "Dépenses | Imputations");

$aujourdhui = date('Y-m-d');

print '<div class="fichecenter"><div class="fichethirdleft">';
if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
	$sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule";
else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE fk_user_creation=".$user->id;
	$res_vehicule = $db->query($sql_vehicule);
	if($res_vehicule){
		$num_vehicule = $db->num_rows($res_vehicule);
	}
	//$dataseries[] = array("Nombre total de vehicule", $num_vehicule);

	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
		$sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0";
	else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND fk_user_creation=".$user->id;

	$res_vehicule = $db->query($sql_vehicule);
	if($res_vehicule){
		$num_vehicule_bon_etat = $db->num_rows($res_vehicule);
	}
	$dataseries[] = array("vehicule en bon état", $num_vehicule_bon_etat);

if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
	$sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 1";
else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 1 AND fk_user_creation=".$user->id;

	$res_vehicule = $db->query($sql_vehicule);
	if($res_vehicule){
		$num_vehicule_panne = $db->num_rows($res_vehicule);
	}
	$dataseries[] = array("vehicule en panne", $num_vehicule_panne);

	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
		$sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND rowid IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin IS NULL OR date_fin >='".$aujourdhui."')";
	else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND rowid IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin IS NULL OR date_fin >='".$aujourdhui."') AND fk_user_creation=".$user->id;

	$res_vehicule = $db->query($sql_vehicule);
	if($res_vehicule){
		$num_vehicule_bon_etat = $db->num_rows($res_vehicule);
	}
	$dataseries[] = array("vehicule assigné", $num_vehicule_bon_etat);

	$salarie_societe_graph = '<div class="div-table-responsive-no-min">';
	$salarie_societe_graph .= '<table class="noborder nohover centpercent">'."\n";
	$salarie_societe_graph .= '<tr class="liste_titre"><th colspan="2">'.$langs->trans("Statistique - Véhicules").'</th></tr>';
	$dolgraph = new DolGraph();
	$dolgraph->SetData($dataseries);
	$dolgraph->setShowLegend(2);
	$dolgraph->setShowPercent(1);
	$dolgraph->SetType(array('pie'));
	$dolgraph->setHeight('200');

	// --- IMPORTANT : couleurs dans le même ordre que $dataseries
	$colors = array(
		'#106e05ff',//bon état
		'#920000ff', //panne
		'#0b0532ff'//assigné
	);

$dolgraph->SetDataColor($colors);
	$dolgraph->draw('idgraphsalariesociete');
	$salarie_societe_graph .= '<tr><td>'.$dolgraph->show();
	$salarie_societe_graph .= '</td></tr>';
	$salarie_societe_graph .= '<tr class="liste_total"><td>Nombre total de véhicule</td><td class="right">';
	$salarie_societe_graph .= $num_vehicule;
	$salarie_societe_graph .= '</td></tr>';
	$salarie_societe_graph .= '</table>';
	$salarie_societe_graph .= '</div>';

	print $salarie_societe_graph;
	print '<br>';
/*
 * Draft vehicule
 */
if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
		$sql_vehicule = "SELECT rowid, reference_interne, fk_user_creation FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND rowid NOT IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin IS NULL OR date_fin <'".$aujourdhui."')";
	else $sql_vehicule = "SELECT rowid, reference_interne, fk_user_creation FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND rowid NOT IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin IS NULL OR date_fin <'".$aujourdhui."') AND fk_user_creation=".$user->id;

	$sql_vehicule .= " ORDER BY date_creation DESC";
	$res_vehicule = $db->query($sql_vehicule);
	print $db->error();
	if ($res_vehicule) {
		print '<div class="div-table-responsive-no-min">';
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">vehicules non assignés</th></tr>';
		$langs->load("orders");
		$num = $db->num_rows($res_vehicule);
		if ($num) {
			$i = 0;
			while ($i < $num) {
				$obj = $db->fetch_object($res_vehicule);

				print '<tr class="oddeven">';
				print '<td class="nowrap">';
				print "<a href='./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$obj->rowid."'>".$obj->reference_interne."</a>";
				print "</td>";
				print '<td class="nowrap">';
				$nom_prenon = "";
				$sql = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$obj->fk_user_creation;
				$res = $db->query($sql);
				if($res){
					$obj_user = $db->fetch_object($res);
				$nom_prenon .= $obj_user->lastname." ".$obj_user->firstname;

				}
		
				print "<a href='../user/card.php?id=".$obj->fk_user_creation."'>".$nom_prenon."</a>";;
				print '</td></tr>';
				$i++;
			}
		} else {
			print '<tr class="oddeven"><td colspan="3">'.$langs->trans("Aucun vehicule").'</td></tr>';
		}
		print "</table></div><br>";
	}

/*
 * Orders that are in process
 */
if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
	$sql_last_modif = "SELECT rowid, reference_interne, fk_user_creation, panne, date_creation FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 1";
else $sql_last_modif = "SELECT rowid, reference_interne, fk_user_creation, panne, date_creation FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 1";

$resql = $db->query($sql_last_modif);
	if ($resql) {
		$num = $db->num_rows($resql);

		print '<div class="div-table-responsive-no-min">';
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("Véhicule en panne").' <a href="./creation_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&action=panne"><span class="badge">'.$num.'</span></a></th></tr>';

		if ($num) {
			$i = 0;
			$max = 3;
			while ($i < $num && $i < $max) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven">';
				print '<td class="nowrap" width="20%">';

				print '<table class="nobordernopadding"><tr class="nocellnopadd">';
				print '<td width="96" class="nobordernopadding nowrap">';
				print img_picto("", "maintenance", 'class="paddingright pictofixedwidth"')."<a href='./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=&leftmenu=listevehicule&id_vehicule=".$obj->rowid."'>".$obj->reference_interne."</a>";
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
	//Carburant vehicule
print '</div><div class="fichetwothirdright">';
print getNumberCarburantPieChart();
	print '<br>';


/*
 * document vehicule
 */


print '<div class="div-table-responsive-no-min">';

print getNumberDocumentPieChart();
print '<br>';

print "</div><br>";

$sql_document = "SELECT dv.*, td.rowid as tdrowid, td.nom, vh.rowid as vhrowid, vh.nom as vhnom, vh.reference_interne FROM ".MAIN_DB_PREFIX."document_vehicule as dv";
	$sql_document .= " LEFT JOIN ".MAIN_DB_PREFIX."type_document as td on dv.fk_type_document = td.rowid";
	$sql_document .= " LEFT JOIN ".MAIN_DB_PREFIX."vehicule as vh on dv.fk_vehicule = vh.rowid";

if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write")){
	$sql_document .= " WHERE 1=1";
}else{	
	$sql_document .= "  WHERE dv.fk_user_creation=".$user->id;
}	

	$sql_document .= " AND date_expiration <'".$aujourdhui."'";
	$sql_document .= " ORDER BY dv.date_creation DESC";
	$res_document = $db->query($sql_document);
	print $db->error();
	if ($res_document) {
		print '<div class="div-table-responsive-no-min">';
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">Documents expirés</th>';
		print '<th>Date Expiration</th></tr>';
		$langs->load("orders");
		$num = $db->num_rows($res_document);
		if ($num) {
			$i = 0;
			while ($i < $num) {
				$obj = $db->fetch_object($res_document);

				print '<tr class="oddeven">';
				print '<td class="nowrap">';
				print "<a title ='".$obj->vhnom." (".$obj->reference_interne.")' href='./onglets/document_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$obj->vhrowid."'>".$obj->nom."</a>";
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
				print $obj->date_expiration;
				print '</td></tr>';
				$i++;
			}
		} else {
			print '<tr class="oddeven"><td colspan="3">'.$langs->trans("Aucun vehicule").'</td></tr>';
		}
		print "</table></div><br>";
	}


print '</div></div>';


//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}