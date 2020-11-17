<?php 
namespace Home;

$datas = BOUTIQUE::findBy(["id ="=>getSession("boutique_connecte_id")]);
if (count($datas) == 1) {
	$boutique = $datas[0];
	$boutique->actualise();
	$comptebanque = $boutique->comptebanque;

	$mouvements = $comptebanque->fourni("mouvement", ["DATE(created) >= "=> $date1, "DATE(created) <= "=> $date2]);

	$transferts = TRANSFERTFOND::findBy(["comptebanque_id_source="=>$comptebanque->id, "etat_id ="=>ETAT::VALIDEE, "DATE(created) >= "=> $date1, "DATE(created) <= "=> $date2]);


	$temp1 = TRANSFERTFOND::findBy(["comptebanque_id_source="=>$comptebanque->id, "etat_id ="=>ETAT::ENCOURS]);
	$temp2 = TRANSFERTFOND::findBy(["comptebanque_id_destination="=>$comptebanque->id, "etat_id ="=>ETAT::ENCOURS]);
	$transfertsattentes = array_merge($temp1, $temp2);


	$entrees = $depenses = [];
	foreach ($mouvements as $key => $value) {
		$value->actualise();
		if ($value->typemouvement_id == TYPEMOUVEMENT::DEPOT) {
			$entrees[] = $value;
		}else{
			$depenses[] = $value;
		}
	}


	$stats = $comptebanque->stats($date1, $date2);

	$title = "BRIXS | Compte de caisse";
}else{
	header("Location: ../master/dashboard");
}




?>