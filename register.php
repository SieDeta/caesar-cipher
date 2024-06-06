<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Register</title>
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
        
        if(isset($_POST['submit'])){
            $username = $_POST['username'];
            $email = $_POST['email'];
            $nim = $_POST['nim'];
            $password = $_POST['password'];
        
            // Caesar cipher key (ganti dengan kunci yang diinginkan)
            $caesarKey = 5;
        
            // Enkripsi password dengan Caesar cipher sebelum disimpan ke database
            $encryptedUsername = caesarEncrypt($username, $caesarKey);
            $encryptedEmail = caesarEncrypt($email, $caesarKey);
            $encryptedNim = caesarEncrypt($nim, $caesarKey);
            $encryptedPassword = caesarEncrypt($password, $caesarKey);
        
            // Verifikasi email unik
            $verify_query = mysqli_query($con,"SELECT Email FROM users WHERE Email='$email'");
        
            if(mysqli_num_rows($verify_query) != 0 ){
                echo "<div class='message'>
                          <p>This email is used, Try another one please!</p>
                      </div> <br>";
                echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
            } else {
                mysqli_query($con,"INSERT INTO users(Username,Email,NIM,Password) VALUES('$encryptedUsername','$encryptedEmail','$encryptedNim','$encryptedPassword')") or die("Error Occurred");
        
                echo "<div class='message'>
                          <p>Registration successful!</p>
                      </div> <br>";
                echo "<a href='index.php'><button class='btn'>Login Now</button>";
            }
        }else{
         
        ?>

            <header>Sign Up</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="age">NIM</label>
                    <input type="text" name="nim" id="nim" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    
                    <input type="submit" class="btn" name="submit" value="Register" required>
                </div>
                <div class="links">
                    Already a member? <a href="index.php">Sign In</a>
                </div>
            </form>
        </div>
        <?php } ?>
      </div>
</body>
</html>