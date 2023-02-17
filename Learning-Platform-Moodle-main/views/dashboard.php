<?php
include("../includes/header.php");
include $_SERVER["DOCUMENT_ROOT"] . "/includes/auth.php";
include("../config/Connection.php");
global $conn;
global $totalFee;
if (!isset($_SESSION["userId"])) {
    Header("Location: login.php");
}
$userId = $_SESSION["userId"];
$userType = $_SESSION["userType"];

if ($userType == "student" || $userType == "admin") {
    $totalFee = 0;
    $courses = getUserCourses($conn, $_SESSION["userId"]);
}
?>
<?php
if (isset($_GET["formSubmission"])) {
    $formSubmitted = $_GET["formSubmission"];
    if ($formSubmitted === "true") {
        ?>
        <div class="form-submission-success">
            <p>Your form was successfully submitted</p>
            <p>Please wait for the course tutor to review your application</p>
        </div>
    <?php }
}
?>
<div>
</div>
<!--<div style="margin-top: 50px;">-->
<!---->
<!--    --><?php
//    // only if a student logged in the courses and courseFee pops up.
//    if ($userType == "student" || $userType == "admin"){
//    if (isset($courses) && count($courses) > 0) {
//        echo "<p>Your courses</p>";
//        foreach ($courses as $course) {
//            echo "<p>" . $course . "</p>";
//        }
//    } else { ?>
<!--        <p>No courses</p>-->
<!--    --><?php //}
//    }
//    ?>
<!--    <a href="enrollOnCourse.php" class="button">Enroll on a course</a>-->
<!---->
<!--</div>-->
<div class="intro wrapper">
    <h2>Hello <?php echo $_SESSION["name"] . " " . $_SESSION["surname"]; ?>!</h2>
    <h2>Welcome to Virtual Learning Environment by AceTraining!</h2>
</div>
<div class="wrapper dashboard-courses">
    <h1>Your courses</h1>
    <div class="grid">
        <?php
        if (isset($courses) && count($courses) > 0) {
            foreach (array_keys($courses) as $courseId) { ?>
                <div class="grid-course">
                    <h2><?php echo $courses[$courseId] ?></h2>
                    <a href=<?php echo "courses.php?courseId=$courseId&lecture=1" ?> class="button">Course page</a>
                </div>
            <?php }
        } else { ?>
            <p>No courses</p>
        <?php } ?>

    </div>
</div>
<div class="wrapper">
    <h1>Find your new learning path!</h1>
    <a href="enrollOnCourse.php" class="button" style="width: 70%; margin:0 auto;">See other courses and enroll</a>
    <h1>Announcement</h1>
    <span>
        Hey listen....<br>ACE Training is planning to launch a new course.<br>Advanced C++
    </span>
    <h1>Get to know us BETTER!</h1>
    <img src="/public/assets/school.svg" alt="">
    <a href="../views/universityFaculty.php" id="facultyLink">University faculty</a>
</div>

<?php
include "../includes/footer.php";
?>


