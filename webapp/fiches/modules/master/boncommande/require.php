<?php 

namespace Home;

if ($this->id != null) {
	$datas = COMMANDE::findBy(["id ="=> $this->id, 'etat_id !='=>ETAT::ANNULEE]);
	if (count($datas) > 0) {
		$commande = $datas[0];
		$commande->actualise();

		$datas = $commande->fourni("reglementclient", ["montant = "=> $commande->avance, "DATE(created) ="=> date("Y-m-d", strtotime($commande->created))]);
		if (count($datas) > 0) {
			$reglement = $datas[0];
			$reglement->actualise();
		}

		$commande->fourni("lignecommande");

		$title = "GPV | Bon de commande ";
		
	}else{
		header("Location: ../master/clients");
	}
}else{
	header("Location: ../master/clients");
}

?>