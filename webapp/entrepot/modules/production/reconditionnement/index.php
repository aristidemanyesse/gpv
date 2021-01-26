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
                    <h2 class="text-uppercase text-blue gras">Les reconditionnements de stock en entrepot</h2>
                    <div class="container">
                    </div>
                </div>
                <div class="col-sm-3 text-right">
                    <button style="margin-top: 5%;" type="button" data-toggle=modal data-target='#modal-reconditionnement' class="btn btn-primary btn-sm dim float-right"><i class="fa fa-plus"></i> Nouveau reconditionnement </button>
                </div>
            </div>

            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Tous les reconditionnements survenues dans cet entrepot</h5>
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
                                <th>Produit renvoyé</th>
                                <th>Quantité</th>
                                <th>Enregistré par</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datas as $key => $reconditionnement) {
                                $reconditionnement->actualise(); 
                                ?>
                                <tr style="border-bottom: 2px solid black">
                                    <td class="project-status">
                                        <span class="label label-<?= $reconditionnement->etat->class ?>"><?= $reconditionnement->etat->name ?></span>
                                    </td>
                                    <td>
                                        <span class="text-uppercase gras">Reconditionnement de stock</span><br>
                                        <small>Enregistré <?= depuis($reconditionnement->created)  ?></small>
                                    </td>
                                    <td>
                                        <b><?= $reconditionnement->produit->name() ?></b>
                                    </td>
                                    <td>
                                        <span><?= start0($reconditionnement->quantite)  ?></span><br>
                                        <small class="text-uppercase gras"><img style="width: 20px;" src="<?= $this->stockage("images", "emballages", $reconditionnement->emballage->image) ?>"> <?= $reconditionnement->emballage->name() ?></small>
                                    </td>
                                    <td><i class="fa fa-user"></i> <?= $reconditionnement->employe->name() ?></td>
                                    <td>
                                        <?php if ($employe->isAutoriser("modifier-supprimer") && $reconditionnement->etat_id != Home\ETAT::ANNULEE) { ?>
                                            <button onclick="annuler('reconditionnement', <?= $reconditionnement->id ?>)" class="btn btn-white btn-sm"><i class="fa fa-trash text-red"></i></button>
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

<?php include($this->rootPath("webapp/entrepot/elements/templates/script.php")); ?>



<?php include($this->rootPath("composants/assets/modals/modal-reconditionnement.php")); ?>  



</body>

</html>
