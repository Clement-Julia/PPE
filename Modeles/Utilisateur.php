<?php

class Utilisateur extends Modele {

    private $idUtilisateur;
    private $email;
    private $mdp;
    private $nom;
    private $prenom;
    private $age;
    private $birth;
    protected $idRole;
    private $token;
    private $messages = [];
    private $avis = [];

    public function __construct($idUtilisateur = null){

        if ( $idUtilisateur != null ){

            $requete = $this->getBdd()->prepare("SELECT * FROM utilisateurs WHERE idUtilisateur = ?");
            $requete->execute([$idUtilisateur]);
            $infoUser =  $requete->fetch(PDO::FETCH_ASSOC);

            $this->idUtilisateur = $infoUser["idUtilisateur"];
            $this->email = $infoUser["email"];
            $this->mdp = $infoUser["mdp"];
            $this->nom = $infoUser["nom"];
            $this->prenom = $infoUser["prenom"];
            $this->idRole = $infoUser["idRole"];
            $this->birth = $infoUser["DoB"];
            $this->token = $infoUser["token"];

        }
        
    }

    public function inscription($email, $mdp, $nom, $prenom, $age, $idRole, $rgpd){

        $mdp = password_hash($mdp, PASSWORD_BCRYPT);
        $requete = $this->getBdd()->prepare("INSERT INTO utilisateurs(email, mdp, nom, prenom, DoB, idRole, acceptRGPD, dateAcceptRGPD) VALUES (?, ?, ?, ?, ?, ?, ?, now())");
        $requete->execute([$email, $mdp, $nom, $prenom, $age, $idRole, $rgpd]);

    }

    public function connexion($email, $mdp){ 
        
        $requete = $this->getBdd()->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $requete->execute([$email]);

        if($requete->rowCount() > 0){

            $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);
            
            if(!password_verify($mdp, $utilisateur["mdp"])){
                $return["success"] = false;
                $return["error"] = 1;
            }else{

                $this->idUtilisateur = $utilisateur["idUtilisateur"];
                $this->idRole = $utilisateur["idRole"];
                $this->email = $utilisateur["email"];
                $this->nom = $utilisateur["nom"];
                $this->prenom = $utilisateur["prenom"];
                $_SESSION["idUtilisateur"] = $this->getIdUtilisateur();
                $_SESSION["nom"] = $this->getNom();
                $_SESSION["prenom"] = $this->getPrenom();
                $_SESSION["idRole"] = $this->getIdRole();
                $_SESSION["email"] = $this->getEmail();
                $_SESSION["mdp"] = $mdp;

                $return["success"] = true;
                $return["error"] = 0;
            }


        }
        return $return;
    }
    
    function check_mdp_format($mdp){

        $erreursMdp = [];
        $minuscule = preg_match("/[a-z]/", $mdp);
        $majuscule = preg_match("/[A-Z]/", $mdp);
        $chiffre = preg_match("/[0-9]/", $mdp);
        $caractereSpecial = preg_match("/[^a-zA-Z0-9]/", $mdp);
        $str = strlen($mdp);
    
        if(!$minuscule){
            $erreursMdp[] = 4;
        }
        if(!$majuscule){
            $erreursMdp[] = 5;
        }
        if(!$chiffre){
            $erreursMdp[] = 6;
        }
        if(!$caractereSpecial){
            $erreursMdp[] = 7;
        }
        if($str < 8){
            $erreursMdp[] = 8;
        }
    
        return $erreursMdp;
    }

    // vérification si l'email est déjà présent dans la base de donnée
    public function emailExiste($email){

        $requete = $this->getBdd()->prepare("SELECT email FROM utilisateurs WHERE email = ?");
        $requete->execute([$email]);
        $emailExist = $requete->fetch(PDO::FETCH_ASSOC);

        return $emailExist;

    }

    public function getIdUtilisateur(){
        return $this->idUtilisateur;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getMdp(){
        return $this->mdp;
    }

    public function getNom(){
        return $this->nom;
    }

    public function getPrenom(){
        return $this->prenom;
    }

    public function getAge(){
        return $this->age;
    }

    public function getBirth(){
        return $this->birth;
    }

    public function getAgeByDate(){
        $birth = $this->birth;
        $now = date("Y-m-d");
        $diff = date_diff(date_create($birth), date_create($now));
        return $diff->format('%y');
    }

    public function getIdRole(){
        return $this->idRole;
    }

    public function getMessages(){
        return $this->messages;
    }

    public function countUser(){
        $requete = $this->getBdd()->prepare("SELECT count(idUtilisateur) as nbr from utilisateurs");
        $requete->execute();
        $info_nbr = $requete->fetch(PDO::FETCH_ASSOC);
        return $info_nbr;
    }

    public function getAllUsers(){
        $requete = $this->getBdd()->prepare("SELECT * from utilisateurs");
        $requete->execute();
        $info = $requete->fetchALL(PDO::FETCH_ASSOC);
        return $info;
    }

    public function acceptCookies(){
        setcookie("accept_cookies", 1, timestampAddDay(1), "/");
    }

    public function setConnectionCookies(){
        $codeCookies = bin2hex(random_bytes(50));
        $nbLigneRequete = 1;
        while($nbLigneRequete != 0){
            $requete = $this->getBdd()->prepare("SELECT COUNT(*) as result FROM utilisateurs WHERE token = ?");
            $requete->execute([$codeCookies]);
            $nbLigneRequete = $requete->fetch(PDO::FETCH_ASSOC)['result'];
            if($nbLigneRequete == 1){
                $codeCookies = bin2hex(random_bytes(50));
            }
        }

        try{
            $requete = $this->getBdd()->prepare("UPDATE utilisateurs SET token = ? WHERE idUtilisateur = ?");
            $requete->execute([$codeCookies, $this->idUtilisateur]);
        } catch (Exception $e){
            return false;
        }
        setcookie("connection_cookies", $this->idUtilisateur . "-" .$codeCookies, timestampAddDay(30), "/");
        return true;
    }

    public function getUserByConnectionCookies($COOKIES){

        $cookiesExploded = explode('-', $COOKIES);
        $idUser = $cookiesExploded[0];
        $token = $cookiesExploded[1];

        $requete = $this->getBdd()->prepare("SELECT * FROM utilisateurs WHERE idUtilisateur = ? AND token = ?");
        $requete->execute([$idUser, $token]);
        return $requete->fetch(PDO::FETCH_ASSOC);

    }

    public function getUserByEmail($email){
        $requete = $this->getBdd()->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $requete->execute([$email]);
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

}