<?php 
   session_start();

   include("php/config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: index.php");
   }

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Change Profile</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php"> Logo</a></p>
        </div>

        <div class="right-links">
            <a href="#">Change Profile</a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <div class="container">
        <div class="box form-box">
            <?php 
               if(isset($_POST['submit'])){
                $username = $_POST['username'];
                $email = $_POST['email'];
                $nim = $_POST['nim'];
                $id = $_SESSION['id'];

                // Caesar cipher key (ganti dengan kunci yang diinginkan)
                $caesarKey = 5;
            
                // Enkripsi password dengan Caesar cipher sebelum disimpan ke database
                $encryptedUsername = caesarEncrypt($username, $caesarKey);
                $encryptedEmail = caesarEncrypt($email, $caesarKey);
                $encryptedNim = caesarEncrypt($nim, $caesarKey);

                $edit_query = mysqli_query($con,"UPDATE users SET Username='$encryptedUsername', Email='$encryptedEmail', NIM='$encryptedNim' WHERE Id=$id ") or die("error occurred");

                if($edit_query){
                    echo "<div class='message'>
                    <p>Profile Updated!</p>
                </div> <br>";
              echo "<a href='home.php'><button class='btn'>Go Home</button>";
       
                }
               }else{

                $id = $_SESSION['id'];
                $query = mysqli_query($con,"SELECT*FROM users WHERE Id=$id ");

                while($result = mysqli_fetch_assoc($query)){
                    $res_Uname = caesarDecrypt($result['Username'], 5); // Dekripsi Username
                    $res_Email = caesarDecrypt($result['Email'], 5); // Dekripsi Email
                    $res_Nim = caesarDecrypt($result['NIM'], 5);
                }

            ?>
            <header>Change Profile</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?php echo $res_Uname; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" value="<?php echo $res_Email; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="nim">NIM</label>
                    <input type="text" name="nim" id="nim" value="<?php echo $res_Nim; ?>" autocomplete="off" required>
                </div>
                
                <div class="field">
                    
                    <input type="submit" class="btn" name="submit" value="Update" required>
                </div>
                
            </form>
        </div>
        <?php } ?>
      </div>
</body>
</html>