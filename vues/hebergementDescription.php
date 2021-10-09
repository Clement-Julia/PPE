<?php
require_once "header.php";

// (SECURITE) On vérifie que le paramètre récupéré est bien du type INT attendu
if (!empty($_GET['idHebergement']) && is_numeric($_GET['idHebergement'])){
    $doesItExist = new Hebergement($_GET["idHebergement"]);
    // (VERIFICATION) On vérifie que l'hébergement existe réellement
    if($doesItExist->getIdHebergement() != null){
        // (VERIFICATION) On vérifie que l'hébergement est bien présent dans la ville choisi
        if($doesItExist->getIdVille() == $_SESSION['idVille']){

            $ReservationVoyage = new ReservationVoyage();
            $isBuilding = $ReservationVoyage->getIsBuildingByUserId($_SESSION['idUtilisateur']);
            if (empty($isBuilding)){

                $dateDebut = new DateTime($_SESSION['date']);
                $NextMonth = new DateTime($dateDebut->format('Y-m-d') . '+1 month');

            } else {
                
                $lastReservation = $ReservationVoyage->getLastReservationHebergement($isBuilding['idReservationVoyage']);
                $dateDebut = new DateTime($lastReservation['dateFin']);
                $NextMonth = new DateTime($lastReservation['dateFin'] . '+1 month');
            }

            $_SESSION['idHebergement'] = $_GET['idHebergement'];

            $today = new DateTime();

            $Calendar = new Month($dateDebut->format('m'), $dateDebut->format('y'));
            $NextCalendar = new Month($NextMonth->format('m'), $NextMonth->format('y'));

            $lastmonday = $Calendar->getStartingDay()->format('N') === '1' ? $Calendar->getStartingDay() : $Calendar->getStartingDay()->modify('last monday');
            $nextLastmonday = $NextCalendar->getStartingDay()->format('N') === '1' ? $NextCalendar->getStartingDay() : $NextCalendar->getStartingDay()->modify('last monday');

            $Hebergement = new Hebergement($_GET["idHebergement"]);
            $bookingDates = $Hebergement->getWhenHebergementIsBooking($Hebergement->getIdHebergement(), $dateDebut->format('y-m-d'));

            $Favoris = new Favoris($_SESSION['idHebergement'], $_SESSION['idUtilisateur']);
            ?>

            <div data-idHebergement="<?=$_GET["idHebergement"]?>" id="hebergement-description-container">
                <div id="hd-title-container">
                    <div id="hd-title"><a href="hebergementVille.php?idVille=<?=$Hebergement->getIdVille()?>" class="btn btn-sm btn-secondary back-button"><</a><?= $Hebergement->getLibelle() ?></div>
                    <div id="hd-infos">
                        <div id="hd-rate"></div>
                        <div id="hd-heart"><?=$Favoris->getIdHebergement() == null ? "<i class='far fa-heart'></i>" : "<i class='fas fa-heart'></i>"?></div>
                    </div>
                </div>
                <div id="hd-pictures">
                    <div id="big-img">
                        <img src="../src/img/default-hotel.jpg" alt="" class="img-fluid">
                    </div>
                    <div id="little-img">
                        <div class="second-div-img">
                            <div class="third-div-img">
                                <img src="../src/img/default-hotel.jpg" alt="" class="img-fluid">   
                            </div>
                            <div class="third-div-img">
                                <img src="../src/img/default-hotel.jpg" alt="" class="img-fluid radius-top-right">
                            </div>
                        </div>
                        <div class="second-div-img">
                            <div class="third-div-img">
                                <img src="../src/img/default-hotel.jpg" alt="" class="img-fluid">
                            </div>
                            <div class="third-div-img">
                                <img src="../src/img/default-hotel.jpg" alt="" class="img-fluid radius-bottom-right">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="hd-description-container">
                    <div id="hd-description" class="card">
                        <div class="card-header"><h6>Description</h6></div>
                        <div class="card-body"><?= $Hebergement->getDescription() ?></div>
                        
                    </div>
                        <div class="card">
                            <div class="card-header"><h6>Ce que propose le logement : </h6></div>
                            <div class="card-body d-flex flex-wrap">
                        <?php
                        foreach ($Hebergement->getOptions() as $item){
                            ?>
                                <div class="hd-tools-item"><?=$item->getIcon()?><span><?=$item->getLibelle()?></span></div>
                            <?php
                        }
                        ?>
                            </div>
                        </div>
                </div>

                <div id="hd-date-price-container">
                    <div id="hd-date" class="card">
                        <div class="card-header"><h6>Calendrier</h6></div>
                        <div id="calendar-container" class="card-body">
                            <div class="calendar">
                                <div class="calendar-header"><?= $Calendar->toString();?></div>
                                <table id="table1" class="calendar__table calendar__table--<?=$Calendar->getWeeks();?>weeks">
                                    <tr>
                                        <?php foreach($Calendar->days as $day){?>
                                            <th>
                                                <?=$day;?>
                                            </th>
                                        <?php } ?>
                                    </tr>
                                <?php for ($i = 0; $i < $Calendar->getWeeks(); $i++){ ?>
                                    <tr>
                                        <?php foreach($Calendar->days as $k => $day){
                                            $date = (clone $lastmonday)->modify("+" . ($k + $i * 7) ." days") ?>
                                            <td>
                                                <div id="<?=$date->format("Y-m-d")?>" class="
                                                <?=$Calendar->withinMonth($date) ? '' : 'calendar__overmonth';?> 
                                                <?=$date->format("Y-m-d") == $dateDebut->format("Y-m-d") && $Calendar->withinMonth($date) ? 'date-debut' : '';?> 
                                                <?= in_array($date->format("Y-m-d"), $bookingDates) ? 'booking' : '';?>
                                                <?= !in_array($date->format("Y-m-d"), $bookingDates) &&  !($date->format("Y-m-d") < $today->format("Y-m-d")) ? 'selectable' : '';?>
                                                "><?= $date->format('d');?></div>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                                </table>
                            </div>

                            <div class="calendar">
                                <div class="calendar-header"><?= $NextCalendar->toString();?></div>
                                <table id="table2" class="calendar__table calendar__table--<?=$NextCalendar->getWeeks();?>weeks">
                                    <tr>
                                        <?php foreach($NextCalendar->days as $day){?>
                                            <th>
                                                <div><?=$day;?></div>
                                            </th>
                                        <?php } ?>
                                    </tr>
                                <?php for ($i = 0; $i < $NextCalendar->getWeeks(); $i++){ ?>
                                    <tr>
                                        <?php foreach($NextCalendar->days as $k => $day){
                                            $date = (clone $nextLastmonday)->modify("+" . ($k + $i * 7) ." days") ?>
                                            <td>
                                                <div id="<?=$date->format("Y-m-d")?>" class="
                                                <?=$NextCalendar->withinMonth($date) ? '' : 'calendar__overmonth';?>
                                                <?=$date->format("Y-m-d") == $dateDebut->format("Y-m-d") && $NextCalendar->withinMonth($date) ? 'date-debut' : '';?> 
                                                <?= in_array($date->format("Y-m-d"), $bookingDates) ? 'booking' : '';?>
                                                <?= !in_array($date->format("Y-m-d"), $bookingDates) &&  !($date->format("Y-m-d") < $today->format("Y-m-d")) ? 'selectable' : '';?>
                                                "><?= $date->format('d');?></div>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>


                    <div id="hd-price" class="card">
                        <div class="card-header"><h6>Détail du prix</h6></div>
                        <div class="card-body">
                            <span>Vous allez réserver : </span>
                            <span id="nbJours">0 nuit</span> x <span id="prixHebergement" data-prix="<?=$Hebergement->getPrix()?>"><?=$Hebergement->getPrix()?> €</span> = <span id="prix">0 €</span>
                        </div>
                    </div>
                    <div>
                        <div id="alert-danger" class="alert alert-danger d-none"></div>
                    </div>
                </div>
                <?php if(!empty($_GET['error'])){ ?>
                    <div class="alert alert-warning">Les dates sélectionnées ne sont pas valide. Veuillez selectionner une plage de date libre.</div>
                <?php } ?>
                <div class="card">
                    <div class="card-body d-flex justify-content-center">
                        <button id="submit" class="btn btn-success btn-sm">Valider</button>
                        <div id="hidden" class="d-none">
                            <div>Souhaitez vous ajoutez une destination à votre voyage ?</div>
                            <div>
                                <button id="submitYes" class="btn btn-sm btn-success">Oui</button>
                                <button id="submitNo" class="btn btn-sm btn-secondary">Non</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="hd-avis"></div>
            </div>

            <script src="../js/hebergementDescription.js"></script>
            <script src="../js/moment.js"></script>
        <?php

        }
        else { ?>
            <div class="alert alert-warning">L'hébergement sélectionné ne correspond pas à la ville choisie</div>
        <?php }

    } else { ?>
        <div class="alert alert-warning">L'hébergement sélectionné n'existe pas</div>
    <?php }

} else { ?>
    <div class="alert alert-warning">Un problème est survenu avec les paramètres</div>
<?php } ?>

<?php
require_once "footer.php";
?>