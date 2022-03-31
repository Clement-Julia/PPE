<?php
require_once "headerAdmin.php";
$admin = new Admin();
$logs = $admin->getAllLogs();

$date = new DateTime();
?>

<?php
    if(!empty($_GET["error"]) && $_GET["error"] == "crash"){
        ?>
        <div class="container alert alert-danger">
            <p>
                La fonctionnalité est actuellement indisponible <br>
                Pour plus d'information contacter le développeur
            </p>
        </div>
        <?php
    }
    if(isset($_GET["success"])){
        ?>
        <div class="container alert alert-success">
            <p>
                L'ip a bien été banni !
            </p>
        </div>
        <?php
    }
?>

<div class="container mb-4">
    <?php if(!empty($logs)){ ?>
        <h1 class="mb-4">Journal des utilisateurs :</h1>
        <table id="Datatable-user" class="table table-hover align-td">
            <thead class="bg-primary text-light">
                <tr>
                <th scope="col">Nom</th>
                <th scope="col">Prénom</th>
                <th scope="col">Ip</th>
                <th scope="col">Heure</th>
                <th scope="col">Date</th>
                <th scope="col" class="action text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($logs as $log){
                        ?>
                        <tr>
                            <td><?=htmlspecialchars($log["nom"], ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($log["prenom"], ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($log["ip"], ENT_QUOTES)?></td>
                            <td><?=$log["date"]?></td>
                            <td><?=$log["date"]?></td>
                            <td>
                                <a href="banIpUser.php?id=<?=$log["ip"]?>" class="btn btn-danger" title="Bannir l'ip">
                                    <i class="fas fa-gavel"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </tbody>
        </table>
    <?php }else{ ?>
        <h3 class="text-muted">Aucun utilisateur ne s'est encore connecté</h3>
    <?php } ?> 
</div>
    
<script src="vendor/jquery/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#Datatable-user').DataTable({
            language: {
                url: 'vendor/datatables/FR.json'
            }
        });
    } );
</script>

<?php
require_once "footerAdmin.php";
?>