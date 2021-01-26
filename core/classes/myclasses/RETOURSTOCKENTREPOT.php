<?php
namespace Home;
use Native\RESPONSE;
use Native\EMAIL;
/**
 * 
 */
class RETOURSTOCKENTREPOT extends TABLE
{
	public static $tableName = __CLASS__;
	public static $namespace = __NAMESPACE__;

	public $boutique_id;
	public $entrepot_id;

	public $produit_id_source;
	public $emballage_id_source;
	public $quantite;

	public $produit_id_destination;
	public $emballage_id_destination;
	public $quantite1;

	public $comment;
	public $employe_id;
	public $etat_id = ETAT::VALIDEE;




	public function enregistre(){
		$data = new RESPONSE;
		$this->employe_id = getSession("employe_connecte_id");
		$this->entrepot_id = getSession("entrepot_connecte_id");
		$this->emballage_id_source = getSession("emballagesource_id");
		$this->emballage_id_destination = getSession("emballagedestination_id");

		if (!(($this->produit_id_source == $this->produit_id_destination) && ($this->emballage_id_source == $this->emballage_id_destination))) {
			$datas = PRODUIT::findBy(["id ="=>$this->produit_id_source]);
			if (count($datas) == 1) {
				$produitsource = $datas[0];
				$datas = PRODUIT::findBy(["id ="=>$this->produit_id_destination]);
				if (count($datas) == 1) {
					$produitdestination = $datas[0];
					$datas = EMBALLAGE::findBy(["id ="=>$this->emballage_id_source]);
					if (count($datas) == 1) {
						$emb1 = $datas[0];
						$datas = EMBALLAGE::findBy(["id ="=>$this->emballage_id_destination]);
						if (count($datas) == 1) {
							$emb2 = $datas[0];

							if ($this->quantite >= 1 && $this->quantite1 >= 1){
								if ($produitdestination->enEntrepot(PARAMS::DATE_DEFAULT, dateAjoute(1), $this->emballage_id_destination, getSession("entrepot_connecte_id")) >= $this->quantite1) {

									if ($produitsource->enBoutique(PARAMS::DATE_DEFAULT, dateAjoute(1), $this->emballage_id_source, $this->boutique_id) >= $this->quantite) {

										$data = $this->save();

									}else{
										$data->status = false;
										$data->message = "Vous n'avez pas suffisemment de stock de ce produit en dans la boutique pour l'echanger, veuillez recommencer !!";
									}									
								}else{
									$data->status = false;
									$data->message = "Vous n'avez pas suffisemment de stock de ce produit en entrepot pour l'echanger, veuillez recommencer !!";
								}
							}else{
								$data->status = false;
								$data->message = "Veuillez vérifier la quantité à transferer, veuillez recommencer !!";
							}

						}else{
							$data->status = false;
							$data->message = "Veuillez selectionner le type d'emballage à transferer, veuillez recommencer !!";
						}
					}else{
						$data->status = false;
						$data->message = "Veuillez selectionner le type d'emballage à retourner, veuillez recommencer !!";
					}
				}else{
					$data->status = false;
					$data->message = "Veuillez selectionner le produit final, veuillez recommencer !!";
				}
			}else{
				$data->status = false;
				$data->message = "Veuillez selectionner le produit à retourner, veuillez recommencer !!";
			}
		}else{
			$data->status = false;
			$data->message = "Vous ne pouvez pas convertir dans le même produit, veuillez recommencer !!";
		}

		return $data;
	}


	public function sentenseCreate(){
		return $this->sentense = "Nouveau retour  de stock à partir de la boutique ".$this->boutique->name();
	}
	public function sentenseUpdate(){
		return $this->sentense = "Modification des informations du retour  de stock $this->id ";
	}
	public function sentenseDelete(){
		return $this->sentense = "Suppression definitive du retour  de stock $this->id";
	}

}



?>