<?php
require_once("../models/User.php");

$user = new User();

$user->logout();

header("Location: ../login.php");
exit();
?>