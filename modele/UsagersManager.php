<?php

require_once('../modele/DbConnexion.php');
require_once('../controleur/Usager.php');
require_once('PersonneNonTrouveException.php');

class UsagersManager {
	private $_pdo;
	public function __construct (){
		 $connexion = new DbConnexion();
		 $this->_pdo = $connexion->getPdo();
	}
    public static function getAllUsagers(){

        $connexion = new DbConnexion();
        $pdo = $connexion->getPdo();
        $requeteAllUsagers= $pdo->query('select * from usager order by nom, prenom asc Limit 0,30');

        return $requeteAllUsagers;


    }
	/*fonction : affiche les usagers à partir de la base de données en fonction des mots clefs.
	prends en entrée un tableau de String $motsClefs, qui contient les mots clefs de la recherche(sans espaces(explode en amont)).
	retourne un tableau contenant les usagers correspondant à la requête avec leurs informations
	**/
	public function afficherUsagers ($PostRecherche){
		$motClefs = explode(" ",$PostRecherche);
		$requeteSearch ="select * from usager where  ";
		$compteur = 0;
		foreach ($motClefs as $mot) {
			// pour le premier mot clef il n'y a pas de OR
			if ($mot != $motClefs[0]){
				$requeteSearch .= "OR ";
			}
			/**
			chaque mot clef est rechercher dans toutes les colonnes de la table
			Usager, pour différencier les différents mots clefs, un compteur a été
			 mis en place, pour chaque itération(mot clef différent) --> motclef1,
			  motclef2 etc..**/
				$requeteSearch .= "nom like :mot".$compteur." OR prenom like :mot".$compteur." OR civilite like :mot".$compteur." OR adresse  like :mot".$compteur." OR cp  like :mot".$compteur." OR ville like :mot".$compteur."  OR dateNaissance like :mot".$compteur." OR
				lieuNaissance like :mot".$compteur." OR numeroSS like :mot".$compteur." ";

			$compteur++;
		}
		$reqRecherche = $this->_pdo->prepare($requeteSearch);
		$compteur = 0;
		/**
		pour chaque motclefi on fait $ArrayExecute = array('mot'.$0=>'%'.$motClefs[0].'%',...)
		**/
		for ($i = 0; $i < count($motClefs);$i++){
			$ArrayExecute['mot'.$i] = '%'.$motClefs[$i].'%';
		}
		$reqRecherche->execute($ArrayExecute);
		//erreur eventuelles sur la requête
		if ($reqRecherche === FALSE) {
		var_dump($linkpdo->errorInfo());
		exit();
		}
		return $reqRecherche;
	}

	public function ajoutUsager(Usager $usager){
		//verifie que l'usager n'existe pas deja dans la bd pour éviter les doublons
		if (! $this->checkUsagerExistant($usager->getNom(),$usager->getPrenom(), $usager->getNumeroSS())){
			$reqAjout = $this->_pdo->prepare("insert into usager (nom,prenom,civilite,adresse,cp,ville,numeroSS
,lieuNaissance,dateNaissance) values(:nom,:prenom, :civilite, :adresse, :cp, :ville, :numeroSS, :lieuNaissance, :dateNaissance)");

			$this->bindAllDataUsager($reqAjout,$usager);
			return $reqAjout->execute();
			if ($reqAjout === FALSE) {
				var_dump($this->_pdo->errorInfo());
				exit();
			}

		}else {
			echo "L'usager existe déjà";
		}
	}
	/** cette methode prend en entrée un objet de type Usager, qui correspond à un objet Usager modifié (par exemple dans le controleur);
	**/
	public function modifierUsager(Usager $usager,$oldNom,$oldPrenom,$oldNumeroSS){
		if (! $this->checkUsagerExistant($oldNom,$oldPrenom,$oldNumeroSS )){
			throw new PersonneNonTrouveException("L'usager n'existe pas, il ne peut donc être modifié");
		}
		$reqModif = $this->_pdo->prepare("UPDATE usager
		Set nom = :nom, prenom = :prenom, adresse = :adresse, civilite = :civilite, cp = :cp, ville =:ville, dateNaissance = :dateNaissance, lieuNaissance = :lieuNaissance, numeroSS = :numeroSS
		where nom = :oldNom and prenom = :oldPrenom and numeroSS = :oldNumeroSS");
		$this->bindAllDataUsager($reqModif,$usager);
		$reqModif->bindValue(':oldNom',$oldNom,PDO::PARAM_STR);
		$reqModif->bindValue(':oldPrenom',$oldPrenom, PDO::PARAM_STR);
		$reqModif->bindValue(':oldNumeroSS',$oldNumeroSS, PDO::PARAM_STR);

		/**
		execute() renvoie le nombre de lignes modifiées dans la table, si nbLignes >= 1 => la requête a marché
		**/
		$nbLignesSQL = $reqModif->execute();
		if($nbLignesSQL >=1){
			return TRUE;
		}
	}
	/**
	quand on utilisera supprimerUsager(), ne pas oublier de Try/catch
	prend en entrée un objet de type Usager
	//déclenche une Exception de type UsagerNonTrouve si l'usager n'existe pas dans la BD
	retourne void
	**/
	public function supprimerUsager(Usager $usager){
		if (! $this->checkUsagerExistant($usager->getNom(),$usager->getPrenom(),$usager->getNumeroSS() )){
				throw new PersonneNonTrouveException("L'usager n'existe pas, il ne peut donc être supprimé");
		}
		$reqSuppresion = $this->_pdo->prepare("delete from usager where nom = :nom AND prenom = :prenom AND numeroSS = :numeroSS");
		/*$this->bindUnqDataUsager($reqSuppresion,$usager->getNom(),$usager->getPrenom(),$usager->getNumeroSS());*/
		$reqSuppresion->execute(array('nom' =>$usager->getNom(),'prenom'=>$usager->getPrenom(), 'numeroSS'=> $usager->getNumeroSS() ));
		$reqSuppresion->execute();
		if (! $this->checkUsagerExistant($usager->getNom(),$usager->getPrenom(),$usager->getNumeroSS())){
			return TRUE;
		}else{
			return FALSE;
		}


	}

	//verifie si l'usager existe déjà pour éviter les doublons (un Usager est caractérisé par son nom, prenom et un numeroSS dans la BD)
	//retourne vrai si l'usager existe déjà et faux sinon.
	public static function checkUsagerExistant($nomUsager,$prenomUsager,$numeroSSUsager){
        $connexion = new DbConnexion();
        $pdo = $connexion->getPdo();
		$requeteVerif = $pdo->prepare("select count(*) from usager where nom =:nom and prenom = :prenom and
                numeroSS = :numeroSS");
		$requeteVerif->execute(array('nom' => $nomUsager, 'prenom' => $prenomUsager, 'numeroSS' =>$numeroSSUsager));
		if($requeteVerif->fetchColumn() >= 1){
			return TRUE ;
		}else {
			return FALSE;
		}
	}
	//prend en parametre le nom de la variable contenant la requête SQL sur laquelle on va bind les paramètres uniques de l'usager
	private function bindUnqDataUsager($nomRequete,$nomUsager,$prenomUsager,$numeroSSUsager){
		$nomRequete->bindValue(':nom',$nomUsager,PDO::PARAM_STR);
		$nomRequete->bindValue(':prenom',$prenomUsager, PDO::PARAM_STR);
		$nomRequete->bindValue(':numeroSS',$numeroSSUsager, PDO::PARAM_STR);
		return $nomRequete;
	}
	//on recupere le tableau des getters de l'objet de type Usager, et on construit l'indice fictif à partir du tableau associatif(getDonneesUsagers)
	//applique bindValue sur toutes les données de usager
	private function bindAllDataUsager($nomRequete,$usager){
		foreach ($usager->getDonneesUsager() as $colonne => $valeur){
					$nomFictif = ":$colonne";
					$nomRequete->bindValue($nomFictif, $valeur, PDO::PARAM_STR);
			}
	}
	public static function getId($nom,$prenom,$numeroSS){
		 $connexion = new DbConnexion();
		 $pdo = $connexion->getPdo();
		$requeteGetId= $pdo->prepare('select id_patient from usager where nom = :nom AND prenom = :prenom AND
			numeroSS = :numeroSS');
		$requeteGetId->bindValue(':nom',$nom,PDO::PARAM_STR);
		$requeteGetId->bindValue(':prenom',$prenom, PDO::PARAM_STR);
		$requeteGetId->bindValue(':numeroSS',$numeroSS, PDO::PARAM_STR);
		$requeteGetId->execute();
		$id = $requeteGetId->fetch();
		return $id['id_patient'];

		//$requeteGetId->execute(array('nom' =>$nom, 'prenom' =>$prenom),'numeroSS' =>$numeroSS));

	}
	//retourne les informations d'un usager sous la forme d'une tableau associatif à partir de son id
	// en entrée prends l'id de l'usagers, retourne un tableau associatif des informations de l'usager
	public static function getUsager($id){
		$connexion = new DbConnexion();
		$pdo = $connexion->getPdo();
		$requeteGetUsager= $pdo->prepare('select nom, prenom, civilite, adresse, cp, ville, numeroSS, lieuNaissance, dateNaissance from usager where id_patient = :id');
		$requeteGetUsager->execute(array('id' => $id));
		$donneesUsager = $requeteGetUsager->fetch(PDO::FETCH_ASSOC);
		return $donneesUsager;
	}
	//fonction qui permet d'ajouter un à un patient un medecin referent
	public static function ajoutReferent($id_patient,$id_medecin){
		$connexion = new DbConnexion();
		$pdo = $connexion->getPdo();
		// verifie si le patient ne possède déja pas un medecin referent
		if( ! self::checkReferentExistant($id_patient) == TRUE){
			//si c'est le cas ajoute le couple (patient,medecin)
			$reqSetReference = $pdo->prepare("insert into referent (id_patient,id_medecin) values(:id_patient,:id_medecin)");
			$reqSetReference->execute(array('id_patient'=>$id_patient, 'id_medecin' => $id_medecin));
			if($reqSetReference){
				return "Le medecin référent a été ajouté au patient </br>";
			}
		}else{
			echo "Le patient possède déjà un référent </br>";
		}
	}
	public static function checkReferentExistant($id_patient){
		$connexion = new DbConnexion();
		$pdo = $connexion->getPdo();
		$checkReferentExistant = $pdo->prepare("select count(*) as nbLignes from referent where id_patient = :id_patient");
		$checkReferentExistant->execute(array('id_patient'=>$id_patient));
		$resultCheck = $checkReferentExistant->fetch(PDO::FETCH_ASSOC);
		if($resultCheck['nbLignes'] > 0){
			return TRUE;
		}else {
			return FALSE;
		}
	}
	//renvoit l'id du medecin referent
	public static function getReferent($id_patient){
		$connexion = new DbConnexion();
		$pdo = $connexion->getPdo();
		if(self::checkReferentExistant($id_patient)){
			$getReferent = $pdo->prepare("select id_medecin from referent
            where id_patient = :id_patient");
			$getReferent->execute(array('id_patient'=>$id_patient));
			$referent= $getReferent->fetch(PDO::FETCH_ASSOC);
			return $referent['id_medecin'];

		}

	}


}

 //echo UsagersManager::UsagersParAgeEtCivilite('plusDe50','Monsieur')



?>
