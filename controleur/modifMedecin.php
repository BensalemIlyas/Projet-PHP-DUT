<?php
require_once 'Medecin.php';
require_once '../modele/MedecinsManager.php';
if(isset($_POST) AND ! empty($_POST)){
	$i = 0;
	foreach (Medecin::getIndicesTextuels() as $indiceTextuels ) {
		$donneesMedecin[$indiceTextuels] = $_POST[$indiceTextuels];
		$i++;
	}
	$medecinModif = new Medecin($donneesMedecin);
	$medecinManager =  new MedecinsManager();
	if($medecinManager->modifierMedecin($medecinModif,$_POST['oldNom'],$_POST['oldPrenom'])){
		//header('Refresh:2;url=../vue/rechercherMedecin.php');
		//header('location: ../vue/rechercherMedecin.php');
		echo "Le medecin a bien été modifié <br/>";
		echo '<a href="../vue/rechercherMedecin.php">retour rechercher medecin </a>'
	}
}
?>
