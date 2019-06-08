<?php
require_once 'authenfication.php';
require_once 'MenuIndexModification.php';
require_once 'Formulaire.php';
require_once '../controleur/Medecin.php';
if( isset($_GET) && ! empty($_GET)){//cas saisie contact modifié//
	$arrDonneesMedecin = MedecinsManager::getMedecin($_GET['id_medecin']);
	$formMedecin = new Formulaire('../../controleur/modifMedecin.php','POST');
	$formMedecin->setLegend('Formulaire de modification des données personnel des medecins');
	$arrForNameID = Medecin::getIndicesTextuels();
	$arrLabel = array('Nom','Prenom','Civilite');
    $arrCivilite = array('Monsieur','Madame','Autre');
	for ($i = 0; $i < count($arrForNameID); $i++){
		//public function setLabel($for,$label)
        if ($arrForNameID[$i] == 'civilite'){
            $formMedecin->setLabel('civilite','Civilite');
            $formMedecin->setSelect('civilite','civilite');
            $formMedecin->setOptionSelected($arrDonneesMedecin[$arrForNameID[$i]],$arrDonneesMedecin[$arrForNameID[$i]]);
            foreach ($arrCivilite as $civilite ) {
                // si la civilite est differente de celle par defaut on l'ajoute
                if($civilite != $arrDonneesMedecin[$arrForNameID[$i]]){
                    $formMedecin->setOption($civilite, $civilite);
                }
            }
            $formMedecin->setEndSelect();
        }else {
            $formMedecin->setLabel($arrForNameID[$i], $arrLabel[$i]);
            $formMedecin->setTextValue($arrForNameID[$i], $arrForNameID[$i], $arrDonneesMedecin[$arrForNameID[$i]]);
        }
	}
	// public function setSubmit($name,$value)
	$formMedecin->setSubmit('Modifier');
	//public function setReset($name,$value)
	$formMedecin->setReset('reinitialiser');
	//envoie des données unique du patient non modifié.
	$formMedecin->setHidden('oldNom',$arrDonneesMedecin['nom']);
	$formMedecin->setHidden('oldPrenom',$arrDonneesMedecin['prenom']);
	//retourne this.form
	echo($formMedecin->getForm());
	//echo htmlspecialchars($formUsager->getForm());
}
?>
