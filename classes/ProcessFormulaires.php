<?php 
require_once ABS_GENERAL_PATH.'formFunctions.php';

/**
 * @name ProcessFormulaires
 * @author cvonfelten
 * Classe créant et validant lesformulaires
 */

Class ProcessFormulaires {

    private $_dbaccess;

    private $_tabInsert;

    private $_msgErr;



    public function __construct($dbaccess) 
    {
        $this->_dbaccess = $dbaccess;
        $this->_tabInsert = array();
        $this->msgErr = array();
        
    } 

    /**
     * @name          getFormFromTable
     * @description   Obtient un formulaire à partir de la table $tableName
     * 
     * @param         string   $tableName: Nom de la table
     * @param         int      $nbChampsParLigne: Nombre de champs par ligne (par défaut 3)
     * @return        string   $retour:   Formulaire au format html
     */
    public function getFormFromTable($tableName, $nbChampsParLigne = 3) {
        $retour = '';   
        // Connexion
        $handler = $this->_dbaccess->connect();
        if ($handler === false) {
            $retour = 'Problème de connexion à la base ';
        } else {
            $tabChamps = array();
            $tabChamps = $this->_dbaccess->getTableDatas($tableName);
            $retour = '';
            if (is_array($tabChamps) && count($tabChamps) > 0) {
                $i = 0;
                $numGroupe = 0;
                $nbChampsParLigne = 3;
                $champPrefixe = substr($tableName, 0, 3);
                $retour .= '<div class="legende_titre"><h1>Enregistrement ' . $tableName .'</h1></div>';
                $retour .= '<form action="">'; 
                $retour .= '<div id="panel_' . $tableName . '" name = "panel_' .$tableName .'"><table id="' . $tableName . '" class= "tab_params">';
                // Liste de tous les types d'événement
                foreach ($tabChamps as $value) {
                    $typeChamp = $value['typechamp'];
                    $nomChamp = $value['nomchamp'];
                    $isNullable = $value['is_nullable'];
                    $modulo = intval($i % $nbChampsParLigne );
                    if ($modulo == 1) {
                        $retour .=   '<tr id='.$numGroupe.'>';
                        //  class="'.$classeParite.'"
                    }
                    $classeIcone = ($isNullable == 'YES' ? '' : 'class="form_icon ui-icon ui-icon-alert" title ="champ obligatoire"');
                    $retour .= '<td>';
                    $libelleChamp = underscoreToLibelle($nomChamp);
                    $nomChampFinal = $champPrefixe . '_' . $nomChamp;
                    // label
                    $retour .= '<label for="' .$nomChampFinal . '">' . $libelleChamp . '</label>:&nbsp;';
                    $required = ($isNullable == 'NO' ? 'required="required"' : '');
                    

                    // parsing champs
                if (strstr($nomChamp, 'mail') == true) {
                        $retour .= '<input type="email" id="' . $nomChampFinal .' " name="' . $nomChampFinal .'"
                                ' . $required . ' placeholder="' . $nomChamp . '" alt = "' . $libelleChamp . '" onchange="verifEmail($(this).attr(\'name\'));/>';
                    }else {
                        switch($typeChamp) {
                            case 'varchar':
                                $retour .= '<input type="text" id="' . $nomChampFinal .' " name="' . $nomChampFinal .'"
                                        ' . $required . ' placeholder="' . $nomChamp . '" alt = "' . $libelleChamp . '" maxlength="30" />';
                            break;
                            case 'date':
                                $retour .= '<input type="date" id="' . $nomChampFinal .'" name="' . $nomChampFinal .'" 
                                ' . $required . ' alt = "' . $libelleChamp . '" size="10" maxlength="10" class="champ_date" />';
                            break;
                        }
                    }
                    $retour .='<span id="res_' . $nomChamp . '_img" name ="res_' . $nomChamp . '_img" ' . $classeIcone . '>&nbsp</span>';
                    $retour .= '</td>';
                    
                    if ($modulo == $nbChampsParLigne || $i >= count($tabChamps)) {
                        $retour .="</tr>";
                        $numGroupe++;
                    }
                    if ($i >= count($tabChamps)-1) {
                        $retour .= '<tr><td><input type="submit" id="validation_' . $tableName . '" value="Enregistrer" onclick="validerSaisie' . ucfirst($tableName) .'();"/></td></tr>'; 
                        $retour .= '</table"></div>';
                    }
                    $i++;
                }
                
                
            }
            $retour .= '</form>';
        }
        $this->_dbaccess->close($handler);
        return $retour;
    }


    public function checkForm($tabChamps) 
    {
        $msgErr = "";
        $isOk = true;
        try {
            foreach($tabChamps as $stdObj) {
                $nomChamp = $stdObj['nom'];
                $nomChampFinal = substr($nomChamp, 4);
                $valeurChamp = $stdObj['valeur'];
                $typeChamp = $stdObj['type'];
                $labelChamp = $stdObj['label'];
                $requiredChamp = isset($stdObj['required']) ? $stdObj['required'] : false;
    
                // On ne prend pas en compte les champs vides
                if(empty($valeurChamp)) {
                  // ... sauf s'ils sont obligatoires
                    if($requiredChamp) {
                      $isOk = false;
                      $this->msgErr .= "<br>Erreur: Le champ " . $labelChamp . " est obligatoire.";
                    }
                } else {
                
                    switch($typeChamp) {
                        case 'email':
                          $valeurChamp = filter_var($valeurChamp, FILTER_SANITIZE_EMAIL);
                          if(!filter_var($valeurChamp, FILTER_VALIDATE_EMAIL)) {
                            $isOk = false;
                            $this->msgErr .= "<br>Erreur: Le champ " . $labelChamp . " n'a pas une adresse email valide.";
                          }
                        break;
    
                        case 'text':
                            $valeurChamp = filter_var($valeurChamp, FILTER_SANITIZE_STRING);
                            if($nomChampFinal == "nom" || $nomChampFinal == "prenom") {
                                if (!preg_match("/^[a-zA-Z-\séèàüöñøå' ]*$/", $valeurChamp)) {
                                  $this->msgErr .= "<br>Erreur: Seul les lettres et les espaces sont authorisés pour le champ " . $labelChamp;
                                  $isOk = false;
                                }
                            }
                        break;
    
                        case 'select-one':
                          $valeurChamp = filter_var($valeurChamp, FILTER_SANITIZE_NUMBER_INT);
                          $nomChampFinal .= '_id';
                        break;
    
                        case 'date':
                            if (!preg_match("/^(\d{4})(-)(\d{1,2})(-)(\d{1,2})$/", $valeurChamp)) {
                              $this->msgErr .= "<br>Erreur: Seul le format date aaaa-mm-jj est authorisé pour le champ " . $labelChamp;
                              $isOk = false;
                            }
                        break;
    
                        case 'tel':
                          $valeurChamp = filter_var($valeurChamp, FILTER_SANITIZE_NUMBER_INT);
                          if (!preg_match("/^[0-9]{9,}$/", $valeurChamp)) {
                            $this->msgErr .= "<br>Erreur: Seul les chifres sont authorisés pour le champ " . $labelChamp;
                            $isOk = false;
                          }
                        break;
    
                        case 'num':
                          $valeurChamp = filter_var($valeurChamp, FILTER_SANITIZE_NUMBER_INT);
                          if(!filter_var($valeurChamp, FILTER_VALIDATE_INT)) {
                            $isOk = false;
                            $this->msgErr .= "<br>Erreur: Le champ " . $labelChamp . "ne contient pas de valeurs numériques.";
                          }
                        break;
    
                        default:
                          // radios
    
                        break;
                    }
    
    
                }
                if($isOk) {
                    $this->_tabInsert[$nomChampFinal] = $valeurChamp;
                }
    
            }
        } catch (Exception $e) {
          echo "Erreur: Une erreur s'est produite lors de l'enregistrement du champ " . $labelChamp;
          exit();
        }

        return $isOk;
    }

    public function getTabInsert() {
        return $this->_tabInsert;
    }

    public function getMsgErreurs() {
        return $this->msgErr;
    }




}