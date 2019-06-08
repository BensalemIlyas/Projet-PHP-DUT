
<?php
require_once 'authenfication.php';
require_once 'MenuIndexModification.php';
require_once 'Formulaire.php';
require_once '../controleur/Usager.php';
require_once '../modele/MedecinsManager.php';
require_once '../modele/UsagersManager.php';
if( isset($_GET) && ! empty($_GET)){//cas saisie contact modifié//
	//tableau associatif  obtenu à partir de l'id patient contenant les indices textuels et les données du patient
	$arrDonneesUsager = UsagersManager::getUsager($_GET['id_patient']);
	$formUsager = new Formulaire('../../controleur/modifUsager.php','POST');
	$formUsager->setLegend('Formulaire de modification des données personnel des patient');
	$arrForNameID = array('nom','prenom','civilite','adresse','cp','ville',
'numeroSS','lieuNaissance');
	$arrLabel = array('Nom','Prenom','Civilite','Adresse','Code Postal', 'Ville', 'Numéro de sécurité sociale',
	'Lieu de naissance');
	//tableau des civilites possibles
	$arrCivilite = array('Monsieur','Madame','Autre');
	for ($i = 0; $i < count($arrForNameID); $i++){
		//public function setLabel($for,$label)
		if ($arrForNameID[$i] == 'civilite'){
			$formUsager->setLabel('civilite','Civilite');
			$formUsager->setSelect('civilite','civilite');
			//ce qui va etre afficher par defaut
			$formUsager->setOption($arrDonneesUsager[$arrForNameID[$i]],$arrDonneesUsager[$arrForNameID[$i]]);
			foreach ($arrCivilite as $civilite ) {
				// si la civilite est differente de celle par defaut on l'ajoute
				if($civilite != $arrDonneesUsager[$arrForNameID[$i]]){
					$formUsager->setOption($civilite, $civilite);
				}
			}
			$formUsager->setEndSelect();
		}else{// sinon on rajoute les autres champs qui ne sont pas des selects avec des valeurs par defaut
			$formUsager->setLabel($arrForNameID[$i],$arrLabel[$i]);
			$formUsager->setTextValue($arrForNameID[$i],$arrForNameID[$i],$arrDonneesUsager[$arrForNameID[$i]]);
		}
	}
//code pour mettre tous les medecins dans le select
$formUsager->setLabel('referent','choix du medecin referent');
$formUsager->setSelect('referent','referent');
	//mettre par defaut le medecin referent du patient
$idmedecinReferent = UsagersManager::getReferent($_GET['id_patient']);
$medecinReferent = MedecinsManager::getMedecin($idmedecinReferent);
$formUsager->setOption($idmedecinReferent,$medecinReferent['nom'].' '.$medecinReferent['prenom'] );
foreach (MedecinsManager::getAllMedecins() as $medecin){
	if($medecin['id_medecin'] != $idmedecinReferent){
		// ce qu'on montre c'est le nom du medecin, ce que j'envois c'est son ID dans le post
		$formUsager->setOption($medecin['id_medecin'], $medecin['nom'].' '.$medecin['prenom']);
	}
}
$formUsager->setEndSelect();

	$formUsager->setLabel('dateNaissance','Date de naissance');
	//$dateNaissanceObj = new dateTime($_GET['dateNaissance']);
	//$dateNaissance = $dateNaissanceObj->format('m/d/Y');
	//echo $dateNaissance;
	$formUsager->setDateValue('dateNaissance','dateNaissance',$arrDonneesUsager['dateNaissance']);
	// public function setSubmit($name,$value)
	$formUsager->setSubmit('Modifier');
	//public function setReset($name,$value)
	$formUsager->setReset('reinitialiser');
	//envoie des données unique du patient non modifié.
	$formUsager->setHidden('oldNom',$arrDonneesUsager['nom']);
	$formUsager->setHidden('oldPrenom',$arrDonneesUsager['prenom']);
	$formUsager->setHidden('oldNumeroSS',$arrDonneesUsager['numeroSS']);
	//retourne this.form
	echo($formUsager->getForm());
	//echo htmlspecialchars($formUsager->getForm());
}
?>
