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
    $id = intval($_POST['id']);
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email_address = $_POST['email_address'];
    $phone_number = $_POST['phone_number'];
    $applied_position = $_POST['applied_position'];
    $start_date = $_POST['start_date'];
    $address = $_POST['address'];
    $nationality = $_POST['nationality'];

    $applicantObj = new Applicant();

    $applicantObj->updateApplicant($id, $username, $first_name, $last_name, $email_address, $phone_number, $applied_position, $start_date, $address, $nationality, $userId);

    header("Location: ../index.php?success=Applicant updated successfully.");
    exit();
}
?>