<?php 
namespace Home;
unset_session("emballages");
unset_session("ressources");

$emballages = EMBALLAGE::getAll();
foreach ($emballages as $key => $value) {
	if ($value->comptable == TABLE::NON) {
		unset($emballages[$key]);
	}
}

$title = "GPV | Stock des emballages ";
?>