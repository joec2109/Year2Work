<?php
include $_SERVER["DOCUMENT_ROOT"] . "/includes/header.php";
include $_SERVER["DOCUMENT_ROOT"] . "/includes/auth.php";
include $_SERVER["DOCUMENT_ROOT"] . "/scripts/student.php";
include $_SERVER["DOCUMENT_ROOT"] . "/scripts/tutor.php";
include $_SERVER["DOCUMENT_ROOT"] . "/scripts/restriction.php";
include $_SERVER["DOCUMENT_ROOT"] . "/config/Connection.php";
include "../scripts/forms.php";
global $conn;
global $resultingTutor;
global $courses;
global $ans;

if (isset($_SESSION["userType"]) && $_SESSION["userType"] === "student") {
    echo "<div class = 'error-box'>
         <p> Unauthorised Access!</p>
         </div>";
    die();
}
// gets the courses done by the logged tutor
if ($_SESSION["userType"] === "tutor") {
    $userId = $_SESSION["userId"];
    $userType = $_SESSION["userType"];

    $sql = "SELECT course.courseName,course.courseId FROM tutorcourse INNER JOIN course ON tutorcourse.courseId = course.courseId WHERE tutorcourse.tutorId = $userId";
    $resultingTutor = $conn->query($sql);
    if ($resultingTutor) {
        $resultingTutor = mysqli_fetch_all($resultingTutor);
    }
}

// get the course details
$sql = "SELECT * from course";
$courses = $conn->query($sql);
if ($courses->num_rows > 0) {
    $courses = mysqli_fetch_all($courses);
}

$formError = false;

?>
<div class="admin">
    <?php if ($_SESSION["userType"] === "admin") { ?>
        <h1 class="page-header"> AceTraining Learning Platform Administrator Panel</h1>
        <?php
    } else {
        echo "<h1> Welcome " . $_SESSION['name'] . " to tutors portal</h1>";
    }
    ?>
    <div class="page-content">

        <?php
        //only admin can view and authorise students
        if ($_SESSION["userType"] === "admin") { ?>
            <h2><i class="fa fa-id-badge" aria-hidden="true"> Student Applications</i></h2>
            <?php

            $sql = "SELECT * FROM user WHERE userType = 'student' AND userAuthorised = 0";
            $result = mysqli_query($conn, $sql);
            authoriseStudent($conn);
            rejectStudent($conn);
            while ($row = $result->fetch_object()) {
                ?>
                <form method='post' action='admin.php'>
                    <table>
                        <tr>
                            <td><?php echo $row->id ?> </td>
                            <td><?php echo $row->name ?></td>
                            <td><?php echo $row->surname ?></td>
                            <td><input type='submit' class="button" name='authoriseStudent' value='Authorise'</td>
                            <td><input type='submit' class="button" name='rejectStudent' value='Reject'</td>
                        </tr>
                    </table>
                    <input type='hidden' name='userAuthorised' value='<?php echo $row->userAuthorised ?>'/>
                    <input type='hidden' name='id' value='<?php echo $row->id ?>'/>

                </form>
                <?php
            }
        }
        ?>
    </div>
    <div class="page-content">
        <table>
            <h2><i class="fa fa-book" aria-hidden="true"> Enrollment of students onto applied courses </i></h2>
            <tr>
                <th>Student Id</th>
                <th>Name</th>
                <th>Surname</th>
                <th>Course Id number</th>
                <th>Approve</th>
                <th>Reject</th>
            </tr>
            <?php
            //both admin and tutors can view the details of students enrollment. But tutors are restricted from viewing the enrollment of students from other courses
            {
                $sql = "SELECT studentcourse.studentId AS studentId ,studentcourse.courseId AS courseId  ,user.name AS name ,user.surname AS surname   FROM studentcourse  INNER JOIN user ON studentcourse.studentId = user.id WHERE studentcourse.courseApproved = 0";
                $result = mysqli_query($conn, $sql);
                authoriseEnrollmentCourse($conn);
                rejectEnrollmentCourse($conn);

                while ($row = $result->fetch_object()) {
                    // if user is a tutor, then he can enroll students on his course
                    if ($_SESSION['userType'] == 'tutor') {
                        foreach ($resultingTutor as $ans) {
                            if ($row->courseId == $ans[1]) {
                                runenrollmentStudent($conn, $row);
                            }
                        }
                    } // admin can enroll all
                    else {
                        runenrollmentStudent($conn, $row);
                    }
                }
            }

            ?>
        </table>

    </div>

    <?php
    if (isset($_POST["createTutor"])) {
        $formError = addNewTutor($conn, $_POST);
    }
    ?>
    <div class="page-content">

        <h2><i class="fa fa-university"> Manage Tutors </i></h2>
        <form action="admin.php" method="POST" class="flex-form add-form">
            <h2>Add new tutor</h2>

            <label for="name">Name</label>
            <input type="text" name="name" id="name"/>
            <label for="surname">Surname</label>
            <input type="text" name="surname" id="surname"/>
            <label for="email">Email</label>
            <input type="email" name="email" id="email"/>
            <label for="phoneNumber">Phone Number</label>
            <input type="number" name="phoneNumber" id="phoneNumber"/>
            <label for="address">Your address</label>
            <input type="text" name="address" id="address"/>
            <label for="dateOfBirth">Date of birth</label>
            <input type="date" name="dateOfBirth" id="dateOfBirth"/>
            <label for="password">Password</label>
            <input type="password" name="password" id="password"/>
            <label for="retypePassword">Confirm Password</label>
            <input type="password" name="retypePassword" id="retypePassword"/>
            <?php
            if (isset($formError) && isset($_POST["createTutor"])) {
                if ($formError) {
                    displayError("There was an error while creating new tutor!");
                } else {
                    displaySuccessMessage("Your tutor was created successfully!");
                }
            }
            ?>
            <button type="submit" name="createTutor">Create tutor</button>
        </form>

    </div>

    <div class="page-content">

        <?php
        //only admin can view and authorise tutors
        if ($_SESSION["userType"] === "admin") { ?>
            <table>
            <h2><i class="fa fa-university"> Tutors Application </i></h2>
            <tr>
                <th>Tutor ID number</th>
                <th>Name</th>
                <th>Surname</th>
                <th>Contact details</th>
                <th>e-mail Address</th>
                <th>Place of residence</th>
                <th>Date of birth</th>
            </tr>
            <?php
            $sql = "SELECT * FROM user WHERE userType = 'tutor' AND userAuthorised = 0";
            $result = mysqli_query($conn, $sql);
            authoriseTutor($conn);
            rejectTutor($conn);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_object()) {
                    ?>
                    <form method='post' action='admin.php'>

                        <tr>
                            <td><?php echo $row->id ?> </td>
                            <td><?php echo $row->name ?></td>
                            <td><?php echo $row->surname ?></td>
                            <td><?php echo $row->phoneNumber ?></td>
                            <td><?php echo $row->email ?></td>
                            <td><?php echo $row->address ?></td>
                            <td><?php echo $row->dateOfBirth ?></td>
                            <td><input type='submit' class="button" name='authoriseTutor' value='Authorise'</td>
                            <td><input type='submit' class="button" name='rejectTutor' value='Reject'</td>
                        </tr></table>
                    <input type='hidden' name='userAuthorised' value='
    <?php echo $row->userAuthorised ?>'/>
                    <input type='hidden' name='id' value='<?php echo $row->id ?>'/>
                    </form>

                    <?php
                }
            } else {
                echo "<h3 style='color:black;'>No tutors to authorise</h3>";
            }
            ?>
            </table><?php } ?>
    </div>

    <div class="page-content">

        <?php
        //only admin can view and authorise tutors onto courses
        if ($_SESSION["userType"] === "admin"){ ?>
        <h2><i class="fa fa-book" aria-hidden="true"> Enrollment of tutors onto courses </i></h2>

        <?php
        $sql = "SELECT tutor.tutorId ,user.name,user.surname, tutor.contractType  FROM tutor  INNER JOIN user ON tutor.tutorId = user.id  ";
        $result = mysqli_query($conn, $sql);
        enrollTutor($conn);
        courseLeader($conn) ?>
        <table style="padding: 10px; column-gap: 10px; row-gap: 10px">
            <tr>
                <th>Tutor Id number</th>
                <th>Name</th>
                <th>Surname</th>
                <th>Select the course</th>
                <th>Type of contract</th>
                <th>Promote to course leader</th>
                <th>Submit</th>
            </tr><?php
            while ($row = $result->fetch_object()) {
                ?>
                <form method='post' action='admin.php'>
                    <tr>
                        <td><?php echo $row->tutorId ?> </td>
                        <td><?php echo $row->name ?></td>
                        <td><?php echo $row->surname ?></td>
                        <td>
                            <select name="courseEnroll" id="courseEnroll" required>
                                <?php
                                foreach ($courses as $course) { ?>
                                    <option value="<?php echo $course[0] ?>"><?php echo $course[1] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <select name="contract" id="contract" required>
                                <option value="FT">Full-Time</option>
                                <option value="PT">Part-Time</option>

                            </select>

                        </td>
                        <td><input type='submit' class="button" name='courseLeader' value='Promote'
                        </td>
                        <td><input type='submit' class="button" name='enrollTutor' value='Enroll'</td>
                    </tr>

                    <input type='hidden' name='tutorId' value='<?php echo $row->tutorId ?>'/>
                    <input type='hidden' name='tutorName' value='<?php echo $row->name ?>'/>
                    <input type='hidden' name='tutorSurname' value='<?php echo $row->surname ?>'/>

                </form>

                <?php

            ?> </table>
    </div>
    <div class="page-content">
        <table>
            <h2><i class="fa fa-info-circle"> Edit the course </i></h2>
            <tr>
                <th>Course Number</th>
                <th>Course name</th>
                <th>Course Tutor</th>
                <th>Edit course</th>
            </tr>
            <?php foreach ($courses as $course) {
                if ($_SESSION['userType'] == 'tutor') {
                    foreach ($resultingTutor as $ans) {
                        if ($course[0] == $ans[1]) {
                            runedictCourse($conn, $course);
                        }

                    }

                } else {
                    runedictCourse($conn, $course);
                }
            }
            ?>
        </table>
    </div>
    <div class="page-content">
        <table>
            <h2><i class="fa fa-info-circle"> Information </i></h2>
            <h3>Tutors Info</h3>
            <tr>
                <th>Tutor</th>
                <th>Courses</th>
            </tr>
            <?php
            $sql = "SELECT user.name AS name, user.surname AS surname, user.id AS Id  FROM tutor
                    LEFT JOIN user ON tutor.tutorId = user.id ";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                while ($row = $result->fetch_object()) {
                    ?>
                    <tr>
                        <td><?php echo $row->name . "" . $row->surname; ?></td>
                        <td>
                            <?php
                            $sqlin = "SELECT course.courseName AS courseName FROM tutorcourse LEFT JOIN course ON tutorcourse.courseId = course.courseId WHERE tutorcourse.tutorId = $row->Id ";
                            $resultsqlin = mysqli_query($conn, $sqlin);
                            if ($resultsqlin) {
                                while ($dis = $resultsqlin->fetch_object()) {
                                    echo $dis->courseName . "<br>";
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        <table>
            <!--  Tutors can only see students on their branch -->
            <h3>Students Info</h3>
            <tr>
                <th>Student ID Number</th>
                <th>Name</th>
                <th>Surname</th>
                <th>Course Name</th>
                <th>Financial Status</th>
            </tr>
            <?php
            $sql = "SELECT user.name AS name, user.surname AS surname, user.id AS Id, studentcourse.courseId AS courseId,course.courseFee AS fee, course.courseName AS courseName FROM studentcourse 
                    LEFT JOIN user ON studentcourse.studentId = user.id    LEFT JOIN course ON studentcourse.courseId = course.courseId   LEFT JOIN student ON studentcourse.studentId = student.studentId AND user.id
                    WHERE studentcourse.courseApproved = 1";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                while ($row = $result->fetch_object()) {
                    if ($_SESSION['userType'] == 'tutor') {
                        foreach ($resultingTutor as $ans) {
                            if ($row->courseId == $ans[1]) {
                                rundisplayStudent($conn, $row);
                            }
                        }
                    } else {
                        rundisplayStudent($conn, $row);
                    }
                }
            } else {
                echo $conn->error;
            }
            ?>
        </table>
    </div>
    <div class="page-content">
        <!--
        only admin can add new course into the system-->
        <?php if ($_SESSION['userType'] == 'admin') {
            ?>
            <h2><i class="fa fa-file"> Add Course </i></h2>
            <?php
            if (isset($_POST["addCourseForm"])) {
                $courseLeader = mysqli_escape_string($conn, $_POST["courseLeader"]);
                $courseName = mysqli_escape_string($conn, $_POST["courseName"]);
                $courseProgramme = mysqli_escape_string($conn, $_POST["courseProgramme"]);

                $sql = "INSERT INTO course (courseName,courseLeader,courseProgramme) VALUES ('$courseName','$courseLeader','$courseProgramme')";
                if (!$conn->query($sql)) {
                    echo mysqli_error($conn);
                }
            }
            ?>
            <form action="admin.php" method="POST" class="flex-form add-form">
                <label for="courseName">Course Name</label>
                <input type="text" name="courseName" id="courseName">
                <label for="courseProgramme">Course Programme</label>
                <textarea name="courseProgramme" id="courseProgramme">Course programme</textarea>
                <label for="courseLeader">Course Leader</label>
                <input type="text" name="courseLeader" id="courseLeader">
                <button type="submit" value="true" name="addCourseForm">Add</button>
            </form>
            <?php
        }
        ?>
    </div>
    <div class="page-content">
        <!-- Assignment file handling. Admin can access all files. But tutors are restricted to access files of their own course-->
        <h1>Assignments</h1>
        <h2><i class="fa fa-upload"> Upload Assignment Files</i></h2>
        <form action="admin.php" method="POST" class="flex-form add-form" enctype="multipart/form-data">
            <label for="courseId">Choose course</label>
            <select name="courseId" id="courseId" required>
                <?php
                foreach ($courses as $course) {
                    if ($_SESSION['userType'] == 'tutor') {
                        foreach ($resultingTutor as $ans) {
                            if ($course[0] == $ans[1]) {
                                ?>
                                <option value="<?php echo $course[0] ?>"><?php echo $course[1] ?></option>
                            <?php }
                        }
                    } else {
                        ?>
                        <option value="<?php echo $course[0] ?>"><?php echo $course[1] ?></option>
                        <?php

                    }
                }
                ?>
            </select>
            <label for="assignmentDetails">Assignment Details</label>
            <textarea id="assignmentDetails" name="assignmentDetails" required></textarea>
            <label for="assignmentDate">Assignment Due Date</label>
            <input type="date" id="assignmentDate" name="assignmentDate" required> </input>
            <label for="file">Assignment Resource</label>
            <input type="file" name="file" id="file">
            <button type="submit" value="true" name="addAssignmentResource">Add</button>
        </form>


        <?php
        if (isset($_POST["addAssignmentResource"]) && $_FILES["file"]) {
            $courseId = mysqli_escape_string($conn, $_POST["courseId"]);
            $assignmentDetails = mysqli_escape_string($conn, $_POST["assignmentDetails"]);
            $assignmentDate = date('Y-m-d', strtotime($_POST['assignmentDate']));
            $fileData = $_FILES["file"];

         <?php
         }
         ?>
     </div>
    <!-- Assignment file handling. Admin can access all files. But tutors are restricted to access files of their own course-->
    <h1>Assignments</h1>
    <h2><i class = "fa fa-upload"> Upload Assignment Files</i></h2>
    <form action="admin.php" method="POST" class="flex-form"  enctype="multipart/form-data">
        <label for="courseId">Choose course</label>
        <select name="courseId" id="courseId" required>
            <?php
            foreach ($courses as $course) {
                if($_SESSION['userType'] == 'tutor'){
                    foreach ($resultingTutor as $ans){
                        if ($course[0] == $ans[1]){
                            ?>
                <option value="<?php echo $course[0] ?>"><?php echo $course[1] ?></option>
            <?php }
                    }
                }
             else {
            ?>
             <option value="<?php echo $course[0] ?>"><?php echo $course[1] ?></option>
             <?php
}
         }
 ?>
        </select>
        <label for="assignmentDetails">Assignment Details</label>
        <textarea id="assignmentDetails" name="assignmentDetails" required></textarea>
        <label for="assignmentDate">Assignment Due Date</label>
        <input type="date" id="assignmentDate" name="assignmentDate" required> </input>
        <label for="file">Assignment Resource</label>
        <input type="file" name="file" id="file">
        <button type="submit" value="true" name="addAssignmentResource">Add</button>
    </form>


            $tmpName = $fileData["tmp_name"];
            $filename = $fileData["name"];

            $noOfForbiddenChars = 0;

            // Count no. of forbidden chars in file name


                    }
                }
                ?>
            </select>
            <label for="assignmentDetails">Assignment Details</label>
            <textarea id="assignmentDetails" name="assignmentDetails" required></textarea>
            <label for="assignmentDate">Assignment Due Date</label>
            <input type="date" id="assignmentDate" name="assignmentDate" required> </input>
            <label for="file">Assignment Resource</label>
            <input type="file" name="file" id="file">
            <button type="submit" value="true" name="addAssignmentResource">Add</button>
        </form>

        <?php
        if (isset($_POST["addAssignmentResource"]) && $_FILES["file"]) {
            $courseId = mysqli_escape_string($conn, $_POST["courseId"]);
            $assignmentDetails = mysqli_escape_string($conn, $_POST["assignmentDetails"]);
            $assignmentDate = date('Y-m-d', strtotime($_POST['assignmentDate']));
            $fileData = $_FILES["file"];

            $tmpName = $fileData["tmp_name"];
            $filename = $fileData["name"];

            $noOfForbiddenChars = 0;

            // Count no. of forbidden chars in file name


            for ($i = 0; $i < strlen($filename); $i++) {
                if (($filename[$i] == "<") || ($filename[$i] == ">") || ($filename[$i] == "#") || ($filename[$i] == "%")) {
                    $noOfForbiddenChars++;
                }
            }



            if ((move_uploaded_file($tmpName, $_SERVER["DOCUMENT_ROOT"] . "/uploads/$filename")) && ($noOfForbiddenChars == 0)) {
                $sql = "INSERT INTO assignment(courseId,assignmentDetails,dueDate) VALUES ('$courseId','$assignmentDetails', '$assignmentDate');";
                if (!$conn->query($sql)) {
                    echo mysqli_error($conn);
                }
                $assignmentId = $conn->insert_id;
                $authorId = $_SESSION["userId"];
                $sql = "INSERT INTO file(fileName,authorId) VALUES ('$filename','$authorId');";
                if (!$conn->query($sql)) {
                    echo mysqli_error($conn);
                }
                $fileId = $conn->insert_id;
                $sql = "INSERT INTO assignmentresource (assignmentId,fileId) VALUES ('$assignmentId','$fileId')";
                if (!$conn->query($sql)) {
                    echo mysqli_error($conn);
                }
            } else {
                echo "File uploading has failed. Check if the name of the file contains a '<', '>', '%' or a '#'.";

        if((move_uploaded_file($tmpName,$_SERVER["DOCUMENT_ROOT"] . "/uploads/$filename")) && ($noOfForbiddenChars == 0)){
            $sql = "INSERT INTO assignment(courseId,assignmentDetails,dueDate) VALUES ('$courseId','$assignmentDetails', '$assignmentDate');";
            if(!$conn->query($sql)){
                echo mysqli_error($conn);


            }
        } ?>

        <!-- Grade student assignment work -->
    </div>
        <div class="page-content">>
            <h2><i class="fa fa-university"> Grade Assignments</i></h2>

            <table>
                <tr>
                    <th>Assignment</th>
                    <th>Submitted Work</th>
                    <th>Student</th>
                    <th>Grade</th>
                </tr>
                <?php
                $sql = mysqli_query($conn, "SELECT * FROM assignmentgrade;");
                $fileNames = mysqli_query($conn, "SELECT fileName FROM file INNER JOIN assignmentgrade ON assignmentgrade.fileId=file.fileId WHERE file.fileId=assignmentgrade.fileId");
                while ($row = mysqli_fetch_array($sql)) { ?>
                    <form method='POST' action='<?php echo "admin.php?fileid=" . $row['fileId']; ?>' method='POST'
                          enctype='multipart/form-data'>
                        <tr>
                            <td> <?php echo $row['assignmentId']; ?> </td>
                            <td><?php echo "<a href='/learning-platform-moodle/uploads/" . mysqli_fetch_array($fileNames)[0] . "'/>Download file</a>"; ?></td>
                            <td> <?php echo $row['userId']; ?> </td>
                            <td><input type='text' name='Grade' id='Grade' required></td>
                            <td><input type="submit" value="Submit" name="uploadGrade"></td>
                        </tr>
                    </form>
                <?php }

                if (isset($_POST["uploadGrade"])) {
                    $fileId = $_GET['fileid'];
                    $userId = $_SESSION['userId'];
                    $grade = mysqli_escape_string($conn, $_POST["Grade"]);

                    $sql = "UPDATE assignmentgrade SET grade = '$grade' WHERE fileId='$fileId'";
                    if (!$conn->query($sql)) {
                        echo mysqli_error($conn);
                    }
                } ?>
            </table>
        </div>
    <div class="page-content">
        <h1>Quizzes</h1>
        <h2><i class="fa fa-file"> Add Quiz</i></h2>

        <form action="admin.php" method="POST" class="flex-form add-form" enctype="multipart/form-data">
            <label for="courseId">Course</label>
            <select name="courseId" id="courseId" required>
                <?php
                foreach ($courses as $course) {
                    if ($_SESSION['userType'] == 'tutor') {
                        foreach ($resultingTutor as $ans) {
                            if ($course[0] == $ans[1]) {
                                ?>
                                <option value="<?php echo $course[0] ?>"><?php echo $course[1] ?></option>
                            <?php }
                        }
                    } else {
                        ?>
                        <option value="<?php echo $course[0] ?>"><?php echo $course[1] ?></option>
                        <?php

                    }
                }
                ?>
            </select>
            <label for="quizName">Quiz Name</label>
            <input type="text" id="quizName" name="quizName" required></input>
            <button type="submit" value="true" name="addQuiz">Add</button>
        </form>

    <?php } 

    if (isset($_POST["uploadGrade"])) {
        $fileId = $_GET['fileid'];
        $userId = $_SESSION['userId'];
        $grade = mysqli_escape_string($conn,$_POST["Grade"]);

        $sql = "UPDATE assignmentgrade SET grade = '$grade' WHERE fileId='$fileId'";
        if(!$conn->query($sql)) {
            echo mysqli_error($conn);
        }
    } ?>
</table>

    <h1>Quizzes</h1>
    <h2><i class = "fa fa-file"> Add Quiz</i></h2>

    <form action="admin.php" method="POST" class="flex-form"  enctype="multipart/form-data">
        <label for="courseId">Course</label>
        <select name="courseId" id="courseId" required>
            <?php
            foreach ($courses as $course) {
                if ($_SESSION['userType'] == 'tutor'){
                    foreach ($resultingTutor as $ans){
                        if ($course[0] == $ans[1]){
                            ?>
                <option value="<?php echo $course[0] ?>"><?php echo $course[1] ?></option>
            <?php }

                    }
                }

            }
            ?>
        </select>
        <label for="quizName">Quiz Name</label>
        <input type="text" id="quizName" name="quizName" required></input>
        <button type="submit" value="true" name="addQuiz">Add</button>
    </form>

                ?>
            </select>
            <label for="quizName">Quiz Name</label>
            <input type="text" id="quizName" name="quizName" required></input>
            <button type="submit" value="true" name="addQuiz">Add</button>
        </form>



        <?php

        if (isset($_POST["addQuiz"])) {

            $noOfForbiddenChars = 0;
            $courseId = mysqli_escape_string($conn, $_POST["courseId"]);
            $quizName = mysqli_escape_string($conn, $_POST["quizName"]);

            for ($i = 0; $i < strlen($quizName); $i++) {
                if (($quizName[$i] == "<") || ($quizName[$i] == ">") || ($quizName[$i] == "#") || ($quizName[$i] == "%")) {
                    $noOfForbiddenChars++;
                }
            }

            if ($noOfForbiddenChars == 0) {
                $sql = "INSERT INTO quiz(courseId, quizName) VALUES ('$courseId', '$quizName');";
                if (!$conn->query($sql)) {
                    echo mysqli_error($conn);
                }
            } else {
                echo "Quiz name invalid. Ensure there are no '<', '>', '#' or '%' symbols";
            }
        }


        ?>

        <h2 style="margin-top: 30px;"><i class="fa fa-upload"> Upload Quiz Questions</i></h2>

        <form action="admin.php" method="POST" class="flex-form add-form" enctype="multipart/form-data">
            <label for="quizId">Quiz</label>
            <select name="quizId" id="quizId" required>
                <?php
                foreach ($quizzes as $quiz) { ?>
                    <option value="<?php echo $quiz[0] ?>"><?php echo $quiz[2] ?></option>
                <?php } ?>
            </select>
            <label for="question">Question</label>
            <textarea id="question" name="question" required></textarea>
            <label for="answer1">Answer 1</label>
            <textarea id="answer1" name="answer1" required></textarea>
            <label for="answer2">Answer 2</label>
            <textarea id="answer2" name="answer2" required></textarea>
            <label for="answer3">Answer 3</label>
            <textarea id="answer3" name="answer3"></textarea>
            <label for="answer4">Answer 4</label>
            <textarea id="answer4" name="answer4"></textarea>
            <label for="correctAnswer">Correct Answer</label>
            <select name="correctAnswer" id="correctAnswer" required>
                <option value="answer1">Answer 1</option>
                <option value="answer2">Answer 2</option>
                <option value="answer3">Answer 3</option>
                <option value="answer4">Answer 4</option>
            </select>
            <button type="submit" value="true" name="addQuestion" style="margin-top: 10px">Add</button>



        ?>

        <h2 style="margin-top: 30px;"><i class="fa fa-upload"> Upload Quiz Questions</i></h2>

        <form action="admin.php" method="POST" class="flex-form add-form" enctype="multipart/form-data">
            <label for="quizId">Quiz</label>
            <select name="quizId" id="quizId" required>
                <?php
                foreach ($quizzes as $quiz) { ?>
                    <option value="<?php echo $quiz[0] ?>"><?php echo $quiz[2] ?></option>
                <?php } ?>
            </select>
            <label for="question">Question</label>
            <textarea id="question" name="question" required></textarea>
            <label for="answer1">Answer 1</label>
            <textarea id="answer1" name="answer1" required></textarea>
            <label for="answer2">Answer 2</label>
            <textarea id="answer2" name="answer2" required></textarea>
            <label for="answer3">Answer 3</label>
            <textarea id="answer3" name="answer3"></textarea>
            <label for="answer4">Answer 4</label>
            <textarea id="answer4" name="answer4"></textarea>
            <label for="correctAnswer">Correct Answer</label>
            <select name="correctAnswer" id="correctAnswer" required>
                <option value="answer1">Answer 1</option>
                <option value="answer2">Answer 2</option>
                <option value="answer3">Answer 3</option>
                <option value="answer4">Answer 4</option>
            </select>
            <button type="submit" value="true" name="addQuestion" style="margin-top: 10px">Add</button>

        </form>

        <?php

        if (isset($_POST["addQuestion"])) {

            $noOfForbiddenCharsQuestion = 0;
            $noOfForbiddenCharsAnswer1 = 0;
            $noOfForbiddenCharsAnswer2 = 0;
            $noOfForbiddenCharsAnswer3 = 0;
            $noOfForbiddenCharsAnswer4 = 0;
            $quizId = mysqli_escape_string($conn, $_POST["quizId"]);
            $question = mysqli_escape_string($conn, $_POST["question"]);
            $answer1 = mysqli_escape_string($conn, $_POST["answer1"]);
            $answer2 = mysqli_escape_string($conn, $_POST["answer2"]);
            $answer3 = mysqli_escape_string($conn, $_POST["answer3"]);
            $answer4 = mysqli_escape_string($conn, $_POST["answer4"]);
            $correctAnswer = mysqli_escape_string($conn, $_POST["correctAnswer"]);
            if ($correctAnswer == "answer1") {
                $correctAnswer = $answer1;
            } else if ($correctAnswer == "answer2") {
                $correctAnswer = $answer2;
            } else if ($correctAnswer == "answer3") {
                $correctAnswer = $answer3;
            } else {
                $correctAnswer = $answer4;
            }
            // Making sure questions and answers dont have forbidden chars

            for ($i = 0; $i < strlen($question); $i++) {
                if (($question[$i] == "<") || ($question[$i] == ">") || ($question[$i] == "#") || ($question[$i] == "%")) {
                    $noOfForbiddenCharsQuestion++;
                }
            }

            for ($i = 0; $i < strlen($answer1); $i++) {
                if (($answer1[$i] == "<") || ($answer1[$i] == ">") || ($answer1[$i] == "#") || ($answer1[$i] == "%")) {
                    $noOfForbiddenCharsQuestionAnswer1++;
                }
            }

            for ($i = 0; $i < strlen($answer2); $i++) {
                if (($answer2[$i] == "<") || ($answer2[$i] == ">") || ($answer2[$i] == "#") || ($answer2[$i] == "%")) {
                    $noOfForbiddenCharsQuestionAnswer2++;
                }
            }

            for ($i = 0; $i < strlen($answer3); $i++) {
                if (($answer3[$i] == "<") || ($answer3[$i] == ">") || ($answer3[$i] == "#") || ($answer3[$i] == "%")) {
                    $noOfForbiddenCharsQuestionAnswer3++;
                }
            }

            for ($i = 0; $i < strlen($answer4); $i++) {
                if (($answer4[$i] == "<") || ($answer4[$i] == ">") || ($answer4[$i] == "#") || ($answer4[$i] == "%")) {
                    $noOfForbiddenCharsQuestionAnswer4++;
                }
            }

            if (($noOfForbiddenCharsQuestion == 0) && ($noOfForbiddenCharsAnswer1 == 0) && ($noOfForbiddenCharsAnswer2 == 0) && ($noOfForbiddenCharsAnswer3 == 0) && ($noOfForbiddenCharsAnswer4 == 0)) {
                $sql = "INSERT INTO quizquestions (quizId, question, answer1, answer2, answer3, answer4, correctAnswer) VALUES ('$quizId', '$question', '$answer1', '$answer2', '$answer3', '$answer4', '$correctAnswer');";
                if (!$conn->query($sql)) {
                    echo mysqli_error($conn);
                }
            } else {
                echo "Ensure the question and answers do not have '<', '>', '#' or '%' in them";
            }

        }

        ?>


    </div>
</div>
</div>
<?php
include("../includes/footer.php");
?>

