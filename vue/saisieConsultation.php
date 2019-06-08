<?php
require_once 'authenfication.php';
require_once 'MenuIndexModification.php';
require_once 'Formulaire.php';
require_once '../modele/UsagersManager.php';
require_once '../modele/ConsultationsManager.php';
require_once '../modele/MedecinsManager.php';

if (isset($_GET) && !empty($_GET)){

	$formConsultation = new Formulaire('../../controleur/ajoutConsultation.php','POST');
	$formConsultation->setLegend('Saisir une nouvelle Consultation');
	$formConsultation->setLabel('patient','Patient');
	//recuper les données du patient sous forme de tableau associatif
	$patient = UsagersManager::getUsager($_GET['id_patient']);
 	$formConsultation->setTextValue('patient','patient',$patient['nom'].' '.$patient['prenom']);
 	// liste déroulante affichant les medecins
    $formConsultation->setLabel('medecinConsultation','Medecin');
    $formConsultation->setSelect('medecinConsultation','medecinConsultation');
    /*************************************************************************************************************/
    //alogorithme pour mettre le medecin referent en priorité dans la liste
    // si le medecin referent existe il va prendre une valeur nulle sinon il obtient une valeur
    $idMedecinReferent= null;
    if(UsagersManager::checkReferentExistant($_GET['id_patient'])) {
        $idMedecinReferent = UsagersManager::getReferent($_GET['id_patient']);
        $medecinReferent = MedecinsManager::getMedecin($idMedecinReferent);
        $formConsultation->setOptionSelected($idMedecinReferent, $medecinReferent['nom'].' '.$medecinReferent['prenom']);
    }
// Le cas écheant on affiche tous les medecins
    foreach (MedecinsManager::getAllMedecins() as $medecin) {
        if ($idMedecinReferent != $medecin['id_medecin']){
            $formConsultation->setOption($medecin['id_medecin'], $medecin['nom'] . ' ' . $medecin['prenom']);
        }
    }
    /********************************************************************************************************************/
    $formConsultation->setEndSelect();
	$formConsultation->setLabel('dateConsultation','Date');
	$formConsultation->setDateValue('dateConsultation','dateConsultation',date('Y-m-d'));
	$formConsultation->setLabel('heureConsultation','Heure de la consultation');
	$formConsultation->setTime('heureConsultation','Heure',date('H:i'));
	// public function setNumber($name,$id,$min,$max,$step,$value)
		$formConsultation->setLabel('dureeConsultation','Duree de la consultation');
	$formConsultation->setNumber('dureeConsultation','dureeConsultation',15,240,15,30);
	$formConsultation->setHidden('id_patient',$_GET['id_patient']);
	$formConsultation->setSubmit('ajouter');
	$formConsultation->setReset('réinitialiser');
	echo $formConsultation->getForm();
}
?>