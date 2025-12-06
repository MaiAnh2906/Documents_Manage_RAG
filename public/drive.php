<?php
include("../config/config.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

define('ALLOW_ACCESS', true);
$pageTitle = "My Drive";
include "navbar.php";

?>

<div class="main-container">

</div>