<?php
namespace Home;
use Native\RESPONSE;

/**
 * 
 */
class LIGNETRANSFERTBOUTIQUE extends TABLE
{
	
	
	public static $tableName = __CLASS__;
	public static $namespace = __NAMESPACE__;


	public $transfertboutique_id;
	public $produit_id;
	public $emballage_id;
	public $quantite_depart;
	public $quantite_demande;
	public $quantite;
	public $perte = 0;


	public function enregistre(){
		$data = new RESPONSE;
		$datas = MISEENBOUTIQUE::findBy(["id ="=>$this->transfertboutique_id]);
		if (count($datas) == 1) {
			$datas = PRODUIT::findBy(["id ="=>$this->produit_id]);
			if (count($datas) == 1) {
				if ($this->quantite_depart >= 0) {
					$this->quantite = $this->quantite_depart;
					$data = $this->save();
				}				
			}else{
				$data->status = false;
				$data->message = "Une erreur s'est produite lors de la mise en boutique du produit !";
			}			
		}else{
			$data->status = false;
			$data->message = "Une erreur s'est produite lors de la mise en boutique du produit !";
		}
		return $data;
	}




	public function sentenseCreate(){}
	public function sentenseUpdate(){}
	public function sentenseDelete(){}
}

?>