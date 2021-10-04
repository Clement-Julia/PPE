<?php
require_once "header.php";

unset($_SESSION['idReservationHebergement']);
// (SECURITE) On vérifie que le paramètre récupéré est bien du type INT attendu
if ((!empty($_GET['idRegion']) && is_numeric($_GET['idRegion'])) || !isset($_GET['idRegion'])){

    if (!empty($_GET['idRegion'])){
        $_SESSION['idRegion'] = $_GET['idRegion'];
    }
    $ReservationVoyage = new ReservationVoyage();
    $BuildingTravelId = $ReservationVoyage->getIdBuildingTravelByUserId($_SESSION['idUtilisateur']);
    if($BuildingTravelId != null){
        $ReservationVoyage = new ReservationVoyage($BuildingTravelId);
    }

    // On récupère l'idRegion soit par le GET si l'utilisateur est en train de renseigner le début de son voyage (pas encore inscrit en BDD) ou bien alors par la BDD
    $idRegion = isset($_GET['idRegion']) ? $_GET['idRegion'] : $ReservationVoyage->getIdRegionForBuildingTravelByUserId($_SESSION['idUtilisateur']);

    $Lodging = new Region($idRegion);
    $Lodgings = $Lodging->getVilles();

    ?>
    <div id="ligne-points">
    <?php
    // On récupère les lat et lng des reservations hébergement fait par l'utilisateur sur un voyage en cours de construction
    foreach ($ReservationVoyage->getVilleLatLngByUserId($_SESSION['idUtilisateur']) as $latLngVille){ ?>
        <div data-lat="<?=$latLngVille['latitude']?>" data-lng="<?=$latLngVille['longitude']?>"></div>
    <?php } ?>
    </div>

    <div id="create-travel-container">
        <div id="ct-choose-town">
            <div id="choose-town-top">
                <?php 
                if($BuildingTravelId != null){
                    $index = 1;
                    foreach ($ReservationVoyage->getReservationHebergement() as $reservationHebergement){
                        $infos = $reservationHebergement->getHebergementById($reservationHebergement->getIdHebergement());
                        ?>
                        <div class="mx-3 my-3 ct-resume-container">
                            <div class="card ct-resume">
                                    <div class="card-header">Etape : <?=$index?></div>
                                    <div class="d-flex">
                                        <div class="card-body ct-p-container">
                                            <p class="card-text">Ville : <?=$infos['villeNom']?></p>
                                            <p class="card-text">Hébergement : <?=$infos['nomHebergement']?></p>
                                            <p class="card-text">Description hébergement : <?=$infos['description']?></p>
                                            <p class="card-text">Date d'arrivée : <?=$reservationHebergement->getDateDebut()?></p>
                                            <p class="card-text">Date de départ : <?=$reservationHebergement->getDateFin()?></p>
                                            <p class="card-text">Code réservation : <?=$reservationHebergement->getCodeReservation()?></p>
                                            <p class="card-text">Prix : <?=$reservationHebergement->getPrix()?></p>
                                        </div>

                                        <div class="edit-container">
                                            <button class="btn btn-sm btn-warning editButton">Modifier</button>
                                            <form action="../controleurs/editTravel.php" method="POST" class="editForm">
                                                <select name="options[<?=$reservationHebergement->getIdReservationHebergement()?>]" class="form-control">
                                                    <option disabled selected>Modifier ...</option>
                                                    <option value="1">La date du voyage</option>
                                                    <option value="2">La ville</option>
                                                    <option value="3">L'hotel</option>
                                                    <option value="4">Supprimer cette étape</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-warning">Modifier</button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                
                                

                            </div>
                        </div>
                        <?php
                        $index++;
                    }
                }
                ?>
                </div>

<!-- ------------------------------------------- -->
                <div id="total-prix-container"><?= $BuildingTravelId != null ? "Le prix total de votre voyage est de : " . $ReservationVoyage->getPrix() . " €" : "" ?></div>
                <a href="resumeTravel.php" class="btn btn-sm btn-success">Valider voyage</a>
<!-- ------------------------------------------- -->

                
            <div id="choose-town-bot">
                <?php
                foreach ($Lodgings as $item)
                { ?>
                    <div class="col-md-4 mb-3 col-lg-3">
                        <div data-id="<?= $item->getIdVille()?>" data-name="<?= $item->getLibelle()?>" data-lat="<?= $item->getLatitude()?>" data-lng="<?= $item->getLongitude()?>" data-zoom="9" class="card ct-a js-marker">
                            <img class="img-fluid" alt="100%x280" src="https://images.unsplash.com/photo-1530735606451-8f5f13955328?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1470&q=80">
                            <div class="card-body ct-text-ville">
                                <h6 class="card-title"><?= $item->getLibelle()?></h6>
                            </div>
                        </div>
                    </div>
                <?php }
                ?>
            </div>
        </div>
        <div data-lat="<?=$Lodging->getLatitude();?>" data-lng="<?=$Lodging->getLongitude();?>" data-zoom="8" class="map" id="map"></div>
    </div>



    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
    integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script src="../js/app.js"></script>

<?php } else { ?>
    <div class="alert alert-warning">Un problème est survenu avec les paramètres</div>
<?php } ?>

<script src="../js/createTravel.js"></script>

<?php
require_once "footer.php";
?>