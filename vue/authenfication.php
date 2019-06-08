<?php
session_start();
define('LOGIN','ilyas');
define('PSW','bensalem');
if ($_SESSION['login'] !== LOGIN OR $_SESSION['psw'] !==PSW ){
  header('Location: ../index.php' );
}



?>
