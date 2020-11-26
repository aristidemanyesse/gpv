<?php 
namespace Home;
require '../../../../../core/root/includes.php';

use Native\RESPONSE;

$data = new RESPONSE;
extract($_POST);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



if ($action == "demandetransfertboutique") {
	$meb = new TRANSFERTBOUTIQUE();
	$meb->hydrater($_POST);
	$meb->etat_id = ETAT::PARTIEL;
	$data = $meb->enregistre();
	if ($data->status) {
		$listeproduits = explode(",", $listeproduits);
		foreach ($listeproduits as $key => $value) {
			$lot = explode("-", $value);
			$id = $lot[0];
			$format_id = $lot[1];
			$qte = end($lot);
			$datas = PRODUIT::findBy(["id ="=> $id]);
			if (count($datas) == 1) {
				$produit = $datas[0];

				$ligne = new LIGNETRANSFERTBOUTIQUE();
				$ligne->transfertboutique_id = $meb->id;
				$ligne->emballage_id = $format_id;
				$ligne->produit_id = $produit->id;
				$ligne->quantite_demande = $ligne->quantite = intval($qte);
				$data = $ligne->enregistre();	
			}
		}
	}
	echo json_encode($data);
}




if ($action == "accepterTransfertboutique") {
	$id = getSession("transfertboutique_id");
	$datas = TRANSFERTBOUTIQUE::findBy(["id ="=>$id, "etat_id = "=>ETAT::PARTIEL]);
	if (count($datas) > 0) {
		$transfert = $datas[0];
		$transfert->actualise();
		$transfert->fourni("lignetransfertboutique");

		$produits = explode(",", $tableau);
		foreach ($produits as $key => $value) {
			$lot = explode("-", $value);
			$array[$lot[0]] = end($lot);
		}

		if (count($produits) > 0) {
			$tests = $array;
			foreach ($tests as $key => $value) {
				foreach ($transfert->lignetransfertboutiques as $cle => $lgn) {
					$lgn->actualise();
					if (($lgn->id == $key) && ($lgn->quantite_demande >= $value) && ($lgn->produit->enBoutique(PARAMS::DATE_DEFAULT, dateAjoute(1), $lgn->emballage_id, getSession("boutique_connecte_id")) >= $value)) {
						unset($tests[$key]);
					}
				}
			}
			if (count($tests) == 0) {
				foreach ($array as $key => $value) {
					foreach ($transfert->lignetransfertboutiques as $cle => $lgn) {
						if ($lgn->id == $key) {
							$lgn->quantite_depart = $value;
							$lgn->save();
							break;
						}
					}					
				}
				$transfert->hydrater($_POST);
				$data = $transfert->accepter();
			}else{
				$data->status = false;
				$data->message = "Veuillez à bien vérifier les quantités des différents produits, certaines sont incorrectes (<b>".$lgn->produit->name()."</b>) !";
			}			
		}else{
			$data->status = false;
			$data->message = "Une erreur s'est produite lors de l'opération! Veuillez recommencer 0";
		}
	}else{
		$data->status = false;
		$data->message = "Une erreur s'est produite lors de l'opération! Veuillez recommencer 1";
	}
	echo json_encode($data);
}



if ($action == "transfertboutique") {
	$tests = $listeproduits = explode(",", $listeproduits);
	foreach ($tests as $key => $value) {
		$lot = explode("-", $value);
		$format_id = $lot[1];
		$id = $lot[0];
		$qte = end($lot);
		$datas = PRODUIT::findBy(["id ="=> $id]);
		if (count($datas) == 1) {
			$produit = $datas[0];
			if ($produit->enBoutique(PARAMS::DATE_DEFAULT, dateAjoute(1), $format_id, getSession("boutique_connecte_id")) >= $qte) {
				unset($tests[$key]);
			}	
		}
	}
	if (count($tests) == 0) {
		$meb = new TRANSFERTBOUTIQUE();
		$meb->hydrater($_POST);
		$meb->etat_id = ETAT::ENCOURS;
		$data = $meb->enregistre();
		if ($data->status) {
			foreach ($listeproduits as $key => $value) {
				$lot = explode("-", $value);
				$format_id = $lot[1];
				$id = $lot[0];
				$qte = end($lot);
				$datas = PRODUIT::findBy(["id ="=> $id]);
				if (count($datas) == 1) {
					$produit = $datas[0];
					if ($qte > 0) {
						$ligne = new LIGNETRANSFERTBOUTIQUE();
						$ligne->transfertboutique_id = $meb->id;
						$ligne->emballage_id = $format_id;
						$ligne->produit_id = $produit->id;
						$ligne->quantite_depart = intval($qte);
						$data = $ligne->enregistre();
					}

				}
			}
		}
	}else{
		$data->status = false;
		$data->message = "Certains des produits sont en quantité insuffisantes pour faire cette sortie de boutique !";
	}
	echo json_encode($data);
}



if ($action == "validerTransfertboutique") {
	$id = getSession("transfertboutique_id");
	$datas = TRANSFERTBOUTIQUE::findBy(["id ="=>$id, "etat_id = "=>ETAT::ENCOURS]);
	if (count($datas) > 0) {
		$transfert = $datas[0];
		$transfert->actualise();
		$transfert->fourni("lignetransfertboutique");

		$produits = explode(",", $tableau);
		foreach ($produits as $key => $value) {
			$lot = explode("-", $value);
			$array[$lot[0]] = end($lot);
		}

		if (count($produits) > 0) {
			$tests = $array;
			foreach ($tests as $key => $value) {
				foreach ($transfert->lignetransfertboutiques as $cle => $lgn) {
					if (($lgn->id == $key) && ($lgn->quantite_depart >= $value)) {
						unset($tests[$key]);
					}
				}
			}
			if (count($tests) == 0) {
				foreach ($array as $key => $value) {
					foreach ($transfert->lignetransfertboutiques as $cle => $lgn) {
						if ($lgn->id == $key) {
							$lgn->quantite = $value;
							$lgn->perte = $lgn->quantite_depart - $value;
							$lgn->save();
							break;
						}
					}					
				}
				$transfert->hydrater($_POST);
				$data = $transfert->valider();
			}else{
				$data->status = false;
				$data->message = "Veuillez à bien vérifier les quantités des différents produits, certaines sont incorrectes !";
			}			
		}else{
			$data->status = false;
			$data->message = "Une erreur s'est produite lors de l'opération! Veuillez recommencer";
		}
	}else{
		$data->status = false;
		$data->message = "Une erreur s'est produite lors de l'opération! Veuillez recommencer";
	}
	echo json_encode($data);
}