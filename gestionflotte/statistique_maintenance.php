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

print load_fiche_titre("Statistiques", '', 'maintenance');
print "<hr>";

$aujourdhui = date('Y-m-d');

if($user->id == 1 || $user->hasRight("gestionflotte", "gestionvehicule", "write")){
//Statistiques de l'année en cours
//-------------------------------------------------------------------------------------------------------------------------------------------
    // Tableau des mois
	// Tableau des mois
	$annee_rechercher = GETPOST('annee', 'int')?:date('Y');

	//Filtre par année
					print "<div style='float: right; margin-right:'30px'>";
					print '<form name="add" method="POST" action="'.$_SERVER['PHP_SELF'].'?mainmenu=gestionflotte&leftmenu=maintenance&id_vehicule='.$id_vehicule.'">';
					print '<input type="hidden" name="token" value="'.newToken().'">';
					print '<input type="hidden" name="action" value="save_edit">';
					$info = "Les années affichées sont les années auquelles ce salarié à au moins un bulletin";
					print info_admin($langs->trans($info), 1)."<select style='font-size: 24px; font-weight: bold;' name='annee_rechercher' id='annee_rechercher'><option value='0'></option>";
					//affichage de la zone de recherche année
					//les valeurs son uniquement les années au cours desquelles le salarié a au moins un bulletin
					$sql_verif = "SELECT DISTINCT YEAR(date_demande) as annee FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE fk_vehicule = ".$id_vehicule;
						$res_verif = $db->query($sql_verif);
						$annee_tab = array();
							if($res_verif){
								$i = 0;
								$nb = $db->num_rows($res_verif);
								while($obj_verif = $db->fetch_object($res_verif)){
									$annee_tab[] = $obj_verif->annee;
									if($obj_verif->annee == $annee_rechercher)
										print "<option value='".$obj_verif->annee."' selected >".$obj_verif->annee."</option>";
									else 
										print "<option value='".$obj_verif->annee."'>".$obj_verif->annee."</option>";

									$i ++;
								}
							}
								if($nb == 0){
									print "<option value='".date("Y")."' selected >".date("Y")."</option>";
								}elseif(!in_array(date("Y"), $annee_tab))
									if($annee_rechercher == date("Y"))
										print "<option value='".date("Y")."' selected>".date("Y")."</option>";
									else print "<option value='".date("Y")."' >".date("Y")."</option>";
							
							print '<input class="button"  type="submit" value="Afficher">';
							print'</form>';
					print "</div>";

    $months = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    ];

		// En-têtes : les mois
		print "<br><br><div>";
		print "<table class='tagtable liste' style='width:100%;'>";
		print '<tr class="liste_titre">';
		print '<th colspan ="14" align = "center" >Statistiques de <mark>'.$annee_rechercher.'</th>';
		print '</tr>';
		print '<tr class="liste_titre">';
		print '<th>Périodes</th>';
		$indice = 1;
		foreach ($months as $month) {
			$debut = $annee_rechercher.'-'.$indice.'-1';
			$debut = new DateTime($debut);
			$debut = $debut->format('Y-m-d');

			$fin = $annee_rechercher.'-'.$indice.'-'.cal_days_in_month(CAL_GREGORIAN, $indice, $annee_rechercher);
			$fin = new DateTime($fin);
			$fin = $fin->format('Y-m-d');

			$param = 'date_maintenance1='.$debut.'&date_maintenance2='.$fin;
			print '<th style="text-align:right;">';
			print '<a href="./liste_maintenance_complete.php?mainmenu=gestionflotte&leftmenu=maintenance&'.$param.'">';
			print $month;
			print '</a>';
			print '</th>';
			$indice ++;
		}
		print '<th align="center">Total</th>';
    	print '</tr>';
		
		// Exemple d’utilisation :
		print '<tr>';
		$numVertical = array();
		$coutVertical = array();
		$total_totaux = 0;
		$cout_totaux = 0;
		for ($i = 0; $i < 5; $i ++) {		
			$indice = 0;
			$total_horzontal = 0;
			$cout_horizontal = 0;
			
			foreach ($months as $month) {
				$year = $annee_rechercher;
				$weeks = getWeeksOfMonth($year, $indice + 1);
				if($indice == 0)
					print '<th align="left" >Semaine ' . ($i + 1).'</th>';
				$param = '&date_maintenance1='.$weeks[$i]["debut"].'&date_maintenance2='.$weeks[$i]["fin"];
				//$param .= '&search_date_endday='.cal_days_in_month(CAL_GREGORIAN, $indice, $annee).'&search_date_endmonth='.$indice.'&search_date_endyear='.$annee;
				$sql_vehicule = 'SELECT cout FROM '.MAIN_DB_PREFIX.'maintenance_vehicule WHERE maintenance_reparation = 1 AND date_maintenance >= "'.$weeks[$i]["debut"].'" AND date_maintenance <= "'.$weeks[$i]["fin"].'" ORDER BY date_maintenance DESC';
				$result_vehicule = $db->query($sql_vehicule);
				//print $sql_vehicule;
				$num = 0;
				$cout = 0;;
				if($result_vehicule){
					$num = $db->num_rows($result_vehicule);
					while ($obj_carb = $db->fetch_object($result_vehicule)) {
						$cout += $obj_carb->cout;
					}
				}
				$total_horzontal += $num;
				$cout_horizontal += $cout;
				print '<td align="center">';
				print '<a title="'.$num.' maintenance(s)" href="./liste_maintenance_complete.php?mainmenu=gestionflotte&leftmenu=maintenance&'.$param.'">';
				print $cout;
				print '</a>';
				print '</td>';

				$numVertical[$indice] += $num;
				$coutVertical[$indice] += $cout;
				$indice ++;
			}

			$total_totaux += $total_horzontal;
			$cout_totaux += $cout_horizontal;
			//total
			print '<td title="'.$total_horzontal.' maintenance(s)" align="center">';
				//print '<a href="./detail_carburant.php?id_vehicule='.$id_vehicule.'&'.$param.'">';
				print $cout_horizontal;
				//print '</a>';
				print '</td>';
			print '</tr>';

		}

		print '<tr>';
		print '<th align="left">Total</th align="left">';
		$indice = 0;
		foreach ($numVertical as $key => $value) {
			print '<td title="'.$value.' maintenance(s)" align="center">'.$coutVertical[$indice].'</td>';
			$indice ++;
		}
		print '<td title="cc'.$total_totaux.' maintenance(s)" align="center">'.$cout_totaux.'</td>';
		print '</tr>';
		print "</table></div>";
	}
//-------------------------------------------------------------------------------------------------------------------------------------------

//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}