<?php
class DbConnexion{
	private $_pdo;
	/*
	*/
	public function __construct(){
		//appel à la methode private pour connexion
		$this->_pdo = $this->connexion();

	}
	public function getPdo(){
		return $this->_pdo;
	}
	//Connexion avec mdp encapsulé
	private function connexion(){
		try{
			$linkpdo = new PDO("mysql:host=147.135.211.73;dbname=gestioncabinetmedical",'fistouf','toor');
		}catch(Exception $e){
			die('Erreur'.$e->getMessage());
		}
		$linkpdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		return $linkpdo;
	}
}
/*
$connexion = new DbConnexion();
$pdo = $connexion->getPdo();
$test =$pdo->query("select * from usager");
while ($data = $test->fetch()){
	echo $data[0];echo $data[1];echo $data[2];echo $data[3];
	echo $data['id_patient'];echo $data['nom'];echo $data['prenom'];echo $data['adresse'];
}*/
?>
