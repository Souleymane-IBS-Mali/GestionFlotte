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
require_once DOL_DOCUMENT_ROOT.'/custom/gestionflotte/core/modules/modgestionflotte.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/gestionflotte/class/html.form.class.php';


$action = GETPOST('action', 'alpha')?:"liste";
$id_carburant = GETPOST('id_carburant', 'int');
$aujourdhui = date("Y-m-d");

$monform = new Form1($db);

$message = '';
//--------------------------------------------
$id_vehicule = GETPOST('id_vehicule','int');
$limit = GETPOST('limit','alpha')?:20;
$arret = GETPOST('arret','int')?:0;
$nb_page = GETPOST('nbpage','int')?:1;
$tri = GETPOST('tri','alpha');
$etat = GETPOST('etat','alpha');

$date_debut = GETPOST('date_debut');
$date_fin = GETPOST('date_fin');

$recherche_vehicule = GETPOST("recherche_vehicule", "int");
$recherche_type_carburant = GETPOST("recherche_type_carburant", "int");
$recherche_libelle = GETPOST("recherche_libelle", "alpha");
$recherche_date_demande = GETPOST("recherche_date_demande");

llxHeader("", "Gestion Vehicule");
//Titre
//print load_fiche_titre($langs->trans("Détails Carburant"), '', '');

$head = vehicule_Head($id_vehicule);
	print dol_get_fiche_head($head, 'carburant', "", -1, '');

	$obj_soc = prepare_objet_entete($id_vehicule, $db);
    entete_vehicule($obj_soc, 'commande');
	print '<hr>';

	$vehicule = "SELECT * FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid=".$id_vehicule;
		
	$result_vehicule = $db->query($vehicule);//= $db->query($covSql);

	if($result_vehicule)
		$obj_vehicule = $db->fetch_object($result_vehicule);

	if(empty($recherche_vehicule))
		$recherche_vehicule = $obj_vehicule->reference_interne;

	if(empty($recherche_type_carburant))
		$recherche_type_carburant = $obj_vehicule->fk_type_carburant;
	
	$obj_liste = array();
    //print_barre_liste("", $page, $_SERVER["PHP_SELF"], "", "", "", "", "", "", 'bill', 0, dolGetButtonTitle("Créer un nouveau type de salarié", '', 'fa fa-plus-circle', $_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&action=creation', '', 1), '', 0, 0, 0, 1);

		$sql_vehicule  = 'SELECT cb.*, vh.rowid as vhrowid, vh.nom as vhnom, vh.img, vh.reference_interne, vh.fk_type_carburant, tc.nom AS nom_type_carburant';
		$sql_vehicule .= ' FROM '.MAIN_DB_PREFIX.'carburant_vehicule AS cb';
		$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'vehicule AS vh ON cb.fk_vehicule = vh.rowid';
		$sql_vehicule .= ' LEFT JOIN '.MAIN_DB_PREFIX.'type_carburant AS tc ON vh.fk_type_carburant = tc.rowid';

		if($user->id == 1 || $user->hasRight("gestionflotte", "gestionvehicule", "read"))
			$sql_vehicule .= ' WHERE 1 = 1';
		else $sql_vehicule = ' WHERE fk_user_creation='.$user->id;

	/*if($user->id == 1 || $user->hasRight("gestionflotte", "gestionvehicule", "read"))
		$sql_vehicule = 'SELECT * FROM '.MAIN_DB_PREFIX.'carburant_vehicule WHERE 1 = 1';
	else $sql_vehicule = 'SELECT * FROM '.MAIN_DB_PREFIX.'carburant_vehicule WHERE fk_user_creation='.$user->id;*/

	if($libelle){
		$sql_vehicule .= ' AND cb..libelle LIKE "%'.$libelle.'%"';
	}
		$sql_vehicule .= ' AND vh.reference_interne LIKE "%'.$recherche_vehicule.'%"';

	if($recherche_type_carburant){
		$sql_vehicule .= ' AND vh.fk_type_carburant = '.$recherche_type_carburant;
	}

	if($date_debut){
		$sql_vehicule .= ' AND date_demande >= "'.$date_debut.'"';
	}

	if($date_fin){
		$sql_vehicule .= ' AND date_demande <= "'.$date_fin.'"';
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
		$sql_vehicule .= ' AND date_fin > "'.$aujourdhui.'"';
	elseif($etat == "expire")
		$sql_vehicule .= ' AND date_fin < "'.$aujourdhui.'"';

		if($tri == 'reference')
			$sql_vehicule .= " ORDER BY vh.reference_interne";
		elseif($tri == 'libelle')
			$sql_vehicule .= " ORDER BY cb.libelle";
		elseif($tri == 'type_carburant')
			$sql_vehicule .= " ORDER BY tc.nom";
		elseif($tri == 'date_demande')
			$sql_vehicule .= " ORDER BY cb.date_demande";
		elseif($tri == 'cout')
			$sql_vehicule .= " ORDER BY cb.cout";
		elseif($tri == 'kilometre')
			$sql_vehicule .= " ORDER BY cb.kilometre";
		else $sql_vehicule .= " ORDER BY cb.date_creation";
	
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
		print '<form name="ajouter" method="POST" action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule='.$id_vehicule.'">';
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
					window.location.href = "'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&limit="+limit+"&action=rechercher&recherche_vehicule='.$recherche_vehicule.'&recherche_user='.$recherche_user.'&recherche_date_debut='.$recherche_date_debut.'&recherche_date_fin='.$recherche_date_fin.'";
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

	//libelle
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_libelle.'" name="recherche_libelle" ></td>';

	/*//libelle
	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="text" Placeholder="" value="'.$recherche_vehicule.'" name="recherche_vehicule" ></td>';
*/
	//refer
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
	<input style="padding:5px; width: 100px;" type="number" step="any" Placeholder="" value="'.$recherche_cout.'" name="recherche_cout" ></td>';

	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="number" step="any" Placeholder="" value="'.$recherche_kilometre.'" name="recherche_kilometre" ></td>';

	print '<td align="center" style="padding: 5px; width: '.$largeur.';">
	<input style="padding:5px; width: 100px;" type="date" Placeholder="" value="'.$date_debut.'" name="date_debut" ><br>
	<input style="padding:5px; width: 100px;" type="date" Placeholder="" value="'.$date_fin.'" name="date_fin" ></td>';
    
	if($etat == "encours")
		$active = "selected";
	elseif($etat == "expire")
		$desactive = "selected";
	print '<td align="center" rowspan="2">';
	/*print '	<select style="padding: 5px; width: 120px;" name="etat">';
    print '<option value="0"></option>';
	print '<option value="encours" '.$active.'>En cours</option>';
	print '<option value="expire" '.$desactive.'>Expirée</option></select><br>';*/
	print '<input type="submit" class="button" value="Rechercher" style="padding: 4px" >';
	print "</form>";
	print '<br>	<a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule" class="button" style="padding: 4px" >Annuler</a>';
	print '</td></tr>';

 	print '<tr class="liste_titre">';
	print '<td align="center" style="" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=libelle" style="" >libelle</label></a></td>';
	//print '<td align="center" style="" ><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=reference" style="" ><label>Ref. véhicule</label></a></td>';
	print '<td align="center" style="" ><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=type_carburant" style="" ><label>Type carb.</label></a></td>';
	print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=cout" style="" >Cout</a></label></td>';
	print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=kilometre" style="" >Kilométrage</a></label></td>';
	print '<td align="center" style="25%; color: darkblue;" ><label><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&tri=date_demande" style="" >Date demande</a></label></td>';

 	print '</tr>';

		$num = count($obj_liste);
		$i = $arret;
		while ($i < $num){
            print '<tr class="impair">';
			//print '<td align="center"><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_carburant='.$obj_liste[$i]->rowid.'&action=detail">'.$obj_liste[$i]->libelle.'</a></td>';
			print affiche_long_texte('', $obj_liste[$i]->libelle, 0, '../carburant.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_carburant='.$obj_liste[$i]->rowid.'&action=detail', 'nom', '', '', '', '');

			$sql_veh = "SELECT rowid, reference_interne, img FROM ".MAIN_DB_PREFIX."vehicule WHERE rowid = ".$obj_liste[$i]->fk_vehicule;
			$res_veh = $db->query($sql_veh);
			if($res_veh)
				$obj_veh = $db->fetch_object($res_veh);

			/*if($obj_veh->img)
				$photo = img_picto($obj_liste[$i]->vhnom, 'image_vehicule/'.$obj_liste[$i]->img, 'style="width:30px; height:auto;"');
			else $photo = "";
				print '<td align="center">'.$photo.' <a href="./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_carburant='.$obj_liste[$i]->vhrowid.'">'.$obj_liste[$i]->reference_interne.'</a></td>';

				*/

			print '<td align="center">'.$obj_liste[$i]->nom_type_carburant.'</td>';
			print '<td align="center">'.price($obj_liste[$i]->cout).'</td>';
			print '<td align="center">'.$obj_liste[$i]->kilometre.' km</td>';
				print '<td align="center">'.$obj_liste[$i]->date_demande.'</td>';

				print '<td align="center">';
				if($user->hasRight("gestionflotte", "gestionvehicule", "write")){
						print '<a class="reposition editfielda" title="Modifier l\'Véhicule" href="../carburant.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_carburant='.$obj_liste[$i]->rowid.'&action=modifier">'.img_edit('Modifier','').'</a>';
						print '&nbsp;&nbsp;<a class="reposition editfielda" title="Activer l\'Véhicule" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=listevehicule&id_carburant='.$obj_liste[$i]->rowid.'&action='.$acts[$obj_liste[$i]->actif].'">'.$actl[$obj_liste[$i]->actif].'</a>';
					
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
				print '<tr><td align="center" colspan="6">Aucun enregistrement trouvé</td></tr>';
			
	print '</table>';

	 //affichage du reste de la liste
	 print '<span style="float:right; margin-left: 20px;">';
        $nb = (((int)($num%$limit))==0?((int)($num/$limit)):((int)($num/$limit)+1));
	$page_link = "";
	if($num>$limit){
		if($nb_page!= 1)
			if($nb==0 && 1 < ($nb))
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=0&nbpage=1&action=recherche&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";
			else if(1 < ($nb+1))
			$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=0&nbpage=1&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>Debut</b>    </a>&nbsp;&nbsp;";

		
		if($arret > $limit){

			
			if($nb_page-3>=0)
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb_page-3))."&nbpage=".($nb_page-2)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>".($nb_page -2)."</b></a>&nbsp;&nbsp;";

			if($nb_page-2>=0)
						$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb_page-2))."&nbpage=".($nb_page-1)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>".($nb_page-1)."</b></a>&nbsp;&nbsp;";
			
			
			if($nb_page-1>=0)
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb_page-1))."&nbpage=".($nb_page)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>".($nb_page)."</b></a>&nbsp;&nbsp;";

		

			
				if(	(($nb_page+1) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*$nb_page)."&nbpage=".($nb_page+1)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 1)."</b></a>&nbsp;&nbsp;";

			
				if((($nb_page+2) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb_page +1))."&nbpage=".($nb_page+2)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 2)."</b></a>&nbsp;&nbsp;";
					
				
				if((($nb_page+3) <= ($nb)))
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb_page+2))."&nbpage=".($nb_page+3)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>".($nb_page + 3)."</b></a>&nbsp;&nbsp;";

					


		}else{

			
				if( 1 <= ($nb))
					
					$page_link .= "<a style='background-color: yellow;' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=0&nbpage=1&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>1</b></a>&nbsp;&nbsp;";
			
			
				if(2 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".$limit."&nbpage=2&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>2</b></a>&nbsp;&nbsp;";
			
			
				if(3 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*2)."&nbpage=3&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>3</b></a>&nbsp;&nbsp;";
				
				if(4 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*3)."&nbpage=4&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>4</b></a>&nbsp;&nbsp;";

				if(5 <= ($nb))
					
					$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*4)."&nbpage=5&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'><b>5</b></a>&nbsp;&nbsp;";



		}
		if($nb_page != ($nb)  )
				$page_link .= "<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".($limit*($nb-1))."&nbpage=".($nb)."&action=recherche&action=rechercher&recherche_vehicule=".$recherche_vehicule."&recherche_user=".$recherche_user."&recherche_date_debut=".$recherche_date_debut."&recherche_date_fin=".$recherche_date_fin."&recherche_type_carburant=".$recherche_type_carburant."&tri=".$tri."' style='padding: 5px'>      <b>Fin</b></a>&nbsp;&nbsp;";

		
		/*if($limit == ($arret +1))
			$page_link .= "<a style='background-color: yellow; padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".(($arret +1) -$limit)."'>".((int)(($arret+1)/$limit))."</a>";
		else $page_link .= ($arret +1 - $limit)>?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".(($arret +1) -$limit)."' style='padding: 5px'><b>".((int)(($arret+1)/$limit))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*2 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*2)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= ((($arret +1)*3 -$limit) < $num)?"<a href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".(($arret +1)*3 -$limit)."' style='padding: 5px'><b>".(((int)((($arret+1)*3)/$limit)))."</b></a>&nbsp;&nbsp;":"";
		$page_link .= "<a style='padding: 5px' href='".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=listevehicule&limit=".$limit."&arret=".(($arret +1)*2 -$limit)."'><b>></b>&nbsp;&nbsp;</a>";
	*/}
	print $page_link.'</span>';

print $db->error();

//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}
