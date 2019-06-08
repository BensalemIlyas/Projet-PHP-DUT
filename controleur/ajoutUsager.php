<?php
require_once('Usager.php');
require_once('../modele/UsagersManager.php');
if( isset($_POST) && !empty($_POST) ){
	//getIndicesTextuels retourne un tableau des caractéristiques d'un usager(nom,prenom,adresse...)
	$donneesUsagerForm = Usager::getIndicesTextuels();
	//creer un tableau associatif['nomColonneSQL']= ['valeurChampRenseignéForm'] avec nomColonneSQL = index$_POST
	for ($i = 0 ; $i < count($donneesUsagerForm) ; $i++) {
		//remplir des tableaux associatifs
		$donneesUsagerObjet[$donneesUsagerForm[$i]] = $_POST[$donneesUsagerForm[$i]];
	}

	$usager = new Usager($donneesUsagerObjet);
	$usagerManager = new UsagersManager();
	if ($usagerManager->ajoutUsager($usager)){
		//apres l'ajout(id_patient existe), si un medecin referent a été choisit on l'ajoute
		// a la table referent de la base de données à partir de l'id_medecin et l'id_patient
		if (! is_null($_POST['referent'])){
			$id_patient = UsagersManager::getId($_POST['nom'],$_POST['prenom'],$_POST['numeroSS']);
			echo (UsagersManager::ajoutReferent($id_patient,$_POST['referent']));
		}
		header('Refresh:5;url=../index.php');
		echo "Le patient a été ajouté avec succès";
	}else{
		header('Refresh:2;url=../vue/saisieUsager.php');
		echo "Un problème est survenu...Le patient ne peut être ajouté";
	}
}

?>