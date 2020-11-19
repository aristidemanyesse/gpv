<?php 
namespace Home;

unset_session("ressources");

$title = "GPV | Toutes les approvisionnements des extras d'emballage";

$encours = $entrepot->fourni("appropackage", ["etat_id ="=>ETAT::ENCOURS], [], ["created"=>"DESC"]);

$datas = $entrepot->fourni("appropackage", ["etat_id !="=>ETAT::ENCOURS, "DATE(created) >="=>$date1, "DATE(created) <="=>$date2], [], ["created"=>"DESC"]);

?>