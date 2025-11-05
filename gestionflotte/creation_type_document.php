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
require_once DOL_DOCUMENT_ROOT.'/custom/gestionflotte/core/modules/modGestionFlotte.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/gestionflotte/class/html.form.class.php';

llxHeader('', "Gestion de flotte");
$action = GETPOST('action', 'alpha')?:"liste";
$tri = GETPOST('tri', 'alpha');
$id_type_document = GETPOST('id_type_document', 'int');
$monform = new Form1($db);

$message = '';


if($action == "add_type_document"){
		$nom = GETPOST('nom', 'alpha');
		$type = GETPOST('type', 'alpha');
        $description = GETPOST('description', 'alpha');
		$fk_type_vehicule = GETPOST('fk_type_vehicule', 'alpha');

		if(empty($nom))
			$message = 'Le champ "nom" est oblogatoire<br>';

		if(empty($description))
			$message = 'Le champ "DESCRIPTION" est oblogatoire<br>';

		if(empty($fk_type_vehicule))
			$message = 'Le champ "TYPE VEHICULE" est oblogatoire<br>';
		
        if(empty($message)){
			$fk_type_vehicule = implode(',', $fk_type_vehicule);
            $sql = 'INSERT INTO '.MAIN_DB_PREFIX.'type_document (nom, fk_type_vehicule, type, commentaire, fk_user_creation) 
			VALUES("'.$nom.'", "'.$fk_type_vehicule.'", "'.$type.'", "'.$description.'", '.$user->id.')';
		    $result = $db->query($sql);
			//print $sql;
            if($result){
                $message = "Un nouveau type document cré avec succès";
                $action = "liste";
				header('Location: ./creation_type_vehicule.php?mainmenu=gestionflotte&leftmenu=type_document&message='.$message);
            }else{
                $action = "creation";
                $message = "Un problème est survenu";
            }
        }else{
            $action = "creation";
        }
        
}


if($action == "save_modif_type_document"){
		$nom = GETPOST('nom', 'alpha');
        $description = GETPOST('description', 'alpha');
		$type = GETPOST('type', 'alpha');
		$fk_type_vehicule = GETPOST('fk_type_vehicule', 'alpha');

		if(empty($nom))
			$message = 'Le champ "nom" est oblogatoire<br>';
		
		if(empty($description))
			$message = 'Le champ "DESCRIPTION" est oblogatoire<br>';

        if(empty($message)){
			$fk_type_vehicule = implode(',', $fk_type_vehicule);
            $sql = 'UPDATE '.MAIN_DB_PREFIX.'type_document SET type = "'.$type.'", fk_type_vehicule = "'.$fk_type_vehicule.'", nom="'.$nom.'", commentaire="'.$description.'" WHERE rowid='.$id_type_document;
		    $result = $db->query($sql);
			//print $sql;
            if($result){
                $message = "Un type document modifié avec succès";
                $action = "liste";
            }else{
                $action = "modifier_type_document";
                $message = "Un problème est survenu";
            }
        }else{
            $action = "modifier_type_document";
        }
}


//Suppression des lignes de besoin
if($action == "attente_type_suppression"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=type_document&id_type_document=".$id_type_document;
    $titre = 'Veuillez confirmer la suppression';

      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          "", 
          'supprimer_type_document_ok', 
          $array, 
          '', 
          1,
          100,
          '30%'
      );
	  
      $action = "liste";
}

if($action == "supprimer_type_document_ok"){ 

    //suppression
    $sql = "DELETE FROM ".MAIN_DB_PREFIX."type_document WHERE rowid=".$id_type_document;
    $result = $db->query($sql);
    if($result)
        $message = 'Type document supprimée avec succès';
    else    $message = 'Un problème est survenu';

    $action = "liste";
}

if(!empty(GETPOST('message')))
	$message = GETPOST('message');
print $db->error();
print $formconfirm;
if($action == "creation"){
	print load_fiche_titre($langs->trans("Ajout d'un nouveau type document véhicule"), '', '').img_picto('', 'papier', 'class="paddingright pictofixedwidth"');
		print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i>';
		print "<hr><br>";
	print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document" method="post">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add_type_document">';

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>nom</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><input type="text" id="nom" name="nom" value="'.GETPOST("nom").'"/></td></tr>';

	//Type document
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_type_vehicule">Type véhicule</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px">';
		$array_id = array(-1);
		$array_nom = array("Aucun");
		$id_selected_array = GETPOST('fk_type_vehicule')?:array();
	
		$sql = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."type_vehicule";
		$res = $db->query($sql);
		if($res){
			$num = $db->num_rows($res);
			$a = 0;
			while ($obj_type_document = $db->fetch_object($res)) {
				$array_id[] = $obj_type_document->rowid;
				$array_nom[] = $obj_type_document->nom;	
			}
		}

		$alltype = array_combine($array_id, $array_nom);
		$monform = new Form($db);
		print $monform->multiselectarray('fk_type_vehicule', $alltype, $id_selected_array, null, 0, 'minwidth200 height10', 0, 0);

		print '</td></tr>';

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>Description</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><textarea name="description" style="width:300px; height: 80px;" name="description" >'.GETPOST("description").'</textarea></td></tr>';

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>Type</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px">
	<select id="type" name="type" style="width: 250px; padding-right: 30px; padding-bottom: 10px">';
	print '<option value="obligatoire">Obligatoire</option>';
	print '<option value="facultatif">Facultatif</option>';
	print '</td></tr>';

	print '</table>';
	print '<hr>';
	print '
		<div style="text-align: center"; align-items: center; justify-content: center">
        <input class="button" type="submit" value="Créer" name=""/>
        </form>
        <a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document&action=liste" class="button">Annuler</a></td></tr>
    </div>
    ';
}

if($action == "modifier_type_document"){
		print load_fiche_titre($langs->trans("Modification d'un type de document véhicule"), '', '');

	$sql_maintenance = "SELECT * FROM ".MAIN_DB_PREFIX."type_document WHERE rowid=".$id_type_document;
	$result_maintenance = $db->query($sql_maintenance);//= $db->query($covSql);

	if($result_maintenance & $id_type_document){
		$obj_type_document = $db->fetch_object($result_maintenance);

		print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i>';
		print "<hr><br>";
		print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document&id_type_document='.$id_type_document.'" method="post">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<input type="hidden" name="action" value="save_modif_type_document">';

		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>nom</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><input type="text" id="nom" name="nom" value="'.(GETPOST("nom")?:$obj_type_document->nom).'"/></td></tr>';

		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>Description</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><textarea name="description" style="width:300px; height: 80px;" name="description" >'.(GETPOST("description")?:$obj_type_document->commentaire).'</textarea></td></tr>';

		//Type document
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 7px" class="fieldrequired" ><label for="fk_type_vehicule">Type véhicule</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 7px">';

		$array_id = array(-1);
		$array_nom = array("Aucun");
		$id_selected_array = (GETPOST('fk_type_vehicule')?:(explode(',', $obj_type_document->fk_type_vehicule)?:array(-1)));
	
		$sql = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."type_vehicule";
		$res = $db->query($sql);
		if($res){
			$num = $db->num_rows($res);
			$a = 0;
			while ($obj_type_vehicule = $db->fetch_object($res)) {
				$array_id[] = $obj_type_vehicule->rowid;
				$array_nom[] = $obj_type_vehicule->nom;	
			}
		}

		$alltype = array_combine($array_id, $array_nom);
		$monform = new Form($db);
		print $monform->multiselectarray('fk_type_vehicule', $alltype, $id_selected_array, null, 0, 'minwidth200 height10', 0, 0);
		print '</td></tr>';

		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>Type</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px">
		<select id="type" name="type" style="width: 250px; padding-right: 30px; padding-bottom: 10px">';
		print '<option value="obligatoire" '.(($obj_type_document->type == 'obligatoire')?'selected':'').'>Obligatoire</option>';
		print '<option value="facultatif" '.(($obj_type_document->type == 'facultatif')?'selected':'').'>Facultatif</option>';
		print '</td></tr>';


		print '</table>';
		print '<hr>';
		print '
			<div style="text-align: center"; align-items: center; justify-content: center">
			<input class="button" type="submit" value="Valider" name=""/>
			</form>
			<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document&action=liste" class="button">Annuler</a></td></tr>
		</div>
		';
	}
}

$limit = GETPOST('limit','alpha')?:20;
$arret = GETPOST('arret','int')?:0;
$nb_page = GETPOST('nbpage','int')?:1;

	$recherche_nom = GETPOST("recherche_nom", "alpha");
	$recherche_description = GETPOST("recherche_description", "alpha");
	$recherche_type = GETPOST("recherche_type", "alpha");
	$recherche_type_vehicule = GETPOST("recherche_type_vehicule", "int");

if($action == "liste" || $action == "rechercher"){
	$obj_liste = array();

	$sql_type_document = "SELECT * FROM ".MAIN_DB_PREFIX."type_document WHERE 1=1";

	if (!($user->id == 1 || $user->hasRight("gestionflotte", "gestionvehicule", "read"))) {
		$sql_type_document .= " AND fk_user = ".((int) $user->id);
	}

	if (!empty($recherche_nom)) {
		$sql_type_document .= " AND nom LIKE '%".$db->escape($recherche_nom)."%'";
	}

	if (!empty($recherche_description)) {
		$sql_type_document .= " AND commentaire LIKE '%".$db->escape($recherche_description)."%'";
	}

	if (!empty($recherche_type)) {
		$sql_type_document .= " AND type = '".$db->escape($recherche_type)."'";
	}

	if (!empty($recherche_type_vehicule)) {
		$sql_type_document .= " AND FIND_IN_SET(".((int) $recherche_type_vehicule).", fk_type_vehicule)";
	}
	

	if($tri){
		if($tri == "nom")
			$sql_type_document .= " ORDER BY nom";
		elseif($tri == "commentaire")
			$sql_type_document .= " ORDER BY commentaire";
		elseif($tri == 'type')
			$sql_type_document .= " ORDER BY type DESC";
	}else $sql_type_document .= " ORDER BY date_creation DESC";

	$result_type_document = $db->query($sql_type_document);
	//print $sql_type_document;
	$j = 0;
	if($result_type_document){
		$num = $db->num_rows($result_type_document);
		while ($j < $num){
			$obj_liste[] = $db->fetch_object($result_type_document);
            $j ++;
		}
	}
		//Titre
		print load_fiche_titre($langs->trans("Liste des types de document(".$num.")"), '', '');
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
		print '<form name="ajouter" method="POST" action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document">';
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
					window.location.href = "'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document&limit="+limit+"&action=rechercher&recherche_nom='.$recherche_nom.'&recherche_description='.$recherche_description.'&recherche_typr='.$recherche_type.'&recherche_type_vehicule='.$recherche_type_vehicule.'";
				},
				false,
				);
				</script>';
			print "</select>";
			print "</div><br><br>";
    
    $largeur = "12,5px";
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau Besoin", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document&action=creation', '', 1), '', 0, 0, 0, 1);
    print '<table style="width : 100%" class="tagtable liste">';
	print '<tr class="liste_titre">';
	print '<td style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_nom.'" name="recherche_nom" ></td>';
	
	print '<td style="padding: 5px; width: '.$largeur.';">
	<select id="recherche_type" name="recherche_type">';

	$select_obl = '';
	$select_fac = '';
	if(GETPOST('recherche_type') == 'obligatoire')
		$select_obl = 'selected';
	if(GETPOST('recherche_type') == 'facultatif')
		$select_fac = 'selected';
	print '<option value="0"></option>
	<option value="obligatoire" '.$select_obl.'>Obligatoire</option>
	<option value="facultatif" '.$select_fac.'>Facultatif</option>';
	print '</td>';
	print '<td><select id="recherche_type_vehicule" name="recherche_type_vehicule" style="padding: 5px; width: '.$largeur.';">
    <option value="0"></option>';

    $sql_type = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."type_vehicule ORDER BY date_creation";
    $res_type = $db->query($sql_type);
    if($res_type){
        $nb = $db->num_rows($res_type);
        $a = 0;
        while ($obj_type = $db->fetch_object($res_type)) {
            if($recherche_type_vehicule == $obj_type->rowid)
                print '<option value="'.$obj_type->rowid.'" selected>'.$obj_type->nom.'</option>';
            else 
                print '<option value="'.$obj_type->rowid.'">'.$obj_type->nom.'</option>';

        }

    }
	print '</select></td>';
	print '<td style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_description.'" name="recherche_description" ></td>';
	print '<td align="center" rowspan="2"><input type="submit" class="button" value="Rechercher" style="padding: 4px" >';
	print "</form>";
	print '<br>	<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document" class="button" style="padding: 4px" >Annuler</a>';
	print '</td></tr>';

    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau type de salarié", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document&action=creation', '', 1), '', 0, 0, 0, 1);
	print '<tr class="liste_titre">';
 	print '<td style="25%; color: darkblue;"><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document&limit='.$limit.'&recherche_nom='.$recherche_nom.'&recherche_description='.$recherche_description.'&recherche_type='.$recherche_type.'&recherche_type_vehicule='.$recherche_type_vehicule.'&tri=nom" title="Trié par nom">Nom</a></label></td>';
	print '<td style="25%; color: darkblue;"><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document&limit='.$limit.'&recherche_nom='.$recherche_nom.'&recherche_description='.$recherche_description.'&recherche_type='.$recherche_type.'&recherche_type_vehicule='.$recherche_type_vehicule.'&tri=type" title="type obligatoire d\'abord">Type</a></label></td>';
	print '<td style="25%; color: darkblue;"><label>Type veh.</label></td>';
	print '<td style="25%; color: darkblue;"><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document&limit='.$limit.'&recherche_nom='.$recherche_nom.'&recherche_description='.$recherche_description.'&recherche_type='.$recherche_type.'&recherche_type_vehicule='.$recherche_type_vehicule.'&tri=commentaire" title="Trié par description">Description</a></label></td>';
	print '</tr>';

 	$acts[0] = "activation";
	$acts[1] = "desactivation";
	$actl[0] = img_picto($langs->trans("Disabled"), 'switch_off', 'class="size30x"');
	$actl[1] = img_picto($langs->trans("Activated"), 'switch_on', 'class="size30x"');
 
	$num = count($obj_liste);
		$i = $arret;
		while ($i < $num){
                print '<tr class="impair">';
                print ''.affiche_long_texte(img_picto("", "statut7_blue", "class='paddingright pictofixedwidth'"), $obj_liste[$i]->nom, 0, '', 'nom', '', '', '', '').'';
				print '<td style="25%; color: darkblue;"><label>'.$obj_liste[$i]->type.'</label></td>';
				$outils = "";
				if($obj_liste[$i]->fk_piece_remplacer != -1){
					$sql_outil = "SELECT nom FROM ".MAIN_DB_PREFIX."type_vehicule WHERE rowid IN (".$obj_liste[$i]->fk_type_vehicule.") ORDER BY nom";
					$res_outil = $db->query($sql_outil);
					if($res_outil)
						while($obj_outil = $db->fetch_object($res_outil)){
							$outils .= '<span style="background-color: #6c757d; color: white; padding: 1px 6px; border-radius: 3px; font-weight: bold;">
							<a style="color : white; text-decoration : none;" href="./creation_type_vehicule.php?mainmenu=gestionflotte&leftmenu=panne&recherche_nom='.$obj_outil->nom.'">'.$obj_outil->nom.'</a>
							</span>&nbsp;'; // Gris
						}
				}else{
					$outils .= '<span style="background-color: #6c757d; color: white; padding: 3px 6px; border-radius: 3px; font-weight: bold;">Aucune</span>&nbsp;'; // Gris

				}

				print '<td style="25%; color: darkblue;"><label>'.$outils.'</label></td>';
                print ''.affiche_long_texte('',  $obj_liste[$i]->commentaire, 1, '', '', '', '', '', '');


				//comptes du nombre de vehicule avec ce type de document
				$sql_type_doc = "SELECT COUNT(rowid) AS nombre_doc FROM ".MAIN_DB_PREFIX."document_vehicule WHERE date_expiration >= '".$db->escape($aujourdhui)."' AND fk_type_document = ".(int) $obj_liste[$i]->rowid;
				$res_type_doc = $db->query($sql_type_doc);

				$nb_nombre_vehicule = 0;
				if ($res_type_doc) {
					$obj_type_doc = $db->fetch_object($res_type_doc);
					if ($obj_type_doc) {
						$nb_nombre_vehicule = $obj_type_doc->nombre_doc;
					}
				}
				
				print '<td align="center">';
				if($user->hasRight("gestionflotte", "gestionvehicule", "write")){
					print '<a class="reposition editfielda" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_document&id_type_document='.$obj_liste[$i]->rowid.'&action=modifier_type_document">'.img_edit('Modifier','').'</a>';
					if($nb_nombre_vehicule > 0)
						print '&nbsp;&nbsp;<a class="reposition editfielda" href="./creation_vehicule.php?mainmenu=gestionflotte&leftmenu=type_document&recherche_type_document='.$obj_liste[$i]->rowid.'&action=rechercher">'.img_picto("Un véhicule a ce type de document actif", "vehicule", 'class="paddingright pictofixedwidth"')."(".$nb_nombre_vehicule.")</a>";
				}else{
					print img_picto("Vous n\'avez pas le droit","warning");
				}
					print '</td>';

                print '</tr>';

				if($i!= 0 & (($i+1)%$limit) == 0){
					$arret = $i;
					$i = $num;
				}else
					$i ++;
			}

			if($num <= 0)
				print '<tr><td align="center" colspan="5">Aucun Type document disponible</td></tr>';
			

	print '</table>';
	 //affichage du reste de la liste
	 print '<span style="float:right; margin-left: 20px;">';
        $nb = (((int)($num%$limit))==0?((int)($num/$limit)):((int)($num/$limit)+1));
	$page_link = "";
	if($num>$limit){
		if($nb_page!= 1)
			if($nb==0 & 1 < ($nb))
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=0&nbpage=1&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";
			else if(1 < ($nb+1))
			$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=0&nbpage=1&action=rechercher&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";

		
		if($arret > $limit){

			
			if($nb_page-3>=0)
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".($limit*($nb_page-3))."&nbpage=".($nb_page-2)."&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>".($nb_page -2)."</b></a>&nbsp;&nbsp;";

			if($nb_page-2>=0)
						$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".($limit*($nb_page-2))."&nbpage=".($nb_page-1)."&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>".($nb_page-1)."</b></a>&nbsp;&nbsp;";
			
			
			if($nb_page-1>=0)
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".($limit*($nb_page-1))."&nbpage=".($nb_page)."&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>".($nb_page)."</b></a>&nbsp;&nbsp;";

		

			
				if(	(($nb_page+1) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".($limit*$nb_page)."&nbpage=".($nb_page+1)."&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>".($nb_page + 1)."</b></a>&nbsp;&nbsp;";

			
				if((($nb_page+2) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".($limit*($nb_page +1))."&nbpage=".($nb_page+2)."&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>".($nb_page + 2)."</b></a>&nbsp;&nbsp;";
					
				
				if((($nb_page+3) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".($limit*($nb_page+2))."&nbpage=".($nb_page+3)."&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>".($nb_page + 3)."</b></a>&nbsp;&nbsp;";

					


		}else{

			
				if( 1 <= ($nb))
					
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=0&nbpage=1&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>1</b></a>&nbsp;&nbsp;";
			
			
				if(2 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".$limit."&nbpage=2&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>2</b></a>&nbsp;&nbsp;";
			
			
				if(3 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".($limit*2)."&nbpage=3&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>3</b></a>&nbsp;&nbsp;";
				
				if(4 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".($limit*3)."&nbpage=4&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>4</b></a>&nbsp;&nbsp;";

				if(5 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".($limit*4)."&nbpage=5&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'><b>5</b></a>&nbsp;&nbsp;";



		}
		if($nb_page != ($nb)  )
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".($limit*($nb-1))."&nbpage=".($nb)."&action=recherche&tri=".$tri."&recherche_nom=".$recherche_nom."&recherche_description=".$recherche_description."&recherche_type=".$recherche_type."&recherche_type_vehicule=".$recherche_type_vehicule."' style='padding: 5px'>      <b>Fin</b></a>&nbsp;&nbsp;";

		
		/*if($limit == ($arret +1))
			$page_link .= "<a style='background-color: yellow; padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".(($arret +1) -$limit)."'>".((int)(($arret+1)/$limit))."</a>";
		else $page_link .= ($arret +1 - $limit)>?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".(($arret +1) -$limit)."' style='padding: 5px'><b>".((int)(($arret+1)/$limit))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*2 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*2)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*3 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".(($arret +1)*3 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*3)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= "<a style='padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_document&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."'><b>></b>&nbsp;&nbsp;</a>";
	*/}
	print $page_link.'</span>';
}
//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}