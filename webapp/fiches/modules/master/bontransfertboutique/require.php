<?php 

namespace Home;

if ($this->id != null) {
	$datas = TRANSFERTBOUTIQUE::findBy(["id ="=> $this->id, 'etat_id !='=>ETAT::ANNULEE]);
	if (count($datas) > 0) {
		$transfert = $datas[0];
		$transfert->actualise();

		$transfert->fourni("lignetransfertboutique");

		$title = "GPV | Bon de transfert de boutique ";
		
	}else{
		header("Location: ../production/transfertboutique");
	}
}else{
	header("Location: ../production/transfertboutique");
}

?>