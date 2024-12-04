<?php
require_once("../models/Applicant.php");
session_start(); 

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $applicantId = intval($_POST['id']);

    $applicantObj = new Applicant();

    $applicantObj->deleteApplicant($applicantId, $userId);

    header("Location: ../index.php?success=Applicant deleted successfully.");
    exit();
}
?>