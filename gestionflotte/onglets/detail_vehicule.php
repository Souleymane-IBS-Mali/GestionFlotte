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
$monform = new Form1($db);

//Suppression des lignes de besoin
if($action == "attente_suppression"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$id_vehicule;
    $titre = 'Voulez-vous supprimer ce Véhicule';

      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          img_picto('NB', 'warning')."Attention vous allez supprimé toutes les information qui y sont liées", 
          'suppression_ok', 
          $array, 
          '', 
          1,
          180,
          '35%'
      );
      $action = "detail";
}

if($action == "suppression_ok"){ 

    //suppression du véhicule
	$sql = "DELETE FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$id_vehicule;
    $result = $db->query($sql);

    if($result){
        $message = 'Véhicule supprimé avec succès';
        header('Location: ./../creation_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&action=liste');
    }else    $message = 'Un problème est survenu';
    
}

//Declaration en panne
if($action == "attente_declaration_panne"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$id_vehicule;
    $titre = 'Voulez-vous déclarer ce véhicule en panne ?';

      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          '', 
          'declaration_panne_ok', 
          $array, 
          '', 
          1,
          130,
          '35%'
      );
      $action = "detail";
}


if($action == "declaration_panne_ok"){ 

    //suppression du véhicule
	$sql = "UPDATE ".MAIN_DB_PREFIX."vehicule SET panne = 1 WHERE rowid=".$id_vehicule;
    $result = $db->query($sql);

    if($result){
        $message = 'Panne déclarée avec succès';
        //header('Location: ./../creation_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&action=liste');
    }else    $message = 'Un problème est survenu';
    
}


//Declaration en panne
if($action == "declarer_reparer"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$id_vehicule;
    $titre = 'Ce véhicule est-il réparé ?';

      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          '', 
          'declarer_reparer_ok', 
          $array, 
          '', 
          1,
          130,
          '35%'
      );
      $action = "detail";
}


if($action == "declarer_reparer_ok"){ 

    //suppression du véhicule
	$sql = "UPDATE ".MAIN_DB_PREFIX."vehicule SET panne = 0 WHERE rowid=".$id_vehicule;
    $result = $db->query($sql);

    if($result){
        $message = 'Véhicule déclaré avoir été réparé avec succès';
        //header('Location: ./../creation_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&action=liste');
    }else    $message = 'Un problème est survenu';
    
}


if($action == "save_modif_vehicule"){
	$nom_vehicule = GETPOST('nom', 'alpha');
		$identification = GETPOST('identification', 'alpha');
		$numero_chassis = GETPOST('numero_chassis', 'alpha');
		$code_immobilisation = GETPOST('code_immobilisation', 'alpha');
		$plaque = GETPOST('plaque', 'alpha');
		$fk_type_carburant = GETPOST('fk_type_carburant', 'int');        
		$note = GETPOST('note', 'alpha');
		$marque = GETPOST('marque', 'alpha');
        $modele = GETPOST('modele', 'alpha');
		$kilometrage = GETPOST('kilometrage', 'alpha');
		$kilometrage_calcule = GETPOST('kilometrage_calcule', 'alpha')?:'0';
        $type_vehicule = GETPOST('type', 'int');
		$date_acquisition = GETPOST('date_acquisition');
		$fk_stationnement = GETPOST('fk_stationnement', 'int');
		$img = "";

		if(empty($nom_vehicule))
			$message = 'Le champ "NOM" est oblogatoire<br>';

		if(empty($identification))
			$message = 'Le champ "IDENTIFICATION" est oblogatoire<br>';

		if(empty($numero_chassis))
			$message .= 'Le champ "N° CHASSIS" est oblogatoire<br>';

		if(empty($code_immobilisation))
			$message .= 'Le champ "CODE IMMOBILISATION" est oblogatoire<br>';

		if(empty($type_vehicule))
			$message .= 'Le champ "TYPE" est oblogatoire<br>';

		if(empty($plaque))
			$message .= 'Le champ "PLAQUE IMMATRICULATION" est oblogatoire<br>';

		if(empty($date_acquisition))
			$message .= 'Le champ "DATE ACQUISITION" est oblogatoire<br>';

		if(empty($marque))
			$message .= 'Le champ "MARQUE" est oblogatoire<br>';

		if(empty($modele))
			$message .= 'Le champ "MODELE" est oblogatoire<br>';
		
		if(empty($fk_type_carburant))
			$message .= 'Le champ "TYPE CARBURANT" est oblogatoire<br>';
		
		if(empty($kilometrage))
			$message .= 'Le champ "KILOMETRAGE" est oblogatoire<br>';

		if(empty($fk_stationnement))
			$message .= 'Le champ "LIEUR DE STATIONNEMENT" est obligatoire';

		//Chargement de l'image
			if (isset($_FILES['image_vehicule']) && $_FILES['image_vehicule']['error'] == 0 && empty($message)) {
				$nom = $_FILES['image_vehicule']['name'];
				$chemin = $_FILES['image_vehicule']['tmp_name'];
				$extension = strrchr($nom,".");
				$extension_autorisees = array('.JPG','.jpg','.png','.PNG','.jpeg','.JPEG','.pdf','.PDF');
				$extension = '.png';
				$destination = DOL_DOCUMENT_ROOT.'/theme/eldy/img/image_vehicule/'.$identification.$extension;
				$nomDossier = 'image_vehicule';
				// Vérifier si le dossier n'existe pas déjà
				if (!file_exists($nomDossier))
					if (mkdir($nomDossier, 0777, true)) {
						$img = $identification.$extension;
					}

				//$destination = DOL_DOCUMENT_ROOT.'/paiementsalaire/onglets/image_vehicule/contrat'.$fk_salarie.'__'.date('d_m_y_h_i_s').''.$extension;
				if(in_array($extension,$extension_autorisees)){
					if($_FILES['image_vehicule']['size']<=3000000){
						if(move_uploaded_file($chemin,$destination)){
							$img = $identification.$extension;
						}elseif(isset($_FILES['image_vehicule'])){
							$message .= '❌ Erreur lors de l\'enregistrement de l\'image.';
						}
					}
				}
			}


        if(empty($message)){
            $sql_update = 'UPDATE '.MAIN_DB_PREFIX.'vehicule SET nom="'.$nom_vehicule.'", reference_interne="'.$identification.'", numero_chassis="'.$numero_chassis.'"';
			$sql_update .= ', code_immobilisation="'.$code_immobilisation.'", fk_type_vehicule='.$type_vehicule.', plaque_immatriculation="'.$plaque.'"';
			$sql_update .= ', date_acquisition="'.$date_acquisition.'", marque="'.$marque.'", modele="'.$modele.'", fk_type_carburant='.$fk_type_carburant.', note="'.$note.'"';
			$sql_update .= ', fk_stationnement = '.$fk_stationnement.', kilometrage = "'.$kilometrage.'", kilometrage_calcule = "'.$kilometrage_calcule.'"';
			if($img)
				$sql_update .=  ', img = "'.$img.'"';

			$sql_update .= ' WHERE rowid='.$id_vehicule;
		    $result = $db->query($sql_update);
			//print $sql_update;
            if($result){
				//Modification de l'acquisiteur
                $message = "Véhicule modifié avec succès";
                $action = "liste";
            }else{
                $action = "modifier_vehicule";
                $message = "Un problème est survenu";
            }
        }else{
            $action = "modifier_vehicule";
        }
}

print $db->error();
llxHeader("", "Gestion Vehicule");
//Titre
//print load_fiche_titre($langs->trans("Information de base du vehicule"), '', '');

print $formconfirm;

if($id_vehicule){

	$head = vehicule_Head($id_vehicule);
	print dol_get_fiche_head($head, 'detail', "", -1, '');

	$obj_soc = prepare_objet_entete($id_vehicule, $db);
    entete_vehicule($obj_soc, 'commande');
	print '<hr>';

		avertissement($db, $id_vehicule);

if($action == "modifier_vehicule"){
	$sql_vehicule = "SELECT * FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$id_vehicule;
	$result_vehicule = $db->query($sql_vehicule);//= $db->query($covSql);

	if($result_vehicule && $id_vehicule){
		$obj_vehicule = $db->fetch_object($result_vehicule);

		print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i>';
		print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="save_modif_vehicule">';

	//nom du vehilé
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Nom du véhicule</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="nom" name="nom" value="'.(GETPOST("nom")?:$obj_vehicule->nom).'"></td></tr>';

	//Numero d'identification
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>N° d\'identification</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="identification" name="identification" value="'.(GETPOST("identification")?:$obj_vehicule->reference_interne).'"></td></tr>';

	//Numero de chassis
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>N° Châssis</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="numero_chassis" name="numero_chassis" value="'.(GETPOST("numero_chassis")?:$obj_vehicule->numero_chassis).'"></td></tr>';

	//Code immobilisation
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Code immobilisation</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="code_immobilisation" name="code_immobilisation" value="'.(GETPOST("code_immobilisation")?:$obj_vehicule->code_immobilisation).'"></td></tr>';

	//Type de vehicule
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired" ><label for="type">Type</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><select id="type" name="type" class="minwidth200 height10">';
	print '<option></option>';
    $sql = "SELECT * FROM ".MAIN_DB_PREFIX."type_vehicule";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		while ($obj_type_vehicule = $db->fetch_object($res)) {
			if(!empty(GETPOST("type", "int"))){
				if( $obj_type_vehicule->rowid == empty(GETPOST("type")))
					print '<option value="'.$obj_type_vehicule->rowid.'" selected>'.$obj_type_vehicule->nom.'</option>';
				else
					print '<option value="'.$obj_type_vehicule->rowid.'" >'.$obj_type_vehicule->nom.'</option>';
			}else{
				if(($obj_type_vehicule->rowid == $obj_vehicule->fk_type_vehicule))
					print '<option value="'.$obj_type_vehicule->rowid.'" selected>'.$obj_type_vehicule->nom.'</option>';
				else
					print '<option value="'.$obj_type_vehicule->rowid.'" >'.$obj_type_vehicule->nom.'</option>';
			}

    	}
	}
	print '</select></tr></td>';

	//Plaque immatriculation
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Plaque d\'immatriculation</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="plaque" name="plaque" value="'.(GETPOST("plaque")?:$obj_vehicule->plaque_immatriculation).'"></td></tr>';

	//Date d'acquisition
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date d\'acquisition</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_acquisition" value="'.(GETPOST("date_acquisition")?:$obj_vehicule->date_acquisition).'" name="date_acquisition" ></td></tr>';

	//Marque
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Marque</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="marque" name="marque" value="'.(GETPOST("marque")?:$obj_vehicule->marque).'"></td></tr>';

	//Modèle
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Modèle</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="modele" name="modele" value="'.(GETPOST("modele")?:$obj_vehicule->modele).'"></td></tr>';

	//Type carburant
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_type_carburant">Type carburant</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_type_carburant" name="fk_type_carburant" class="minwidth200 height10">';
	print '<option></option>';
    $sql = "SELECT * FROM ".MAIN_DB_PREFIX."type_carburant";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($obj_type_carburant = $db->fetch_object($res)) {
			if(!empty(GETPOST("fk_type_carburant", 'int'))){
				if($obj_type_carburant->rowid == GETPOST("fk_type_carburant"))
					print '<option value="'.$obj_type_carburant->rowid.'" selected>'.$obj_type_carburant->nom.'</option>';
				else
					print '<option value="'.$obj_type_carburant->rowid.'" >'.$obj_type_carburant->nom.'</option>';
			}elseif($obj_vehicule->fk_type_carburant){
				if($obj_type_carburant->rowid == $obj_vehicule->fk_type_carburant)
					print '<option value="'.$obj_type_carburant->rowid.'" selected>'.$obj_type_carburant->nom.'</option>';
				else
					print '<option value="'.$obj_type_carburant->rowid.'" >'.$obj_type_carburant->nom.'</option>';
			}
		}

    }
	print '</select></tr></td>';

	//kilometrage
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Kilométrages</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="kilometrage" name="kilometrage" value="'.(GETPOST("kilometrage")?:$obj_vehicule->kilometrage).'"></td></tr>';

	//Lieu de stationnement
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired" ><label for="fk_stationnement">Lie de stationnement</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><select id="fk_stationnement" name="fk_stationnement" class="minwidth200 height10">';
	print '<option></option>';
    $sql = "SELECT * FROM ".MAIN_DB_PREFIX."stationnement";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		while ($obj_type_vehicule = $db->fetch_object($res)) {
			if(!empty(GETPOST("fk_stationnement", "int"))){
				if( $obj_type_vehicule->rowid == empty(GETPOST("fk_stationnement")))
					print '<option value="'.$obj_type_vehicule->rowid.'" selected>'.$obj_type_vehicule->nom.'</option>';
				else
					print '<option value="'.$obj_type_vehicule->rowid.'" >'.$obj_type_vehicule->nom.'</option>';
			}else{
				if(($obj_type_vehicule->rowid == $obj_vehicule->fk_stationnement))
					print '<option value="'.$obj_type_vehicule->rowid.'" selected>'.$obj_type_vehicule->nom.'</option>';
				else
					print '<option value="'.$obj_type_vehicule->rowid.'" >'.$obj_type_vehicule->nom.'</option>';
			}

    	}
	}
	print '</select></tr></td>';

	//Kilometrage calcule
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px"><label>Kilométrages calculés</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="kilometrage_calcule" name="kilometrage_calcule" value="'.(GETPOST("kilometrage_calcule")?:($obj_vehicule->kilometrage + $obj_vehicule->kilometrage_calcule?:0)).'"></td></tr>';

	print "<tr><td style='width: 200px; padding-right: 30px; padding-bottom: 7px'>Image</td>";
	print "<td style='width: 600px; padding-right: 30px; padding-bottom: 7px'><input type='file' name='image_vehicule' id='image_vehicule' ><3Mo ".img_picto($obj_vehicule->nom, 'image_vehicule/'.$obj_vehicule->img, 'style="width:30px; height:auto;"')."</td></tr>";

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" ><label>Note</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><textarea name="note" style="width:200px; height: 60px;">'.(GETPOST("note")?:$obj_vehicule->note).'</textarea></td></tr>';
	print '</table>';
		print '<hr>';
		print '
			<div style="text-align: center"; align-items: center; justify-content: center">
			<input class="button" type="submit" value="Valider" />
			</form>
        <a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'" class="button">Annuler</a></td></tr>
		</div>';
	}
}else{

	//detail
	$vehicule = "SELECT * FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$id_vehicule;
		
	$result_vehicule = $db->query($vehicule);//= $db->query($covSql);

	if($result_vehicule)
		$obj_vehicule = $db->fetch_object($result_vehicule);

	//print '<hr>';
    	print '<div class="fichecenter">';
		print '<div class="fichehalfleft">';
		print '<div class="underbanner clearboth"></div>';
		print '<table class="border tableforfield centpercent">';
		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td>Nom du Véhicule</td>';
		print '<td>'.$obj_vehicule->nom.'</td>';
		print '</tr>';

		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td>Réference interne</td>';
		print '<td>'.$obj_vehicule->reference_interne.'</td>';
		print "</tr>";

		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td>Numéro de Chassis</td>';
		print '<td >'.$obj_vehicule->numero_chassis.'</td>';
		print "</tr>";

		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td>Code d\'immobilisation</td>';
		print '<td>'.$obj_vehicule->code_immobilisation.'</td>';
		print "</tr>";

		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td style="width: 200px;">Plaque d\'immatriculation</td>';
		print '<td>'.$obj_vehicule->plaque_immatriculation.'</td>';
		print "</tr>";

		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td>Type de Véhicule</td>';
        $sql_imp = "SELECT nom FROM ".MAIN_DB_PREFIX."type_vehicule WHERE rowid=".$obj_vehicule->fk_type_vehicule;
        $result_imp = $db->query($sql_imp);//= $db->query($sql_imp);
        $nom = "";
        if($result_imp){
            
                $obj_imp = $db->fetch_object($result_imp);
                if ($obj_imp)
                {
                    $nom = $obj_imp->nom;
               	} 
        }
		
		print '<td>';
        print $nom;
        print '</td>';
		print "</tr>";

        print '</table>';
		print '</div>';
        //------
        print '<div class="fichehalfright">';
		print '<div class="underbanner clearboth"></div>';
		print '<table class="border tableforfield centpercent">';
		//type carburant
		$type_carb_sql = "SELECT nom FROM ".MAIN_DB_PREFIX."type_carburant WHERE rowid=".$obj_vehicule->fk_type_carburant;
		$type_carb_res_veh = $db->query($type_carb_sql);//= $db->query($covSql);
		if($type_carb_res_veh)
			$obj_type_carb = $db->fetch_object($type_carb_res_veh);

		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td>Type carburant</td>';
		print '<td>'.$obj_type_carb->nom.'</td>';
		print "</tr>";

		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td>Date d\'acquisition</td>';
		print '<td>'.$obj_vehicule->date_acquisition.'</td>';
		print "</tr>";

		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td>Marque</td>';
		print '<td>'.($obj_vehicule->marque).'</td>';
		print "</tr>";

		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td>Modèle</td>';
		print '<td>'.($obj_vehicule->modele).'</td>';
		print "</tr>";

		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td>Utilisablabe</td>';
		print '<td>'.($obj_vehicule->immatriculation?"Oui":"Non").'</td>';
		print "</tr>";

		print '<tr style="border-bottom: 1px solid #ccc;">';
		print '<td>Kilométrage</td>';
		print '<td>'.($obj_vehicule->kilometrage + $obj_vehicule->kilometrage_calcule).' Km</td>';
		print "</tr>";

                print '</table>';
                print '</div>';
	//--------------------------------------------------------
		print '</div>';
		print '<div style="clear:both"></div><br>';

		print '</div>';
		print '<div style="clear:both"></div>';
		print '<div class="tabsAction">'."\n";

		if($user->hasRight("gestionflotte", "gestionvehicule", "write")){
			print '<a class="butAction" title="Modifier les informations du véhicule" href="'.$_SERVER["PHP_SELF"].'?id_vehicule='.$id_vehicule.'&action=modifier_vehicule">Modifier</a>';
			
			if($obj_vehicule->panne)
				print '<a class="button" title="Signaler avoir réparé" href="'.$_SERVER["PHP_SELF"].'?id_vehicule='.$id_vehicule.'&action=declarer_reparer">Déclarer avoir réparé</a>';
			else print '<a class="butActionDelete" title="Signaler la panne" href="'.$_SERVER["PHP_SELF"].'?id_vehicule='.$id_vehicule.'&action=attente_declaration_panne">Déclarer en panne</a>';

		}else{
			print '<button class="butActionRefused" title="Vous n\'avez pas cette permission" >Modifier</button>';

		}

		if($user->hasRight("gestionflotte", "gestionvehicule", "write"))
			print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'&action=attente_suppression">Supprimer</a>';
		else
			print '<button class="butActionRefused" title="Vous n\'avez pas cette permission" >Supprimer</button>';

		print '</div>';
		print '</div>';
	}
}

if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}