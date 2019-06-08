<?php 
require_once '../vue/Table.php';
require_once 'TableauNonAssociatifException.php';
require_once '../modele/UsagersManager.php';
require_once '../modele/MedecinsManager.php';

class Usager {
	public $_nom;
	private $_prenom;
	private $_civilite;
	private $_adresse;
	private $_cp;
	private $_ville;
	private $_numeroSS;
	private $_lieuNaissance;
	private $_dateNaissance;
	private $_donneesUsager;
	// en entrée obligation de donner un tableau associatif
	function __construct($donneesUsager)
	{
		if(! $this->checkIsAnAssociativeArray($donneesUsager)){
			throw new TableauNonAssociatifException("Le tableau en paramètre du constructeur de la classe Usager n'est pas un tableau associatif");
		}
		$this->hydrate($donneesUsager);
	}
	//verifie si le tableau du constructeur est associatif
	private function checkIsAnAssociativeArray($donneesUsager){
  		return count(array_filter(array_keys($donneesUsager), 'is_string')) > 0;
	}
	public static function getIndicesTextuels (){
		return array('nom','prenom','civilite','adresse','cp','ville','numeroSS','lieuNaissance','dateNaissance');
	} 
	public static function getIndicesUnqUsager (){
		return array('nom','prenom','numeroSS');
	} 
	public function hydrate(array $donnees){
		foreach($donnees as $colonne => $valeur){
			$method = 'set'.ucfirst($colonne);
			$getMethod = 'get'.ucfirst($colonne);
			if(method_exists($this,$method)){
				$this->$method($valeur);
				$this->_donneesUsager[$colonne] =$this->$getMethod();
				
			}
			
		}

	}
	public static function  afficherAllUsagers($demande){
        //recuperer les indices SQL
        $caracUsager = Usager::getIndicesTextuels();
        //creer un tableau html
        $tabReq  = new Table(0,2,2,'60%');
        $tabReq->trStart();
        //ligne de titre
        foreach ($caracUsager as $indice) {
            $tabReq->celluleTitre($indice);
        }
        $tabReq->celluleTitre('Medecin référent');
        $tabReq->trEnd();
        //afficher le contenu du tableau
       $allPatients =  UsagersManager::getAllUsagers();
        while ($ligneUsagers = $allPatients->fetch(PDO::FETCH_ASSOC) ) {
            $tabReq->trStart();
            foreach ($caracUsager as $indice) {
                //contenu de" la cellule ex: $ligneContact['nom'] = bensalem
                $tabReq->celluleContenu($ligneUsagers[$indice]);
            }
            $id_patient = UsagersManager::getId($ligneUsagers['nom'],$ligneUsagers['prenom'], $ligneUsagers['numeroSS']);
            $id_medecin = UsagersManager::getReferent($id_patient);
            $medecin = UsagersManager::getReferent($id_medecin);
            $tabReq->celluleContenu($medecin['nom'].' '.$medecin['prenom']);
            switch ($demande){

                case 'ModifOuSupression':
                    $tabReq->celluleContenu('<a href="saisieModifUsager.php/?id_patient='.$id_patient.'" >modifier le contact</a>');
                    $tabReq->celluleContenu('<a href=../controleur/supprimerUsager.php?id_patient='.$id_patient.'>supprimer le contact</a>');
                    break;
                case 'consultation':
                    $tabReq->celluleContenu('<a href="saisieConsultation.php/?id_patient='.$id_patient.'&amp;wish=consultation" >selectionner ce patient</a>');
            }
            $tabReq->trEnd();

        }
        return $tabReq->getTableau();

    }

	/**
	prends en entrée le resultat de la recherche d'usagers dans la base de données
	c'est un tableau à 2dimensions $reqRecherche[lignesUsagers][nomColonneSql ou numero]

	**/
	public static function afficherTableauUsagers($reqRecherche,$demande){
	    /*
        $_SESSION['donneesConsultation'] = array('id_patient' =>$_GET['id_patient'],'id_medecin' =>$_GET['id_medecin'],'dateConsultation' => $_GET['dateConsultation'],
            'heureConsultation' => $_GET['heureConsultation'], 'dureeConsultation' => $_GET['dureeConsultation']);
        echo $_SESSION['donneesConsultation']['id_patient'];*/
	//recuperer les indices SQL
			$caracUsager = Usager::getIndicesTextuels();
			//creer un tableau html
			$tabReq  = new Table(0,2,2,'60%');
			$tabReq->trStart();
			//ligne de titre
			foreach ($caracUsager as $indice) {
				$tabReq->celluleTitre($indice);
			}
            $tabReq->celluleTitre('Medecin référent');
            $tabReq->trEnd();
			//afficher le contenu du tableau
			while ($ligneContact = $reqRecherche ->fetch()) {
				$tabReq->trStart();
				foreach ($caracUsager as $indice) {
					//contenu de" la cellule ex: $ligneContact['nom'] = bensalem
					$tabReq->celluleContenu($ligneContact[$indice]);
				}
				$id_patient = UsagersManager::getId($ligneContact['nom'],$ligneContact['prenom'], $ligneContact['numeroSS']);
                $id_medecin = UsagersManager::getReferent($id_patient);
                $medecin = MedecinsManager::getMedecin($id_medecin);
                $tabReq->celluleContenu($medecin['nom'].' '.$medecin['prenom']);
				switch ($demande){

                    case 'ModifOuSupression':
                        $tabReq->celluleContenu('<a href="saisieModifUsager.php/?id_patient='.$id_patient.'" >modifier le contact</a>');
                        $tabReq->celluleContenu('<a href=../controleur/supprimerUsager.php?id_patient='.$id_patient.'>supprimer le contact</a>');
                        break;
                    case 'consultation':
                        $tabReq->celluleContenu('<a href="saisieConsultation.php/?id_patient='.$id_patient.'&amp;wish=consultation" >selectionner ce patient</a>');
                }
				$tabReq->trEnd();
			    
			}
			return $tabReq->getTableau();

	}

		/*
		 * <td><a href="modification.php?nom=<?php echo($ligneContact['nom'])?>&amp;prenom=<?php echo($ligneContact['prenom'])?>&amp;adresse=<?php echo($ligneContact['adresse'])?>&amp;codePostal=<?php echo ($ligneContact['codePostal'])?>&amp;ville=<?php echo($ligneContact['ville'])?>&amp;telephone=<?php echo($ligneContact['telephone'])?>" >modifier le contact</a></td>
		<td><a href="suppression.php?nom=<?php echo($ligneContact['nom'])?>&amp;prenom=<?php echo($ligneContact['prenom'])?>&amp;adresse=<?php echo($ligneContact['adresse'])?>&amp;codePostal=<?php echo ($ligneContact['codePostal'])?>&amp;ville=<?php echo($ligneContact['ville'])?>&amp;telephone=<?php echo($ligneContact['telephone'])?>">supprimer le contact</a></td>-->*/

	public function getNom(){
		return $this->_nom;
	}
	public function getPrenom(){
		return $this->_prenom;
	}
		public function getCivilite(){
		return $this->_civilite;
	}
		public function getAdresse(){
		return $this->_adresse;
	}
		public function getCp(){
		return $this->_cp;
	}
		public function getVille(){
		return $this->_ville;
	}
		public function getNumeroSS(){
		return $this->_numeroSS;
	}
		public function getLieuNaissance(){
		return $this->_lieuNaissance;
	}
		public function getDateNaissance(){
		return $this->_dateNaissance;
	}
		public function setNom($nom){
			if(is_string($nom)){
				$this->_nom = $nom;
			}
	}
	public function setPrenom($prenom){
		if(is_string($prenom)){
				$this->_prenom = $prenom;
			}
	}
		public function setCivilite($civilite){
		if(is_string($civilite)){
				$this->_civilite = $civilite;
			}
	}
		public function setAdresse($adresse){
		if(is_string($adresse)){
				$this->_adresse = $adresse;
			}
	}
		public function setCp($cp){
		if(is_string($cp)){
				$this->_cp = $cp;
			}
	}
		public function setVille($ville){
		if(is_string($ville)){
				$this->_ville = $ville;
			}
	}
		public function setNumeroSS($numeroSS){
		if(is_string($numeroSS)){
				$this->_numeroSS = $numeroSS;
			}
	}
		public function setLieuNaissance($lieuNaissance){
		if(is_string($lieuNaissance)){
				$this->_lieuNaissance = $lieuNaissance;
			}
	}
		public function setDateNaissance($dateNaissance){
		if(is_string($dateNaissance)){
			//MySQL n'accepte pas les chaines de caractères vides(champ date formulaire non renseigné), il faut rentrer null manuellement. 
			if($dateNaissance == ''){
				$dateNaissance = NULL;
			}
				$this->_dateNaissance = $dateNaissance;
		}
	}
	    public function getDonneesUsager(){
	    	return $this->_donneesUsager;
	    }
	    public function getTailleDonnees(){
	    	return count($this->_donneesUsager);
	    }

}
	
 ?>