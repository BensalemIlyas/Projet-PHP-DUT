<?php
require_once 'Usager.php';
require_once '../modele/UsagersManager.php';
if(isset($_POST) AND ! empty($_POST)){
	$i = 0;
	foreach (Usager::getIndicesTextuels() as $indiceTextuels ) {
		$donneesUsager[$indiceTextuels] = $_POST[$indiceTextuels];
		$i++;
	}
	$usagerModif = new Usager($donneesUsager); 
	$usagerManager =  new UsagersManager();
	if($usagerManager->modifierUsager($usagerModif,$_POST['oldNom'],$_POST['oldPrenom'],$_POST['oldNumeroSS'])){
		header('Refresh:2;url=../vue/rechercherUsager.php');
		echo "Le contact a bien été modifié";
	}
}
?>