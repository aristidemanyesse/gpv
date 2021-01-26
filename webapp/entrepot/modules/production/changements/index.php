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
                    <h2 class="text-uppercase text-blue gras">Les changements et retours de stock en entrepot</h2>
                    <div class="container">
                    </div>
                </div>
                <div class="col-sm-3 text-right">
                    <button style="margin-top: 5%;" type="button" data-toggle=modal data-target='#modal-retourstockentrepot' class="btn btn-primary btn-sm dim float-right"><i class="fa fa-plus"></i> Nouveau changement/retour </button>
                </div>
            </div>

            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Tous les retours de stock survenues dans cet entrepot</h5>
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
                                <th></th>
                                <th>Boutique</th>
                                <th>Produit renvoyé</th>
                                <th></th>
                                <th>Produit échangé</th>
                                <th>Enregistré par</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datas as $key => $retour) {
                                $retour->actualise(); 
                                ?>
                                <tr style="border-bottom: 2px solid black">
                                    <td class="project-status">
                                        <span class="label label-<?= $retour->etat->class ?>"><?= $retour->etat->name ?></span>
                                    </td>
                                    <td>
                                        <span class="text-uppercase gras">Retour de stock</span><br>
                                        <small>Enregistré <?= depuis($retour->created)  ?></small>
                                    </td>
                                    <td><?= $retour->boutique->name();  ?></td>
                                    <td>
                                        <span><?= start0($retour->quantite)  ?></span><br>
                                        <small class="text-uppercase gras"><img style="width: 20px;" src="<?= $this->stockage("images", "emballages", $retour->emballage_source->image) ?>"> <?= $retour->emballage_source->name() ?></small><br>
                                        <b><?= $retour->produit_source->name() ?></b>
                                    </td>
                                    <td><i class="fa fa-long-arrow-right fa-2x"></i></td>
                                    <td class="">
                                        <span><?= start0($retour->quantite1)  ?></span><br>
                                        <small class="text-uppercase gras"><img style="width: 20px;" src="<?= $this->stockage("images", "emballages", $retour->emballage_destination->image) ?>"> <?= $retour->emballage_destination->name() ?></small><br>
                                        <b><?= $retour->produit_source->name() ?></b>
                                    </td>
                                    <td><i class="fa fa-user"></i> <?= $retour->employe->name() ?></td>
                                    <td>
                                        <?php if ($employe->isAutoriser("modifier-supprimer") && $retour->etat_id != Home\ETAT::ANNULEE) { ?>
                                            <button onclick="annuler('retourstockboutique', <?= $retour->id ?>)" class="btn btn-white btn-sm"><i class="fa fa-trash text-red"></i></button>
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




<div class="modal inmodal fade" id="modal-retourstockentrepot">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Nouveau changement ou retour de stock</h4>
                <small>Veuillez selectionner le type de produit à convertir</small>
            </div>
            <form method="POST" class="formShamman" classname="retourstockentrepot">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-4">
                            <label>La boutique concernée<span1>*</span1></label>
                            <div class="form-group">
                                <?php Native\BINDING::html("select","boutique"); ?>                                    
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label>Le motif du retour/changement<span1>*</span1></label>
                            <div class="form-group">
                                <textarea class="form-control" rows="4" name="comment"></textarea>
                            </div>
                        </div>
                    </div><br>

                    <div class="row">
                        <div class="col-sm-5 border-right">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Quantité<span1>*</span1></label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="quantite" >
                                    </div>
                                </div> 
                                <div class="col-sm-9">
                                    <label>Produit renvoyé <span1>*</span1></label>
                                    <div class="form-group">
                                        <?php Native\BINDING::html("select-tableau-startnull", Home\PRODUIT::Actives(), null, "produit_id_source"); ?>                                    
                                    </div>
                                </div> 
                            </div>
                            <div class="row div-source text-center">
                                <!-- rempli en ajax -->
                            </div>
                        </div>
                        <div class="col-sm-2 text-center">
                            <br><br><br>
                            <label>Changé en </label><br>
                            <i class="fa fa-long-arrow-right fa-3x"></i>
                        </div>
                        <div class="col-sm-5 border-left">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Quantité<span1>*</span1></label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="quantite1" >
                                    </div>
                                </div> 
                                <div class="col-sm-9">
                                    <label>Nouveau produit <span1>*</span1></label>
                                    <div class="form-group">
                                        <?php Native\BINDING::html("select-tableau-startnull", Home\PRODUIT::Actives(), null, "produit_id_destination"); ?>                                    
                                    </div>
                                </div>
                            </div>
                            <div class="row div-destination text-center">
                                <!-- rempli en ajax -->
                            </div>
                        </div>                      
                    </div><br>
                </div><hr>
                <div class="container">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-sm  btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Annuler</button>
                    <button class="btn dim btn-primary pull-right"><i class="fa fa-refresh"></i> Valider l'opération</button>
                </div>
                <br>
            </form>
        </div>
    </div>
</div>


<?php foreach ($produits as $key => $produit) {
    include($this->rootPath("composants/assets/modals/modal-transfertstockentrepot.php")); 
}  ?>


<?php include($this->rootPath("webapp/entrepot/elements/templates/script.php")); ?>
<script type="text/javascript" src="<?= $this->rootPath("webapp/entrepot/modules/master/client/script.js") ?>"></script>


</body>

</html>
