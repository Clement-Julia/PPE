<?php

class Option extends Modele {

    private $idOption;
    private $libelle;
    private $icon;

    public function __construct($idOption = null){

        if ( $idOption != null ){

            $requete = $this->getBdd()->prepare("SELECT * FROM options WHERE idOption = ?");
            $requete->execute([$idOption]);
            $infoRegion =  $requete->fetch(PDO::FETCH_ASSOC);

            $this->idOption = $infoRegion["idOption"];
            $this->libelle = $infoRegion["libelle"];
            $this->icon = $infoRegion["icon"];

        }
        
    }

    public function initialiserRegion($idRegion, $libelle){

        $this->idOption = $idOption;
        $this->libelle = $libelle;
        $this->icon = $icon;

    }

    public function getIdOption(){
        return $this->idRegion;
    }

    public function getLibelle(){
        return $this->libelle;
    }

    public function getAllOption(){
        $requete = $this->getBdd()->prepare("SELECT * FROM options");
        $requete->execute();
        $Alloption = $requete->fetchALL(PDO::FETCH_ASSOC);
        return $Alloption;
    }

    public function getOptionChecked($idHebergement){
        $requete = $this->getBdd()->prepare("SELECT * FROM options_by_hebergement where idHebergement = ?");
        $requete->execute([$idHebergement]);
        $Alloption = $requete->fetchALL(PDO::FETCH_ASSOC);
        return $Alloption;
    }

    public function addOptions($idHebergement, $options){

        $nbr = count($options);
        $str = "";
        $array = [];
        $req = "INSERT INTO options_by_hebergement(idHebergement, idOption) VALUES ";
        for($i=0; $i < $nbr; $i++){
            $str .= "(?, ?),";
            $array[] = $idHebergement;
            $array[] = $options[$i];
        };
        $str = substr($str, 0, -1);

        $requete = $this->getBdd()->prepare($req . $str);
        $requete->execute($array);
    }

    public function supOptions($idHebergement){
        $requete = $this->getBdd()->prepare("DELETE FROM options where idHebergement = ?");
        $requete->execute([$idHebergement]);
    }

}