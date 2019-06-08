

<?php
require_once('Medecin.php');
require_once('../modele/MedecinsManager.php');
if(! empty($_POST)){
//getIndicesTextuels retourne un tableau des caractéristiques d'un usager(nom,prenom,adresse...)

	$donneesMedecinForm = Medecin::getIndicesTextuels();
//creer un tableau associatif['nomColonneSQL']= ['valeurChampRenseignéForm'] avec nomColonneSQL = index$_POST
	for ($i = 0 ; $i < count($donneesMedecinForm) ; $i++) {
	//remplir des tableaux associatifs//Erreur : $donneesMedecinObjet[$i] --> donne un tableau numerique

		$donneesMedecinObjet[$donneesMedecinForm[$i]] = $_POST[$donneesMedecinForm[$i]];
	}

	$medecin = new Medecin($donneesMedecinObjet);
	$medecinManager = new MedecinsManager();
	$ResultatRequete = $medecinManager->ajoutMedecin($medecin);

    switch($ResultatRequete[0]){
        case 'C' :
            header('Refresh:2;url=../vue/saisieMedecin.php');
            break;
        case 'U' :
            header('Refresh:2;url=../vue/saisieMedecin.php');
            break;
        case 'L' :
            header('Refresh:2;url=../index.php');
    }
		echo $ResultatRequete;

}

?>