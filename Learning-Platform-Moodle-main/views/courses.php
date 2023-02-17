<?php
include("../includes/header.php");
include("../config/Connection.php");
global $conn;
$currentCourse = null;
$currentLecture = "1";
$lectures = [];
$resources = [];
$lectureResources = [];
if (isset($_GET["courseId"]) && isset($_GET["lecture"])) {
    $courseId = $_GET["courseId"];
    $currentLecture = $_GET["lecture"];
    $courseInfo = getCourseInfo($conn, $courseId);
    $currentCourse = $courseInfo->courseName;
    $lectures = getAllLectures($conn, $courseId);
    $lectureResources = getLectureResources($conn, $lectures[$currentLecture - 1]->lectureId);
    getCourseProgress($conn,$courseId,$_SESSION["userId"]);
}
if(isset($_POST["completeLecture"]) && isset($_POST["lectureId"])){
    $lectureId = $lectures[$currentLecture - 1]->lectureId;
    completeLecture($conn,$lectureId,$_SESSION["userId"],$courseId);
    echo mysqli_error($conn);
}

$isCurrentLectureCompleted = isLectureCompleted($conn,$lectures[$currentLecture - 1]->lectureId, $_SESSION["userId"],$courseId);

?>

<div class="courses-content-wrapper">
    <div class="progress-bar-wrapper">
        <h2>Course progress: <?php echo getCourseProgress($conn,$courseId,$_SESSION["userId"]) . "%"; ?></h2>
        <div class="progress-bar">
            <div class="progress-bar-inner" style="<?php echo "width:" . getCourseProgress($conn,$courseId,$_SESSION["userId"]). "%;";?>"></div>
        </div>
    </div>
    <div class="week-dropdown">
        <h1>All lectures</h1>
        <ul class="week-dropdown-list">
            <?php for ($i = 0; $i < sizeof($lectures); $i += 1) { ?>
                <li class="week-dropdown-list-item">
                    <a
                            class="<?php echo($currentLecture == ($i + 1) ? "current" : "") ?>"
                            href=<?php echo "courses.php?courseId=" . $courseId . "&lecture=" . ($i + 1) ?>>
                        <?php echo "Lecture " . ($i + 1) ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="course-content-container course-content">
        <div class="course-title-wrapper">
            <h1><?php echo $currentCourse . " Lecture" . " " . $currentLecture ?></h1>
            <form action="<?php echo "courses.php?" . "courseId=$courseId&lecture=$currentLecture"?>" method="POST" class="complete-toggle-button">
                <input type="text" hidden name="lectureId" value="<?php echo $lectures[$currentLecture - 1]->lectureId?>">
                <button type="submit" name="completeLecture" class="button-tick <?php if(isset($isCurrentLectureCompleted) && $isCurrentLectureCompleted == 1) echo "button-ticked"?>" <?php if(isset($isCurrentLectureCompleted) && $isCurrentLectureCompleted == 1) echo "disabled"?>><img src="../public/assets/tick.png" alt=""></button>
            </form>
        </div>
        <div class="lecture-description">
            <h1 class="lecture-heading">Lecture description</h1>
            <p><?php echo $lectures[$currentLecture - 1]->lectureDescription ?></p>
        </div>
        <div class="lecture-resources">
            <h1 class="lecture-heading">Resources</h1>
            <?php
            foreach ($lectureResources as $lectureResource) {
                $filename = explode("/", $lectureResource->fileName)[1];
                $fileExt = explode(".", $filename);
                $fileExt = $fileExt[sizeof($fileExt) - 1];
                $fileImg = "file.png";
                if ($fileExt == "pptx") $fileImg = "pptx.png";
                else if ($fileExt == "zip" || $fileExt == "rar") $fileImg = "folder.png";
                ?>
                <div class="file-wrapper">
                    <img src="../public/assets/<?php echo $fileImg ?>" alt="" class="file-icon">
                    <p class="file-name"><a download href="<?php echo "../" . $lectureResource->fileName ?>"><?php echo $filename; ?></a> </p>
                </div>
            <?php } ?>
        </div>

    </div>
</div>
</div>

<?php
include("../includes/footer.php");
?>

