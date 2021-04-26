<?php

include_once '../config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_CLASSES_PATH.'CvfDate.php';
require_once ABS_CLASSES_PATH.'Ressource.php';
require_once ABS_CLASSES_PATH.'Event.php';
require_once ABS_CLASSES_PATH.'Planning.php';
require_once ABS_GENERAL_PATH.'formFunctions.php';



/* 
 * Affichage du planning en fonction des ressources
 */
setlocale( LC_TIME, 'fra' ); 
$retour = '';   
// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if($handler===FALSE){
    $retour = 'Probléme de connexion à la base ';
}else{
    // Vérification des données passées en $_POST:
    $nbSemaines = 2;
    $datselect = new CvfDate(date("d/m/Y"));
    if(isset($_REQUEST['date_sel']) && !is_null($_REQUEST['date_sel']) && strlen($_REQUEST['date_sel'])>0){
        $datselect = new CvfDate($_REQUEST['date_sel']);
        //$datselect = new CvfDate('15/02/2015');
    }
    
    // Traitement site selectionné
    $siteId = "";
    if(isset($_REQUEST['site_sel']) && !is_null($_REQUEST['site_sel']) &&  $_REQUEST['site_sel'] == true){
        $siteId = $_REQUEST['site_sel'];
    }
    
    // Traitement departement selectionné
    $departementLibelle = "";
    if(isset($_REQUEST['departement_sel']) && !is_null($_REQUEST['departement_sel']) &&  $_REQUEST['departement_sel'] == true){
        $departementLibelle = $_REQUEST['departement_sel'];
    }
    // Traitement service selectionnée
    $serviceLibelle = "";
    if(isset($_REQUEST['service_sel']) && !is_null($_REQUEST['service_sel']) &&  $_REQUEST['service_sel'] == true){
        $serviceLibelle = $_REQUEST['service_sel'];
    }
    
    
    if(isset($_REQUEST['col_sup']) && !is_null($_REQUEST['col_sup']) &&  $_REQUEST['col_sup'] == true){
        $nbSemaines += $_REQUEST['col_sup'];
    }  
    
    // Cherchons de l'info en BD:
    // - Chargement des ressources par services
    $ressource = new Ressource($dbaccess);
    $tabRessources = $ressource->getRessourcesBySelection($siteId, $departementLibelle, $serviceLibelle);
    $maxRessource = count($tabRessources);
    // - Chargement de la nomenclature des événements
    $typesEvent = new Event($dbaccess);
    $tabTypeEvent = $typesEvent->getAll();
    
    
    // Horodatage
    $numSemaine = $datselect->semaine();
    //on calcule la date de début de semaine
    $today = new CvfDate(date("d/m/Y"));
    $jourCal = new CvfDate($_REQUEST['date_sel']);	
    
    
     ?>
<script>
    // On va remettre le datePicker à la date choisie
    setDateWidget("<?php echo $jourCal->tspToDate();?>");
</script>
<?php
    
    /**
     * AFFICHAGE DU CALENDRIER
     */
    $jourCal->decJours( intval($datselect->numJourSemaine()-1) );
    $premierJour = $jourCal->tspToDate();
    // et celle de fin
    $vendrediSuivant = new CvfDate(date("d/m/Y"));	
    $vendrediSuivant->decJours( $datselect->numJourSemaine() );
    $vendrediSuivant->incJours( 6 );
    
    
    $dernierJour = new CvfDate($_REQUEST['date_sel']);
    $dernierJour->decJours( intval($datselect->numJourSemaine()-1) );
    $dernierJour->incJours(intval(7*$nbSemaines)-1);
    
    // construction des liens pour la semaine précédente et suivante
    $semainePrecedente = new CvfDate($jourCal->tspToDate());
    $semainePrecedente->decJours(intval(7*$nbSemaines)-1);
    $semaineSuivante = new CvfDate($jourCal->tspToDate());
    $semaineSuivante->incJours(intval(7*intval($nbSemaines+1))-1);
    $lienPrecedent ='<img src="'.IMAGES_PATH.
            'precedent.jpg" onclick=\'refreshCalendar("'.$semainePrecedente->tspToDate().
            '")\' title="semaines précédentes" />';
    $lienToday = '<img src="'.IMAGES_PATH.
            'today.png" onclick=\'refreshCalendar("'.$today->tspToDate().'")\' title="aujourd\'hui"/>';
    $lienSuivant ='<img src="'.IMAGES_PATH.'suivant.jpg" onclick=\'refreshCalendar("'.
            $semaineSuivante->tspToDate().'")\' title="semaines suivantes" />';
    
    
    // affichage des entêtes de numéro de semaine
    $retour.='<div class="legende_semaine">'.$lienPrecedent.' '.$lienToday.' '.$lienSuivant.'</div>';
    for($semaine = intval($numSemaine); $semaine<$numSemaine+$nbSemaines; $semaine++){
        $retour.='<div class="entete_semaine">'.$semaine.'</div>';
    }
    // Calcul des jours feriés
    $tabFeries = CvfDate::getFeries($jourCal->annee());
    // affichage des entêtes des jours par semaine
    $retour.='<div class="legende_jour"><span class="legende_titre">Ressource</div>';
    $tabNbRessources = array();
    for($i=0;$i<7*$nbSemaines;$i++){
        // On initie le tableau de comptage des ressources
        $tabNbRessources[$jourCal->getTsp()][1] = 0;// journée
        $tabNbRessources[$jourCal->getTsp()][2] = 0;// après-midi
        $tabNbRessources[$jourCal->getTsp()][3] = 0;// matin
        // Est-ce aujourd'hui ?
        if($jourCal==$today){
            $retour.= '<div class="entete_today">';
        }else{
            $retour.= '<div class="entete_jour">';
        }
        $retour.= $jourCal->jourAbbr().'<br>'.$jourCal->getEntete().'</div>';
        $jourCal->incJours();
    }
    
    // Chargement des données des événements
    $objPlanning = new Planning($dbaccess, null, null, $premierJour, $dernierJour->tspToDate());
    $tabActivites = $objPlanning->getActivites();
    ?>
<script>
    // Redimensionnement du scroll
    function redim(){
        var hauteur_ressources = parseInt(<?php echo $maxRessource;?> * ($(".legende_semaine").css("height").replace("px", "")));
        var hauteur_semaine = parseInt($(".legende_semaine").css("height").replace("px", ""));
        var hauteur_jours = parseInt($(".entete_jour").css("height").replace("px", ""));
        var hauteur_tot = parseInt(hauteur_ressources) + 6;
        var planning_height = parseInt($('#planning').css("height").replace("px", ""));
        if(hauteur_tot < 355){
            
        }else{
            hauteur_tot = 355;
        }
        $('#defilement').css("height", hauteur_tot + "px");
        h_planning = hauteur_tot + 80;
        $('#planning').css("height", h_planning + "px");
    }
    
    
</script>

<?php
    $isMe = false;
    $retour .= '<div id="defilement">';
    // Affichage des entêtes des ressources
    $indexEvent = null;
    foreach($tabRessources as $numRes => $value){
        unset($jourCal);
        $jourCal = new CvfDate($premierJour);	
        if(isset($idUser) && (intval($idUser) == intval($numRes))){
            $isMe = TRUE;
            $classeLegende = 'legende_me';
        }else{
            $isMe = FALSE;
            $classeLegende = 'legende_ressources';
        }
        $retour.='<div id = ressource_' . $numRes . ' class="'.$classeLegende.'">'.
        ' ' . utf8_encode($tabRessources[$numRes]['nom']) . ' ' .
        utf8_encode($tabRessources[$numRes]['prenom']).
        '</div>';
        
        
        /*  Affichage des jours par ressources */
        for($i=0;$i<7*$nbSemaines;$i++){
            $indexEvent = 0; // index = 0 pour tout jour travaillé
            
            /**
             * @todo éliminer les booléens pour faire une seule variable de
             * statut: A voir si on peut cumuler les we ou les jours feriés 
             * avec des activités et avoir un statut multiple.
             */
            $isEvent = false;
            $isFerie = false;
            $isWeekend = false;
            if($jourCal==$today){
                $retour.= '<div class="today" ';
            }else{
                $retour.= '<div class="cell';
            }
            
            // W-e
            if($jourCal->isWeekEnd()){
                $isWeekend = true;
                $retour.= ' we';
            }
            
            // Ferié: surpasse le w-e
            if(isset($tabFeries[$jourCal->getTsp()])){
                $isFerie = true;
                $retour.= ' ferie';
            }
            /*
             * Injection des événements par ressources: on peut aussi faire un else avec 
             * la condition we si le w-e ne peut étre affecté.
             */
            $retour.= '"';
            $periode = '';
            $demiJournee = false;
            $intJourCal = $jourCal->getTsp();
            if(isset($tabActivites[$numRes]) && isset($tabActivites[$numRes][$intJourCal])){
                // essayer avec !isnull($tabActivites[$numRes][$jourCal])
                $isEvent = true;
                $indexEvent = intval($tabActivites[$numRes][$intJourCal]['type']);
                $periode = intval($tabActivites[$numRes][$intJourCal]['periode']);
                $libellePeriode='';
                $couleur = $tabTypeEvent[$indexEvent]['couleur'];
                $retour.= ' style="background: #'.$couleur.';';
                
                
                
                if($periode > 1){
                    $demiJournee = true;
                    $retour.= colorieDemiJournee($couleur, $periode);
                    $tabNbRessources[$jourCal->getTsp()][$periode]++;
                }
                $retour.='" ';
            }
            
            // ------ Représentation des jours -------
            $ressource =  $tabRessources[$numRes]['nom']. ' ' . 
                    $tabRessources[$numRes]['prenom'];
            // fonction hoover
            $infoBulle = ' title = "'.$jourCal->jourDetails().' ' . $ressource.' statut: ';
            
            $fonctionHoover = '';
            if($isEvent){
                $libellePeriode = ' (' . utf8_decode($tabPeriode[$periode]) . ')';
                
                $fonctionHoover.= $infoBulle . $tabTypeEvent[$indexEvent]['libelle'] . $libellePeriode . '" ';
            }elseif($isFerie){
                $fonctionHoover.= $infoBulle . $tabFeries[$jourCal->getTsp()];
            }elseif($isWeekend){
                $fonctionHoover.= $infoBulle . 'en week-end';
            }else{
                $fonctionHoover.= $infoBulle . 'disponible';
                // Si ressource dispo, on incrémente le tableau de comptage pour ce jour.
                isset($tabNbRessources[$jourCal->getTsp()][1]) ? $tabNbRessources[$jourCal->getTsp()][1]++ : $tabNbRessources[$jourCal->getTsp()][1] = 1;
            }
            $fonctionHoover .= '" ';
            $retour.= utf8_encode($fonctionHoover);
            
            // click: selection d'une activité
            if($isAdmin || $isMe){      
                if(!($isWeekend || $isFerie)){
                    $fonctionClick = 'afficherSaisie("'.$jourCal->tspToDate().'",'.$numRes.','.$indexEvent.');';
                    $fonctionClick .= 'infoRessource("'
                        . str_replace(' ', '---', $tabRessources[$numRes]['nom']) 
                        .'","'.str_replace(' ', '---', $tabRessources[$numRes]['prenom']).'");';
                    $retour.= 'onclick='.$fonctionClick;
                }
            }
            // Affichage dans cellule
            $retour.= '>';
            if($isEvent){
                $retour.= $tabTypeEvent[$indexEvent]['affichage'];
            }elseif($isFerie){
                $retour.= 'férié';
                //$retour.= utf8_encode('férié');
            }
            $retour.= '</div>';
            $jourCal->incJours();
        }
    }
    $retour .= '</div>';
    
    /* 
     * Dernière ligne: Totaux des ressources libres
     * Elle toujours affichée car se trouve en dehors du div de défilement
     */
    unset($jourCal);
    $jourCal = new CvfDate($premierJour);
    $retour .= '<div class = "legende_ressources" style="background: none;"><b>Nombre de ressources disponibles</b></div>';
    for($i=0;$i<7*$nbSemaines;$i++){
        $indexEvent = 0; // index = 0 pour tout jour travaillé
        
        if($tabNbRessources[$jourCal->getTsp()][2] > 0 || $tabNbRessources[$jourCal->getTsp()][3] > 0){
            $tabNbRessources[$jourCal->getTsp()][2]+=$tabNbRessources[$jourCal->getTsp()][1];
            $tabNbRessources[$jourCal->getTsp()][3]+=$tabNbRessources[$jourCal->getTsp()][1];
            $retour.= '<div class="today"><b>' . $tabNbRessources[$jourCal->getTsp()][3] . ' / ' . $tabNbRessources[$jourCal->getTsp()][2] . '</b></div>';
        }else{
            $retour.= '<div class="today"><b>' . $tabNbRessources[$jourCal->getTsp()][1] . '</b></div>';
        }
        $jourCal->incJours();
    }        
    
}

unset($tabActivites);
unset($tabFeries);
unset($tabNbRessources);
unset($tabNbRessources);
$dbaccess->close($handler);
//echo utf8_encode($retour);

echo $retour;


?>
