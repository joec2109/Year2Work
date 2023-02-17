<?php
include ("../includes/header.php");
include $_SERVER["DOCUMENT_ROOT"] . "/learning-platform-moodle/includes/auth.php";
include("../config/Connection.php");

global $conn;
$quizId = $_GET["quizid"];
$quizCompleted = 0;

$checkIfUserCompletedQuiz = mysqli_query($conn, "SELECT quizId FROM quizgrade WHERE userId = $_SESSION[userId]");
while ($row = mysqli_fetch_array($checkIfUserCompletedQuiz)) {
	if ($row[0] == $quizId) {
		$quizCompleted = 1;
	}
}

$sql = mysqli_query($conn, "SELECT courseId, quizName FROM quiz WHERE quizId=$quizId");

while ($row = mysqli_fetch_array($sql)) {
	$quizName = $row[1];
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

?>

<?php if ($quizCompleted == 0) { ?>
<div class = "quizzes-title">
<h1><?php echo $quizName ?></h1>
</div>

<div class = "questions">
	<form class = 'questions-item' action='assignments.php?quizid=<?php echo $quizId ?>' method='POST' enctype='multipart/form-data'>
		<?php
			foreach($qQuestions as $key => $question) { ?>
				
				
					
				<div class = "questions-item-question">
					<ul>
						<li><h2><?php echo $key+1 . ". " . $question ?></h2></li>
						<li><?php echo $qAnswer1s[$key]; ?><input type = "radio" name = "q<?php echo $key+1; ?>" value = "<?php echo $qAnswer1s[$key] ?>"></li>
						<li><?php echo $qAnswer2s[$key]; ?><input type = "radio" name = "q<?php echo $key+1; ?>" value = "<?php echo $qAnswer2s[$key] ?>"></li>
						<?php if (sizeof($qAnswer3s) != 0){ ?>
							<li><?php echo $qAnswer3s[$key]; ?><input type = "radio" name = "q<?php echo $key+1; ?>" value = "<?php echo $qAnswer3s[$key] ?>"></li>
						<?php }
						if (sizeof($qAnswer4s) != 0){ ?>
							<li><?php echo $qAnswer4s[$key]; ?><input type = "radio" name = "q<?php echo $key+1; ?>" value = "<?php echo $qAnswer4s[$key] ?>"></li>
						<?php } ?>
					</ul>
				</div>

						
				

		
		<?php } ?>
		<input type = "submit" id = "btn_submit" value = "Submit" name = "submitQuiz">
	</form>
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

} else { ?>
	<h1 class = "quizzes-title">Quiz Completed!</h1>
<?php }



	include "../includes/footer.php";
?>