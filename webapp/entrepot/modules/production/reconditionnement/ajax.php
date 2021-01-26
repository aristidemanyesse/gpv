<?php 
namespace Home;
require '../../../../../core/root/includes.php';

use Native\RESPONSE;
use Native\ROOTER;

$rooter = new ROOTER;
$data = new RESPONSE;
extract($_POST);

unset_session("emballages-disponibles");
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($action == "getEmballageSource") {
	$datas = PRODUIT::findBy(["id = "=>$id]);
	if (count($datas) > 0) {
		$produit = $datas[0];
		foreach ($produit->getListeEmballageProduit() as $key => $emb) {
			?>
			<div class="col-4 text-center mb-2 cursor emballage-source" id="<?= $emb->id ?>">
				<img class="border" style="height: 50px" src="<?= $rooter->stockage("images", "emballages", $emb->image)  ?>">
				<h5><?= $emb->name(); ?></h5>
			</div>
			<?php
		}
	}
}