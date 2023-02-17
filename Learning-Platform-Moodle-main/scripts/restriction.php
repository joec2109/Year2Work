<?php

// this file contains the functions that operate differently for admin and tutors
function runenrollmentStudent($conn, $row){
    ?>

    <form method='post' action='admin.php'>

        <tr>
            <td><?php echo $row->studentId ?> </td>
            <td><?php echo $row->name ?></td>
            <td><?php echo $row->surname ?></td>
            <td><?php echo $row->courseId ?></td>

            <td><input type='submit' class="button" name='authorise' value='Authorise'</td>
            <td><input type='submit' class="button" name='reject' value='Reject'</td>
        </tr>

        <input type='hidden' name='courseApproved' value='<?php echo $row->courseApproved ?>'/>
        <input type='hidden' name='course' value='<?php echo $row->courseId ?>'/>
        <input type='hidden' name='student' value='<?php echo $row->studentId ?>'/>

    </form>
    <?php
}

function runedictCourse($conn,$course){?>
    <tr>
        <td><?php echo $course[0] ;?></td>
        <td><?php echo $course[1] ;?></td>
        <td><?php echo $course[2]; ?></td>
        <td style="padding: 10px;">
            <form action="adminEditCourse.php" method="POST" class="flex-column">
           <input type="text" hidden value="<?php echo $course[0] ?>" name="courseId">
            <button type="submit" style="margin: 0 auto;">Edit</button>
        </form>
      </td>
     </tr>
<?php
}
function rundisplayStudent($conn, $row){?>
<tr>
    <td><?php echo $row-> Id ;?></td>
    <td><?php echo $row->name ;?></td>
    <td><?php echo $row-> surname; ?></td>
    <td><?php echo $row-> courseName ;?></td>
    <td><?php echo $row-> fee ;?></td>
</tr>
<?php
}
?>
