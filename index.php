<?php
    session_start();

    if (isset($_POST['belepes_gomb']))
    {
        $l = mysqli_connect('localhost', 'root', '', 'gyakorlas1');

        $email = mysqli_real_escape_string($l, $_POST['email']);
        $jelszo = mysqli_real_escape_string($l, $_POST['jelszo']);
        $kodolt_jelszo = hash("sha256", $jelszo);

        $lek = mysqli_query($l, "SELECT * FROM `adminok` WHERE `email` = '".$email."' AND `jelszo` = '".$kodolt_jelszo."' AND `statusz` = 'aktív'");
        $db = mysqli_num_rows($lek);


        if ($db == 1)
        {
            $adat = mysqli_fetch_array($lek);

            $_SESSION['admin_belepett'] = 'igen';
            $_SESSION['admin_email'] = $adat['email'];
            $_SESSION['admin_id'] = $adat['id'];
            $_SESSION['admin_jog'] = $adat['jogosultsag'];

            print '<meta http-equiv="refresh" content="0; url=admin.php">';

        }
        else
        {
            print '<h4 class="bg-danger text-white text-center">Hibás e-mail cím vagy jelszó!</h4>';
        }


        mysqli_close($l);

    }



?>




<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Tóth és Társa admin felület</title>

    <link rel="stylesheet" href="style.css">
    <script src="js/bootstrap.js"></script>
    <link rel="stylesheet" href="css/bootstrap.css">


</head>


<div class="row justify-content-center">
    <div class="col-md-6 belepes_box">
        <h2>Admin felület</h2>
        <br>
        <form method="post">
            <input type="text" name="email" placeholder="E-mail cím" required class="form-control">
            <input type="password" name="jelszo" placeholder="Jelszó" required class="form-control">
            <br>
            <input type="submit" name="belepes_gomb" value="Belépés" class="btn btn-primary">
        </form>
    </div>
</div>



</body>
</html>
