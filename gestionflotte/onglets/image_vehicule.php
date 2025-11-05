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

$id_vehicule = GETPOST("id_vehicule", "int");
$action = GETPOST("action", "alpha");
$message = GETPOST("message", "alpha");
if($id_vehicule){

	//detail
	$vehicule = "SELECT * FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$id_vehicule;
		
	$result_vehicule = $db->query($vehicule);//= $db->query($covSql);

	if($result_vehicule)
		$obj_vehicule_mere = $db->fetch_object($result_vehicule);

	if($action == "add_image"){
		$note = GETPOST('note', 'alpha');
		$date_obtention = GETPOST('date_obtention');


		//Chargement de l'image
			if (isset($_FILES['image_vehicule']) && $_FILES['image_vehicule']['error'] == 0 && empty($message)) {
				//création du dossier s'il n'existe pas
				$nomDossier = './image_vehicule';

				$rowid_doc = 0;
				$sql_document = "SELECT rowid FROM ".MAIN_DB_PREFIX."image_vehicule ORDER BY rowid DESC";
				$result_document = $db->query($sql_document);
				if($result_document)
					$rowid_doc = $db->fetch_object($result_document)->rowid + 1;

				$identification = $obj_vehicule_mere->reference_interne."_doc".$rowid_doc;
				if (!file_exists($nomDossier))
					if (mkdir($nomDossier, 0777, true)) {
						$img = $identification.$extension;
					}

				$nom = $_FILES['image_vehicule']['name'];
				$chemin = $_FILES['image_vehicule']['tmp_name'];
				$extension = strrchr($nom,".");
				$extension_autorisees = array('.JPG','.jpg','.png','.PNG','.jpeg','.JPEG','.pdf','.PDF');
				$destination = './image_vehicule/'.$identification.$extension;
				//$destination = DOL_DOCUMENT_ROOT.'/paiementsalaire/onglets/image_vehicule/contrat'.$fk_salarie.'__'.date('d_m_y_h_i_s').''.$extension;
				if(in_array($extension,$extension_autorisees)){
					if($_FILES['image_vehicule']['size']<=3000000){
						if(move_uploaded_file($chemin,$destination)){
							$img = $identification.$extension;
						}elseif(isset($_FILES['image_vehicule'])){
							$message = '❌ Erreur lors de l\'enregistrement de l\'image.';
						}
					}else $message .= ' "TAILLE IMAGE" dépassée<br>';
				}
			}else{
				$message .= 'Le champ "IMAGE" est oblogatoire<br>';
			}
			
		if(empty($date_obtention))
			$message .= 'Le champ "DATE OBTENTION" est oblogatoire<br>';

		if(empty($note))
			$message .= 'Le champ "DESCRIPTION" est oblogatoire<br>';



        if(empty($message)){
			 //insertion
			$sql_insert = "INSERT INTO ".MAIN_DB_PREFIX."image_vehicule (fk_vehicule, date_obtention, fk_user_creation, img, commentaire)";
			
			$sql_insert .= " VALUES(".$id_vehicule.", '".$date_obtention."', ".$user->id.", '".$img."', '".$note."')";

			$result = $db->query($sql_insert);
			//print $sql_insert;
			if($result){

				$message = "Un document enresistré avec succès";
				header('Location: ./image_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'&message='.$message);
				$action = "liste";
			}else{
				$action = "ajouter";
				$message = "Un problème est survenu";
			}
        }else{
            $action = "ajouter";
        }
}

print $db->error();

llxHeader("", "Gestion Vehicule");
	//Titre
	//print load_fiche_titre($langs->trans("Images du véhicule"), '', '');
	$head = vehicule_Head($id_vehicule);
	print dol_get_fiche_head($head, 'images', "", -1, '');

	$obj_soc = prepare_objet_entete($id_vehicule, $db);
    entete_vehicule($obj_soc, 'commande');
	print '<hr>';

		avertissement($db, $id_vehicule);

	if($action == "ajouter"){
			print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i><br><br>';
	print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add_image">';

	print "<tr><td style='width: 200px; padding-right: 30px; padding-bottom: 7px' class='fieldrequired'>Image</td>";
	print "<td style='width: 600px; padding-right: 30px; padding-bottom: 7px'><input type='file' name='image_vehicule' id='image_vehicule' ><3Mo</td></tr>";

	//Date obtention
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date obtention</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_obtention" value="'.GETPOST("date_obtention").'" name="date_obtention" ></td></tr>';

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Description</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><textarea name="note" style="width:300px; height: 50px;">'.GETPOST("note").'</textarea></td></tr>';

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
			print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Ajouter une image", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'&action=ajouter' , '', 1), '', 0, 0, 0, 1);
			print "<div>";
			print "<h3 >Images ajouteés</h3>";
			print "<table class='tagtable liste' style='width:100%;'>";
			print "<tr class='liste_titre'><td style='padding: 10px;'>image</td><td style='padding: 10px;'>Date d'ajout";
			print "</td><td style='padding: 10px;'>Date création</td><td style='padding: 10px;'>Description</td></tr>";

		//Partie affichage du Contrat ------------------------------------------------------------------------------------------------------------------------------------------
			//contrat actif
				$sql_image = "SELECT * FROM ".MAIN_DB_PREFIX."image_vehicule WHERE fk_vehicule = ".$id_vehicule;
				$result_image = $db->query($sql_image);
				if($result_image){
					$num = $db->num_rows($result_image);
					while ($obj_image = $db->fetch_object($result_image)){
						$img = '<a title="Visualiser" target="_blank" href="./image_vehicule/'.$obj_image->img.'">'.$obj_image->img.'</a>';
						print "<tr><td style='padding: 10px;'>".$img."</td><td style='padding: 10px;'>".$obj_image->date_obtention."";
						print "</td><td style='padding: 10px;'>".$obj_image->date_creation."</td>";
						print ''.affiche_long_texte('', $obj_image->commentaire, 1, '', '', '', '', '', '');
						
						print "</tr>";
					}
				}
				if($num <= 0)
					print '<tr><td align="center" colspan="4">Aucun enregistrement trouvé</td></tr>';
				print "</table></div>";

	}
}

if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}