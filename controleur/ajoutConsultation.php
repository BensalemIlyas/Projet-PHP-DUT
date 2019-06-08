<?php
if(!empty($_POST)&& isset($_POST)){
    require_once '../modele/ConsultationsManager.php';
    //ajout dans la base de données d'une consultation selon id_patient, id_medecin consultant, date, heure et duree
   if( ConsultationsManager::ajoutConsultation($_POST['id_patient'],$_POST['medecinConsultation'],$_POST['dateConsultation'],$_POST['heureConsultation'],
        $_POST['dureeConsultation'])){
       header('Refresh:2;url=../index.php');
       echo 'Votre consultation a bien été ajoutée';
   }else{
       header('Refresh:2;url=../vue/saisieConsultation.php/?id_patient='.$_POST['id_patient'].'&wish=consultation');
   }

}

?>