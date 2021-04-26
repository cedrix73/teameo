<?php

/**
 * @name DbAccess
 * @author cvonfelten
 * Classe créant une couche d'abstraction pour le driver PostGreSql et  gérant les accès à la BD
 */



//namespace base;
// fichier appelant: use base as DbAccess;
// \DbAccess\Mysqli->methode
class DbPostGresql implements DbInterface {

	private $_noMsg;
	private $_stmt;


    public function setLog($bln) {
        $this->_noMsg = $bln;
    }

    public function getLog() {
        return $this->_noMsg;
	}

	
	/* Etablit une connexion à un serveur de base de données et retourne un identifiant de connexion
	   L'identifiant est positif en cas de succès, FALSE sinon.
	   On pourrait se connecter avec un utilisateur lambda
	   */
	public function connect($conInfos, $no_msg = 0)
	{
		$this->_stmt = false;
        $host = $conInfos['host'];
		$dbname = $conInfos['dbase'];
		$link = pg_connect("\'host=".$host . " user=" . $conInfos['username'] . " password=" . $conInfos['password'] . " dbname=" . $dbname . " port=". $conInfos['host']. "\'");
		if (!$link)
		{
				if ($no_msg == 0){
					echo "Erreur de connexion sur ".$this->host." par ".$this->username;
				}
				return false;
		}
		return $link;
	}
	
	public function database_error($result){
            echo pg_result_error($result);
            exit;
	}

	/* Ferme la connexion au serveur MySQL associée à l'identifiant $hcon
	   Retourne TRUE en cas de succès, FALSE sinon */
	public function close() {
		return pg_close($this->link);
	}

	/**
         * Sélectionne la base de données $db
         * --> Sans objet pour mysqli. 
         */
        
	public function selectDb($db) {
            
	}

	/**
	 * @name: execQuery
	 * @description: Execute la requete SQL $query et renvoie  le resultSet
	 * pour être interprétée ultérieurement par fetchRow ou fetchArray.
	 * 
	 * @param ressource $link: instance renvoiée lors de la connexion PDO.
	 * @param string $query: chaine SQL
	 * @return array $resultSet : resultat de l'execution
	 */
	public function execQuery($link, $query) {
		$resultSet = pg_query($link, $query);
		return $resultSet;
    }
    

    /**
	 * @name: execPreparedQuery
	 * @description: il s'agit d'un prepared Statement: Prépare et execute 
	 * la requete SQL $query et renvoie le resultSet pour être interprétée 
	 * ultérieurement par fetchRow ou fetchArray. Si on passe des arguments 
	 * dans la requête, leurs valeurs doivent juste être déclarées dans 
	 * l'ORDRE dans l'argument $args, sous la forme d'un simple tableau  
	 * style: array("joe", "campbell") et ce même s'il n'y a qu'une seule 
	 * valeur à passer. 
	 * Important ! La requête doit être de la forme :
	 * '.. WHERE author.last_name = $1 AND author.name = $2'
	 * 
	 * @param ressource $link: instance renvoiée lors de la connexion PDO.
	 * @param string $query: chaine SQL
	 * @param boolean $again: Si true, le même statement est réexecuté avec de
	 *                de nouveaux arguments; $query peut être vide.
	 * @return mixed $stmt : retourne le statement de la requête.
	 */
	public function execPreparedQuery($link, $query, $args=null, $again) {
		if(!$again) {
			$this->_stmt = false;
		}
		
		try {
			if(!$this->_stmt) {
				$this->_stmt = $link->pg_prepare($link, "req1", $query);
			}
            
			$this->_stmt = pg_execute($link, "req1", $args);
			
		} catch (PDOException $e) {
			if($this->_noMsg !== false) {
				echo 'Problème lors de l\'execution de la requête: ' . $e->getMessage();
			}
			
		}
		return $this->_stmt;
	}

    /* Retourne un tableau énulméré qui correspond à la ligne demandée, ou FALSE si il ne reste plus de ligne
	   Chaque appel suivant retourne la ligne suivante dans le résultat, ou FALSE si il n'y a plus de ligne disponible */
	public function fetchRow($result) {
            return pg_fetch_row($result);
	}
	
	public function numRows($result) {
            return pg_num_rows($result);
	}
        
    public function fetchField($result){
        return mysqli_fetch_fields($result);
    }

    public function fetchAssoc($result){
        return pg_fetch_assoc($result);
    }

    /**
     * fetch_array()
     * @param type $result
     * @return type
     * Retourne un tableau associatif par clé qui correspond à la ligne demandée, 
     * Chaque appel suivant retourne la ligne suivante dans le résultat, 
     * ou FALSE si il n'y a plus de ligne disponible
     */
	public function fetchArray($result) {
		return pg_fetch_array($result, null, PGSQL_ASSOC);
	}
	
	public function escapeString($link, $donnee){
		return pg_escape_string($donnee);
	}
        

        
        
    public function free_result($result){
        pg_freeresult($result);
    }
        
    /**
     * prepareExecute
     * Prepare puis execute des requêtes simples
     * On appelle une 1ère fois la fonction avec les champs $query rempli
     * --> Prepare
     * et une seconde fois avec les champs $stmt et $var completés et 
     * $query à null --> Execute
     * @param string $query genre "SELECT District FROM City WHERE Name=?"
     * @param type $var
     * @param type $stmt
     * @return type
     */
    public function prepareExecute($query=null,$var=null, $stmt=null){
        if(isset($query) && !is_null($query)){
            // mode preparation
            $stmt = pg_prepare($this->link, "my_query", $query);
            return $stmt;
        }else{
            $myrow = array();
            $result = pg_execute($this->link, "my_query", array("my_results_tab"));
            
            while ($myrow = $this->fetchArray($result)) {
                
            }
            return $result;
        }
    }

    public function getTableDatas($link, $query)
	{
		return $this->execQuery($link, $query);
	}
}

?>
