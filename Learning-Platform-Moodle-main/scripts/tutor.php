<?php

function addNewTutor($conn, $postData){
    if (isset($postData["dateOfBirth"]) && isset($postData["surname"]) && isset($postData["name"]) && isset($postData["phoneNumber"]) && isset($postData["email"]) && isset($postData["address"])) {
        $name = mysqli_escape_string($conn, $postData["name"]);
        $surname = mysqli_escape_string($conn, $postData["surname"]);
        $phoneNumber = mysqli_escape_string($conn, $postData["phoneNumber"]);
        $email = mysqli_escape_string($conn, $postData["email"]);
        $address = mysqli_escape_string($conn, $postData["address"]);
        $dateOfBirth = mysqli_escape_string($conn, $postData["dateOfBirth"]);
        $password = password_hash(mysqli_escape_string($conn, $postData["password"]), PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (name,surname,phoneNumber,email,password,address,dateOfBirth,userType)
        VALUES ('$name','$surname','$phoneNumber','$email','$password','$address','$dateOfBirth','tutor');";
        if ($conn->query($sql)) {
            return false;
        } else {
            return true;
        }
    }
}
function authoriseTutor($conn){
if (isset($_POST['authoriseTutor']) && $_POST['id']){
$id = mysqli_escape_string($conn,$_POST['id']);
$userAuthorised = mysqli_escape_string($conn,$_POST['userAuthorised']);
$userAuthorised = 1;
$sql = "UPDATE user SET userAuthorised = $userAuthorised WHERE id = $id";
if(($conn)->query($sql) == True){
// adding authorised tutor to the tutor table
$sqladd = "INSERT INTO tutor (tutorId) VALUES ($id)";
if ($conn->query($sqladd) === TRUE) {
echo "Record updated successfully";
} else {
echo "Error updating record: " . $conn->error;
}
}
}
}
function rejectTutor($conn){
if(isset($_POST['rejectTutor']) && $_POST['id']){
$id = mysqli_escape_string($conn,$_POST['id']);
$sql = "DELETE FROM user where id = $id";
if ($conn->query($sql) === TRUE) {
echo "Record deleted successfully";
} else {
echo "Error updating record: " . $conn->error;
}
}
}

function enrollTutor($conn){
if (isset($_POST['enrollTutor']) && $_POST['courseEnroll']) {
$name = mysqli_escape_string($conn, $_POST['tutorName']);
$courseId = mysqli_escape_string($conn, $_POST['courseEnroll']);
$tutorId = mysqli_escape_string($conn, $_POST['tutorId']);
$contractType = $_POST['contract'];
print_r($contractType);
// enrolling each tutor to courses by the admin; many-to-many relationship
$check = "SELECT * FROM tutorcourse where tutorId  = $tutorId && courseId = $courseId";
$res = $conn ->query($check);
$sql = mysqli_fetch_array($res , MYSQLI_NUM);
if($sql > 1){
echo "<p>You have assigned ".$name.", for this course!</p>";
}
else {
$sql = "INSERT INTO tutorcourse (tutorId,courseId) VALUES ('$tutorId','$courseId')";
$res = $conn->query($sql);
if($res)
{
echo "ADDED Successfully";
$courseSalary = array();
$totalSalary = 0;

$sqlsalary = "SELECT course.courseSalary from tutorcourse INNER JOIN course on tutorcourse.courseId = course.courseId where tutorId = $tutorId ";
$resultSal = $conn->query($sqlsalary);

if ($resultSal->num_rows > 0){
$courseSalary = mysqli_fetch_all($resultSal);
}
if (count($courseSalary) > 0){
foreach($courseSalary as $salary){
$totalSalary = $totalSalary + $salary[0];
}
print_r($totalSalary);
$sql = "UPDATE tutor set salary =$totalSalary where tutorId = $tutorId ";
$result = mysqli_query($conn,$sql);
}

else {
echo "Error updating record: " . $conn->error;
}
}
else {
echo "There was an error while enrolling on to a course";
echo mysqli_error($conn);
}
}
}
}
// assigning teh course Leader
function courseLeader($conn){
if (isset($_POST['courseLeader']) && $_POST['courseEnroll']){
$forename = mysqli_escape_string($conn, $_POST['tutorName']);
$surname = mysqli_escape_string($conn, $_POST['tutorSurname']);
$courseId = mysqli_escape_string($conn, $_POST['courseEnroll']);
$tutorId = mysqli_escape_string($conn, $_POST['tutorId']);
$name = $forename.$surname;
// checks if the tutor is already enrolled to that course
$check ="SELECT * FROM tutorcourse WHERE tutorId = '$tutorId' AND courseId = '$courseId' ";
$out = $conn->query($check);
$sql = mysqli_fetch_array($out , MYSQLI_NUM);
if($sql > 1){
$sql = "UPDATE course SET courseLeader = '$name' WHERE courseId = '$courseId' ";
if(($conn->query($sql))===True){
echo $name."is the course leader for the course having id ".$courseId;
}else{
echo"Error:".$conn->error;
}
}else{
echo "You have to enroll ".$name." to this course.".$conn->error;
}
}
}