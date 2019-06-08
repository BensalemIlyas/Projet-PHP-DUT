<?php
require_once 'vue/FormulaireAuthentification.php';
if (!empty($_POST)) {
    $_SESSION['login'] = $_POST['login'];
    $_SESSION['psw'] = $_POST['psw'];
    $_SESSION['consultation']='consultation';
}
$formAuthentif = new FormulaireAuthentification('index.php', 'POST');
$formAuthentif->setLegend('Accès réservé aux personnes autorisées');
$formAuthentif->setText('login', 'Login');
$formAuthentif->setPassword('psw', 'Mot de passe');
$formAuthentif->setSubmit('Connexion');
echo $formAuthentif->getForm();


?>
<body>
<link rel="stylesheet" href="Presentation.css" />
<header>
    <nav>
        <ul class="menu">
            <li>Usagers
                <ul class="submenu">
                    <li><a href="vue/rechercherUsager.php">Rechercher un patient</a></li>

                    <li><a href="vue/saisieUsager.php">Ajouter un nouveau patient</a></li>

                </ul>
            </li>
            <li>Médecins
                <ul class="submenu">
                    <li><a href="vue/rechercherMedecin.php">Rechercher un Medecin</a></li>

                    <li><a href="vue/saisieMedecin.php">Ajouter un nouveau Medecin</a></li>

                </ul>
            </li>
            <li>Consultations
            <ul class="submenu">
                <li><a href="vue/rechercherConsultation.php">Voir les consultations</a></li>
                <li><a href="vue/rechercherModifConsultation.php"> Ajouter Une nouvelle Consultation </a></li>
            </ul>
            </li>

            <li><a href="vue/afficherStatistiques.php">Statistiques</a></li>


        </ul>

        </form>

    </nav>
</header>
