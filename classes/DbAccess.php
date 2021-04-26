<?php
// namespace TeamPlanning\Classes\Db\Repository;
require_once realpath(dirname(__FILE__)).'/../config.php';
require_once ABS_CLASSES_PATH.'DbInterface.php';

/**
 * @name DbAccess
 * @author cvonfelten
 * Classe repository créant une couche d'abstraction et gérant les accès à la BD
 */

class DbAccess 
{
    public $dbase;
	public $host;
	public $username;
	public $password;
	private $_link;
    private $_conInfos = array();
    private $_dbInterface;
    private $_log;

    public function __construct($dbInterface)
	{
            $this->_conInfos['dbase'] = M_DBNAME;
            $this->_conInfos['host'] = M_DBHOST;
            $this->_conInfos['username'] = M_DBUSER;
            $this->_conInfos['password'] = M_DBPASSWORD;
            $this->_conInfos['port'] = M_DBPORT;
            $this->_link = false;
            $this->log = array();
            
            $this->_dbInterface = $dbInterface;  
    }

    public function displayError($no_msg ) {
        if(is_bool($no_msg)) {
            $this->_link = $this->_dbInterface->setLog($no_msg);
        }   
    }
    
    /**
     * connect
     * @description  Procède à la connexion et crée le pointeur $_link
     */
    public function connect($no_msg = false)
	{
        try {
            $this->_link = $this->_dbInterface->connect($this->_conInfos, $no_msg);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
        return $this->_link;
    }
    

    /**
     * close
     * @description Supprime le pointeur de connexion
     */
    public function close($link) 
    {
        $this->link = null;
        $retour = false;
        if ($this->link === null) {
            $retour = true;
        }
        return $retour;
    }

    /**
     * select_db
     * Sélectionne la base de données $db
	 * Retourne TRUE en cas de succès, FALSE sinon
     */
    public function selectDb($db) 
    {
        $retour = $this->_dbInterface->selectDb($this->_link, $db);
        if ($retour !== false) {
			$this->_conInfos['dbase'] = $db;
		}
		return $retour;
    }

    /* Envoie la requête SQL $req pour son execution
	   Retourne un resultSet */
    public function execPreparedQuery($query, $args=null, $again = false) 
    {
		return $this->_dbInterface->execPreparedQuery($this->_link, $query, $args, $again);
    }

    /* Envoie la requête SQL $req pour son execution
	   Retourne un resultSet */
       public function execQuery($query) 
       {
           return $this->_dbInterface->execQuery($this->_link, $query);
       }

    /**
     * fetchRow
     * Retourne un tableau énulméré qui correspond à la ligne demandée, ou FALSE si il ne reste plus de ligne
	 * Chaque appel suivant retourne la ligne suivante dans le résultat, ou FALSE si il n'y a plus de ligne disponible 
     */
    
	public function fetchRow($resultSet=null) {
        $results = $this->_dbInterface->fetchRow($resultSet);
		return $results;    
	}
    
    /**
     * numRows
     * Indique le nombre de lignes retour,ées par le requête à partir d'un resultSet
     * prélablement executé par execQuery
     */
	public function numRows($resultSet) {
		return $this->_dbInterface->numRows($resultSet);
	}
    /** 
     * fetchArray
     * Retourne un tableau associatif par clé qui correspond à la ligne demandée, ou FALSE si il ne reste plus de ligne
	 * Chaque appel suivant retourne la ligne suivante dans le résultat, ou FALSE si il n'y a plus de ligne disponible 
     */
    public function fetchArray($resultSet) 
    {
		$results = $this->_dbInterface->fetchArray($resultSet);
		return $results;
	}
    
    /**
     * escapeString
     * Met en quote les paramètres d'entrée de la requête
     */
    public function escapeString($arg) 
    {
        return $this->_dbInterface->escapeString($this->link, $arg);
    }

    /**
     * GetTableDatas
     */

     /**
      * GetTableDatas
      * @description Retourne un tableau  
      */
    public function getTableDatas($tableName) {
         $query = ' SELECT COLUMN_NAME AS nomchamp, DATA_TYPE AS typechamp, is_nullable'
                .' FROM INFORMATION_SCHEMA.COLUMNS' 
                .' WHERE TABLE_SCHEMA = \'' . $this->_conInfos['dbase'] . '\''
                .' AND TABLE_NAME = \'' . $tableName. '\'' 
                .' AND NOT COLUMN_NAME = \'id\'';
        $resultSet =  $this->_dbInterface->getTableDatas($this->_link, $query);
        $results = $this->fetchArray($resultSet);
        return $results;
    }

    public function queryPlaceholders() {
        $author1 = "";
        if (ctype_alpha($_GET['author1'])) {
            $author1 = $_GET['author1'];
        }
         // $stmt = $this->link->prepare('SELECT foo FROM bar');
         $sql = 'SELECT author.*, book.*
                 FROM author
                LEFT JOIN book ON author.id = book.author_id
                WHERE author.last_name = ?';
         
        $stmt = $this->link->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->bindParam(1, $author1, PDO::PARAM_STR, 20);

        /* Exécute la première requête */
        $stmt->execute();

        /* Récupération de tout les résultats */
        $results = $stmt->fetchAll();
        foreach ($results as $row) {
            echo "{$row->title}, {$row->last_name}\n";
            }

        /* L'appel suivant à closeCursor() peut être requis par quelques drivers */
        $stmt->closeCursor();
        return $results;
    }


    /**
     * @name         create
     * @description  Insère un tableau nom champ - valeur en base
     * @param String $tableName : nom de la table concernée par l'insertion
     * @param array  $tabInsert :tableau associatif avec nom du champ = clé et valeur associée
     */
    public function create($tableName, $tabInsert)
    {
        $sqlData = 'VALUES (';
            // fonction "raccourci" qui effectue une simple reconversion d'une chaîne
        $sqlInsert = 'INSERT INTO ' . $tableName . ' (';
        $i = 0;
        $max = count($tabInsert)-1;
        foreach ($tabInsert as $key=>$value) {
            $sqlInsert .= $key;
            $sqlData .= '\'' . utf8_decode($value) . '\'';
            if ($i<$max) {
                $sqlInsert .= ', ';
                $sqlData .= ', ';
            } else {
                $sqlInsert .= ') ';
                $sqlData .= ') ';
            }
            $i++;
        }
        
        $sql = $sqlInsert . $sqlData;
        try{
            $retour = $this->execPreparedQuery($sql);
        }catch(Exception $e){
            $this->_log = $e->getMessage();
            $retour = false;
        }
        return $retour;
        
            
    }
    



}
