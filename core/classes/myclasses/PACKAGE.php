<?php
namespace Home;
use Native\FICHIER;
use Native\RESPONSE;

/**
 * 
 */
class PACKAGE extends TABLE
{
	
	public static $tableName = __CLASS__;
	public static $namespace = __NAMESPACE__;

	public $name ;
	public $unite ;
	public $isActive = TABLE::OUI;
	public $price = 0;
	public $stkAlert = 0;
	public $image ;


	public function enregistre(){
		$data = new RESPONSE;
		if ($this->name != "") {
			$data = $this->save();
			if ($data->status) {
				
				$this->uploading($this->files);
				
				foreach (ENTREPOT::getAll() as $key => $exi) {
					$ligne = new INITIALPACKAGEENTREPOT();
					$ligne->entrepot_id = $exi->id;
					$ligne->package_id = $this->id;
					$ligne->quantite = 0;
					$ligne->enregistre();
				}

			}
		}else{
			$data->status = false;
			$data->message = "Veuillez à bien renseigner le nime de l'package !";
		}
		return $data;
	}



	public function uploading(Array $files){
		//les proprites d'images;
		$tab = ["image"];
		if (is_array($files) && count($files) > 0) {
			$i = 0;
			foreach ($files as $key => $file) {
				if ($file["tmp_name"] != "") {
					$image = new FICHIER();
					$image->hydrater($file);
					if ($image->is_image()) {
						$a = substr(uniqid(), 5);
						$result = $image->upload("images", "packages", $a);
						$name = $tab[$i];
						$this->$name = $result->filename;
						$this->save();
					}
				}	
				$i++;			
			}			
		}
	}


	public function isPrimaire(){
		return ($this->package_id == null);
	} 


	public function nombre(){
		if ($this->package_id == null) {
			return $this->quantite;
		}
		$this->actualise();
		return $this->quantite * $this->package->nombre();
	}



	public function totalEmballagePrice(){
		$this->actualise();
		if ($this->package_id == null) {
			return $this->quantite * $this->price();
		}
		return $this->quantite * $this->package->totalEmballagePrice();
	}



	public function isDisponible(int $a = 1){
		$tab = [];
		if (getSession("packages-disponibles") != null) {
			$tab = getSession("packages-disponibles");
		}
		$this->actualise();
		if ($this->package_id == null) {
			$test = ($this->stock(PARAMS::DATE_DEFAULT, dateAjoute(1), getSession("entrepot_connecte_id")) >=  $a);
			$tab[$this->id] = (isset($tab[$this->id]))? intval($tab[$this->id]) + intval($a) : intval($a);
			session("packages-disponibles", $tab);
			return $test;
		}
		$test = (($this->stock(PARAMS::DATE_DEFAULT, dateAjoute(1), getSession("entrepot_connecte_id")) >=  $a) && $this->package->isDisponible($this->package->quantite * $a));
		$tab[$this->id] = (isset($tab[$this->id]))? intval($tab[$this->id]) + intval($a) : intval($a);
		session("packages-disponibles", $tab);
		return $test;
	}



	public function stock(String $date1, String $date2, int $entrepot_id){
		$item = $this->fourni("initialpackageentrepot", ["entrepot_id ="=>$entrepot_id])[0];
		return $this->achat($date1, $date2, $entrepot_id) - $this->consommee($date1, $date2, $entrepot_id) - $this->perte($date1, $date2, $entrepot_id) + $item->quantite;
	}


	public function achat(string $date1, string $date2, int $entrepot_id = null){
		$paras = "";
		if ($entrepot_id != null) {
			$paras.= "AND entrepot_id = $entrepot_id ";
		}
		$requette = "SELECT SUM(quantite_recu) as quantite  FROM ligneappropackage, appropackage WHERE ligneappropackage.package_id = ? AND ligneappropackage.appropackage_id = appropackage.id AND appropackage.etat_id = ? AND DATE(appropackage.created) >= ? AND DATE(appropackage.created) <= ? $paras ";
		$item = LIGNEAPPROPACKAGE::execute($requette, [$this->id, ETAT::VALIDEE, $date1, $date2]);
		if (count($item) < 1) {$item = [new LIGNEAPPROPACKAGE()]; }
		return $item[0]->quantite;
	}



	public function consommee(string $date1, string $date2, int $entrepot_id = null){
		$total = 0;
		$paras = "";
		if ($entrepot_id != null) {
			$paras.= "AND entrepot_id = $entrepot_id ";
		}
		$requette = "SELECT SUM(ligneconsommationpackage.quantite) as quantite  FROM ligneconsommationpackage, conditionnement WHERE ligneconsommationpackage.package_id =  ? AND ligneconsommationpackage.conditionnement_id = conditionnement.id AND conditionnement.etat_id != ? AND DATE(conditionnement.created) >= ? AND DATE(conditionnement.created) <= ? $paras ";
		$item = LIGNECONSOMMATIONPACKAGE::execute($requette, [$this->id, ETAT::ANNULEE, $date1, $date2]);
		if (count($item) < 1) {$item = [new LIGNECONSOMMATIONPACKAGE()]; }

		return $item[0]->quantite + $total;
	}



	public function perte(string $date1, string $date2, int $entrepot_id = null){
		$paras = "";
		if ($entrepot_id != null) {
			$paras.= "AND entrepot_id = $entrepot_id ";
		}
		$requette = "SELECT SUM(quantite) as quantite  FROM perteentrepot WHERE perteentrepot.package_id = ? AND perteentrepot.produit_id IS NULL AND perteentrepot.etat_id = ? AND DATE(perteentrepot.created) >= ? AND DATE(perteentrepot.created) <= ? $paras ";
		$item = PERTEENTREPOT::execute($requette, [$this->id, ETAT::VALIDEE, $date1, $date2]);
		if (count($item) < 1) {$item = [new PERTEENTREPOT()]; }
		return $item[0]->quantite;
	}




	public function price(){
		$requette = "SELECT SUM(quantite_recu) as quantite, SUM(transport) as transport, SUM(ligneappropackage.price) as price FROM ligneappropackage, appropackage WHERE ligneappropackage.package_id = ? AND ligneappropackage.appropackage_id = appropackage.id AND appropackage.etat_id = ? ";
		$datas = LIGNEAPPROPACKAGE::execute($requette, [$this->id, ETAT::VALIDEE]);
		if (count($datas) < 1) {$datas = [new LIGNEAPPROPACKAGE()]; }
		$item = $datas[0];

		$requette = "SELECT SUM(quantite_recu) as quantite FROM ligneappropackage, appropackage WHERE ligneappropackage.appropackage_id = appropackage.id AND appropackage.id IN (SELECT appropackage_id FROM ligneappropackage WHERE ligneappropackage.package_id = ? ) AND appropackage.etat_id = ? ";
		$datas = LIGNEAPPROPACKAGE::execute($requette, [$this->id, ETAT::VALIDEE]);
		if (count($datas) < 1) {$datas = [new LIGNEAPPROPACKAGE()]; }
		$ligne = $datas[0];

		if ($item->quantite == 0) {
			return 0;
		}
		if (intval($this->price) <= 0) {
			$total = ($item->price / $item->quantite) + ($item->transport / $ligne->quantite);
			return $total;
		}
		return $this->price + ($item->transport / $ligne->quantite);
	}



	public static function ruptureEntrepot(int $entrepot_id = null){
		$params = PARAMS::findLastId();
		$datas = static::findBy(["isActive ="=>TABLE::OUI]);
		foreach ($datas as $key => $item) {
			if ($item->stock(PARAMS::DATE_DEFAULT, dateAjoute(1), $entrepot_id) > $params->ruptureStock) {
				unset($datas[$key]);
			}
		}
		return $datas;
	}





	public function sentenseCreate(){
		$this->sentense = "enregistrement d'un nouvel package ".$this->name();
	}
	public function sentenseUpdate(){
		$this->sentense = "Modification des informations de l'package ".$this->name();
	}
	public function sentenseDelete(){
		$this->sentense = "Suppression de l'package ".$this->name();
	}


}

?>