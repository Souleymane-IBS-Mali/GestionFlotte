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

$id_vehicule = GETPOST("id_vehicule", "int");
$action = GETPOST("action", "alpha");
$aujourdhui = date('Y-m-d');
//Titre
//print load_fiche_titre($langs->trans("Maintenances"), '', '');

if($id_vehicule){
$monform = new Form1($db);
	
	//detail
	$vehicule = "SELECT * FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$id_vehicule;
		
	$result_vehicule = $db->query($vehicule);//= $db->query($covSql);

	if($result_vehicule)
		$obj_vehicule = $db->fetch_object($result_vehicule);

	if($action == "ajouter_reparation"){
		$date = GETPOST('date_maintenance');
		$type_maintenance = GETPOST('type_maintenance', 'int');
		$nature_traveaux = GETPOST('nature_traveaux', 'alpha');
		$description = GETPOST('description', 'alpha');
		$cout = GETPOST('cout', 'alpha');
		$piece_remplace = GETPOST('piece', 'alpha');

		if(empty($description))
			$message = 'Le champ "Description" est oblogatoire<br>';
		
		if(empty($date))
			$message .= 'Le champ "DATE MAINTENANCE" est oblogatoire<br>';

		if(empty($nature_traveaux))
			$message .= 'Le champ "TRAVEAUX EFFECTUES" est oblogatoire<br>';

		if(empty($cout))
			$message .= 'Le champ "COUT" est oblogatoire<br>';

		if(empty($piece_remplace))
			$message .= 'Le champ "PIECE REMPLACE" est oblogatoire<br>';

		//Chargement de l'image
			if (isset($_FILES['facture']) && $_FILES['facture']['error'] == 0 && empty($message)) {
				//création du dossier s'il n'existe pas
				$nomDossier = './facture_maintenance_reparation';

				$rowid_doc = 0;
				$sql_document = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule ORDER BY rowid DESC";
				$result_document = $db->query($sql_document);
				if($result_document)
					$rowid_doc = $db->fetch_object($result_document)->rowid + 1;

				$identification = $obj_vehicule_mere->reference_interne."_facture".$rowid_doc;
				if (!file_exists($nomDossier))
					if (mkdir($nomDossier, 0777, true)) {
						$img = $identification.$extension;
					}

				$nom = $_FILES['facture']['name'];
				$chemin = $_FILES['facture']['tmp_name'];
				$extension = strrchr($nom,".");
				$extension_autorisees = array('.JPG','.jpg','.png','.PNG','.jpeg','.JPEG','.pdf','.PDF');
				$destination = './facture_maintenance_reparation/'.$identification.$extension;
				//$destination = DOL_DOCUMENT_ROOT.'/paiementsalaire/onglets/facture/contrat'.$fk_salarie.'__'.date('d_m_y_h_i_s').''.$extension;
				if(in_array($extension,$extension_autorisees)){
					if($_FILES['facture']['size']<=3000000){
						if(move_uploaded_file($chemin,$destination)){
							$img = $identification.$extension;
						}elseif(isset($_FILES['facture'])){
							$message = '❌ Erreur lors de l\'enregistrement de l\'image.';
						}
					}
				}
			}

		if(empty($message)){
			$piece_remplace = implode(',', $piece_remplace);
			$sql_insert = 'INSERT INTO '.MAIN_DB_PREFIX.'maintenance_vehicule (maintenance_reparation, commentaire, fk_vehicule, date_maintenance, travaux_effectue, fk_piece_remplacer, cout, fk_user_creation';
			if($img)
				$sql_insert .= ', facture';
			$sql_insert .= ')';

			$sql_insert .= ' VALUES(2, "'.$description.'", '.$id_vehicule.', "'.$date.'","'.$nature_traveaux.'", "'.$piece_remplace.'", '.$cout.', '.$user->id;
			if($img)
				$sql_insert .= ', "'.$img.'"';

			$sql_insert .= ')';
			//print $sql_insert;
			$res_insert = $db->query($sql_insert);

			if($res_insert){
				$message = "Une réparation enregistrée avec succès";
				header('Location: '.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule);
			}else{
				$message = "Un problème est survenu";
				$action = "ajouter";
			}
			
		}else $action = "ajouter";

	}
			print $db->error();

	llxHeader("", "Gestion Vehicule");
	$head = vehicule_Head($id_vehicule);
	print dol_get_fiche_head($head, 'maintenance', "", -1, '');

	$obj_soc = prepare_objet_entete($id_vehicule, $db);
    entete_vehicule($obj_soc, 'commande');
	//print '<hr>';

		avertissement($db, $id_vehicule);

	$head = maintenanceHead($id_vehicule);
	print dol_get_fiche_head($head, 'reparation', "", -1, '');
	if($action == "ajouter"){
			print ' <form name="ajout" method="POST" action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'" enctype="multipart/form-data">';
			print '<input type="hidden" name="token" value="'.newToken().'">';
			print '<input type="hidden" name="action" value="ajouter_reparation">';

			print "<table>";

			//Descrition
			print "<tr><td class='fieldrequired' style='width: 250px; padding-top: 10px;'>Description</td><td style='width: 200px; padding-top: 10px;'>
			<textarea name='description'>".GETPOST('description')."</textarea>";
			print "</td></tr>";

			print "<tr><td class='fieldrequired' style='width: 250px; padding-top: 10px;'>Date réparation</td><td style='width: 200px; padding-top: 10px;'><input type='date' name='date_maintenance' id='date_maintenance' value='".GETPOST("date_maintenance")."'></td></tr>";

			//nature des traveaux
			print "<tr><td class='fieldrequired' style='width: 250px; padding-top: 10px;'>Nature des traveaux effectués</td><td style='width: 200px; padding-top: 10px;'>
			<textarea name='nature_traveaux'>".GETPOST('nature_traveaux')."</textarea>";
			print "</td></tr>";

			//Coût
			print "<tr><td class='fieldrequired' style='width: 250px; padding-top: 10px;'>Coût</td><td style='width: 200px; padding-top: 10px;'>
			<input type='text' value='".(GETPOST('cout'))."' name='cout' id='cout'>";
			print "</td></tr>";

			//Pièces remplacées
			print "<tr><td style='width: 250px; padding-top: 10px;' class='fieldrequired'>Pièces remplacées</td><td>";
			$array_id = array(-1);
			$array_nom = array("Aucune pièce");
			$id_selected_array = GETPOST('piece')?:array();
			$sql = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."piece";
			$res = $db->query($sql);
			if($res){
				$num = $db->num_rows($res);
				while ($obj_user = $db->fetch_object($res)) {
					$array_id[] = $obj_user->rowid;
					$array_nom[] = $obj_user->nom;		
				}

			}

			$alltype = array_combine($array_id, $array_nom);
			$monform = new Form($db);
			print $monform->multiselectarray('piece', $alltype, $id_selected_array, null, 0, 'minwidth200 height10', 0, 0);


			print "</td></tr>";

			//Reçu
			print "<tr><td style='width: 250px; padding-top: 10px;'>Facture</td><td style='width: 350px; padding-top: 10px;'><input type='file' name='facture' id='facture' ><1Mo</td></tr>";

			print '<tr>';
			print '<td style=" padding-right: 250px; padding-bottom: 30px"></td><td style="padding-top: 30px; width: 300px;"><input class="button" type="submit" value="Enregistrer">';
			print'</form>';
			print '<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'" class="button">Annuler</a></td></tr>';
			print '</table>';
	}else{
		$annee_rechercher = GETPOST('annee', 'int')?:date('Y');
			print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Déclarer une maintenance ou réparation", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'&action=ajouter', '', 1), '', 0, 0, 0, 1);
			print "<div>";
			$actl[0] = img_picto("expiré", 'switch_off', 'class="size15x"');
			$actl[1] = img_picto("actif", 'switch_on', 'class="size15x"');

			//Filtre par année
				print "<div style='float: right; margin-right:'30px'>";
				print '<form name="add" method="POST" action="'.$_SERVER['PHP_SELF'].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'">';
				print '<input type="hidden" name="token" value="'.newToken().'">';
				print '<input type="hidden" name="action" value="save_edit">';
				$info = "Les années affichées sont les années auquelles ce salarié à au moins un bulletin";
				print info_admin($langs->trans($info), 1)."<select style='font-size: 24px; font-weight: bold;' name='annee_rechercher' id='annee_rechercher'><option value='0'></option>";
				//affichage de la zone de recherche année
				//les valeurs son uniquement les années au cours desquelles le salarié a au moins un bulletin
				$sql_verif = "SELECT DISTINCT YEAR(date_maintenance) as annee FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation = 1 AND fk_vehicule = ".$id_vehicule;
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


			print "<h3 >Historiques des réparations de <mark>".$annee_rechercher."</mark></h3>";
			print "<table class='tagtable liste' style='width:100%;'>";
			print "<tr class='liste_titre'><td style='padding: 10px;'>Description</td><td style='padding: 10px;'>Date réparation</td><td style='padding: 10px;'>Traveaux effectués";
			print "</td><td style='padding: 10px;'>Pièces remplacées</td><td style='padding: 10px;'>facture</td><td style='padding: 10px;'>Statut</td></tr>";

			$sql_maintenance = "SELECT mv.*, tm.rowid as tmrowid, tm.libelle FROM ".MAIN_DB_PREFIX."maintenance_vehicule as mv";
			$sql_maintenance .= " LEFT JOIN ".MAIN_DB_PREFIX."type_maintenance as tm on mv.fk_type_maintenance = tm.rowid";
			$sql_maintenance .= " WHERE mv.fk_type_maintenance != '1' AND mv.maintenance_reparation != 1 AND mv.fk_vehicule=".$id_vehicule;

			if($annee_rechercher != date('Y'))
				$sql_maintenance .= " AND YEAR(date_maintenance) = ".$annee_rechercher;

			$sql_maintenance .= " ORDER BY date_maintenance DESC";

			//print $sql_maintenance;
			$res_maintenance = $db->query($sql_maintenance);
			if($res_maintenance){
				$num = $db->num_rows($res_maintenance);
				while ($obj_maintenance = $db->fetch_object($res_maintenance)) {
					$description = img_picto('','maintenance', 'class="paddingright pictofixedwidth"').$obj_maintenance->commentaire;

					print "<tr><td style='padding: 10px;'>".$description."</td><td style='padding: 10px;'>".$obj_maintenance->date_maintenance."</td>";
					print affiche_long_texte('',  $obj_maintenance->travaux_effectue, 1, '', '', '', '', '', '');;
					$outils = "";
						if($obj_maintenance->fk_piece_remplacer != -1){
							$sql_outil = "SELECT nom FROM ".MAIN_DB_PREFIX."piece WHERE rowid IN (".$obj_maintenance->fk_piece_remplacer.") ORDER BY nom";
							$res_outil = $db->query($sql_outil);
							if($res_outil)
								while($obj_outil = $db->fetch_object($res_outil)){
									$outils .= '<span style="background-color: #6c757d; color: white; padding: 1px 6px; border-radius: 3px; font-weight: bold;">
									<a style="color : white; text-decoration : none;" href="../creation_piece.php?mainmenu=gestionflotte&leftmenu=panne&recherche_nom='.$obj_outil->nom.'">'.$obj_outil->nom.'</a>
									</span>&nbsp;'; // Gris
								}
						}else{
							$outils .= '<span style="background-color: #6c757d; color: white; padding: 3px 6px; border-radius: 3px; font-weight: bold;">Aucune</span>&nbsp;'; // Gris

						}
					$img = '<a title="Visualiser" target="_blank" href="./facture_maintenance_reparation/'.$obj_maintenance->facture.'">'.$obj_maintenance->facture.'</a>';
					print "<td style='padding: 10px;'>".$outils."</td><td style='padding: 10px;'>".$img."</td><td style='padding: 10px;'>Effectuées</td></tr>";

				}
			}
			if($num <= 0)
				print "<tr><td align = 'center' colspan = '6' >Aucun enregistrement trouvé</td></tr>";
			print "</table></div>";

		
	}
}


//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}