<!DOCTYPE html>
<html>

<?php include($this->rootPath("webapp/entrepot/elements/templates/head.php")); ?>


<body class="fixed-sidebar">

    <div id="wrapper">

        <?php include($this->rootPath("webapp/entrepot/elements/templates/sidebar.php")); ?>  

        <div id="page-wrapper" class="gray-bg">

            <?php include($this->rootPath("webapp/entrepot/elements/templates/header.php")); ?>  

            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-9">
                    <h2 class="text-uppercase text-blue gras">Les conversion de stock en entrepot</h2>
                    <div class="container">
                    </div>
                </div>
                <div class="col-sm-3 text-right">
                    <button style="margin-top: 5%;" type="button" data-toggle=modal data-target='#modal-listeproduits' class="btn btn-primary btn-sm dim float-right"><i class="fa fa-plus"></i> Nouvelle conversion </button>
                </div>
            </div>

            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Toutes les conversion de stock survenues dans cet entrepot</h5>
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
                    <?php if (count($datas) > 0) { ?>
                        <table class="footable table table-stripped toggle-arrow-tiny">
                            <thead>
                                <tr>

                                    <th data-toggle="true">Status</th>
                                    <th>Produit</th>
                                    <th>Source</th>
                                    <th></th>
                                    <th>Final</th>
                                    <th>Entrepôt</th>
                                    <th>Enregistré par</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datas as $key => $transfert) {
                                    $transfert->actualise(); 
                                    ?>
                                    <tr style="border-bottom: 2px solid black">
                                        <td>
                                            <span class="text-uppercase gras">Transfert de stock</span><br>
                                            <small>Enregistré <?= depuis($transfert->created)  ?></small>
                                        </td>
                                        <td><b><?= $transfert->produit->name() ?></b></td>
                                        <td class="">
                                            <span><?= start0($transfert->quantite)  ?></span><br>
                                            <small class="text-uppercase gras"><img style="width: 20px;" src="<?= $this->stockage("images", "emballages", $transfert->emballage_source->image) ?>"> <?= $transfert->emballage_source->name() ?></small>
                                        </td>
                                        <td><i class="fa fa-long-arrow-right fa-2x"></i></td>
                                        <td class="">
                                            <span class=""><?= start0($transfert->quantite1)  ?></span><br>
                                            <small class="text-uppercase gras"><img style="width: 20px;" src="<?= $this->stockage("images", "emballages", $transfert->emballage_destination->image) ?>"> <?= $transfert->emballage_destination->name() ?></small>
                                        </td>
                                        <td>
                                            <h6 class="text-uppercase text-muted gras" style="margin: 0"><?= $transfert->entrepot->name() ?></h6>
                                        </td>
                                        <td><i class="fa fa-user"></i> <?= $transfert->employe->name() ?></td>
                                        <td>
                                            <?php if ($employe->isAutoriser("modifier-supprimer") && $transfert->etat_id != Home\ETAT::ANNULEE) { ?>
                                                <button onclick="annuler('transfertstockentrepot', <?= $transfert->id ?>)" class="btn btn-white btn-sm"><i class="fa fa-trash text-red"></i></button>
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
                        <h1 style="margin: 6% auto;" class="text-center text-muted"><i class="fa fa-folder-open-o fa-3x"></i> <br> Aucune conversion pour le moment</h1>
                    <?php } ?>

                </div>
            </div>
        </div>


        <?php include($this->rootPath("webapp/entrepot/elements/templates/footer.php")); ?> 

    </div>
</div>



<div class="modal inmodal fade" id="modal-listeproduits">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Nouvelle conversion de stock</h4>
                <small>Veuillez selectionner le type de produit à convertir</small>
            </div>
            <div class="modal-body"><br>
                <div class="row justify-content-center">
                    <?php foreach ($produits as $key => $produit) {
                        $produit->actualise(); ?>
                        <div class="col-sm-4 cursor" onclick="session('produit_id', <?= $produit->id ?>)" data-toggle="modal" data-target="#modal-transfertstockentrepot<?= $produit->id ?>">
                            <div class="card text-center p-2">
                                <i class="fa fa-cubes fa-2x"></i>
                                <h5><?= $produit->name();  ?></h5>
                            </div>
                        </div>
                    <?php }  ?>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>


<?php foreach ($produits as $key => $produit) {
    include($this->rootPath("composants/assets/modals/modal-transfertstockentrepot.php")); 
}  ?>


<?php include($this->rootPath("webapp/entrepot/elements/templates/script.php")); ?>
<script type="text/javascript" src="<?= $this->rootPath("webapp/boutique/modules/master/client/script.js") ?>"></script>


</body>

</html>
