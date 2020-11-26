<?php
namespace Home;
use Native\RESPONSE;

/**
 * 
 */
class TRANSFERTBOUTIQUE extends TABLE
{
	
	
	public static $tableName = __CLASS__;
	public static $namespace = __NAMESPACE__;

	public $reference;
	public $employe_id;
	public $boutique_id;
	public $boutique_id_destination;
	public $datereception;
	public $employe_id_reception;
	public $employe_id_accepter;

	public $nom_livreur;
	public $contact_livreur;

	public $etat_id = ETAT::ENCOURS;
	public $comment;


	public function enregistre(){
		$data = new RESPONSE;
		if ($this->boutique_id != $this->boutique_id_destination) {
			$datas = BOUTIQUE::findBy(["id ="=>$this->boutique_id]);
			if (count($datas) == 1) {
				$datas = BOUTIQUE::findBy(["id ="=>$this->boutique_id_destination]);
				if (count($datas) == 1) {
					$this->reference = "TRB/".date('dmY')."-".strtoupper(substr(uniqid(), 5, 6));
					$this->employe_id = getSession("employe_connecte_id");
					$data = $this->save();				
				}else{
					$data->status = false;
					$data->message = "Une erreur s'est produite lors du prix !";
				}				
			}else{
				$data->status = false;
				$data->message = "Une erreur s'est produite lors du prix !";
			}
		}else{
			$data->status = false;
			$data->message = "Vous ne pouvez pas faire un transfert de stock dans votre propre boutique !";
		}

		return $data;
	}



	public function valider(){
		$data = new RESPONSE;
		if ($this->etat_id == ETAT::ENCOURS) {
			$this->etat_id = ETAT::VALIDEE;
			$this->datereception = date("Y-m-d H:i:s");
			$this->employe_id_reception = getSession("employe_connecte_id");
			$this->historique("La mise en boutique en reference $this->reference vient d'être receptionné !");
			$data = $this->save();
		}else{
			$data->status = false;
			$data->message = "Vous ne pouvez plus faire cette opération sur cette mise en boutique !";
		}
		return $data;
	}


	public function accepter(){
		$data = new RESPONSE;
		if ($this->etat_id == ETAT::PARTIEL) {
			$this->etat_id = ETAT::ENCOURS;
			$this->employe_id_accepter = getSession("employe_connecte_id");
			$this->historique("La demande de mise en boutique en reference $this->reference vient d'être accepté !");
			$data = $this->save();
		}else{
			$data->status = false;
			$data->message = "Vous ne pouvez plus faire cette opération sur cette mise en boutique !";
		}
		return $data;
	}






	public function sentenseCreate(){
		return $this->sentense = "enregistrement d'un nouveau transfert en boutique N°$this->reference";
	}
	public function sentenseUpdate(){
		return $this->sentense = "Modification des informations du transfert en boutique N°$this->reference ";
	}
	public function sentenseDelete(){
		return $this->sentense = "Suppression definitive du transfert en boutique N°$this->reference";
	}
}

?>