<?php
// this webpage will promt the user to upload the image and display that on image.php
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
    $_SERVER['HTTP_USER_AGENT'])) different_user();
    else {
        $user = $_SESSION['username'];
        echo "Hello Admin $user<br>";
        echo "Please <a href='logout.php'>click here</a> to log out.";
        webpage();
    }
}
else
{
    different_user();
}
// if we press the submit button after uploading an image, following code will be executed
if (isset($_POST["submit"])) {
    if ($_FILES) {
        $types = array('image/jpeg', 'image/gif', 'image/png');
        if (in_array($_FILES['filename']['type'], $types)) {
            $name = $_FILES['filename']['name'];
            $name = santitize_file_contents("[^A-Za-z0-9.]", "", $name);
            $x = $_SERVER['DOCUMENT_ROOT'];
            $_SESSION['y']= $name;
            move_uploaded_file($_FILES['filename']['tmp_name'], $name);
            header("Location:image.php?uploadSuccessful");
        }
        else{
            echo " Please upload only JPEG/PNG/GIF files";
        }
    }
    else {
        echo " please upload a file";
    }
}
else{
    echo " Please try again :)";
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
        <form action="continue.php" method="post" enctype="multipart/form-data">
            <h2> Welcome to Ari's DREAM"</h2>
            <br>     
            <h2>Upload File</h2>
            <label for="fileSelect">Filename:</label>
            <input type="file" name="filename" id="fileSelect">
            <p><strong>Note:</strong> upload the images (JPEG, JPG, PNG) .</p>
            <input type="submit" name="submit" value="Upload">
        </form>
    </body>
   _END;
}


// function used: 
// if the we dount the authenicity of the user, following code will be executed. 
function different_user()
{
 echo <<<_END
    </br>OOPS !!!!!!
    </br>Please try again
    </br>
    _END;
echo "Please <a href='midtermadmin.php'>click here to log in again </a> to log in.";
}

function mysql_fatal_err()
{
    echo <<<_END
    </br>OOPS !!!!!!
    </br>We are sorry, but it was not possible to complete
    </br>the requested task.
    </br>
    _END;
}
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


