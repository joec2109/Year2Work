<?php
include ("../includes/header.php");
include $_SERVER["DOCUMENT_ROOT"] . "/includes/auth.php";
include("../config/Connection.php");

global $conn;
// Find the courseIds of the courses that the user is taking and the course enrolment has been approved
$userId = $_SESSION["userId"];

if(isset($_GET["quizid"])) {
	$quizId = $_GET["quizid"];

	$sql = mysqli_query($conn, "SELECT courseId, quizName FROM quiz WHERE quizId=$quizId");

	while ($row = mysqli_fetch_array($sql)) {
		$quizName = $row[1];
		$quizId = $row[0];
	}

	$qQuestions = array();
	$qAnswer1s = array();
	$qAnswer2s = array();
	$qAnswer3s = array();
	$qAnswer4s = array();
	$qCorrectAnswers = array();
	$questionNames = array();
	$numberCorrect = 0;

	$sql2 = mysqli_query($conn, "SELECT * FROM quizquestions WHERE quizId=$quizId");

	while($row = mysqli_fetch_array($sql2)) {
		array_push($qQuestions, $row['question']);
		array_push($qAnswer1s, $row['answer1']);
		array_push($qAnswer2s, $row['answer2']);
		array_push($qAnswer3s, $row['answer3']);
		array_push($qAnswer4s, $row['answer4']);
		array_push($qCorrectAnswers, $row['correctAnswer']);
	}
}

/*

					-- ASSIGNMENTS --

*/

// Grabs the IDs of the courses that the logged in user is taken and that have been approved by the admin
$courseIdResults = mysqli_query($conn, "SELECT studentcourse.courseId FROM studentcourse INNER JOIN course ON studentcourse.courseId=course.courseId WHERE studentId='$userId' AND courseApproved = 1");
$courseIds = mysqli_fetch_all($courseIdResults);


// Array variables that will hold the names, details and dates of each assignment that is assigned to the logged in user
$courseNamesResults = array();
$courseDetailsResults = array();
$courseDatesResults = array();
$courseFileNames = array();
$fileIds = array();
$quizNamesResults = array();
$quizIds = array();

$Ids = array();

// Grab the names, details and dates of each assignment of the courses that are being taken by the logged in user
if (sizeof($courseIds) > 0) {
	for ($i = 0; $i < sizeof($courseIds); $i++) {
		$courseId = $courseIds[$i][0];

		$quizzes = mysqli_query($conn, "SELECT quizName, quizId FROM quiz INNER JOIN course ON quiz.courseId=course.courseId WHERE quiz.courseId=$courseId");
		echo mysqli_error($conn);
		while ($row = mysqli_fetch_array($quizzes)) {
			array_push($quizNamesResults, $row['quizName']);
			array_push($quizIds, $row['quizId']);
		}

		// Grab the assignment ids of the assignments for the courses that the user is taking
		$files = mysqli_query($conn, "SELECT assignmentId FROM assignment WHERE assignment.courseId=$courseId");

		while($row = mysqli_fetch_array($files)) {
			$courseFileIds = mysqli_query($conn, "SELECT fileId FROM assignmentresource WHERE assignmentId = $row[0]");
			$courseNames = mysqli_query($conn, "SELECT courseName FROM assignment INNER JOIN course ON assignment.courseId=course.courseId WHERE assignment.courseId=$courseId and assignment.assignmentId=$row[assignmentId]");
			$courseDetails = mysqli_query($conn, "SELECT assignmentDetails FROM assignment INNER JOIN course ON assignment.courseId=course.courseId WHERE assignment.courseId=$courseId and assignment.assignmentId=$row[assignmentId]");
			$courseDates = mysqli_query($conn, "SELECT dueDate FROM assignment INNER JOIN course ON assignment.courseId=course.courseId WHERE assignment.courseId=$courseId and assignment.assignmentId=$row[assignmentId]");
			array_push($Ids, $row['assignmentId']);
			array_push($fileIds, mysqli_fetch_array($courseFileIds)['fileId']);
			array_push($courseNamesResults, mysqli_fetch_array($courseNames));
			array_push($courseDetailsResults, mysqli_fetch_array($courseDetails));
			array_push($courseDatesResults, mysqli_fetch_array($courseDates));
		}

	}

}

if (sizeof($fileIds) > 0) {
	for ($i = 0; $i < sizeof($Ids); $i++) {
		$fileNames = mysqli_query($conn, "SELECT fileName FROM file WHERE file.fileId=$fileIds[$i]");
		array_push($courseFileNames, mysqli_fetch_array($fileNames));

	}
}

// Gather the course names, assignment titles and due dates for each assignment

$assignmentCourses = array();
$assignmentDetails = array();
$assignmentDueDates = array();
$assignmentFileNames = array();
$assignmentFileIds = array();
$assignmentDatePassed = array();

// Push each name, detail and date for each assignment to the array variables that will be used within the html to present them to the user
if (sizeof($fileIds) > 0) {
	for ($i = 0; $i < sizeof($fileIds); $i++) {
		$name = $courseNamesResults[$i];
		$details = $courseDetailsResults[$i];
		$date = $courseDatesResults[$i];
		$fileName = $courseFileNames[$i];
		$id = $Ids[$i];
		array_push($assignmentCourses, $name);
		array_push($assignmentDetails, $details);
		array_push($assignmentDueDates, $date);
		array_push($assignmentFileNames, $fileName);
		array_push($assignmentFileIds, $id);
		array_push($assignmentDatePassed, 0);
	}
}

for ($i = 0; $i < sizeof($assignmentDueDates); $i++) {
	$currentDate = strtotime(date('Y-m-d'));
	$dueDate = strtotime($assignmentDueDates[$i]['dueDate']);
	if ($dueDate < $currentDate) {
		$assignmentDatePassed[$i] = 1;
	}
}

?>

<!-- Assignments -->

<div class = "assignments-grid">
	<div>
		<h1>Course Assignments</h1>
		<div class = "assignments">
			<?php if (sizeof($fileIds) > 0) { ?>
			<ul class = "assignments-courses-list">
				<h2>Course</h2>
				<?php foreach($assignmentCourses as $course){?>
					<td>
					<li class="assignments-course-item">
						<p>
						<?php
							echo $course[0];
						?>
						</p>
					</li>
				</td>
				<?php } ?>
			</ul>
			<ul class = "assignments-assignment-titles">
				<h2>Assignment Title</h2>
				<?php foreach($assignmentDetails as $detail){?>
						<td>
							<li class = "assignments-assignment-titles-item">
								<p>
									<?php
										echo $detail[0];
									?>
								</p>
							</li>
						</td>
				<?php } ?>
			</ul>

			<ul class = "assignments-assignment-dates">
				<h2>Due Date</h2>
				<?php foreach($assignmentDueDates as $date){?>
						<td>
							<li class = "assignments-assignment-dates-item">
								<p>
									<?php
										echo $date[0];
									?>
								</p>
							</li>
						</td>
				<?php }

				if (sizeof($fileIds) == 0) {
					echo "<p>There are no assignments!</p>";
				}

				 ?>
			</ul>

			<!-- File handling  (download & upload buttons) -->

			<ul class = "assignments-assignment-resources">
				<h2>Assignment Resource</h2>
				<?php foreach($assignmentFileNames as $file){ ?>
					<td>
						<li class = "assignments-assignment-resources-item">
							<p>
								<?php
									echo "<a href='/learning-platform-moodle/uploads/" . $file[0] . "'/>Download file</a>";
								?>
							</p>
						</li>
					</td>
				<?php } ?>
			</ul>

			<ul class ="assignments-assignment-file-upload">
				<h2>Upload File</h2>
				<?php 

					foreach($assignmentFileIds as $id) {
						if ($assignmentDatePassed[array_search($id, $assignmentFileIds)] == 0) {?>
							<td>
							<li class = "assignments-assignment-file-upload-item">
								<form action='<?php echo "assignments.php?fileid=" . $id; ?>' method='POST' enctype='multipart/form-data'>
									<input type="file" name="fileToUpload" id="fileToUpload">
									<input type="submit" value="Submit" name="uploadFile">
									<input type="submit" value="Unsubmit" name="unsubmitFile">
								</form>
							</li>
						</td>
				<?php } else { ?>
							<li class = "assignments-assignment-file-upload-item">
								<p>Due date has passed!</p>
							</li>
				<?php }
				}
				} else { ?>
					<p>There are no assignments</p>
				<?php } ?>
			</ul>

			<?php 

			if(isset($_POST["uploadFile"]) && $_FILES["fileToUpload"]){
				$assignmentId = $_GET["fileid"];

				$fileData = $_FILES["fileToUpload"];
		        $tmpName =$fileData["tmp_name"];
		        $filename = $fileData["name"];
		        $fileExists = 0;

		        $noOfForbiddenChars = 0;

		        // Count no. of forbidden chars in file name

		        for ($i = 0; $i < strlen($filename); $i++) {
		            if (($filename[$i] == "<") || ($filename[$i] == ">") || ($filename[$i] == "#") || ($filename[$i] == "%")) {
		                $noOfForbiddenChars++;
		            }
		        }

		        $checkExistingFiles = mysqli_query($conn, "SELECT fileName FROM file WHERE authorId=$_SESSION[userId]");
		        while($row = mysqli_fetch_array($checkExistingFiles)) {
		        	if ($row[0] == $filename) {
		        		$fileExists = 1;
		        	}
		        }
		        if ($fileExists == 0) {
					if((move_uploaded_file($tmpName,$_SERVER["DOCUMENT_ROOT"] . "/uploads/$filename")) && ($noOfForbiddenChars == 0)){

						$authorId = $_SESSION["userId"];
						$sql = "INSERT INTO file(fileName,authorId) VALUES ('$filename','$authorId');";
			            if(!$conn->query($sql)){
			                echo mysqli_error($conn);
			            }

			            $fileId = $conn->insert_id;
						$sql = "INSERT INTO assignmentgrade(assignmentId,fileId, userId) VALUES ('$assignmentId','$fileId', '$authorId');";
			            if(!$conn->query($sql)){
			                echo mysqli_error($conn);
			            }

					} else {
						echo "File uploading has failed. Check if the name of the file contains a '<', '>', '%' or a '#'.";
					}
				} else {
					echo "You have already uploaded that file";
				}

			}

			if(isset($_POST["unsubmitFile"])){
				$assignmentId = $_GET["fileid"];

				$sql = mysqli_query($conn, "SELECT fileId FROM assignmentgrade WHERE assignmentId=$assignmentId AND userId=$_SESSION[userId]");

				while($row = mysqli_fetch_array($sql)) {
					$deleteFile = mysqli_query($conn, "DELETE FROM assignmentgrade WHERE fileId=$row[0]");
					$deleteFile2 = mysqli_query($conn, "DELETE FROM file WHERE fileId=$row[0] AND authorId=$_SESSION[userId]");
				}

			}

			?>


		</div>
	</div>
	
	<div>
		<h1>Quizzes</h1>
		<div class = "quizzes">
			<br /><br />
			<ul class = "quizzes-list">
				<?php foreach($quizNamesResults as $key => $quiz){?>
					<div class = "quizzes-item">
						<button id = "quizButton" onclick="location.href = 'quizzes.php?quizid=<?php echo $quizIds[$key]?>';"> <?php echo $quiz ?> </button>
									
							</div>
				<?php } ?>
			</ul>
			<br /><br />
		</div>
	</div>
</div>

<?php

if(isset($_POST["submitQuiz"])) {
				$questionsAnswered = 1;
				foreach($qQuestions as $key => $q) {
					if(isset($_POST["q" . $key+1])) {
						if ($_POST["q" . $key+1] == $qCorrectAnswers[$key]) {
							$numberCorrect ++;
						}
					} else {
						$questionsAnswered=0;
					}
				}

				if ($questionsAnswered == 0) {
					echo "<h1 class = 'quizzes-title'>You need to answer all questions in order to successfully submit the quiz</h1>";
				}

				if ($questionsAnswered == 1) {

					$percentage = 100*($numberCorrect / sizeof($qQuestions));

					$userId = $_SESSION["userId"];
					$quizId = $_GET["quizid"];

					$sql = "INSERT INTO quizgrade(quizId, userId, grade) VALUES ($quizId, $userId, $percentage)";
					if (!$conn->query($sql)) {
						echo mysqli_error($conn);
					}

					$quizCompleted = 1;
					
				}


			}

include ("../includes/footer.php");
?>

