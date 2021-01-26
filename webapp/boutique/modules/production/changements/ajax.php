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



if ($action == "annulerTransfert") {
	$datas = EMPLOYE::findBy(["id = "=>getSession("employe_connecte_id")]);
	if (count($datas) > 0) {
		$employe = $datas[0];
		$employe->actualise();
		if ($employe->checkPassword($password)) {
			$datas = TRANSFERTSTOCKENTREPOT::findBy(["id ="=>$id]);
			if (count($datas) == 1) {
				$transfertstockentrepot = $datas[0];
				$data = $transfertstockentrepot->annuler();
			}else{
				$data->status = false;
				$data->message = "Une erreur s'est produite lors de l'opération! Veuillez recommencer";
			}
		}else{
			$data->status = false;
			$data->message = "Votre mot de passe ne correspond pas !";
		}
	}else{
		$data->status = false;
		$data->message = "Vous ne pouvez pas effectué cette opération !";
	}
	echo json_encode($data);
}



if ($action == "getEmballageDestination") {
	$datas = PRODUIT::findBy(["id = "=>$id]);
	if (count($datas) > 0) {
		$produit = $datas[0];
		foreach ($produit->getListeEmballageProduit() as $key => $emb) {
			?>
			<div class="col-4 text-center mb-2 cursor emballage-destination" id="<?= $emb->id ?>">
				<img class="border" style="height: 50px" src="<?= $rooter->stockage("images", "emballages", $emb->image)  ?>">
				<h5><?= $emb->name(); ?></h5>
			</div>
			<?php
		}
	}
}




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
