<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
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
 *	\file       gestionflotte/gestionflotteindex.php
 *	\ingroup    gestionflotte
 *	\brief      Home page of gestionflotte top menu
 */
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/gestionflotte/lib/gestionflotte.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/gestionflotte/class/html.form.class.php';

/*
 * View
 */

$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');
$message = '';
$form = new Form($db);

//Enregistrement des types d'alerte par defaut
    $array_type = ["kilometrage"];
	$array_val = ["100"];
	$ind = 0;
    $covSql = "SELECT * FROM ".MAIN_DB_PREFIX."gestion_flotte_alerte";
    $result = $db->query($covSql);               
	if($result){
		$num = $db->num_rows($result);
		if($num == 0){
			foreach ($array_type as $key => $value) {
				$sql_insert = 'INSERT INTO '.MAIN_DB_PREFIX.'gestion_flotte_alerte (nom, valeur, fk_user)';
				$sql_insert .= ' VALUES("'.$value.'", "'.$array_val[$ind].'", '.$user->id.')';
				$result_insert = $db->query($sql_insert);
				$ind ++;
			}
		}
	}

if($action == 'edit_Kilometrage'){
	$km = GETPOST('kilometrage', 'int');
	if(empty($km)){
		$message = 'Le champ "KILOMETRAGE" est obligatoire';
	}
	if(empty($message)){
		$sql = 'UPDATE '.MAIN_DB_PREFIX."gestion_flotte_alerte SET valeur = '".$km."' WHERE nom = 'kilometrage'";
		if($db->query($sql)){
			$message = 'Marge de kilométrage modifiée avec succès';
			header("Location: ".$_SERVER["PHP_SELF"]."?mainmenu=gestionflotte&leftmenu=configuration&action&action=maintenance&message=".$message);

		}else{
			$message = 'Un problème est survenu';
		}
	}
	$action = 'maintenance';

}

$erreur = $db->error();
if(empty($message))
	$message = GETPOST('message', 'alpha');
llxHeader("", "Configuration", '', '', 0, 0, '', '', '', 'mod-gestionflotte page-index');
print '<div><span class="error">'.$erreur.'</span></div>';

if(empty($action)){

print load_fiche_titre($langs->trans("Maintenance"), '', 'gestionflotte.png@gestionflotte');
print '<div class="div-table-responsive-no-min">';
                    print '<table class="noborder centpercent">';
                    // Line for title
                    print '<!-- line title to add new entry -->';
                    print '<tr class="liste_titre">';
                    print '<th>Configuration des besoins</th><th></th><th></th>';
                    print '</tr>';

                    /*print '<tr class="oddeven nodrag nodrop nohover">';
                    print '<td><a href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=configuration&action=modele_numerotation" >Modèles de numérotations des besoins</a></td>';
                    print '<td></td>';
                    print '<td></td>';

                    print '</tr>';
                    print '<tr class="oddeven nodrag nodrop nohover">';
                    print '<td><a title="Constutition des chiffres sur le bulletin" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=configuration&action=modele_documents" >Modèles de document des besoins</a></td>';
                    print '<td></td>';
                    print '<td></td>';
                    print '</tr>';*/

                    print '</tr>';
                    print '<tr class="oddeven nodrag nodrop nohover">';
                    print '<td><a title="Alerté les utilisateurs dans les traitements des besoins" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=configuration&action=maintenance" >Maintenances</a></td>';
                    print '<td></td>';
                    print '<td></td>';
                    print '</tr>';

            print '</table>';
            print '</div>';
}elseif($action == 'maintenance'){
	print load_fiche_titre($langs->trans("Maintenances"), '', '');
	$covSql = "SELECT nom, valeur FROM ".MAIN_DB_PREFIX."gestion_flotte_alerte WHERE nom='kilometrage'";
	$res = $db->query($covSql);
	if($res){      
		$obj_alerte = $db->fetch_object($res);
		
	}
    print '<hr>';
    print '<span style="float:right;"><a title="Voir configuration" href="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=configuration">Retour</a></span><br>';
    //print '<h4>Hierarchie</h4>';
    print '<table style="width : 100%" class="tagtable liste">';
    print '<tr class="liste_titre">';
    print '<td style="padding: 5px; width: '.$largeur.';" colspan="2">Vidange</td>';
	print '<td style="padding: 5px; width: '.$largeur.';">Valeur</td>';
    print '</tr>';
    //ligne 1
    print '<tr>';
    print '<form action="'.$_SERVER["PHP_SELF"].'?mainmenu=gestionflotte&leftmenu=configuration" method="post">';
    print '<input type="hidden" name="token" value="'.newToken().'">';
    print '<input type="hidden" name="action" value="edit_Kilometrage">';
    print '<td style="padding: 5px; width: '.$largeur.';">Kilométrage</td>';
    print '<td style="padding: 5px; width: '.$largeur.';">Marge kilométrique avant l’alerte de maintenance (vidange).</td>';
    print '<td>';
    
	$info = "Valeur représentant l’écart entre le kilométrage actuel et le kilométrage de maintenance, après lequel la vidange doit être effectuée.";
    print '<input type="number" name="kilometrage" size = 5 value="'.(GETPOST('kilometrage', 'int')?:$obj_alerte->valeur).'">Km<input class="button" style="padding : 2px;" type="submit" value="valider">';
    print info_admin($info, '1');
    print '</td>';
	
	print '</form>';
    print '</tr>';
}


// End of page
llxFooter();
$db->close();

if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}
