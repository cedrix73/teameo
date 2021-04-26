<?php

include_once '../config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_GENERAL_PATH.'formFunctions.php';


/* 
 * Création ou modification d'un événement selon la variable javascript 
 * infoRessource.action ={insertion, modification}
 */

$retour = '';   
$isOk = true;
$periode = 1;
if(isset($_POST['id_activite']) && 
    !is_null($_POST['id_activite']) 
    &&  $_POST['id_activite'] == true  
    && is_numeric($_POST['id_activite']))
{
    $id_activite = $_POST['id_activite'];
    $isOk = true;
}

// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if($handler===FALSE){
    $retour = 'Problème de connexion à la base ';
    $isOk = false;
}

if($isOk){
    $strActivites = selectLoad('libelle', 'evenement', $dbaccess, $id_activite);
    $retour = utf8_encode($strActivites);
}
$dbaccess->close($handler);
echo $retour;
?>
