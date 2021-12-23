<?php
require_once "../controleurs/traitement.php";

if (!empty($_SESSION['idUtilisateur'])){
    $ReservationVoyage = new ReservationVoyage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
   integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
   crossorigin=""/>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <script src="https://kit.fontawesome.com/f3f16a7b72.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="../assets/src/img/logo.png">
    <title>locallacol</title>
</head>
<body>

<?php
if(empty($_COOKIE["accept_cookies"])){
    require_once "modalsCookies.php";
}
if(!empty($_COOKIE["connection_cookies"])){
    $Utilisateur = new Utilisateur();
    $user = $Utilisateur->getUserByConnectionCookies($_COOKIE["conection_cookies"]);
    if($user["idUtilisateur"] != null){
        $_SESSION["idUtilisateur"] = $user["idUtilisateur"];
        $_SESSION["nom"] = $user["nom"];
        $_SESSION["prenom"] = $user["prenom"];
        $_SESSION["idRole"] = $user["idRole"];
        $_SESSION["email"] = $user["email"];
    }
}
?>

    <!-- on refait toutes la navbar -->


<nav id="navbar" class="navbar navbar-expand-lg navbar-light bg-light z-index">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav align-items-center">
                <a class="nav-link active" aria-current="page" href="index.php"><img id="logo" src="../assets/src/img/locallacol.png" alt="Logo du site locallacol"></a>
                <?=(!empty($_SESSION["idRole"]) && $_SESSION["idRole"] == 2 ? "<a href='../admin' class='nav-link'>Accès admin</a>" : "");?>
                <?= !empty($_SESSION['idUtilisateur']) && $ReservationVoyage->getIdBuildingTravelByUserId($_SESSION['idUtilisateur']) != null ? "<a href='resumeTravel.php?building=1' class='nav-link'>Continuer ?</a>" : "" ;?>
            </div>
        </div>

        <?=(!empty($_SESSION["idUtilisateur"]) ? "" : "<a href='inscription.php' class='btn-group btn btn-outline-primary btn-sm align-self-center ml-auto'>Inscription</a><a href='connexion.php' class='btn btn-outline-success btn-sm align-self-center'>Connexion</a>");?>
        <?php if(!empty($_SESSION["idUtilisateur"])){
                ?>
                <div class="dropdown me-3">
                    <a class="btn dropdown-toggle" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= $_SESSION["prenom"] ." " . $_SESSION["nom"]?>
                    </a>
            
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <!-- <li><a class="dropdown-item" href="historique.php">Historique</a></li> -->
                        <li><a class="dropdown-item" href="avis.php">Avis</a></li>
                        <li><a class="dropdown-item" href="favoris.php">Favoris</a></li>
                        <li><a class="dropdown-item" href="profil.php">Paramètres</a></li>
                        <li><a class="dropdown-item li" href="../controleurs/deconnexion.php">Déconnexion</a></li>
                    </ul>
                </div>
                <?php
            }
        ?>
    </div>
</nav>