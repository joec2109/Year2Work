<?php
// to authorise the enrollment
function authoriseEnrollmentCourse($conn){
    if (isset($_POST['authorise']) && isset($_POST["course"]) && isset($_POST["courseApproved"])){
        $courseId = mysqli_escape_string($conn, $_POST["course"]);
        $studentId = mysqli_escape_string($conn, $_POST["student"]);
        $Approved = mysqli_escape_string($conn, $_POST["courseApproved"]);
        $Approved = 1;
        $sql = "UPDATE studentcourse SET courseApproved=$Approved WHERE courseId = $courseId AND studentId = $studentId ";
        if ($conn->query($sql) === TRUE) {
            echo "Record updated successfully";
            // adding the total fee for the student.
            $courseFee = array();
            $totalFee = 0;
            $sqlfee = "SELECT courseFee from studentcourse INNER JOIN course on studentcourse.courseId = course.courseId where studentId = $studentId AND courseApproved = 1 ";
            $resultFee = $conn->query($sqlfee);
            echo mysqli_error($conn);
            if ($resultFee->num_rows > 0){
                $courseFee = mysqli_fetch_all($resultFee);
            }
            if (count($courseFee) > 0){
                foreach($courseFee as $fee){
                    $totalFee = $totalFee + $fee[0];
                }
                $sql = "UPDATE student set fees =$totalFee  where studentId = $studentId ";
                $result = mysqli_query($conn,$sql);
            }}
            else {
                echo "Error updating record: " . $conn->error;
            }

        }
    }

    function rejectEnrollmentCourse($conn){
        // to reject the enrollment.
        if (isset($_POST['reject'])&& isset($_POST["course"])){
            $courseId = mysqli_escape_string($conn,$_POST["course"]);
            $studentId = mysqli_escape_string($conn,$_POST["student"]);
            $Approved = mysqli_escape_string($conn,$_POST["courseApproved"]);
            $sql = "DELETE FROM studentcourse WHERE courseId = $courseId AND studentId = $studentId ";
            if ($conn->query($sql) === TRUE) {
                echo "Record deleted successfully";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    }

    function authoriseStudent($conn){
        if (isset($_POST['authoriseStudent']) && $_POST['id']){
            $id = mysqli_escape_string($conn,$_POST['id']);
            $userAuthorised = mysqli_escape_string($conn,$_POST['userAuthorised']);
            $userAuthorised = 1;
            $sql = "UPDATE user SET userAuthorised = $userAuthorised WHERE id = $id  ";
            if ($conn->query($sql) === TRUE) {
// adding the approved students to 'student' table
                $sqladd = "INSERT INTO student (studentId) VALUES ($id)";
                if ($conn->query($sqladd) === TRUE){
                    echo "Student added to the student table successfully";
                }
            }
            else {
                echo "Error updating record: " . $conn->error;
            }


        }

    }
    function rejectStudent($conn){
        if(isset($_POST['rejectStudent']) && $_POST['id']){
            $id = mysqli_escape_string($conn,$_POST['id']);
            $sql = "DELETE FROM user where id = $id";
            if ($conn->query($sql) === TRUE) {
                echo "Record deleted successfully";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    }
