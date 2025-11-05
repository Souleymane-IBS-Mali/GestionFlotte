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
$aujourdhui = date('Y-m-d');




llxHeader("", "Maintenances");
//Titre
print load_fiche_titre($langs->trans("Liste des maintenances"), '', '');

// Recuperation des information après le clique sur l'onglet Salaire au niveau du module user
			$action = GETPOST("action", "alpha");


			$salaire_base = 0;
			$message = "";
			$annee = date("Y");
			$mois = (int)date("m");

			$cout = GETPOST("cout", "alpha");
			$type_maintenance = GETPOST("type_maintenance", "int");
			$reference_interne = GETPOST("reference_interne", "alpha");
			$id_societe = GETPOST("id_societe", "int");
			$date_fin_main = GETPOST("date_fin_main", "int");			

			$date_maintenance1 = GETPOST("date_maintenance1");
			$date_maintenance2 = GETPOST("date_maintenance2");

			$date_fin1 = GETPOST("date_fin1");
			$date_fin2 = GETPOST("date_fin2");

			$statut = GETPOST("statut", "int");

			if($date_maintenance){
				$date = new DateTime($date_maintenance);
				$date_maintenance = $date->format('Y-m-d');
			}

			if($date_fin){
				$date = new DateTime($date_fin);
				$date_fin = $date->format('Y-m-d');
			}
	//print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Ajouter un nouveau contrat", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=liste_vehicule&id='.$fk_user.'&fk_salarie='.$fk_salarie.'&id_convention='.$id_convention.'&id_societe='.$id_societe.'&action=ajouter' , '', 1), '', 0, 0, 0, 1);
	//Partie affichage du Contrat ------------------------------------------------------------------------------------------------------------------------------------------
			$acts[0] = "activate";
			$acts[1] = "disable";
			$actl[0] = img_picto($langs->trans("Disabled"), 'switch_off', 'class="size15x"');
			$actl[1] = img_picto($langs->trans("Activated"), 'switch_on', 'class="size15x"');
			$array_id_soc = "(0";
			$sql = "SELECT fk_soc FROM ".MAIN_DB_PREFIX."societe_commerciaux";
			$sql .= " WHERE fk_user=".$user->id;
			$result = $db->query($sql);
			if($result){
				$i = 0;
				$num = $db->num_rows($result);
				while ($i < $num){
					$array_id_soc .= ", ".$db->fetch_object($result)->fk_soc;
					$i ++;
				}
			}
			$array_id_soc .= ")";
		//les maintenances expirés
		print "<hr><div>";
		print "<table class='tagtable liste'>";

		print '<form name="ajouter" method="POST" action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<input type="hidden" name="action" value="recherche">';
		//type maintenance
		print "<tr class='liste_titre'><td >Type maintenance<br><select name='type_maintenance'>";
		print "<option value=0></option>";

		$sql = "SELECT rowid, libelle FROM ".MAIN_DB_PREFIX."type_maintenance";
		$result = $db->query($sql);

		if($result){
			while ($type_maintenance = $db->fetch_object($result)){
				if($type_maintenance == $type_maintenance->rowid)
					print "<option value=".$type_maintenance->rowid." selected>".$type_maintenance->libelle."</option>";
				else
					print "<option value=".$type_maintenance->rowid.">".$type_maintenance->libelle."</option>";
			}

		}

	print "</select></td>";
	print "<td >Cout<br><input type='text' name='cout' value='".$cout."' size='10'></td>";
		print "</td><td >Ref. véhicule<br><input type='text' name='reference_interne' value='".$reference_interne."' size='10'>";

		//Société

	//Date fin
	$sel3 ="";
	$sel6 ="";
	$sel9 ="";

	print "</td><td>Date maintenance<br><input type='date' name='date_maintenance1' value='".$date_maintenance1."' size='10'>
	<br><input type='date' name='date_maintenance2' value='".$date_maintenance2."' size='10'></td>";
		print "</td><td>Date fin<br><input type='date' name='date_fin1' value='".$date_fin1."' size='10'>
		<br><input type='date' name='date_fin2' value='".$date_fin2."' size='10'></td>";
		
		print "<td>Mois restant(s)<br><select name='date_fin_main'>";
		if($date_fin_main == 3)
			$sel3 = "selected";
		elseif($date_fin_main == 6)
			$sel6 = "selected";
		elseif($date_fin_main == 9)
			$sel9 = "selected";
		print "<option value=0 ></option>";
		print "<option value=3 ".$sel3." >3 mois</option>";
		print "<option value=6 ".$sel6." >6 mois</option>";
		print "<option value=9 ".$sel9." >9 mois</option>";
		print "</select>";

		//statut
		print "</td><td ><select name='statut'>";
		$actif = "";
		$expire = "";
		if($statut == 1)
			$actif = "selected";
		else if($statut == 2)
			$expire = "selected";

		print "<option value='' ></option>";
		print "<option value=1 ".$actif." >Actif</option>";
		print "<option value=2 ".$expire." >Expiré</option>";
		print "</select><br><input style='padding: 2px;' type='submit' class='button' value='Rechercher' >";
		print "</form><br>";
		print '<a class="button" style="padding: 2px;"" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique" >Annuler</a>';
		print "</td></td></tr>";

		$annee = (int)date("Y");
		$mois = (int)date("m");
		$jour = (int)date("d");

		//les maintenances qui finissent dans 6 mois ue.egp=".$id_societe."
		$sql_maintenance = "SELECT mv.*, tm.rowid as tmrowid, tm.libelle, vh.rowid as vhrowid, vh.nom as vhnom, vh.reference_interne FROM ".MAIN_DB_PREFIX."maintenance_vehicule as mv";
		$sql_maintenance .= " LEFT JOIN ".MAIN_DB_PREFIX."type_maintenance as tm on mv.fk_type_maintenance = tm.rowid";
		$sql_maintenance .= " LEFT JOIN ".MAIN_DB_PREFIX."vehicule as vh on mv.fk_vehicule = vh.rowid";

		if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver")){
			$sql_maintenance .= " WHERE mv.maintenance_reparation = 1";
		}else{	
			$sql_maintenance .= "  WHERE mv.maintenance_reparation = 1 AND mv.fk_user_creation=".$user->id;
		}


		if($date_maintenance1){
			$sql_maintenance .= ' AND date_maintenance >= "'.$date_maintenance1.'"';
		}

		if($date_maintenance2){
			$sql_maintenance .= ' AND date_maintenance <= "'.$date_maintenance2.'"';
		}

		if(!empty($statut))
			if($statut == 1)
				$sql_maintenance .= " AND mv.date_fin >= '".$aujourdhui."'";
			else $sql_maintenance .= " AND mv.date_fin < '".$aujourdhui."'";

		
			if(!empty($cout))
				$sql_maintenance .= ' AND mv.cout ='.$cout;

			if(!empty($type_maintenance))
				$sql_maintenance .= ' AND td.nom LIKE "%'.$type_maintenance.'%"';

			if(!empty($reference_interne))
				$sql_maintenance .= ' AND vh.reference_interne LIKE "%'.$reference_interne.'%"';
			
			if(!empty($date_maintenance))
				$sql_maintenance .= ' AND mv.date_maintenance = "'.$date_maintenance.'"';

			if($date_fin1){
				$sql_maintenance .= ' AND date_fin >= "'.$date_fin1.'"';
			}

			if($date_fin2){
				$sql_maintenance .= ' AND date_fin <= "'.$date_fin2.'"';
			}

			if(!empty($date_fin_main)){
				$annee = (int)date("Y");
				$mois = (int)date("m");
				$sql_maintenance .= " AND (( YEAR(mv.date_fin)>".$annee." AND (MONTH(mv.date_fin) + 12 - ".$mois.") <= ".$date_fin_main." AND ( MONTH(mv.date_fin) +12 - ".$mois.") > 0)";
				$sql_maintenance .= " OR (YEAR(mv.date_fin) = ".$annee."  AND MONTH(mv.date_fin) >= ".$mois." AND  (MONTH(mv.date_fin) - ".$mois." <= ".$date_fin_main.")  ))";
			}

		if(!empty($date_fin_main))
			$sql_maintenance .= " ORDER BY mv.date_fin ASC";
		else
			$sql_maintenance .= " ORDER BY mv.date_fin";
		
		//print $sql_maintenance;
			$res_maintenance = $db->query($sql_maintenance);

			$actl[0] = img_picto("actif", 'switch_off', 'class="size15x"');
			$actl[1] = img_picto("expiré", 'switch_on', 'class="size15x"');
			if($res_maintenance){
				$num = $db->num_rows($res_maintenance);
				$i = 0;
				while($obj_mixte = $db->fetch_object($res_maintenance)){

					//calcul du nombre de mois restant
					$d_f = "";
					if(!empty($obj_mixte->date_fin)){
						$tab = explode("-", $obj_mixte->date_fin);

						$d_f = $tab[2]."-".$tab[1]."-".$tab[0];

						if($tab[0] == date("Y")){
							if($tab[1] > ((int)date("m")+1))
								$d_f = ($tab[1] - (int)date("m"))." mois";
							else if($tab[1]== date("m")){
								 if($tab[2] > date("d"))
								 	$d_f = ($tab[2] - (int)date("d"))." jour(s)";
								 else if($tab[2] == date("d"))
								 	$d_f = "Aujourd'hui";
								 else $d_f = "<span style='color: red'>Expiré</span>";
							}else $d_f = "<span style='color: red'>Expiré</span>";
						}else if($tab[0] > date("Y")){
							$d_f = (($tab[0] - (int)date("Y"))*12 + $tab[1] - (int)date("m"))." mois";
						}else $d_f = "<span style='color: red'>Expiré</span>";
					}

					print "<tr class='fieldrequired'><td><a href='./onglets/maintenance_vehicule.php?mainmenu=gestionflotte&leftmenu=liste_vehicule&id_vehicule=".$obj_mixte->vhrowid."'>".($obj_mixte->libelle?:"N/A")."</a></td>";

						print "<td>".($obj_mixte->cout?:"N/A")."</td>";
						print "<td >".($obj_mixte->reference_interne ?:"N/A")."</td>";
						print "<td >".($obj_mixte->date_maintenance?:"&#8734;")."</td>";
						print "<td >".($obj_mixte->date_fin?:"&#8734;")."</td>";
						print "<td >".($d_f?:"&#8734;")."</td>";

						$etat = $actl[1];
						if($obj_mixte->date_fin && $obj_mixte->date_fin < $aujourdhui)
							$etat = $actl[0];
						print "<td >".$etat."</a></td>";
						//print "<td></td>";

						print '</tr>';

				}
			}
				if($num == 0)
					print "<tr><td align='center' colspan=7>Aucun maintenance</td></tr>";

			print "</table></div>";
			$db->free();
