<?php
//this webpage will displaye the quotes and poem addded by user

require_once "login.php";
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) {
    die(mysql_fatal_err());
} //if there is an error in connection, function mysql_fatal_err would be called

session_start();

// checking the autheniciy of the user in this page
// Session Security - Preventing session hijacking : using hash check
// It calls the function different_user if the stored IP address doesn’t match the current one


if (isset($_SESSION['username'])) {
    if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'] .
    $_SERVER['HTTP_USER_AGENT'])) {
        different_user();
    }
    else{
        $user = $_SESSION['username'];
        echo "Hello Admin $user ";
        echo "Please <a href='logout.php'>click here</a> to log out.  ";
        echo "Please <a href='continue.php'>click here</a> to upload image.";
        $target_file2 = $_SESSION['path'];
        //echo "the path is : $target_file2";
        $data = file_get_contents($target_file2);
        $data = santitize_file_contents($data);
        echo " -----------------";
        echo " -----------------";
        echo " Welcome to Ari's DREAM<br>";
        echo "<br>";
        echo " What a beutiful poem !!!!<br><br>";
        echo "<pre>";
        echo "$data";
        echo "</pre>";
        echo "<br>";
        // displaying the quote
        $path = $_SESSION['quote'];
        echo "<br> What a beutuful quote !!!! <br>";
        $quote = file_get_contents($path);
        $quote = santitize_file_contents($quote);
        echo "<pre>"; 	// Enables display of line feeds
        echo $quote;
        echo "</pre>";  // Terminates pre tag
        echo "<br>";

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

// function when the user is not trusted
function different_user()
{
    echo <<<_END
    </br>OOPS !!!!!!
    </br>Please try again
    </br>
    _END;
    echo "Please <a href='midtermadmin.php'>click here to log in again </a> to log in.";
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
