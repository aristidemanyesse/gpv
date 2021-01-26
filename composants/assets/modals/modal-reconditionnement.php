<div class="modal inmodal fade" id="modal-reconditionnement">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Reconditionnement de stock</h4>
                <small>Veuillez selectionner le type de produit à reconditionner</small>
            </div>
            <form method="POST" class="formShamman" classname="reconditionnement">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Quantité <span1>*</span1></label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="quantite" >
                            </div>
                        </div> 
                        <div class="col-sm-8">
                            <label>Produit à reconditionner <span1>*</span1></label>
                            <div class="form-group">
                                <?php Native\BINDING::html("select-tableau-startnull", Home\PRODUIT::Actives(), null, "produit_id"); ?>                                    
                            </div>
                        </div> 
                    </div>
                    <div class="row div-source text-center">
                        <!-- rempli en ajax -->
                    </div><br>
                </div><hr>
                <div class="container">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-sm  btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Annuler</button>
                    <button class="btn dim btn-primary pull-right"><i class="fa fa-refresh"></i> Reconditionner</button>
                </div>
                <br>
            </form>
        </div>
    </div>
</div>
