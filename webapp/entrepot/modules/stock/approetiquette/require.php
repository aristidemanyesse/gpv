<?php 
namespace Home;

unset_session("ressources");

$title = "GPV | Toutes les approvisionnements d'etiquette";

$encours = $entrepot->fourni("approetiquette", ["entrepot_id ="=>$entrepot->id, "etat_id ="=>ETAT::ENCOURS], [], ["created"=>"DESC"]);

$datas = $entrepot->fourni("approetiquette", ["entrepot_id ="=>$entrepot->id, "etat_id !="=>ETAT::ENCOURS, "DATE(created) >="=>$date1, "DATE(created) <="=>$date2], [], ["created"=>"DESC"]);

?>