<?php 
namespace Home;
use Faker\Factory;
$faker = Factory::create();

if ($this->id != null) {

	$title = "GPV | Trésorerie générale";

}else{
	header("Location: ../master/clients");
}
?>