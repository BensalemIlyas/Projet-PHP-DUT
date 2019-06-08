<?php
require_once('Usager.php');
require_once('../modele/UsagersManager.php');
//getIndicesTextuels retourne un tableau des caractéristiques unique d'un usager(nom,prenom,numeroSS.)
$donneesUsagerURL = Usager::getIndicesUnqUsager();
$arrDonneesUsager = UsagersManager::getUsager($_GET['id_patient']);
//creer un tableau associatif['nomColonneSQL']= ['valeurChampRenseignéForm'] avec nomColonneSQL = index$_GET
for ($i = 0 ; $i < count($donneesUsagerURL) ; $i++) {
	//remplir des tableaux associatifs
	$donneesUsagerObjet[$donneesUsagerURL[$i]] = $arrDonneesUsager[$donneesUsagerURL[$i]];
}
$usager = new Usager($donneesUsagerObjet);
$usagerManager = new UsagersManager();
if($usagerManager->supprimerUsager($usager)){
	header('Refresh:1;url=../vue/rechercherUsager.php');
	echo "Le patient a bien été supprimé";
}else{
	header('Refresh:1;url=../vue/rechercherUsager.php');
	echo "Un problème est survenu, le patient ne peut être supprimé";
}