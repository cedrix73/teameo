

<?php
  include_once 'classes/CvfDate.php';
  ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
            <title>Essais Dates</title>
            <meta http-equiv="Content-Type" content="text/HTML; charset=UTF-8" />
            <meta http-equiv="Content-Language" content="fr" />
    </head>
    <body>
<?php
$nbSemaines = 2;
echo 'TESTS:<br>';
$today = new CvfDate(date('28/12/2004'));
$dateDebutTxt = '28/12/2014';
$dateDebut = new CvfDate(date('28/12/2014'));
$dateFin = new CvfDate(date('04/01/2015'));
echo '<br>' . $dateFin->tspToSql() . '<br>';

$sqlData = 'VALUES ';
$pointeurJour = new CvfDate($dateDebutTxt);
$diffJours = $dateDebut->nbJourEcart($dateFin);
echo $diffJours . '<br>';
for($j=0; $j<$diffJours; $j++){
    $sqlData .= ' (1,1, \''. $pointeurJour->tspToSql() . '\','
   . 0
   . ')';
   if($j < intval($diffJours-1)){
      $sqlData .=  ', ';
   }
   $pointeurJour->incJours();
}

$sqlInsert = 'INSERT INTO planning (planning_event, '
                           . 'planning_ressource, '
                           . 'planning_jour, '
                           . 'planning_isferie) ';
$sql = $sqlInsert . $sqlData;
echo $sql . '<br>';

/*
$datselect = $today;
echo $datselect->tspToDate().'<br>';
echo 'numero jour: ' . $datselect->numJourSemaine().'<br>';
$jourCal = $datselect;	
$jourCal->decJours( intval($datselect->numJourSemaine()-1) );
$lundi = $jourCal;
echo 'lundi: '.$lundi->tspToDate().'<br>';
echo 'jourCal: '.$jourCal->tspToDate().'<br>';
echo '-----------------------------<br>';

for($i=0;$i<7*$nbSemaines;$i++){
    
    echo $jourCal->jourAbbr().' '.$jourCal->numJour();
    $jourCal->incJours();
}
    
    

$datselec = new CvfDate(date("d/m/Y"));	

echo "<br>".$datselec->tspToDate();

$jourCal->decJours( intval($datselect->numJourSemaine()-1) );
    $lundi = $jourCal;
    
if(isset($_REQUEST['dateselect']) && $_REQUEST['dateselect']!=null){
    $datselec=$_REQUEST['dateselect'];
}


   

//on calcule la date de début de semaine
$datdeb = new CvfDate(date("d/m/Y"));	
$datdeb->decJours( $datselec->numJourSemaine() );
            //on calcule la date de fin de semaine

$datfin = new CvfDate(date("d/m/Y"));	
$datfin->decJours( $datselec->numJourSemaine() );
$datfin->incJours( 6 );

//$datfin = new CvfDate(date("Ymd"));
//$datfin = $datdeb->incr_x( 6 );


$dat_affich_debut = $datdeb->tspToDate();
$dat_affich_fin = $datfin->tspToDate();

echo 'date début demaine : ' . $dat_affich_debut;
echo '<br>date de fin de semaine: ' . $dat_affich_fin;
*/

//$date_du_jour = $planning_aff->getdate_deb();
/*
echo '<br>Timestamp de la date de début : '.$datdeb->dateToTsp();
echo '<br>Timestamp de la date de fin : '.$datfin->dateToTsp();
echo '<br>Affichage de la date de début : '.$datdeb->affDate();
echo '<br>Affichage de la date de fin : '.$datfin->affDate();
echo '<br>Jour de la date de fin : '.$datfin->jour_lettre();
*/
echo '<br><br><u>Utilisation d\'une date :</u> ';
//$archDateFin = new ArchDate($datfin->dateToTsp());
$archDateFin = new CvfDate('22/10/2014');
echo '<br>Date retournée: ' . $archDateFin->tspToDate();
echo '<br>Jour de la date de fin: ' . $archDateFin->jour_lettre();
echo '<br>On avance de 2 jours..';
$archDateFin->incJours(2);
echo '<br>Semaine de la date de fin: ' . $archDateFin->semaine();
echo '<br>Jour de la semaine de la date de fin:: ' . $archDateFin->jour_lettre();
echo '<br>Jour de la date de fin: ' . $archDateFin->jour_lettre();
echo '<br>Mois de la date de fin: ' . $archDateFin->numMois();
echo '<br>On avance de 3 mois..';
$archDateFin->incMois(3);
echo '<br>Jour retourné: ' . $archDateFin->numJour();
echo '<br>Mois retourné: ' . $archDateFin->numMois();
echo '<br>Année retourné: ' . $archDateFin->annee();
echo '<br>Date retournée: ' . $archDateFin->tspToDate();

echo '<br>Test d\'une date de calendrier';


?>
    </body>
</html>
