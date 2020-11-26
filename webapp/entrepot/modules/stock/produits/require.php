<?php 
namespace Home;
unset_session("emballages");
unset_session("ressources");

if ($this->id != null) {
	$datas = PRODUIT::findBy(["id ="=> $this->id]);
	if (count($datas) > 0) {
		$produit = $datas[0];
		$produit->actualise();

		$emballages = $produit->getListeEmballageProduit();

		$title = "GPV | Stock des ".$produit->name();;
		
	}else{
		header("Location: ../stock/stock");
	}
}else{
	header("Location: ../stock/stock");
}


?>