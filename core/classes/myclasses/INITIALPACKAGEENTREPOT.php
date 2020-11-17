<?php
namespace Home;
use Native\RESPONSE;/**
 * 
 */
class INITIALPACKAGEENTREPOT extends TABLE
{

	public static $tableName = __CLASS__;
	public static $namespace = __NAMESPACE__;


	public $entrepot_id;
	public $package_id;
	public $quantite = 0;

	public function enregistre(){
		$data = new RESPONSE;
		$datas = ENTREPOT::findBy(["id ="=>$this->entrepot_id]);
		if (count($datas) == 1) {
			$datas = PACKAGE::findBy(["id ="=>$this->package_id]);
			if (count($datas) == 1) {
				if ($this->quantite >= 0) {
					$data = $this->save();
				}else{
					$data->status = false;
					$data->message = "Veuillez renseigner la bonne quantité du stock !";
				}
			}else{
				$data->status = false;
				$data->message = "veuillez selectionner un commercial pour la vente!";
			}
		}else{
			$data->status = false;
			$data->message = "Une erreur s'est produite lors de l'enregistrement de la vente!";
		}
		return $data;
	}



	public function sentenseCreate(){
		return $this->sentense = "";
	}
	public function sentenseUpdate(){
		return $this->sentense = "Modification du stock initial du package: ".$this->package->name()." dans ".$this->entrepot->name();
	}
	public function sentenseDelete(){
		return $this->sentense = "Suppression definitive de l'element $this->id :$this->id";
	}


}
?>