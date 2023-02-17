<?php
function getUserCourses($conn, $userId): array
{
    $sql = "SELECT courseName,studentcourse.courseId FROM studentcourse INNER JOIN course ON studentcourse.courseId=course.courseId WHERE studentId=$userId AND courseApproved=1";
    $result = $conn->query($sql);
    $courses = [];
    if ($result->num_rows > 0) {
        while ($course = mysqli_fetch_assoc($result)) {
            $courses[$course["courseId"]] = $course["courseName"];
        }
    }
    return $courses;
}

function getAllLectures($conn, $courseId): array
{
    $sql = "SELECT lectureId,lectureDescription from lecture WHERE lecture.courseId=$courseId ORDER BY lecture.lectureId";
    $result = $conn->query($sql);
    $lectures = [];
    if ($result->num_rows > 0) {
        while ($lecture = $result->fetch_object()) {
            array_push($lectures, $lecture);
        }
    }
    return $lectures;
}

function getLectureResources($conn, $lectureId): array
{
    $sql = "SELECT * from lectureresource INNER JOIN file ON lectureresource.fileId=file.fileId WHERE lectureId=$lectureId";
    $result = $conn->query($sql);
    $resources = [];
    if ($result->num_rows > 0) {
        while ($file = $result->fetch_object()) {
            array_push($resources, $file);
        }
    }
    return $resources;
}

function getCourseInfo($conn, $courseId): object
{
    $sql = "SELECT courseName, courseProgramme FROM course WHERE courseId = $courseId";
    $res = $conn->query($sql);
    return $res->fetch_object();
}

function deleteResource($conn, $fileId){
    $sql = "DELETE FROM file WHERE fileId=$fileId";
    $sqlLectureResource = "DELETE FROM lectureresource WHERE fileId=$fileId";
}

function completeLecture($conn,$lectureId, $studentId,$courseId){
    // Check if lecture is already completed
    $sql = "SELECT * from studentcompletedlecture WHERE lectureId=$lectureId AND studentId=$studentId AND courseId=$courseId";
    $res = $conn->query($sql);
    if($res && $res->num_rows <= 0){
        $sql = "INSERT INTO studentcompletedlecture (lectureId,studentId,courseId) VALUES ('$lectureId','$studentId','$courseId')";
        $conn->query($sql);
    }
}

function isLectureCompleted($conn,$lectureId,$studentId,$courseId): bool{
    $sql = "SELECT * from studentcompletedlecture WHERE lectureId=$lectureId AND studentId=$studentId AND courseId=$courseId";
    $res = $conn->query($sql);
    if($res && $res->num_rows > 0) return true;
    return false;
}

function getCourseProgress($conn,$courseId,$studentId){
    $sql = "SELECT COUNT(lectureId) from studentcompletedlecture WHERE courseId=$courseId AND studentId=$studentId";
    $countStudentCompletedLecturesQuery = $conn->query($sql);
    if($countStudentCompletedLecturesQuery && $countStudentCompletedLecturesQuery->num_rows > 0){
        $studentCompletedLecture = mysqli_fetch_row($countStudentCompletedLecturesQuery)[0];
        $sql = "SELECT COUNT(lectureId) from lecture WHERE courseId=$courseId";
        $countLectureOfACourse = $conn->query($sql);
        if($countLectureOfACourse && $countLectureOfACourse->num_rows > 0){
            $numberOfLectures = mysqli_fetch_row($countLectureOfACourse)[0];
            return floor(($studentCompletedLecture * 100) / $numberOfLectures);
        }
    }
    return 0;
}
?>