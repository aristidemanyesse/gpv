<?php 
namespace Home;

$title = "GPV | Tous les clients !";
$clients = CLIENT::findBy(["boutique_id ="=>$boutique->id],[],["name"=>"ASC"]);
$clients2 = CLIENT::findBy(["forAll ="=>TABLE::OUI],[],["name"=>"ASC"]);

$clients = array_merge($clients, $clients2);
?>