<?php
require_once "headerAdmin.php";

$admin = new Admin();
$hea = $admin->getHebergementEnAttente();

?><div class="container"><?php
if(!empty($_GET["success"]) && $_GET["success"] == "ref"){?>
    <div class="alert alert-success">La demande a bien été refusée !</div>
<?php }
elseif(!empty($_GET["success"]) && $_GET["success"] == "acc"){?>
    <div class="alert alert-success">La demande a bien été acceptée !</div>
<?php }
elseif(isset($_GET["error"])){?>
    <div class="alert alert-warning">Un problème est survenu lors de l'execution de la requête demandée.<br>Réessayez plus tard ou contactez un développeur si le problème persiste.</div>
<?php }
?></div>

<div class="container">
    <?php if(count($hea) > 0){ ?>
        <h1 class="mb-5">Demande d'ajout d'hébergement en attente :</h1>
        <div class="row table-responsive">
            <table class="table table-hover table-striped mt-3 align-td">
                <thead class="bg-primary text-light">
                    <tr>
                    <th scope="col">Propriétaire</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Descritption</th>
                    <th scope="col">Ville</th>
                    <th scope="col">Prix</th>
                    <th scope="col" style ="min-width: 175px !important;">Date de la demande</th>
                    <th scope="col" class="action text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($hea as $demande){
                            $_SESSION["demande".$demande["id_admin_valid_hebergement"]] = $demande;
                            ?>
                            <tr>
                                <td><?=$demande["nom"] . " " . $demande["prenom"]?></td>
                                <td><?=$demande["libelle"]?></td>
                                <td><?=$demande["description"]?></td>
                                <td><?=$demande["nomVille"]?></td>
                                <td><?=$demande["prix"]?>€</td>
                                <td><?=$demande["dateEnregistrement"]?></td>
                                <td class="btn-group d-flex" >
                                    <a href="../controleurs/demandeHebergement.php?id=<?=$demande["id_admin_valid_hebergement"]?>&command=acc" class="btn btn-success">Accepter</a>
                                    <a href="../controleurs/demandeHebergement.php?id=<?=$demande["id_admin_valid_hebergement"]?>&command=ref" class="btn btn-danger">Refuser</a>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>
    <?php }else{ ?>
        <h3 class="text-muted">Aucune demande d'hébergement est en attente</h3>
    <?php } ?>
    <a href="index.php" class="btn btn-secondary mt-5"><i class="fas fa-arrow-left"></i></a>
</div>

<?php
require_once "footerAdmin.php";