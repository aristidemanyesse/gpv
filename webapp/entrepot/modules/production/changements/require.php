<?php 
namespace Home;

$title = "GPV | Toutes les retours de stock dans cet entrepot";

unset_session("emballagesource_id");
unset_session("emballagedestination_id");

$datas = $entrepot->fourni("retourstockentrepot", ["DATE(created) >="=>$date1, "DATE(created) <="=>$date2], [], ["created"=>"DESC"]);

?>