<?php
namespace Home;
use Native\RESPONSE;
use Native\EMAIL;
/**
 * 
 */
class RECONDITIONNEMENT extends TABLE
{
	public static $tableName = __CLASS__;
	public static $namespace = __NAMESPACE__;

	public $entrepot_id;

	public $produit_id;
	public $emballage_id;
	public $quantite;

	public $comment;
	public $employe_id;
	public $etat_id = ETAT::VALIDEE;




	public function enregistre(){
		$data = new RESPONSE;
		$this->employe_id = getSession("employe_connecte_id");
		$this->entrepot_id = getSession("entrepot_connecte_id");
		$this->emballage_id = getSession("emballage_id");

		$datas = PRODUIT::findBy(["id ="=>$this->produit_id]);
		if (count($datas) == 1) {
			$produit = $datas[0];
			$datas = EMBALLAGE::findBy(["id ="=>$this->emballage_id]);
			if (count($datas) == 1) {
				$emb = $datas[0];

				if ($this->quantite >= 1){
					if ($produit->enentrepot(PARAMS::DATE_DEFAULT, dateAjoute(1), $this->emballage_id, getSession("entrepot_connecte_id")) >= $this->quantite) {

						$data = $this->save();
					}else{
						$data->status = false;
						$data->message = "Vous n'avez pas cette quantité de ce produit , veuillez recommencer !!";
					}
				}else{
					$data->status = false;
					$data->message = "Veuillez vérifier la quantité à reconditionner, veuillez recommencer !!";
				}
			}else{
				$data->status = false;
				$data->message = "Veuillez selectionner le type d'emballage à reconditionner, veuillez recommencer !!";
			}
		}else{
			$data->status = false;
			$data->message = "Veuillez selectionner le produit à reconditionner, veuillez recommencer !!";
		}
		return $data;
	}


	public function sentenseCreate(){
		return $this->sentense = "Nouveau reconditionnement de stock à partir de l'entrepot ".$this->entrepot->name();
	}
	public function sentenseUpdate(){
		return $this->sentense = "Modification des informations du reconditionnement de stock $this->id ";
	}
	public function sentenseDelete(){
		return $this->sentense = "Suppression definitive du reconditionnement de stock $this->id";
	}

}



?>