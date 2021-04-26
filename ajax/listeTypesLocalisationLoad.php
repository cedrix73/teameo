<?php

include_once '../config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_CLASSES_PATH.'Localisation.php';
require_once ABS_GENERAL_PATH.'formFunctions.php';
//require_once ABS_GENERAL_PATH.'formFunctions.php';


/* 
 * Affichage de tous les types d'activité
 * sélectionnées.
 */

$retour = '';   
// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if ($handler === false){
    $retour = 'Problème de connexion à la base ';
    $isOk = false;
} else {
    $siteId = '';
    if (isset($_POST['site_id']) 
    && !is_null($_POST['site_id']) 
    &&  $_POST['site_id'] == true
    && ctype_digit($_POST['site_id']))
    {
        $siteId = $_POST['site_id'];
        $isOk = true;
    } else {
        $isOk = false;
    }
    $departementLibelle = '';
    if (isset($_POST['departement_lib']) 
        && !is_null($_POST['departement_lib']) 
        )
    {
        $departementLibelle = $_POST['departement_lib'];
        $isOk = true;
    }else{
        $isOk = false;
    }
    

    if( !$isOk) 
    {
        $retour = "paramètres incorrects";
        
    } else {
        if(empty($siteId)) {
            $typeLocalisation = "site";
            $typeSuperieur = "site";
        } else {
            if(empty($departementLibelle)) {
                $typeLocalisation = "departement";
                $typeSuperieur = "site";
                
            }else{
                $typeLocalisation = "service";
                $typeSuperieur = "departement";
            }
        }

        $localisation = new Localisation($dbaccess, $typeLocalisation);
        $tabLocalisation = array();
        $tabLocalisation = $localisation->getAll();
        $retour = '';
        
        $retour .= '<table id="tab_localisations" class= "tab_params">';
        $retour .= '<th>libellé</th><th>description</th>';
        if ($typeLocalisation != 'site') {
            $retour .= '<th>' . $typeSuperieur . '</th>';
            $typeSuperieurId = $typeSuperieur . '_id';
        }
        $retour .= '<th>action</th>';
        
        $tabOptions = tabLoad('libelle', $typeSuperieur, $dbaccess);

        $classeParite = 'pair';

        if (is_array($tabLocalisation) && count($tabLocalisation) > 0 ) {
            $i = 1;
            // Liste de tous les types d'événement
            foreach ($tabLocalisation as $key => $value) {
                $id = $value['id'];
                $classeParite = ($i%2 == 0 ? 'pair':'impair');
                $retour .=   '<tr id='.$id.' class="'.$classeParite.'">';
                $retour .= '<td id="libelle_' . $id . '"><input type="text" class="legende_activite" disabled value="' . $value['libelle'] . '" /></td>';
                $retour .= '<td><input type="text" id="description_' . $id . '" disabled value="'.$value['description'].'" maxlength="250" /></td>';

                
                if ($typeLocalisation != 'site') {
                    // combobox des options liés à la clé secondaire  avec la bonne valeur sélectionnée
                    $options = getOptionsFromTab($tabOptions, $value[$typeSuperieurId]);
                    $retour .= '<td><select id ="key_' . $id . '"  disabled >' . $options . '</select></td>';
                }


                $retour .= '<td><input type="button" id="' . $id . '_validation_ligne" disabled value="valider" onclick="modifierTypeLocalisation('. $id .');"/></td>';
                /**
                 * TO DO:
                 * 
                 */
                $retour .="</tr>";
                $i++;
            } 
        }

        

        // Ajout d'un nouveau type d'événement
        $retour .=   '<tr id="newLine" class="'.$classeParite.'">';
        $retour .= '<td><input type="text" id="libelle_localisation" value="" /> </td>';
        $retour .= '<td><input type="text" id="description_localisation" value="" maxlength="250" /></td>';
        $typeSuperieur = null;

        if ($typeLocalisation == 'service') {

            $tabOptions = $localisation->getDepartementsBySite($siteId, true);
        }

        if ($typeLocalisation != 'site') {
            $options = getOptionsFromTab($tabOptions);
            $retour .= '<td><select id = "key_localisation">' . $options . '</select></td>';
        }
        $retour .= '<td><input id="new_validation" type="button" value="ajouter" onclick="insererTypeLocalisation(\'' . $typeLocalisation . '\');"/></td>';
        $retour .="</tr>";
        $retour .= '</table>';
        //$retour = utf8_encode($retour);
    }
}

$dbaccess->close($handler);
//echo utf8_encode($retour);
echo $retour;


?>
