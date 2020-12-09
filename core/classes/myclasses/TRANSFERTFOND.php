<?php
namespace Home;
use Native\RESPONSE;
use Native\EMAIL;
/**
 * 
 */
class TRANSFERTFOND extends TABLE
{
	public static $tableName = __CLASS__;
	public static $namespace = __NAMESPACE__;

	/* cette classe n'est liée à aucune table; elle ne sert que d'interface pour les operations de tranfert de fonds */

	public $montant;
	public $comptebanque_id_source;
	public $comptebanque_id_destination;
	public $comment;
	public $employe_id;
	public $etat_id = ETAT::ENCOURS;
	public $employe_id_validation;
	public $datevalidation;



	public function enregistre(){
		$data = new RESPONSE;
		if ($this->comptebanque_id_source != $this->comptebanque_id_destination) {
			$datas = COMPTEBANQUE::findBy(["id ="=>$this->comptebanque_id_source]);
			if (count($datas) == 1) {
				$source = $datas[0];
				$datas = COMPTEBANQUE::findBy(["id ="=>$this->comptebanque_id_destination]);
				if (count($datas) == 1) {
					$destination = $datas[0];
					if ($source->solde() >= $this->montant && $this->montant > 0) {
						$this->employe_id = getSession("employe_connecte_id");
						$data = $this->save() ;	
					}else{
						$data->status = false;
						$data->message = "Le montant pour cette opération est incorrecte, veuillez recommencer !!";
					}
				}else{
					$data->status = false;
					$data->message = "Une erreur s'est produite lors de l'opération, veuillez recommencer !!";
				}
			}else{
				$data->status = false;
				$data->message = "Une erreur s'est produite lors de l'opération, veuillez recommencer !!";
			}
		}else{
			$data->status = false;
			$data->message = "Vous ne pouvez pas faire un transfert de caisse sur le même compte, veuillez recommencer !!";
		}
		return $data;
	}



	public function valider(){
		$data = new RESPONSE;
		if ($this->etat_id == ETAT::ENCOURS) {
			$this->actualise();
			$data = $this->comptebanque_source->transaction($this->montant, $this->comptebanque_destination, $this->comment);
			if ($data->status) {
				$this->etat_id = ETAT::VALIDEE;
				$this->datevalidationa = date("Y-m-d H:i:s");
				$this->employe_id_validation = getSession("employe_connecte_id");
				$this->historique("Le transfert de fond de".$this->comptebanque_source->name()." vers ".$this->comptebanque_destination->name()." vient d'être validé !");
				$data = $this->save();
			}
		}else{
			$data->status = false;
			$data->message = "Vous ne pouvez plus valider ce transfert de fond !";
		}
		return $data;
	}



	public function sentenseCreate(){
		return $this->sentense = "Nouveau transfert de fond à partir de compte ".$this->comptebanque_source->name();
	}
	public function sentenseUpdate(){
		return $this->sentense = "Modification des informations du transfert de fond $this->id ";
	}
	public function sentenseDelete(){
		return $this->sentense = "Suppression definitive du transfert de fond $this->id";
	}

}



?>