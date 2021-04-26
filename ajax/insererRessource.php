<?php

require_once '../config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_CLASSES_PATH.'Ressource.php';
require_once ABS_CLASSES_PATH.'ProcessFormulaires.php';
require_once ABS_GENERAL_PATH.'formFunctions.php';

/* 
 * Sanitization et vérification back-office du formulaire posté
 */

$retour = "";   
$isOk = false;
$msgErr = "";



if ($isOk === false) {
    $retour = 'Paramètres incorrects';
}


// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if($handler===FALSE){
    $retour = "Problème de connexion à la base ";
    $isOk = false;
}


$tabJson = "";
$tabInsert = array();
$validationClass = new ProcessFormulaires($dbaccess);


if (isset($_POST['json_datas']) && !is_null($_POST['json_datas']) &&  $_POST['json_datas'] == false) {
    $isOk = false;
} else {
    $jsonString = $_POST['json_datas'];
    $isOk = true;
    $tabJson = json_decode($jsonString, true);
    $isOk = $validationClass->checkForm($tabJson);
}


// On a collecté et verifié toutes les données
if(!$isOk) {
  $retour = $validationClass->getMsgErreurs();
} else {
  // envoyer tableau INSERT en BD 
  $ressource = new Ressource($dbaccess);
  $tabInserts = $validationClass->getTabInsert();
  $retour = $ressource->create($tabInserts);
}
    
$dbaccess->close($handler);
//echo utf8_encode($retour);
echo $retour;


?>
