<?php 

require_once '../vue/Table.php';
require_once 'TableauNonAssociatifException.php';
require_once '../modele/MedecinsManager.php';

class Medecin {
	public $_nom;
	private $_prenom;
	private $_civilite;
	private $_donneesMedecin;
	// en entrée obligation de donner un tableau associatif
	function __construct($donneesMedecin)
	{
		if(! $this->checkIsAnAssociativeArray($donneesMedecin)){
			throw new TableauNonAssociatifException("Le tableau en paramètre du constructeur de la classe Medecin n'est pas un tableau associatif");
		}
		$this->hydrate($donneesMedecin);
	}
	//verifie si le tableau du constructeur est associatif
	private function checkIsAnAssociativeArray($donneesUsager){
  		return count(array_filter(array_keys($donneesUsager), 'is_string')) > 0;
	}
	public static function getIndicesTextuels (){
		return array('nom','prenom','civilite');
	} 
	public static function getIndicesUnqMedecin (){
		return array('nom','prenom');
	} 
	public function hydrate(array $donnees){
		foreach($donnees as $colonne => $valeur){
			$method = 'set'.ucfirst($colonne);
			$getMethod = 'get'.ucfirst($colonne);
			if(method_exists($this,$method)){
				$this->$method($valeur);
				$this->_donneesMedecin[$colonne] =$this->$getMethod();
				
			}
			
		}

	}
    public static function  afficherAllMedecins(){
        //recuperer les indices SQL
        $caracMedecin = Medecin::getIndicesTextuels();
        //creer un tableau html
        $tabReq  = new Table(0,2,2,'60%');
        $tabReq->trStart();
        //ligne de titre
        foreach ($caracMedecin as $indice) {
            $tabReq->celluleTitre($indice);
        }
        $tabReq->trEnd();
        //afficher le contenu du tableau
        $allMedecins =  MedecinsManager::getAllMedecins();
        while ($ligneMedecins = $allMedecins->fetch(PDO::FETCH_ASSOC) ) {
            $tabReq->trStart();
            foreach ($caracMedecin as $indice) {
                //contenu de" la cellule ex: $ligneContact['nom'] = bensalem
                $tabReq->celluleContenu($ligneMedecins[$indice]);
            }
            $id_medecin = MedecinsManager::getId($ligneMedecins['nom'],$ligneMedecins['prenom']);

                $tabReq->celluleContenu('<a href="saisieModifMedecin.php/?id_medecin='.$id_medecin.'" >modifier données du medecin</a>');
                $tabReq->celluleContenu('<a href=../controleur/supprimerMedecin.php?id_medecin='.$id_medecin.'>supprimer le medecin</a>');
            }

            $tabReq->trEnd();


        return $tabReq->getTableau();

    }

    /**
	prends en entrée le resultat de la recherche d'usagers dans la base de données
	c'est un tableau à 2dimensions $reqRecherche[lignesUsagers][nomColonneSql ou numero]

	**/
	public static function afficherTableauMedecins($reqRecherche,$demande){
			//recuperer les indices SQL 
			$caracMedecin = self::getIndicesTextuels();
			//creer un tableau html
			$tabReq  = new Table(0,2,2,'60%');
			$tabReq->trStart();
			//ligne de titre
			foreach ($caracMedecin as $indice) {
				$tabReq->celluleTitre($indice);
			}
			$tabReq->trEnd();
			//afficher le contenu du tableau
			while ($ligneContact = $reqRecherche ->fetch()) {
				$tabReq->trStart();
				foreach ($caracMedecin as $indice) {
					//contenu de la cellule ex: $ligneContact['nom'] = bensalem
					$tabReq->celluleContenu($ligneContact[$indice]);
				}

				$id_medecin = MedecinsManager::getId($ligneContact['nom'],$ligneContact['prenom']);

				if($demande[0] == 'consultation'){
					$tabReq->celluleContenu('<a href="../saisieConsultation.php/?id_patient='.$id_medecin.'&amp;id_medecin='.$demande[1].'&amp;wish=consultation" >selectionner le medecin</a>');
				}else if ($demande['0'] == 'ModifOuSupression'){
					$tabReq->celluleContenu('<a href="saisieModifMedecin.php/?id_medecin='.$id_medecin.'" >modifier données du medecin</a>');
					$tabReq->celluleContenu('<a href=../controleur/supprimerMedecin.php?id_medecin='.$id_medecin.'>supprimer le medecin</a>');
				}
				$tabReq->trEnd();
			}
			return $tabReq->getTableau();
	}
			
		/*?>

	

						<td><a href="modification.php?nom=<?php echo($ligneContact['nom'])?>&amp;prenom=<?php echo($ligneContact['prenom'])?>&amp;adresse=<?php echo($ligneContact['adresse'])?>&amp;codePostal=<?php echo ($ligneContact['codePostal'])?>&amp;ville=<?php echo($ligneContact['ville'])?>&amp;telephone=<?php echo($ligneContact['telephone'])?>" >modifier le contact</a></td>
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
	
	public function getDonneesMedecin(){
	    return $this->_donneesMedecin;
	}

	public function getTailleDonnees(){
	    return count($this->_donneesMedecin);
	}

}
	
?>