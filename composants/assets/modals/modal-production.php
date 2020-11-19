
<div class="modal inmodal fade" id="modal-production<?= $pro->id  ?>" style="z-index: 9999999999">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Nouvelle production</h4>
                <small class="font-bold">Nouvelle production de <?= $pro->name() ?></small>
            </div>
            
            <?php if ($pro->ressource_id != null) { ?>
                <div class="row">
                    <div class="col-md-8">
                        <br><br>
                        <div class="row text-center">
                            <div class="offset-sm-3 col-sm-6">
                                <?php if ($params->productionAuto == Home\TABLE::OUI) { ?>
                                    <label>Quantité de <b><?= $pro->ressource->name() ?></b> utilisé (<?= $pro->ressource->unite ?>)</label>
                                <?php }else{ ?>
                                    <label>Quantité de <b><?= $pro->name() ?></b> finalement produite</label>
                                <?php } ?>
                                <input type="text" step="0.01" number id="<?= $pro->id ?>" name="quantite" class="form-control text-center">
                            </div>
                        </div><hr>

                        <?php if ($params->productionAuto == Home\TABLE::OUI) { ?>
                            <div class="ajax">

                            </div>
                        <?php }else{ ?>
                            <h3 class="text-uppercase text-center"><u>Consommation des matières premières pour toute la productionr</u></h3><br>
                            <div class="row container-fluid conso">
                                <?php foreach (Home\RESSOURCE::getAll() as $key => $ressource) { ?>
                                    <div class="col-md-3 text-center">
                                        <label class=""><?= $ressource->name() ?> (<?= $ressource->abbr ?>)</label>
                                        <input step="0.01" type="number" value="0" min=0 number class="gras form-control text-center" name="conso-<?= $ressource->getId() ?>">
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>

                    </div>
                    <div class="col-md-4 ">
                        <div class="ibox"  style="background-color: #eee">
                            <div class="ibox-title" style="padding-right: 2%; padding-left: 3%; ">
                                <h5 class="text-uppercase">Finaliser la production</h5>
                            </div>
                            <div class="ibox-content"  style="background-color: #fafafa">
                                <form id="formProduction">
                                    <div class="">
                                        <label>Coût de la main d'oeuvre </label>
                                        <div class="form-group">
                                            <input type="text" class="form-control" value="0" number name="maindoeuvre">
                                        </div>
                                    </div>

                                    <div>
                                        <label>Ajouter une note</label>
                                        <textarea class="form-control" name="comment" rows="4"></textarea>
                                    </div>

                                    <input type="hidden" name="entrepot_id" value="<?= $entrepot->id ?>">

                                </form>
                                <hr/>
                                <button onclick="nouvelleProduction(<?= $pro->id  ?>)" class="btn btn-primary btn-block dim"><i class="fa fa-check"></i> valider la production</button>
                            </div>
                        </div>

                    </div>
                </div>

            <?php }else{ ?>
                <h2 class="text-center">Veuillez definir la ressource de base de ce produit dans les paramètres avant de pouvoir enregistrer une production !</h2>
            <?php } ?>

        </div>
    </div>
</div>

