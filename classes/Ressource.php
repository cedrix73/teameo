<?php

/**
 * Classe gérant les ressources
 *
 * @author CV170C7N
 */
class Ressource {
    
    protected $dbaccess;
    protected $site;
    protected $service;
    protected $domaine;
    protected $tabRessources;
    protected $requeteRessources;
    protected $requeteJointures;
    private $_tableName;
    
    public function __construct($dbaccess) 
    {
        $this->dbaccess = $dbaccess;
        $this->_tableName = "ressource";
        $this->siteId = false;
        $this->departementLibelle = false;
        $this->serviceLibelle = false;
        $this->tabRessources =  array();
        $this->requeteSelect = "SELECT ressource.id as ressource_id, ressource.nom, ressource.prenom, "
        . " site.libelle, departement.libelle, service.libelle, ressource.statut "
        . " FROM " . $this->_tableName;
        $this->requeteJointures = " INNER JOIN service on ressource.service_id = service.id " 
                               . " INNER JOIN departement on service.departement_id = departement.id " 
                               . " INNER JOIN site on departement.site_id = site.id ";
        
        
    }
    
    /**
     * GetRessourcesBySelection
     * @description  Sort les ressources en Ajax en fonction 
     * des valeur (chaine) sélectionnées à partir ds
     * combobox site, département et service du formulaire
     * 
     * @param int    $siteId             
     * @param string $departementLibelle 
     * @param string $serviceLibelle     
     * 
     * @return array
     */
    public function getRessourcesBySelection($site = null, $departementLibelle = '', $serviceLibelle = ''){
        $requete = $this->requeteSelect . $this->requeteJointures;
        $requete . " WHERE dateSortie IS NULL";
        // Traitement sites
        if($site != null && $site!='Tous*'){
            $this->siteId = $site;
            $requete.= " AND site.id = '" . $this->siteId ."'";
        }

        // Traitement departements
        if($departementLibelle != null && $departementLibelle != 'Tous*'){
            $this->departementLibelle = $departementLibelle;
            $requete.= " AND departement.libelle = '" . $this->departementLibelle ."'";
        }
        
        // Traitement services
        if($serviceLibelle != null && $serviceLibelle != 'Tous*'){
            $this->serviceLibelle = $serviceLibelle;
            $requete.= " AND service.libelle = '" . $this->serviceLibelle ."'";
        } 
        
        $requete.= " ORDER BY ressource.nom ";
	    $rs = $this->dbaccess->execQuery($requete);
        $results=$this->dbaccess->fetchArray($rs);
        
        foreach ($results as $ligne) {
            $id = $ligne['ressource_id'];
            unset($ligne['ressource_id']);
            $this->tabRessources[$id]=$ligne;
        }
        return $this->tabRessources;
    }
    
    
    
    
    /**
     * GetRessourceById
     * Retourne l'id, le nom, prénom d'une ressource
     * ainsi que son affectation (site, département et service)
     * 
     * @param int $idRessource 
     * @return array
     */
    public function getRessourceById($idRessource)
    {
        $ressource = array();
        $requete = $this->requeteSelect . $this->requeteJointures;
        $requete .= " WHERE dateSortie IS NULL"
                 . " AND id = " . $idRessource;
        $rs = $this->dbaccess->execQuery($requete);
        $ressource=$this->dbaccess->fetchArray($rs);
        return $ressource;
    }

    /**
     * Create
     * Enregistre une ressource en base de donnée
     */
    public function create($tabInsert)
    {

        $retour = $this->dbaccess->create($this->_tableName, $tabInsert);
        if($retour !== false){
            $retour = "La ressource a été correctement enregistrée !";
        }else{
            $retour = "Erreur: Un problème est survenu lors de la création d\'un collaborateur.";
        }
        return $retour;   
    }
}
