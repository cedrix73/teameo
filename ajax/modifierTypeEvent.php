<?php

include_once '../config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_CLASSES_PATH.'Event.php';
//require_once ABS_GENERAL_PATH.'formFunctions.php';


/* 
 * Modification d'un type d'activité donné
 */

$retour = '';   
$isOk = false;

$activiteId = '';
if(isset($_POST['activite_id']) && !is_null($_POST['activite_id']) &&  $_POST['activite_id'] == true){
    $activiteId = $_POST['activite_id'];
    $isOk = true;
}

$activiteCouleur = '';
if(isset($_POST['activite_couleur']) && !is_null($_POST['activite_couleur']) &&  $_POST['activite_couleur'] == true){
    $activiteCouleur = $_POST['activite_couleur'];
    $isOk = true;
}

$activiteAbbrev = '';
if(isset($_POST['activite_abbrev']) && !is_null($_POST['activite_abbrev']) &&  $_POST['activite_abbrev'] == true){
    $activiteAbbrev = $_POST['activite_abbrev'];
    $isOk = true;
}

// On enlève le # qu'on ne souhaite pas sauver en base
$activiteCouleur = str_replace('#', '', $activiteCouleur);


if($isOk===FALSE){
    $retour = 'Paramètres incorrects';
}

// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if($handler===FALSE){
    $retour = 'Problème de connexion à la base ';
    $isOk = false;
}

$modification = false;
if($isOk){
    $activite = new Event($dbaccess);
    $modification = $activite->update($activiteId, $activiteCouleur, utf8_decode($activiteAbbrev));
    if(!$modification){
        $retour = 'Un problème est survenu lors de la màj du type d\'activité !';
        $retour.= $activite->getSql();
    }else{
        $retour = 'Màj effectuée !';
    }
}
echo $retour;

$dbaccess->close($handler);
//echo utf8_encode($retour);


?>
