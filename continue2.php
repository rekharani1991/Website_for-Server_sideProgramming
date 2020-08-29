<?php
// this webpage will promt the user to upload the quote and poem and display that on poem.php
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
    else {
        $user = $_SESSION['username'];
        echo "Hello Admin $user <br><br>";
        echo "Please <a href='logout.php'>click here</a> to log out.";
        webpage();
    }
}
else
{
    different_user();
}

// we upload the poem and quote and press submit button: 
if (isset($_POST['submit']) && !empty($_POST['quote']))  {
    if ($_FILES){
        $name = $_FILES['filename']['name'];
        $name = santitize_file_contents("[^A-Za-z0-9.]", "", $name);
        $ext = '';
        // Validation of file type : if it is actually a text file or not
        switch ($_FILES['filename']['type']) {
        case 'text/css':
        $ext = 'css';
        break;
        case 'text/xml':
        $ext = 'xml';
        break;
        case 'text/plain':
        $ext = 'plain';
        break;
        case 'html':
        $ext = 'html';
        break;
        default:
        $ext = '';
        break;
            }
        // if it is valid file type
        if ($ext) {
            move_uploaded_file($_FILES['filename']['tmp_name'], $name);
            //$path = $_SERVER['DOCUMENT_ROOT'];
            $_SESSION['path'] = $name;
            //santiizing the quote received from user
            $quote = santitizeSuper($conn, 'quote');
            // server contain a quote file where all quotes would be appended
            $quotefile = 'quote.txt';
            $_SESSION['quote'] = $quotefile;
            $fh = fopen($quotefile, 'r+') or die("Failed to open file");
            if (flock($fh, LOCK_EX)) {
                fseek($fh, 0 , SEEK_END);
                fwrite($fh, "$quote") or die("Could not write to file");
                fflush($fh);
                flock($fh, LOCK_UN);
            }
            fclose($fh);
            header("Location: poem.php?uploadSuccessful");
        } else {
            echo " Please upload only text file";
        }
    } else {
        echo " please add QUOTE and POEM !!!!";
    }
}
else{
    echo "NO FIle uploaded yet !!! ";
}


$conn->close(); // closing the connection

function webpage()
{
    echo <<<_END
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>File Upload Form</title>
    </head>
    <body>
        <form action="continue2.php" method="post" enctype="multipart/form-data"> 
            <h2> Welcome to Ari's DREAM"</h2>
            <br>     
            <h2>Hello ! Poem :) </h2>
            <label for="fileSelect">Poem:</label>
            <input type="file" name="filename" id="fileSelect" input style="widtn:200px;">
            <h2>Hello ! Quote :) </h2>
            <input type= "text" name="quote" id="entertext" input style="widtn:200px;">
            <br>
            <br>
            <p><strong>Note:</strong> Has anyone told you today that you are awesome :)</p>
            <input type="submit" name="submit" value="Upload">
        </form>
    </body>
   _END;
}
//FUNCTION USED:
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
// function when there is an connection error
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

function santitizeSuper($conn, $var)
{
    if (get_magic_quotes_gpc()) {
        $var = stripslashes($var);
    }
    $var = strip_tags($var);
    $temp = $conn->real_escape_string($_POST[$var]);
    return htmlentities($temp);
}
