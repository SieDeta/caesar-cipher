<?php 
session_start();

include("php/config.php");
if(!isset($_SESSION['valid'])){
    header("Location: index.php");
}

// Fungsi dekripsi Caesar cipher
function caesarDecrypt($ciphertext, $key) {
    $result = '';
    $length = strlen($ciphertext);
    $shift = $key % 95;

    for ($i = 0; $i < $length; $i++) {
        $char = $ciphertext[$i];

        if (ctype_alpha($char)) {
            $isUpper = ctype_upper($char);
            $offset = ord($isUpper ? 'A' : 'a');
            $result .= chr((ord($char) - $offset - $shift + 52) % 26 + $offset);
        } else {
            $ascii = ord($char);
            if ($ascii === 64) { // ASCII untuk karakter '@'
                $result .= '@';
            } else {
                $result .= chr((($ascii - 32 - $shift + 95) % 95) + 32);
            }
        }
    }

    return $result;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Home</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php">Kriptografi APP</a> </p>
        </div>

        <div class="right-links">

            <?php 
            
            $id = $_SESSION['id'];
            $query = mysqli_query($con,"SELECT*FROM users WHERE Id=$id");

            while($result = mysqli_fetch_assoc($query)){
                $res_Uname = $result['Username'];
                $res_Email = $result['Email'];
                $res_Nim = $result['NIM'];
                $res_id = $result['Id'];
            }
            
            echo "<a href='edit.php?Id=$res_id'>Change Profile</a>";

            ?>
            

            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>

        </div>
    </div>
    <main>

    <div class="main-box top">
          <div class="top">
            <div class="box">
                <?php 
                $id = $_SESSION['id'];
                $query = mysqli_query($con,"SELECT * FROM users WHERE Id=$id");

                while($result = mysqli_fetch_assoc($query)){
                    $res_Uname = caesarDecrypt($result['Username'], 5); // Dekripsi Username
                    $res_Email = caesarDecrypt($result['Email'], 5); // Dekripsi Email
                    $res_Nim = caesarDecrypt($result['NIM'], 5); // Dekripsi NIM
                    $res_id = $result['Id'];
                }
                
                echo "<p>Hello <b>$res_Uname</b>, Welcome</p>";
                echo "<p>Your email is <b>$res_Email</b>.</p>";
                echo "<p>And your NIM are <b>$res_Nim </b>.</p>";
                ?>
            </div>
          </div>
       </div>

    </main>
</body>
</html>