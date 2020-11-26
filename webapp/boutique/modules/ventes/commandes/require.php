<?php 
namespace Home;

$title = "GPV | Toutes les commandes en cours";

GROUPECOMMANDE::etat();

foreach (COMMANDE::findBy(["boutique_id ="=> $boutique->id, "isRegle !="=>TABLE::OUI]) as $key => $comm) {
	if ($comm->reste() > 0) {
		$commandes[] = $comm;
	}else{
		$comm->isRegle = TABLE::OUI;
		$comm->save();
	}
}

$encours = GROUPECOMMANDE::encours($boutique->id);
$groupes = $boutique->fourni("groupecommande", ["etat_id !="=>ETAT::ENCOURS, "DATE(created) >="=>$date1, "DATE(created) <="=>$date2], [], ["created"=>"DESC"]);

?>