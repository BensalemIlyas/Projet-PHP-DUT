<?php
require_once 'authenfication.php';
require_once 'Menu.php';
require_once 'Formulaire.php';
require_once '../modele/UsagersManager.php';
//$_SERVER['HTTP_REFERER'] ='http://localhost/projetCabinetMedical/vue/lienConsultation.php';
//savoir si on doit afficher un lien pour une consultation ou une modif/suppression ds le tableau
//if(h)
$demande ='consultation';
if(isset($_GET['demande'])){
   $_SESSION['donneesConsultation'] = array('id_patient' =>$_GET['id_patient'],'id_medecin' =>$_GET['id_medecin'],'dateConsultation' => $_GET['dateConsultation'],
       'heureConsultation' => $_GET['heureConsultation'], 'dureeConsultation' => $_GET['dureeConsultation'], 'demande' => $_GET['demande']);

}
$formRecherche = new Formulaire('rechercherUsagerConsultation.php','POST');
$formRecherche->setLegend('Rechercher les patients');
$formRecherche->setTextPh('recherche','recherche','rechercher');
if (!empty( $_SESSION['donneesConsultation'])){
    $formRecherche->setHidden('wish',$_SESSION['donneesConsultation']['demande']);
}else{
    $formRecherche->setHidden('wish',$demande);
}
$formRecherche->setSubmit('rechercher');
echo ($formRecherche->getForm());

if(isset($_POST['recherche']) && ! empty($_POST['recherche'])){
	//echo $_SERVER['HTTP_REFERER'];
	$usagersManager = new UsagersManager();
	$reqRecherche = $usagersManager->afficherUsagers($_POST['recherche']);
	//affiche le tableau html qui contient 
	echo(Usager::afficherTableauUsagers($reqRecherche, $_POST['wish']));
	echo '<a href="saisieUsager.php">Ajouter un nouveau patient</a>';
}else{
    echo (Usager::afficherAllUsagers('consultation'));
}
