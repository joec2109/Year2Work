<?php

function displayError($message){
    echo "
    <div class='error'>
        <h3>Error</h3>
        <p>$message</p>
    </div>
    
    ";
}

function displaySuccessMessage($message){
    echo "
    <div class='form-submission-success'>
        <h3>Success</h3>
        <p>$message</p>
    </div>
    
    ";
}