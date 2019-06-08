<?php
require_once 'authenfication.php';
require_once 'Menu.php';
require_once '../controleur/Consultation.php';
?>
</br></br></br>
<H1> Statistiques des patients ayant déjà pris un rendez vous avec un medecin </H1>
<?php
echo Consultation::afficherStatistiquesUsagers();
?>
<H1> Statistiques relatives aux consultations pour chaque medecin </H1>
<?php
echo Consultation::afficherNbHeuresParMedecin();
?>
