<?php

include_once '../config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_CLASSES_PATH.'CvfDate.php';
require_once ABS_CLASSES_PATH.'Planning.php';


/* 
 * Affichage des domaines en fonction des services
 * sélectionnées.
 */

$retour = '';   
$isOk = false;

$ressourceId = '';
if(isset($_POST['ressource_id']) && !is_null($_POST['ressource_id']) &&  $_POST['ressource_id'] == true){
    $ressourceId = $_POST['ressource_id'];
    $isOk = true;
}

$activiteSel = '';
if(isset($_POST['activite_sel']) && !is_null($_POST['activite_sel']) &&  $_POST['activite_sel'] == true){
    $activiteSel = $_POST['activite_sel'];
    $isOk = true;
}

$dateDebut = '';
if(isset($_POST['date_debut']) && !is_null($_POST['date_debut']) &&  $_POST['date_debut'] == true){
    $dateDebut = $_POST['date_debut'];
    $isOk = true;
}

$dateFin = '';
if(isset($_POST['date_fin']) && !is_null($_POST['date_fin']) &&  $_POST['date_fin'] == true){
    $dateFin = $_POST['date_fin'];
    $isOk = true;
}

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

if($isOk){
    $insertion = true;
    $planning = new Planning($dbaccess, $ressourceId, $activiteSel, $dateDebut, $dateFin);
    // Est ce qu'on a un evenement pour la même ressource et pour le(s) même(s) jour(s) ?
    $tabActivites = $planning->read();
    // Si tel est le cas, on le(s) supprime
    if(count($tabActivites) > 0){
        $suppression = $planning->delete();
    }
    
    
    if(!$suppression){
        $retour .= 'Problème lors de la suppression';
    }else{
        $retour .= "suppression effectuée avec succès.";
    }
    //$retour .= $planning->getSql();
}
$dbaccess->close($handler);
echo $retour;


?>
