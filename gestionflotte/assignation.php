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
$id_assignation = GETPOST('id_assignation', 'int');
$aujourdhui = date("Y-m-d");

$monform = new Form1($db);

$message = '';

if($action == "add_assignation"){
		$fk_vehicule = GETPOST('fk_vehicule', 'int');
		$fk_user = GETPOST('fk_user', 'int');
        $note = GETPOST('note', 'alpha');
		$date_debut = GETPOST('date_debut');
		$date_fin = GETPOST('date_fin');
		$img = "";

		if(empty($fk_vehicule))
			$message = 'Le champ "VEHICULE" est oblogatoire<br>';

		if(empty($fk_user))
			$message = 'Le champ "Utilisateur" est oblogatoire<br>';

		if(empty($date_debut))
			$message .= 'Le champ "DATE DEBUT" est oblogatoire<br>';

		if(empty($date_fin))
			$message .= 'Le champ "DATE FIN" est oblogatoire<br>';

		if(!empty($date_debut) && !empty($date_fin))
			if($date_debut >= $date_fin)
				$message .= 'La "DATE DEBUT" doit être supérieure à la "DATE FIN"<br>';

        if(empty($message)){
            $sql_insert = 'INSERT INTO '.MAIN_DB_PREFIX.'assignation_vehicule (fk_vehicule, fk_user, date_debut, date_fin, fk_user_creation, note)';
			$sql_insert .= 'VALUES('.$fk_vehicule.', '.$fk_user.', "'.$date_debut.'", "'.$date_fin.'", '.$user->id.', "'.$note.'")';
		    $result = $db->query($sql_insert);
			//print $sql_insert;
            if($result){

                $message = "Véhicule assigné avec succès";
                $action = "liste";
				//header('Location: ./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=historique&id_assignation='.$id_nouveau_vehicule);
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

if($action == "save_modif_assignation"){
		$fk_vehicule = GETPOST('fk_vehicule', 'int');
		$fk_user = GETPOST('fk_user', 'int');
        $note = GETPOST('note', 'alpha');
		$date_debut = GETPOST('date_debut');
		$date_fin = GETPOST('date_fin');
		$img = "";

		if(empty($fk_vehicule))
			$message = 'Le champ "VEHICULE" est oblogatoire<br>';

		if(empty($fk_user))
			$message = 'Le champ "Utilisateur" est oblogatoire<br>';

		if(empty($date_debut))
			$message .= 'Le champ "DATE DEBUT" est oblogatoire<br>';

		if(empty($date_fin))
			$message .= 'Le champ "DATE FIN" est oblogatoire<br>';
		
		if(!empty($date_debut) && !empty($date_fin))
			if($date_debut >= $date_fin)
				$message .= 'La "DATE DEBUT" doit être supérieure à la "DATE FIN"<br>';

        if(empty($message)){
            $sql_update = 'UPDATE '.MAIN_DB_PREFIX.'assignation_vehicule SET fk_vehicule = '.$fk_vehicule.', fk_user = '.$fk_user.', date_debut = "'.$date_debut.'", date_fin = "'.$date_fin.'"';
			$sql_update .= ', note = "'.$note.'" WHERE rowid='.$id_assignation;
		    $result = $db->query($sql_update);
			//print $sql_update;
            if($result){
				//Modification de l'acquisiteur
                $message = "Assignation modifiée avec succès";
                $action = "liste";
            }else{
                $action = "modifier_assignation";
                $message = "Un problème est survenu";
            }
        }else{
            $action = "modifier_assignation";
        }
        print $db->error();
}


//Suppression des lignes de besoin
if($action == "activation" || $action == "desactivation"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=historique&id_assignation=".$id_assignation;
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
		$sql_upd = "UPDATE ".MAIN_DB_PREFIX."vehicule SET actif=1 WHERE rowid=".$id_assignation;
		$result = $db->query($sql_upd);

	}elseif($action == "desactivation_ok"){
		$sql_upd = "UPDATE ".MAIN_DB_PREFIX."vehicule SET actif=0 WHERE rowid=".$id_assignation;
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
	print load_fiche_titre($langs->trans("Assignation en cours"), '', '').img_picto('', 'assigner', 'class="paddingright pictofixedwidth"');
	print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i>';
	print "<hr>";
	print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add_assignation">';

	//Vehicule
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_vehicule">Véhicule</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_vehicule" name="fk_vehicule" class="minwidth200 height10" >';
	print '<option></option>';
    $sql = "SELECT rowid, nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid NOT IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin >'".$aujourdhui."')";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($obj_vehicule = $db->fetch_object($res)) {
			
			if(!empty(GETPOST("fk_vehicule", 'int')) && $obj_vehicule->rowid == GETPOST("fk_vehicule"))
				print '<option value="'.$obj_vehicule->rowid.'" selected>'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
			else
				print '<option value="'.$obj_vehicule->rowid.'" >'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
		}

    }
	print '</select></td></tr>';

	//utilisateur
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_user">Utilisateur</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_user" name="fk_user" class="minwidth200 height10">';
	print '<option></option>';
    $sql = "SELECT rowid, firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid NOT IN (SELECT fk_user FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin >'".$aujourdhui."')";
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

	//Date d'acquisition
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date début</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_debut" value="'.GETPOST("date_debut").'" name="date_debut" ></td></tr>';

	//Date d'acquisition
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date fin</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_fin" value="'.GETPOST("date_fin").'" name="date_fin" ></td></tr>';

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" ><label>Note</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><textarea name="note" style="width:250px; height: 40px;">'.GETPOST("note").'</textarea></td></tr>';

	print '</table>';
	print '<hr>';
	print '
		<div style="text-align: center"; align-items: center; justify-content: center">
        <input class="button" type="submit" value="Créer">
        </form>
        <a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique&action=liste" class="button">Annuler</a></td></tr>
    </div>
    ';
}


if($action == "modifier_assignation"){
	print load_fiche_titre($langs->trans("Modification d'un assignation en cours"), '', '').img_picto('', 'assigne', 'class="paddingright pictofixedwidth"');
	print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i>';
	print "<hr>";
	print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique&id_assignation='.$id_assignation.'" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="save_modif_assignation">';


	$sql_assignation = "SELECT * FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE rowid=".$id_assignation;
	$result_assignation = $db->query($sql_assignation);//= $db->query($covSql);

	if($result_assignation && $id_assignation){
		$obj_assignation = $db->fetch_object($result_assignation);

		//Vehicule
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_vehicule">Véhicule</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_vehicule" name="fk_vehicule" class="minwidth200 height10">';
		print '<option></option>';
		$sql = "SELECT rowid, nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid NOT IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE WHERE date_fin >'".$aujourdhui."' )";
		$res = $db->query($sql);
		if($res){
			$num = $db->num_rows($res);
			$a = 0;
			while ($a < $num) {
				$obj_vehicule = $db->fetch_object($res);
				if(!empty(GETPOST("fk_vehicule", 'int'))){
					if($obj_assignation->fk_vehicule == GETPOST("fk_vehicule"))
						print '<option value="'.$obj_vehicule->rowid.'" selected>'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
					else
						print '<option value="'.$obj_vehicule->rowid.'" >'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
				}elseif($obj_assignation->fk_vehicule){
					if($obj_vehicule->rowid == $obj_assignation->fk_vehicule)
						print '<option value="'.$obj_vehicule->rowid.'" selected>'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
					else
						print '<option value="'.$obj_vehicule->rowid.'" >'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
				}
				$a ++;
			}

		}
		print '</select></td></tr>';


		//utilisateur
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_user">Utilisateur</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><select id="fk_user" name="fk_user" class="minwidth200 height10">';
		print '<option></option>';
		$sql = "SELECT rowid, firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid NOT IN (SELECT fk_user FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin >'".$aujourdhui."')";
		$res = $db->query($sql);
		if($res){
			$num = $db->num_rows($res);
			$a = 0;
			while ($a < $num) {
				$obj_user = $db->fetch_object($res);
				if(!empty(GETPOST("fk_user", 'int'))){
					if($obj_fk_user_assignation->fk_user == GETPOST("fk_user"))
						print '<option value="'.$obj_user->rowid.'" selected>'.$obj_user->firstname.' '.$obj_user->lastname.'</option>';
					else
						print '<option value="'.$obj_user->rowid.'" >'.$obj_user->firstname.' '.$obj_user->lastname.'</option>';
				}elseif($obj_user->rowid){
					if($obj_assignation->fk_user == $obj_user->rowid)
						print '<option value="'.$obj_user->rowid.'" selected>'.$obj_user->firstname.' '.$obj_user->lastname.'</option>';
					else
						print '<option value="'.$obj_user->rowid.'" >'.$obj_user->firstname.' '.$obj_user->lastname.'</option>';
				}
				$a ++;
			}

		}
		print '</select></td></tr>';

		//Date d'acquisition
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date début</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_debut" value="'.(GETPOST("date_debut")?:$obj_assignation->date_debut).'" name="date_debut" ></td></tr>';

		//Date d'acquisition
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired"><label>Date fin</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><input style="padding:5px" type="date" Placeholder="date_fin" value="'.(GETPOST("date_fin")?:$obj_assignation->date_fin).'" name="date_fin" ></td></tr>';

		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" ><label>Note</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px"><textarea name="note" style="width:250px; height: 40px;">'.(GETPOST("note")?:$obj_assignation->note).'</textarea></td></tr>';

		print '</table>';
		print '<hr>';
		print '
			<div style="text-align: center"; align-items: center; justify-content: center">
			<input class="button" type="submit" value="Créer">
			</form>
			<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique&action=liste" class="button">Annuler</a></td></tr>
		</div>
		';
	}
}


$limit = GETPOST('limit','alpha')?:20;
$arret = GETPOST('arret','int')?:0;
$nb_page = GETPOST('nbpage','int')?:1;
$tri = GETPOST('tri','alpha');
$etat = GETPOST('etat','alpha');
$recherche_vehicule = GETPOST("recherche_vehicule", "int");
$recherche_user = GETPOST("recherche_user", "int");
$recherche_type = GETPOST("recherche_type", "int");
$recherche_date_debut1 = GETPOST("recherche_date_debut1");
$recherche_date_debut2 = GETPOST("recherche_date_debut2");

$recherche_date_fin1 = GETPOST("recherche_date_fin1");
$recherche_date_fin2 = GETPOST("recherche_date_fin2");

if($action == "liste" || $action == "rechercher"){
	$obj_liste = array();
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau type de salarié", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique&action=creation', '', 1), '', 0, 0, 0, 1);

	
	$sql_vehicule = 'SELECT av.rowid, av.date_debut, av.date_fin, v.rowid as vrowid, v.nom, v.reference_interne, tv.nom as tvnom, v.img, u.rowid as urowid, u.firstname, u.lastname FROM '.MAIN_DB_PREFIX.'assignation_vehicule as av';
	$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'vehicule as v on av.fk_vehicule = v.rowid';
	$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'user as u on av.fk_user = u.rowid';
	$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'type_vehicule as tv on v.fk_type_vehicule = tv.rowid';
	$sql_vehicule .= ' WHERE 1 = 1';
	if($recherche_vehicule){
		$sql_vehicule .= ' AND av.fk_vehicule = '.$recherche_vehicule;
	}

	if($recherche_user){
		$sql_vehicule .= ' AND av.fk_user = '.$recherche_user;
	}

	if($recherche_date_debut1){
		$sql_vehicule .= ' AND av.date_debut >= "'.$recherche_date_debut1.'"';
	}

	if($recherche_date_debut2){
		$sql_vehicule .= ' AND av.date_debut <= "'.$recherche_date_debut2.'"';
	}

	if($recherche_type){
		$sql_vehicule .= ' AND tv.rowid = '.$recherche_type;
	}

	if($recherche_date_fin1){
		$sql_vehicule .= ' AND av.date_fin >= "'.$recherche_date_fin1.'"';
	}

	if($recherche_date_fin2){
		$sql_vehicule .= ' AND av.date_fin <= "'.$recherche_date_fin2.'"';
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
	if($etat == "encours")
		$sql_vehicule .= ' AND av.date_fin > "'.$aujourdhui.'"';
	elseif($etat == "expire")
		$sql_vehicule .= ' AND av.date_fin < "'.$aujourdhui.'"';

	if($tri){
		if($tri == 'date_debut')
			$sql_vehicule .= " ORDER BY av.date_debut";
		elseif($tri == "date_fin") 
			$sql_vehicule .= " ORDER BY av.date_fin";
		elseif($tri == "reference") 
			$sql_vehicule .= " ORDER BY v.reference_interne";
		elseif($tri == "utilisateur") 
			$sql_vehicule .= " ORDER BY u.lastname";
	}else $sql_vehicule .= " ORDER BY av.date_creation DESC";
	
	$result_vehicule = $db->query($sql_vehicule);
	//print $sql_vehicule;
	$j = 0;
	if($result_vehicule){
		$num = $db->num_rows($result_besoin);
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
		print '<form name="ajouter" method="POST" action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique">';
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
					window.location.href = "'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique&limit="+limit+"&action=rechercher&recherche_vehicule='.$recherche_vehicule.'&recherche_user='.$recherche_user.'&recherche_date_debut1='.$recherche_date_debut1.'&recherche_date_debut2='.$recherche_date_debut2.'&recherche_date_fin='.$recherche_date_fin.'&recherche_type='.$recherche_type.'";
				},
				false,
				);
				</script>';
			print "</select>";
			print "</div><br><br>";
    
    $largeur = "12,5px";
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau Besoin", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique&action=creation', '', 1), '', 0, 0, 0, 1);
    print '<table style="width : 100%" class="tagtable liste">';
	print '<tr class="liste_titre">';
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">';
	print '<select id="recherche_vehicule" value="'.$recherche_vehicule.'" name="recherche_vehicule">';
	print '<option></option>';
	$sql = "SELECT rowid, nom, reference_interne FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule)";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($obj_vehicule = $db->fetch_object($res)) {
			
			if(!empty($recherche_vehicule) && $obj_vehicule->rowid == $recherche_vehicule)
				print '<option value="'.$obj_vehicule->rowid.'" selected>'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
			else
				print '<option value="'.$obj_vehicule->rowid.'" >'.$obj_vehicule->nom.' ('.$obj_vehicule->reference_interne.')</option>';
		}

    }
	print '</select></td>';
	
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">';
		print '<select id="recherche_user" value="'.$recherche_user.'" name="recherche_user">';
		print '<option></option>';
		$sql = "SELECT rowid, firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid IN (SELECT fk_user FROM ".MAIN_DB_PREFIX."assignation_vehicule)";
		$res = $db->query($sql);
		if($res){
			$num = $db->num_rows($res);
			$a = 0;
			while ($a < $num) {
				$obj_user = $db->fetch_object($res);
					if(!empty($recherche_user) && $obj_fk_user_assignation->fk_user == $recherche_user)
						print '<option value="'.$obj_user->rowid.'" selected>'.$obj_user->firstname.' '.$obj_user->lastname.'</option>';
					else
						print '<option value="'.$obj_user->rowid.'" >'.$obj_user->firstname.' '.$obj_user->lastname.'</option>';
				
				$a ++;
			}

		}
		print '</select></td>';


	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input type="date" Placeholder="" value="'.$recherche_date_debut1.'" name="recherche_date_debut1" ><br>
	<input type="date" Placeholder="" value="'.$recherche_date_debut2.'" name="recherche_date_debut2" ></td>';
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input type="date" Placeholder="" value="'.$recherche_date_fin1.'" name="recherche_date_fin1" ><br>
	<input type="date" Placeholder="" value="'.$recherche_date_fin2.'" name="recherche_date_fin2" ></td>';
    
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

	if($etat == "encours")
		$active = "selected";
	elseif($etat == "expire")
		$desactive = "selected";
	print '<td align="center" rowspan="2">';
	print '	<select style="padding: 5px; width: 120px;" name="etat">';
    print '<option value="0"></option>';
	print '<option value="encours" '.$active.'>En cours</option>';
	print '<option value="expire" '.$desactive.'>Expirée</option></select><br>';
	print '<input type="submit" class="button" value="Rechercher" style="padding: 4px" >';
	print "</form>";
	print '<br>	<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique" class="button" style="padding: 4px" >Annuler</a>';
	print '</td></tr>';

 print '<tr class="liste_titre">';
  print '<td align="left" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique&tri=reference&recherche_vehicule='.$recherche_vehicule.'&recherche_user='.$recherche_user.'&recherche_date_debut1='.$recherche_date_debut1.'&recherche_date_debut2='.$recherche_date_debut2.'&recherche_date_fin2='.$recherche_date_fin2.'&recherche_date_fin2='.$recherche_date_fin2.'&recherche_type='.$recherche_type.'" >Ref. véhicule</a></label></td>';
 print '<td align="center" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique&tri=utilisateur&recherche_vehicule='.$recherche_vehicule.'&recherche_user='.$recherche_user.'&recherche_date_debut1='.$recherche_date_debut1.'&recherche_date_debut2='.$recherche_date_debut2.'&recherche_date_fin2='.$recherche_date_fin2.'&recherche_date_fin2='.$recherche_date_fin2.'&recherche_type='.$recherche_type.'" >Utilisateurs</a></label></td>';
 print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique&tri=date_debut&recherche_vehicule='.$recherche_vehicule.'&recherche_user='.$recherche_user.'&recherche_date_debut1='.$recherche_date_debut1.'&recherche_date_debut2='.$recherche_date_debut2.'&recherche_date_fin2='.$recherche_date_fin2.'&recherche_date_fin2='.$recherche_date_fin2.'&recherche_type='.$recherche_type.'" >Date début</a></label></td>';
 print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=historique&tri=date_fin&recherche_vehicule='.$recherche_vehicule.'&recherche_user='.$recherche_user.'&recherche_date_debut1='.$recherche_date_debut1.'&recherche_date_debut2='.$recherche_date_debut2.'&recherche_date_fin2='.$recherche_date_fin2.'&recherche_date_fin2='.$recherche_date_fin2.'&recherche_type='.$recherche_type.'" >Date fin</a></label></td>';
 print '<td align="center" ><label>Type véh.</label></td>';

 print '</tr>';
		$actl[0] = img_picto("expiré", 'switch_off', 'class="size15x"');
		$actl[1] = img_picto("actif", 'switch_on', 'class="size15x"');
		$num = count($obj_liste);
		$i = $arret;
		$num = $db->num_rows($result_vehicule);
		while ($i < $num){
            print '<tr class="impair">';
					//print $photo.' <a href="./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=historique&id_assignation='.$obj_liste[$i]->rowid.'&action=detail">'.$obj_liste[$i]->reference_interne.'</a></td>';

			if($obj_liste[$i]->img)
				$photo = img_picto($obj_liste[$i]->nom, 'image_vehicule/'.$obj_liste[$i]->img, 'style="width:30px; height:auto;"');
			else $photo = "";
				print '<td align="left">'.$photo.' <a href="./onglets/assignation_vehicule.php?mainmenu=gestionflotte&leftmenu=historique&id_vehicule='.$obj_liste[$i]->vrowid.'">'.$obj_liste[$i]->reference_interne.'</a></td>';

				print '<td align="center"><a href="../user/card.php?mainmenu=gestionflotte&leftmenu=historique&id='.$obj_liste[$i]->urowid.'">'.$obj_liste[$i]->firstname.' '.$obj_liste[$i]->lastname.'</a></td>';


				print '<td align="center">'.$obj_liste[$i]->date_debut.'</td>';
				print '<td align="center">'.($obj_liste[$i]->date_fin?:'&infin;').'</td>';
				print '<td align="center">'.$obj_liste[$i]->tvnom.'</td>';

				$d_fin = $actl[1];
				if($obj_liste[$i]->date_fin && $obj_liste[$i]->date_fin < $aujourdhui)
					$d_fin = $actl[0];
				print '<td align="center">';
				print $d_fin;
                print '</td>';
                print '</tr>';

				if($i!= 0 && (($i+1)%$limit) == 0){
					$arret = $i;
					$i = $num;
				}else
					$i ++;
			}

			if($num <= 0)
				print '<tr><td align="center" colspan="6">Aucun enregistrement trouvé</td></tr>';
			
	print '</table>';

	 //affichage du reste de la liste
	 print '<span style="float:right; margin-left: 20px;">';
        $nb = (((int)($num%$limit))==0?((int)($num/$limit)):((int)($num/$limit)+1));
	$page_link = "";
	if($num>$limit){
		if($nb_page!= 1)
			if($nb==0 && 1 < ($nb))
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=0&nbpage=1&action=recherche&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";
			else if(1 < ($nb+1))
			$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=0&nbpage=1&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";

		
		if($arret > $limit){

			
			if($nb_page-3>=0)
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".($limit*($nb_page-3))."&nbpage=".($nb_page-2)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>".($nb_page -2)."</b></a>&nbsp;&nbsp;";

			if($nb_page-2>=0)
						$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".($limit*($nb_page-2))."&nbpage=".($nb_page-1)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>".($nb_page-1)."</b></a>&nbsp;&nbsp;";
			
			
			if($nb_page-1>=0)
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".($limit*($nb_page-1))."&nbpage=".($nb_page)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>".($nb_page)."</b></a>&nbsp;&nbsp;";

		

			
				if(	(($nb_page+1) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".($limit*$nb_page)."&nbpage=".($nb_page+1)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 1)."</b></a>&nbsp;&nbsp;";

			
				if((($nb_page+2) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".($limit*($nb_page +1))."&nbpage=".($nb_page+2)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 2)."</b></a>&nbsp;&nbsp;";
					
				
				if((($nb_page+3) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".($limit*($nb_page+2))."&nbpage=".($nb_page+3)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 3)."</b></a>&nbsp;&nbsp;";

					


		}else{

			
				if( 1 <= ($nb))
					
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=0&nbpage=1&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>1</b></a>&nbsp;&nbsp;";
			
			
				if(2 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".$limit."&nbpage=2&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>2</b></a>&nbsp;&nbsp;";
			
			
				if(3 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".($limit*2)."&nbpage=3&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>3</b></a>&nbsp;&nbsp;";
				
				if(4 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".($limit*3)."&nbpage=4&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>4</b></a>&nbsp;&nbsp;";

				if(5 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".($limit*4)."&nbpage=5&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'><b>5</b></a>&nbsp;&nbsp;";



		}
		if($nb_page != ($nb)  )
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".($limit*($nb-1))."&nbpage=".($nb)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut1=".$recherche_date_debut1."&recherche_date_debut2=".$recherche_date_debut2."&recherche_date_fin1=".$recherche_date_fin1."&recherche_date_fin2=".$recherche_date_fin2."&recherche_type=".$recherche_type."&tri=".$tri."' style='padding: 5px'>      <b>Fin</b></a>&nbsp;&nbsp;";

		
		/*if($limit == ($arret +1))
			$page_link .= "<a style='background-color: yellow; padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".(($arret +1) -$limit)."'>".((int)(($arret+1)/$limit))."</a>";
		else $page_link .= ($arret +1 - $limit)>?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".(($arret +1) -$limit)."' style='padding: 5px'><b>".((int)(($arret+1)/$limit))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*2 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*2)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*3 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".(($arret +1)*3 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*3)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= "<a style='padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=historique&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."'><b>></b>&nbsp;&nbsp;</a>";
	*/}
	print $page_link.'</span>';
}
print $db->error();

//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}
