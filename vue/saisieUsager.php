<?php
require_once 'authenfication.php';
require_once 'Menu.php';
require_once 'Formulaire.php';
require_once 'Select.php';
require_once '../modele/MedecinsManager.php';
//rentrer (action,method)
$formConsultation = new Formulaire('../controleur/ajoutUsager.php','POST');
$formConsultation->setLegend("Formulaire d'ajout d'un patient");
$arrForNameID = array('nom','prenom','civilite','adresse','cp','ville',
'numeroSS','lieuNaissance');
$arrLabel = array('Nom','Prenom','Civilite','Adresse','Code Postal', 'Ville', 'Numéro de sécurité sociale',
 'Lieu de naissance');
for ($i = 0; $i < count($arrForNameID); $i++){
	if ($arrForNameID[$i] == 'civilite'){
		$formConsultation->setLabel('civilite','Civilite');
		$formConsultation->setSelect('civilite','civilite');
		$formConsultation->setOption(Null, '');
		$formConsultation->setOption('Monsieur', 'Monsieur');
		$formConsultation->setOption('Madame', 'Madame');
		$formConsultation->setOption('Autre', 'Autre');
		$formConsultation->setEndSelect();
	}else{
		//public function setLabel($for,$label)
		$formConsultation->setLabel($arrForNameID[$i],$arrLabel[$i]);
		// public function setText($name,$id)
		$formConsultation->setText($arrForNameID[$i],$arrForNameID[$i]);
	}
}
//code pour mettre tous les medecins dans le select
$formConsultation->setLabel('referent','choix du medecin referent');
$formConsultation->setSelect('referent','referent');
$formConsultation->setOption(NULL, 'Aucun');
foreach (MedecinsManager::getAllMedecins() as $medecin){
	// ce qu'on montre c'est le nom du medecin, ce que j'envois c'est son ID dans le post
	$formConsultation->setOption($medecin['id_medecin'], $medecin['nom'].' '.$medecin['prenom']);

}
$formConsultation->setEndSelect();

$formConsultation->setLabel('dateNaissance','Date de naissance');
$formConsultation->setDate('dateNaissance','dateNaissance');
// public function setSubmit($name,$value)
$formConsultation->setSubmit('ajout');
//public function setReset($name,$value)
$formConsultation->setReset('reinitialiser');
//retourne this.form
echo($formConsultation->getForm());
//echo htmlspecialchars($formUsager->getForm());



/*alter Table consultation add constraint FK_IdPatient FOREIGN KEY (id_patient) REFERENCES usager(id_patient)

alter Table consultation add constraint FK_IdMedecin FOREIGN KEY (id_medecin) REFERENCES medecin(id_medecin)

alter Table referent add constraint FK_IdPatient FOREIGN KEY (id_patient) REFERENCES usager(id_patient);

alter Table referent add constraint FK_IdMedecin FOREIGN KEY (id_medecin) REFERENCES medecin(id_medecin)
*/

?>
