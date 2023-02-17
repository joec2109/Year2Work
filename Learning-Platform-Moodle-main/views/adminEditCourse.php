<?php
include $_SERVER["DOCUMENT_ROOT"] . "/includes/header.php";
include $_SERVER["DOCUMENT_ROOT"] . "/includes/auth.php";
include $_SERVER["DOCUMENT_ROOT"] . "/config/Connection.php";
global $conn;

if (isset($_SESSION["userType"]) && $_SESSION["userType"] === "student") {
    echo "<div class = 'error-box'>
         <p> Unauthorised Access!</p>
         </div>;";
    die();
}
// Handles adding new lectures
if (isset($_POST["addLecture"]) && isset($_FILES["files"])) {
    $courseId = mysqli_escape_string($conn, $_POST["courseId"]);
    $lectureDescription = mysqli_escape_string($conn, $_POST["lectureDescription"]);
    $sql = "INSERT INTO lecture(courseId,lectureDescription) VALUES ('$courseId','$lectureDescription');";
    if (!$conn->query($sql)) {
        echo mysqli_error($conn);
    }
    $files = $_FILES["files"];
    $authorId = mysqli_escape_string($conn,$_POST["authorId"]);
    $lectureId = $conn->insert_id;
    $lectureDescription = mysqli_escape_string($conn,$_POST["lectureDescription"]);
    if($files["name"][0] != null) {
        for ($i = 0; $i < sizeof($files["name"]); $i++){
            $filename = $files["name"][$i];
            $tempPath = $files["tmp_name"][$i];
            $type= $files["type"][$i];
            $finalPath = "uploads/" . $filename;
            if(move_uploaded_file($tempPath, $_SERVER["DOCUMENT_ROOT"] . "/" . $finalPath)){
                $sql = "INSERT INTO file(fileName,authorId) VALUES ('$finalPath','$authorId');";
                if (!$conn->query($sql)) {
                    echo mysqli_error($conn);
                }
                $fileId = $conn->insert_id;
                $sql = "INSERT INTO lectureresource (lectureId,fileId) VALUES ('$lectureId','$fileId')";
                if (!$conn->query($sql)) {
                    echo mysqli_error($conn);
                }
            } else {
                echo "File uploading has failed.";
            }
        }
    }
}
if(isset($_POST["courseId"])){
    $courseId = $_POST["courseId"];
    $lectures = getAllLectures($conn, $courseId);
    $courseInfo = getCourseInfo($conn, $courseId);
} else {
    Header("Location: admin.php");
}
$editMode = false;
// If button in the table is clicked, edit for appears
if(isset($_POST["editLecture"])){
    $editMode = true;
    $editingLectureId = $_POST["lectureId"];
    $editingLectureNumber = $_POST["lectureNumber"];
    $editingLecture = $lectures[$editingLectureNumber - 1];
}
// Handles updating lecture
if(isset($_POST["updateLecture"]) && isset($_FILES["files"])){
//    $editMode = false;
    $files = $_FILES["files"];
    $authorId = mysqli_escape_string($conn,$_POST["authorId"]);
    $lectureId = mysqli_escape_string($conn,$_POST["lectureId"]);
    $lectureDescription = mysqli_escape_string($conn,$_POST["lectureDescription"]);
    if($files["name"][0] != null) {
        for ($i = 0; $i < sizeof($files["name"]); $i++){
            $filename = $files["name"][$i];
            $tempPath = $files["tmp_name"][$i];
            $type= $files["type"][$i];
            $finalPath = "uploads/" . $filename;
            if(move_uploaded_file($tempPath, $_SERVER["DOCUMENT_ROOT"] . "/" . $finalPath)){
                $sql = "INSERT INTO file(fileName,authorId) VALUES ('$finalPath','$authorId');";
                if (!$conn->query($sql)) {
                    echo mysqli_error($conn);
                }
                $fileId = $conn->insert_id;
                $sql = "INSERT INTO lectureresource (lectureId,fileId) VALUES ('$lectureId','$fileId')";
                if (!$conn->query($sql)) {
                    echo mysqli_error($conn);
                }
            } else {
                echo "File uploading has failed.";
            }
        }
    }
    if(isset($_POST["lectureDescription"])){
        $sql = "UPDATE lecture SET lectureDescription='$lectureDescription' WHERE lectureId=$lectureId";
        if (!$conn->query($sql)) {
            echo mysqli_error($conn);
        }
    }

}

// Handles deleting resources from lectures
if(isset($_POST["deleteResource"])){
    $fileId = $_POST["fileId"];
    $lectureId = $_POST["lectureId"];



    $sql = "DELETE FROM file WHERE fileId=$fileId";
    $conn->query($sql);
    $sql = "DELETE FROM lect;ureresource WHERE fileId=$fileId AND lectureId=$lectureId";
    $conn->query($sql);

}
?>

<div class="admin">
    <div class="page-content">
        <h1>Course: <?php echo $courseInfo->courseName ?></h1>
    </div>
    <div class="page-content">

        <h1>Add lecture</h1>
        <form action="adminEditCourse.php" method="POST" class="flex-form" style="margin-bottom: 50px;" enctype="multipart/form-data">
            <input type="text" hidden name="courseId" value="<?php echo $courseId?>">
            <input type="text" hidden name="authorId" value="<?php echo $_SESSION["userId"]?>">
            <label for="lectureDescription">Lecture Description</label>
            <textarea id="lectureDescription" name="lectureDescription" required></textarea>
            <label for="files">Upload one or more files (Hold shift to select multiple files from your disk)</label>
            <input type="file" name="files[]" id="files" multiple="multiple">
            <button type="submit" value="true" name="addLecture">Add</button>
        </form>
    </div>
    <div class="page-content">
        <h1>Lectures</h1>
        <table>
            <tr>
                <th>Lecture Number</th>
                <th>Lecture ID</th>
                <th>Resources</th>
                <th>Edit lecture</th>
            </tr>
            <?php
            $lectureCounter = 0;
            foreach ($lectures as $lecture) {
                $lectureCounter++;
                $lectureResources = getLectureResources($conn, $lecture->lectureId);
                ?>
                <tr>
                    <td><?php echo $lectureCounter ?></td>
                    <td><?php echo $lecture->lectureId ?></td>
                    <td>
                        <ul class="links-list">
                            <?php foreach ($lectureResources as $resource) {
                                $nameOfTheFile = explode("/",$resource->fileName)[1];
                                ?>
                                <li>
                                    <a download  href="<?php echo "../" . $resource->fileName?>"><?php echo $nameOfTheFile; ?></a>
                                    <form action="adminEditCourse.php" method="POST">
                                        <input type="text" hidden name="courseId" value="<?php echo $courseId?>">
                                        <input type="text" hidden name="lectureId" value="<?php echo $lecture->lectureId?>">
                                        <input type="text" hidden name="fileId" value="<?php echo $resource->fileId?>">
                                        <button type="submit" name="deleteResource" class="delete-btn">X</button>
                                    </form>
                                </li>
                            <?php } ?>
                        </ul>

                    </td>
                    <td style="padding: 10px;">
                        <form action="adminEditCourse.php" method="POST" class="flex-column">
                            <input type="text" hidden value="<?php echo $courseId;?>" name="courseId">
                            <input type="text" hidden value="<?php echo $lecture->lectureId ?>" name="lectureId">
                            <input type="text" hidden value="<?php echo $lectureCounter ?>" name="lectureNumber">
                            <button type="submit" style="margin: 0 auto;" name="editLecture">Edit</button>
                        </form>
                    </td>
                </tr>

            <?php } ?>
        </table>
    </div>
    <?php if($editMode && isset($editingLecture) && isset($editingLectureId)) { ?>
    <div class="page-content">
        <h1>Edit lecture</h1>
        <form action="adminEditCourse.php" method="POST" class="flex-form" style="margin-bottom: 50px;" enctype="multipart/form-data">
            <input type="text" hidden name="courseId" value="<?php echo $courseId?>">
            <input type="text" hidden name="authorId" value="<?php echo $_SESSION["userId"]?>">
            <input type="text" hidden name="lectureId" value="<?php echo $editingLectureId ?>">
            <label for="lectureDescription">Lecture Description</label>
            <textarea id="lectureDescription" name="lectureDescription" required><?php echo $editingLecture->lectureDescription ?></textarea>
            <label for="files">Upload one or more files (Hold shift to select multiple files from your disk)</label>
            <input type="file" name="files[]" id="files" multiple="multiple">
            <button type="submit" value="true" name="updateLecture">Add</button>
        </form>
    </div>
    <?php } ?>
</div>

