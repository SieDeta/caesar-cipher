<?php 
   session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Login</title>
</head>
<body>
      <div class="container">
        <div class="box form-box">
            <?php 
             include("php/config.php");
             // Fungsi enkripsi Caesar cipher
             function caesarEncrypt($plaintext, $key) {
                $result = '';
                $length = strlen($plaintext);
                $shift = $key % 95; // Menggunakan 95 karena jumlah karakter yang mungkin (ASCII 32-126)

                for ($i = 0; $i < $length; $i++) {
                    $char = $plaintext[$i];

                    if (ctype_alpha($char)) {
                        $isUpper = ctype_upper($char);
                        $offset = ord($isUpper ? 'A' : 'a');
                        $result .= chr((ord($char) - $offset + $shift) % 26 + $offset);
                    } else {
                        $ascii = ord($char);
                        if ($ascii === 64) { // ASCII untuk karakter '@'
                            $result .= '@';
                        } else {
                            $result .= chr((($ascii - 32 + $shift) % 95) + 32);
                        }
                    }
                }

                return $result;
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
             
             if (isset($_POST['submit'])) {
                 $username = mysqli_real_escape_string($con, $_POST['username']);
                 $password = mysqli_real_escape_string($con, $_POST['password']);
             
                 // Caesar cipher key (sama dengan kunci yang digunakan pada pendaftaran)
                 $caesarKey = 5;
             
                 // Enkripsi username dan password sebelum melakukan pengecekan di database
                 $encryptedUsername = caesarEncrypt($username, $caesarKey);
                 $encryptedPassword = caesarEncrypt($password, $caesarKey);
             
                 $result = mysqli_query($con, "SELECT * FROM users WHERE Username='$encryptedUsername'") or die("Select Error");
                 $row = mysqli_fetch_assoc($result);
             
                 if (is_array($row) && !empty($row)) {
                     // Dekripsi password yang tersimpan di database
                     $storedPassword = caesarDecrypt($row['Password'], $caesarKey);
                     // Dekripsi password yang dimasukkan pada form untuk membandingkan
                     $decryptedPassword = caesarDecrypt($encryptedPassword, $caesarKey);
             
                     if ($decryptedPassword === $storedPassword) {
                         $_SESSION['email'] = $row['Email'];
                         $_SESSION['valid'] = $row['Username'];
                         $_SESSION['nim'] = $row['NIM'];
                         $_SESSION['id'] = $row['Id'];
                     } else {
                         echo "<div class='message'>
                                 <p>Wrong Username or Password</p>
                               </div> <br>";
                         echo "<a href='index.php'><button class='btn'>Go Back</button>";
                     }
                 } else {
                     echo "<div class='message'>
                             <p>Wrong Username or Password</p>
                           </div> <br>";
                     echo "<a href='index.php'><button class='btn'>Go Back</button>";
                 }
             
                 if (isset($_SESSION['valid'])) {
                     header("Location: home.php");
                 }
             }else{

            ?>
            <header>Login</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="email">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    
                    <input type="submit" class="btn" name="submit" value="Login" required>
                </div>
                <div class="links">
                    Don't have account? <a href="register.php">Sign Up Now</a>
                </div>
            </form>
        </div>
        <?php } ?>
      </div>
</body>
</html>