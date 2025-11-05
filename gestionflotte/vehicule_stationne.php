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
$id_vehicule = GETPOST('id_vehicule', 'int');
$type = GETPOST('type', 'int');
$niveau = GETPOST('niveau', 'int');
$tri = GETPOST('tri', 'alpha');
$monform = new Form1($db);

$message = '';

if($action == "add_vehicule"){
		$nom_vehicule = GETPOST('nom', 'alpha');
		$identification = GETPOST('identification', 'alpha');
		$numero_chassis = GETPOST('numero_chassis', 'alpha');
		$code_immobilisation = GETPOST('code_immobilisation', 'alpha');
		$plaque = GETPOST('plaque', 'alpha');
		$fk_type_carburant = GETPOST('fk_type_carburant', 'int');
        $note = GETPOST('note', 'alpha');
		$marque = GETPOST('marque', 'alpha');
        $modele = GETPOST('modele', 'alpha');
        $type_vehicule = GETPOST('type', 'int');
		$date_acquisition = GETPOST('date_acquisition');
		$kilometrage = GETPOST('kilometrage', 'alpha');
		$img = "";
		$fk_stationnement = GETPOST('fk_stationnement', 'int');

		if(empty($nom_vehicule))
			$message = 'Le champ "NOM" est oblogatoire<br>';

		if(empty($identification))
			$message = 'Le champ "REFERENCE INTERNE" est oblogatoire<br>';

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
				//création du dossier s'il n'existe pas
				$nomDossier = DOL_DOCUMENT_ROOT.'/theme/eldy/img/image_vehicule';
				if (!file_exists($nomDossier))
					if (mkdir($nomDossier, 0777, true)) {
						$img = $identification.$extension;
					}

				$nom = $_FILES['image_vehicule']['name'];
				$chemin = $_FILES['image_vehicule']['tmp_name'];
				$extension = strrchr($nom,".");
				$extension_autorisees = array('.JPG','.jpg','.png','.PNG','.jpeg','.JPEG','.pdf','.PDF');
				$destination = DOL_DOCUMENT_ROOT.'/theme/eldy/img/image_vehicule/'.$identification.$extension;

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
            $sql_insert = 'INSERT INTO '.MAIN_DB_PREFIX.'vehicule (nom, reference_interne, numero_chassis, code_immobilisation, fk_type_vehicule, plaque_immatriculation, marque, modele, date_acquisition, fk_type_carburant, kilometrage, note, fk_stationnement, fk_user_creation, img)';
			$sql_insert .= 'VALUES("'.$nom_vehicule.'", "'.$identification.'","'.$numero_chassis.'", "'.$code_immobilisation.'", '.$type.', "'.$plaque.'", "'.$marque.'", "'.$modele.'", "'.$date_acquisition.'", '.$fk_type_carburant.', "'.$kilometrage.'", "'.$note.'", '.$fk_stationnement.', '.$user->id.', "'.$img.'")';
		    $result = $db->query($sql_insert);
			//print $sql_insert;
            if($result){
				//récuperation de l'id du nouveau vehicule
					$result = $db->query("SELECT LAST_INSERT_ID() as rowid;");
					$obj = $db->fetch_object($result);
					$id_nouveau_vehicule =  $obj->rowid;
					//L'assignation du nouveau vehicule
					//print $user_id."******************** Assignation du véhicule à l'utilisateur";

                $message = "Véhicule ajouté avec succès";
                $action = "liste";
				header('Location: ./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_nouveau_vehicule);
            }else{
                $action = "creation";
                $message = "Un problème est survenu";
            }
        }else{
            $action = "creation";
        }
        print $db->error();
}

llxHeader('', "Gestion de flotte");
//Suppression des lignes de besoin
if($action == "activation" || $action == "desactivation"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$id_vehicule;
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
      print $formconfirm;
      $action = "liste";
}

if($action == "activation_ok" || $action == "desactivation_ok"){ 

	$act = "activé";
	if($action == "activation_ok"){
		//suppression des affectations liées à cette Véhicule
		$sql_upd = "UPDATE ".MAIN_DB_PREFIX."vehicule SET actif=1 WHERE rowid=".$id_vehicule;
		$result = $db->query($sql_upd);

	}elseif($action == "desactivation_ok"){
		$sql_upd = "UPDATE ".MAIN_DB_PREFIX."vehicule SET actif=0 WHERE rowid=".$id_vehicule;
		$result = $db->query($sql_upd);
		$act = "desactivé";
	}
	

    //notification
    if($result)
        $message = 'Véhicule '.$act.' avec succès';
    else    $message = 'Un problème est survenu';

    $action = "liste";
}


if($action == "creation"){
	print load_fiche_titre($langs->trans("Ajout d'un Véhicule"), '', '').img_picto('', 'vehicule', 'class="paddingright pictofixedwidth"');
	print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i>';
	print "<hr>";
	print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add_vehicule">';

	//nom du vehilé
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Nom du véhicule</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="nom" name="nom" value="'.GETPOST("nom").'"></td></tr>';

	//Numero d'identification
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>N° d\'identification</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="identification" name="identification" value="'.GETPOST("identification").'"></td></tr>';

	//Numero de chassis
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>N° Châssis</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="numero_chassis" name="numero_chassis" value="'.GETPOST("numero_chassis").'"></td></tr>';

	//Code immobilisation
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Code immobilisation</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="code_immobilisation" name="code_immobilisation" value="'.GETPOST("code_immobilisation").'"></td></tr>';

	//Type de vehicule
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="type">Type</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="type" name="type" class="minwidth200 height10">';
	print '<option></option>';
    $sql = "SELECT * FROM ".MAIN_DB_PREFIX."type_vehicule";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($a < $num) {
			$obj_type_vehicule = $db->fetch_object($res);
			if(!empty(GETPOST("type", 'int')) && $obj_type_vehicule->rowid == GETPOST("type"))
				print '<option value="'.$obj_type_vehicule->rowid.'" selected>'.$obj_type_vehicule->nom.'</option>';
			else
				print '<option value="'.$obj_type_vehicule->rowid.'" >'.$obj_type_vehicule->nom.'</option>';
			$a ++;
		}

    }
	print '</select></tr></td>';

	//Plaque immatriculation
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Plaque d\'mmatriculation</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="plaque" name="plaque" value="'.GETPOST("plaque").'"></td></tr>';

	//Date d'acquisition
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date d\'acquisition</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_acquisition" value="'.GETPOST("date_acquisition").'" name="date_acquisition" ></td></tr>';

	//Marque
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Marque</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="marque" name="marque" value="'.GETPOST("marque").'"></td></tr>';

	//Modèle
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Modèle</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="modele" name="modele" value="'.GETPOST("modele").'"></td></tr>';

	//Type carburant
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_type_carburant">Type carburant</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_type_carburant" name="fk_type_carburant" class="minwidth200 height10">';
	print '<option></option>';
    $sql = "SELECT * FROM ".MAIN_DB_PREFIX."type_carburant";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($a < $num) {
			$obj_type_carburant = $db->fetch_object($res);
			if(!empty(GETPOST("fk_type_carburant", 'int')) && $obj_type_carburant->rowid == GETPOST("fk_type_carburant"))
				print '<option value="'.$obj_type_carburant->rowid.'" selected>'.$obj_type_carburant->nom.'</option>';
			else
				print '<option value="'.$obj_type_carburant->rowid.'" >'.$obj_type_carburant->nom.'</option>';
			$a ++;
		}

    }
	print '</select></tr></td>';

	//Kilometrage
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Kilométrages actuels</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input type="text" id="kilometrage" name="kilometrage" value="'.(GETPOST("kilometrage")?:$obj_vehicule->kilometrage).'"></td></tr>';

	//Lieu de stationnement
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_stationnement">Lieu de stationnement</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_stationnement" name="fk_stationnement" class="minwidth200 height10">';
	print '<option></option>';
    $sql = "SELECT * FROM ".MAIN_DB_PREFIX."stationnement";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($a < $num) {
			$obj_type_carburant = $db->fetch_object($res);
			if(!empty(GETPOST("fk_stationnement", 'int')) && $obj_type_carburant->rowid == GETPOST("fk_stationnement"))
				print '<option value="'.$obj_type_carburant->rowid.'" selected>'.$obj_type_carburant->nom.'</option>';
			else
				print '<option value="'.$obj_type_carburant->rowid.'" >'.$obj_type_carburant->nom.'</option>';
			$a ++;
		}

    }
	print '</select></tr></td>';

	print "<tr><td style='width: 200px; padding-right: 30px; padding-bottom: 7px'>Image</td>";
	print "<td style='width: 600px; padding-right: 30px; padding-bottom: 7px'><input type='file' name='image_vehicule' id='image_vehicule' ><3Mo</td></tr>";

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" ><label>Note</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><textarea name="note" style="width:200px; height: 30px;">'.GETPOST("note").'</textarea></td></tr>';

	print '</table>';
	print '<hr>';
	print '
		<div style="text-align: center"; align-items: center; justify-content: center">
        <input class="button" type="submit" value="Créer">
        </form>
        <a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&action=liste" class="button">Annuler</a></td></tr>
    </div>
    ';
}

$limit = GETPOST('limit','alpha')?:20;
$arret = GETPOST('arret','int')?:0;
$nb_page = GETPOST('nbpage','int')?:1;
				$etat = GETPOST("etat", "alpha");
			if($action == "rechercher"){
				$recherche_reference = GETPOST("recherche_reference", "alpha");
				$recherche_nom = GETPOST("recherche_nom", "alpha");
				$recherche_numero_chassis = GETPOST("recherche_numero_chassis", "alpha");
				$recherche_code_immobilisation = GETPOST("recherche_code_immobilisation", "alpha");
				$recherche_plaque_immatriculation = GETPOST("recherche_plaque_immatriculation", "alpha");
				$recherche_type = GETPOST("recherche_type", "int");
				$recherche_numero_chassis = GETPOST("recherche_numero_chassis", "alpha");
				$recherche_fk_stationnement = GETPOST("recherche_fk_stationnement", "alpha");
			}

if($action == "liste" || $action == "rechercher"){
	$obj_liste = array();
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau type de salarié", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&action=creation', '', 1), '', 0, 0, 0, 1);

	$sql_vehicule = 'SELECT vh.*, tv.nom as tvnom, s.nom as stnom FROM '.MAIN_DB_PREFIX.'vehicule as vh';
	$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'type_vehicule as tv on vh.fk_type_vehicule = tv.rowid';
	$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'document_vehicule as dv on vh.rowid = dv.fk_vehicule';
	$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'stationnement as s on vh.fk_stationnement = s.rowid';

	if($user->id == 1 || $user->hasRight("gestionflotte", "entites", "read"))
		$sql_vehicule .= ' WHERE 1 = 1';
	else $sql_vehicule .= ' WHERE fk_user_creation='.$user->id;

	if($recherche_fk_stationnement){
		$sql_vehicule .= ' AND vh.fk_stationnement = '.$recherche_fk_stationnement;
	}

	if($recherche_nom){
		$sql_vehicule .= ' AND nom LIKE "%'.$recherche_nom.'%"';
	}

	if($recherche_type){
		$sql_vehicule .= ' AND fk_type_vehicule = '.$recherche_type;
	}

	if($recherche_reference){
		$sql_vehicule .= ' AND reference_interne LIKE "%'.$recherche_reference.'%"';
	}

	if($recherche_numero_chassis){
		$sql_vehicule .= ' AND numero_chassis LIKE "%'.$recherche_numero_chassis.'%"';
	}

	if($recherche_code_immobilisation){
		$sql_vehicule .= ' AND code_immobilisation LIKE "%'.$recherche_code_immobilisation.'%"';
	}

	if($recherche_plaque_immatriculation){
		$sql_vehicule .= ' AND plaque_immatriculation LIKE "%'.$recherche_plaque_immatriculation.'%"';
	}


	if($etat){
		if($etat == "active")
			$sql_vehicule .= ' AND actif = 1';
		elseif($etat == "inactive") 
			$sql_vehicule .= ' AND actif = 0';
		elseif($etat == "bonetat") 
			$sql_vehicule .= ' AND panne = 0';
		else $sql_vehicule .= ' AND panne = 1';
	}
	$active = "";
	$desactive = "";
	

	if($tri){
		if($tri == "nom")
			$sql_vehicule .= " ORDER BY vh.nom";
		elseif($tri == "reference")
			$sql_vehicule .= " ORDER BY vh.reference_interne";
		elseif($tri == "chassis")
			$sql_vehicule .= " ORDER BY vh.numero_chassis";
		elseif($tri == "code")
			$sql_vehicule .= " ORDER BY vh.code_immobilisation";
		elseif($tri == "type")
			$sql_vehicule .= " ORDER BY tv.nom";
		elseif($tri == "plaque")
			$sql_vehicule .= " ORDER BY vh.plaque_immatriculation";
		elseif($tri == "stationnement")
			$sql_vehicule .= " ORDER BY s.nom";
		elseif($tri == "desc")
			$sql_vehicule .= " ORDER BY note";
	}else $sql_vehicule .= " ORDER BY date_creation";
	$result_entite = $db->query($sql_vehicule);
	//print $sql_vehicule;

	$j = 0;
	$num = 0;
	if($result_entite){
		$num = $db->num_rows($result_besoin);
		while ($j < $num){
			$obj_liste[] = $db->fetch_object($result_entite);
            $j ++;
		}
	}
		//Titre
		print load_fiche_titre($langs->trans("Liste des véhicules(".$num.")"), '', '');
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
		print '<form name="ajouter" method="POST" action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
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
					window.location.href = "'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&limit="+limit+"&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom='.$recherche_nom.'&recherche_type='.$recherche_type.'&recherche_numero_chassis='.$recherche_numero_chassis.'&recherche_code_immobilisation='.$recherche_code_immobilisation.'";
				},
				false,
				);
				</script>';
			print "</select>";
			print "</div><br><br>";
    
    $largeur = "12,5px";
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau Besoin", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&action=creation', '', 1), '', 0, 0, 0, 1);
    print '<table style="width : 100%" class="tagtable liste">';
	print '<tr class="liste_titre">';
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_reference.'" name="recherche_reference" ></td>';
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_nom.'" name="recherche_nom" ></td>';
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_numero_chassis.'" name="recherche_numero_chassis" ></td>';
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_code_immobilisation.'" name="recherche_code_immobilisation" ></td>';
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_plaque_immatriculation.'" name="recherche_plaque_immatriculation" ></td>';
		
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<select style="padding: 5px; width: 120px;" name="recherche_type" >';
    print '<option value="0"></option>';

    $sql_type = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."type_vehicule ORDER BY date_creation";
    $res_type = $db->query($sql_type);
    if($res_type){
        $nb = $db->num_rows($res_type);
        $a = 0;
        while ($obj_type = $db->fetch_object($res_type)) {
            if($recherche_type == $obj_type->rowid)
                print '<option value="'.$obj_type->rowid.'" selected>'.$obj_type->nom.'</option>';
            else 
                print '<option value="'.$obj_type->rowid.'">'.$obj_type->nom.'</option>';

        }

    }
	print '</select></td>';

	print '<td align="center" style="padding: 5px; width: '.$largeur.';"><select id="recherche_fk_stationnement" name="recherche_fk_stationnement" style="width: 120px;">';
	print '<option></option>';
    $sql = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."stationnement";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($obj_type_document = $db->fetch_object($res)) {
			if(!empty(GETPOST('recherche_fk_stationnement', 'int'))){
				if($obj_type_document->rowid == GETPOST('recherche_fk_stationnement', 'int'))
					print '<option value="'.$obj_type_document->rowid.'" selected>'.$obj_type_document->nom.'</option>';
				else
					print '<option value="'.$obj_type_document->rowid.'">'.$obj_type_document->nom.'</option>';
			}else
				print '<option value="'.$obj_type_document->rowid.'">'.$obj_type_document->nom.'</option>';
		}

    }
	print '</select></td>';

	if($etat == "active")
		$active = "selected";
	elseif($etat == "inactive")
		$desactive = "selected";
	elseif($etat == "bonetat")
		$bonetat = "selected";
	elseif($etat == "panne")
		$panne = "selected";
	elseif($etat == "stationne")
		$stationne = "selected";

	print '<td align="center" rowspan="2">';
	print '	<select style="padding: 5px; width: 120px;" name="etat">';
    print '<option value="0"></option>';
	print '<option value="active" '.$active.'>Active</option>';
	print '<option value="inactive" '.$desactive.'>Inactive</option>
	<option value="bonetat" '.$bonetat.'>Bon etat</option>
	<option value="panne" '.$panne.'>Panne</option>
	<option value="stationne" '.$stationne.'>Stationne</option></select><br>';
	print '<input type="submit" class="button" value="Rechercher" style="padding: 4px" >';
	print "</form>";
	print '<br>	<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule" class="button" style="padding: 4px" >Annuler</a>';
	print '</td></tr>';

 print '<tr class="liste_titre">';
  print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=reference" title="Trié par la reference interne" >Ref.</a></label></td>';
 print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=nom" title="Trié par nom">Nom</a></label></td>';
 print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=chassis" title="Trié par numéro de chassis">N° chassis</a></label></td>';
 print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=code" title="Trié par code immatriculation">Code Immob.</a></label></td>';
 print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=plaque" title="Trié par plaque immatriculation">Plaque immat</a></label></td>';
 print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=type" title="Trié par nom de type véhicule">Type</a></label></td>';
  print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=stationnement" title="Trié par le lieu de stationnement">Lieu station.</a></label></td>';

 print '</tr>';
	$acts[0] = "activation";
	$acts[1] = "desactivation";
	$actl[0] = img_picto($langs->trans("Disabled"), 'switch_off', 'class="size30x"');
	$actl[1] = img_picto($langs->trans("Activated"), 'switch_on', 'class="size30x"');

		$num = count($obj_liste);
		$i = $arret;
		while ($i < $num){
                print '<tr class="impair">';
				print '<td style="color: darkblue;" align="left">';
				if($obj_liste[$i]->img)
						$photo = img_picto($obj_liste[$i]->nom, 'image_vehicule/'.$obj_liste[$i]->img, 'style="width:30px; height:auto;"');
				else $photo = ""; //img_picto('No photo', 'vehicule', 'style="width:25px; height:auto;"');

				if($obj_liste[$i]->actif == 0)
            		print img_picto('Non utilisable', 'statut7_red').' <a href="./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$obj_liste[$i]->rowid.'&action=detail">'.$obj_liste[$i]->reference_interne.'</a></td>';
                else{
					print $photo.' <a href="./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$obj_liste[$i]->rowid.'&action=detail">'.$obj_liste[$i]->reference_interne.'</a></td>';
				}


				print '<td align="center">'.$obj_liste[$i]->nom.'</td>';
				print '<td align="center">'.$obj_liste[$i]->numero_chassis.'</td>';
				print '<td align="center">'.$obj_liste[$i]->code_immobilisation.'</td>';
				print '<td align="center">'.$obj_liste[$i]->plaque_immatriculation.'</td>';

				print '<td align="center">'.$obj_liste[$i]->tvnom.'</td>';

				//les document actif du véhicule

				print '<td align="center">'.($obj_liste[$i]->stnom?:"N/A").'</td>';

				print '<td align="center">';
				if($user->hasRight("gestionflotte", "gestionvehicule", "write")){
					if($obj_liste[$i]->actif == 1){
						print '<a class="reposition editfielda" title="Modifier l\'Véhicule" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$obj_liste[$i]->rowid.'&action=modifier_vehicule">'.img_edit('Modifier','').'</a>';
						print '&nbsp;&nbsp;<a class="reposition editfielda" title="Activer l\'Véhicule" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$obj_liste[$i]->rowid.'&action='.$acts[$obj_liste[$i]->actif].'">'.$actl[$obj_liste[$i]->actif].'</a>';
					}else{
						print '<a class="reposition editfielda" title="Désactiver l\'Véhicule" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$obj_liste[$i]->rowid.'&action='.$acts[$obj_liste[$i]->actif].'">'.$actl[$obj_liste[$i]->actif].'</a>';
					}
				}else{
					print img_picto("Vous n\'avez pas le droit","warning");
				}
                print '</td>';
                print '</tr>';

				if($i!= 0 && (($i+1)%$limit) == 0){
					$arret = $i;
					$i = $num;
				}else
					$i ++;
			}

			if($num <= 0)
				print '<tr><td align="center" colspan="8">Aucun Véhicule disponible</td></tr>';
			
	print '</table>';

	 //affichage du reste de la liste
	 print '<span style="float:right; margin-left: 20px;">';
        $nb = (((int)($num%$limit))==0?((int)($num/$limit)):((int)($num/$limit)+1));
	$page_link = "";
	if($num>$limit){
		if($nb_page!= 1)
			if($nb==0 && 1 < ($nb))
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=0&nbpage=1&action=recherche&recherche_reference=".$recherche_reference."&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";
			else if(1 < ($nb+1))
			$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=0&nbpage=1&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";

		
		if($arret > $limit){

			
			if($nb_page-3>=0)
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb_page-3))."&nbpage=".($nb_page-2)."&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>".($nb_page -2)."</b></a>&nbsp;&nbsp;";

			if($nb_page-2>=0)
						$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb_page-2))."&nbpage=".($nb_page-1)."&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>".($nb_page-1)."</b></a>&nbsp;&nbsp;";
			
			
			if($nb_page-1>=0)
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb_page-1))."&nbpage=".($nb_page)."&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>".($nb_page)."</b></a>&nbsp;&nbsp;";

		

			
				if(	(($nb_page+1) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*$nb_page)."&nbpage=".($nb_page+1)."&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 1)."</b></a>&nbsp;&nbsp;";

			
				if((($nb_page+2) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb_page +1))."&nbpage=".($nb_page+2)."&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 2)."</b></a>&nbsp;&nbsp;";
					
				
				if((($nb_page+3) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb_page+2))."&nbpage=".($nb_page+3)."&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 3)."</b></a>&nbsp;&nbsp;";

					


		}else{

			
				if( 1 <= ($nb))
					
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=0&nbpage=1&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>1</b></a>&nbsp;&nbsp;";
			
			
				if(2 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".$limit."&nbpage=2&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>2</b></a>&nbsp;&nbsp;";
			
			
				if(3 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*2)."&nbpage=3&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>3</b></a>&nbsp;&nbsp;";
				
				if(4 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*3)."&nbpage=4&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>4</b></a>&nbsp;&nbsp;";

				if(5 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*4)."&nbpage=5&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'><b>5</b></a>&nbsp;&nbsp;";



		}
		if($nb_page != ($nb)  )
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb-1))."&nbpage=".($nb)."&action=recherche&recherche_reference=".$recherche_reference."&action=recherche&recherche_reference=".$recherche_reference."r&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_numero_chassis=".$recherche_numero_chassis."&recherche_code_immobilisation=".$recherche_code_immobilisation."&recherche_plaque_immatriculation=".$recherche_plaque_immatriculation."&recherche_fk_stationnement=".$recherche_fk_stationnement."&etat=".$etat."&tri=".$tri."' style='padding: 5px'>      <b>Fin</b></a>&nbsp;&nbsp;";

		
		/*if($limit == ($arret +1))
			$page_link .= "<a style='background-color: yellow; padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".(($arret +1) -$limit)."'>".((int)(($arret+1)/$limit))."</a>";
		else $page_link .= ($arret +1 - $limit)>?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".(($arret +1) -$limit)."' style='padding: 5px'><b>".((int)(($arret+1)/$limit))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*2 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*2)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*3 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".(($arret +1)*3 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*3)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= "<a style='padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."'><b>></b>&nbsp;&nbsp;</a>";
	*/}
	print $page_link.'</span>';
}

//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}
