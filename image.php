
<?php
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
 }
    else{
        $user = $_SESSION['username'];
        echo "Hello Admin $user<br><br>";
        echo "Please <a href='logout.php'>click here</a> to log out.";
        echo "Please <a href='continue2.php'>click here</a> to upload poem and quote.";
        $name = $_SESSION['y'];
        $name = santitize_file_contents($name);
        echo "Uploaded image <br><img src='$name'>";
    }

} 
else {
    different_user();
}


$conn->close(); // closing the connection

function mysql_fatal_err()
{
    echo <<<_END
    </br>OOPS !!!!!!
    </br>We are sorry, but it was not possible to complete
    </br>the requested task.
    </br>
    _END;
}

// santitization function
function santitize_file_contents($var)
{
    if (get_magic_quotes_gpc()) {
        $var = stripslashes($var);
    }
    $var = strtolower(preg_replace("[^A-Za-z0-9.]", "", $var));
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}

//if the we dount the authenicity of the user, following code will be executed.
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

