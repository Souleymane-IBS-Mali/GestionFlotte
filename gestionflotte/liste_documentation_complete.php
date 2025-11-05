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




llxHeader("", "Documentations");
//Titre
print load_fiche_titre($langs->trans("Liste des documents"), '', '');

// Recuperation des information après le clique sur l'onglet Salaire au niveau du module user
			$action = GETPOST("action", "alpha");


			$salaire_base = 0;
			$message = "";
			$annee = date("Y");
			$mois = (int)date("m");

			$tarif = GETPOST("tarif", "alpha");
			$type_document = GETPOST("type_document", "int");
			$reference_interne = GETPOST("reference_interne", "alpha");
			$id_societe = GETPOST("id_societe", "int");
			$date_fin = GETPOST("date_fin", "int");
			$date_debut = GETPOST("date_debut");
			$date_expiration = GETPOST("date_expiration");
			$statut = GETPOST("statut", "int");

			if($date_debut){
				$date = new DateTime($date_debut);
				$date_debut = $date->format('Y-m-d');
			}

			if($date_expiration){
				$date = new DateTime($date_expiration);
				$date_expiration = $date->format('Y-m-d');
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
		//les documents expirés
		print "<hr><div>";
		print "<table class='tagtable liste'>";

		print '<form name="ajouter" method="POST" action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=gestiondocument">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<input type="hidden" name="action" value="recherche">';
		//type document
		print "<tr class='liste_titre'><td >Type doc.<br><select name='type_document'>";
		print "<option value=0></option>";

		$sql = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."type_document";
		$result = $db->query($sql);

		if($result){
			$i = 0;
			$num = $db->num_rows($result);
			while ($i < $num){
				$typ_contrat = $db->fetch_object($result);
				if($type_document == $typ_contrat->rowid)
					print "<option value=".$typ_contrat->rowid." selected>".$typ_contrat->nom."</option>";
				else
					print "<option value=".$typ_contrat->rowid.">".$typ_contrat->nom."</option>";
				$i ++;
			}

		}

	print "</select></td>";
	print "<td >Tarif<br><input type='text' name='tarif' value='".$tarif."' size='10'></td>";
		print "</td><td >Ref. véhicule<br><input type='text' name='reference_interne' value='".$reference_interne."' size='10'>";

		//Société

	//Date fin
	$sel3 ="";
	$sel6 ="";
	$sel9 ="";

	print "</td><td>Date debut<br><input type='date' name='date_debut' value='".$date_debut."' size='10'></td>";
		print "</td><td>Date fin<br><input type='date' name='date_expiration' value='".$date_expiration."' size='10'></td>";
		
		print "<td>Mois restant(s)<br><select name='date_fin'>";
		if($date_fin == 3)
			$sel3 = "selected";
		elseif($date_fin == 6)
			$sel6 = "selected";
		elseif($date_fin == 9)
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
		print '<a class="button" style="padding: 2px;"" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=gestiondocument" >Annuler</a>';
		print "</td></td></tr>";

		$annee = (int)date("Y");
		$mois = (int)date("m");
		$jour = (int)date("d");

		//les documents qui finissent dans 6 mois ue.egp=".$id_societe."
		$sql_document = "SELECT dv.*, td.rowid as tdrowid, td.nom, vh.rowid as vhrowid, vh.nom as vhnom, vh.reference_interne FROM ".MAIN_DB_PREFIX."document_vehicule as dv";
	$sql_document .= " LEFT JOIN ".MAIN_DB_PREFIX."type_document as td on dv.fk_type_document = td.rowid";
	$sql_document .= " LEFT JOIN ".MAIN_DB_PREFIX."vehicule as vh on dv.fk_vehicule = vh.rowid";

		if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver")){
			$sql_document .= " WHERE 1=1";
		}else{	
			$sql_document .= "  WHERE dv.fk_user_creation=".$user->id;
		}


		if(!empty($statut))
			if($statut == 1)
				$sql_document .= " AND dv.date_expiration >= '".$aujourdhui."'";
			else $sql_document .= " AND dv.date_expiration < '".$aujourdhui."'";

		
			if(!empty($tarif))
				$sql_document .= ' AND dv.tarif ='.$tarif;

			if(!empty($type_document))
				$sql_document .= ' AND td.nom LIKE "%'.$type_document.'%"';

			if(!empty($reference_interne))
				$sql_document .= ' AND vh.reference_interne LIKE "%'.$reference_interne.'%"';
			
			if(!empty($date_debut))
				$sql_document .= ' AND dv.date_debut = "'.$date_debut.'"';

			if(!empty($date_expiration))
				$sql_document .= ' AND dv.date_expiration = "'.$date_expiration.'"';

			if(!empty($date_fin)){
				$annee = (int)date("Y");
				$mois = (int)date("m");
				$sql_document .= " AND (( YEAR(dv.date_expiration)>".$annee." AND (MONTH(dv.date_expiration) + 12 - ".$mois.") <= ".$date_fin." AND ( MONTH(dv.date_expiration) +12 - ".$mois.") > 0)";
				$sql_document .= " OR (YEAR(dv.date_expiration) = ".$annee."  AND MONTH(dv.date_expiration) >= ".$mois." AND  (MONTH(dv.date_expiration) - ".$mois." <= ".$date_fin.")  ))";
			}

		if(!empty($date_fin))
			$sql_document .= " ORDER BY dv.date_expiration ASC";
		else
			$sql_document .= " ORDER BY dv.date_expiration";
		
		//print $sql_document;
			$res_document = $db->query($sql_document);

			$actl[0] = img_picto("actif", 'switch_off', 'class="size15x"');
			$actl[1] = img_picto("expiré", 'switch_on', 'class="size15x"');
			if($res_document){
				$num = $db->num_rows($res_document);
				$i = 0;
				while($obj_mixte = $db->fetch_object($res_document)){

					//calcul du nombre de mois restant
					$d_f = "";
					if(!empty($obj_mixte->date_expiration)){
						$tab = explode("-", $obj_mixte->date_expiration);

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

					print "<tr class='fieldrequired'><td><a href='./onglets/document_vehicule.php?mainmenu=gestionflotte&leftmenu=liste_vehicule&id_vehicule=".$obj_mixte->vhrowid."'>".($obj_mixte->nom?:"N/A")."</a></td>";

						print "<td>".($obj_mixte->tarif?:"N/A")."</td>";
						print "<td >".($obj_mixte->reference_interne ?:"N/A")."</td>";
						print "<td >".($obj_mixte->date_debut?:"&#8734;")."</td>";
						print "<td >".($obj_mixte->date_expiration?:"&#8734;")."</td>";
						print "<td >".($d_f?:"&#8734;")."</td>";

						$etat = $actl[1];
						if($obj_mixte->date_expiration < $aujourdhui)
							$etat = $actl[0];
						print "<td >".$etat."</a></td>";
						//print "<td></td>";

						print '</tr>';

				}
			}
				if($num == 0)
					print "<tr><td align='center' colspan=7>Aucun document</td></tr>";

			print "</table></div>";
			$db->free();
