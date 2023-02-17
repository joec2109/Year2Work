<?php
$currentPage = explode("/", $_SERVER['SCRIPT_NAME']);
$currentPage = end($currentPage);
session_start();
include $_SERVER["DOCUMENT_ROOT"] . "/scripts/courses.php";
include $_SERVER["DOCUMENT_ROOT"] . "/config/Connection.php";
require_once "../scripts/courses.php";
global $conn;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ace training</title>
    <!-- Import fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,500;1,700&display=swap"
          rel="stylesheet">
    <!-- End of fonts imports -->
    <!-- Import CSS -->
    <link href="../public/css/styles.css" type="text/css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- End of CSS imports -->

</head>
<body>

<?php

if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION["isLoggedIn"]) && $_SESSION["isLoggedIn"] == true && isset($_SESSION["userId"])) {
    $courses = getUserCourses($conn, $_SESSION["userId"]);
}
?>

<header>
    <nav class="nav">
        <?php if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] === false || strcmp($currentPage, "login.php") == 0 || strcmp($currentPage, "signup.php") == 0) {
            ?>
            <a class="nav-item <?php if ($currentPage == "index.php") {
                echo "nav-item-active";
            } ?>" href="../views/index.php"> Homepage </a>
        <?php } else if (isset($_SESSION["isLoggedIn"]) && $_SESSION["isLoggedIn"] === true) { ?>
            <a class="nav-item <?php if ($currentPage == "dashboard.php") {
                echo "nav-item-active";
            } ?>" href="../views/dashboard.php">Dashboard</a>
            <a class="nav-item <?php if ($currentPage == "admin.php") {
                echo "nav-item-active";
            } ?>" href="../views/admin.php">Admin</a>
            <div class="nav-item-dropdown">
                    <a class="nav-item  <?php if ($currentPage == "courses.php") {
                        echo "nav-item-active";
                    } ?>" id="course-dropdown-parent" href="../views/courses.php?courseId=<?= array_keys($courses)[0] ?>&lecture=1">courses</a>
                <div class="nav-item-course-dropdown" id="course-dropdown-list">
                    <?php
                    if (isset($courses)) {
                        foreach (array_keys($courses) as $courseId) {?>
                            <a class="nav-item dropdown-nav-item"
                               href="../views/courses.php?courseId=<?php echo $courseId ?>&lecture=1"><?php echo $courses[$courseId] ?></a>
                        <?php }
                    } ?>
                </div>

            </div>
            <a class="nav-item <?php if ($currentPage == "assignments.php") {
                echo "nav-item-active";
            } ?>" href="../views/assignments.php">assignments</a>
            <a class="nav-item <?php if ($currentPage == "grades.php") {
                echo "nav-item-active";
            } ?>" href="../views/grades.php">grades</a>
            <a class="nav-item <?php if($currentPage == "profile.php") {echo "nav-item-active";}?>" href="http://localhost/Learning-Platform-Moodle/views/profile/index.php">Profile
            </a>
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        <?php } ?>
    </nav>
</header>
