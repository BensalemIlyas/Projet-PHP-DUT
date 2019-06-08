<?php
require_once 'authenfication.php';
require_once 'MenuIndexModification.php';
require_once 'Formulaire.php';
require_once '../modele/MedecinsManager.php';
require_once '../modele/UsagersManager.php';
if (isset($_GET) && !empty($_GET)){
    $formConsultation = new Formulaire('../../controleur/modifConsultation.php','POST');
    $formConsultation->setLegend('Modifier une consultation');
    $formConsultation->setLabel('nomPatient','Nom du patient');
    //recuper les données du patient sous forme de tableau associatif
    $patient = UsagersManager::getUsager($_GET['id_patient']);
    $formConsultation->setTextValue('nomPatient','nomPatient',$patient['nom']);
    $formConsultation->setLabel('prenomPatient','Prenom du patient');
    // liste déroulante affichant les medecins
    $formConsultation->setTextValue('prenomPatient','prenomPatient', $patient['prenom']);
    $formConsultation->setLabel('numeroSS','Numéro de sécurité social du patient');
    $formConsultation->setTextValue('numeroSS','numeroSS',$patient['numeroSS']);

    $formConsultation->setLabel('medecinConsultation','Medecin');
    $formConsultation->setSelect('medecinConsultation','medecinConsultation');
    /*************************************************************************************************************/
    //alogorithme pour mettre le medecin choisis auparavant en priorité dans la liste
    $medecinDefaut = MedecinsManager::getMedecin($_GET['id_medecin']);
    $formConsultation->setOptionSelected($_GET['id_medecin'], $medecinDefaut['nom'].' '.$medecinDefaut['prenom']);
    // on affiche tous les medecins
    foreach (MedecinsManager::getAllMedecins() as $medecin) {
        if ($_GET['id_medecin'] != $medecin['id_medecin']){
            $formConsultation->setOption($medecin['id_medecin'], $medecin['nom'] . ' ' . $medecin['prenom']);
        }
    }
/********************************************************************************************************************/
    $formConsultation->setEndSelect();
    $formConsultation->setLabel('medecin','Medecin');
    $formConsultation->setLabel('dateConsultation','Date');
    $formConsultation->setDateValue('dateConsultation','dateConsultation', $_GET['dateConsultation']);
    $formConsultation->setLabel('heureConsultation','Heure de la consultation');
    $formConsultation->setTime('heureConsultation','Heure',$_GET['heureConsultation']);
    // public function setNumber($name,$id,$min,$max,$step,$value)
    $formConsultation->setLabel('dureeConsultation','Duree de la consultation');
    $formConsultation->setNumber('dureeConsultation','dureeConsultation',15,240,15,$_GET['dureeConsultation']);
    $formConsultation->setHidden('oldId_patient',$_GET['id_patient']);
    $formConsultation->setHidden('oldId_medecin',$_GET['id_medecin']);
    $formConsultation->setHidden('oldDateConsultation', $_GET['dateConsultation']);
    $formConsultation->setHidden('oldHeureConsultation',$_GET['heureConsultation']);
    $formConsultation->setHidden('oldDureeConsultation',$_GET['dureeConsultation']);
    $formConsultation->setSubmit('modifier');
    $formConsultation->setReset('réinitialiser');
    echo $formConsultation->getForm();
}
if(isset($_SESSION)){
    unset($_SESSION['donneesConsultation']);

}

?>