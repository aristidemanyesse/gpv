<?php 
namespace Home;
use Native\ROOTER;
require '../../../../../core/root/includes.php';
use Native\RESPONSE;

$data = new RESPONSE;
extract($_POST);




if ($action == "changementPrice") {
	$datas = PRICE::findBy(["id ="=>$id]);
	if (count($datas) == 1) {
		$prix = $datas[0];
		$prix->$name = intval($val);
		$data = $prix->save();
	}
	echo json_encode($data);
}


if ($action == "changement") {
	$datas = PRODUIT::findBy(["id ="=>$id]);
	if (count($datas) == 1) {
		$prix = $datas[0];
		$prix->$name = intval($val);
		$data = $prix->save();
	}
	echo json_encode($data);
}

