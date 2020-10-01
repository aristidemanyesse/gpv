<?php 
namespace Home;

COMMERCIAL::finDuMois();
GROUPECOMMANDE::etat();


$produits = PRODUIT::findBy(["isActive ="=>TABLE::OUI]);


$title = "GPV | Tableau de bord";


$stats = VENTE::stats(dateAjoute(-7), dateAjoute(), $entrepot->id);


?>