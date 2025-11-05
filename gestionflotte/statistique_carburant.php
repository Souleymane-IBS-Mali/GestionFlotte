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

llxHeader('', "Gestion | Flottes");

print load_fiche_titre("Statistiques des demandes", '', 'carburant');

$aujourdhui = date('Y-m-d');

if($user->id == 1 || $user->hasRight("gestionflotte", "gestionvehicule", "write")){
//Statistiques de l'année en cours
//-------------------------------------------------------------------------------------------------------------------------------------------
    // Tableau des mois
	$annee_rechercher = date('Y');
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

			$param = 'date_debut='.$debut.'&date_fin='.$fin;
			print '<th style="text-align:right;">';
			print '<a href="./carburant.php?mainmenu=gestionflotte&leftmenu=carburant&'.$param.'">';
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
				$param = '&date_debut='.$weeks[$i]["debut"].'&date_fin='.$weeks[$i]["fin"];
				//$param .= '&search_date_endday='.cal_days_in_month(CAL_GREGORIAN, $indice, $annee).'&search_date_endmonth='.$indice.'&search_date_endyear='.$annee;
				$sql_vehicule = 'SELECT cout FROM '.MAIN_DB_PREFIX.'carburant_vehicule WHERE date_demande >= "'.$weeks[$i]["debut"].'" AND date_demande <= "'.$weeks[$i]["fin"].'" ORDER BY date_demande DESC';
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
				print '<a title="'.$num.' demande(s)" href="./carburant.php?mainmenu=gestionflotte&leftmenu=carburant&'.$param.'">';
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
			print '<td title="'.$total_horzontal.' demande(s)" align="center">';
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
			print '<td title="'.$value.' demande(s)" align="center">'.$coutVertical[$indice].'</td>';
			$indice ++;
		}
		print '<td title="'.$total_totaux.' demande(s)" align="center">'.$cout_totaux.'</td>';
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