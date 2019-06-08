<?php
require_once '../modele/ConsultationsManager.php';
if(UsagersManager::checkUsagerExistant($_POST['nomPatient'],$_POST['prenomPatient'],$_POST['numeroSS'])){
    $id_medecin = $_POST['medecinConsultation'];
//Pour demain : modifier le POST ['id_patient'] pour nom, prenom et numeroSS
//verifier que ce nouveau client, dans le cas où il n'existe pas rediriger vers une page de creation d'un nouveau client
// ou proposer de revenir réessayer faire 2 liens quoi.
    $id_patient = UsagersManager::getId($_POST['nomPatient'],$_POST['prenomPatient'],$_POST['numeroSS']);
    $dateConsultation = $_POST['dateConsultation'];
    $heureConsultation = $_POST['heureConsultation'];
    $dureeConsultation  =$_POST['dureeConsultation'];
    $oldId_patient = $_POST['oldId_patient'];
    $oldId_medecin = $_POST['oldId_medecin'];
    $oldDateConsultation  =$_POST['oldDateConsultation'];
    $oldHeureConsultation = $_POST['oldHeureConsultation'];
    $oldDureeConsultation = $_POST['oldDureeConsultation'];

    if(ConsultationsManager::modifierConsultation($id_patient,$id_medecin,$dateConsultation,$heureConsultation,$dureeConsultation,
        $oldId_patient,$oldId_medecin,$oldDateConsultation,$oldHeureConsultation,$oldDureeConsultation )){
        header('Refresh:2;url=../vue/rechercherConsultation.php');
        echo "La consultation a bien été modifiée \n";
    }
}else{
    header('Refresh:2;url=../vue/saisieModifConsultation.php');
    echo "Malheureusement le patient que vous venez de saisir n'existe pas";
}

?>
