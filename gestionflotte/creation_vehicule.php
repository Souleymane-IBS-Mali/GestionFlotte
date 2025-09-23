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
require_once DOL_DOCUMENT_ROOT.'/custom/depensesimputations/lib/depensesimputations.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/depensesimputations/core/modules/modDepensesImputations.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/depensesimputations/class/html.form.class.php';


llxHeader('', "Dépenses | Imputations");
$action = GETPOST('action', 'alpha')?:"liste";
$id_entite = GETPOST('id_entite', 'int');
$type = GETPOST('type', 'int');
$niveau = GETPOST('niveau', 'int');

$monform = new Form1($db);

$message = '';

if($action == "add_entite"){
		$nom = GETPOST('nom', 'alpha');
        $description = GETPOST('description', 'alpha');
        $type_entite = GETPOST('type', 'int');
		$niveau = GETPOST('niveau', 'int');
		if(empty($nom))
			$message = 'Le champ "NOM" est oblogatoire<br>';

		if(empty($type_entite))
			$message .= 'Le champ "TYPE" est oblogatoire<br>';
		
        if(empty($message)){
            $sql_insert = 'INSERT INTO '.MAIN_DB_PREFIX.'entite (nom, commentaire, fk_type_entite, fk_parent, actif) VALUES("'.$nom.'","'.$description.'", '.$type_entite.', '.($niveau?:0).', 1)';
		    $result = $db->query($sql_insert);
			//print $sql_insert;
            if($result){
                $message = "Une nouvelle entité créée avec succès";
                $action = "liste";
            }else{
                $action = "creation";
                $message = "Un problème est survenu";
            }
        }else{
            $action = "creation";
        }
        print $db->error();
}

if($action == "save_modif_entite"){
		$nom = GETPOST('nom', 'alpha');
        $description = GETPOST('description', 'alpha');
        $type_entite = GETPOST('type', 'int');
		$niveau = GETPOST('niveau', 'int');        

		if(empty($nom))
			$message = 'Le champ "NOM" est oblogatoire<br>';

		if(empty($type_entite))
			$message .= 'Le champ "TYPE" est oblogatoire<br>';
		
        if(empty($message)){
            $sql_update = 'UPDATE '.MAIN_DB_PREFIX.'entite SET nom="'.$nom.'", commentaire="'.$description.'", fk_type_entite='.$type_entite.', fk_parent='.$niveau.' WHERE rowid='.$id_entite;
		    $result = $db->query($sql_update);
			//print $sql_update;
            if($result){
                $message = "Entité modifiée avec succès";
                $action = "liste";
            }else{
                $action = "modifier_entite";
                $message = "Un problème est survenu";
            }
        }else{
            $action = "modifier_entite";
        }
        print $db->error();
}


//Suppression des lignes de besoin
if($action == "activation" || $action == "desactivation"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=depensesimputations&leftmenu=entite&id_entite=".$id_entite;
	$suffixe = "l\'activation";
	$act = "activation_ok";
	if($action == "desactivation"){
		$suffixe = "la desactivation";
		$act = "desactivation_ok";
	}
    $titre = 'Veuillez confirmer '.$suffixe;
	$notice = "";
	$nb_user = GETPOST("nb_user", "int");
	if($nb_user > 0)
		$notice = img_picto("Attention", "warning")." Il y a ".$nb_user." utilisateur(s) affecté(s) à cette entité";
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

	$act = "activée";
	if($action == "activation_ok"){
		//suppression des affectations liées à cette entité
		$sql_upd = "UPDATE ".MAIN_DB_PREFIX."entite SET actif=1 WHERE rowid=".$id_entite;
		$result = $db->query($sql_upd);

	}elseif($action == "desactivation_ok"){
		$sql_upd = "UPDATE ".MAIN_DB_PREFIX."entite SET actif=0 WHERE rowid=".$id_entite;
		$result = $db->query($sql_upd);
		$act = "desactivée";
	}
	

    //notification
    if($result)
        $message = 'Entité '.$act.' avec succès';
    else    $message = 'Un problème est survenu';

    $action = "liste";
}


if($action == "creation"){
	print load_fiche_titre($langs->trans("Ajout d'une nouvelle entité"), '', '');
	print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i>';
	print "<hr><br>";
	print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=entite" method="post">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add_entite">';

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>Nom</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><input type="text" id="nom" name="nom" value="'.GETPOST("nom").'" required></td></tr>';

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired" ><label for="type">Type</label></td>';

	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><select id="type" name="type" class="fieldrequired minwidth200 height10" required>';
	print '<option></option>';
    $sql = "SELECT * FROM ".MAIN_DB_PREFIX."type_entite";
    $res = $db->query($sql);
    if($res){
		$num = $db->num_rows($res);
		$a = 0;
		while ($a < $num) {
			$obj_type_entite = $db->fetch_object($res);
			if(!empty(GETPOST("type", 'int')) && $obj_type_entite->rowid == GETPOST("type"))
				print '<option value="'.$obj_type_entite->rowid.'" selected>'.$obj_type_entite->nom.'</option>';
			else
				print '<option value="'.$obj_type_entite->rowid.'" >'.$obj_type_entite->nom.'</option>';
			$a ++;
		}

    }
	print '</select>';
	if(!empty($type)){
			print '  Parent : <select name="niveau" id="niveau" class="fieldrequired minwidth200 height10">';
			$id_parent = 0;
			$nom_type_parent = "";
			$sql = "SELECT fk_parent FROM ".MAIN_DB_PREFIX."type_entite WHERE rowid=".$type;
			$res = $db->query($sql);
			if($res){
				$obj = $db->fetch_object($res);
				$id_parent = $obj->fk_parent;

				$sql = "SELECT nom FROM ".MAIN_DB_PREFIX."type_entite WHERE rowid=".$id_parent;
				$res = $db->query($sql);
				if($res){
					$obj = $db->fetch_object($res);
					$nom_type_parent = $obj->nom;
				}

				$sql = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."entite WHERE actif=1 AND fk_type_entite=".$id_parent;
				$res = $db->query($sql);
				if($res){
					$num = $db->num_rows($res);
					$a = 0;
					while ($a < $num) {
						$obj_type_entite = $db->fetch_object($res);
						if((!empty($id_entite) && $obj_type_entite->rowid == $id_entite) || (!empty(GETPOST("type") && $obj_type_entite->rowid == GETPOST("type"))))
							print '<option value="'.$obj_type_entite->rowid.'" selected>'.$obj_type_entite->nom.'</option>';
						else
							print '<option value="'.$obj_type_entite->rowid.'" >'.$obj_type_entite->nom.'</option>';
						$a ++;
					}

				}
			}
			/*if(empty($num))
				$info = "Il a pour type parent '".$nom_type_parent."' Et il n'y a accun type '".$nom_type_parent."' crée dans les entités";*/
		}
			print '</select>';
			/*if(!empty($info))
				info_admin($info, "1");

				print '<mark>'.$info.'</mark>';*/
	
	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" ><label>Description</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><textarea name="description" style="width:300px; height: 80px;">'.GETPOST("description").'</textarea></td></tr>';
print '<script type="text/javascript">
		var type = document.getElementById("type");
		var niveau = document.getElementById("niveau");
		var nom = document.getElementById("nom");

		
		type.addEventListener("change", function () {
			var val_type = type.value;
			var val_nom = nom.value;
			window.location.href = "'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=creation_entite&type="+val_type+"&nom="+val_nom+"&action=creation";
		},
		false,
		);
	</script>';
	print '</table>';
	print '<hr>';
	print '
		<div style="text-align: center"; align-items: center; justify-content: center">
        <input class="button" type="submit" value="Créer">
        </form>
        <a href="'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=entite&action=liste" class="button">Annuler</a></td></tr>
    </div>
    ';
}

if($action == "modifier_entite"){
		print load_fiche_titre($langs->trans("Modification d'une entité"), '', '');

	$sql_entite = "SELECT * FROM ".MAIN_DB_PREFIX."entite WHERE rowid=".$id_entite;
	$result_entite = $db->query($sql_entite);//= $db->query($covSql);

	if($result_entite && $id_entite){
		$obj_entite = $db->fetch_object($result_entite);

		print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i>';
		print "<hr><br>";
		print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=entite&id_entite='.$id_entite.'" method="post">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<input type="hidden" name="action" value="save_modif_entite">';

		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>Nom</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><input type="text" id="nom" name="nom" value="'.(GETPOST("nom")?:$obj_entite->nom).'" required/></td></tr>';

		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired" ><label>Type</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><select id="type" name="type" class="fieldrequired minwidth200 height10" required >';
		print '<option value="0">Racine</option>';
		$nom_type_entite = "Entité";
		$sql = "SELECT * FROM ".MAIN_DB_PREFIX."type_entite";
		$res = $db->query($sql);
		if($res){
			$num = $db->num_rows($res);
			$a = 0;
			while ($a < $num) {
				$obj_type_entite = $db->fetch_object($res);
				if(GETPOST("type")){
					if($obj_type_entite->rowid == GETPOST("type"))
						print '<option value="'.$obj_type_entite->rowid.'" selected>'.$obj_type_entite->nom.'</option>';
					else
						print '<option value="'.$obj_type_entite->rowid.'" >'.$obj_type_entite->nom.'</option>';
				}elseif($obj_type_entite->rowid == $obj_entite->fk_type_entite)
					print '<option value="'.$obj_type_entite->rowid.'" selected>'.$obj_type_entite->nom.'</option>';
				else
					print '<option value="'.$obj_type_entite->rowid.'" >'.$obj_type_entite->nom.'</option>';
				$a ++;
			}

		}
		print '</select>';

		if(!empty($obj_entite->fk_parent) || !empty($type)){
			if(empty($type))
				$type = $obj_entite->fk_parent;
			print '  Parent : <select name="niveau" id="niveau" class="fieldrequired minwidth200 height10">';
			$id_parent = 0;
			$nom_type_parent = "";
			$sql = "SELECT fk_parent FROM ".MAIN_DB_PREFIX."type_entite WHERE rowid=".$type;
			$res = $db->query($sql);
			if($res){
				$obj = $db->fetch_object($res);
				$id_parent = $obj->fk_parent;

				$sql = "SELECT nom FROM ".MAIN_DB_PREFIX."type_entite WHERE rowid=".$id_parent;
				$res = $db->query($sql);
				if($res){
					$obj = $db->fetch_object($res);
					$nom_type_parent = $obj->nom;
				}

				$sql = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."entite WHERE actif=1 AND fk_type_entite=".$id_parent;
				$res = $db->query($sql);
				if($res){
					$num = $db->num_rows($res);
					$a = 0;
					if($num == 0)
						print '<option value="0">Racine</option>';

					while ($a < $num) {
						$obj_type_entite = $db->fetch_object($res);
						if((!empty($id_entite) && $obj_type_entite->rowid == $id_entite) || (!empty(GETPOST("type") && $obj_type_entite->rowid == GETPOST("type"))))
							print '<option value="'.$obj_type_entite->rowid.'" selected>'.$obj_type_entite->nom.'</option>';
						else
							print '<option value="'.$obj_type_entite->rowid.'" >'.$obj_type_entite->nom.'</option>';
						$a ++;
					}

				}
			}
			if(empty($num))
				$info = "Il a pour type parent '".$nom_type_parent."' Et il n'y a accun type '".$nom_type_parent."' crée dans les entités";
		}

		print '</select></td>';;
		print '</tr>';
		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" ><label>Description</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><textarea name="description" style="width:300px; height: 80px;" name="description" >'.(GETPOST("description")?:$obj_entite->commentaire).'</textarea></td></tr>';

		print '</table>';
		print '<hr>';

		print '<script type="text/javascript">
		var type = document.getElementById("type");
		var nom = document.getElementById("nom");

		
		type.addEventListener("change", function () {
			var val_type = type.value;
			var val_nom = nom.value;
			window.location.href = "'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=entite&id_entite='.$id_entite.'&type="+val_type+"&nom="+val_nom+"&action=modifier_entite";
		},
		false,
		);
	</script>';
		print '
			<div style="text-align: center"; align-items: center; justify-content: center">
			<input class="button" type="submit" value="Valider" />
			</form>
			<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=entite&action=liste" class="button">Annuler</a></td></tr>
		</div>';
	}
}


$limit = GETPOST('limit','alpha')?:20;
$arret = GETPOST('arret','int')?:0;
$nb_page = GETPOST('nbpage','int')?:1;
$recherche_nom = "";		

			if($action == "rechercher"){
				$recherche_nom = GETPOST("recherche_nom", "alpha");
				$recherche_type = GETPOST("recherche_type", "int");
				$recherche_parent = GETPOST("recherche_parent", "int");
				$recherche_etat = GETPOST("recherche_etat", "alpha");
			}

if($action == "liste" || $action == "rechercher"){
	$obj_liste = array();
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau type de salarié", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=listeentite&action=creation', '', 1), '', 0, 0, 0, 1);

	
	if($user->id == 1 || $user->hasRight("depensesimputations", "entites", "read"))
		$sql_entite = 'SELECT * FROM '.MAIN_DB_PREFIX.'entite WHERE fk_user_impute IS NULL';
	else $sql_entite = 'SELECT * FROM '.MAIN_DB_PREFIX.'entite WHERE fk_user_impute IS NULL AND fk_user='.$user->id;

	if($recherche_nom){
		$sql_entite .= ' AND nom LIKE "%'.$recherche_nom.'%"';
	}

	if($recherche_type){
		$sql_entite .= ' AND fk_type_entite = '.$recherche_type;
	}

	if($recherche_parent){
		$sql_entite .= ' AND fk_parent = '.$recherche_parent;
	}

	$active = "";
	$desactive = "";
	if($recherche_etat == "active"){
		$sql_entite .= ' AND actif = 1';
		$active = "selected";
	}elseif($recherche_etat == "inactive"){
		$sql_entite .= ' AND actif = 0';
		$desactive = "selected";
	}
	

	$sql_entite .= " ORDER BY fk_parent";
	$result_entite = $db->query($sql_entite);
	//print $sql_entite;
	$j = 0;
	if($result_entite){
		$num = $db->num_rows($result_besoin);
		while ($j < $num){
			$obj_liste[] = $db->fetch_object($result_entite);
            $j ++;
		}
	}
		//Titre
		print load_fiche_titre($langs->trans("Liste des entités(".$num.")"), '', '');
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
		print '<form name="ajouter" method="POST" action="'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=entite">';
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
					window.location.href = "'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=entite&limit="+limit+"&action=rechercher&recherche_nom='.$recherche_nom.'&recherche_type='.$recherche_type.'&recherche_parent='.$recherche_parent.'&recherche_etat='.$recherche_etat.'";
				},
				false,
				);
				</script>';
			print "</select>";
			print "</div><br><br>";
    
    $largeur = "12,5px";
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau Besoin", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=entite&action=creation', '', 1), '', 0, 0, 0, 1);
    print '<table style="width : 100%" class="tagtable liste">';
	print '<tr class="liste_titre">';
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_nom.'" name="recherche_nom" ></td>';
	print '<td align="center" style="padding: 5px; width: '.$largeur.';"></td>';
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<select style="padding: 5px; width: 120px;" name="recherche_type" >';
    print '<option value="0"></option>';

    $sql_type = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."type_entite ORDER BY fk_parent";
    $res_type = $db->query($sql_type);
    if($res_type){
        $nb = $db->num_rows($res_type);
        $a = 0;
        while ($a < $nb) {
            $obj_type = $db->fetch_object($res_type);
            if($recherche_type == $obj_type->rowid)
                print '<option value="'.$obj_type->rowid.'" selected>'.$obj_type->nom.'</option>';
            else 
                print '<option value="'.$obj_type->rowid.'">'.$obj_type->nom.'</option>';

            $a ++;
        }

    }
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<select style="padding: 5px; width: 120px;" name="recherche_parent" >';
    print '<option value="0"></option>';

    $sql_ent = "SELECT rowid, nom FROM ".MAIN_DB_PREFIX."entite WHERE actif=1";
    $res_ent = $db->query($sql_ent);
    if($res_ent){
        $nb = $db->num_rows($res_ent);
        $a = 0;
        while ($a < $nb) {
            $obj_ent = $db->fetch_object($res_ent);
            if($recherche_parent == $obj_ent->rowid)
                print '<option value="'.$obj_ent->rowid.'" selected>'.$obj_ent->nom.'</option>';
            else 
                print '<option value="'.$obj_ent->rowid.'">'.$obj_ent->nom.'</option>';

            $a ++;
        }

    }
    print '</select></td>';

	print '<td align="center" rowspan="2">';
	print '	<select style="padding: 5px; width: 120px;" name="recherche_etat">';
    print '<option value="0"></option>';
	print '<option value="active" '.$active.'>Active</option>';
	print '<option value="inactive" '.$desactive.'>Inactive</option></select><br>';
	print '<input type="submit" class="button" value="Rechercher" style="padding: 4px" >';
	print "</form>";
	print '<br>	<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=entite" class="button" style="padding: 4px" >Annuler</a>';
	print '</td></tr>';

 print '<tr class="liste_titre">';
 print '<td align="center" style="25%; color: darkblue;" ><label>Nom</label></td>';
 print '<td align="center" style="25%; color: darkblue;" ><label>Description</label></td>';
 print '<td align="center" style="25%; color: darkblue;" ><label>Type</label></td>';
  print '<td align="center" style="25%; color: darkblue;" ><label>Entite parent</label></td>';

 print '</tr>';
	$acts[0] = "activation";
	$acts[1] = "desactivation";
	$actl[0] = img_picto($langs->trans("Disabled"), 'switch_off', 'class="size30x"');
	$actl[1] = img_picto($langs->trans("Activated"), 'switch_on', 'class="size30x"');

		$num = count($obj_liste);
		$i = $arret;
		$num = $db->num_rows($result_entite);
		while ($i < $num){
                print '<tr class="impair">';
				if($obj_liste[$i]->actif == 0)
					print ''.affiche_long_texte(img_picto("", "statut7_red", "class='paddingright pictofixedwidth'"), $obj_liste[$i]->nom, 0, '', 'nom', '', '', '', '').'';
                else print ''.affiche_long_texte(img_picto("", "statut3", "class='paddingright pictofixedwidth'"), $obj_liste[$i]->nom, 0, '', 'nom', '', '', '', '').'';
                print ''.affiche_long_texte('',  $obj_liste[$i]->commentaire, 1, '', '', '', '', '', '');

				$nom_type_entite = "Entité";
                $sql = "SELECT * FROM ".MAIN_DB_PREFIX."type_entite WHERE rowid=".$obj_liste[$i]->fk_type_entite;
                $res = $db->query($sql);
                if($res){
                    $obj_type_entite = $db->fetch_object($res);
                    $nom_type_entite = $obj_type_entite->nom;

                }

				print '<td align="center">'.$nom_type_entite.'</td>';

				//entite parent
				$nom_parent = "Racine";
				if($obj_liste[$i]->fk_parent != 0){
					$sql = "SELECT * FROM ".MAIN_DB_PREFIX."entite WHERE rowid=".$obj_liste[$i]->fk_parent;
					$res = $db->query($sql);
					if($res){
						$obj_type_entite = $db->fetch_object($res);
						$nom_parent = $obj_type_entite->nom;

					}
				}


				//le nombre d'utilisateur affectés à cette entite
				$nb_user = 0;
				$sql = "SELECT COUNT(*) as nb_user FROM ".MAIN_DB_PREFIX."entite WHERE actif = 0 AND fk_user_impute IS NOT NULL AND fk_parent = ".$obj_liste[$i]->rowid;
				$res = $db->query($sql);
				if ($res) {
					$obj_result = $db->fetch_object($res);
					$nb_user = $obj_result->nb_user;
				}

				print '<td align="center">'.$nom_parent.'</td>';
				print '<td align="center">';
				if($user->hasRight("depensesimputations", "entites", "write")){
					if($obj_liste[$i]->actif == 1){
						print '<a class="reposition editfielda" title="Modifier l\'entité" href="'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=listeentite&id_entite='.$obj_liste[$i]->rowid.'&action=modifier_entite">'.img_edit('Modifier','').'</a>';
						print '&nbsp;&nbsp;<a class="reposition editfielda" title="Activer l\'entité" href="'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=listeentite&id_entite='.$obj_liste[$i]->rowid.'&nb_user='.$nb_user.'&action='.$acts[$obj_liste[$i]->actif].'">'.$actl[$obj_liste[$i]->actif].'</a>';
						print '&nbsp;&nbsp;<a class="reposition editfielda" title="Affecter un nouvel utilisateur" class="reposition editfielda" href="./affectation_user_entite.php?mainmenu=depensesimputations&leftmenu=entite&id_entite='.$obj_liste[$i]->rowid.'">'.img_picto("Affecter ou désaffecter des utilisateurs","user").'('.$nb_user.')</span></a>';//<span class="fa fa-search-plus"></span>
					}else{
						print '<a class="reposition editfielda" title="Désactiver l\'entité" href="'.$_SERVER["PHP_SELF"].'?mainmenu=depensesimputations&leftmenu=listeentite&id_entite='.$obj_liste[$i]->rowid.'&action='.$acts[$obj_liste[$i]->actif].'">'.$actl[$obj_liste[$i]->actif].'</a>';
						print '&nbsp;&nbsp;<a class="reposition editfielda" title="Entité inactive"  href="#" disabled>'.img_picto("Affecter ou désaffecter des utilisateurs","user").'('.$nb_user.')</span></a>';//<span class="fa fa-search-plus"></span>
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
				print '<tr><td align="center" colspan="5">Aucune entité disponible</td></tr>';
			
	print '</table>';

	 //affichage du reste de la liste
	 print '<span style="float:right; margin-left: 20px;">';
        $nb = (((int)($num%$limit))==0?((int)($num/$limit)):((int)($num/$limit)+1));
	$page_link = "";
	if($num>$limit){
		if($nb_page!= 1)
			if($nb==0 && 1 < ($nb))
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=0&nbpage=1&action=recherche&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";
			else if(1 < ($nb+1))
			$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=0&nbpage=1&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";

		
		if($arret > $limit){

			
			if($nb_page-3>=0)
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".($limit*($nb_page-3))."&nbpage=".($nb_page-2)."&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>".($nb_page -2)."</b></a>&nbsp;&nbsp;";

			if($nb_page-2>=0)
						$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".($limit*($nb_page-2))."&nbpage=".($nb_page-1)."&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>".($nb_page-1)."</b></a>&nbsp;&nbsp;";
			
			
			if($nb_page-1>=0)
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".($limit*($nb_page-1))."&nbpage=".($nb_page)."&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>".($nb_page)."</b></a>&nbsp;&nbsp;";

		

			
				if(	(($nb_page+1) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".($limit*$nb_page)."&nbpage=".($nb_page+1)."&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>".($nb_page + 1)."</b></a>&nbsp;&nbsp;";

			
				if((($nb_page+2) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".($limit*($nb_page +1))."&nbpage=".($nb_page+2)."&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>".($nb_page + 2)."</b></a>&nbsp;&nbsp;";
					
				
				if((($nb_page+3) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".($limit*($nb_page+2))."&nbpage=".($nb_page+3)."&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>".($nb_page + 3)."</b></a>&nbsp;&nbsp;";

					


		}else{

			
				if( 1 <= ($nb))
					
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=0&nbpage=1&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>1</b></a>&nbsp;&nbsp;";
			
			
				if(2 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".$limit."&nbpage=2&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>2</b></a>&nbsp;&nbsp;";
			
			
				if(3 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".($limit*2)."&nbpage=3&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>3</b></a>&nbsp;&nbsp;";
				
				if(4 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".($limit*3)."&nbpage=4&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>4</b></a>&nbsp;&nbsp;";

				if(5 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".($limit*4)."&nbpage=5&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'><b>5</b></a>&nbsp;&nbsp;";



		}
		if($nb_page != ($nb)  )
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".($limit*($nb-1))."&nbpage=".($nb)."&action=recherche&action=rechercher&recherche_nom=".$recherche_nom."&recherche_type=".$recherche_type."&recherche_parent=".$recherche_parent."&recherche_etat=".$recherche_etat."' style='padding: 5px'>      <b>Fin</b></a>&nbsp;&nbsp;";

		
		/*if($limit == ($arret +1))
			$page_link .= "<a style='background-color: yellow; padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".(($arret +1) -$limit)."'>".((int)(($arret+1)/$limit))."</a>";
		else $page_link .= ($arret +1 - $limit)>?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".(($arret +1) -$limit)."' style='padding: 5px'><b>".((int)(($arret+1)/$limit))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*2 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*2)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*3 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".(($arret +1)*3 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*3)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= "<a style='padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=depensesimputations&leftmenu=entite&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."'><b>></b>&nbsp;&nbsp;</a>";
	*/}
	print $page_link.'</span>';
}

//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}
