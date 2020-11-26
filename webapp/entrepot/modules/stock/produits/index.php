<!DOCTYPE html>
<html>

<?php include($this->rootPath("webapp/entrepot/elements/templates/head.php")); ?>


<body class="fixed-sidebar">

    <div id="wrapper">

        <?php include($this->rootPath("webapp/entrepot/elements/templates/sidebar.php")); ?>  

        <div id="page-wrapper" class="gray-bg">

          <?php include($this->rootPath("webapp/entrepot/elements/templates/header.php")); ?>  


          <div class="ibox">
            <div class="ibox-title">
                <h5 class="text-uppercase">Stock de <?= $produit->name(); ?></h5>
                <div class="ibox-tools">
                    
                </div>
            </div>
            <div class="ibox-content">
                <div class="row text-center">
                  <?php $total = 0; foreach ($emballages as $key => $emballage) {
                    $stock = $produit->enEntrepot(Home\PARAMS::DATE_DEFAULT, dateAjoute(1), $emballage->id, $entrepot->id);  ?>
                    <div class="col-sm-4 col-md-3 col-lg-2 border-left border-bottom">
                        <div class="p-xs">
                            <img style="height: 15px" src="<?= $this->stockage("images", "emballages", $emballage->image)  ?>">
                            <h5 class="m-xs gras <?= ($stock > $params->ruptureStock)?"":"clignote" ?>"><?= round($stock, 2) ?> </h5>
                            <h6 class="no-margins text-uppercase gras <?= ($stock > $params->ruptureStock)?"":"clignote" ?>"><?= $emballage->name() ?> </h6>
                        </div>
                    </div>
                <?php } ?>
            </div><br>
        </div>
    </div>



    <div class="wrapper wrapper-content">
        <div class="text-center animated fadeInRightBig">

            <div class="ibox ">
                <div class="ibox-title">
                    <h5 class="float-left text-uppercase">Historiques du <?= datecourt($date1) ?> au <?= datecourt($date2) ?></h5>
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
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th rowspan="2" class="border-none"></th>
                                <?php foreach ($emballages as $key => $emballage) {  ?>
                                    <th><small class="gras"><?= $emballage->name() ?></small></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $index = $date1;
                            while ($index <= $date2) { ?>
                                <tr>
                                    <td class="gras"><?= datecourt($index) ?></td>
                                    <?php foreach ($emballages as $key => $emballage) {
                                        $stock = $produit->enEntrepot(Home\PARAMS::DATE_DEFAULT, $index, $emballage->id, $entrepot->id);
                                        $condi = $produit->conditionnement($index, $index, $emballage->id, $entrepot->id);
                                        $sortie = $produit->totalSortieEntrepot($index, $index, $emballage->id, $entrepot->id);
                                        $conversion = $produit->transfertEntrepot($index, $index, $emballage->id, $entrepot->id);
                                        $perte = $produit->perteEntrepot($index, $index, $emballage->id, $entrepot->id)
                                        ?>
                                        <td class="cursor myPopover"
                                        data-toggle="popover"
                                        data-placement="left"
                                        title="<small><b><?= $emballage->name() ?></b> | <?= datecourt($index) ?></small>"
                                        data-trigger="hover"
                                        data-html="true"
                                        data-content="
                                        <span>Conditionnement du jour : <b><?= round($condi, 2) ?> </b></span><br>
                                        <span>Sorties du jour : <b><?= round($sortie, 2) ?> </b></span><br>
                                        <span>Conversion : <b><?= $conversion ?> </b></span><br>
                                        <span>Perte : <b><?= round($perte, 2) ?> </b></span>
                                        <hr style='margin:1.5%'>
                                        <span>En stock à ce jour : <b><?= round($stock, 2) ?> </b></span><br> <span>">
                                            <?= round($stock, 2) ?> 
                                        </td>
                                    <?php } ?>
                                </tr>
                                <?php
                                $index = dateAjoute1($index, 1);
                            }
                            ?>
                            <tr style="height: 18px;"></tr>
                        </tbody>
                    </table> 
                </div>

            </div>


        </div>
    </div>


    <?php include($this->rootPath("webapp/entrepot/elements/templates/footer.php")); ?>

</div>
</div>


<?php include($this->rootPath("webapp/entrepot/elements/templates/script.php")); ?>

<script type="text/javascript" src="<?= $this->relativePath("../approemballage/script.js") ?>"></script>




</body>

</html>
