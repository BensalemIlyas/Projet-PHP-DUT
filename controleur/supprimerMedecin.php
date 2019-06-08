

<?php
require_once('Medecin.php');
require_once('../modele/MedecinsManager.php');
//getIndicesTextuels retourne un tableau des caractéristiques unique d'un usager(nom,prenom,numeroSS.)
$donneesMedecinURL = Medecin::getIndicesUnqMedecin();
$arrDonneesMedecin = MedecinsManager::getMedecin($_GET['id_medecin']);
//creer un tableau associatif['nomColonneSQL']= ['valeurChampRenseignéForm'] avec nomColonneSQL = index$_GET
for ($i = 0 ; $i < count($donneesMedecinURL) ; $i++) {
	//remplir des tableaux associatifs
	$donneesMedecinObjet[$donneesMedecinURL[$i]] = $arrDonneesMedecin[$donneesMedecinURL[$i]];
}
$medecin = new Medecin($donneesMedecinObjet);
$medecinManager = new MedecinsManager();
if($medecinManager->supprimerMedecin($medecin)){
	header('Refresh:2;url=../vue/rechercherMedecin.php');
	//header('location: ../vue/rechercherMedecin.php');
	echo "Le medecin a bien été supprimé";
}else{
	header('Refresh:2;url=../vue/rechercherMedecin.php');
	echo "Un problème est survenu, le medecin ne peut être supprimé";
}
