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
require_once DOL_DOCUMENT_ROOT.'/custom/gestionflotte/core/modules/modgestionflotte.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/gestionflotte/class/html.form.class.php';


$action = GETPOST('action', 'alpha')?:"liste";
$id_carburant = GETPOST('id_carburant', 'int');
$aujourdhui = date("Y-m-d");
$mass_action = GETPOST('mass_action', 'alpha');

$monform = new Form1($db);
$array_id = array(0);

$message = '';

if($action == "add_carburant"){
		$libelle = GETPOST('libelle', 'alpha');
		$fk_vehicule = GETPOST('fk_vehicule', 'int');
		$quantite = GETPOST('quantite');
		$date_demande = GETPOST('date_demande');
		$cout = GETPOST('cout');
		$kilometre = GETPOST('kilometre');

		if(empty($libelle))
			$message = 'Le champ "LIBELLE" est oblogatoire<br>';

		if(empty($fk_vehicule))
			$message .= 'Le champ "VEHICULE" est oblogatoire<br>';

		if(empty($cout))
			$message .= 'Le champ "COUT" est oblogatoire<br>';

		if(empty($date_demande))
			$message .= 'Le champ "DATE demande" est oblogatoire<br>';

		if(empty($kilometre))
			$message .= 'Le champ "KILOMETRE" est oblogatoire<br>';

        if(empty($message)){
			if(empty($quantite))
				$quantite = 0;
            $sql_insert = 'INSERT INTO '.MAIN_DB_PREFIX.'carburant_vehicule (libelle, fk_vehicule, quantite, cout, kilometre, date_demande, fk_user_creation)';
			$sql_insert .= 'VALUES("'.$libelle.'", '.$fk_vehicule.', '.$quantite.', '.$cout.', '.$kilometre.', "'.$date_demande.'", '.$user->id.')';
		    $result = $db->query($sql_insert);
			//print $sql_insert;
            if($result){
				$result = $db->query("SELECT LAST_INSERT_ID() as rowid;");
				$obj = $db->fetch_object($result);
				$id_carburant =  $obj->rowid;

                $message = "Demande enregistrée avec succès";
                $action = "detail";
				//header('Location: ./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$id_nouveau_vehicule);
            }else{
                $action = "creation";
                $message = "Un problème est survenu";
            }
        }else{
            $action = "creation";
        }
}

if($action == "save_modification"){
		$libelle = GETPOST('libelle', 'alpha');
		$fk_vehicule = GETPOST('fk_vehicule', 'int');
		$quantite = GETPOST('quantite');
		$date_demande = GETPOST('date_demande');
		$cout = GETPOST('cout');
		$kilometre = GETPOST('kilometre');

		if(empty($libelle))
			$message = 'Le champ "LIBELLE" est oblogatoire<br>';

		if(empty($fk_vehicule))
			$message .= 'Le champ "VEHICULE" est oblogatoire<br>';

		if(empty($cout))
			$message .= 'Le champ "COUT" est oblogatoire<br>';

		if(empty($date_demande))
			$message .= 'Le champ "DATE demande" est oblogatoire<br>';

		if(empty($kilometre))
			$message .= 'Le champ "KILOMETRE" est oblogatoire<br>';

        if(empty($message)){
			if(empty($quantite))
				$quantite = 0;
            $sql_update = 'UPDATE '.MAIN_DB_PREFIX.'carburant_vehicule SET libelle = "'.$libelle.'", fk_vehicule = '.$fk_vehicule.', quantite = '.$quantite;
			$sql_update .= ', cout = '.$cout.', kilometre = '.$kilometre.', date_demande = "'.$date_demande.'", fk_user_creation ='.$user->id;
		    $result = $db->query($sql_update);
			//print $sql_update;
            if($result){

                $message = "Demande modifiée avec succès";
                $action = "detail";
				//header('Location: ./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$id_nouveau_vehicule);
            }else{
                $action = "creation";
                $message = "Un problème est survenu";
            }
        }else{
            $action = "creation";
        }
		
}


//Suppression des lignes de demande
if($action == "activation" || $action == "desactivation"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=carburant&id_carburant=".$id_carburant;
	$suffixe = "l\'activation";
	$act = "activation_ok";
	if($action == "desactivation"){
		$suffixe = "la desactivation";
		$act = "desactivation_ok";
	}
    $titre = 'Veuillez confirmer '.$suffixe;
	$notice = "";
      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          $notice, 
          $act, 
          $array, 
          '', 
          1,
          200,
          '30%'
      );
      $action = "liste";
}

if($action == "activation_ok" || $action == "desactivation_ok"){ 

	$act = "activé";
	if($action == "activation_ok"){
		//suppression des affectations liées à cette Véhicule
		$sql_upd = "UPDATE ".MAIN_DB_PREFIX."vehicule SET actif=1 WHERE rowid=".$id_carburant;
		$result = $db->query($sql_upd);

	}elseif($action == "desactivation_ok"){
		$sql_upd = "UPDATE ".MAIN_DB_PREFIX."vehicule SET actif=0 WHERE rowid=".$id_carburant;
		$result = $db->query($sql_upd);
		$act = "desactivé";
	}
	

    //notification
    if($result)
        $message = 'Véhicule '.$act.' avec succès';
    else    $message = 'Un problème est survenu';

    $action = "liste";
}

//Soumission
if($action == "attente_soumission"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=carburant&id_carburant=".$id_carburant;
    $titre = 'Voulez-vous vraiment soumettre cette demande ?';

    $note = img_picto("", "warning")." Attention : La demande sera envoyée!";
      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          $note, 
          'attente_soumission_ok', 
          $array, 
          '', 
          1,
          180,
          '40%'
      );
      $action = "detail";
}


if($action == "attente_soumission_ok"){ 

    $sql = 'UPDATE '.MAIN_DB_PREFIX.'carburant_vehicule SET soumis = 1';
	$sql  .= ' WHERE rowid='.$id_carburant;
	$result = $db->query($sql);
    if($result){
        $message = 'Demande soumi avec succès.<br>';
    }else  $message = 'Un problème est survenu';

    $action = "detail";
}

//Annuler Soumission
if($action == "attente_annuler_soumission"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=carburant&id_carburant=".$id_carburant;
    $titre = 'Voulez-vous vraiment annuler la soumission ?';

    $note = img_picto("", "warning")." Attention : La demande sera renvoyée au créateur!";
      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          $note, 
          'attente_annuler_soumission_ok', 
          $array, 
          '', 
          1,
          180,
          '40%'
      );
      $action = "detail";
}


if($action == "attente_annuler_soumission_ok"){ 

    $sql = 'UPDATE '.MAIN_DB_PREFIX.'carburant_vehicule SET soumis = 0';
	$sql  .= ' WHERE rowid='.$id_carburant;
	$result = $db->query($sql);
    if($result){
        $message = 'Soumission annulée avec succès.<br>';
    }else  $message = 'Un problème est survenu';

    $action = "detail";
}


//Validation
if($action == "attente_valider"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=carburant&id_carburant=".$id_carburant;
    $titre = 'Voulez-vous vraiment valider cette demande ?';

    $note = "";
      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          $note, 
          'attente_valider_ok', 
          $array, 
          '', 
          1,
          150,
          '40%'
      );
      $action = "detail";
}


if($action == "attente_valider_ok"){ 

    $sql = 'UPDATE '.MAIN_DB_PREFIX.'carburant_vehicule SET valider = 1, fk_user_valider_rejeter='.$user->id;
	$sql  .= ' WHERE rowid='.$id_carburant;
	$result = $db->query($sql);
    if($result){
        $message = 'Demande valiée avec succès.<br>';
    }else  $message = 'Un problème est survenu';

    $action = "detail";
}


//Validation des lignes cochés
if($mass_action == "attente_valider_cocher"){

	$sql_last_modif = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule";
		$res_last_modif = $db->query($sql_last_modif);
		if($res_last_modif){
			while($obj_mixte = $db->fetch_object($res_last_modif)){
				$name = "vehicule".$obj_mixte->rowid;
				if(GETPOST($name) == 'on'){
					$array_id[] = $obj_mixte->rowid;
					$array[] = array('label'=>'','type'=> 'hidden', 'size'=>'', 'morecss'=>'fieldrequired minwidth200', 'moreattr'=>'checked', 'name'=>$name, 'value' => 'on');					//$result_insert = $db->query($sql_insert);
				}
			}
		}

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=carburant";
    $titre = 'Voulez-vous valider toutes les demandes cochées ?';

    $note = "";
      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          $note, 
          'attente_valider_cocher_ok', 
          $array, 
          '', 
          1,
          150,
          '40%'
      );
      $action = "liste";
}


if($action == "attente_valider_cocher_ok"){
	$trouve = true; 
		$sql_select = "SELECT rowid, soumis, valider, approuver FROM ".MAIN_DB_PREFIX."carburant_vehicule";
		$result = $db->query($sql_select);
		if($result){
			while($obj = $db->fetch_object($result)){
				$ind = 'vehicule'.$obj->rowid;
				if(GETPOST($ind) == 'on'){
					$array_id[] = $obj->rowid;
					if(!($obj->soumis && !$obj->valider))
						$trouve = false;
				}
			}
		}

	if(count($array_id) > 1 && $trouve){
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'carburant_vehicule SET valider = 1, fk_user_valider_rejeter='.$user->id;
		$sql  .= ' WHERE rowid IN ('.implode(',', $array_id).')';
		$result_up = $db->query($sql);
	}else{
		$message = 'Des demandes cochées ne sont peut-être pas soumises<br>';
		$message .= 'Des demandes cochées sont peut-être déjà validées<br>';
	}

    if($result_up){
		$array_id = array(0);
        $message = 'Demandes valiées avec succès.<br>';
    }elseif(empty($message))  
		$message = 'Un problème est survenu';

    $action = "liste";
}

//Rejection
if($action == "attente_rejeter"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=carburant&id_carburant=".$id_carburant;
    $titre = 'Voulez-vous vraiment rejeter cette demande ?';

    $note = img_picto("", "warning")." Attention : La demande sera pas traitée!";
      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          $note, 
          'attente_rejeter_ok', 
          $array, 
          '', 
          1,
          180,
          '40%'
      );
      $action = "detail";
}


if($action == "attente_rejeter_ok"){ 

    $sql = 'UPDATE '.MAIN_DB_PREFIX.'carburant_vehicule SET rejeter = 1, fk_user_valider_rejeter='.$user->id;
	$sql  .= ' WHERE rowid='.$id_carburant;
	$result = $db->query($sql);
    if($result){
        $message = 'Demande rejetée avec succès.<br>';
    }else  $message = 'Un problème est survenu';

    $action = "detail";
}

//reouverture
if($action == "attente_reouverture"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=carburant&id_carburant=".$id_carburant;
    $titre = 'Voulez-vous re-ouvrir cette demande ?';

    $note = img_picto("", "warning")." Attention : La demande sera de nouveau active!";
      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          $note, 
          'attente_reouverture_ok', 
          $array, 
          '', 
          1,
          180,
          '40%'
      );
      $action = "detail";
}


if($action == "attente_reouverture_ok"){ 

    $sql = 'UPDATE '.MAIN_DB_PREFIX.'carburant_vehicule SET rejeter = 0, fk_user_valider_rejeter='.$user->id;
	$sql  .= ' WHERE rowid='.$id_carburant;
	$result = $db->query($sql);
    if($result){
        $message = 'Demande ouverte avec succès.<br>';
    }else  $message = 'Un problème est survenu';

    $action = "detail";
}

//Suppression
if($action == "attente_suppression"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=carburant&id_carburant=".$id_carburant;
    $titre = 'Voulez-vous vraiment Supprimer cette demande ?';

    $note = img_picto("", "warning")." Attention : La demande sera supprimée définitivement !";
      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          $note, 
          'attente_suppression_ok', 
          $array, 
          '', 
          1,
          180,
          '40%'
      );
      $action = "detail";
}


if($action == "attente_suppression_ok"){ 

    $sql = 'DELETE FROM '.MAIN_DB_PREFIX.'carburant_vehicule';
	$sql  .= ' WHERE rowid='.$id_carburant;
	$result = $db->query($sql);
    if($result){
        $message = 'Demande supprimeée avec succès.<br>';
		$action = "liste";
    }else{  
		$message = 'Un problème est survenu';
		$action = 'detail';
	}

}

//approbation
if($action == "attente_approbation"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=carburant&id_carburant=".$id_carburant;
    $titre = 'Voulez-vous approuver cette demande ?';

    $note = img_picto("", "warning");
      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          $note, 
          'attente_approbation_ok', 
          $array, 
          '', 
          1,
          180,
          '40%'
      );
      $action = "detail";
}


if($action == "attente_approbation_ok"){ 

    $sql = 'UPDATE '.MAIN_DB_PREFIX.'carburant_vehicule SET approuver = 1, fk_user_approuver='.$user->id;
	$sql  .= ' WHERE rowid='.$id_carburant;
	$result = $db->query($sql);
    if($result){
        $message = 'Demande approuvée avec succès.<br>';

		$sql_select = "SELECT fk_vehicule, kilometre FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE rowid = ".$id_carburant;
		$result = $db->query($sql_select);
		if($result){
			if($obj = $db->fetch_object($result))

			$sql = 'UPDATE '.MAIN_DB_PREFIX.'vehicule SET kilometrage_calcule = kilometrage_calcule + '.$obj->kilometre;
			$sql  .= ' WHERE rowid='.$obj->fk_vehicule;
			$result = $db->query($sql);
		}

    }else  $message = 'Un problème est survenu';

    $action = "detail";
}


//approbation
if($mass_action == "attente_approuver_cocher"){

	$sql_last_modif = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule";
		$res_last_modif = $db->query($sql_last_modif);
		if($res_last_modif){
			while($obj_mixte = $db->fetch_object($res_last_modif)){
				$name = "vehicule".$obj_mixte->rowid;
				if(GETPOST($name) == 'on'){
					$array_id[] = $obj_mixte->rowid;
					$array[] = array('label'=>'','type'=> 'hidden', 'size'=>'', 'morecss'=>'fieldrequired minwidth200', 'moreattr'=>'checked', 'name'=>$name, 'value' => 'on');					//$result_insert = $db->query($sql_insert);
				}
			}
		}

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=carburant&id_carburant=".$id_carburant;
    $titre = 'Voulez-vous approuver toutes les demandes cochées?';

    $note = img_picto("", "warning");
      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          $note, 
          'attente_approuver_cocher_ok', 
          $array, 
          '', 
          1,
          180,
          '40%'
      );
      $action = "liste";
}


if($action == "attente_approuver_cocher_ok"){ 
    
	$trouve = true;
	$sql_select = "SELECT rowid, soumis, valider, approuver FROM ".MAIN_DB_PREFIX."carburant_vehicule";
		$result = $db->query($sql_select);
		if($result){
			while($obj = $db->fetch_object($result)){
				$ind = 'vehicule'.$obj->rowid;
				if(GETPOST($ind) == 'on'){
					$array_id[] = $obj->rowid;
					if(!($obj->soumis && $obj->valider && !$obj->approuver)){
						$trouve = false;
					}
				}
			}
		}

	if(count($array_id) > 1 && $trouve){
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'carburant_vehicule SET approuver = 1, fk_user_approuver='.$user->id;
		$sql  .= ' WHERE rowid IN ('.implode(',', $array_id).')';
		$result_up = $db->query($sql);
	}else{
		$message = 'Des demandes cochées ne sont peut-être pas soumises<br>';
		$message .= 'Des demandes cochées ne sont peut-être pas validées<br>';
		$message .= 'Des demandes cochées sont peut-être déjà approuvées<br>';
	}

    if($result_up){
		$array_id = array(0);
        $message = 'Demandes approuvées avec succès.<br>';
    }elseif(empty($message))  
		$message = 'Un problème est survenu';


    $action = "liste";
}

print $db->error();
llxHeader('', "Gestion de flotte");
print $formconfirm;

if($action == "creation"){
	print load_fiche_titre($langs->trans("Création d'une dépense"), '', '').img_picto('', 'carburant', 'class="paddingright pictofixedwidth"');
	print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i>';
	print "<hr>";
	print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add_carburant">';

	//proposition d'un libellé
	$sql = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid = ".$user->id;
    $res = $db->query($sql);
	$nom_prenom = "";
    if($res){
		$user_connecte  = $db->fetch_object($res);
		$nom_prenom = $user_connecte->firstname." ".$user_connecte->lastname;
	}

	$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule";
    $res = $db->query($sql);
	$num = 0;
    if($res){
		$num = $db->num_rows($res);
	}
	$num ++;
		$label = "Demande Carburant-".$nom_prenom."-".date('d-m-Y');
	//libelle
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Libelle</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="libelle" name="libelle" value="'.(GETPOST("libelle")?:$label).'"></td></tr>';

	//Vehicule
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_vehicule">Véhicule</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_vehicule" name="fk_vehicule">';
	print '<option></option>';
    //Vehicule
	$sql = "SELECT rowid, fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE fk_user = ".$user->id;
    $res = $db->query($sql);
    if($res){
		$obj_v  = $db->fetch_object($res);
		$id_v = $obj_v->fk_vehicule;
	}
    $sql = "SELECT rowid, nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE actif = 1 AND rowid IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule)";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($obj_vehicule = $db->fetch_object($res)) {
			if(!empty(GETPOST('fk_vehicule', 'int'))){
				if(GETPOST('fk_vehicule', 'int') == $obj_vehicule->rowid)
					print '<option value="'.$obj_vehicule->rowid.'" selected>'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
				else
					print '<option value="'.$obj_vehicule->rowid.'" >'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
			}else{
				if($id_v == $obj_vehicule->rowid)
					print '<option value="'.$obj_vehicule->rowid.'" selected>'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
				else
					print '<option value="'.$obj_vehicule->rowid.'" >'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
			}
		}

    }
	print '</select>';
	if(0 >= $num){
		print info_admin("Aucun véhicule assigné trouvé", 1);

	}
	print '</td></tr>';

	//quantite
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px"><label>Quantite (en litre)</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><input type="number" step="any" id="quantite" name="quantite" value="'.GETPOST("quantite").'"/></td></tr>';

	//Coût
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Coût</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="cout" name="cout" value="'.(GETPOST("cout")).'"></td></tr>';

	//Date demande
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date demande</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_demande" value="'.GETPOST("date_demande").'" name="date_demande" ></td></tr>';

	//Kilometrage
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>Kilomètre</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><input type="number" step="any" id="kilometre" name="kilometre" value="'.GETPOST("kilometre").'"/></td></tr>';

	print '</table>';
	print '<hr>';
	print '
		<div style="text-align: center"; align-items: center; justify-content: center">
        <input class="button" type="submit" value="Enregistrer">
        </form>
        <a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&action=liste" class="button">Annuler</a></td></tr>
    </div>
    ';
}

//--------------------------------------------
if($action == 'detail' || $action == 'modifier'){
    print load_fiche_titre($langs->trans("Demande de carburant"), '', '');
    print "<hr>";
    //-------------------------------
    $obj_soc = prepare_objet_entete_carburant($id_carburant, $db);
    entete_carburant($obj_soc, 'commande');
	
	$soc_sql = "SELECT * FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE rowid=".$id_carburant;
	$soc_res_carb_veh = $db->query($soc_sql);//= $db->query($covSql);
	if($soc_res_carb_veh)
		$obj_main_carburant = $db->fetch_object($soc_res_carb_veh);

	//Véhicule
	$soc_sql = "SELECT nom, reference_interne, fk_type_carburant FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$obj_main_carburant->fk_vehicule;
	$soc_res_veh = $db->query($soc_sql);//= $db->query($covSql);
	if($soc_res_veh)
		$obj_veh = $db->fetch_object($soc_res_veh);

	//type carburant
	$type_carb_sql = "SELECT nom FROM ".MAIN_DB_PREFIX."type_carburant WHERE rowid=".$obj_veh->fk_type_carburant;
	$type_carb_res_veh = $db->query($type_carb_sql);//= $db->query($covSql);
	if($type_carb_res_veh)
		$obj_type_carb = $db->fetch_object($type_carb_res_veh);

	//Assignateur
	$soc_sql = "SELECT u.firstname, u.lastname, av.rowid, av.fk_user FROM ".MAIN_DB_PREFIX."user as u";
	$soc_sql .= " LEFT JOIN ".MAIN_DB_PREFIX."assignation_vehicule as av on u.rowid = av.fk_user WHERE fk_vehicule=".$obj_main_carburant->fk_vehicule;
	$soc_sql .= " ORDER BY av.rowid DESC";
	$soc_res_user = $db->query($soc_sql);//= $db->query($covSql);
	if($soc_res_user)
		$obj_user_assign = $db->fetch_object($soc_res_user);

	$sql_user = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid = ".$obj_main_carburant->fk_user_creation;
	$res_user = $db->query($sql_user);
	if ($res_user) {
		$num_user = $db->num_rows($res_user);
		$xy = 0;
		if (0 < $num_user) {
			$obj_user = $db->fetch_object($res_user);
		}

	}
	if($action == 'modifier'){
		print "<hr>";
		print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$id_carburant.'" method="post" enctype="multipart/form-data">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<input type="hidden" name="action" value="save_modification">';

		//libelle
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Libelle</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="libelle" name="libelle" value="'.(GETPOST("libelle")?:$obj_main_carburant->libelle).'"></td></tr>';

		//Vehicule
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_vehicule">Véhicule</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_vehicule" name="fk_vehicule">';
		print '<option></option>';
		//Vehicule
		$sql = "SELECT rowid, fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE fk_user = ".$user->id;
		$res = $db->query($sql);
		if($res){
			$obj_v  = $db->fetch_object($res);
			$id_v = $obj_v->fk_vehicule;
		}
		$sql = "SELECT rowid, nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE actif = 1 AND rowid IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule)";
		$res = $db->query($sql);
		if($res){
			$num = $db->num_rows($res);
			$a = 0;
			while ($obj_vehicule = $db->fetch_object($res)) {
				if(!empty(GETPOST('fk_vehicule', 'int'))){
					if(GETPOST('fk_vehicule', 'int') == $obj_vehicule->rowid)
						print '<option value="'.$obj_vehicule->rowid.'" selected>'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
					else
						print '<option value="'.$obj_vehicule->rowid.'" >'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
				}else{
					if($obj_main_carburant->fk_vehicule == $obj_vehicule->rowid)
						print '<option value="'.$obj_vehicule->rowid.'" selected>'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
					else
						print '<option value="'.$obj_vehicule->rowid.'" >'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
				}
			}

		}
		print '</select>';
		print '</td></tr>';

		//quantite
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px"><label>Quantite (en litre)</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><input type="number" step="any" id="quantite" name="quantite" value="'.(GETPOST("quantite")?:$obj_main_carburant->quantite).'"/></td></tr>';

		//Coût
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Coût</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="cout" name="cout" value="'.(GETPOST("cout")?:$obj_main_carburant->cout).'"></td></tr>';

		//Date demande
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date demande</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_demande" value="'.(GETPOST("date_demande")?:$obj_main_carburant->date_demande).'" name="date_demande" ></td></tr>';

		//Kilometrage
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>Kilomètre</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><input type="number" step="any" id="kilometre" name="kilometre" value="'.(GETPOST("kilometre")?:$obj_main_carburant->kilometre).'"/></td></tr>';

		print '</table>';
		print '<hr>';
		print '
			<div style="text-align: center"; align-items: center; justify-content: center">
			<input class="button" type="submit" value="Enregistrer">
			</form>
			<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&action=liste" class="button">Annuler</a></td></tr>
		</div>
    ';
	}else{
		//affichage des informations
		print '<div class="fichecenter">';
			print '<div class="fichehalfleft">';
			print '<div class="underbanner clearboth"></div>';
			print '<table class="border tableforfield centpercent">';

			print '<tr style="border-bottom: 1px solid #ccc;">';
			print '<td>Type carburant</td>';
			print '<td>'.$obj_type_carb->nom.'</td>';
			print '</tr>';

			print '<tr style="border-bottom: 1px solid #ccc;">';
			print '<td>Cout</td>';
			print '<td>'.$obj_main_carburant->cout.'</td>';
			print '</tr>';

			print '<tr style="border-bottom: 1px solid #ccc;">';
			print '<td>Date demande</td>';
			print '<td>'.$obj_main_carburant->date_demande.'</td>';
			print "</tr>";

			print '</table>';
			print '</div>';
			//------
			print '<div class="fichehalfright">';
			print '<div class="underbanner clearboth"></div>';
			print '<table class="border tableforfield centpercent">';
			
			print '<tr style="border-bottom: 1px solid #ccc;">';
			print '<td>Kilomètre</td>';
			print '<td>'.$obj_main_carburant->kilometre.' km</td>';
			print '</tr>';

			print '<tr style="border-bottom: 1px solid #ccc;">';
			print '<td>Quantité</td>';
			print '<td>'.($obj_main_carburant->quantite?$obj_main_carburant->quantite. " litre(s)":"N/A").'</td>';
			print "</tr>";

			print '<tr style="border-bottom: 1px solid #ccc;">';
			print '<td>Date création</td>';
			print '<td>'.($obj_main_carburant->date_creation).'</td>';
			print "</tr>";

			

			print '</table>';
			print '</div>';
		//--------------------------------------------------------
			print '</div>';
			print '<div style="clear:both"></div><br>';

			print '</div>';
			print '<div style="clear:both"></div>';
			print '<div class="tabsAction">'."\n";

		if(empty($obj_main_carburant->soumis)){
			if($obj_user_assign->fk_user == $user->id){
				print '<a class="button" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_main_carburant->rowid.'&action=attente_soumission">Soumettre</a>';
				print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_main_carburant->rowid.'&action=attente_suppression_demande">Supprimer</a>';
				print '<a class="button" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_main_carburant->rowid.'&action=modifier">Modifier</a>';

			}else{
				print '<button class="butActionRefused" title="demande non soumis" >Pas encore soumis</button>';
				print '<button class="butActionRefused" >Supprimer</button>';
				print '<button class="butActionRefused" >Modifier</button>';
			}

		}else{
			if($user->hasRight("gestionflotte", "gestionvehicule", "write")){
				print '<a class="button" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_main_carburant->rowid.'&action=modifier">Modifier</a>';
				if(empty($obj_main_carburant->valider) && empty($obj_main_carburant->rejeter)){
					print '<a class="button" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_main_carburant->rowid.'&action=attente_annuler_soumission">Annuler Soumission</a>';
					print '<a class="button" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_main_carburant->rowid.'&action=attente_valider">Valider</a>';
					print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_main_carburant->rowid.'&action=attente_rejeter">Rejeter</a>';
				}

				if($obj_main_carburant->valider)
					print '<a class="button" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_main_carburant->rowid.'&action=attente_approbation">Approuver</a>';
				
				if($obj_main_carburant->rejeter)
					print '<a class="button" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_main_carburant->rowid.'&action=attente_reouverture">Re-ouvrir</a>';
			}else{
				print '<button class="butActionRefused" title="Vous n\'avez pas cette permission" >Valider</button>';
				print '<button class="butActionRefused" title="Vous n\'avez pas cette permission" >Rejeter</button>';

			}

			if($user->hasRight("gestionflotte", "gestionvehicule", "write"))
				print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=gestionflotte&id_carburant='.$id_carburant.'&action=attente_suppression">Supprimer</a>';
			else
				print '<button class="butActionRefused" title="Vous n\'avez pas cette permission" >Supprimer</button>';

			print '</div>';
			print '</div>';
		}
	}

}
//--------------------------------------------
$limit = GETPOST('limit','alpha')?:20;
$arret = GETPOST('arret','int')?:0;
$nb_page = GETPOST('nbpage','int')?:1;
$tri = GETPOST('tri','alpha');
$etat = GETPOST('etat','alpha');
				$recherche_vehicule = GETPOST("recherche_vehicule", "int");
				$recherche_type_carburant = GETPOST("recherche_type_carburant", "int");
				$recherche_libelle = GETPOST("recherche_libelle", "alpha");
				$recherche_date_demande1 = GETPOST("recherche_date_demande1");
				$recherche_date_demande2 = GETPOST("recherche_date_demande2");
				$recherche_cout1 = GETPOST("recherche_cout1", "alpha");
				$recherche_cout2 = GETPOST("recherche_cout2", "alpha");

				$recherche_kilometre1 = GETPOST("recherche_kilometre1", "alpha");
				$recherche_kilometre2 = GETPOST("recherche_kilometre2", "alpha");


if($action == "liste" || $action == "rechercher" || $action == "valider" || $action == "rejeter" || $action == "approuver" || $action == "soumis"){
	$obj_liste = array();
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau type de salarié", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&action=creation', '', 1), '', 0, 0, 0, 1);

		$sql_vehicule  = 'SELECT cv.*, vh.rowid as vhrowid, vh.nom as vhnom, vh.img, vh.reference_interne, vh.fk_type_carburant, tc.nom AS nom_type_carburant';
		$sql_vehicule .= ' FROM '.MAIN_DB_PREFIX.'carburant_vehicule AS cv';
		$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'vehicule AS vh ON cv.fk_vehicule = vh.rowid';
		$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'type_carburant AS tc ON vh.fk_type_carburant = tc.rowid';

		if($user->id == 1 || $user->hasRight("gestionflotte", "gestionvehicule", "read"))
			$sql_vehicule .= ' WHERE 1 = 1';
		else $sql_vehicule .= ' WHERE cv.fk_user_creation='.$user->id;

		if($action == "valider"){
			$sql_vehicule .= ' AND valider != 0 AND approuver = 0';
		}elseif($action == "rejeter"){
			$sql_vehicule .= ' AND rejeter != 0 AND approuver = 0';
		}elseif($action == "approuver"){
			$sql_vehicule .= ' AND approuver != 0';
		}elseif($action == "soumis"){
			$sql_vehicule .= ' valider = 0 AND rejeter = 0';
		}

		
		if($libelle){
			$sql_vehicule .= ' AND cv.libelle LIKE "%'.$libelle.'%"';
		}
		if($recherche_vehicule){
			$sql_vehicule .= ' AND vh.reference_interne LIKE "%'.$recherche_vehicule.'%"';
		}

		if($recherche_type_carburant){
			$sql_vehicule .= ' AND vh.fk_type_carburant = '.$recherche_type_carburant;
		}

		if($recherche_date_demande1){
			$sql_vehicule .= ' AND cv.date_demande >= "'.$recherche_date_demande1.'"';
		}

		if($recherche_date_demande2){
			$sql_vehicule .= ' AND cv.date_demande <= "'.$recherche_date_demande2.'"';
		}

		if($recherche_cout1){
			$sql_vehicule .= ' AND cv.cout >= "'.$recherche_cout1.'"';
		}

		if($recherche_cout2){
			$sql_vehicule .= ' AND cv.cout <= "'.$recherche_cout2.'"';
		}

		if($recherche_kilometre1){
			$sql_vehicule .= ' AND cv.kilometre >= "'.$recherche_kilometre1.'"';
		}

		if($recherche_kilometre2){
			$sql_vehicule .= ' AND cv.kilometre <= "'.$recherche_kilometre2.'"';
		}

	/*$active = "";
	$desactive = "";
	if($recherche_date_fin == "active"){
		$sql_vehicule .= ' AND actif = 1';
		$active = "selected";
	}elseif($recherche_date_fin == "inactive"){
		$sql_vehicule .= ' AND actif = 0';
		$desactive = "selected";
	}*/
	

		if($tri == 'reference')
			$sql_vehicule .= " ORDER BY vh.reference_interne";
		elseif($tri == 'libelle')
			$sql_vehicule .= " ORDER BY cv.libelle";
		elseif($tri == 'type_carburant')
			$sql_vehicule .= " ORDER BY tc.nom";
		elseif($tri == 'date_demande')
			$sql_vehicule .= " ORDER BY cv.date_demande";
		elseif($tri == 'cout')
			$sql_vehicule .= " ORDER BY cv.cout";
		elseif($tri == 'kilometre')
			$sql_vehicule .= " ORDER BY cv.kilometre";
		else $sql_vehicule .= " ORDER BY cv.date_creation DESC";
	
	$result_vehicule = $db->query($sql_vehicule);
	//print $sql_vehicule;
	$j = 0;
	if($result_vehicule){
		$num = $db->num_rows($result_demande);
		while ($j < $num){
			$obj_liste[] = $db->fetch_object($result_vehicule);
            $j ++;
		}
	}

		//Titre
		print load_fiche_titre($langs->trans("Liste des assignations de vehicule(".$num.")"), '', '');
    	print "<hr>";

		$num = count($obj_liste) == 0 ? 1 : count($obj_liste);
	$sel5 = "selected";
		$sel10 = "";
		$sel25 = "";
		$sel20 = "";
		$sel30 = "";
		$sel50 = "";
		$sel100 = "";
		$sel200 = "";
		$sel500 = "";
		$sel1000 = "";
		$seltout = "";
		if($limit == 5)
			$sel5 = "selected";
		elseif($limit == 10)
			$sel10 = "selected";
		elseif($limit == 15)
			$sel15 = "selected";
		elseif($limit == 20)
			$sel20 = "selected";
		elseif($limit == 30)
			$sel30 = "selected";
		elseif($limit == 50)
			$sel50 = "selected";
		elseif($limit == 100) 
			$sel100 = "selected";
		elseif($limit == 200)
			$sel200 = "selected";
		elseif($limit == 500)
			$sel500 = "selected";
		elseif($limit == 1000)
			$sel1000 = "selected";
		else $seltout = "selected";
		print "<div style='float:right; margin-right:20px;'>";

		//action valider - approuver
		// Bloc contenant les éléments à centrer
		print '<form name="ajouter" method="POST" action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<span id="liste_action" style="display: none;">';
		print '<select name="mass_action" id="mass_action" class="flat checkforaction" onChange=maFonctionBtn()>';
		print '<option value="">-- Sélectionnez une action --</option>';
		print '<option value="attente_valider_cocher">Valider</option>';
		print '<option value="attente_approuver_cocher">Approuver</option>';
		print '</select> ';
		print '<input type="submit" id="mon_submit" class="button small" style="padding: 2px;" name="bouton" value="Confirmer" disabled>';
		print '</span>';
		print '<input type="hidden" name="action" value="rechercher">';

		print"<select style='padding:10px' name='limit' id='limit' >";
				print "<option value='5' ".$sel5." ><b>5</b></option>
				<option value='10' ".$sel10."><b>10</b></option>
				<option value='15' ".$sel15."><b>15</b></option>
				<option value='20' ".$sel20."><b>20</b></option>
				<option value='30' ".$sel30."><b>30</b></option>
				<option value='50' ".$sel50."><b>50</b></option>
				<option value='100' ".$sel100."><b>100</b></option>
				<option value='200' ".$sel200."><b>200</b></option>
				<option value='500' ".$sel500."><b>500</b></option>
				<option value='1000' ".$sel1000."><b>1000</b></option>
				<option value='tout' ".$seltout."><b>tout</b></option>";
				
				print "</select>";
				if($limit == 'tout')
					$limit = $num;
				print "<mark><b>".(GETPOST("nbpage","int")?:1)."</b></mark>/<mark><b>".(((int)($num%$limit))==0?((int)($num/$limit)):((int)($num/$limit)+1))."</b></mark>";
				print '<script type="text/javascript">
				var convention = document.getElementById("limit");
				convention.addEventListener("change", function () {
					var limit = convention.value;
					window.location.href = "'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&limit="+limit+"&action=rechercher&recherche_vehicule='.$recherche_vehicule.'&recherche_user='.$recherche_user.'&recherche_date_debut1='.$recherche_date_debut1.'&recherche_cout1='.$recherche_cout1.'&recherche_cout2='.$recherche_cout2.'&recherche_kilometre1='.$recherche_kilometre1.'&recherche_kilometre2='.$recherche_kilometre2.'";
				},
				false,
				);
				</script>';
			print "</select>";
			print "</div><br><br>";
    
    $largeur = "12,5px";
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau demande", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&action=creation', '', 1), '', 0, 0, 0, 1);
    print '<table style="width : 100%" class="tagtable liste">';
	print '<tr class="liste_titre">';

	print '<td align="center" style="width: 10px;"></td>';

	//refererence véhicule 
	print '<td align="left" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_vehicule.'" name="recherche_vehicule" ></td>';


	print '<td align="center" style="padding: 5px; width: '.$largeur.';">';
	print '<select id="recherche_type_carburant" value="'.$recherche_type_carburant.'" name="recherche_type_carburant">';
	print '<option></option>';
	$sql = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."type_carburant";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($obj_vehicule = $db->fetch_object($res)) {
			if(!empty($recherche_type_carburant) && $obj_vehicule->rowid == $recherche_type_carburant)
				print '<option value="'.$obj_vehicule->rowid.'" selected>'.$obj_vehicule->nom.'</option>';
			else
				print '<option value="'.$obj_vehicule->rowid.'" >'.$obj_vehicule->nom.'</option>';
		}

    }
	print '</select></td>';
	
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="number" step="any" Placeholder="" value="'.$recherche_cout1.'" name="recherche_cout1" ><br>
	<input style="padding:5px; width: 100px;" type="number" step="any" Placeholder="" value="'.$recherche_cout2.'" name="recherche_cout2" ></td>';

	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="number" step="any" Placeholder="" value="'.$recherche_kilometre1.'" name="recherche_kilometre1" ><br>
	<input style="padding:5px; width: 100px;" type="number" step="any" Placeholder="" value="'.$recherche_kilometre2.'" name="recherche_kilometre2" ></td>';

	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="date" Placeholder="" value="'.$recherche_date_demande1.'" name="recherche_date_demande1" ><br>
	<input style="padding:5px; width: 100px;" type="date" Placeholder="" value="'.$recherche_date_demande2.'" name="recherche_date_demande2" ></td>';
    
	if($action == "valider")
		$valid = "selected";
	elseif($action == "rejeter")
		$rejeter = "selected";
	elseif($action == "approuver")
		$approuver = "selected";
	elseif($action == "soumis")
		$soumis = "selected";
	print '<td align="center" rowspan="2">';
	
	print '	<select style="padding: 5px; width: 120px;" name="action">';
    print '<option value="0"></option>';
	print '
		<option value="soumis" '.$soumis.'>Soumis</option>
		<option value="valider" '.$valid.'>Valider</option>
		<option value="rejeter" '.$rejeter.'>Rejeter</option>
		<option value="approuver" '.$approuver.'>Approuver</option></select><br>';

	print '<input type="submit" class="button" value="Rechercher" style="padding: 4px" >';
	print '<br>	<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant" class="button" style="padding: 4px" >Annuler</a>';
	print '</td></tr>';
	
 	print '<tr class="liste_titre">';
		print '<td align="center" style="width: 10px;"><input type="checkbox" id="tout_cocher2" name="tout_cocher2"></td>';
	print '<td align="left" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&tri=reference" >Ref. véhicule</a></label></td>';
	print '<td align="center" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&tri=type_carburant" >Type carb.</a></label></td>';
	print '<td align="center" style=" color: darkblue;"><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&tri=cout" >Cout</a></label></td>';
	print '<td align="center" style=" color: darkblue;"><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&tri=kilometre" >Kilométrage</a></label></td>';
	print '<td align="center" style=" color: darkblue;"><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&tri=date_demande" >Date demande</a></label></td>';

 	print '</tr>';

		$num = count($obj_liste);
		$i = $arret;
		while ($i < $num){
            print '<tr class="impair">';
			$name = "vehicule".$obj_liste[$i]->rowid;
			print '<td align="center" style="width: 10px;"><input type="checkbox" '.(in_array($obj_liste[$i]->rowid, $array_id)?"checked":"").' class="case_a_cocher_desaffectation" id="'.$name.'" name="'.$name.'" onchange="verifierCheckboxDesaffecter()"></td>';

			$sql_veh = "SELECT rowid, reference_interne, img FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid = ".$obj_liste[$i]->fk_vehicule;
			$res_veh = $db->query($sql_veh);
			if($res_veh)
				$obj_veh = $db->fetch_object($res_veh);

			if($obj_veh->img)
				$photo = img_picto($obj_liste[$i]->vhnom, 'image_vehicule/'.$obj_liste[$i]->img, 'style="width:30px; height:auto;"');
			else $photo = "";
			print '<td align="left">';

			print $photo.' <a title = "'.$obj_liste[$i]->libelle.'" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=carburant&id_carburant='.$obj_liste[$i]->rowid.'&action=detail">'.$obj_liste[$i]->reference_interne.'</a></td>';

			print '<td align="center">'.$obj_liste[$i]->nom_type_carburant.'</td>';
			print '<td align="center">'.price($obj_liste[$i]->cout).'</td>';
			print '<td align="center">'.$obj_liste[$i]->kilometre.' km</td>';
			print '<td align="center">'.$obj_liste[$i]->date_demande.'</td>';

			$colors = array(
			'#3f473eff',//soumises
			'#920000ff', //rejetées
			'#949906ff', //validées
			'#06612cff', //approuvées
			);
			//----------------------------
			$status = '<span style="color: white; background-color: #000000ff; padding: 3px 6px; border-radius: 10%; font-weight: bold;">Non soumis</span>'; 
			if($obj_liste[$i]->soumis == 1)
            	$status = '<span style="color: white; background-color: #3f473eff; padding: 3px 6px; border-radius: 10%; font-weight: bold;">Soumis</span>';
			
			if ($obj_liste[$i]->rejeter == 1) {
                $status = '<span style="color: white; background-color: #920000ff; padding: 3px 6px; border-radius: 10%; font-weight: bold;">Rejeter</span>'; 
            }

			if ($obj_liste[$i]->valider == 1 && $obj_liste[$i]->approuver != 1) {
                $status = '<span style="color: white; background-color: #949906ff; padding: 3px 6px; border-radius: 10%; font-weight: bold;">Valider</span>'; 
            }

			if ($obj_liste[$i]->approuver == 1) {
                $status = '<span style="color: white; background-color: #06612cff; padding: 3px 6px; border-radius: 10%; font-weight: bold;">Approuver</span>'; 
            }
			print '<td align="center">'.$status;
            print '</td>';
            print '</tr>';

			if($i!= 0 && (($i+1)%$limit) == 0){
				$arret = $i;
				$i = $num;
			}else
				$i ++;
		}

			if($num <= 0)
				print '<tr><td align="center" colspan="7">Aucun enregistrement trouvé</td></tr>';
	print '</form>';
	print '</table>';

	 //affichage du reste de la liste
	 print '<span style="float:right; margin-left: 20px;">';
        $nb = (((int)($num%$limit))==0?((int)($num/$limit)):((int)($num/$limit)+1));
	$page_link = "";
	if($num>$limit){
		if($nb_page!= 1)
			if($nb==0 && 1 < ($nb))
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=0&nbpage=1&action=recherche&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";
			else if(1 < ($nb+1))
			$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=0&nbpage=1&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";

		
		if($arret > $limit){

			
			if($nb_page-3>=0)
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".($limit*($nb_page-3))."&nbpage=".($nb_page-2)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>".($nb_page -2)."</b></a>&nbsp;&nbsp;";

			if($nb_page-2>=0)
						$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".($limit*($nb_page-2))."&nbpage=".($nb_page-1)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>".($nb_page-1)."</b></a>&nbsp;&nbsp;";
			
			
			if($nb_page-1>=0)
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".($limit*($nb_page-1))."&nbpage=".($nb_page)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>".($nb_page)."</b></a>&nbsp;&nbsp;";

		

			
				if(	(($nb_page+1) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".($limit*$nb_page)."&nbpage=".($nb_page+1)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 1)."</b></a>&nbsp;&nbsp;";

			
				if((($nb_page+2) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".($limit*($nb_page +1))."&nbpage=".($nb_page+2)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 2)."</b></a>&nbsp;&nbsp;";
					
				
				if((($nb_page+3) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".($limit*($nb_page+2))."&nbpage=".($nb_page+3)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 3)."</b></a>&nbsp;&nbsp;";

					


		}else{

			
				if( 1 <= ($nb))
					
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=0&nbpage=1&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>1</b></a>&nbsp;&nbsp;";
			
			
				if(2 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".$limit."&nbpage=2&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>2</b></a>&nbsp;&nbsp;";
			
			
				if(3 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".($limit*2)."&nbpage=3&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>3</b></a>&nbsp;&nbsp;";
				
				if(4 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".($limit*3)."&nbpage=4&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>4</b></a>&nbsp;&nbsp;";

				if(5 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".($limit*4)."&nbpage=5&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'><b>5</b></a>&nbsp;&nbsp;";



		}
		if($nb_page != ($nb)  )
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".($limit*($nb-1))."&nbpage=".($nb)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."recherche_date_debut2=".$recherche_date_debut2."&recherche_type_carburant=".$recherche_type_carburant."&recherche_cout1=".$recherche_cout1."&recherche_cout2=".$recherche_cout2."&recherche_kilometre1=".$recherche_kilometre1."&recherche_kilometre2=".$recherche_kilometre2."&tri=".$tri."' style='padding: 5px'>      <b>Fin</b></a>&nbsp;&nbsp;";

		
		/*if($limit == ($arret +1))
			$page_link .= "<a style='background-color: yellow; padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".(($arret +1) -$limit)."'>".((int)(($arret+1)/$limit))."</a>";
		else $page_link .= ($arret +1 - $limit)>?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".(($arret +1) -$limit)."' style='padding: 5px'><b>".((int)(($arret+1)/$limit))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*2 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*2)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*3 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".(($arret +1)*3 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*3)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= "<a style='padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=carburant&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."'><b>></b>&nbsp;&nbsp;</a>";
	*/}
	print $page_link.'</span>';
}
print $db->error();


print "<script>

				const tout_cocher2 = document.getElementById('tout_cocher2');
				const liste_action = document.getElementById('liste_action');
				const mass_action = document.getElementById('mass_action');
				const mon_submit = document.getElementById('mon_submit');


				//Desaffectation
				function verifierCheckboxDesaffecter() {

					const desaffect_cocher = document.querySelectorAll('input.case_a_cocher_desaffectation:checked').length;
					const total_desaffect = document.querySelectorAll('input.case_a_cocher_desaffectation').length;
					if (desaffect_cocher > 0) {

						liste_action.style.display = 'inline';
						//si ON selectionne tout 1 par 1 on coche automatiquement tout_cocher pour desaffectation
						if(total_desaffect == desaffect_cocher && total_desaffect > 1){
							tout_cocher2.checked = true;

						}else{
							tout_cocher2.checked = false;
						}
					} else {
						liste_action.style.display = 'none';
					}
				}
				
				tout_cocher2.addEventListener('change', function () {
					if(tout_cocher2.checked)
						liste_action.style.display = 'inline';
					else liste_action.style.display = 'none';
					const checkboxes = document.querySelectorAll('input.case_a_cocher_desaffectation');

						checkboxes.forEach(cb => {
							cb.checked = tout_cocher2.checked;
						});
					},
					false,
				);
				
				function maFonctionBtn(){
						if(mass_action.value == 'attente_valider_cocher' || mass_action.value == 'attente_approuver_cocher'){
							mon_submit.disabled = false;
						}else{
							mon_submit.disabled = true;
						}


				}
			</script>";
//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}
