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



	public function enregistre(){
		$data = new RESPONSE;
		if ($this->comptebanque_id_source != $this->comptebanque_id_destination) {
			$datas = COMPTEBANQUE::findBy(["id ="=>$this->comptebanque_id_source]);
			if (count($datas) == 1) {
				$source = $datas[0];
				$datas = COMPTEBANQUE::findBy(["id ="=>$this->comptebanque_id_destination]);
				if (count($datas) == 1) {
					$destinataire = $datas[0];
					$data = $source->transaction($this->montant, $destinataire, $this->comment);
					if ($data->status) {
						$this->employe_id = getSession("employe_connecte_id");
						$data = $this->save() ;
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