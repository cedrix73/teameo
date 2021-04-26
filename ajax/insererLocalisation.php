<?php

require_once '../config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_CLASSES_PATH.'Localisation.php';
require_once ABS_GENERAL_PATH.'formFunctions.php';
//require_once ABS_GENERAL_PATH.'formFunctions.php';


/* 
 * Modification d'un type d'activité donné
 */

$retour = '';   
$isOk = false;

$typeLocalisation = '';
if (isset($_POST['type_localisation']) && !is_null($_POST['type_localisation']) &&  $_POST['type_localisation'] == true 
    && ctype_alnum($_POST['type_localisation'])) {
    $typeLocalisation = $_POST['type_localisation'];
    $isOk = true;
}

$libelleLocalisation = '';
if (isset($_POST['libelle_localisation']) && !is_null($_POST['libelle_localisation']) &&  $_POST['libelle_localisation'] == true) {
    $libelleLocalisation = $_POST['libelle_localisation'];
    $isOk = true;
}

$descriptionLocalisation= '';
if (isset($_POST['description_localisation']) && !is_null($_POST['description_localisation']) &&  $_POST['description_localisation'] == true 
    && ctype_alnum($_POST['description_localisation'])) {
    $descriptionLocalisation = $_POST['description_localisation'];
    $isOk = true;
}
if ($typeLocalisation != 'site') {
    if(isset($_POST['key_localisation']) && !is_null($_POST['key_localisation']) &&  $_POST['key_localisation'] == true 
       && ctype_alnum($_POST['key_localisation'])) {
        $keyLocalisation = $_POST['key_localisation'];
        $isOk = true;
    }
}


if ($isOk === false) {
    $retour = 'Paramètres incorrects';
} else {
// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if($handler===FALSE){
    $retour = 'Problème de connexion à la base ';
    $isOk = false;
}

$insertion = false;
if ($isOk) {
    $localisation = new Localisation($dbaccess, $typeLocalisation);
    $tabInsert = array();
    $tabInsert['libelle'] = $libelleLocalisation;
    $tabInsert['description'] = $descriptionLocalisation;
    if ($typeLocalisation != 'site') {
        switch($typeLocalisation) {
            case 'departement':
                $tabInsert['site_id'] = $keyLocalisation;
            break;

            case 'service':
                $tabInsert['departement_id'] = $keyLocalisation;
            break;
        }
    }
    
    $insertion = $localisation->create($tabInsert);
    if (!$insertion) {
        $retour = 'Un problème est survenu lors de la création d\'un nouveau ' . $typeLocalisation . ' !';
        //$retour.= $activite->getSql();
    } else {
        $retour = 'Votre nouveau ' . $typeLocalisation . ' a été créé.';
    }
}

$dbaccess->close($handler);

}




//echo utf8_encode($retour);
echo $retour;


?>
