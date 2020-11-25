<?php
namespace Home;
use Native\FICHIER;
use Native\RESPONSE;

/**
 * 
 */
class EMBALLAGE extends TABLE
{
	
	public static $tableName = __CLASS__;
	public static $namespace = __NAMESPACE__;

	const PRIMAIRE = 1;

	public $name ;
	public $quantite ;
	public $emballage_id ;
	public $isActive = TABLE::OUI;
	public $comptable = TABLE::OUI;
	public $price = 0;
	public $stkAlert = 0;
	public $image ;


	public function enregistre(){
		$data = new RESPONSE;
		if ($this->name != "") {
			$data = $this->save();
			if ($data->status) {

				$this->uploading($this->files);
				foreach (PRODUIT::getAll() as $key => $produit) {
					$item = new PRICE;
					$item->produit_id = $produit->id;
					$item->emballage_id = $this->id;
					$item->prix = 200;
					$item->prix_gros = 200;
					$item->enregistre();
				}

				foreach (ENTREPOT::getAll() as $key => $exi) {
					$ligne = new INITIALEMBALLAGEENTREPOT();
					$ligne->entrepot_id = $exi->id;
					$ligne->emballage_id = $this->id;
					$ligne->quantite = 0;
					$ligne->enregistre();
				}


				foreach (BOUTIQUE::getAll() as $key => $exi) {
					foreach (PRODUIT::getAll() as $key => $prod) {
						$ligne = new INITIALPRODUITBOUTIQUE();
						$ligne->boutique_id = $exi->id;
						$ligne->produit_id = $prod->id;
						$ligne->emballage_id = $this->id;
						$ligne->quantite = 0;
						$ligne->enregistre();
					}
				}


				foreach (ENTREPOT::getAll() as $key => $exi) {
					foreach (PRODUIT::getAll() as $key => $prod) {
						$ligne = new INITIALPRODUITENTREPOT();
						$ligne->entrepot_id = $exi->id;
						$ligne->produit_id = $prod->id;
						$ligne->emballage_id = $this->id;
						$ligne->quantite = 0;
						$ligne->enregistre();
					}
				}

			}
		}else{
			$data->status = false;
			$data->message = "Veuillez à bien renseigner le nime de l'emballage !";
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
						$result = $image->upload("images", "emballages", $a);
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
		return ($this->emballage_id == null);
	} 


	public function nombre(){
		if ($this->emballage_id == null) {
			return $this->quantite;
		}
		$this->actualise();
		return $this->quantite * $this->emballage->nombre();
	}



	public function totalEmballagePrice(){
		$this->actualise();
		if ($this->emballage_id == null) {
			return $this->quantite * $this->price();
		}
		return $this->quantite * $this->emballage->totalEmballagePrice();
	}


	public function packaging(int $value, int $id){
		foreach ($this->fourni("caracteristiquepackage") as $key => $carac) {
			$quantite = ($carac->quantite * $value) / $carac->quantite2;
			if ($quantite > 0) {
				$ligne = new LIGNECONSOMMATIONPACKAGE;
				$ligne->conditionnement_id = $id;
				$ligne->package_id = $carac->package_id;
				$ligne->quantite = $quantite;
				$ligne->enregistre();
			}
		}
		$this->actualise();
		if ($this->emballage->id > 0) {
			$this->emballage->packaging($value * $this->quantite, $id);
		}
	}


	public function isDisponible(int $a = 1){
		$tab = [];
		$test = true;
		if (getSession("emballages-disponibles") != null) {
			$tab = getSession("emballages-disponibles");
		}
		if ($this->comptable == TABLE::OUI) {
			$this->actualise();
			if ($this->emballage_id == null) {
				$test = ($this->stock(PARAMS::DATE_DEFAULT, dateAjoute(1), getSession("entrepot_connecte_id")) >=  $a);
			}else{
				$test = (($this->stock(PARAMS::DATE_DEFAULT, dateAjoute(1), getSession("entrepot_connecte_id")) >=  $a) && $this->emballage->isDisponible($this->emballage->quantite * $a));
			}
		}
		$tab[$this->id] = (isset($tab[$this->id]))? intval($tab[$this->id]) + intval($a) : intval($a);
		session("emballages-disponibles", $tab);
		return $test;
	}



	public function stock(String $date1, String $date2, int $entrepot_id){
		$item = $this->fourni("initialemballageentrepot", ["entrepot_id ="=>$entrepot_id])[0];
		return $this->achat($date1, $date2, $entrepot_id) - $this->consommee($date1, $date2, $entrepot_id) - $this->perte($date1, $date2, $entrepot_id) + $item->quantite;
	}


	public function achat(string $date1, string $date2, int $entrepot_id = null){
		$paras = "";
		if ($entrepot_id != null) {
			$paras.= "AND entrepot_id = $entrepot_id ";
		}
		$requette = "SELECT SUM(quantite_recu) as quantite  FROM ligneapproemballage, approemballage WHERE ligneapproemballage.emballage_id = ? AND ligneapproemballage.approemballage_id = approemballage.id AND approemballage.etat_id = ? AND DATE(approemballage.created) >= ? AND DATE(approemballage.created) <= ? $paras ";
		$item = LIGNEAPPROEMBALLAGE::execute($requette, [$this->id, ETAT::VALIDEE, $date1, $date2]);
		if (count($item) < 1) {$item = [new LIGNEAPPROEMBALLAGE()]; }
		return $item[0]->quantite;
	}



	public function consommee(string $date1, string $date2, int $entrepot_id = null){
		$total = 0;
		$paras = "";
		if ($entrepot_id != null) {
			$paras.= "AND entrepot_id = $entrepot_id ";
		}
		$requette = "SELECT SUM(ligneconditionnement.quantite) as quantite  FROM ligneconditionnement, conditionnement WHERE ligneconditionnement.emballage_id =  ? AND ligneconditionnement.conditionnement_id = conditionnement.id AND conditionnement.etat_id != ? AND DATE(conditionnement.created) >= ? AND DATE(conditionnement.created) <= ? $paras ";
		$item = LIGNECONDITIONNEMENT::execute($requette, [$this->id, ETAT::ANNULEE, $date1, $date2]);
		if (count($item) < 1) {$item = [new LIGNECONDITIONNEMENT()]; }

		foreach ($this->fourni("emballage", ["isActive ="=>TABLE::OUI]) as $key => $emballage) {
			$total += $emballage->consommee($date1, $date2, $entrepot_id) * $emballage->quantite;
		}
		return $item[0]->quantite + $total;
	}



	public function perte(string $date1, string $date2, int $entrepot_id = null){
		$paras = "";
		if ($entrepot_id != null) {
			$paras.= "AND entrepot_id = $entrepot_id ";
		}
		$requette = "SELECT SUM(quantite) as quantite  FROM perteentrepot WHERE perteentrepot.emballage_id = ? AND perteentrepot.produit_id IS NULL AND perteentrepot.etat_id = ? AND DATE(perteentrepot.created) >= ? AND DATE(perteentrepot.created) <= ? $paras ";
		$item = PERTEENTREPOT::execute($requette, [$this->id, ETAT::VALIDEE, $date1, $date2]);
		if (count($item) < 1) {$item = [new PERTEENTREPOT()]; }
		return $item[0]->quantite;
	}




	public function price(){
		$requette = "SELECT SUM(quantite_recu) as quantite, SUM(transport) as transport, SUM(ligneapproemballage.price) as price FROM ligneapproemballage, approemballage WHERE ligneapproemballage.emballage_id = ? AND ligneapproemballage.approemballage_id = approemballage.id AND approemballage.etat_id = ? ";
		$datas = LIGNEAPPROEMBALLAGE::execute($requette, [$this->id, ETAT::VALIDEE]);
		if (count($datas) < 1) {$datas = [new LIGNEAPPROEMBALLAGE()]; }
		$item = $datas[0];

		$requette = "SELECT SUM(quantite_recu) as quantite FROM ligneapproemballage, approemballage WHERE ligneapproemballage.approemballage_id = approemballage.id AND approemballage.id IN (SELECT approemballage_id FROM ligneapproemballage WHERE ligneapproemballage.emballage_id = ? ) AND approemballage.etat_id = ? ";
		$datas = LIGNEAPPROEMBALLAGE::execute($requette, [$this->id, ETAT::VALIDEE]);
		if (count($datas) < 1) {$datas = [new LIGNEAPPROEMBALLAGE()]; }
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
		foreach ($datas as $key => $emballage) {
			if ($emballage->stock(PARAMS::DATE_DEFAULT, dateAjoute(1), $entrepot_id) > $emballage->stkAlert) {
				unset($datas[$key]);
			}
		}
		return $datas;
	}





	public function sentenseCreate(){
		$this->sentense = "enregistrement d'un nouvel emballage ".$this->name();
	}
	public function sentenseUpdate(){
		$this->sentense = "Modification des informations de l'emballage ".$this->name();
	}
	public function sentenseDelete(){
		$this->sentense = "Suppression de l'emballage ".$this->name();
	}


}

?>