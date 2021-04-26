<?php
	
/**
 * CvfDate
 * Classe gérant les dates avec une variable d'instance
 * de type timeStamp qu'on peut faire évoluer
 *
 * @author Cédric Von Felten
 */

setlocale(LC_TIME, 'fr','fr_FR','fr_FR@euro','fr_FR.utf8','fr-FR','fra');

	class CvfDate
	{   
            private $tampon_unix;

            //construteur de la classe ArchDate
            function __construct($date, $formatSql = false){
                if($formatSql){
                    $this->sqlToTsp($date);
                }else{
                    $this->dateToTsp($date);
                }
            }
            
            public function getTsp(){
                return $this->tampon_unix;
            }
            
            /**
             * incTemps
             * @param type $heures
             * @param type $min
             * @param type $jours
             * @param type $mois
             * @param type $annees
             * Augmente ou décroit de façon personnalisée la date d'instance
             * Ex: On ajoute 2 ans, on diminue 3 jours, on ajoute 6 heures et 20 minutes:
             * incTemps(6, 20, -3,,2);
             */
            public function incTemps($heures=0, $min=0, $jours=0, $mois=0, $annees=0)
            {
                $this->tampon_unix = mktime(
                        $this->heure()+ $heures, 
                        $this->minute() + $min,
                        0,
                        $this->numMois() + $mois,
                        $this->numJour()+ $jours,
                        $this->annee() + $annees);
            }
            
            /**
             * incJours
             * incrémente la date de $nb jours ($jour += X)
             */
            public function incJours($nb=1)
            {
                    $this->tampon_unix += intval($nb)*24*3600;
            }

            //décremente la date de $nb jours ($jour -= X)
            public function decJours($nb)
            {
                    $this->tampon_unix -= $nb*24*3600;
            }


            /**
             * numJour
             * retourne le numéro du jour dans le mois	
             *  au format single digit
             * @return type
             */			  
            public function numJour()
            {
                return intval(strftime("%d",$this->tampon_unix));
            }
            
            /**
             * jour
             * retourne le numéro du jour dans le mois	
             * @return type
             */			  
            public function jour()
            {
                return strftime("%d",$this->tampon_unix);
            }


            /**
             * numMois
             * Retourne le numéro du mois au format %m				  
             */
            public function numMois()
            {
                    return intval(strftime("%m",$this->tampon_unix));
            }
            
            /**
             * numMois
             * Retourne le numéro du mois au format %m				  
             */
            public function mois()
            {
                    return strftime("%m",$this->tampon_unix);
            }
            
            /**
             * getEntete
             * @return string jj/mm
             * Retourn le jour et le mois chacun sur 2 digits
             */
            public function getEntete()
            {
                return date ('d/m', $this->tampon_unix);
            }
            
            /**
             * incMois
             * @param  int $nb
             * incrémente le mois de $nb mois
             * 	modifie la variable d'instance		  
             */
            public function incMois($nb=1)
            {
                $this->tampon_unix = mktime(
                        $this->heure(), 
                        $this->minute(),
                        0,
                        $this->numMois()+$nb,
                        $this->numJour(),
                        $this->annee());
            }
            
             /**
             * decMois
             * @param  int $nb
             * decrémente le mois de $nb mois
             * 	modifie la variable d'instance		  
             */
            public function decMois($nb=1)
            {
                $this->incMois(-$nb);
            }

            /**
             * annee
             * retourne l'année (4 chiffres)			  
             */
            public function annee()
            {
                    return strftime("%Y",$this->tampon_unix);
            }
            
            
            /**
             * incMois
             * @param  int $nb
             * incrémente l'année de $nb année
             * 	modifie la variable d'instance		  
             */
            public function incAnnee($nb=1)
            {
                $this->tampon_unix = mktime(
                        $this->heure(), 
                        $this->minute(),
                        0,
                        $this->numMois(),
                        $this->numJour(),
                        $this->annee()+$nb);
            }
            
             /**
             * decMois
             * @param  int $nb
             * decrémente l'année de $nb années
             * 	modifie la variable d'instance		  
             */
            public function decAnnee($nb=1)
            {
                $this->incAnnee(-$nb);
            }

            /**
             * heure
             * retourne l'heure						   
             */
            public function heure()
            {
                return strftime("%H",$this->tampon_unix);
            }
            
            /**
             * incHeure
             * @param  int $nb
             * incrémente l'heures de $nb unités
             * 	modifie la variable d'instance		  
             */
            public function incHeure($nb=1)
            {
                $this->tampon_unix = mktime(
                    $this->heure()+$nb, 
                    $this->minute(),
                    0,
                    $this->numMois(),
                    $this->numJour(),
                    $this->annee());
            }
            
             /**
             * decHeure
             * @param  int $nb
             * decrémente l'heure de $nb unités
             * 	modifie la variable d'instance		  
             */
            public function decHeure($nb=1)
            {
                $this->incHeure(-$nb);
            }
            /*
             * minute
             *  retourne les minutes				
             */
            public function minute()
            {
                return strftime("%M",$this->tampon_unix);
            }


            //retourne les minutes * 10/6			   
            public function minute_100eme()
            {
                return strftime("%M",$this->tampon_unix)*(10/6);
            }

            /**
             * semaine
             * retourne le numéro de la semaine (commence par le lundi)
             */
            public function semaine()
            {
                 //return intval(strftime("%W",$this->tampon_unix) +1);
                 return date('W', $this->tampon_unix);
            }

            /**
             * numJourSemaine
             * retourne numéro du jour de la semaine [1..7]
             */
            public function numJourSemaine()
            {
                $jour = strftime("%w",$this->tampon_unix);
                if ($jour == 0){
                    return 7;
                }else{
                    return $jour;
                }
            }
            
            /**
             * isWeekEnd()
             * retourne true si c'est le cas
             */
            public function isWeekEnd()
            {
                $retour = false;
                if($this->numJourSemaine()==6 || $this->numJourSemaine()==7){
                    $retour = true;
                }
                return $retour;
            }


            /** 
             * nbJourEcart ($date)
             * @param  $date est une autre instance de cette classe.	
             * retourne le nombre de jour entre $date et $this
             */			
            public function nbJourEcart($date)
            {
                $d1 = $this->tampon_unix;
                $d2 = $date->getTsp();
                //$d1 = mktime (0,0,0, $this->numMois(),$this->numJour(),$this->annee);
                //$d2 = mktime (0,0,0, $date->numMois(),$date->numJour(),$date->annee());
                
                $ecart = abs ($d1 - $d2);
                for ($i=0;$ecart > 0;$i++)
                        $ecart -= 24*3600;
                return $i;
            }

		
            /**
             * 	APRES ($date)	
             * 	@param : $date est une instance de date	 	
             *  retourne vrai si $this est avant $date
            */
            public function isAvant($date)
            {
                return $this->tampon_unix < $date->tampon_unix;
            }

            /**
             * 	APRES ($date)	
             * 	@param : $date est une instance de date	
             *  retourne vrai si $this est apres $date
            */
            public function isApres($date)
            {
                return $this->tampon_unix > $date->tampon_unix;
            }


            // Fixe une date à partir d'une chaine de caracteres
            public function setDatefromString($stringDate, $valjour){
                $this->tampon_unix=strtotime($stringDate, $valjour);
            }

            /**
             * 
             * @param int $mois
             * @return int nb de jours ds le mois
             */
            public function nbJoursdsMois($mois){
                $nb_jour = 0;
                if (($mois == 1)||($mois == 3)||($mois == 5)||($mois == 7)||($mois == 8)||($mois == 10)||($mois == 12))
                {
                        $nb_jour = 31;
                }
                else
                {
                    if ($mois != 2)
                        $nb_jour = 30;
                    else
                    {
                        if (($annee%4 == 0)&&(($annee%100 != 0)||($annee%400 == 0)))
                            $nb_jour = 29;
                        else
                            $nb_jour = 28;
                    }
                }
                return $nb_jour;
            }			
            /**
             * dernierDimancheMois
             * fonction utilisée dans changement_heure.php 
             * retournant la date du dernier dimanche du mois, 
             * le changement d'heure se fait toujours le dernier dimanche du mois 
             * (de mars et octobre).
             * */

            public function dernierDimancheMois($an,$mois)
            {
                //on isole le mois et l'année du changement d'heure
                $dt = new ArchDate(mktime(2,0,0, $mois+1, 0,$an));
                $e=time2date($dt->tampon_unix);

                //on récupère le dernier jours du mois
                $djm=$dt->jour_semaine();
                //pour chaque cas, on donne la date du dernier dimanche du mois
                $nbJours = $this->arch_nbJoursdsMois($mois);
                return  date("d/m/Y H:i:s", mktime(2, 0, 0, $mois,31-$djm, $an));
            }
            
            /**
             * jourLettre()
             * @return String le jour de la semaine
             */
            public function jourLettre(){
                return strftime( "%A", $this->tampon_unix);
            }
            
            /**
             * jourAbbr()
             * @return String le jour de la semaine en abbregé
             */
            public function jourAbbr(){
                return strftime( "%a", $this->tampon_unix);
            }
            
            
            /**
             * jourDetails()
             * Retourne format l, D (ex: Mercredi, 12/12/2014)
             * @return String 
             */
            public function jourDetails(){
                //return date('l', $this->tampon_unix).', '.$this->tspToDate();
                
                return strftime( "%A", $this->tampon_unix).', '.$this->tspToDate();
            }
            /**
             * affDate
             * Retourne la date au format d/m/Y
             * @return String
             */
            public function tspToDate($sep='/'){
                return date('d'.$sep.'m'.$sep.'Y', $this->tampon_unix);
            }
            
            
            /**
             * sqlToTsp
             * @param String $date Ymd
             *    
             */
            public function sqlToTsp($date){
                // 0123-56-89
                $longueur = strlen($date);
                $jour = intval(substr($date, 8, 2));
                $mois = intval(substr($date, 5, 2));
                $annee = intval(substr($date, 0, 4));
                $heure = 9;
                $minute = 0;
                $seconde = 0;
                
                if($longueur>10){
                    $heure = intval(substr($date, 11, 2));
                    $minute = intval(substr($date, 14, 2));
                    $seconde = substr($date, $longueur-2, 2);
                }
                //echo '<br> jour: '.$jour.' mois: '.$mois . ' année: ' . $annee . ' heure: ' .$heure. ' minute: ' . $minute . ' seconde: ' . $seconde .'<br>';
                //echo 'longueur chaine: '. $longueur;
                $this->tampon_unix = mktime($heure, $minute, $seconde, $mois, $jour, $annee );
            }
            
            /**
             * sqlToTspStatic
             * : Permet une conversion rapide du format sql en tsp
             * @param String aaaa-mm-jj
             * @return Integer timestamp
             */
            public static function sqlToTspStatic($date){
                $jour = intval(substr($date, 8, 2));
                $mois = intval(substr($date, 5, 2));
                $annee = intval(substr($date, 0, 4));
                $heure = 9;
                $minute = 0;
                $seconde = 0;
                
                if(strlen($date)>10){
                    if(strlen(substr($date, 8, strlen($date)))==3){
                        $heure= "0".substr($date, 8, 1);
                    }else{
                        $heure= substr($date, 8, 2);
                    }
                    $minute = substr($date, strlen($date)-2, 2);
                }
                
                return mktime($heure, $minute, $seconde, $mois, $jour, $annee );
            }
            
            /**
             * tspToSql
             * @param String $sep= '-'
             * Transforme un timestamp en date notation sql
             * @return String (format yyyy-mm-dd)
             */
            public function tspToSql($sep = '-'){
                return date('Y' . $sep . 'm'  .$sep . 'd', $this->tampon_unix);
            }
            
            /**
             * 
             * @param type $date
             * Convertit une date au format jj/mm/aaaa
             * au format SQL AAAA-MM-JJ 
             */
            
            
            
            public function dateToSql($date){
                $jour = intval(substr($date, 0, 2));
                $mois = intval(substr($date, 3, 2));
                $annee = intval(substr($date, 6, 4));
                return $annee . "-" . $mois . "-" . $jour;
            }
            
            /**
             * dateToTsp
             * @param String $date d/m/Y
             * dd/mm/yyyy
             * 0123456789
             */
            public function dateToTsp($date){
                $jour = intval(substr($date, 0, 2));
                $mois = intval(substr($date, 3, 2));
                $annee = intval(substr($date, 6, 4));
                $this->tampon_unix = mktime(9, 0, 0, $mois, $jour, $annee );
            }
        
            /**
             * getFeries
             * retourne un tableau [timestamp) = libellé des jours feriés
             * de l'année en cours et de l'année suivante
             * @param type $year
             * @return array[tsp] = string
             */
            public static function getFeries($annee = null)
            {
                if ($annee === null)
                {
                    $annee = intval(strftime('%Y'));
                }
                $holidays = array();
                for($year = $annee; $year<intval($annee + 2); $year++){
                    $datePaques = easter_date($year);
                    $jourPaques = date('j', $datePaques);
                    $moisPaques = date('n', $datePaques);
                    $anneePaques = date('Y', $datePaques);
                    // Jours feries fixes
                    $holidays[mktime(9, 0, 0, 1, 1, $year)] = 'Jour de l\'an';
                    $holidays[mktime(9, 0, 0, 5, 1, $year)] = utf8_decode('Fête du travail');
                    $holidays[mktime(9, 0, 0, 5, 8, $year)] = 'Victoire des allies';
                    $holidays[mktime(9, 0, 0, 7, 14, $year)] = utf8_decode('Fête nationale');
                    $holidays[mktime(9, 0, 0, 8, 15, $year)] = 'Assomption';
                    $holidays[mktime(9, 0, 0, 11, 1, $year)] = 'Toussaint';
                    $holidays[mktime(9, 0, 0, 11, 11, $year)] = 'Armistice';
                    $holidays[mktime(9, 0, 0, 12, 25, $year)] = utf8_decode('Noël');

                    // Jour feries qui dependent de paques
                    $holidays[mktime(9, 0, 0, $moisPaques, $jourPaques + 1, $anneePaques)] = utf8_decode('Lundi de pâques');
                    $holidays[mktime(9, 0, 0, $moisPaques, $jourPaques + 39, $anneePaques)] = 'Ascension';
                    $holidays[mktime(9, 0, 0, $moisPaques, $jourPaques + 50, $anneePaques)] = utf8_decode('Pentecôte');
                }
                //sort($holidays);
                return $holidays;
            }
            
            /**
             * isFerie()
             * indique si le timeStamp est un jour ferié
             * @return boolean
             */
            public function isFerie(){
                $retour = false;
                $tabFeries = getFeries(annee());
                if(isset($tabFeries[$this->tampon_unix])){
                    $retour = true;
                }
                return $retour;
            }
            
            public function isToday(){
                $retour = false;
                if($this->tampon_unix == $this->dateToTsp(date("d/m/Y"))){
                    $retour =  true;
                }
                return $retour;
            }
            
}																					//

?>
