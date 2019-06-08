<?php
require_once 'authenfication.php';
require_once 'MenuIndexModification.php';
require_once 'Formulaire.php';
require_once '../modele/MedecinsManager.php';

$formRecherche = new Formulaire('rechercherMedecin.php','POST');
$formRecherche->setLegend('Rechercher des medecins');
$formRecherche->setTextPh('recherche','recherche','rechercher');
if (isset($_GET) && !empty($_GET)){
	$demande['wish'] =  $_GET['wish'];
    $demande['id_patient']=  $_GET['id_patient'];
}else {
	$demande['wish'] = 'ModifOuSupression';
}

$formRecherche->setHidden('demande',implode(',',$demande));
$formRecherche->setSubmit('rechercher');
echo ($formRecherche->getForm());


if(isset($_POST['recherche']) && ! empty($_POST['recherche'])){

	$demande = explode(",",$_POST['demande']);
	$medecinsManager = new MedecinsManager();
	$reqRecherche = $medecinsManager->afficherMedecins($_POST['recherche']);
	//affiche le tableau html qui contient
	echo(Medecin::afficherTableauMedecins($reqRecherche, $demande));
	echo '<a href="saisieMedecin.php">Ajouter un nouveau medecin</a>';
}else {
    echo(Medecin::afficherAllMedecins());
}
