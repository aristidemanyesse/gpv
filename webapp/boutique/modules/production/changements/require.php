<?php 
namespace Home;
unset_session("emballagesource_id");
unset_session("emballagedestination_id");

$title = "GPV | Toutes les retours de stock dans cette boutique";

$datas = $boutique->fourni("retourstockboutique", ["DATE(created) >="=>$date1, "DATE(created) <="=>$date2], [], ["created"=>"DESC"]);

?>