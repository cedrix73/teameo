<?php

include_once '../config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_CLASSES_PATH.'CvfDate.php';
require_once ABS_CLASSES_PATH.'Localisation.php';


/* 
 * Affichage des domaines en fonction des départements
 * sélectionnées.
 */

$retour = '';   
// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if($handler===false){
    $retour = 'Problème de connexion à la base ';
}else{
    $siteId = null;
    if(isset($_POST['site_id']) 
            && !is_null($_POST['site_id']) 
            &&  $_POST['site_id'] == true
            && ctype_digit($_POST['site_id']))
    {
        $siteId = $_POST['site_id'];
    }

    $contexteInsertion = false;
    if(isset($_POST['contexte_insertion']) 
            && !is_null($_POST['contexte_insertion']) 
            &&  $_POST['contexte_insertion'] == true
            && ctype_alnum($_POST['contexte_insertion']))
    {
        $contexteInsertion = $_POST['contexte_insertion'];
    }

    
    // affichage des jours par ressources
    $localisation = new Localisation($dbaccess);
    $tabDepartements = $localisation->getDepartementsBySite($siteId, $contexteInsertion);
    
    $retour = json_encode($tabDepartements);
}
$dbaccess->close($handler);
echo $retour;


?>
