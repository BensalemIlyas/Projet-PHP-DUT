<?php
	require_once('../modele/DbConnexion.php');
	require_once ('MedecinsManager.php');
	require_once ('UsagersManager.php');
class ConsultationsManager{
	
	public static function getReferent($id_patient){
		$connexion = new DbConnexion();
		$pdo = $connexion->getPdo();
		$reqGetReferent= $pdo->prepare("select id_medecin, count(*) AS nbLignes from REFERENT where id_patient = :id_patient ");
		$reqGetReferent->bindValue(':id_patient',$id_patient);
		$reqGetReferent->execute();
		$donneesReferent=$reqGetReferent->fetch();
	    $idReferent = $donneesReferent['id_medecin'];
		$nbLignes =  $donneesReferent['nbLignes'];
		if ($nbLignes >  0){
			return $idReferent ;
		}else {
			return 'medecinNotFound';
		}
	}
	public static function ajoutConsultation($id_patient,$id_medecin,$dateConsultation,$heureConsultation,$dureeConsultation){
	    if (self::verifChevauchementConsultationParMedecin($dateConsultation,$heureConsultation,$dureeConsultation,$id_medecin) == FALSE){
            $connexion = new DbConnexion();
            $pdo = $connexion->getPdo();
            $requeteAjoutConsultation = $pdo->prepare('insert into CONSULTATION (id_patient,id_medecin,dateConsultation,
            heureConsultation,dureeConsultation) values (:id_patient,:id_medecin,:dateConsultation,:heureConsultation,:dureeConsultation)');
            /*test : insert into consultation (id_patient,id_medecin,dateConsultation,heureConsultation,dureeConsultation)
            values (2,2,'1990-03-02','12:20',30);*/
            $requeteAjoutConsultation->bindValue(':id_patient',$id_patient,PDO::PARAM_STR);
            $requeteAjoutConsultation->bindValue(':id_medecin',$id_medecin,PDO::PARAM_STR);
            $requeteAjoutConsultation->bindValue(':dateConsultation',$dateConsultation,PDO::PARAM_STR);
            $requeteAjoutConsultation->bindValue(':heureConsultation',$heureConsultation, PDO::PARAM_STR);
            $requeteAjoutConsultation->bindValue('dureeConsultation',$dureeConsultation,PDO::PARAM_INT);
            $requeteFonctionne = $requeteAjoutConsultation->execute();
            return $requeteFonctionne;
        }else{
	        echo 'Les horaires se chevauchent, ajout impossible';
        }


    }
    public static function modifierConsultation($id_patient,$id_medecin,$dateConsultation,$heureConsultation,$dureeConsultation,
                                         $oldId_patient,$oldId_medecin,$oldDateConsultation,$oldHeureConsultation,$oldDureeConsultation  )
    {
        ConsultationsManager::supprimerConsultation($oldId_patient,$oldId_medecin,$oldDateConsultation,$oldHeureConsultation);
        if ( self::verifChevauchementConsultationModificationParMedecin($dateConsultation,$heureConsultation,$dureeConsultation,$id_medecin)== FALSE){
            $medecin  = MedecinsManager::getMedecin($oldId_medecin);
            $patient = UsagersManager::getUsager($oldId_patient);
            if (MedecinsManager::checkMedecinExistant($medecin['nom'],$medecin['prenom'] ) == true AND
                UsagersManager::checkUsagerExistant($patient['nom'],$patient['prenom'], $patient['numeroSS'] ) == true){
                /*
    if (! $this->checkMedecinExistant($oldNom,$oldPrenom)){
        throw new PersonneNonTrouveException("Le medecin n'existe pas, il ne peut donc être modifié");
    }*/
                $connexion = new DbConnexion();
                $pdo = $connexion->getPdo();
                $reqModif = $pdo->prepare("UPDATE consultation
		    Set id_patient = :id_patient, id_medecin = :id_medecin, dateConsultation = :dateConsultation,
		    heureConsultation = :heureConsultation, dureeConsultation = :dureeConsultation  
		    where id_patient = :oldId_patient and id_medecin = :oldId_medecin and dateConsultation = :oldDateConsultation
		    and heureConsultation = :oldHeureConsultation");
                $nbLignesSql = $reqModif->execute(array('id_patient' => $id_patient, 'id_medecin' => $id_medecin, 'dateConsultation' =>
                    $dateConsultation, 'heureConsultation' => $heureConsultation, 'dureeConsultation' => $dureeConsultation, 'oldId_patient'
                => $oldId_patient, 'oldId_medecin' => $oldId_medecin, 'oldDateConsultation' => $oldDateConsultation,
                    'oldHeureConsultation' => $oldHeureConsultation));
                // si ce qui est retourné est supérieur a 0 c'est que ça a marché
                if ($nbLignesSql > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        }else {
            echo 'les horaires se chevauchent, modification impossible';
            ConsultationsManager::ajoutConsultation( $oldId_patient,$oldId_medecin,$oldDateConsultation,$oldHeureConsultation,$oldDureeConsultation);
        }

    }

    public static function supprimerConsultation($id_patient,$id_medecin,$dateConsultation,$heureConsultation){
        $connexion = new DbConnexion();
        $pdo = $connexion->getPdo();
	    $reqSupprimerConsultation = $pdo->prepare('delete from CONSULTATION where id_patient = :id_patient AND id_medecin =
            :id_medecin AND dateConsultation = :dateConsultation AND heureConsultation = :heureConsultation');
	    $reqSupprimerConsultation->execute(array('id_patient' =>$id_patient, 'id_medecin'=> $id_medecin, 'dateConsultation'
	    => $dateConsultation,'heureConsultation' => $heureConsultation ));
	    return $reqSupprimerConsultation;
    }
    public static  function getAllIdMedecinsConsultation(){

        $connexion = new DbConnexion();
        $pdo = $connexion->getPdo();
        //requête synchronisée pour jouer, sinon  équivaut à select * from consultation
        /*$getAllIdMedecinsConsultation = ('select id_medecin from MEDECIN
                    where id_medecin in (select id_medecin from CONSULTATION)
		    order by nom, prenom');*/
	$getAllIdMedecinsConsultation = ('SELECT DISTINCT id_medecin FROM consultation');
	$allIdsMedecin = $pdo->query($getAllIdMedecinsConsultation);
	$res = $allIdsMedecin->fetchAll();
        return $allIdsMedecin;
    }
    public static  function getConsultation(){
	    //Enzo: ce truc renvoie un boolean
        $connexion = new DbConnexion();
        $pdo = $connexion->getPdo();
        //requête synchronisée affiche toutes les consultaitons.
        $requeteGetConsultation = 'select id_patient, id_medecin, dateConsultation, heureConsultation,dureeConsultation 
        from consultation where EXISTS(select usager.nom from usager where consultation.id_patient = usager.id_patient
            UNION
        select medecin.nom from medecin where consultation.id_medecin = medecin.id_medecin)order by dateConsultation DESC,
         heureConsultation ASC';
	$tableConsultation = $pdo->query($requeteGetConsultation);
        return $tableConsultation->fetchAll();
    }
    public static function getConsultationsByMedecin($id_medecin){
        $connexion = new DbConnexion();
        $pdo = $connexion->getPdo();
        $requetegetConsultationsByMedecin= $pdo->prepare( 'select * from consultation where id_medecin = :id_medecin');
        $requetegetConsultationsByMedecin->execute(array('id_medecin'=>$id_medecin));
        return $requetegetConsultationsByMedecin;

    }
    public static function  UsagersParAgeEtCivilite($age,$civilite) {
        $connexion = new DbConnexion();
        $pdo = $connexion->getPdo();

        /*$requetegeStatistiqueUsager = 'select distinctid_patient from consultation where
            id_patient in (select id_patient from Usager where civilite = :civilite AND
              datediff(CURRENT_DATE,Usager.dateNaissance)/365.25';*/
        $requetegeStatistiqueUsager = 'select count(*) as stat from usager where
            id_patient in (select distinct id_patient from consultation) AND civilite = :civilite AND 
              datediff(CURRENT_DATE,usager.dateNaissance)/365.25';
        switch ($age) {
            case 'moinsDe25' :
                $requetegeStatistiqueUsager .= ' < 25';
                break;
            case 'entre25Et50' :
                $requetegeStatistiqueUsager .= ' BETWEEN 25 AND 50';
                break;
            case 'plusDe50':

                $requetegeStatistiqueUsager .= ' > 50';
        }
        $requeteStatistiquesConsultationUsager = $pdo->prepare($requetegeStatistiqueUsager);
        $requeteStatistiquesConsultationUsager->execute(array('civilite' => $civilite));
        return $requeteStatistiquesConsultationUsager->fetch()['stat'];
    }

    public static function nbHeuresHeuresConsultationByMedecin(){
        $connexion = new DbConnexion();
        $pdo = $connexion->getPdo();
        $requetenbHeuresByMedecin = $pdo->query('select id_medecin as medecin, SUM(round(dureeConsultation/60,2)) 
            as nbHeures from consultation group by id_medecin');
        return $requetenbHeuresByMedecin;
    }
    public static function verifChevauchementConsultationParMedecin($dateConsultation,$heureConsultation,$dureeConsultation,$id_medecin){
        $connexion = new DbConnexion();
        $pdo = $connexion->getPdo();
        $requeteVerifChevauchement = $pdo->prepare("select count(*) as nbChevauchements  from consultation c1
	where EXISTS ( select * from consultation as  c2
                  where c1.dateConsultation = c2.dateConsultation and c1.heureConsultation = c2.heureConsultation 
                 and ABS ( TIMESTAMPDIFF( SECOND ,concat(c2.dateConsultation,' ',c2.heureConsultation) , concat(:dateConsultation,' ',:heureConsultation) ) ) / 60 < :dureeConsultation 
        ) AND id_medecin = :id_medecin");
       $requeteVerifChevauchement->execute(array('dateConsultation' =>$dateConsultation, 'heureConsultation' =>$heureConsultation,'dureeConsultation' =>$dureeConsultation,
            'id_medecin'=>$id_medecin));
        return  $requeteVerifChevauchement->fetch()['nbChevauchements'];
     }
    public static function verifChevauchementConsultationModificationParMedecin($dateConsultation,$heureConsultation,$dureeConsultation,$id_medecin){
        $connexion = new DbConnexion();
        $pdo = $connexion->getPdo();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $maxHeureConsultationInferieurANewHeureConsultation = $pdo->prepare("select heureConsultation, dureeConsultation from CONSULTATION 
   where heureConsultation = (select max(heureConsultation) from consultation where heureConsultation <= :heureConsultation AND id_medecin = :id_medecin and dateConsultation = :dateConsultation )");

        $maxHeureConsultationInferieurANewHeureConsultation->execute(array('dateConsultation' =>$dateConsultation, 'heureConsultation' =>$heureConsultation,
            'id_medecin'=>$id_medecin));
        if($maxHeureConsultationInferieurANewHeureConsultation->fetch() == true) {
            $infHoraire = $maxHeureConsultationInferieurANewHeureConsultation->fetch();
            //var_dump($infHoraire);
            // var_dump( date("H:i:s", time ($infHoraire['heureConsultation']) ));
            // Test si la nouvelle houraire commence pendant l'ancienne

            if (strtotime($heureConsultation) < (strtotime($infHoraire['heureConsultation']) + ($infHoraire['dureeConsultation']) * 60)) {

                return true;
            }
        }
        $minHeureConsultationSuperieurANewHeureConsultation = $pdo->prepare("select heureConsultation, dureeConsultation from CONSULTATION 
   where heureConsultation = (select min(heureConsultation) from CONSULTATION where heureConsultation >= :heureConsultation AND id_medecin = :id_medecin and dateConsultation = :dateConsultation )");

        $minHeureConsultationSuperieurANewHeureConsultation->execute(array('dateConsultation' =>$dateConsultation, 'heureConsultation' =>$heureConsultation,
            'id_medecin'=>$id_medecin));
        //$linkpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if($minHeureConsultationSuperieurANewHeureConsultation->fetch() == true) {
            $supHoraire = $minHeureConsultationSuperieurANewHeureConsultation->fetch();
            //var_dump($supHoraire);
            if (strtotime($supHoraire['heureConsultation']) < (strtotime($heureConsultation) + ($dureeConsultation) * 60)) {
                //echo strtotime($supHoraire['heureConsultation']) . '</br>';
                //echo strtotime($heureConsultation);
                return true;
            }
        }

    return false;
    }

}

//var_dump(ConsultationsManager::verifChevauchementConsultationParMedecin('2019-06-17','20:49:00', 30, 14));
?>
