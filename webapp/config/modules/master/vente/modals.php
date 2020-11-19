

<?php include($this->rootPath("composants/assets/modals/modal-palier.php") );  ?>


<div class="modal inmodal fade" id="modal-zonedevente">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">zone de vente</h4>
			</div>
			<form method="POST" class="formShamman" classname="zonedevente">
				<div class="modal-body">
					<div class="">
						<label>Libéllé </label>
						<div class="form-group">
							<input type="text" class="form-control" name="name" required>
						</div>
					</div>
				</div><hr>
				<div class="container">
					<input type="hidden" name="id">
					<button type="button" class="btn btn-sm  btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Annuler</button>
					<button class="btn btn-sm btn-primary pull-right dim"><i class="fa fa-check"></i> enregistrer</button>
				</div>
				<br>
			</form>
		</div>
	</div>
</div>

