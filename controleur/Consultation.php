

<?php 
require_once '../vue/Table.php';
require_once 'TableauNonAssociatifException.php';
require_once '../modele/MedecinsManager.php';
require_once '../modele/UsagersManager.php';
require_once '../modele/ConsultationsManager.php';

class Consultation {

	private function checkIsAnAssociativeArray($donneesUsager){
  		return count(array_filter(array_keys($donneesUsager), 'is_string')) > 0;
	}
	public static function getIndicesTextuels (){
		return array('dateConsultation','heureConsultation','dureeConsultation');
	} 
//prend en paramètre d'entrée le resultat de la méthode GetConsultation de ConsultationsManager
	public static function afficherConsultation($allConsultations){
			//creer un tableau html
			$tabReq  = new Table(0,2,2,'60%');
			//ligne de titres
			$tabReq->trStart();
            $tabReq->celluleTitre('Medecin');
            $tabReq->celluleTitre('Patient');
            $tabReq->celluleTitre('Date');
            $tabReq->celluleTitre('Heure');
            $tabReq->celluleTitre('Durée');
			$tabReq->trEnd();
			//afficher le contenu du tableau
	    $arrIndicesTable = Consultation::getIndicesTextuels();
	    		//ICI il faut revoir le while. $allConsultation est un Array et on ne peut fetch() un array
	    #while ($ligneConsultation = next($allConsultations)){#->fetch()) {
	    #pourquoi faire compliqué quand on peut faire simple
    			foreach($allConsultations as $ligneConsultation){
		  	        $tabReq->trStart();
				$medecin =   MedecinsManager::getMedecin($ligneConsultation['id_medecin']);
				$patient = UsagersManager::getUsager($ligneConsultation['id_patient']);
                $tabReq->celluleContenu($medecin['nom'].' '.$medecin['prenom']);
                    $tabReq->celluleContenu($patient['nom'].' '.$patient['prenom']);
				foreach ($arrIndicesTable as $indice) {
					//contenu de la cellule ex: $ligneContact['nom'] = bensalem
					$tabReq->celluleContenu($ligneConsultation[$indice]);
				}

				$tabReq->celluleContenu('<a href="saisieModifConsultation.php/?id_patient='.$ligneConsultation['id_patient'].'&amp;id_medecin='
                    .$ligneConsultation['id_medecin'].'&amp;dateConsultation='.$ligneConsultation['dateConsultation'].'&amp;heureConsultation='.
                    $ligneConsultation['heureConsultation'].'&amp;dureeConsultation='.$ligneConsultation['dureeConsultation'].'&amp;demande=modifConsultation" >Modifier la consultation</a>');
				$tabReq->celluleContenu('<a href="../controleur/supprimerConsultation.php/?id_patient='.$ligneConsultation['id_patient'].'&amp;id_medecin='
                    .$ligneConsultation['id_medecin'].'&amp;dateConsultation='.$ligneConsultation['dateConsultation'].'&amp;heureConsultation='.
                    $ligneConsultation['heureConsultation'].'" >Supprimer la consultation</a>');
				//on mettra ici les liens

				$tabReq->trEnd();
			}
			return $tabReq->getTableau();
	}
//'moinsDe25';'entre25Et50';'plusDe50' ''Monsieur', 'Madame'
	public static function afficherStatistiquesUsagers(){
	    $arrAge = array('moinsDe25','entre25Et50','plusDe50');
	    $arrCivilite = array('Monsieur', 'Madame');
	    $arrIntitule = array('Moins de 25ans', 'Entre 25 et 50ans', 'Plus de 50ans');
        $tabStats  = new Table(0,2,2,'60%');
        $tabStats->trStart();
        $tabStats->celluleTitre('Tranche d\'âge');
        $tabStats->celluleTitre('Nb Homme');
        $tabStats->celluleTitre('Nb Femme');
        $tabStats->trEnd();
        for ($i = 0; $i < count($arrAge); $i++){
            $tabStats->trStart();
            $tabStats->celluleContenu($arrIntitule[$i]);
            for ($j = 0; $j < count($arrCivilite); $j++){
                $tabStats->celluleContenu(ConsultationsManager::UsagersParAgeEtCivilite($arrAge[$i],$arrCivilite[$j]));
            }
            $tabStats->trEnd();
        }
        return $tabStats->getTableau();
    }
    public static function afficherNbHeuresParMedecin(){
        $tabNbHeuresParMedecin  = new Table(0,2,2,'60%');
        $tabNbHeuresParMedecin->trStart();
        $tabNbHeuresParMedecin->celluleTitre('medecin');
        $tabNbHeuresParMedecin->celluleTitre('Nb heures');
        $tabNbHeuresParMedecin->trEnd();
        $dataReq = ConsultationsManager::nbHeuresHeuresConsultationByMedecin();
        while ( $ligneMedecin = $dataReq->fetch() ) {
           $tabNbHeuresParMedecin->trStart();
           $medecin = MedecinsManager::getMedecin($ligneMedecin['medecin']);
            $tabNbHeuresParMedecin->celluleContenu($medecin['nom'] . ' ' . $medecin['prenom']);
            $tabNbHeuresParMedecin->celluleContenu($ligneMedecin['nbHeures']);
           $tabNbHeuresParMedecin->trEnd();
        }
        return $tabNbHeuresParMedecin->getTableau();

    }
			


}

	
?>
