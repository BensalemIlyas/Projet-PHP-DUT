<?php
require_once 'authenfication.php';
require_once 'Menu.php';
require_once '../controleur/Consultation.php';
require_once '../modele/ConsultationsManager.php';
require_once  'Formulaire.php';
    $formTri = new Formulaire('rechercherConsultation.php','POST');
    $formTri->setLegend('Trier les consultation par medecin');
    $formTri->setSelect('triMedecin','triMedecin');
    foreach (ConsultationsManager::getAllIdMedecinsConsultation() as $id){
        $medecin  = MedecinsManager::getMedecin($id['id_medecin']);
        $formTri->setOption($id['id_medecin'],$medecin['nom'].' '. $medecin['prenom']);
}
        $formTri->setOptionSelected('AllMedecins','Tous les médecins');
    $formTri->setEndSelect();
    $formTri->setSubmit('Trier');
    echo $formTri->getForm();
    //affichage du tableau html à partir d'une requête SQL qui retourne toutes les consultation trié par date et heure
    if(empty($_POST) OR  $_POST['triMedecin'] == 'AllMedecins'){
    echo (Consultation::afficherConsultation(ConsultationsManager::getConsultation()));
}else {
    $id_medecin = $_POST['triMedecin'];
    echo (Consultation::afficherConsultation(ConsultationsManager::getConsultationsByMedecin($id_medecin)));
}

?>
