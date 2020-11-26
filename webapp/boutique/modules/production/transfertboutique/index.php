<!DOCTYPE html>
<html>

<?php include($this->rootPath("webapp/boutique/elements/templates/head.php")); ?>


<body class="fixed-sidebar">

    <div id="wrapper">

        <?php include($this->rootPath("webapp/boutique/elements/templates/sidebar.php")); ?>  

        <div id="page-wrapper" class="gray-bg">

          <?php include($this->rootPath("webapp/boutique/elements/templates/header.php")); ?>  

          <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-sm-7">
                <h2 class="text-uppercase text-green gras">Transfert en boutique</h2>
            </div>
            <div class="col-sm-5">
                <button style="margin-top: 5%;" type="button" data-toggle=modal data-target='#modal-transfertboutique-demande' class="btn btn-primary btn-sm dim float-right"><i class="fa fa-plus"></i> Faire une demande </button>

                <button style="margin-top: 5%;" type="button" data-toggle=modal data-target='#modal-transfertboutique' class="btn btn-info btn-sm dim float-right"><i class="fa fa-plus"></i> Nouveau transfert </button>
            </div>
        </div>

        <div class="wrapper wrapper-content">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Toutes les transferts en boutique de la production</h5>
                    <div class="ibox-tools">
                       <form id="formFiltrer" method="POST">
                        <div class="row" style="margin-top: -1%">
                            <div class="col-5">
                                <input type="date" value="<?= $date1 ?>" class="form-control input-sm" name="date1">
                            </div>
                            <div class="col-5">
                                <input type="date" value="<?= $date2 ?>" class="form-control input-sm" name="date2">
                            </div>
                            <div class="col-2">
                                <button type="button" onclick="filtrer()" class="btn btn-sm btn-white"><i class="fa fa-search"></i> Filtrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="ibox-content">
                <?php if (count($datas + $encours) > 0) { ?>
                    <table class="footable table table-stripped toggle-arrow-tiny">
                        <thead>
                            <tr>

                                <th data-toggle="true">Status</th>
                                <th>Reference</th>
                                <th>Boutique qui livre</th>
                                <th></th>
                                <th>Boutique de destination</th>
                                <th data-hide="all">Produits</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php foreach ($encours as $key => $transfert) {
                            $transfert->actualise(); 
                            $lots = $transfert->fourni("lignetransfertboutique");
                            ?>
                            <tr style="border-bottom: 2px solid black">
                                <td class="project-status">
                                    <span class="label label-<?= $transfert->etat->class ?>"><?= $transfert->etat->name ?></span>
                                </td>
                                <td>
                                    <span class="text-uppercase gras">Transfert en boutique</span><br>
                                    <span><?= $transfert->reference ?></span>
                                </td>
                                <td>
                                    <h6 class="text-uppercase text-muted gras" style="margin: 0"><?= $transfert->boutique->name() ?></h6>
                                    <small>Emise <?= depuis($transfert->created) ?></small>
                                </td>
                                <td><i class="fa fa-long-arrow-right fa-2x"></i></td>
                                <td>
                                    <h6 class="text-uppercase text-muted gras" style="margin: 0"><?= $transfert->boutique_destination->name() ?></h6>
                                    <small>Emise <?= depuis($transfert->created) ?></small>
                                </td>
                                <td class="border-right">
                                 <table class="table table-bordered">
                                    <thead>
                                        <tr class="no">
                                            <th></th>
                                            <?php foreach ($transfert->lignetransfertboutiques as $key => $ligne) {
                                                $ligne->actualise(); ?>
                                                <th class="text-center" style="padding: 2px"><span class="small"><?= $ligne->produit->typeproduit_parfum->name() ?><br><?= $ligne->produit->quantite->name() ?></span></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><h4 class="mp0">Démandé : </h4></td>
                                            <?php foreach ($transfert->lignetransfertboutiques as $key => $ligne) { ?>
                                                <td class="text-center"><?= start0($ligne->quantite_demande) ?><br><small><?= $ligne->emballage->name()  ?></small></td>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <td><h4 class="mp0">sorti : </h4></td>
                                            <?php foreach ($transfert->lignetransfertboutiques as $key => $ligne) { ?>
                                                <td class="text-center"><?= start0($ligne->quantite_depart) ?><br><small><?= $ligne->emballage->name()  ?></small></td>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <td><h4 class="mp0">Livré : </h4></td>
                                            <?php foreach ($transfert->lignetransfertboutiques as $key => $ligne) { ?>
                                                <td class="text-center"><?= start0($ligne->quantite) ?><br><small><?= $ligne->emballage->name()  ?></small></td>
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                 <?php if ($transfert->etat_id == Home\ETAT::PARTIEL && $transfert->boutique_id_destination != $boutique->id) { ?>
                                        <button onclick="accepter(<?= $transfert->id ?>)" class="btn btn-white btn-sm text-green"><i class="fa fa-check"></i> Accepter de livrer</button>
                                    <?php } ?>

                                <?php if ($transfert->etat_id == Home\ETAT::ENCOURS && $transfert->boutique_id_destination == $boutique->id) { ?>
                                    <button onclick="terminer(<?= $transfert->id ?>)" class="btn btn-white btn-sm text-green"><i class="fa fa-check"></i> Valider</button>
                                <?php } ?>

                                <a href="<?= $this->url("fiches", "master", "bontransfertboutique", $transfert->id) ?>" target="_blank" class="btn btn-white btn-sm"><i class="fa fa-file-text text-blue"></i></a>
                                <?php if ($employe->isAutoriser("modifier-supprimer") && $transfert->etat_id != Home\ETAT::ANNULEE) { ?>
                                    <button onclick="annuler('transfertboutique', <?= $transfert->id ?>)" class="btn btn-white btn-sm"><i class="fa fa-trash text-red"></i></button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php  } ?>
                    <tr />
                    <?php foreach ($datas as $key => $transfert) {
                        $transfert->actualise(); 
                        $lots = $transfert->fourni("lignetransfertboutique");
                        ?>
                        <tr style="border-bottom: 2px solid black">
                            <td class="project-status">
                                <span class="label label-<?= $transfert->etat->class ?>"><?= $transfert->etat->name ?></span>
                            </td>
                            <td>
                                <span class="text-uppercase gras">Transfert en boutique</span><br>
                                <span><?= $transfert->reference ?></span>
                            </td>
                            <td>
                                <h6 class="text-uppercase text-muted gras" style="margin: 0"><?= $transfert->boutique->name() ?></h6>
                                <small>Emise <?= depuis($transfert->created) ?></small>
                            </td>
                            <td><i class="fa fa-long-arrow-right fa-2x"></i></td>
                            <td>
                                <h6 class="text-uppercase text-muted gras" style="margin: 0"><?= $transfert->boutique_destination->name() ?></h6>
                                <small>Emise <?= depuis($transfert->created) ?></small>
                            </td>
                            <td class="border-right">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="no">
                                            <th></th>
                                            <?php foreach ($transfert->lignetransfertboutiques as $key => $ligne) {
                                                $ligne->actualise(); ?>
                                                <th class="text-center" style="padding: 2px"><span class="small"><?= $ligne->produit->typeproduit_parfum->name() ?><br><?= $ligne->produit->quantite->name() ?></span></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><h4 class="mp0">Démandé : </h4></td>
                                            <?php foreach ($transfert->lignetransfertboutiques as $key => $ligne) { ?>
                                                <td class="text-center"><?= start0($ligne->quantite_demande) ?><br><small><?= $ligne->emballage->name()  ?></small></td>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <td><h4 class="mp0">sorti : </h4></td>
                                            <?php foreach ($transfert->lignetransfertboutiques as $key => $ligne) { ?>
                                                <td class="text-center"><?= start0($ligne->quantite_depart) ?><br><small><?= $ligne->emballage->name()  ?></small></td>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <td><h4 class="mp0">Livré : </h4></td>
                                            <?php foreach ($transfert->lignetransfertboutiques as $key => $ligne) { ?>
                                                <td class="text-center"><?= start0($ligne->quantite) ?><br><small><?= $ligne->emballage->name()  ?></small></td>
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <a href="<?= $this->url("fiches", "master", "bontransfertboutique", $transfert->id) ?>" target="_blank" class="btn btn-white btn-sm"><i class="fa fa-file-text text-blue"></i></a>

                                <?php if ($employe->isAutoriser("modifier-supprimer") && $transfert->etat_id != Home\ETAT::ANNULEE) { ?>
                                    <button onclick="annuler('transfertboutique', <?= $transfert->id ?>)" class="btn btn-white btn-sm"><i class="fa fa-trash text-red"></i></button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php  } ?>

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">
                            <ul class="pagination float-right"></ul>
                        </td>
                    </tr>
                </tfoot>
            </table>

        <?php }else{ ?>
            <h1 style="margin: 6% auto;" class="text-center text-muted"><i class="fa fa-folder-open-o fa-3x"></i> <br> Aucun transfert en boutique pour le moment</h1>
        <?php } ?>

    </div>
</div>
</div>


<?php include($this->rootPath("webapp/boutique/elements/templates/footer.php")); ?> 
<?php include($this->rootPath("composants/assets/modals/modal-transfertboutique-demande.php")); ?>
<?php include($this->rootPath("composants/assets/modals/modal-transfertboutique.php")); ?>


<?php 
foreach ($encours as $key => $transfert) {
    if ($transfert->etat_id == Home\ETAT::ENCOURS) { 
        include($this->rootPath("composants/assets/modals/modal-transfertboutique1.php"));
    } 

    if ($transfert->etat_id == Home\ETAT::PARTIEL) { 
        include($this->rootPath("composants/assets/modals/modal-acceptertransfertboutique.php"));
    } 
} 
?>

</div>
</div>


<?php include($this->rootPath("webapp/boutique/elements/templates/script.php")); ?>
<script type="text/javascript" src="<?= $this->relativePath("../../master/client/script.js") ?>"></script>


</body>

</html>
