<?php


/**
 * Event
 * Classe gérant les types d'événements
 * CRUD
 * @author CV170C7N
 */
class Localisation {
    
    private $_dbaccess;
    private $_sql;
    private $_type;
    
    public function __construct($dbaccess, $type = 'site') 
    {
        $this->_dbaccess = $dbaccess;
        $this->_sql = '';
        $this->_type = $type;
        $this->_requeteJointures = " INNER JOIN departement on service.departement_id = departement.id " 
                                 . " INNER JOIN site on departement.site_id = site.id ";
    }
    
    public function getSql()
    {
        return $this->_sql;
    }

    public function setType($type) 
    {
        $this->_type = $type;
    }
    
    public function getAll()
    {
        $results = array();
        $this->_sql = 'SELECT DISTINCT * '.
                      ' FROM ' . $this->_type;
        $reponse = $this->_dbaccess->execQuery($this->_sql);
        $results=$this->_dbaccess->fetchArray($reponse);
        return $results;
    }


    
    
    public function update($typeId, $typeLibelle, $typeDescription, $key = null) {
        $requeteSup = '';
        switch($this->type) {
            case 'departement':
                $requeteSup = ', site_id = \'' . $key . '\'';
            break;

            case 'site':
                $requeteSup = ', departement_id = \'' . $key . '\'';
            break;
        }
        
        $this->_sql = 'UPDATE ' . $this->_type . ' set libelle  = \''.$typeLibelle
                   . '\', description = \''.$typeDescription.'\''. $requeteSup
                   . ' WHERE id = '.$typeId;
        try{
            $retour = $this->_dbaccess->execQuery($this->_sql);
        }catch(Exception $e){
            $retour = false;
        }
        return $retour;
    }
    
    public function create($tabInsert)
    {
        try{
            $retour = $this->_dbaccess->create($this->_type, $tabInsert);
        }catch(Exception $e){
            $retour .= 'Table: ' . $this->_type;
        }
        return $retour;   
    }

    /**
     * @name GetDepartementsBySite
     * @description
     * - Dans un contexte de recherche (param $contexteInsertion = false):
     * retourne un tableau de tous les libellés de departements
     * - Dans un contexte d'un formulaire d'enregistrement de ressources: 
     * (param $contexteInsertion = true):  retourne un tableau de tous les id 
     * ET libellés des departements, ou ceux en fonction d'un site donné ($siteId)
     * Le paramètre whereLibelle a été rajouté pour forcer la correspondance avec un 
     * libellé de site avec un contexte autre que la recherche  ($contexteInsertion = true)
     * 
     * @param mixed $siteId 
     * @param bool $contexteInsertion 
     * @param bool $whereLibelle 
     * @return mixed  
     */
    public function getDepartementsBySite($siteId = null, $contexteInsertion = false){
        $rs = false;
        $champId = ($contexteInsertion === false) ? "" : "departement.id,";
        $requete = "SELECT DISTINCT " . $champId  . " departement.libelle "
                . " FROM departement ";
                
        $tabDepartements = array();
        $champWhere = "";
        if($siteId !== null && $siteId <> 0){
            
            $requete .=  " INNER JOIN site on departement.site_id = site.id "
                      . " WHERE site.id = " . $siteId;
                      
        }
        $requete .=  " ORDER BY departement.libelle";
        try {
            $rs = $this->_dbaccess->execQuery($requete);
            $results=$this->_dbaccess->fetchRow($rs);
            $i = ($contexteInsertion === false) ? 0 : 1;
            foreach ($results as $ligne) {
                $tabDepartements[$ligne[0]]=$ligne[$i];
            }
    
            return $tabDepartements;
        
        } catch(Exception $e){
            return $e->getMessage();
        }
        
        

        
    }
    
    /**
     * @name GetServicesByDepartement
     * @description
     * - Dans un contexte de recherche (param $contexteInsertion = false):
     * retourne un tableau de tous les libellés de services
     * - Dans un contexte d'un formulaire d'enregistrement de ressources: 
     * (param $contexteInsertion = true):  retourne un tableau de tous les id 
     * ET libellés des services, ou ceux en fonction d'un département donné ($siteId)
     * 
     * 
     * @param int    $siteId 
     * @param string $departementLibelle 
     * @param bool $contexteInsertion 
     * 
     * @return array  
     */
    public function getServicesByDepartement($siteId = null, $departement = null, $contexteInsertion = false)
    {
        $rs = false;
        $champId = ($contexteInsertion === false) ? "" : "service.id,";
        $requete = "SELECT DISTINCT " . $champId  . " service.libelle "
                . " FROM service ";
        $requete .= $this->_requeteJointures;
                
        $tabServices = array();

        $champWhere = ($contexteInsertion === false) ? "libelle" : "id";
        if($siteId !== null && $siteId <> 0){
            $requete.= " AND site.id = " . $siteId;
        }

        if($departement != null && $departement!= 'Tous *'){

            $requete.=  " AND departement." . $champWhere . " = '" . $departement ."'";
        }
        $requete.= " ORDER BY service.libelle";

        

	    $rs = $this->_dbaccess->execQuery($requete);
        $results=$this->_dbaccess->fetchRow($rs);
        $i = ($contexteInsertion === false) ? 0 : 1;
        foreach ($results as $ligne) {
            $tabServices[$ligne[0]]=$ligne[$i];
        }
        return $tabServices;
    }
    
    
}
