<?php

session_start();


    if ($_SESSION['admin_belepett'] != 'igen')
    {
        print '<meta http-equiv="refresh" content="0; url=index.php">';
        exit();
    }

    if ($_SESSION['admin_jog'] == 3)
    {
        print '<meta http-equiv="refresh" content="0; url=admin.php">';
        exit();
    }

    print '<h3>Kezdőlap kezelése</h3>';




    if (!isset($_POST['uj_cikk_gomb']))
    {
        print '<form method="post">
                    <input type="submit" name="uj_cikk_gomb" value="Új cikk felvétele" class="btn btn-primary">
               </form>';
    }


    if (isset($_POST['uj_cikk_gomb']))
    {
        print '<div class="col-md-12 box1">
                    <h4>Új cikk rögzítése</h4>
                    <form method="post" enctype="multipart/form-data">
                        <input type="text" name="cim" placeholder="Cím:" class="form-control">
                        
                        <textarea name="szoveg" class="ckeditor">A cikk szövege</textarea>
                        <br><br>
                        Kép (csak JPG file tölthető fel): <input type="file" name="kep" class="form-control">
                        
                                               
                        <br><br>
                        <input type="submit" name="uj_cikk_mentes_gomb" value="Új cikk mentése" class="btn btn-success">
                    </form>
                </div>';
    }

    if (isset($_POST['uj_cikk_mentes_gomb']))
    {
        $cim = mysqli_real_escape_string($l, $_POST['cim']);
        $szoveg = mysqli_real_escape_string($l, $_POST['szoveg']);
        $datum = date("Y-m-d");
        $statusz = 'aktiv';

        $max_sorrend_lek = mysqli_query($l, "SELECT `sorrend` FROM `kezdolap` ORDER BY `sorrend` DESC LIMIT 1");
        $max_sorrend = mysqli_fetch_array($max_sorrend_lek);
        $uj_sorrend = $max_sorrend['sorrend'] + 1;

        mysqli_query($l, "INSERT INTO `kezdolap` SET 
        `id`=NULL,
        `cim`='".$cim."',
        `szoveg`='".$szoveg."',
        `datum`='".$datum."',
        `sorrend`='".$uj_sorrend."',
        `statusz`='".$statusz."'");


        //KÉP FELDOLGOZÁSA
        foreach ($_FILES as $kep)
        {
            if (is_uploaded_file($kep['tmp_name']))
            {
                if ($kep['type'] == "image/jpg" or $kep['type'] == "image/jpeg")
                {
                    $filenev = "../feltoltott_kepek/" . "kezdolapkep-" . date("Y-m-d-H-i-s") . mt_rand(1, 9999999) . ".jpg";

                    //fizikai mentése a megfelelő helyre
                    move_uploaded_file($kep['tmp_name'], $filenev);

                    // Adatbázisba mentés
                    $filenev = substr($filenev, 3);
                    $utolso_id = mysqli_insert_id($l);
                    //betöltés:
                    mysqli_query($l,"UPDATE `kezdolap` SET `kep`='".$filenev."' WHERE `id` = '".$utolso_id."'");

                }
            }
        }

        print '<p class="bg-success text-white">Sikeres mentés</p>';

    }



    //gombok kezelése

    if (isset($_POST['fel_gomb']))
    {
        $id = mysqli_real_escape_string($l, $_POST['id']);
        $jelenlegi_sorrend_lek = mysqli_query($l, "SELECT `sorrend` FROM `kezdolap` WHERE `id` = '".$id."'");
        $jelenlegi_sorrend = mysqli_fetch_array($jelenlegi_sorrend_lek);
        if ($jelenlegi_sorrend['sorrend'] > 1)
        {
            $elozo_modositasa = mysqli_query($l, "UPDATE `kezdolap` SET `sorrend` = '".$jelenlegi_sorrend['sorrend']."' WHERE `sorrend` = '".($jelenlegi_sorrend['sorrend']-1)."'");

            $jelegi_uj_erteke = mysqli_query($l, "UPDATE `kezdolap` SET `sorrend` = '".($jelenlegi_sorrend['sorrend']-1)."' WHERE `id` = '".$id."'");
        }

    }

    if (isset($_POST['le_gomb']))
    {
        $id = mysqli_real_escape_string($l, $_POST['id']);
        $jelenlegi_sorrend_lek = mysqli_query($l, "SELECT `sorrend` FROM `kezdolap` WHERE `id` = '".$id."'");
        $jelenlegi_sorrend = mysqli_fetch_array($jelenlegi_sorrend_lek);


        $max_sorrend_lek = mysqli_query($l, "SELECT `sorrend` FROM `kezdolap` ORDER BY `sorrend` DESC LIMIT 1");
        $max_sorrend = mysqli_fetch_array($max_sorrend_lek);



        if ($jelenlegi_sorrend['sorrend'] < $max_sorrend['sorrend'])
        {
            $kovetkezo_modositasa = mysqli_query($l, "UPDATE `kezdolap` SET `sorrend` = '".$jelenlegi_sorrend['sorrend']."' WHERE `sorrend` = '".($jelenlegi_sorrend['sorrend']+1)."'");

            $jelegi_uj_erteke = mysqli_query($l, "UPDATE `kezdolap` SET `sorrend` = '".($jelenlegi_sorrend['sorrend']+1)."' WHERE `id` = '".$id."'");
        }

    }

    if (isset($_POST['torles_gomb']))
    {
        $id = mysqli_real_escape_string($l, $_POST['id']);
        $cikk_lek = mysqli_query($l, "SELECT * FROM `kezdolap` WHERE `id` = '".$id."'");
        $cikk = mysqli_fetch_array($cikk_lek);

        if (is_file('../' . $cikk['kep']));
        {
            unlink('../' . $cikk['kep']);
        }

        mysqli_query($l, "UPDATE `kezdolap` SET `sorrend` = `sorrend`-1 WHERE `sorrend` > '".$cikk['sorrend']."'");


        mysqli_query($l, "DELETE FROM `kezdolap` WHERE  `id` = '".$id."'");

    }

    if (isset($_POST['statusz']))
    {
        $id = mysqli_real_escape_string($l, $_POST['id']);
        $statusz = mysqli_real_escape_string($l, $_POST['statusz']);

        mysqli_query($l, "UPDATE `kezdolap` SET `statusz` = '".$statusz."' WHERE `id` = '".$id."' ");

    }


    if (isset($_POST['modositas_gomb']))
    {
        $id = mysqli_real_escape_string($l, $_POST['id']);
        $cikk_lek = mysqli_query($l, "SELECT * FROM `kezdolap` WHERE `id` = '".$id."'");
        $cikk = mysqli_fetch_array($cikk_lek);

        print '<div class="col-md-12 box1">
                    <h4>Cikk módosítása</h4>
                    <form method="post" enctype="multipart/form-data"> 
                        <input type="text" name="cim" value="'.$cikk['cim'].'" class="form-control">
                        
                        <textarea name="szoveg" class="ckeditor">'.$cikk['szoveg'].'</textarea>
                        <br><br>
                        Jelenlegi kép<br>
                        <img src="../'.$cikk['kep'].'" width="250">
                        
                        <br><br>
                        Kép cseréje(csak JPG file tölthető fel): <input type="file" name="kep" class="form-control">
                        
                                               
                        <br><br>
                        <input type="hidden" name="id" value="'.$id.'">
                        <input type="submit" name="modositas_mentese_gomb" value="Cikk módosítása" class="btn btn-success">
                    </form>
                </div>';
    }


    if (isset($_POST['modositas_mentese_gomb']))
    {
        $id = mysqli_real_escape_string($l, $_POST['id']);
        $cim = mysqli_real_escape_string($l, $_POST['cim']);
        $szoveg = mysqli_real_escape_string($l, $_POST['szoveg']);
        $datum = date("Y-m-d");

        mysqli_query($l, "UPDATE `kezdolap` SET 
                      `cim` = '".$cim."',
                      `szoveg` = '".$szoveg."',
                      `datum` = '".$datum."'
                      WHERE `id` = '".$id."'");


        //Ha volt kép változtatás
        foreach ($_FILES as $kep)
        {
            if (is_uploaded_file($kep['tmp_name']))
            {
                if ($kep['type'] == "image/jpg" or $kep['type'] == "image/jpeg")
                {

                    //mielőtt updateljük az ab-ban, előtte a régi képet ki kell törölni a tárhelyről
                    $jelenlegi_lek = mysqli_query($l, "SELECT `kep` FROM `kezdolap` WHERE `id` = '".$id."'");
                    $jelenlegi = mysqli_fetch_array($jelenlegi_lek);
                    if (is_file('../'.$jelenlegi['kep']))
                    {
                        unlink('../'.$jelenlegi['kep']);
                    }


                    $filenev = "../feltoltott_kepek/" . "kezdolapkep-" . date("Y-m-d-H-i-s") . mt_rand(1, 9999999) . ".jpg";

                    //fizikai mentése a megfelelő helyre
                    move_uploaded_file($kep['tmp_name'], $filenev);

                    //az AB-ba is menteni kell a képet
                    $filenev = substr($filenev, 3);
                    mysqli_query($l,"UPDATE `kezdolap` SET `kep`='".$filenev."' WHERE `id` = '".$id."'");

                }
            }
        }

        print '<p class="bg-success text-white">Sikeres mentés</p>';



    }



    // Cikkek listázása

    print '<table class="table">
                <thead>
                    <th>Cím</th>
                    <th>Dátum</th>
                    <th>Státusz</th>
                </thead>';


    $lek = mysqli_query($l, "SELECT * FROM `kezdolap` ORDER BY `sorrend`");
    while ($cikk = mysqli_fetch_array($lek))
    {

        print '<tr>';
        print '<td>'.$cikk['cim'].'</td>';
        print '<td>'.$cikk['datum'].'</td>';
        print '<td>
                   <form method="post">
                        <input type="hidden" name="id" value="'.$cikk['id'].'">
                        <select name="statusz" onchange="this.form.submit()">';
                        if ($cikk['statusz'] == 'aktív')
                        {
                            print '<option>aktív</option>';
                            print '<option>inaktív</option>';
                        }
                        else
                        {
                            print '<option>inaktív</option>';
                            print '<option>aktív</option>';
                        }
        print'          </select>
                   </form>
               </td>';

        print '<td>
                    <form method="post">
                        <input type="hidden" name="id" value="'.$cikk['id'].'"> <!-- ha megnyomja a cikk melleti gombot akkor az a gomb annak a cikknek az id-ját küldjük tovább -->
                        <input type="submit" name="fel_gomb" value="&Lambda;"> <!-- & ezzel speciális jeleket lehet rakni: copyright -> &copy -->
                        <input type="submit" name="le_gomb" value="V">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <!-- none breakable space, szóközöket jelöl -->
                        <input type="submit" name="modositas_gomb" value="Módosítás" class="btn btn-success">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="submit" name="torles_gomb" value="Törlés" class="btn btn-danger">
                    </form> 
               </td>';



        print '</tr>';

    }

    print '</table>';

?>
