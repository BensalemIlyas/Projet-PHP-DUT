<?php

require_once('../modele/DbConnexion.php');
require_once('../controleur/Medecin.php');
require_once('PersonneNonTrouveException.php');


class MedecinsManager {
	private $_pdo; 
	public function __construct (){
		 $connexion = new DbConnexion();
		 $this->_pdo = $connexion->getPdo();
	}
	/**
	fonction : affiche les usagers à partir de la base de données en fonction des mots clefs.
	prends en entrée un tableau de String $motsClefs, qui contient les mots clefs de la recherche(sans espaces(explode en amont)).
	retourne un tableau contenant les usagers correspondant à la requête avec leurs informations
	**/
	public function afficherMedecins ($PostRecherche){
		$motClefs = explode(" ",$PostRecherche);
		$requeteSearch ="select * from medecin where  ";
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
				$requeteSearch .= "nom like :mot".$compteur." OR prenom like :mot".$compteur.
				" OR civilite like :mot".$compteur." ";

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
		/*if ($reqRecherche === FALSE) {
		var_dump($linkpdo->errorInfo());
		exit();
		}*/
		return $reqRecherche;
	}

	public function ajoutMedecin(Medecin $medecin){
		//verifie que l'usager n'existe pas deja dans la bd pour éviter les doublons
		if (! $this->checkMedecinExistant($medecin->getNom(),$medecin->getPrenom())){
			$reqAjout = $this->_pdo->prepare("insert into medecin (nom,prenom,civilite) values(:nom,:prenom,:civilite)");

			$this->bindAllDataMedecin($reqAjout,$medecin);
			//$reqAjout renvoit vrai si le medecin existe
			if($reqAjout->execute()){
				return 'Le medecin a bien été ajouté';
			}else {
				return "Un problème est survenu...Le medecin ne peut être ajouté";
			}
			if ($reqAjout === FALSE) {
				var_dump($this->_pdo->errorInfo());
				exit();
			}

		}else{
			return 'Ce medecin existe déjà';
		}
	}
	/** cette methode prend en entrée un objet de type Usager, qui correspond à un objet Usager modifié (par exemple dans le controleur); 
	**/
	public function modifierMedecin(Medecin $medecin,$oldNom,$oldPrenom){
		if (! $this->checkMedecinExistant($oldNom,$oldPrenom)){
			throw new PersonneNonTrouveException("Le medecin n'existe pas, il ne peut donc être modifié");
		}
		$reqModif = $this->_pdo->prepare("UPDATE medecin
		Set nom = :nom, prenom = :prenom, civilite = :civilite 
		where nom = :oldNom and prenom = :oldPrenom");
		$this->bindAllDataMedecin($reqModif,$medecin);
		$reqModif->bindValue(':oldNom',$oldNom,PDO::PARAM_STR);
		$reqModif->bindValue(':oldPrenom',$oldPrenom, PDO::PARAM_STR);
		
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
	public function supprimerMedecin(Medecin $medecin){
		if (! $this->checkMedecinExistant($medecin->getNom(),$medecin->getPrenom())){
				throw new PersonneNonTrouveException("Le medecin n'existe pas, il ne peut donc être supprimé");
		}
		try{
            $reqSuppresion = $this->_pdo->prepare("delete from medecin where nom =:nom AND prenom =:prenom");
            $reqSuppresion->execute(array('nom' =>$medecin->getNom(),'prenom' => $medecin->getPrenom()));
        }catch (Exception $e){
            die($e->getMessage());
        }
		if (! self::checkMedecinExistant($medecin->getNom(),$medecin->getPrenom())){
			return TRUE;
		}else{
			return FALSE;
		}


	}

	//verifie si l'usager existe déjà pour éviter les doublons (un Usager est caractérisé par son nom, prenom et un numeroSS dans la BD)
	//retourne vrai si l'usager existe déjà et faux sinon.
	public static function checkMedecinExistant($nomMedecin,$prenomMedecin){
        $connexion = new DbConnexion();
        $pdo = $connexion->getPdo();
		$requeteVerif = $pdo->prepare("select count(*) from medecin where nom =:nom and prenom = :prenom");
		$requeteVerif->execute(array('nom' => $nomMedecin, 'prenom' => $prenomMedecin));
		if($requeteVerif->fetchColumn() >= 1){
			return TRUE ;
		}else {
			return FALSE;
		}
	}
	//prend en parametre le nom de la variable contenant la requête SQL sur laquelle on va bind les paramètres uniques de l'usager
	private function bindUnqDataMedecin($nomRequete,$nomMedecin,$prenomMedecin){
		$nomRequete->bindValue(':nom',$nomMedecin,PDO::PARAM_STR);
		$nomRequete->bindValue(':prenom',$prenomMedecin, PDO::PARAM_STR);
		return $nomRequete;
	}
	//on recupere le tableau des getters de l'objet de type Usager, et on construit l'indice fictif à partir du tableau associatif(getDonneesUsagers)
	//applique bindValue sur toutes les données de usager
	private function bindAllDataMedecin($nomRequete,$medecin){
		foreach ($medecin->getDonneesMedecin() as $colonne => $valeur){
					$nomFictif = ":$colonne";
					$nomRequete->bindValue($nomFictif, $valeur, PDO::PARAM_STR);
			}
	}

	public static function getId($nom,$prenom){
		 $connexion = new DbConnexion();
		 $pdo = $connexion->getPdo();	
		$requeteGetId= $pdo->prepare('select id_medecin from medecin where nom = :nom AND prenom = :prenom');
		$requeteGetId->bindValue(':nom',$nom,PDO::PARAM_STR);
		$requeteGetId->bindValue(':prenom',$prenom, PDO::PARAM_STR);
		$requeteGetId->execute();
		$id = $requeteGetId->fetch();
		return $id['id_medecin'];
		
		//$requeteGetId->execute(array('nom' =>$nom, 'prenom' =>$prenom),'numeroSS' =>$numeroSS));

	} 
	//retourne les informations d'un usager sous la forme d'une tableau associatif à partir de son id
	// en entrée prends l'id de l'usagers, retourne un tableau associatif des informations du medecin
	public static function getMedecin($id){
		$connexion = new DbConnexion();
		$pdo = $connexion->getPdo();
		$requeteGetMedecin= $pdo->prepare('select nom, prenom, civilite from medecin where id_medecin = :id');
		$requeteGetMedecin->execute(array('id' => $id));
		$donneesMedecin = $requeteGetMedecin->fetch(PDO::FETCH_ASSOC); 
		return $donneesMedecin;
	}

	public static function getAllMedecins(){

		$connexion = new DbConnexion();
		$pdo = $connexion->getPdo();
		$requeteAllMedecins= $pdo->query('select * from medecin order by nom, prenom asc ');

		return $requeteAllMedecins;
			

	}



}

?>
