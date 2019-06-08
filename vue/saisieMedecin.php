<?php
require_once 'authenfication.php';
require_once 'Menu.php';
require 'Formulaire.php';
require '../controleur/Medecin.php';
//rentrer (action,method)
$formMedecin = new Formulaire('../controleur/ajoutMedecin.php','POST');
$formMedecin->setLegend("Formulaire d'ajout d'un medecin");
$arrForNameID = Medecin::getIndicesTextuels();
$arrLabel = array('Nom','Prenom','Civilite');
for ($i = 0; $i < count($arrForNameID); $i++){
	if ($arrForNameID[$i] == 'civilite'){
		$formMedecin->setLabel('civilite','Civilite');
		$formMedecin->setSelect('civilite','civilite');
		$formMedecin->setOption(Null,'');
		$formMedecin->setOption('Monsieur','Monsieur');
		$formMedecin->setOption('Madame','Madame');
		$formMedecin->setOption('Autre','Autre');
		$formMedecin->setEndSelect();
	}else{
	//public function setLabel($for,$label)
	$formMedecin->setLabel($arrForNameID[$i],$arrLabel[$i]);
	// public function setText($name,$id)
	$formMedecin->setText($arrForNameID[$i],$arrForNameID[$i]);
	}
}

// public function setSubmit($name,$value)
$formMedecin->setSubmit('ajout');
//public function setReset($name,$value)
$formMedecin->setReset('reinitialiser');
//retourne this.form
echo($formMedecin->getForm());
//echo htmlspecialchars($formUsager->getForm());
?>
