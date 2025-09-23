<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2022 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    paiementsalaire/admin/about.php
 * \ingroup paiementsalaire
 * \brief   About page of module PaiementSalaire.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

// Libraries
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');


/*
 * Actions
 */

// None


/*
 * View
 */

$form = new Form($db);

$help_url = '';
$page_name = "A propos";

llxHeader('', 'Dépenses | Imputations', $help_url);

print load_fiche_titre($langs->trans('Dépenses | Imputations'), "", 'title_setup');

// Configuration header
//$head = paiementsalaireAdminPrepareHead();
//print dol_get_fiche_head($head, 'Menu2', $langs->trans($page_name), 0, 'paiementsalaire@paiementsalaire');

//print $tmpmodule->getDescLong();


print "<h1>DEPENSES IMPUTATIONS POUR <a href='https://www.dolibarr.org/' target='_blank'>DOLIBARR ERP CRM</a></h1>";

print "<h2>Description</h2>";
print "Le module Dépenses | Imputations est conçu pour permettre l'expression des <b>Besoins</b> des employés ayant conduit à une commande ou à une facture émise par une ou plusieurs sociétés maliennes.<br><br>";
print "Ce module de gestion des dépenses permet l’imputation collective ou individuelle des lignes de besoins, de commandes ou de factures. 
Il offre également des statistiques détaillées de l’ensemble des dépenses, selon le type d’imputation ou par utilisateur.";

print "<h2>Avantage</h2>";
print "Simplifiez la gestion des dépenses et des commandes de votre entreprise<br>
Le module Dépenses | Imputations est un outil tout-en-un conçu pour les entreprises maliennes souhaitant optimiser la gestion des besoins exprimés par leurs employés, depuis la demande initiale jusqu’à la commande ou la facturation.<br><br>

Grâce à une interface intuitive, ce module vous permet :<br>

    &ensp;&ensp;De centraliser les demandes internes,<br>

    &ensp;&ensp;De suivre leur évolution jusqu’à la commande ou la facture,<br>

    &ensp;&ensp;D’imputer facilement chaque dépense à un utilisateur ou à un type de charge,<br>

    &ensp;&ensp;D’accéder à des statistiques claires par utilisateur ou par nature d’imputation.<br>

👉 Gagnez en visibilité, en efficacité et en transparence sur toutes les dépenses de votre structure.";
print "<h2>Licences</h2>";
print "licence est payante";

print "<h2>Versions</h2>";
print "<table class='tagtable liste'>";
//Les en-têtes
print "<tr class='liste_titre'>";
print "<td>Les versions</td>";
print "<td>Status</td>";
print "<td>Change log</td>";
print "<td>Compatibilité avec Dolibarr";
print "<td>Lien de téléchargement</td>";
print "</tr>";

/*$soc_sql = "SELECT * FROM ".MAIN_DB_PREFIX."version_dolipaie";
$soc_res = $db->query($soc_sql);//= $db->query($covSql);
$num = $db->num_rows($soc_res);
if($soc_res){
	$a = 0;
	while ($a < $num) {
		$obj = $db->fetch_object($soc_res);*/
		print "<tr>";
		print "<td>V1.0.0</td>";
		print "<td>Active</td>";
		print "<td>Tout juste Développé</td>";
		print "<td>Compatible avec toutes les versions de Dolibarr ERP & CRM (version dolibarr recommandée 20.0.1)</td>";
		print "<td><a href='https://dolipaie-ibs-mali.com'>Internet Business Services IBS-Mali</a></td>";
		print "</tr>";
	/*	$a++;
	}
}
*/
print "</table>";


print "<h2>Version active</h2>";
print "PaiementSalaire : 1.0.0";

print "<h2>Documentation</h2>";
print "<h2>Aucune mise à jour disponible!</h2>";
// Page end
print dol_get_fiche_end();
llxFooter();
$db->close();
