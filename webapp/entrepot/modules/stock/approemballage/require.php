<?php 
namespace Home;

unset_session("ressources");

$title = "GPV | Toutes les approvisionnements d'emballage";

$encours = $entrepot->fourni("approemballage", ["etat_id ="=>ETAT::ENCOURS], [], ["created"=>"DESC"]);

$datas = $entrepot->fourni("approemballage", ["etat_id !="=>ETAT::ENCOURS, "DATE(created) >="=>$date1, "DATE(created) <="=>$date2], [], ["created"=>"DESC"]);

?>