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

llxHeader("", "Gestion Vehicule");
//Titre
//print load_fiche_titre($langs->trans("Assignations"), '', '');

if($id_vehicule){
	$monform = new Form1($db);

	$head = vehicule_Head($id_vehicule);
	print dol_get_fiche_head($head, 'assignation', "", -1, '');

	$obj_soc = prepare_objet_entete($id_vehicule, $db);
    entete_vehicule($obj_soc, 'commande');
	print '<hr>';
	
		avertissement($db, $id_vehicule);

	//detail
	$vehicule = "SELECT * FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$id_vehicule;
		
	$result_vehicule = $db->query($vehicule);//= $db->query($covSql);

	if($result_vehicule)
		$obj_vehicule_mere = $db->fetch_object($result_vehicule);

if(!$obj_vehicule_mere->panne){
	if($action == "new_assignation_ok"){
		$fk_vehicule = GETPOST('fk_vehicule', 'int');
			$fk_user = GETPOST('fk_user', 'int');
			$note = GETPOST('note', 'alpha');
			$etat_vehicule = GETPOST('etat_vehicule', 'alpha');
			$equipement = GETPOST('equipement');
			$date_debut = GETPOST('date_debut');
			$date_fin = GETPOST('date_fin');
			$document = GETPOST('document');

			$message = "";
			if(empty($fk_vehicule) || $fk_vehicule <= 0)
				$message = 'Le champ "VEHICULE" est oblogatoire<br>';

			if(empty($fk_user) || $fk_user <= 0)
				$message .= 'Le champ "Utilisateur" est oblogatoire<br>';

			if(empty($etat_vehicule))
				$message .= 'Le champ "ETAT" est oblogatoire<br>';

			if(empty($equipement))
				$message .= 'Le champ "EQUIPEMENT DE BORD" est oblogatoire<br>';

			if(empty($date_debut))
				$message .= 'Le champ "DATE DEBUT" est oblogatoire<br>';


			if(empty($message)){
				$sql_vehicule = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'assignation_vehicule WHERE fk_vehicule='.$id_vehicule.' ORDER BY rowid DESC';
				$result_vehicule = $db->query($sql_vehicule);
					
				if($result_vehicule){
					$num = $db->num_rows($result_vehicule);
					$obj = $db->fetch_object($result_vehicule);
				}

				$date_hier = date('Y-m-d', strtotime($date_debut.'-1 day'));
				$sql_update = 'UPDATE '.MAIN_DB_PREFIX.'assignation_vehicule SET date_fin = "'.$date_hier.'" WHERE rowid = '.$obj->rowid;
				$result_up = $db->query($sql_update);

				//print $sql_update;

				$sql_insert = 'INSERT INTO '.MAIN_DB_PREFIX.'assignation_vehicule (fk_vehicule, fk_user, date_debut, fk_user_creation, etat_vehicule, equipement, note';

					if(!empty($img))
						$sql_insert .= ', document';

					if(!empty($date_fin))
						$sql_insert .= ', date_fin';
					$sql_insert .=')';

					$sql_insert .= ' VALUES('.$fk_vehicule.', '.$fk_user.', "'.$date_debut.'", '.$user->id.',"'.$etat_vehicule.'","'.$equipement.'", "'.$note.'"';

					if(!empty($img))
						$sql_insert .= ', "'.$document.'"';

					if(!empty($date_fin))
						$sql_insert .= ', "'.$date_fin.'"';
					$sql_insert .=')';
					$result = $db->query($sql_insert);
				//print $sql_insert;
				if($result){

					$message = "Nouvelle assignation éffectuée avec succès";
					$action = "liste";
					//header('Location: ./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_assignation='.$id_nouveau_vehicule);
				}else{
					$action = "ajouter";
					$message = "Un problème est survenu";
				}
			}else $action = "ajouter";
	}
		//Sauvegarde de l'assignation
		if($action == "add_assignation"){
			$fk_vehicule = GETPOST('fk_vehicule', 'int');
			$fk_user = GETPOST('fk_user', 'int');
			$note = GETPOST('note', 'alpha');
			$etat_vehicule = GETPOST('etat_vehicule', 'alpha');
			$equipement = GETPOST('equipement');
			$date_debut = GETPOST('date_debut');
			$date_fin = GETPOST('date_fin');

			$message = "";
			if(empty($fk_vehicule) || $fk_vehicule <= 0)
				$message = 'Le champ "VEHICULE" est oblogatoire<br>';

			if(empty($fk_user) || $fk_user <= 0)
				$message .= 'Le champ "Utilisateur" est oblogatoire<br>';

			if(empty($etat_vehicule))
				$message .= 'Le champ "ETAT" est oblogatoire<br>';

			if(empty($equipement))
				$message .= 'Le champ "EQUIPEMENT DE BORD" est oblogatoire<br>';

			if(empty($date_debut))
				$message .= 'Le champ "DATE DEBUT" est oblogatoire<br>';

			//Document assignation
			//Chargement de l'image
				if (isset($_FILES['document']) && $_FILES['document']['error'] == 0 && empty($message)) {
					//création du dossier s'il n'existe pas
					$identification = 0;
					$sql_vehicule = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'assignation_vehicule WHERE fk_vehicule='.$id_vehicule;
					$result_vehicule = $db->query($sql_vehicule);
						
					if($result_vehicule){
						$identification = $db->fetch_object($result_vehicule)->rowid + 1;
					}

					$nomDossier =  DOL_DOCUMENT_ROOT.'/gestionflotte/onglets/documents_assignations';
					if (!file_exists($nomDossier))
						if (mkdir($nomDossier, 0777, true)) {
							$img = $identification.$extension;
						}

					$nom = $_FILES['document']['name'];
					$chemin = $_FILES['document']['tmp_name'];
					$extension = strrchr($nom,".");
					$extension_autorisees = array('.JPG','.jpg','.png','.PNG','.jpeg','.JPEG','.pdf','.PDF');
					$destination = DOL_DOCUMENT_ROOT.'/gestionflotte/onglets/documents_assignations/'.$identification.$extension;

					//$destination = DOL_DOCUMENT_ROOT.'/paiementsalaire/onglets/document/contrat'.$fk_salarie.'__'.date('d_m_y_h_i_s').''.$extension;
					if(in_array($extension,$extension_autorisees)){
						if($_FILES['document']['size']<=3000000){
							if(move_uploaded_file($chemin,$destination)){
								$img = $identification.$extension;
							}elseif(isset($_FILES['document'])){
								$message .= '❌ Erreur lors de l\'enregistrement de l\'image.';
							}
						}
					}
				}

			if(empty($message)){
				$equipement = implode(',', $equipement);
				$num = 0;
				$sql_vehicule = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'assignation_vehicule WHERE fk_vehicule='.$id_vehicule;
				$result_vehicule = $db->query($sql_vehicule);
					
				if($result_vehicule){
					$num = $db->num_rows($result_vehicule);
				}

				if($num > 0){ //Après avoir mis fin à l'anciennes assignation et création d'une nouvelle


					$url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$id_vehicule;
					$titre = 'Voulez-vous mettre fin à l\'assignation en cours avec les valeurs ci-dessous ?';
					$notice = img_picto('Attention', 'warning').' Vous êtes sur le point d\'assigner ce véhicule';
					$sql = "SELECT rowid, firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid = ".$fk_user;
					$res = $db->query($sql);
					if($res){
						$obj_user = $db->fetch_object($res);

					}
					$array[] = array('label'=> 'Véhicule','type'=> 'hidden', 'size'=>'', 'morecss'=>'fieldrequired minwidth200', 'moreattr'=>'', 'name'=>'fk_vehicule','value' => $fk_vehicule);
					$array[] = array('label'=> 'Utilisateur','type'=> 'hidden', 'size'=>'', 'morecss'=>'fieldrequired minwidth200', 'moreattr'=>'', 'name'=>'fk_user','value' => $obj_user->rowid);
					$array[] = array('label'=> 'Date début','type'=> 'hidden', 'size'=>'', 'morecss'=>'fieldrequired minwidth200', 'moreattr'=>'', 'name'=>'date_debut','value' => $date_debut);
					$array[] = array('label'=> 'Date fin','type'=> 'hidden', 'size'=>'', 'morecss'=>'fieldrequired minwidth200', 'moreattr'=>'', 'name'=>'date_fin','value' => $date_fin);
					$array[] = array('label'=> 'Etat véhicule','type'=> 'hidden', 'size'=>'', 'morecss'=>'fieldrequired minwidth200', 'moreattr'=>'', 'name'=>'etat_vehicule','value' => $etat_vehicule);
					if($img)
						$array[] = array('label'=> 'Document','type'=> 'hidden', 'size'=>'', 'morecss'=>'fieldrequired minwidth200', 'moreattr'=>'', 'name'=>'equipement','value' => $img);

					$array[] = array('label'=> 'Equipement de secours','type'=> 'hidden', 'size'=>'', 'morecss'=>'fieldrequired minwidth200', 'moreattr'=>'', 'name'=>'equipement','value' => $equipement);
					
					if(!empty($note))
						$array[] = array('label'=> 'note','type'=> 'hidden', 'size'=>'', 'morecss'=>'fieldrequired minwidth200', 'moreattr'=>'', 'name'=>'note','value' => $note);

					$formconfirm = $monform->formconfirm(
						$url, 
						$titre, 
						$notice, 
						"new_assignation_ok", 
						$array, 
						'', 
						1,
						180,
						'45%'
					);
					print $formconfirm;
				}elseif($num == 0){ //Pour la première fois
					$sql_insert = 'INSERT INTO '.MAIN_DB_PREFIX.'assignation_vehicule (fk_vehicule, fk_user, date_debut, fk_user_creation, etat_vehicule, equipement, note';

					if(!empty($img))
						$sql_insert .= ', document';

					if(!empty($date_fin))
						$sql_insert .= ', date_fin';
					$sql_insert .=')';

					$sql_insert .= ' VALUES('.$fk_vehicule.', '.$fk_user.', "'.$date_debut.'", '.$user->id.',"'.$etat_vehicule.'","'.$equipement.'", "'.$note.'"';

					if(!empty($img))
						$sql_insert .= ', "'.$img.'"';

					if(!empty($date_fin))
						$sql_insert .= ', "'.$date_fin.'"';

					$sql_insert .=')';

					$result = $db->query($sql_insert);
					//print $sql_insert;
					if($result){

						$message = "Véhicule assigné avec succès";
						$action = "liste";
						//header('Location: ./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_assignation='.$id_nouveau_vehicule);
					}else{
						$action = "ajouter";
						$message = "Un problème est survenu";
					}
				}
			}else{
				$action = "ajouter";
			}
			print $db->error();
	}
		
	if($action == "ajouter"){
		//print load_fiche_titre($langs->trans("Assignation en cours"), '', '').img_picto('', 'assigner', 'class="paddingright pictofixedwidth"');
		print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i><br><br>';
		print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'" method="post" enctype="multipart/form-data">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<input type="hidden" name="action" value="add_assignation">';

		//Vehicule
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_vehicule">Véhicule</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_vehicule" name="fk_vehicule" class="minwidth200 height10">';
		$sql = "SELECT rowid, nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid = ".$id_vehicule;
		$res = $db->query($sql);
		if($res){
			$num = $db->num_rows($res);
			$a = 0;
			while ($obj_veh = $db->fetch_object($res)) {
				print '<option value="'.$obj_veh->rowid.'" selected>'.$obj_veh->nom.' ('.$obj_veh->reference_interne.')</option>';
			}

		}
		print '</select></td></tr>';

		//utilisateur
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_user">Utilisateur</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_user" name="fk_user" class="minwidth200 height10">';
		print '<option></option>';
		$sql = "SELECT rowid, firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid NOT IN (SELECT fk_user FROM ".MAIN_DB_PREFIX."assignation_vehicule)";
		$res = $db->query($sql);
		if($res){
			$num = $db->num_rows($res);
			$a = 0;
			while ($obj_user = $db->fetch_object($res)) {
				if(!empty(GETPOST("fk_user", 'int')) && $obj_user->rowid == GETPOST("fk_user"))
					print '<option value="'.$obj_user->rowid.'" selected>'.$obj_user->firstname.' '.$obj_user->lastname.'</option>';
				else
					print '<option value="'.$obj_user->rowid.'" >'.$obj_user->firstname.' '.$obj_user->lastname.'</option>';
				
			}

		}
		print '</select></td></tr>';

		//Etat du véhicule
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Etat du véhicule</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><textarea name="etat_vehicule" style="width:250px; height: 40px;">'.GETPOST("etat_vehicule").'</textarea></td></tr>';

		//Équipement de bord
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label for="fk_user">Équipement de bord</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px">';
		$array_id = array(-1);
		$array_nom = array("Aucun équipement");
		$id_selected_array = GETPOST('equipement')?:array();
		$sql = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."equipement_vehicule WHERE fk_type_vehicule = ".$obj_vehicule_mere->fk_type_vehicule;
		$res = $db->query($sql);
		if($res){
			$num = $db->num_rows($res);
			while ($obj_user = $db->fetch_object($res)) {
				$array_id[] = $obj_user->rowid;
				$array_nom[] = $obj_user->nom;		
			}

		}

		print $db->error();
		$alltype = array_combine($array_id, $array_nom);
		$monform = new Form($db);
		print $monform->multiselectarray('equipement', $alltype, $id_selected_array, null, 0, 'minwidth200 height10', 0, 0);

		//Date début
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date début</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_debut" value="'.GETPOST("date_debut").'" name="date_debut" ></td></tr>';

		//Date fin
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px"><label>Date fin</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_fin" value="'.GETPOST("date_fin").'" name="date_fin" ></td></tr>';

		//Document
		print "<tr><td style='width: 200px; padding-right: 30px; padding-bottom: 7px'>Document d'assignation</td>";
		print "<td style='width: 600px; padding-right: 30px; padding-bottom: 7px'><input type='file' name='document' id='document' ><3Mo</td></tr>";

		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" ><label>Note</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><textarea name="note" style="width:250px; height: 40px;">'.GETPOST("note").'</textarea></td></tr>';

		print '</table>';
		print '<hr>';
		print '
			<div style="text-align: center"; align-items: center; justify-content: center">
			<input class="button" type="submit" value="Créer">
			</form>
			<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'" class="button">Annuler</a></td></tr>
		</div>
		';
	}else{
			print $info;
				print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Ajouter une nouvelle assignation", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=gestionvehicule&id_vehicule='.$id_vehicule.'&action=ajouter' , '', 1), '', 0, 0, 0, 1);
				print "<div>";
				print "<h3 >Assignation active</h3>";
				print "<table class='tagtable liste' style='width:100%;'>";
				print "<tr class='liste_titre'><td style='padding: 10px;'>Utilisateur</td><td style='padding: 10px;'>Date debut</td><td style='padding: 10px;'>Date fin";
				print "</td><td style='padding: 10px;'>Etat véhicule</td><td style='padding: 10px;'>Equipement</td><td style='padding: 10px;'>document</td><td style='padding: 10px;'>Note</td><td style='padding: 10px;'>Statut</td></tr>";
				
				$actl[0] = img_picto("expiré", 'switch_off', 'class="size15x"');
				$actl[1] = img_picto("actif", 'switch_on', 'class="size15x"');

			//Partie affichage des assignations ------------------------------------------------------------------------------------------------------------------------------------------
				//assignation active
				$sql_vehicule = 'SELECT asv.*, u.rowid as urowid, u.firstname, u.lastname FROM '.MAIN_DB_PREFIX.'assignation_vehicule as asv';
				$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'user as u on asv.fk_user = u.rowid';
				$sql_vehicule .= ' WHERE fk_vehicule='.$id_vehicule.' ORDER BY asv.rowid DESC';

				//print $sql_vehicule;
				$result_vehicule = $db->query($sql_vehicule);
					
				if($result_vehicule){
					$num = $db->num_rows($result_vehicule);
					if($num > 0){
						$obj_assignation = $db->fetch_object($result_vehicule);

							$utilis = img_picto('','assigner', 'class="paddingright pictofixedwidth"').'<a href="../../user/card.php?mainmenu=gestionflotte&leftmenu=listevehicule&id='.$obj_assignation->urowid.'">'.$obj_assignation->firstname.' '.$obj_assignation->lastname.'</a>';

							$outils = "";
							if($obj_assignation->equipement != -1){
								$sql_outil = "SELECT nom FROM ".MAIN_DB_PREFIX."equipement_vehicule WHERE rowid IN (".$obj_assignation->equipement.")  ORDER BY nom";
								$res_outil = $db->query($sql_outil);
								if($res_outil)
									while($obj_outil = $db->fetch_object($res_outil)){
										$outils .= '<span style="background-color: #6c757d; color: white; padding: 3px 6px; border-radius: 3px; font-weight: bold;">
										<a style="color : white; text-decoration : none;" href="../creation_equipement.php?mainmenu=gestionflotte&leftmenu=equipement&recherche_nom='.$obj_outil->nom.'">'.$obj_outil->nom.'</a></span>&nbsp;'; // Gris

									}
							}else{
								$outils .= '<span style="background-color: #6c757d; color: white; padding: 3px 6px; border-radius: 3px; font-weight: bold;">Aucun équipement</span>&nbsp;'; // Gris

							}
							$document = "N/A";
						if($obj_assignation->document)
							$document = '<a target="_blank" href="./documents_assignations/'.$obj_assignation->document.'">'.$obj_assignation->document.'</a>';
						print "<tr><td style='padding: 10px;'>".$utilis."</td><td style='padding: 10px;'>".$obj_assignation->date_debut."</td><td style='padding: 10px;'>".($obj_assignation->date_fin?:"&#8734;");
						print "</td><td style='padding: 10px;'>".$obj_assignation->etat_vehicule."</td><td style='padding: 10px;'>".$outils."</td><td style='padding: 10px;'>".$document."</td><td style='padding: 10px;'>".$obj_assignation->note."</td><td style='padding: 10px;'>".$actl[1]."</td></tr>";				}
				}
				if($num <= 0)
					print '<tr><td align="center" colspan="8">Aucun enregistrement trouvé</td></tr>';
				print "</table></div>";


				//les assignations expirées
				print "<br><div>";
				print "<h3 >Anciennes assignations</h3>";
				print "<table class='tagtable liste' style='width:100%;'>";
				print "<tr class='liste_titre'><td style='padding: 10px;'>Utilisateur</td><td style='padding: 10px;'>Date debut</td><td style='padding: 10px;'>Date fin";
				print "</td><td style='padding: 10px;'>Etat véhicule</td><td style='padding: 10px;'>Equipement</td><td style='padding: 10px;'>document</td><td style='padding: 10px;'>Note</td><td style='padding: 10px;'>Statut</td></tr>";

				$sql_vehicule = 'SELECT asv.*, u.rowid as urowid, u.firstname, u.lastname FROM '.MAIN_DB_PREFIX.'assignation_vehicule as asv';
				$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'user as u on asv.fk_user = u.rowid';
				$sql_vehicule .= ' WHERE fk_vehicule='.$id_vehicule.' ORDER BY asv.rowid DESC';
					$result_vehicule = $db->query($sql_vehicule);
					
					if($result_vehicule){
					$num = $db->num_rows($result_vehicule);
					$obj_assignation = $db->fetch_object($result_vehicule);
					while ($obj_assignation = $db->fetch_object($result_vehicule)){
						$outils = "";
							if($obj_assignation->equipement != -1){
								$sql_outil = "SELECT nom FROM ".MAIN_DB_PREFIX."equipement_vehicule WHERE rowid IN (".$obj_assignation->equipement.")  ORDER BY nom";
								$res_outil = $db->query($sql_outil);
								if($res_outil)
									while($obj_outil = $db->fetch_object($res_outil)){
										$outils .= '<span style="background-color: #6c757d; color: white; padding: 3px 6px; border-radius: 3px; font-weight: bold;">'.$obj_outil->nom.'</span>&nbsp;'; // Gris

									}
							}else{
								$outils .= '<span style="background-color: #6c757d; color: white; padding: 3px 6px; border-radius: 3px; font-weight: bold;">Aucun équipement</span>&nbsp;'; // Gris

							}
						$utilis = '<a href="../../user/card.php?mainmenu=gestionflotte&leftmenu=listevehicule&id='.$obj_assignation->urowid.'">'.$obj_assignation->firstname.' '.$obj_assignation->lastname.'</a>';

						$document = "N/A";
						if($obj_assignation->document)
							$document = '<a target="_blank" href="./documents_assignations/'.$obj_assignation->document.'">'.$obj_assignation->document.'</a>';
						print "<tr><td style='padding: 10px;'>".$utilis."</td><td style='padding: 10px;'>".$obj_assignation->date_debut."</td><td style='padding: 10px;'>".($obj_assignation->date_fin?:"&#8734;");
						print "</td><td style='padding: 10px;'>".$obj_assignation->etat_vehicule."</td><td style='padding: 10px;'>".$outils."</td><td style='padding: 10px;'>".$document."</td><td style='padding: 10px;'>".$obj_assignation->note."</td><td style='padding: 10px;'>".$actl[0]."</td></tr>";
					}

				}
				if($num <= 1)
					print '<tr><td align="center" colspan="8">Aucun enregistrement trouvé</td></tr>';
				
				print "</table></div>";

		}
	}else print '<h2>Ce véhicule est déclaré en panne !</h2>';
}

//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}
