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
require_once DOL_DOCUMENT_ROOT.'/custom/gestionflotte/class/html.form.class.php';

$id_vehicule = GETPOST("id_vehicule","int");
$action = GETPOST("action","alpha");
$aujourdhui = date("Y-m-d");
$id_document = GETPOST('id_document', 'int');

//Titre
if($id_vehicule){
$monform = new Form1($db);
	//detail
	$vehicule = "SELECT rowid, reference_interne, fk_type_vehicule, panne FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$id_vehicule;
		
	$result_vehicule = $db->query($vehicule);//= $db->query($covSql);

	if($result_vehicule)
		$obj_vehicule_mere = $db->fetch_object($result_vehicule);



		//Sauvegarde du document
	if($action == "add_document"){
		$fk_vehicule = GETPOST('fk_vehicule', 'int');
		$fk_type_vehicule = GETPOST('fk_type_vehicule', 'int');
		$fk_type_document = GETPOST('fk_type_document', 'int');
        $tarif = GETPOST('tarif', 'alpha');
		$note = GETPOST('note', 'alpha');
		$date_obtention = GETPOST('date_obtention');
		$date_debut = GETPOST('date_debut');
		$date_expiration = GETPOST('date_expiration');

		if(empty($fk_vehicule))
			$message .= 'Le champ "VEHICULE" est oblogatoire<br>';

		if(empty($fk_type_vehicule))
			$message .= 'Le champ "TYPE VEHICULE" est oblogatoire<br>';

		if(empty($fk_type_document))
			$message .= 'Le champ "TYPE DOCUMENT" est oblogatoire<br>';

		if(empty($tarif))
			$message .= 'Le champ "TARIF" est oblogatoire<br>';

		/*if(empty($date_obtention))
			$message .= 'Le champ "DATE OBTENTION" est oblogatoire<br>';*/


		if(empty($date_expiration))
			$message .= 'Le champ "DATE EXPIRATION" est oblogatoire<br>';

		if(!empty($date_debut) && !empty($date_expiration))
			if(strtotime($date_debut) >= strtotime($date_expiration))
				$message .= 'La "DATE EXPIRATION" doit être supérieure à la "DATE DEBUT"<br>';

		//Chargement de l'image
			if (isset($_FILES['image_document']) && $_FILES['image_document']['error'] == 0 && empty($message)) {
				//création du dossier s'il n'existe pas
				$nomDossier = './image_document';

				$rowid_doc = 0;
				$sql_document = "SELECT rowid FROM ".MAIN_DB_PREFIX."document_vehicule ORDER BY rowid DESC";
				$result_document = $db->query($sql_document);
				if($result_document)
					$rowid_doc = $db->fetch_object($result_document)->rowid + 1;

				$identification = $obj_vehicule_mere->reference_interne."_doc".$rowid_doc;
				if (!file_exists($nomDossier))
					if (mkdir($nomDossier, 0777, true)) {
						$img = $identification.$extension;
					}

				$nom = $_FILES['image_document']['name'];
				$chemin = $_FILES['image_document']['tmp_name'];
				$extension = strrchr($nom,".");
				$extension_autorisees = array('.JPG','.jpg','.png','.PNG','.jpeg','.JPEG','.pdf','.PDF');
				$destination = './image_document/'.$identification.$extension;
				//$destination = DOL_DOCUMENT_ROOT.'/paiementsalaire/onglets/image_document/contrat'.$fk_salarie.'__'.date('d_m_y_h_i_s').''.$extension;
				if(in_array($extension,$extension_autorisees)){
					if($_FILES['image_document']['size']<=3000000){
						if(move_uploaded_file($chemin,$destination)){
							$img = $identification.$extension;
						}elseif(isset($_FILES['image_document'])){
							$message = '❌ Erreur lors de l\'enregistrement de l\'image.';
						}
					}
				}
			}else{
				$message .= 'Le champ "DOCUMENT" est oblogatoire<br>';
			}

        if(empty($message)){
			 //insertion
			$sql_insert = "INSERT INTO ".MAIN_DB_PREFIX."document_vehicule (fk_vehicule, fk_type_vehicule, fk_type_document, tarif, date_expiration, fk_user_creation, nom_fichier";
			if(!empty($date_obtention))
				$sql_insert .= ", date_obtention";

			if(!empty($date_debut))
				$sql_insert .= ", date_debut";

			if(!empty($note))
				$sql_insert .= ", note";

			$sql_insert .= ")";
			
			$sql_insert .= " VALUES(".$fk_vehicule.", ".$fk_type_vehicule.", ".$fk_type_document.", ".$tarif.", '".$date_expiration."', ".$user->id.", '".$img."'";
			if(!empty($date_obtention))
				$sql_insert .= ", '".$date_obtention."'";

			if(!empty($date_debut))
				$sql_insert .= ", '".$date_debut."'";

			if(!empty($note))
				$sql_insert .= ", '".$note."'";

			$sql_insert .= ")";

			$result = $db->query($sql_insert);
			print $sql_insert;
			if($result){

				$message = "Un document enresistré avec succès";
				header('Location: ./document_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule);
				$action = "liste";
			}else{
				$action = "ajouter";
				$message = "Un problème est survenu";
			}
        }else{
            $action = "ajouter";
        }
}

if($action == "desactiver"){
	$date_hier = date('Y-m-d', strtotime($aujourdhui.'-1 day'));
	$url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$id_vehicule."&id_document=".$id_document;
	$titre = 'Voulez-vous déclarer ce document expiré hier le '.$date_hier.'?';
	$notice = img_picto('Attention', 'warning').' Vous êtes sur le point de mettre fin à la validité d\'un document';

	$formconfirm = $monform->formconfirm(
		$url, 
		$titre, 
		$notice, 
		"desactiver_ok", 
		$array, 
		'', 
		1,
		180,
		'45%'
	);
}

if($action == "desactiver_ok"){
	$date_hier = date('Y-m-d', strtotime($aujourdhui.'-1 day'));
	$sql_update = "UPDATE ".MAIN_DB_PREFIX."document_vehicule SET date_expiration = '".$date_hier."' WHERE rowid = ".$id_document;
	$res_upd = $db->query($sql_update);

	//print $sql_update;
	if($res_upd){
		$message = "Document désactivé avec succès";
	}else{
		$message = "Un problème est survenu";
	}
}
	print $db->error();
	llxHeader("", "Gestion Vehicule");
	print $db->error();
	//print load_fiche_titre($langs->trans("Documents ou Papiers du Véhicule"), '', '');
	$head = vehicule_Head($id_vehicule);
	print dol_get_fiche_head($head, 'document', "", -1, '');

	$obj_soc = prepare_objet_entete($id_vehicule, $db);
    entete_vehicule($obj_soc, 'commande');
	print '<hr>';

		avertissement($db, $id_vehicule);

if(!$obj_vehicule_mere->panne){

	print $formconfirm;

	if($action == "ajouter"){
	print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i><br><br>';
	print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add_document">';

	//Date obtention
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" ><label>Date obtention</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_obtention" value="'.GETPOST("date_obtention").'" name="date_obtention" ></td></tr>';

	//Vehicule
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_vehicule">Véhicule</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_vehicule" name="fk_vehicule" class="minwidth200 height10">';
    $sql = "SELECT rowid, nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid = ".$id_vehicule;
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($obj_vehicule = $db->fetch_object($res)) {
			print '<option value="'.$obj_vehicule->rowid.'" selected>'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
		}

    }
	print '</select></td></tr>';
	//Type Vehicule
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_type_vehicule">Type véhicule</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_type_vehicule" name="fk_type_vehicule" class="minwidth200 height10">';
    $sql = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."type_vehicule WHERE rowid = ".$obj_vehicule_mere->fk_type_vehicule;
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($obj_vehicule_type = $db->fetch_object($res)) {
			print '<option value="'.$obj_vehicule_type->rowid.'" selected>'.$obj_vehicule_type->nom.'</option>';
		}

    }
	print '</select></td></tr>';

	//Type document
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_type_document">Type Document</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_type_document" name="fk_type_document" class="minwidth200 height10">';
	print '<option></option>';
    $sql = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."type_document";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($obj_type_document = $db->fetch_object($res)) {
			if(!empty(GETPOST('fk_type_document', 'int'))){
				if($obj_type_document->rowid == GETPOST('fk_type_document', 'int'))
					print '<option value="'.$obj_type_document->rowid.'" selected>'.$obj_type_document->nom.'</option>';
				else
					print '<option value="'.$obj_type_document->rowid.'">'.$obj_type_document->nom.'</option>';
			}else
				print '<option value="'.$obj_type_document->rowid.'">'.$obj_type_document->nom.'</option>';
		}

    }
	print '</select></td></tr>';

	//tarif
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Tarif</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="tarif" name="tarif" value="'.(GETPOST("tarif")).'"></td></tr>';

	//Date début
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date début validitée</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_debut" value="'.GETPOST("date_debut").'" name="date_debut" ></td></tr>';

	//Date expiration
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date expiration</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_expiration" value="'.GETPOST("date_expiration").'" name="date_expiration" ></td></tr>';

	print "<tr><td style='width: 200px; padding-right: 30px; padding-bottom: 7px' class='fieldrequired'>Document</td>";
	print "<td style='width: 600px; padding-right: 30px; padding-bottom: 7px'><input type='file' name='image_document' id='image_document' ><3Mo</td></tr>";

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" ><label>Note</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><textarea name="note" style="width:250px; height: 40px;">'.GETPOST("note").'</textarea></td></tr>';

	print '</table>';
	print '<hr>';
	print '
		<div style="text-align: center"; align-items: center; justify-content: center">
        <input class="button" type="submit" value="Enregistrer">
        </form>
        <a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'" class="button">Annuler</a></td></tr>
    </div>
    ';
}else{

		print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Ajout au renouvellement de document", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'&action=ajouter' , '', 1), '', 0, 0, 0, 1);
		$id_array_obligatoire = array();
		$sql = "SELECT DISTINCT rowid, nom FROM ".MAIN_DB_PREFIX."type_document WHERE type = 'obligatoire' AND FIND_IN_SET(".((int) $obj_vehicule_mere->fk_type_vehicule).", fk_type_vehicule)";
		$res = $db->query($sql);
		if($res){
			$num = $db->num_rows($res);
			$a = 0;
			while ($obj_type_document = $db->fetch_object($res)) {

				$id_array_obligatoire[] = $obj_type_document->rowid;
				print "<div>";
				print "<h3 >".$obj_type_document->nom."</h3>";
				print "<table class='tagtable liste' style='width:100%;'>";
				print "<tr class='liste_titre'><td style='padding: 10px;'>Type</td><td style='padding: 10px;'>Tarif</td><td style='padding: 10px;'>Date début";
				print "</td><td style='padding: 10px;'>date expiration</td><td style='padding: 10px;'>Date d'obtention</td><td style='padding: 10px;'>Fichier</td><td style='padding: 10px;'>Statut</td></tr>";

			//Partie affichage des document ------------------------------------------------------------------------------------------------------------------------------------------
				//document actif
					$actl[0] = img_picto("expiré", 'switch_off', 'class="size15x"');
					$actl[1] = img_picto("Déclaré expiré ?", 'switch_on', 'class="size15x"');
					$sql_document = "SELECT dv.*, td.rowid as tdrowid, td.nom FROM ".MAIN_DB_PREFIX."document_vehicule as dv";
					$sql_document .= " LEFT JOIN ".MAIN_DB_PREFIX."type_document as td on dv.fk_type_document = td.rowid";
					$sql_document .= "  WHERE dv.fk_type_document = ".$obj_type_document->rowid." AND fk_vehicule=".$obj_vehicule_mere->rowid." AND date_expiration >= '".$aujourdhui."'";

					$sql_document .= " ORDER BY date_expiration DESC";
					$result_document = $db->query($sql_document);
					if($result_document){
						$num_doc = $db->num_rows($result_document);
						while ($obj_document = $db->fetch_object($result_document)){

							$document = "N/A";
							if($obj_document->nom_fichier)
								$document = '<a target="_blank" href="./image_document/'.$obj_document->nom_fichier.'">'.$obj_document->nom_fichier.'</a>';

							$etat = 1;
							if($obj_document->date_expiration < $aujourdhui)
								$etat = 0;
							$etat = /*'<a href="'.$_SERVER['PHP_SELF'].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'&id_document='.$obj_document->rowid.'&action=desactiver">'.*/$actl[$etat];//.'</a>';
							$type_document = '<a title="'.$obj_document->note.'" href="../creation_type_document.php?mainmenu=gestionflotte&leftmenu=listevehicule&recherche_nom='.$obj_document->tdrowid.'&action=liste">'.$obj_document->nom.'</a>';
							print "<tr><td style='padding: 10px;'>".$type_document."</td><td style='padding: 10px;'>".$obj_document->tarif."</td><td style='padding: 10px;'>".$obj_document->date_debut;
							print "</td><td style='padding: 10px;'>".$obj_document->date_expiration."</td><td style='padding: 10px;'>".$obj_document->date_obtention."</td><td style='padding: 10px;'>".$document."</td><td style='padding: 10px;'>".$etat."</td></tr>";
						}
					}
					if($num_doc <= 0)
						print '<tr><td align="center" colspan="7">Aucun enregistrement trouvé</td></tr>';

				print "</table></div>";


				/*//les contrats expirés
				print "<br><div>";
					$sql_document = "SELECT dv.*, td.rowid as tdrowid, td.nom FROM ".MAIN_DB_PREFIX."document_vehicule as dv";
					$sql_document .= " LEFT JOIN ".MAIN_DB_PREFIX."type_document as td on dv.fk_type_document = td.rowid";
					$sql_document .= " WHERE fk_vehicule=".$obj_vehicule_mere->rowid." AND date_expiration < '".$aujourdhui."'";

					$sql_document .= " ORDER BY date_expiration";

				$result_document = $db->query($sql_document);
				print "<h3 >Documents expirés</h3>";
				print "<table class='tagtable liste' style='width:100%;'>";
				print "<tr class='liste_titre'><td style='padding: 10px;'>Type</td><td style='padding: 10px;'>Tarif</td><td style='padding: 10px;'>Date début";
				print "</td><td style='padding: 10px;'>date expiration</td><td style='padding: 10px;'>Date d'obtention</td><td style='padding: 10px;'>Fichier</td><td style='padding: 10px;'>Statut</td></tr>";
				
				$result_document = $db->query($sql_document);
					if($result_document){
						$num = $db->num_rows($result_document);
						while ($obj_document = $db->fetch_object($result_document)){

							$document = "N/A";
							if($obj_document->nom_fichier)
								$document = '<a target="_blank" href="./image_document/'.$obj_document->nom_fichier.'">'.$obj_document->nom_fichier.'</a>';

							$type_document = '<a title="'.$obj_document->note.'" href="../creation_type_document.php?mainmenu=gestionflotte&leftmenu=listevehicule&recherche_nom='.$obj_document->tdrowid.'&action=liste">'.$obj_document->nom.'</a>';
							print "<tr><td style='padding: 10px;'>".$type_document."</td><td style='padding: 10px;'>".$obj_document->tarif."</td><td style='padding: 10px;'>".$obj_document->date_debut;
							print "</td><td style='padding: 10px;'>".$obj_document->date_expiration."</td><td style='padding: 10px;'>".$obj_document->date_obtention."</td><td style='padding: 10px;'>".$document."</td><td style='padding: 10px;'>".$actl[0]."</td></tr>";
						}
					}
					if($num <= 0)
						print '<tr><td align="center" colspan="7">Aucun enregistrement trouvé</td></tr>';

					print "</table></div>";*/

			}
		}

		print "<div>";
				print "<h3 >Autres</h3>";
				print "<table class='tagtable liste' style='width:100%;'>";
				print "<tr class='liste_titre'><td style='padding: 10px;'>Type</td><td style='padding: 10px;'>Tarif</td><td style='padding: 10px;'>Date début";
				print "</td><td style='padding: 10px;'>date expiration</td><td style='padding: 10px;'>Date d'obtention</td><td style='padding: 10px;'>Fichier</td><td style='padding: 10px;'>Statut</td></tr>";

			//Partie affichage des document ------------------------------------------------------------------------------------------------------------------------------------------
				//document actif
					$actl[0] = img_picto("expiré", 'switch_off', 'class="size15x"');
					$actl[1] = img_picto("Déclaré expiré ?", 'switch_on', 'class="size15x"');
					$sql_document = "SELECT dv.*, td.rowid as tdrowid, td.nom FROM ".MAIN_DB_PREFIX."document_vehicule as dv";
					$sql_document .= " LEFT JOIN ".MAIN_DB_PREFIX."type_document as td on dv.fk_type_document = td.rowid";
					$sql_document .= "  WHERE dv.fk_type_document NOT IN (".implode(',', $id_array_obligatoire).") AND fk_vehicule=".$obj_vehicule_mere->rowid." AND date_expiration >= '".$aujourdhui."'";

					$sql_document .= " ORDER BY date_expiration DESC";
					$result_document = $db->query($sql_document);
					if($result_document){
						$num_doc = $db->num_rows($result_document);
						while ($obj_document = $db->fetch_object($result_document)){

							$document = "N/A";
							if($obj_document->nom_fichier)
								$document = '<a target="_blank" href="./image_document/'.$obj_document->nom_fichier.'">'.$obj_document->nom_fichier.'</a>';

							$etat = 1;
							if($obj_document->date_expiration < $aujourdhui)
								$etat = 0;
							$etat = /*'<a href="'.$_SERVER['PHP_SELF'].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'&id_document='.$obj_document->rowid.'&action=desactiver">'.*/$actl[$etat];//.'</a>';
							$type_document = '<a title="'.$obj_document->note.'" href="../creation_type_document.php?mainmenu=gestionflotte&leftmenu=listevehicule&recherche_nom='.$obj_document->tdrowid.'&action=liste">'.$obj_document->nom.'</a>';
							print "<tr><td style='padding: 10px;'>".$type_document."</td><td style='padding: 10px;'>".$obj_document->tarif."</td><td style='padding: 10px;'>".$obj_document->date_debut;
							print "</td><td style='padding: 10px;'>".$obj_document->date_expiration."</td><td style='padding: 10px;'>".$obj_document->date_obtention."</td><td style='padding: 10px;'>".$document."</td><td style='padding: 10px;'>".$etat."</td></tr>";
						}
					}
					if($num_doc <= 0)
						print '<tr><td align="center" colspan="7">Aucun enregistrement trouvé</td></tr>';

				print "</table></div>";
	}

}else print '<h2>Ce véhicule est déclaré en panne !</h2>';
}

if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}
