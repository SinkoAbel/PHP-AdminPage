<?php

session_start();


    if ($_SESSION['admin_belepett'] != 'igen')
    {
        print '<meta http-equiv="refresh" content="0; url=index.php">';
        exit();
    }

    if ($_SESSION['admin_jog'] == 2)
    {
        print '<meta http-equiv="refresh" content="0; url=admin.php">';
        exit();
    }

    print '<h3>Ügyfelek kezelése</h3>';




    if (isset($_POST['modositas_mentes_gomb']))
    {
        $ugyfelid = mysqli_real_escape_string($l, $_POST['ugyfelid']);
        $vezeteknev = mysqli_real_escape_string($l, $_POST['vezeteknev']);
        $keresztnev = mysqli_real_escape_string($l, $_POST['keresztnev']);
        $email = mysqli_real_escape_string($l, $_POST['email']);
        $telefon = mysqli_real_escape_string($l, $_POST['telefon']);

        $van_e_lek = mysqli_query($l, "SELECT `id` FROM `ugyfel` WHERE `email` = '".$email."' AND `id` <> '".$ugyfelid."'");
        $db = mysqli_num_rows($van_e_lek);

        if ($db == 0)
        {
            mysqli_query($l, "UPDATE `ugyfel` SET
                                        `vezeteknev` = '".$vezeteknev."',
                                        `keresztnev` = '".$keresztnev."',
                                        `email` = '".$email."',
                                        `telefon` = '".$telefon."'
                                        WHERE `id` = '".$ugyfelid."'");

            print '<p class="bg-success text-white">Sikeres módosítás!</p>';

        }
        else
        {
            print '<p class="bg-danger text-white">Ez az email cím egy másik ügyfélhez tartozik!</p>';
        }
    }



    if (isset($_POST['torles_gomb']))
    {
        $ugyfelid = mysqli_real_escape_string($l, $_POST['ugyfelid']);
        $lek = mysqli_query($l, "SELECT * FROM `ugyfel` WHERE `id` = '".$ugyfelid."'");
        $adat = mysqli_fetch_array($lek);

        print '<h3>Biztosan törli a következő ügyfelet: '.$adat['vezeteknev'].' '.$adat['keresztnev'].' ('.$adat['email'].')?</h3>';
        print '<form method="post">
                   <input type="hidden" name="ugyfelid" value="'.$ugyfelid.'">
                   <input type="submit" name="megerositett_torles_gomb" value="Igen" class="btn btn-danger">
                   <input type="submit" name="nem_gomb" value="Nem" class="btn btn-primary">
               </form>';

    }


    if (isset($_POST['megerositett_torles_gomb']))
    {
        $ugyfelid = mysqli_real_escape_string($l, $_POST['ugyfelid']);
        mysqli_query($l, "DELETE FROM `ugyfel` WHERE `id` = '".$ugyfelid."'");

        print '<p class="bg-success text-white">Sikeres törlés!</p>';
    }







    print 'Ügyfél keresése';    //datalist-el
    print '<form method="post">
               <input  name="ugyfel" list="ugyfel_lista" onchange="this.form.submit()">
               
               <datalist id="ugyfel_lista">';
                    $ugyfel_lek = mysqli_query($l, "SELECT * FROM `ugyfel` ORDER BY `vezeteknev`, `keresztnev`");
                    while ($ugyfel = mysqli_fetch_array($ugyfel_lek))
                    {
                        print '<option value="'.$ugyfel['vezeteknev'].' '.$ugyfel['keresztnev'].' ('.$ugyfel['email'].') / '.$ugyfel['id'].'">';
                    }
    print '    </datalist>
           </form>';

    if (isset($_POST['ugyfel']))
    {
        $ugyfel = mysqli_real_escape_string($l, $_POST['ugyfel']);
        $per_jel = strpos($ugyfel, '/');
        $ugyfelid = substr($ugyfel, ($per_jel+2));

        $lek = mysqli_query($l, "SELECT * FROM `ugyfel` WHERE `id` = '".$ugyfelid."'");
        $adat = mysqli_fetch_array($lek);

        print '<br><br>
               <form method="post">
                   Vezetéknév: <input type="text" name="vezeteknev" value="'.$adat['vezeteknev'].'" class="form-control" required>
                   Keresztnév: <input type="text" name="keresztnev" value="'.$adat['keresztnev'].'" class="form-control" required>
                   E-mail cím: <input type="email" name="email" value="'.$adat['email'].'" class="form-control" required>
                   Telefonszám: <input type="text" name="telefon" value="'.$adat['telefon'].'" class="form-control" required>
                   
                   <br>
                   <input type="hidden" name="ugyfelid" value="'.$ugyfelid.'">
                   <input type="submit" name="modositas_mentes_gomb" value="Módosítás mentése" class="btn btn-success">
                   
                   <br><br>
                   <input type="submit" name="torles_gomb" value="Ügyfél végleges törlése" class="btn btn-danger">
               </form>';

    }

?>
