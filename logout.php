<?php
// logout page
require_once "login.php";
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) {
    die(mysql_fatal_err());
} //if there is an error in connection, function mysql_fatal_err would be called

session_start();

// checking the autheniciy of the user in this page
// Session Security - Preventing session hijacking : using hash check
//It calls the function different_user if the stored IP address doesn’t match the current one:

if (isset($_SESSION['username'])) {
    if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'] .
    $_SERVER['HTTP_USER_AGENT'])) {
        different_user();
    } else {
        $user = $_SESSION['username'];
        destroy_session_and_data();
        echo "See you soon. Take care !!!!! bye $user";
    }
}
else {

   different_user();
}

$conn->close(); // closing the connection


//FUNCTIONS USED:
// Function that would be called when there is a connection error.
function mysql_fatal_err()
{
    echo <<<_END
    </br>OOPS !!!!!!
    </br>We are sorry, but it was not possible to complete
    </br>the requested task.
    </br>
    _END;
}
// this function will destry session and its data. 
function destroy_session_and_data()
{
    $_SESSION = array();    // Delete all the information in the array
    setcookie(session_name(), '', time() - 2592000, '/');
    session_destroy();
}

// if the we dount the authenicity of the user, following code will be executed.
function different_user()
{
    echo <<<_END
    </br>OOPS !!!!!!
    </br>Error occured 
    </br>Please try again
    </br>
    _END;
    echo "Please <a href='midtermadmin.php'>click here to log in again </a> to log in.";
}


