<?php 
namespace Home;
use Faker\Factory;
$faker = Factory::create();

if ($this->id != null) {
	$datas = BOUTIQUE::findBy(["id ="=> $this->id]);
	if (count($datas) > 0) {
		$boutique = $datas[0];
		$boutique->actualise();

		$parfums = $typeproduits = $quantites = [];

		foreach (PARFUM::findBy(["isActive ="=>TABLE::OUI]) as $key => $item) {
			$item->vendu = PRODUIT::totalVendu($date1, $date2, $boutique->id, null, $item->id);
			$parfums[] = $item;
		}

		foreach (TYPEPRODUIT::findBy(["isActive ="=>TABLE::OUI]) as $key => $item) {
			$item->vendu = PRODUIT::totalVendu($date1, $date2, $boutique->id, $item->id);
			$typeproduits[] = $item;
		}

		foreach (QUANTITE::findBy(["isActive ="=>TABLE::OUI]) as $key => $item) {
			$item->vendu = PRODUIT::totalVendu($date1, $date2, $boutique->id, null, null, $item->id);
			$quantites[] = $item;
		}


		$stats = VENTE::stats($date1, $date2, $boutique->id);
		$title = "GPV | Rapport de vente par boutique ";
		$lots = [];

		$clients = $boutique->fourni("client");
		foreach ($clients as $key => $client) {
			$client->actualise();
			$client->commandes = $client->getCommandes($date1, $date2);
			$client->livraisons = $client->getLivraisons($date1, $date2);
		}


	}else{
		header("Location: ../master/dashboard");
	}
}else{
	header("Location: ../master/dashboard");
}

?>