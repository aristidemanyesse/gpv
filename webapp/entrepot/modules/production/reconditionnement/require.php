<?php 
namespace Home;

$title = "GPV | Toutes les retours de stock dans cet entrepot";

unset_session("emballage_id");

$datas = $entrepot->fourni("reconditionnement", ["DATE(created) >="=>$date1, "DATE(created) <="=>$date2], [], ["created"=>"DESC"]);

?>