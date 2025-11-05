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

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/gestionflotte/lib/gestionflotte.lib.php';

$id_vehicule = GETPOST("id_vehicule","int");

llxHeader("", "Gestion Vehicule");
//Titre
//print load_fiche_titre($langs->trans("Incidents"), '', '');

if($id_vehicule){

	$head = vehicule_Head($id_vehicule);
	print dol_get_fiche_head($head, 'incident', "", -1, '');

	$obj_soc = prepare_objet_entete($id_vehicule, $db);
    entete_vehicule($obj_soc, 'commande');
	print '<hr>';

		avertissement($db, $id_vehicule);

	//detail
	$vehicule = "SELECT * FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$id_vehicule;
		
	$result_vehicule = $db->query($vehicule);//= $db->query($covSql);

	if($result_vehicule)
		$obj_vehicule = $db->fetch_object($result_vehicule);

	if($action == "ajouterh"){
			print ' <form name="ajout" method="POST" action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehiculee&id='.$fk_user.'&fk_salarie='.$fk_salarie.'&id_convention='.$id_convention.'&id_societe='.$id_societe.'" enctype="multipart/form-data">';
			print '<input type="hidden" name="token" value="'.newToken().'">';
				print '<input type="hidden" name="action" value="ajouter_contrat">';

			print "<table>";
			print "<tr class='fieldrequired'><td style='width: 200px; padding-top: 20px;'>Numéro Contrat</td><td style='width: 200px; padding-top: 20px;'><input type='text' name='numero_contrat' id='numero_contrat' value='".GETPOST("numero_contrat", "alpha")."' autofocus></td></tr>";

			print "<tr class='fieldrequired'><td style='width: 200px; padding-top: 20px;'>Type Contrat</td><td><select
			name='type_contrat' id='type_contrat'>";
			print "<option value='0'></option>";
			$sql_type_contrat = "SELECT rowid, libelle FROM ".MAIN_DB_PREFIX."type_contrat";
			$restype_contrat = $db->query($sql_type_contrat);
			if($restype_contrat){
				$nb = $db->num_rows($restype_contrat);
				$i =0;
				while ($i < $nb) {
					$obj_typ_cont = $db->fetch_object($restype_contrat);
					if(!empty(GETPOST("type_contrat", "int")) && GETPOST("type_contrat", "int") == $obj_typ_cont->rowid)
						print "<option value='".$obj_typ_cont->rowid."' selected>".$obj_typ_cont->libelle."</option>";
					else
						print "<option value='".$obj_typ_cont->rowid."'>".$obj_typ_cont->libelle."</option>";
					$i ++;
				}
			}

			$sql_select = "SELECT dateemployment FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$fk_user;
			$obj = $db->fetch_object($db->query($sql_select));
			print"</td></tr>";
			print "<tr class='fieldrequired'><td style='width: 200px; padding-top: 20px;'>Date D'embauche</td><td style='width: 200px; padding-top: 20px;'>
			<input type='date' value='".($obj->dateemployment?:(GETPOST('date_embauche')))."' name='date_embauche' id='date_embauche'>";
			print "</td></tr>";
			print "<tr class='fieldrequired'><td style='width: 200px; padding-top: 20px;'>Date Signature</td><td style='width: 200px; padding-top: 20px;'>
			<input type='date' value='".(GETPOST('date_signature'))."' name='date_signature' id='date_signature'>";
			print "</td></tr>";
			print "<tr class='fieldrequired'><td style='width: 200px; padding-top: 20px;'>Date Fin</td><td style='width: 200px; padding-top: 20px;'>
			<input type='date' value='".(GETPOST('date_fin'))."' name='date_fin' id='date_fin'>";
			print "</td></tr>";
			//salaire net
			print "<tr class='fieldrequired'><td style='width: 200px; padding-top: 20px;'>Salaire brut</td><td style='width: 200px; padding-top: 20px;'>
			<input type='text' value='".(GETPOST('salaire_brut', 'int'))."' name='salaire_brut' id='salaire_brut'>";
			print "</td></tr>";
			//salaire brut
			print "<tr class='fieldrequired'><td style='width: 200px; padding-top: 20px;'>Salaire net</td><td style='width: 200px; padding-top: 20px;'>
			<input type='text' value='".(GETPOST('salaire_net', 'int'))."' name='salaire_net' id='salaire_net'>";
			print "</td></tr>";

			print "<tr class='fieldrequired'><td style='width: 200px; padding-top: 20px;'>Fichier du Contrat</td><td style='width: 200px; padding-top: 20px;'><input type='file' name='fichier_contrat' id='fichier_contrat' ><1Mo</td></tr>";

			print '<tr>';
			print '<td style=" padding-right: 30px; padding-bottom: 30px"></td><td style="padding-top: 30px; width: 300px;"><input onclick="MonSubmitForm()" class="button" type="submit" value="Enregistrer">';
			print'</form>';
			print '<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'" class="button">Annuler</a></td></tr>';
			print '</table>';
	}else{
			$annee_rechercher = GETPOST('annee', 'int')?:date('Y');
			print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Ajouter un nouveau contrat", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehiculee&id='.$fk_user.'&fk_salarie='.$fk_salarie.'&id_convention='.$id_convention.'&id_societe='.$id_societe.'&action=ajouter' , '', 1), '', 0, 0, 0, 1);

			//Filtre par année
				print "<div style='float: right; margin-right:'30px'>";
				print '<form name="add" method="POST" action="'.$_SERVER['PHP_SELF'].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'">';
				print '<input type="hidden" name="token" value="'.newToken().'">';
				print '<input type="hidden" name="action" value="save_edit">';
				$info = "Les années affichées sont les années auquelles ce salarié à au moins un bulletin";
				print info_admin($langs->trans($info), 1)."<select style='font-size: 24px; font-weight: bold;' name='annee_rechercher' id='annee_rechercher'><option value='0'></option>";
				//affichage de la zone de recherche année
				//les valeurs son uniquement les années au cours desquelles le salarié a au moins un bulletin
				$sql_verif = "SELECT DISTINCT YEAR(date_incident) as annee FROM ".MAIN_DB_PREFIX."incident_vehicule WHERE fk_vehicule = ".$id_vehicule;
					$res_verif = $db->query($sql_verif);
						if($res_verif){
							$i = 0;
							$nb = $db->num_rows($res_verif);
							$annee_tab = array();
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
			
			print "<div>";
			print "<h3 >Historiques des incidents de <mark>".$annee_rechercher."</mark></h3>";
			print "<table class='tagtable liste' style='width:100%;'>";
			print "<tr class='liste_titre'><td style='padding: 10px;'>N° Contrat</td><td style='padding: 10px;'>Type</td><td style='padding: 10px;'>Date debut";
			print "</td><td style='padding: 10px;'>date fin</td><td style='padding: 10px;'>Salaire Brut(Net)</td><td style='padding: 10px;'>Statut</td></tr>";

		//Partie affichage du Contrat ------------------------------------------------------------------------------------------------------------------------------------------
			//contrat actif
				$sql_contrat = "SELECT * FROM ".MAIN_DB_PREFIX."salarie_contrat WHERE fk_salarie=".$fk_salarie." AND active=1";
				$res_contrat = $db->query($sql_contrat);
				$actl[0] = img_picto("expiré", 'switch_off', 'class="size15x"');
				$actl[1] = img_picto("actif", 'switch_on', 'class="size15x"');

	}
}