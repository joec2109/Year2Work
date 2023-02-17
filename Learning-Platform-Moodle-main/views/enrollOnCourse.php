<?php
include("../includes/header.php");
include $_SERVER["DOCUMENT_ROOT"] . "/includes/auth.php";
include("../config/Connection.php");
global $conn;
$sqlCourses = "SELECT * FROM course";
$res = $conn->query($sqlCourses);
$courses = null;
if ($res->num_rows > 0) {
    $courses = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $json = json_encode($courses);
}
if (isset($_POST["course"]) && $_POST["course"] !== null) {

    $courseId = mysqli_escape_string($conn, $_POST["course"]);
    $studentId = $_SESSION["userId"];

    // check if the user is enrolling to the same course.
    $check = "SELECT * FROM studentcourse where studentId = $studentId && courseId = $courseId";
    $res = $conn->query($check);
    $sql = mysqli_fetch_array($res, MYSQLI_NUM);
    if ($sql > 1) {
        echo "You have already submitted the application for this course!";
    } else {
        $sql = "INSERT INTO studentcourse (studentId,courseId) VALUES ('$studentId','$courseId')";
        $res = $conn->query($sql);
        if ($res) {
        } else {
            echo "There was an error while enrolling on to a course";
            echo mysqli_error($conn);
        }
    }
}
?>

<form action="enrollOnCourse.php" class="flex-form" method="POST" style="margin: 20px 0">
    <label for="course" style="font-size: 2rem">Choose a course:</label>
    <select name="course" id="course" required style="width: 50%;text-align: center;margin-top: 10px">
        <?php
        if ($courses) {
            foreach ($courses as $course) {
                $courseName = $course["courseName"];
                $courseId = $course["courseId"];
                echo "<option value='$courseId'>$courseName</option>";
            }
        } else {
            echo "No courses available";
        }
        ?>
    </select>
    <div class="course-info">
        <h2 id="course-name"></h2>
        <p id="course-programme"></p>
        <h2>Course Leader</h2>
        <h2 id="course-leader"></h2>
        <h2>Course Fee in GBP</h2>
        <h2 id = "course-fee"></h2>
    </div>
    <button type="submit">Enroll</button>
</form>

<script>
    const selectedCourse = document.getElementById("course");
    const courses = JSON.parse('<?= addslashes($json) ?>');
    let courseId = selectedCourse.value;
    let courseInfo = courses.filter(i => i.courseId === courseId)[0];
    const courseNameHeader = document.getElementById("course-name")
    const courseProgrammePTag = document.getElementById("course-programme")
    const courseLeaderPTag = document.getElementById("course-leader")
    const courseFeePTag = document.getElementById("course-fee")
    courseNameHeader.innerText = courseInfo.courseName;
    courseProgrammePTag.innerText = courseInfo.courseProgramme;
    courseLeaderPTag.innerText = courseInfo.courseLeader;
    courseFeePTag.innerText = courseInfo.courseFee;

    selectedCourse.addEventListener("change", (e) => {
        courseId = e.target.value;
        courseInfo = courses.filter(i => i.courseId === courseId)[0];
        courseNameHeader.innerText = courseInfo.courseName;
        courseProgrammePTag.innerText = courseInfo.courseProgramme;
        courseLeaderPTag.innerText = courseInfo.courseLeader;
        courseFeePTag.innerText = courseInfo.courseFee;
    })


</script>
<?php include "../includes/footer.php" ?>
