<?php
require_once('../modele/ConsultationsManager.php');
//getIndicesTextuels retourne un tableau des caractéristiques unique d'un usager(nom,prenom,numeroSS.)
$id_medecin =$_GET['id_medecin'];
$id_patient = $_GET['id_patient'];
$dateConsultation =  $_GET['dateConsultation'];
$heureConsultation = $_GET['heureConsultation'];



if (ConsultationsManager::supprimerConsultation($id_patient,$id_medecin,$dateConsultation,$heureConsultation)) {
  header('Refresh:2;url=../../vue/rechercherConsultation.php');
    echo "La consultation a bien été supprimé";
} else {
   header('Refresh:2;url=../../vue/rechercherConsultation.php');
    echo "Un problème est survenu, la consultation ne peut être supprimé";
}