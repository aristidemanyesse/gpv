<?php
namespace Home;
use Native\RESPONSE;

/**
 * 
 */
class CARACTERISTIQUEPACKAGE extends TABLE
{
	
	
	public static $tableName = __CLASS__;
	public static $namespace = __NAMESPACE__;


	public $package_id ;
	public $quantite ;
	public $emballage_id ;
	public $quantite2 ;


	public function enregistre(){
		$data = new RESPONSE;
		$datas = PACKAGE::findBy(["id ="=>$this->package_id]);
		if (count($datas) == 1) {
			$datas = EMBALLAGE::findBy(["id ="=>$this->emballage_id]);
			if (count($datas) == 1) {
				$data = $this->save();
			}else{
				$data->status = false;
				$data->message = "Une erreur s'est produite lors de l'enregistrement, veuillez recommencer !";
			}
		}else{
			$data->status = false;
			$data->message = "Une erreur s'est produite lors de l'enregistrement, veuillez recommencer !";
		}
		return $data;
	}



	public function typeproduit(){
		if ($this->typeproduit_id != null) {
			return "Seulement que les ".$this->typeproduit->name();
		}
		return "Tous les produits";
	}


	public function parfum(){
		if ($this->parfum_id != null) {
			return " de ".$this->parfum->name();
		}
		return " de tous les parfums ";
	}

	public function quantite(){
		if ($this->quantite_id != null) {
			return " de ".$this->quantite->name();
		}
		return " de toutes les quantite";
	}


	public function stock(String $date){
		return $this->achat("2020-06-01", $date) + intval($this->stock) - $this->consommee("2020-06-01", $date);
	}


	public function consommee(string $date1 = "2020-06-01", string $date2){
		$requette = "SELECT SUM(production) as production  FROM production, ligneproduction, prixdevente, quantite, package WHERE ligneproduction.produit_id = prixdevente.id AND ligneproduction.production_id = production.id AND prixdevente.quantite_id = quantite.id AND package.quantite_id = quantite.id AND  package.id = ? AND production.etat_id != ? AND DATE(ligneproduction.created) >= ? AND DATE(ligneproduction.created) <= ? GROUP BY package.id";
		$item = LIGNEPRODUCTION::execute($requette, [$this->id, ETAT::ANNULEE, $date1, $date2]);
		if (count($item) < 1) {$item = [new LIGNEPRODUCTION()]; }
		return $item[0]->production;
	}


	public function achat(string $date1 = "2020-04-01", string $date2){
		$total = 0;
		$requette = "SELECT SUM(quantite_recu) as quantite  FROM ligneappropackage, package, appropackage WHERE ligneappropackage.package_id = package.id AND package.id = ? AND ligneappropackage.appropackage_id = appropackage.id AND appropackage.etat_id = ? AND DATE(appropackage.created) >= ? AND DATE(appropackage.created) <= ? GROUP BY package.id";
		$item = LIGNEAPPROEMBALLAGE::execute($requette, [$this->id, ETAT::VALIDEE, $date1, $date2]);
		if (count($item) < 1) {$item = [new LIGNEAPPROEMBALLAGE()]; }
		return $item[0]->quantite;
	}



	public function en_cours(){
		$total = 0;
		$requette = "SELECT SUM(quantite) as quantite  FROM ligneappropackage, package, appropackage WHERE ligneappropackage.package_id = package.id AND package.id = ? AND ligneappropackage.appropackage_id = appropackage.id AND appropackage.etat_id = ? GROUP BY package.id";
		$item = LIGNEAPPROEMBALLAGE::execute($requette, [$this->id, ETAT::ENCOURS]);
		if (count($item) < 1) {$item = [new LIGNEAPPROEMBALLAGE()]; }
		return $item[0]->quantite;
	}



	public function exigence(int $quantite, int $produit_id){
		$datas = EXIGENCEPRODUCTION::findBy(["package_id ="=>$this->id, "produit_id ="=>$produit_id]);
		if (count($datas) == 1) {
			$item = $datas[0];
			if ($item->quantite_package == 0) {
				return 0;
			}
			return ($quantite * $item->quantite_produit) / $item->quantite_package;
		}
		return 0;
	}



	public function price(){
		$total = 0;
		$requette = "SELECT SUM(quantite_recu) as quantite, SUM(ligneappropackage.price) as price FROM ligneappropackage, package, appropackage WHERE ligneappropackage.package_id = package.id AND package.id = ? AND ligneappropackage.appropackage_id = appropackage.id AND appropackage.etat_id = ? GROUP BY package.id";
		$item = LIGNEAPPROEMBALLAGE::execute($requette, [$this->id, ETAT::VALIDEE]);
		if (count($item) < 1) {$item = [new LIGNEAPPROEMBALLAGE()]; }
		if ($item[0]->quantite > 0) {
			$total += $item[0]->price / $item[0]->quantite;
			return $total;
		}
		return 0;
	}



	public static function rupture(){
		$params = PARAMS::findLastId();
		$datas = static::findBy(["isActive ="=>TABLE::OUI]);
		foreach ($datas as $key => $item) {
			if ($item->enEntrepot(dateAjoute()) > $params->ruptureStock) {
				unset($datas[$key]);
			}
		}
		return $datas;
	}



	public function coutProduction(String $type, int $quantite){
		if(isJourFerie(dateAjoute())){
			$datas = PAYEFERIE_PRODUIT::findBy(["produit_id ="=>$this->id]);
		}else{
			$datas = PAYE_PRODUIT::findBy(["produit_id ="=>$this->id]);
		}
		if (count($datas) > 0) {
			$ppr = $datas[0];
			switch ($type) {
				case 'production':
				$prix = $ppr->price;
				break;
				
				case 'rangement':
				$prix = $ppr->price_rangement;
				break;

				case 'vente':
				$prix = $ppr->price_vente;
				break;

				default:
				$prix = $ppr->price;
				break;
			}
			return $quantite * $prix;
		}
		return 0;
	}



	public function changerMode(){
		if ($this->isActive == TABLE::OUI) {
			$this->isActive = TABLE::NON;
		}else{
			$this->isActive = TABLE::OUI;
			$pro = PRODUCTION::today();
			$datas = LIGNEPRODUCTION::findBy(["production_id ="=>$pro->id, "produit_id ="=>$pdv->id]);
			if (count($datas) == 0) {
				$ligne = new LIGNEPRODUCTION();
				$ligne->production_id = $pro->id;
				$ligne->produit_id = $pdv->id;
				$ligne->enregistre();
			}			
		}
		return $this->save();
	}



	public function sentenseCreate(){
		return $this->sentense = "Creation d'une nouvelle caractéristique du package";
	}
	public function sentenseUpdate(){
		return $this->sentense = "Modification des informations de la caractéristique du package N°$this->id ";
	}
	public function sentenseDelete(){
		return $this->sentense = "Suppression definitive de la caractéristique du package N°$this->id";
	}
}

?>