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

llxHeader('', "Maintenances");

print load_fiche_titre("Espace Maintenance", '', 'maintenance');

$aujourdhui = date('Y-m-d');

//total maintenance & reparation
print '<div class="fichecenter"><div class="fichethirdleft">';
if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
	$sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule";
else $sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE fk_user_creation=".$user->id;
	$res_maintenance = $db->query($sql_maintenance);
	if($res_maintenance){
		$num_maintenance = $db->num_rows($res_maintenance);
	}
	//$dataseries[] = array("Nombre total de vehicule", $num_maintenance);

	//total maintenance
	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation = 1";
	else $sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation = 1 AND fk_user_creation=".$user->id;

	$res_maintenance = $db->query($sql_maintenance);
	if($res_maintenance){
		$num_maintenance_panne = $db->num_rows($res_maintenance);
	}
	$dataseries[] = array("Mainténances effectuées", $num_maintenance_panne);
	

	$nb_maintenance_retard = 0;
	$id_retard = array(0);
	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$sql_maintenance = "SELECT rowid, fk_vehicule FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation = 1 AND date_fin <'".$aujourdhui."'";
	else $sql_maintenance = "SELECT rowid, fk_vehicule FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation = 1 AND date_fin <'".$aujourdhui."' AND fk_user_creation=".$user->id;

	$res_maintenance = $db->query($sql_maintenance);
	$num = $db->num_rows($res_maintenance);
		if ($num) {
			while ($obj = $db->fetch_object($res_maintenance)) {
				$sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation = 1 AND date_fin >='".$aujourdhui."' AND fk_vehicule =".$obj->fk_vehicule;
				$res_maintenance = $db->query($sql_maintenance);
				if($db->num_rows($res_maintenance) <= 0){
					$nb_maintenance_retard ++;
					$id_retard[] = $obj->rowid;
				}
			}
		}
		$dataseries[] = array("Maintenances en retard", $nb_maintenance_retard);
	//total reparation
	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation != 1";
	else $sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation != 1 AND fk_user_creation=".$user->id;

	$res_maintenance = $db->query($sql_maintenance);
	if($res_maintenance){
		$num_maintenance_panne = $db->num_rows($res_maintenance);
	}
	$dataseries[] = array("Réparations effectuées", $num_maintenance_panne);


	$salarie_societe_graph = '<div class="div-table-responsive-no-min">';
	$salarie_societe_graph .= '<table class="noborder nohover centpercent">'."\n";
	$salarie_societe_graph .= '<tr class="liste_titre"><th colspan="2">'.$langs->trans("Statistique - vehicules").'</th></tr>';
	$dolgraph = new DolGraph();
	$dolgraph->SetData($dataseries);
	$dolgraph->setShowLegend(2);
	$dolgraph->setShowPercent(1);
	$dolgraph->SetType(array('pie'));
	$dolgraph->setHeight('200');

	// --- IMPORTANT : couleurs dans le même ordre que $dataseries
	$colors = array(
		'#106e05ff',//bon état
		'#920000ff', //retard
		'#c5cc08ff'//reparation
	);

$dolgraph->SetDataColor($colors);
	$dolgraph->draw('idgraphsalariesociete');
	$salarie_societe_graph .= '<tr><td>'.$dolgraph->show();
	$salarie_societe_graph .= '</td></tr>';
	$salarie_societe_graph .= '<tr class="liste_total"><td>Nombre total de véhicule</td><td class="right">';
	$salarie_societe_graph .= $num_maintenance;
	$salarie_societe_graph .= '</td></tr>';
	$salarie_societe_graph .= '</table>';
	$salarie_societe_graph .= '</div>';

	print $salarie_societe_graph;
	print '<br>';
/*
 * Draft vehicule
 * 
 * 
 */
$array_id = array(0);
$array_km = array(0);
	$sql = "SELECT rowid, date_maintenance, prochain_kilometrage FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE fk_type_maintenance = 1 AND maintenance_reparation = 1 ORDER BY date_maintenance DESC";
	$res = $db->query($sql);
	if($res){
		while($obj_v = $db->fetch_object($res)){
			$nb_kilometre = 0;
			$nb_total_presume = 0;
			$nb_total_presume = $obj_v->prochain_kilometrage;
			$sql_vidage = "SELECT rowid, kilometre FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE valider = 1 AND date_demande >=".$obj_v->date_maintenance;
			$result = $db->query($sql_vidage);
			if($result){
				while($obj_verif = $db->fetch_object($result)){
					$nb_kilometre += $obj_verif->kilometre;
				}
			}

			if($nb_kilometre > 0 && $nb_total_presume > 0 && ($nb_kilometre + 50 ) >= $nb_total_presume){
				$array_id[] = $obj_v->rowid;
				$array_km[] = $nb_total_presume - $nb_kilometre;
			}
		}
	}

	
	$sql_maintenance = "SELECT mv.rowid, mv.fk_vehicule, mv.date_fin, vh.reference_interne, vh.nom FROM ".MAIN_DB_PREFIX."maintenance_vehicule as mv";
	$sql_maintenance .= " LEFT JOIN ".MAIN_DB_PREFIX."vehicule as vh on mv.fk_vehicule= vh.rowid";
	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write")){
		$sql_maintenance .= " WHERE mv.rowid in (".implode(',', $array_id).")";
	}else{	
		$sql_maintenance .= "  WHERE mv.rowid in (".implode(',', $array_id).") AND mv.fk_user_creation=".$user->id;
	}	
	$sql_maintenance .= " ORDER BY mv.date_creation DESC";
	$res_maintenance = $db->query($sql_maintenance);
	print $db->error();
	if ($res_maintenance) {
		print '<div class="div-table-responsive-no-min">';
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">Vidanges proches</th>';
		print '<th>Kilometrage restant'.info_admin("moins(-) implique le rétard", 1).'</th></tr>';
		$langs->load("orders");
		$num = $db->num_rows($res_maintenance);
		if ($num) {
			$i = 0;
			while ($i < $num) {
				$obj = $db->fetch_object($res_maintenance);

				print '<tr class="oddeven">';
				print '<td class="nowrap">';
				print "<a title ='".$obj->nom." (".$obj->reference_interne.")' href='./onglets/vidange_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$obj->fk_vehicule."'>".$obj->nom."</a>";
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
				for ($i=0; $i < count($array_id); $i++) { 
					if($array_id[$i] == $obj->rowid)
						print $array_km[$i].' Km';
				}
				print '</td></tr>';
				$i++;
			}
		} else {
			print '<tr class="oddeven"><td colspan="3">'.$langs->trans("Aucun vehicule").'</td></tr>';
		}
		print "</table></div><br>";
	}


	$sql_maintenance = "SELECT mv.rowid, mv.fk_vehicule, mv.date_fin, vh.reference_interne, vh.nom FROM ".MAIN_DB_PREFIX."maintenance_vehicule as mv";
	$sql_maintenance .= " LEFT JOIN ".MAIN_DB_PREFIX."vehicule as vh on mv.fk_vehicule= vh.rowid";
	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write")){
		$sql_maintenance .= " WHERE mv.rowid in (".implode(',', $id_retard).")";
	}else{	
		$sql_maintenance .= "  WHERE mv.rowid in (".implode(',', $id_retard).") AND mv.fk_user_creation=".$user->id;
	}	
	$sql_maintenance .= " ORDER BY mv.date_creation DESC";
	$res_maintenance = $db->query($sql_maintenance);
	print $db->error();
	if ($res_maintenance) {
		print '<div class="div-table-responsive-no-min">';
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">Maintenances en retards</th>';
		print '<th>Date Fin</th></tr>';
		$langs->load("orders");
		$num = $db->num_rows($res_maintenance);
		if ($num) {
			$i = 0;
			while ($i < $num) {
				$obj = $db->fetch_object($res_maintenance);

				print '<tr class="oddeven">';
				print '<td class="nowrap">';
				print "<a title ='".$obj->nom." (".$obj->reference_interne.")' href='./onglets/maintenance_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$obj->fk_vehicule."'>".$obj->nom."</a>";
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
				print $obj->date_fin;
				print '</td></tr>';
				$i++;
			}
		} else {
			print '<tr class="oddeven"><td colspan="3">'.$langs->trans("Aucun vehicule").'</td></tr>';
		}
		print "</table></div><br>";
	}


print '</div><div class="fichetwothirdright">';


/*
 * Latest modified maintenance
 */

$sql_last_modif = "SELECT mv.rowid, mv.fk_vehicule, mv.date_fin, mv.fk_user_creation, mv.date_creation, vh.reference_interne, vh.nom FROM ".MAIN_DB_PREFIX."maintenance_vehicule as mv";
	$sql_last_modif .= " LEFT JOIN ".MAIN_DB_PREFIX."vehicule as vh on mv.fk_vehicule= vh.rowid";
	$sql_last_modif .= " WHERE maintenance_reparation = 1";
	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write")){
		//$sql_last_modif .= " WHERE mv.date_fin >= '".$aujourdhui."'";
	}else{	
		$sql_last_modif .= " AND mv.fk_user_creation=".$user->id;
	}

	$sql_last_modif .= " ORDER BY mv.date_creation DESC";

$resql = $db->query($sql_last_modif);
if ($resql) {
	$num = $db->num_rows($resql);

	startSimpleTable($langs->trans("Les 3 dernières maintenances modifiées"), "./gestionflotte/liste_maintenance_complete.php?mainmenu=gestionflotte&leftmenu=maintenance", "", 2, -1, 'order');

	if ($num) {
		$i = 0;
		$max = 3;
		while ($i < $num && $i < $max) {
			$obj = $db->fetch_object($resql);

			print '<tr class="oddeven">';
			print '<td width="20%" class="nowrap">';

			print '<table class="nobordernopadding"><tr class="nocellnopadd">';
			print '<td width="96" class="nobordernopadding nowrap">';
			print "<a title ='".$obj->nom." (".$obj->reference_interne.")' href='./onglets/document_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$obj->fk_vehicule."'>".$obj->nom."</a>";
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
			print $obj->date_creation;
			print '</td>';

			//----------------------------
            $status = '<span style="background-color: #106e05ff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 

            if ($obj->date_fin < $aujourdhui) {
                $status = '<span style="background-color: #920000ff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 
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
 * Latest modified maintenance
 */

$sql_last_modif = "SELECT mv.rowid, mv.fk_vehicule, mv.date_fin, mv.fk_user_creation, mv.date_creation, vh.reference_interne, vh.nom FROM ".MAIN_DB_PREFIX."maintenance_vehicule as mv";
	$sql_last_modif .= " LEFT JOIN ".MAIN_DB_PREFIX."vehicule as vh on mv.fk_vehicule= vh.rowid";
	$sql_last_modif .= " WHERE maintenance_reparation != 1";
	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write")){
		//$sql_last_modif .= " WHERE mv.date_fin >= '".$aujourdhui."'";
	}else{	
		$sql_last_modif .= " AND mv.fk_user_creation=".$user->id;
	}

	$sql_last_modif .= " ORDER BY mv.date_creation DESC";

$resql = $db->query($sql_last_modif);
if ($resql) {
	$num = $db->num_rows($resql);

	startSimpleTable($langs->trans("Les 3 dernières réparations modifiées"), "./gestionflotte/liste_reparation_complete.php?mainmenu=gestionflotte&leftmenu=maintenance", "", 2, -1, 'order');

	if ($num) {
		$i = 0;
		$max = 3;
		while ($i < $num && $i < $max) {
			$obj = $db->fetch_object($resql);

			print '<tr class="oddeven">';
			print '<td width="20%" class="nowrap">';

			print '<table class="nobordernopadding"><tr class="nocellnopadd">';
			print '<td width="96" class="nobordernopadding nowrap">';
			print "<a title ='".$obj->nom." (".$obj->reference_interne.")' href='./onglets/document_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$obj->fk_vehicule."'>".$obj->nom."</a>";
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
			print $obj->date_creation;
			print '</td>';

			//----------------------------
            $status = '<span style="background-color: #c5cc08ff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 

			print '<td class="right">'.$status.'</td>';
			print '</tr>';
			$i++;
		}
	}
	finishSimpleTable(true);
} else {
	//dol_print_error($db);
}
print '</div></div>';

//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}