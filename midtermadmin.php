<?php
// Ari's DREAM :
// The idea of this project is to let the users personalize their own webpage. 
//They are not very tech-savvy and they would prefer something very user friendly. 
//You can imagine your webapplication to have a single text entry box and a file upload for images only. 
//This information would be automatically added to the homepage.

// this is this first page that would appear in the website. it would ask the login detials
// of the admin. After successful login , admin would be promted two choices
//1) move to continue to upload image
// 2) move to continue2 to upload poem and quotes
require_once "login.php";
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) {
    die(mysql_fatal_err());
} //if there is an error in connection, function mysql_fatal_err would be called
// when advisor name, student name, id, and class code is set


// fetching the username and password from the admin table to verify it
// this password is hashed with a salt added to it
$query = "SELECT * FROM admin";
$result = $conn->query($query);
if (!$result) {
    die(mysql_fatal_err());
} elseif ($result->num_rows) {
    $row = $result->fetch_array(MYSQLI_NUM);
    $password = $row[2];
    $username = $row[0];
}
$result->close();


// HTTTP authentication for admin credentials
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    // if the hashed and salted password mathches the hashed and salted password from user,
    // password_verify would return "success"
    $x = 'success';
    $user = mysql_fix_string($conn,$_SERVER['PHP_AUTH_USER']);
    $pass = mysql_fix_string($conn,$_SERVER['PHP_AUTH_PW']);
    $verify_password = password_verify($pass, $password);

    if ($user == $username && $verify_password == $x)
    {
        echo "You are now logged in \n";
        ini_set('session.gc_maxlifetime', 60 * 60 * 24);
        //Session Security - Forcing cookie-only sessions
        ini_set('session.use_only_cookies', 1);
        session_start();

        setcookie(session_name(), '', time(), '/');

// Session Security - Preventing session fixation

        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id();
            $_SESSION['initiated'] = 1;
        }
        if (!isset($_SESSION['count'])) $_SESSION['count'] = 0;
        else ++$_SESSION['count'];
        //echo $_SESSION['count'];

        // Session Security - Preventing session hijacking
        $check = hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
        $_SESSION['check'] = $check;
        $_SESSION['username']=$user;

        $temp = $_SESSION['username'];
        echo "HELLO!!! Welcome !!! <br> <br>";
        die("<p> Welcome to Ari's DREAM <p><p><a href=continue.php> Click here to upload image</a></p><p><a href=continue2.php>Click here to insert poem and quote </a></p><p><a href=logout.php>Click here to LOGOUT </a></p>");

    }
    else {
        die("Invalid username / password combination\n");
    }
}
else {
    header('WWW-Authenticate: Basic realm="Restricted Section"');
    header('HTTP/1.0 401 Unauthorized');
    die("Please enter your username and password\n");
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



//Preventing HTML injections
function mysql_entities_fix_string($conn, $var)
{
    return htmlentities(mysql_fix_string($conn, $var));
}

//The function below will remove any “magic quotes” and  properly sanitize it for you.
function mysql_fix_string($conn, $var)
{
    if (get_magic_quotes_gpc()) {
        $var = stripslashes($var);
    }
    $var = strip_tags($var);
    return $conn->real_escape_string($var);
}

function different_user()
{
    echo <<<_END
    </br>OOPS !!!!!!
    </br>Please try again
    </br>
    _END;
    echo "Please <a href='midtermadmin.php'>click here to log in again </a> to log in.";
}
