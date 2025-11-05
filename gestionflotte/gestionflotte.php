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
include_once DOL_DOCUMENT_ROOT.'/core/class/dolgraph.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/workboardresponse.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/invoice.lib.php';

llxHeader('', "Gestion Flottes");
print load_fiche_titre("Vue globale", '', 'gestionflotte.png@gestionflotte');

$aujourdhui = date('Y-m-d');

//---------------------------------------------------------------------------------------------------------

$hookmanager = new HookManager($db);
$hookmanager->initHooks(['gestionflotteindex']);

if (!getDolGlobalString('MAIN_DISABLE_GLOBAL_WORKBOARD') && getDolGlobalInt('MAIN_OPTIMIZEFORTEXTBROWSER') < 2) {
    $dashboardlines = array();

    //nb total véhicule
    if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
        $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule";
    else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE fk_user_creation=".$user->id;
        $res_vehicule = $db->query($sql_vehicule);
        if($res_vehicule){
            $num_vehicule = $db->num_rows($res_vehicule);
        }

        //nb véhicule en bon état
	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0";
	else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND fk_user_creation=".$user->id;

	$res_vehicule = $db->query($sql_vehicule);
	if($res_vehicule){
		$num_vehicule_bon_etat = $db->num_rows($res_vehicule);
	}

    //nb véhicule en panne
if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
	$sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 1";
else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 1 AND fk_user_creation=".$user->id;

	$res_vehicule = $db->query($sql_vehicule);
	if($res_vehicule){
		$num_vehicule_panne = $db->num_rows($res_vehicule);
	}

    //nb véhicule assigné
	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND rowid IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin IS NULL OR date_fin >='".$aujourdhui."')";
	else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND rowid IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin IS NULL OR date_fin >='".$aujourdhui."') AND fk_user_creation=".$user->id;

	$res_vehicule = $db->query($sql_vehicule);
	if($res_vehicule){
		$num_vehicule_assigne = $db->num_rows($res_vehicule);
	}

    // ------------------------------------------------------------------------------------------
    // Bloc État des véhicules
    $boardEtatVehicule = new WorkboardResponse($db);
    $boardEtatVehicule->img = img_picto('', 'car', 'class="paddingright pictofixedwidth"');
    $boardEtatVehicule->element = "etatvehicule";
    $boardEtatVehicule->label = "État des véhicules";
    $boardEtatVehicule->infoKey = "todo";
    $boardEtatVehicule->url = DOL_URL_ROOT.'/gestionflotte/creation_vehicule.php?etat=bonetat';
    $boardEtatVehicule->nbtodo = 0;
    $boardEtatVehicule->nbtodolate = 0;

    $board1 = clone $boardEtatVehicule;
    $board1->labelShort = "Bon état";
    $board1->nbtodo = $num_vehicule_bon_etat;
    $dashboardlines['vehicule_bon_etat'] = $board1;

    $board2 = clone $boardEtatVehicule;
    $board2->url = DOL_URL_ROOT.'/gestionflotte/creation_vehicule.php?etat=panne';
    $board2->nbtodo = $num_vehicule_panne;
    $board2->labelShort = "En panne";
    $dashboardlines['vehicule_panne'] = $board2;

    // ------------------------------------------------------------------------------------------
    // Bloc Véhicules (assignés/libres)
    $boardVehicule = new WorkboardResponse($db);
    $boardVehicule->img = img_picto('', 'truck', 'class="paddingright pictofixedwidth"');
    $boardVehicule->element = "vehicule";
    $boardVehicule->label = "Véhicules";
    $boardVehicule->infoKey = "todo";
    $boardVehicule->url = DOL_URL_ROOT.'/gestionflotte/creation_vehicule.php';
    $boardVehicule->nbtodo = 0;

    $board3 = clone $boardVehicule;
    $board3->labelShort = "Assignés";
    $board3->nbtodo = $num_vehicule_assigne;
    $dashboardlines['vehicule_assigne'] = $board3;

    $board4 = clone $boardVehicule;
    $board4->labelShort = "Libres";
    $board4->nbtodo = $num_vehicule - $num_vehicule_assigne;
    $dashboardlines['vehicule_libre'] = $board4;

    // ------------------------------------------------------------------------------------------
    // Bloc Documents
    $aujourdhui = date('Y-m-d');
    if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$sql_document = "SELECT rowid FROM ".MAIN_DB_PREFIX."document_vehicule WHERE date_expiration >='".$aujourdhui."'";
	else $sql_document = "SELECT rowid FROM ".MAIN_DB_PREFIX."document_vehicule WHERE date_expiration >='".$aujourdhui."' AND fk_user_creation=".$user->id;

	$res_document = $db->query($sql_document);
	if($res_document){
		$num_document_valide = $db->num_rows($res_document);
	}

    if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
        $sql_document = "SELECT rowid FROM ".MAIN_DB_PREFIX."document_vehicule WHERE date_expiration <'".$aujourdhui."'";
    else $sql_document = "SELECT rowid FROM ".MAIN_DB_PREFIX."document_vehicule WHERE date_expiration <'".$aujourdhui."' AND fk_user_creation=".$user->id;

        $res_document = $db->query($sql_document);
        if($res_document){
            $num_document_expire = $db->num_rows($res_document);
        }

    $boardDocument = new WorkboardResponse($db);
    $boardDocument->img = img_picto('', 'file', 'class="paddingright pictofixedwidth"');
    $boardDocument->element = "document";
    $boardDocument->label = "Documents";
    $boardDocument->infoKey = "todo";
    $boardDocument->url = DOL_URL_ROOT.'/gestionflotte/liste_documentation_complete.php?statut=1';
    $boardDocument->nbtodo = 0;
    $boardDocument->nbtodolate = 0;

    $board5 = clone $boardDocument;
    $board5->labelShort = "Valides";
    $board5->nbtodo = $num_document_valide;
    $dashboardlines['document_valide'] = $board5;

    $board6 = clone $boardDocument;
	$board6->url = DOL_URL_ROOT.'/gestionflotte/liste_documentation_complete.php?statut=2';
    $board6->labelShort = "Expirés";
    $board6->nbtodo = $num_document_expire;
    $dashboardlines['document_expire'] = $board6;

    // ------------------------------------------------------------------------------------------
    // Bloc Maintenances    
    if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation = 1 AND date_fin >='".$aujourdhui."'";
	else $sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation = 1 AND date_fin >='".$aujourdhui."' AND fk_user_creation=".$user->id;

	$res_maintenance = $db->query($sql_maintenance);
	if($res_maintenance){
		$num_maintenance_encours = $db->num_rows($res_maintenance);
	}
	

	$nb_maintenance_retard = 0;
	$id_retard = array(0);
	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$sql_maintenance = "SELECT rowid, fk_vehicule FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation = 1 AND date_fin <'".$aujourdhui."'";
	else $sql_maintenance = "SELECT rowid, fk_vehicule FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation = 1 AND date_fin <'".$aujourdhui."' AND fk_user_creation=".$user->id;

	$res_maintenance = $db->query($sql_maintenance);
	$num = $db->num_rows($res_maintenance);
		if ($num) {
			while ($obj = $db->fetch_object($res_maintenance)) {
				$sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation = 1 AND date_fin >='".$aujourdhui."' AND fk_vehicule =".$obj->fk_vehicule;
				$res_maintenance = $db->query($sql_maintenance);
				if($db->num_rows($res_maintenance) <= 0){
					$nb_maintenance_retard ++;
					$id_retard[] = $obj->rowid;
				}
			}
		}

    $boardMaintenance = new WorkboardResponse($db);
    $boardMaintenance->img = img_picto('', 'maintenance', 'class="paddingright pictofixedwidth"');
    $boardMaintenance->element = "maintenance";
    $boardMaintenance->label = "Maintenances";
    $boardMaintenance->infoKey = "todo";
    $boardMaintenance->url = DOL_URL_ROOT.'/gestionflotte/liste_maintenance_a_venir.php';
    $boardMaintenance->nbtodolate = 0;

    $board7 = clone $boardMaintenance;
    $board7->labelShort = "En retard";
    $board7->nbtodo = $nb_maintenance_retard;
    $dashboardlines['maintenance_retard'] = $board7;

    $board8 = clone $boardMaintenance;
	$board8->url = DOL_URL_ROOT.'/gestionflotte/liste_maintenance_complete.php';
    $board8->labelShort = "En cours";
    $board8->nbtodo = $num_maintenance_encours;
    $dashboardlines['maintenance_activee'] = $board8;

    // ------------------------------------------------------------------------------------------
    // Bloc Réparations
    if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
        $sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation != 1";
    else $sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation != 1 AND fk_user_creation=".$user->id;
	$res_maintenance = $db->query($sql_maintenance);
	if($res_maintenance){
		$num_reparation = $db->num_rows($res_maintenance);
	}
    
    if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation != 1 AND YEAR(date_maintenance) =".date('Y');
	else $sql_maintenance = "SELECT rowid FROM ".MAIN_DB_PREFIX."maintenance_vehicule WHERE maintenance_reparation != 1 AND YEAR(date_maintenance) =".date('Y')." AND fk_user_creation=".$user->id;

	$res_maintenance = $db->query($sql_maintenance);
	if($res_maintenance){
		$num_reparation_annee = $db->num_rows($res_maintenance);
	}

    $boardReparation = new WorkboardResponse($db);
    $boardReparation->img = img_picto('', 'repair', 'class="paddingright pictofixedwidth"');
    $boardReparation->element = "reparation";
    $boardReparation->label = "Réparations";
    $boardReparation->infoKey = "todo";
    $boardReparation->url = DOL_URL_ROOT.'/gestionflotte/listereparation_complete.php';
    $boardReparation->nbtodo = 0;
    $boardReparation->nbtodolate = 0;

    $board9 = clone $boardReparation;
    $board9->labelShort = "Total";
    $board9->nbtodo = $num_reparation;
    $dashboardlines['reparation_total'] = $board9;

    $board10 = clone $boardReparation;
    $board10->labelShort = "En ".date('Y');
    $board10->nbtodo = $num_reparation_annee;
    $dashboardlines['reparation_annee'] = $board10;

    // ------------------------------------------------------------------------------------------
    // Bloc Carburant (1 - demandes soumises/approuvées)
    if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE soumis = 1 AND valider = 0 AND rejeter = 0";
	else $carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE soumis = 1 AND valider = 0 AND rejeter = 0 AND fk_user_creation=".$user->id;

	$res_carburant = $db->query($carburant);
	if($res_carburant){
		$num_num_demande_soumise = $db->num_rows($res_carburant);
	}

    if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE approuver = 1";
	else $carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE  approuver = 1 AND fk_user_creation=".$user->id;

	$res_carburant = $db->query($carburant);
	if($res_carburant){
		$num_num_demande_approuver = $db->num_rows($res_carburant);
	}

	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE rejeter = 1 AND approuver = 0";
	else $carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE  rejeter = 1 AND approuver = 0 AND fk_user_creation=".$user->id;

	$res_carburant = $db->query($carburant);
	if($res_carburant){
		$num_num_demande_rejete = $db->num_rows($res_carburant);
	}

	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write"))
		$carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE valider = 1 AND approuver = 0";
	else $carburant = "SELECT rowid FROM ".MAIN_DB_PREFIX."carburant_vehicule WHERE  valider = 1 AND approuver = 0 AND fk_user_creation=".$user->id;

	$res_carburant = $db->query($carburant);
	if($res_carburant){
		$num_num_demande_valide = $db->num_rows($res_carburant);
	}

    $boardCarburant1 = new WorkboardResponse($db);
    $boardCarburant1->img = img_picto('', 'carburant', 'class="paddingright pictofixedwidth"');
    $boardCarburant1->element = "carburant";
    $boardCarburant1->label = "Demandes carburant approuvées";
    $boardCarburant1->infoKey = "todo";
    $boardCarburant1->url = DOL_URL_ROOT.'/gestionflotte/carburant.php?action=soumis';
    $boardCarburant1->nbtodo = 0;

    $board11 = clone $boardCarburant1;
    $board11->labelShort = "Soumises";
    $board11->nbtodo = $num_num_demande_soumise;
    $dashboardlines['carburant_soumises'] = $board11;

	$board13 = clone $boardCarburant1;
	$board13->url = DOL_URL_ROOT.'/gestionflotte/carburant.php?action=valider';
    $board13->labelShort = "Validées";
    $board13->nbtodo = $num_num_demande_valide;
    $dashboardlines['carburant_valides'] = $board13;

    /*$board14 = clone $boardCarburant1;
	$board14->url = DOL_URL_ROOT.'/gestionflotte/carburant.php?action=rejeter';
    $board14->labelShort = "Rejetées";
    $board14->nbtodo = $num_num_demande_rejete;
    $dashboardlines['carburant_rejetes'] = $board14;*/

	$board12 = clone $boardCarburant1;
	$board12->url = DOL_URL_ROOT.'/gestionflotte/carburant.php?action=approuver';
    $board12->labelShort = "Approuvées";
    $board12->nbtodo = $num_num_demande_approuver;
    $dashboardlines['carburant_approuvees'] = $board12;
    // ------------------------------------------------------------------------------------------
    // Groupes de tableaux
    $dashboardgroup = array(
        'vehicule' => array(
            'groupName' => 'État des véhicules',
            'stats' => array('vehicule_bon_etat', 'vehicule_panne')
        ),
        'assigner' => array(
            'groupName' => 'Véhicules Assignés',
            'stats' => array('vehicule_assigne', 'vehicule_libre')
        ),
        'papier' => array(
            'groupName' => 'Documents véhicules',
            'stats' => array('document_valide', 'document_expire')
        ),
        'maintenance' => array(
            'groupName' => 'Maintenances',
            'stats' => array('maintenance_retard', 'maintenance_activee')
        ),
        'reparer' => array(
            'groupName' => 'Réparations',
            'stats' => array('reparation_total', 'reparation_annee')
        ),
        'carburant_ok' => array(
            'groupName' => 'Carburant (Approuvées)',
            'stats' => array('carburant_soumises', 'carburant_valides', /*'carburant_rejetes',*/ 'carburant_approuvees')
        )
		
    );

    // ------------------------------------------------------------------------------------------
    // Affichage
    print '<div class="fichecenter">';
    print '<div class="opened-dash-board-wrap"><div class="box-flex-container">';

    foreach ($dashboardgroup as $groupKey => $groupElement) {
		//print $groupKey.'***';
        $groupName = $langs->trans($groupElement['groupName']);
        print '<div class="box-flex-item"><div class="box-flex-item-with-margin">';
        print '<div class="info-box medium">';
        print '<span class="info-box-icon bg-infobox-propal"><i>'.img_picto('', $groupKey).'</i></span>';
        print '<div class="info-box-content">';
        print '<div class="info-box-title">'.$groupName.'</div>';
        print '<div class="info-box-lines">';
        foreach ($groupElement['stats'] as $infoKey) {
            if (isset($dashboardlines[$infoKey])) {
                $board = $dashboardlines[$infoKey];
                print '<div class="info-box-line spanoverflow nowrap">';
                print '<a href="'.$board->url.'" class="info-box-text info-box-text-a">';
                print '<div class="marginrightonly inline-block valignmiddle info-box-line-text" title="'.$board->label.'">'.$board->labelShort.'</div>';
                print '<span class="classfortooltip badge badge-info">'.$board->nbtodo.'</span>';
                print '</a></div>';
            }
        }
        print '</div></div></div></div></div>';
    }

    print '</div></div>';
    print '<div class="clearboth"></div>';
}

//---------------------------------------------------------------------------------------------------------
print '<div class="fichecenter"><div class="fichethirdleft">';
/*if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
	$sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule";
else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE fk_user_creation=".$user->id;
	$res_vehicule = $db->query($sql_vehicule);
	if($res_vehicule){
		$num_vehicule = $db->num_rows($res_vehicule);
	}
	//$dataseries[] = array("Nombre total de vehicule", $num_vehicule);

	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
		$sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0";
	else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND fk_user_creation=".$user->id;

	$res_vehicule = $db->query($sql_vehicule);
	if($res_vehicule){
		$num_vehicule_bon_etat = $db->num_rows($res_vehicule);
	}
	$dataseries[] = array("vehicule en bon état", $num_vehicule_bon_etat);

if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
	$sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 1";
else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 1 AND fk_user_creation=".$user->id;

	$res_vehicule = $db->query($sql_vehicule);
	if($res_vehicule){
		$num_vehicule_panne = $db->num_rows($res_vehicule);
	}
	$dataseries[] = array("vehicule en panne", $num_vehicule_panne);

	if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
		$sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND rowid IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin IS NULL OR date_fin >='".$aujourdhui."')";
	else $sql_vehicule = "SELECT rowid FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND rowid IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin IS NULL OR date_fin >='".$aujourdhui."') AND fk_user_creation=".$user->id;

	$res_vehicule = $db->query($sql_vehicule);
	if($res_vehicule){
		$num_vehicule_bon_etat = $db->num_rows($res_vehicule);
	}
	$dataseries[] = array("vehicule assigné", $num_vehicule_bon_etat);

	$salarie_societe_graph = '<div class="div-table-responsive-no-min">';
	$salarie_societe_graph .= '<table class="noborder nohover centpercent">'."\n";
	$salarie_societe_graph .= '<tr class="liste_titre"><th colspan="2">'.$langs->trans("Statistique - Véhicules").'</th></tr>';
	$dolgraph = new DolGraph();
	$dolgraph->SetData($dataseries);
	$dolgraph->setShowLegend(2);
	$dolgraph->setShowPercent(1);
	$dolgraph->SetType(array('pie'));
	$dolgraph->setHeight('200');

	// --- IMPORTANT : couleurs dans le même ordre que $dataseries
	$colors = array(
		'#106e05ff',//bon état
		'#920000ff', //panne
		'#0b0532ff'//assigné
	);

$dolgraph->SetDataColor($colors);
	$dolgraph->draw('idgraphsalariesociete');
	$salarie_societe_graph .= '<tr><td>'.$dolgraph->show();
	$salarie_societe_graph .= '</td></tr>';
	$salarie_societe_graph .= '<tr class="liste_total"><td>Nombre total de véhicule</td><td class="right">';
	$salarie_societe_graph .= $num_vehicule;
	$salarie_societe_graph .= '</td></tr>';
	$salarie_societe_graph .= '</table>';
	$salarie_societe_graph .= '</div>';

	print $salarie_societe_graph;
	print '<br>';
/*
 * Draft vehicule
 */
if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
		$sql_vehicule = "SELECT rowid, reference_interne, fk_user_creation FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND rowid NOT IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin IS NULL OR date_fin <'".$aujourdhui."')";
	else $sql_vehicule = "SELECT rowid, reference_interne, fk_user_creation FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 0 AND rowid NOT IN (SELECT fk_vehicule FROM ".MAIN_DB_PREFIX."assignation_vehicule WHERE date_fin IS NULL OR date_fin <'".$aujourdhui."') AND fk_user_creation=".$user->id;

	$sql_vehicule .= " ORDER BY date_creation DESC";
	$res_vehicule = $db->query($sql_vehicule);
	print $db->error();
	if ($res_vehicule) {
		print '<div class="div-table-responsive-no-min">';
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">vehicules non assignés</th></tr>';
		$langs->load("orders");
		$num = $db->num_rows($res_vehicule);
		if ($num) {
			$i = 0;
			while ($i < $num) {
				$obj = $db->fetch_object($res_vehicule);

				print '<tr class="oddeven">';
				print '<td class="nowrap">';
				print "<a href='./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$obj->rowid."'>".$obj->reference_interne."</a>";
				print "</td>";
				print '<td class="nowrap">';
				$nom_prenon = "";
				$sql = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$obj->fk_user_creation;
				$res = $db->query($sql);
				if($res){
					$obj_user = $db->fetch_object($res);
				$nom_prenon .= $obj_user->lastname." ".$obj_user->firstname;

				}
		
				print "<a href='../user/card.php?id=".$obj->fk_user_creation."'>".$nom_prenon."</a>";;
				print '</td></tr>';
				$i++;
			}
		} else {
			print '<tr class="oddeven"><td colspan="3">'.$langs->trans("Aucun vehicule").'</td></tr>';
		}
		print "</table></div><br>";
	}

/*
 * Orders that are in process
 */
if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "approuver"))
	$sql_last_modif = "SELECT rowid, reference_interne, fk_user_creation, panne, date_creation FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 1";
else $sql_last_modif = "SELECT rowid, reference_interne, fk_user_creation, panne, date_creation FROM ".MAIN_DB_PREFIX."vehicule WHERE panne = 1";

$resql = $db->query($sql_last_modif);
	if ($resql) {
		$num = $db->num_rows($resql);

		print '<div class="div-table-responsive-no-min">';
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("Véhicule en panne").' <a href="./creation_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&action=panne"><span class="badge">'.$num.'</span></a></th></tr>';

		if ($num) {
			$i = 0;
			$max = 3;
			while ($i < $num && $i < $max) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven">';
				print '<td class="nowrap" width="20%">';

				print '<table class="nobordernopadding"><tr class="nocellnopadd">';
				print '<td width="96" class="nobordernopadding nowrap">';
				print img_picto("", "maintenance", 'class="paddingright pictofixedwidth"')."<a href='./onglets/detail_vehicule.php?mainmenu=gestionflotte&leftmenu=&leftmenu=listevehicule&id_vehicule=".$obj->rowid."'>".$obj->reference_interne."</a>";
				print '</td>';

				print '<td width="16" class="nobordernopadding nowrap">';
				print '&nbsp;';
				print '</td>';

				print '<td width="16" class="nobordernopadding hideonsmartphone right">';
				print '</td></tr></table>';

				print '</td>';

				print '<td class="nowrap">';
				$nom_prenon = "";
				$sql = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$obj->fk_user_creation;
				$res = $db->query($sql);
				if($res){
					$obj_user = $db->fetch_object($res);
				$nom_prenon .= $obj_user->lastname." ".$obj_user->firstname;

				}
				print "<a href='../user/card.php?id=".$obj->fk_user_creation."'>".$nom_prenon."</a>";
				print '</td>';

				print '<td class="right">'.$obj->date_creation.'</td>'."\n";

				//----------------------------

            $status = '<span style="background-color: #920000ff; padding: 1px 9px; border-radius: 50%; font-weight: bold;"></span>'; 

			print '<td class="right">'.$status.'</td>';
				print '</tr>';
				$i++;
			}
			if ($i < $num) {
				print '<tr><td><span class="opacitymedium">'.$langs->trans("More").'...</span></td><td></td><td></td><td></td></tr>';
			}
		}

		print "</table></div><br>";
	} else {
		//dol_print_error($db);
	}

	
$sql_document = "SELECT dv.*, td.rowid as tdrowid, td.nom, vh.rowid as vhrowid, vh.nom as vhnom, vh.reference_interne FROM ".MAIN_DB_PREFIX."document_vehicule as dv";
	$sql_document .= " LEFT JOIN ".MAIN_DB_PREFIX."type_document as td on dv.fk_type_document = td.rowid";
	$sql_document .= " LEFT JOIN ".MAIN_DB_PREFIX."vehicule as vh on dv.fk_vehicule = vh.rowid";

if($user->hasRight("gestionflotte", "gestionvehicule", "valider") || $user->hasRight("gestionflotte", "gestionvehicule", "write")){
	$sql_document .= " WHERE 1=1";
}else{	
	$sql_document .= "  WHERE dv.fk_user_creation=".$user->id;
}	

	$sql_document .= " AND date_expiration <'".$aujourdhui."'";
	$sql_document .= " ORDER BY dv.date_creation DESC";
	$res_document = $db->query($sql_document);
	print $db->error();
	if ($res_document) {
		print '<div class="div-table-responsive-no-min">';
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="2">Documents expirés</th>';
		print '<th>Date Expiration</th></tr>';
		$langs->load("orders");
		$num = $db->num_rows($res_document);
		if ($num) {
			$i = 0;
			while ($i < $num) {
				$obj = $db->fetch_object($res_document);

				print '<tr class="oddeven">';
				print '<td class="nowrap">';
				print "<a title ='".$obj->vhnom." (".$obj->reference_interne.")' href='./onglets/document_vehicule.php?mainmenu=gestionflotte&leftmenu=listevehicule&id_vehicule=".$obj->vhrowid."'>".$obj->nom."</a>";
				print "</td>";
				print '<td class="nowrap">';
				$nom_prenon = "";
				$sql = "SELECT firstname, lastname FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$obj->fk_user_creation;
				$res = $db->query($sql);
				if($res){
					$obj_user = $db->fetch_object($res);
				$nom_prenon .= $obj_user->lastname." ".$obj_user->firstname;

				}
		
				print "<a href='../user/card.php?id=".$obj->fk_user_creation."'>".$nom_prenon."</a>";
				print '</td>';
				print '<td>';
				print $obj->date_expiration;
				print '</td></tr>';
				$i++;
			}
		} else {
			print '<tr class="oddeven"><td colspan="3">'.$langs->trans("Aucun vehicule").'</td></tr>';
		}
		print "</table></div><br>";
	}


print '</div><div class="fichetwothirdright">';
print getNumberCarburantPieChart();
	print '<br>';


/*
 * document vehicule
 */


print '<div class="div-table-responsive-no-min">';

print getNumberDocumentPieChart();
print '<br>';

print "</div><br>";
print '</div></div>';


//header('Location: ./compta/facture/card.php');
if($message != ''){		
	print "<script>
	$.jnotify('".$message."', {delay : 5000, fadeSpeed: 500});
	</script>";
}