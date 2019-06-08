<?php
require_once 'authenfication.php';
require_once 'Menu.php';
require_once '../controleur/Consultation.php';
echo Consultation::afficherStatistiquesUsagers();
echo Consultation::afficherNbHeuresParMedecin();
?>