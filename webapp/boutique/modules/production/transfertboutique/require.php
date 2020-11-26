<?php 
namespace Home;

unset_session("produits");

$title = "GPV | Transfert en boutique de la production";

$datas1 = $boutique->fourni("transfertboutique", ["etat_id ="=>ETAT::ENCOURS], [], ["created"=>"DESC"]);;
$datas2 = TRANSFERTBOUTIQUE::findBy(["boutique_id_destination = "=>$boutique->id, "etat_id ="=>ETAT::ENCOURS], [], ["created"=>"DESC"]);;
$datas3 = $boutique->fourni("transfertboutique", ["etat_id ="=>ETAT::PARTIEL], [], ["created"=>"DESC"]);
$datas4 = TRANSFERTBOUTIQUE::findBy(["boutique_id_destination = "=>$boutique->id, "etat_id ="=>ETAT::PARTIEL], [], ["created"=>"DESC"]);;
$encours = array_merge($datas1, $datas2, $datas3, $datas4);


$datas1 = $boutique->fourni("transfertboutique", ["etat_id ="=>ETAT::VALIDEE], [], ["created"=>"DESC"]);;
$datas2 = TRANSFERTBOUTIQUE::findBy(["boutique_id_destination = "=>$boutique->id, "etat_id ="=>ETAT::VALIDEE], [], ["created"=>"DESC"]);;
$datas3 = $boutique->fourni("transfertboutique", ["etat_id ="=>ETAT::ANNULEE], [], ["created"=>"DESC"]);
$datas4 = TRANSFERTBOUTIQUE::findBy(["boutique_id_destination = "=>$boutique->id, "etat_id ="=>ETAT::ANNULEE], [], ["created"=>"DESC"]);;
$datas = array_merge($datas1, $datas2, $datas3, $datas4);

?>