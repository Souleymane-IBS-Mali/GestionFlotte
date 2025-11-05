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
$id_type_maintenance = GETPOST('id_type_maintenance', 'int');
$monform = new Form1($db);

$message = '';

$nom = ["Vidange moteur", "Maintenance réglementaire", "Maintenance curative", "Maintenance prédictive", "Maintenance corrective", "Maintenance préventive"];
$description = ["La vidange consiste à remplacer l’huile moteur usagée par de l’huile neuve afin d’assurer la bonne lubrification du moteur. Cette opération permet de réduire l’usure des pièces, d’améliorer les performances du véhicule et d’allonger la durée de vie du moteur.",
 "Contrôles et entretiens obligatoires imposés par la réglementation (visite technique, contrôle tachygraphe, vérification extincteur, etc.).",
 "Ensemble des actions visant à remettre le véhicule dans un état de fonctionnement normal après une panne constatée.",
 "Intervention planifiée en fonction des données réelles de performance et d’usure (analyse des capteurs, alertes kilométriques, données GPS), pour intervenir juste avant une panne probable.",
 "Réparation effectuée après la détection d’une panne ou d’un dysfonctionnement (remplacement d’un alternateur défectueux, réparation de fuite, changement de batterie, etc.).",
 "Ensemble des opérations planifiées visant à éviter les pannes et prolonger la durée de vie du véhicule (vidanges, contrôle des freins, changement de filtres, vérification des niveaux, etc.)."];
$sql_type_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."type_maintenance";
$restype_maintenance = $db->query($sql_type_maintenance);
if($restype_maintenance){
	$nb = $db->num_rows($restype_maintenance);
	if ($nb <= 0) {
		for ($i=0; $i < count($nom); $i++) { 
			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'type_maintenance (libelle, commentaire, fk_user_creation) VALUES("'.$nom[$i].'","'.$description[$i].'", '.$user->id.')';
			$result = $db->query($sql);
		}
	}
}


if($action == "add_type_maintenance"){
		$libelle = GETPOST('libelle', 'alpha');
        $description = GETPOST('description', 'alpha');
		if(empty($libelle))
			$message = 'Le champ "libelle" est oblogatoire<br>';

		if(empty($description))
			$message = 'Le champ "DESCRIPTION" est oblogatoire<br>';
		
        if(empty($message)){
            $sql = 'INSERT INTO '.MAIN_DB_PREFIX.'type_maintenance (libelle, commentaire, fk_user_creation) VALUES("'.$libelle.'","'.$description.'", '.$user->id.')';
		    $result = $db->query($sql);
			//print $sql;
            if($result){
                $message = "Un nouveau type maintenance créé avec succès";
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


if($action == "save_modif_type_maintenance"){
		$libelle = GETPOST('libelle', 'alpha');
        $description = GETPOST('description', 'alpha');

		if(empty($libelle))
			$message = 'Le champ "libelle" est oblogatoire<br>';
		
		if(empty($description))
			$message = 'Le champ "DESCRIPTION" est oblogatoire<br>';

        if(empty($message)){
            $sql = 'UPDATE '.MAIN_DB_PREFIX.'type_maintenance SET libelle="'.$libelle.'", commentaire="'.$description.'" WHERE rowid='.$id_type_maintenance;
		    $result = $db->query($sql);
			//print $sql;
            if($result){
                $message = "Un type maintenance modifié avec succès";
                $action = "liste";
            }else{
                $action = "modifier_type_maintenance";
                $message = "Un problème est survenu";
            }
        }else{
            $action = "modifier_type_maintenance";
        }
        print $db->error();
}


//Suppression des lignes de besoin
if($action == "attente_type_suppression"){

    $url = $_SERVER['PHP_SELF']."?mainmenu=gestionflotte&leftmenu=type_maintenance&id_type_maintenance=".$id_type_maintenance;
    $titre = 'Veuillez confirmer la suppression';

      $formconfirm = $monform->formconfirm(
          $url, 
          $titre, 
          "", 
          'supprimer_type_maintenance_ok', 
          $array, 
          '', 
          1,
          100,
          '30%'
      );
      print $formconfirm;
      $action = "liste";
}

if($action == "supprimer_type_maintenance_ok"){ 

    //suppression
    $sql = "DELETE FROM ".MAIN_DB_PREFIX."type_maintenance WHERE rowid=".$id_type_maintenance;
    $result = $db->query($sql);
    if($result)
        $message = 'Type maintenance supprimé avec succès';
    else    $message = 'Un problème est survenu';

    $action = "liste";
}


if($action == "creation"){
	print load_fiche_titre($langs->trans("Ajout d'un nouveau type de maintenance véhicule"), '', '').img_picto('', 'maintenance', 'class="paddingright pictofixedwidth"');
		print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i>';
		print "<hr><br>";
	print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance" method="post">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add_type_maintenance">';

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>libelle</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><input type="text" id="libelle" name="libelle" value="'.GETPOST("libelle").'"/></td></tr>';

	print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>Description</label></td>';
	print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><textarea name="description" style="width:300px; height: 80px;" name="description" >'.GETPOST("description").'</textarea></td></tr>';
	print '</table>';
	print '<hr>';
	print '
		<div style="text-align: center"; align-items: center; justify-content: center">
        <input class="button" type="submit" value="Créer" name=""/>
        </form>
        <a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance&action=liste" class="button">Annuler</a></td></tr>
    </div>
    ';
}

if($action == "modifier_type_maintenance"){
		print load_fiche_titre($langs->trans("Modification d'un type de maintenance véhicule"), '', '');

	$sql_maintenance = "SELECT * FROM ".MAIN_DB_PREFIX."type_maintenance WHERE rowid=".$id_type_maintenance;
	$result_maintenance = $db->query($sql_maintenance);//= $db->query($covSql);

	if($result_maintenance && $id_type_maintenance){
		$obj_type_maintenance = $db->fetch_object($result_maintenance);

		print '<span style="color: red">*</span> <i>Tous les champs en gras sont obligatoires</i>';
		print "<hr><br>";
		print '<table><form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance&id_type_maintenance='.$id_type_maintenance.'" method="post">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<input type="hidden" name="action" value="save_modif_type_maintenance">';

		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>libelle</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><input type="text" id="libelle" name="libelle" value="'.(GETPOST("libelle")?:$obj_type_maintenance->libelle).'"/></td></tr>';

		print '<tr><td style="width: 200px; padding-right: 30px; padding-bottom: 10px" class="fieldrequired"><label>Description</label></td>';
		print '<td style="width: 600px; padding-right: 30px; padding-bottom: 10px"><textarea name="description" style="width:300px; height: 80px;" name="description" >'.(GETPOST("description")?:$obj_type_maintenance->commentaire).'</textarea></td></tr>';

		print '</table>';
		print '<hr>';
		print '
			<div style="text-align: center"; align-items: center; justify-content: center">
			<input class="button" type="submit" value="Valider" name=""/>
			</form>
			<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance&action=liste" class="button">Annuler</a></td></tr>
		</div>
		';
	}
}

$limit = GETPOST('limit','alpha')?:20;
$arret = GETPOST('arret','int')?:0;
$nb_page = GETPOST('nbpage','int')?:1;

				$recherche_libelle = GETPOST("recherche_libelle", "alpha");
				$recherche_description = GETPOST("recherche_description", "alpha");

if($action == "liste" || $action == "rechercher"){
	$obj_liste = array();

	$sql_type_maintenance = "SELECT * FROM ".MAIN_DB_PREFIX."type_maintenance";

	if($user->id == 1 || $user->hasRight("gestionflotte", "gestionvehicule", "read"))
		$sql_type_maintenance .= " WHERE 1=1";
	else $sql_type_maintenance .= " WHERE 1=1 AND fk_user=".$user->id;

	if($recherche_libelle){
		$sql_type_maintenance .= ' AND libelle LIKE "%'.$recherche_libelle.'%"';
	}

	if($recherche_description){
		$sql_type_maintenance .= ' AND commentaire LIKE "%'.$recherche_description.'%"';
	}
	

	if($tri){
		if($tri == "nom")
			$sql_type_maintenance .= " ORDER BY libelle";
		elseif($tri == "commentaire")
			$sql_type_maintenance .= " ORDER BY commentaire";
		
	}else $sql_type_maintenance .= " ORDER BY date_creation DESC";

	$result_type_maintenance = $db->query($sql_type_maintenance);
	//print $sql_type_maintenance;
	$j = 0;
	if($result_type_maintenance){
		$num = $db->num_rows($result_type_maintenance);
		while ($j < $num){
			$obj_liste[] = $db->fetch_object($result_type_maintenance);
            $j ++;
		}
	}
		//Titre
		print load_fiche_titre($langs->trans("Liste des types de maintenance(".$num.")"), '', '');
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
		print '<form name="ajouter" method="POST" action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance">';
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
					window.location.href = "'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance&limit="+limit+"&action=rechercher&recherche_libelle='.$recherche_libelle.'&recherche_description='.$recherche_description.'";
				},
				false,
				);
				</script>';
			print "</select>";
			print "</div><br><br>";
    
    $largeur = "12,5px";
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau Besoin", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance&action=creation', '', 1), '', 0, 0, 0, 1);
    print '<table style="width : 100%" class="tagtable liste">';
	print '<tr class="liste_titre">';
	print '<td style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_libelle.'" name="recherche_libelle" ></td>';
	print '<td style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_description.'" name="recherche_description" ></td>';
	print '<td align="center" rowspan="2"><input type="submit" class="button" value="Rechercher" style="padding: 4px" >';
	print "</form>";
	print '<br>	<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance" class="button" style="padding: 4px" >Annuler</a>';
	print '</td></tr>';
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau type de salarié", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance&action=creation', '', 1), '', 0, 0, 0, 1);
	print '<tr class="liste_titre">';
 	print '<td style="25%; color: darkblue;"><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance&tri=nom" title="Trié par nom">Nom</a></label></td>';
	print '<td style="25%; color: darkblue;"><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance&tri=commentaire" title="Trié par description">Description</a></label></td>';
 	print '</tr>';

 	$acts[0] = "activation";
	$acts[1] = "desactivation";
	$actl[0] = img_picto($langs->trans("Disabled"), 'switch_off', 'class="size30x"');
	$actl[1] = img_picto($langs->trans("Activated"), 'switch_on', 'class="size30x"');
 $num = count($obj_liste);
		$i = $arret;
		while ($i < $num){
                print '<tr class="impair">';
                print ''.affiche_long_texte(img_picto("", "statut7_blue", "class='paddingright pictofixedwidth'"), $obj_liste[$i]->libelle, 0, '', 'libelle', '', '', '', '').'';
                print ''.affiche_long_texte('',  $obj_liste[$i]->commentaire, 1, '', '', '', '', '', '');

				print '<td align="center">';
				if($user->hasRight("gestionflotte", "gestionvehicule", "write")){
					print '<a class="reposition editfielda" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance&id_type_maintenance='.$obj_liste[$i]->rowid.'&action=modifier_type_maintenance">'.img_edit('Modifier','').'</a>';
					//print '&nbsp;&nbsp;<a class="reposition editfielda" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=type_maintenance&id_type_maintenance='.$obj_liste[$i]->rowid.'&action=attente_type_suppression">'.img_delete('Supprimer','').'</a>';
					//print '&nbsp;&nbsp;<a class="reposition editfielda" href="./creation_maintenance.php?mainmenu=gestionflotte&leftmenu=type_maintenance&recherche_type='.$obj_liste[$i]->rowid.'&action=rechercher">'.img_picto("maintenances", "statut3")."(".$nb_maintenance.")</a>";
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
				print '<tr><td align="center" colspan="3">Aucun Type maintenance disponible</td></tr>';
			

	print '</table>';
	 //affichage du reste de la liste
	 print '<span style="float:right; margin-left: 20px;">';
        $nb = (((int)($num%$limit))==0?((int)($num/$limit)):((int)($num/$limit)+1));
	$page_link = "";
	if($num>$limit){
		if($nb_page!= 1)
			if($nb==0 && 1 < ($nb))
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=0&nbpage=1&action=recherche&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";
			else if(1 < ($nb+1))
			$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=0&nbpage=1&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";

		
		if($arret > $limit){

			
			if($nb_page-3>=0)
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".($limit*($nb_page-3))."&nbpage=".($nb_page-2)."&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>".($nb_page -2)."</b></a>&nbsp;&nbsp;";

			if($nb_page-2>=0)
						$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".($limit*($nb_page-2))."&nbpage=".($nb_page-1)."&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>".($nb_page-1)."</b></a>&nbsp;&nbsp;";
			
			
			if($nb_page-1>=0)
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".($limit*($nb_page-1))."&nbpage=".($nb_page)."&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>".($nb_page)."</b></a>&nbsp;&nbsp;";

		

			
				if(	(($nb_page+1) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".($limit*$nb_page)."&nbpage=".($nb_page+1)."&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>".($nb_page + 1)."</b></a>&nbsp;&nbsp;";

			
				if((($nb_page+2) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".($limit*($nb_page +1))."&nbpage=".($nb_page+2)."&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>".($nb_page + 2)."</b></a>&nbsp;&nbsp;";
					
				
				if((($nb_page+3) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".($limit*($nb_page+2))."&nbpage=".($nb_page+3)."&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>".($nb_page + 3)."</b></a>&nbsp;&nbsp;";

					


		}else{

			
				if( 1 <= ($nb))
					
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=0&nbpage=1&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>1</b></a>&nbsp;&nbsp;";
			
			
				if(2 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".$limit."&nbpage=2&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>2</b></a>&nbsp;&nbsp;";
			
			
				if(3 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".($limit*2)."&nbpage=3&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>3</b></a>&nbsp;&nbsp;";
				
				if(4 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".($limit*3)."&nbpage=4&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>4</b></a>&nbsp;&nbsp;";

				if(5 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".($limit*4)."&nbpage=5&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'><b>5</b></a>&nbsp;&nbsp;";



		}
		if($nb_page != ($nb)  )
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".($limit*($nb-1))."&nbpage=".($nb)."&action=recherche&&action=rechercher&recherche_libelle=".$recherche_libelle."&recherche_description=".$recherche_description."' style='padding: 5px'>      <b>Fin</b></a>&nbsp;&nbsp;";

		
		/*if($limit == ($arret +1))
			$page_link .= "<a style='background-color: yellow; padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".(($arret +1) -$limit)."'>".((int)(($arret+1)/$limit))."</a>";
		else $page_link .= ($arret +1 - $limit)>?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".(($arret +1) -$limit)."' style='padding: 5px'><b>".((int)(($arret+1)/$limit))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*2 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*2)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*3 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".(($arret +1)*3 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*3)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= "<a style='padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=type_maintenance&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."'><b>></b>&nbsp;&nbsp;</a>";
	*/}
	print $page_link.'</span>';
}
//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}