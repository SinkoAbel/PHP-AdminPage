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




    /////////////////////////////////////////////// Galéria kategóriák START //////////////////////////////////////////////////


    if (!isset($_POST['belepes_gomb']))
    {
    print '<br><h3>Galéria kezelése</h3>';
    print '<div class="col-md-3 box1 text-center">
               <h4>Új kategória felvétele</h4>
               <form method="post">
                   <input type="text" name="nev" placeholder="Új kategória neve:" class="form-control" required>
                   <input type="submit" name="uj_kategoria_mentes_gomb" value="Új kategória felvétele" class="btn btn-success">
               </form>
           </div>
           <br><br><br>';
    }

    if (isset($_POST['uj_kategoria_mentes_gomb']))
    {
        $nev = mysqli_real_escape_string($l, $_POST['nev']);
        $van_e_ilyen = mysqli_query($l, "SELECT `nev` FROM `galeria_kat` WHERE `nev` = '".$nev."'");
        $db = mysqli_num_rows($van_e_ilyen);

        if ($db == 0)
        {
            $max_sorrend_lek = mysqli_query($l, "SELECT `sorrend` FROM `galeria_kat` ORDER BY `sorrend` DESC LIMIT 1");
            $max_sorrend = mysqli_fetch_array($max_sorrend_lek);
            $uj_sorrend = $max_sorrend['sorrend'] + 1;

            mysqli_query($l, "INSERT INTO `galeria_kat` SET 
                        `id` = NULL,
                        `nev` = '".$nev."',
                        `sorrend` = '".$uj_sorrend."',
                        `statusz` = 'inaktív'");

            print '<p class="bg-success text-white">Sikeres mentés!</p>';

        }
        else
        {
            print '<p class="bg-danger text-white">Már létezik ilyen nevű galéria kategória!</p>';
        }

    }



    /////// FEL - LE GOMBOK  START///////
    if (isset($_POST['kategoria_fel_gomb']))
    {
        $id = mysqli_real_escape_string($l, $_POST['id']);
        $jelenlegi_sorrend_lek = mysqli_query($l, "SELECT `sorrend` FROM `galeria_kat` WHERE `id` = '".$id."'");
        $jelenlegi_sorrend = mysqli_fetch_array($jelenlegi_sorrend_lek);

        if ($jelenlegi_sorrend['sorrend'] > 1)
        {
            $elozo_modositasa = mysqli_query($l, "UPDATE `galeria_kat` SET `sorrend` = '".$jelenlegi_sorrend['sorrend']."' WHERE `sorrend` ='".($jelenlegi_sorrend['sorrend']-1)."'");
            $jelenlegi_modositasa = mysqli_query($l, "UPDATE `galeria_kat` SET `sorrend` = '".($jelenlegi_sorrend['sorrend']-1)."' WHERE `id` = '".$id."'");
        }
    }


    if (isset($_POST['kategoria_le_gomb']))
    {
        $id = mysqli_real_escape_string($l, $_POST['id']);
        $jelenlegi_sorrend_lek = mysqli_query($l, "SELECT `sorrend` FROM `galeria_kat` WHERE `id` = '".$id."'");
        $jelenlegi_sorrend = mysqli_fetch_array($jelenlegi_sorrend_lek);

        $max_sorrend_lek = mysqli_query($l, "SELECT `sorrend` FROM `galeria_kat` ORDER BY `sorrend` DESC LIMIT 1");
        $max_sorrend = mysqli_fetch_array($max_sorrend_lek);

        if ($jelenlegi_sorrend['sorrend'] < $max_sorrend['sorrend'])
        {
            $kovetkezo_modositasa = mysqli_query($l, "UPDATE `galeria_kat` SET `sorrend` = '".$jelenlegi_sorrend['sorrend']."' WHERE `sorrend` = '".($jelenlegi_sorrend['sorrend']+1)."'");
            $jelenlegi_modositasa = mysqli_query($l, "UPDATE `galeria_kat` SET `sorrend` = '".($jelenlegi_sorrend['sorrend']+1)."' WHERE `id` = '".$id."'");
        }
    }
    /////// FEL - LE GOMBOK  END///////



    if (isset($_POST['modositas_gomb']))
    {
        $id = mysqli_real_escape_string($l, $_POST['id']);
        $jelenlegi_lek = mysqli_query($l, "SELECT * FROM `galeria_kat` WHERE `id` = '".$id."'");
        $jelenlegi = mysqli_fetch_array($jelenlegi_lek);


        print '<div class="col-md-3 box1 text-center">
               <h4>Kategória módosítása</h4>
               <form method="post">
                   <input type="hidden" name="id" value="'.$id.'">
                   <input type="text" name="nev" value="'.$jelenlegi['nev'].'" class="form-control" required>
                   <input type="submit" name="kategoria_modositas_mentes_gomb" value="Módosítás mentése" class="btn btn-success">
               </form>
           </div>
           <br><br><br>';
    }


    if (isset($_POST['kategoria_modositas_mentes_gomb']))
    {
        $id = mysqli_real_escape_string($l, $_POST['id']);
        $nev = mysqli_real_escape_string($l, $_POST['nev']);

        $van_e_ilyen = mysqli_query($l, "SELECT `nev` FROM `galeria_kat` WHERE `nev` = '".$nev."'");
        $db = mysqli_num_rows($van_e_ilyen);
        if ($db == 0)
        {
            mysqli_query($l,"UPDATE `galeria_kat` SET `nev` = '".$nev."' WHERE `id` = '".$id."'");

            print '<p class="bg-success text-white">Sikeres módosítás!</p>';
        }
        else
        {
            print '<p class="bg-danger text-white">Már létezik ilyen nevű galéria kategória!</p>';
        }

    }


    if (isset($_POST['statusz']))
    {
        $id = mysqli_real_escape_string($l, $_POST['id']);
        $statusz = mysqli_real_escape_string($l, $_POST['statusz']);
        mysqli_query($l, "UPDATE `galeria_kat` SET `statusz` = '".$statusz."' WHERE `id` = '".$id."'");

    }



    if (isset($_POST['torles_gomb'])) //A teljes kategória törlése
    {
        $katid = mysqli_real_escape_string($l, $_POST['katid']);

        $kat_lek = mysqli_query($l, "SELECT * FROM `galeria_kat` WHERE `id` = '".$katid."'");
        $kat = mysqli_fetch_array($kat_lek);

        print '<div class="col-md-6 box1 text-center">
                       <h3>Biztosan törli a(z) '.$kat['nev'].' nevű kategóriát és a benne lévő összes képet?</h3>
                       
                       <form method="post">
                           <input type="hidden" name="katid" value="'.$katid.'">
                           
                           <input type="submit" name="megerositett_kategoria_torles_gomb" value="Igen törlöm" class="btn btn-danger">
                           <input type="submit" name="nem_gomb" value="Mégsem" class="btn btn-primary">
                       </form>
                       
                   </div>';
    }


    if (isset($_POST['megerositett_kategoria_torles_gomb']))
    {
        $katid = mysqli_real_escape_string($l, $_POST['katid']);

        //A benne lévő képek fizikai törlése
        $kepek_lek = mysqli_query($l, "SELECT * FROM `galeria` WHERE `katid` = '".$katid."'");
        while ($kepek = mysqli_fetch_array($kepek_lek))
        {
            unlink("../" . $kepek['kep']);
        }

        // Képek törlése AB-ból
        mysqli_query($l, "DELETE FROM `galeria` WHERE `katid` = '".$katid."'");

        //A mögötte lévő kategóriák egyel előre lépnek
        $jelenlegi_sorrend_lek = mysqli_query($l, "SELECT `sorrend` FROM `galeria_kat` WHERE `id` = '".$katid."'");
        $jelenlegi_sorrend = mysqli_fetch_array($jelenlegi_sorrend_lek);

        mysqli_query($l, "UPDATE `galeria_kat` SET `sorrend` = `sorrend`-1 WHERE `sorrend` > '".$jelenlegi_sorrend['sorrend']."'");

        //Kategória törlése
        mysqli_query($l, "DELETE FROM `galeria_kat` WHERE `id` = '".$katid."'");

    }





    if (isset($_POST['belepes_gomb']))  //ha belépett valamelyik kategóriába
    {
        $katid = mysqli_real_escape_string($l, $_POST['katid']);
        $jelenlegi_lek = mysqli_query($l, "SELECT * FROM `galeria_kat` WHERE `id` = '".$katid."'");
        $jelenlegi = mysqli_fetch_array($jelenlegi_lek);


        print '<br><h3>'.$jelenlegi['nev'].' képeinek kezelése</h3>';

        print '<div class="col-md-3 box1 text-center">
               <h4>Új képek feltöltése</h4>
               <form method="post" enctype="multipart/form-data">
                   <input type="hidden" name="katid" value="'.$katid.'">
                   <input type="hidden" name="belepes_gomb" value="x">  <!-- Imitálás, hogy ne lépjen ki a galéria kezelés főoldalra a kép feltöltése után -->
                   <input type="file" name="kepek[]" class="form-control" multiple> <!-- A multiple miatt lehet több képet feltölteni -->
                   <input type="submit" name="uj_kepek_mentes_gomb" value="Képek feltöltése" class="btn btn-success">
               </form>
           </div>
           <br><br><br>';





        if (isset($_POST['uj_kepek_mentes_gomb']))
        {
            $kepdb = count($_FILES['kepek']['name']);

            for ($i=0; $i<$kepdb; $i++)
            {
                if (is_uploaded_file($_FILES['kepek']['tmp_name'][$i]))
                {
                    if ($_FILES['kepek']['type'][$i] == "image/jpg" or $_FILES['kepek']['type'][$i] == "image/jpeg")
                    {
                        //A "../" azt jelenti, hogy a mappából egy szintet visszalépünk"
                        $filenev = "../galeria_kepek/" . "galeria-" . date("Y-m-d-H-i-s") . mt_rand(1, 9999999) . ".jpg";

                        //fizikai mentése a megfelelő helyre ÁTMÉRETEZÉS UTÁN:
                        $maxmeret = 500;
                        kepgenerator($_FILES['kepek']['tmp_name'][$i], $maxmeret, $filenev);



                        mysqli_query($l, "UPDATE `galeria` SET `sorrend`=`sorrend`+1 WHERE `katid` = '".$katid."'");


                        $filenev = substr($filenev, 3);
                        //betöltés:
                        mysqli_query($l,"INSERT INTO `galeria` SET 
                                        `id` = NULL,
                                        `katid` = '".$katid."',
                                        `kep` = '".$filenev."',
                                        `sorrend` = '1'
                        ");

                    }
                }
            }
        }


        if (isset($_POST['kep_torles_gomb']))
        {
            $katid = mysqli_real_escape_string($l, $_POST['katid']);
            $kepid = mysqli_real_escape_string($l, $_POST['kepid']);

            $kep_lek = mysqli_query($l, "SELECT * FROM `galeria` WHERE `id` = '".$kepid."'");
            $kep = mysqli_fetch_array($kep_lek);

            print '<div class="col-md-6 box1 text-center">
                       <h3>Biztosan törli az alábbi képet?</h3>
                       <img src="../'.$kep['kep'].'" class="img-fluid">
                       
                       <form method="post">
                           <input type="hidden" name="katid" value="'.$katid.'">
                           <input type="hidden" name="belepes_gomb" value="x">
                           <input type="hidden" name="kepid" value="'.$kepid.'">
                           
                           <input type="submit" name="megerositett_kep_torles_gomb" value="Igen törlöm" class="btn btn-danger">
                           <input type="submit" name="nem_gomb" value="Mégsem" class="btn btn-primary">
                       </form>
                       
                   </div>';

        }

        if (isset($_POST['megerositett_kep_torles_gomb']))
        {
            $katid = mysqli_real_escape_string($l, $_POST['katid']);
            $kepid = mysqli_real_escape_string($l, $_POST['kepid']);

            $torlendo_lek = mysqli_query($l, "SELECT * FROM `galeria` WHERE `id`='".$kepid."'");
            $torlendo = mysqli_fetch_array($torlendo_lek);

            mysqli_query($l, "UPDATE `galeria` SET `sorrend`=`sorrend`-1 WHERE `katid` = '".$katid."' AND `sorrend`>'".$torlendo['sorrend']."'");

            //a kép fizikai törlése
            unlink("../".$torlendo['kep']);

            // törlés AB-ból
            mysqli_query($l, "DELETE FROM `galeria` WHERE `id` = '".$kepid."'");


        }


        if (isset($_POST['uj_sorrend']))
        {
            $katid = mysqli_real_escape_string($l, $_POST['katid']);
            $kepid = mysqli_real_escape_string($l, $_POST['kepid']);
            $uj_sorrend = mysqli_real_escape_string($l, $_POST['uj_sorrend']);

            $jelenlegi_sorrend_lek = mysqli_query($l, "SELECT `sorrend` FROM `galeria` WHERE `id`='".$kepid."'");
            $jelenlegi_sorrend = mysqli_fetch_array($jelenlegi_sorrend_lek);

            //Ha előrébb akarjuk vinni (felfelé mozog)
            if ($jelenlegi_sorrend['sorrend'] > $uj_sorrend)
            {
                mysqli_query($l, "UPDATE `galeria` SET `sorrend` = `sorrend`+1 WHERE `katid` = '".$katid."' AND `sorrend` < '".$jelenlegi_sorrend['sorrend']."' AND `sorrend` >= '".$uj_sorrend."'");

                mysqli_query($l, "UPDATE `galeria` SET `sorrend` = '".$uj_sorrend."' WHERE `id` = '".$kepid."'");
            }
            else
            {
                mysqli_query($l, "UPDATE `galeria` SET `sorrend` = `sorrend`-1 WHERE `katid` = '".$katid."' AND `sorrend` > '".$jelenlegi_sorrend['sorrend']."' AND `sorrend` <= '".$uj_sorrend."'");
                mysqli_query($l, "UPDATE `galeria` SET `sorrend` = '".$uj_sorrend."' WHERE `id` = '".$kepid."'");

            }


        }



        //Ebben a kategóriában lévő képek listázása
        print '<br><br><h4>A '.$jelenlegi['nev'].' kategória képei:</h4>';

        $kepek_lek = mysqli_query($l, "SELECT * FROM `galeria` WHERE `katid` = '".$katid."' ORDER BY `sorrend`");
        while ($kepek = mysqli_fetch_array($kepek_lek))
        {
            print '<div class="row">
                           <div class="col-md-3">
                           <img src="../'.$kepek['kep'].'" class="img-fluid">
                       </div>
                       <div class="col-md-3">
                           <form method="post">
                               <input type="hidden" name="katid" value="'.$katid.'">
                               <input type="hidden" name="belepes_gomb" value="x">
                               <input type="hidden" name="kepid" value="'.$kepek['id'].'">
                               
                               Sorrendcsere:
                               <select name="uj_sorrend" onchange="this.form.submit()">';
                                   $osszes_lek = mysqli_query($l, "SELECT `id` FROM `galeria` WHERE `katid` = '".$katid."'");
                                   $osszes = mysqli_num_rows($osszes_lek);
                                   for ($i=1; $i<=$osszes; $i++)
                                   {
                                       if ($i == $kepek['sorrend'])
                                       {
                                           print'<option selected>'.$i.'</option>';
                                       }
                                       else
                                       {
                                           print'<option>'.$i.'</option>';
                                       }
                                   }


            print '            </select>
                               
                           
                               <input type="submit" name="kep_torles_gomb" value="Kép törlése" class="btn btn-danger">
                           </form>
                       </div>
                   </div>';
        }




    }
    else
    {

        // Kategóriák listázás

        print '<table class="table">
                   <thead>
                       <th>Kategória</th>
                       <th>Műveletek</th>
                   </thead>';

        $lek = mysqli_query($l, "SELECT * FROM `galeria_kat` ORDER BY `sorrend`");
        while ($kat = mysqli_fetch_array($lek))
        {
            print '<tr>
                       <td>'.$kat['nev'].'</td>
                       <td>
                           <form method="post">
                               <input type="hidden" name="id" value="'.$kat['id'].'">
                               <input type="hidden" name="katid" value="'.$kat['id'].'">
                               <input type="submit" name="belepes_gomb" value="Belépés" class="btn btn-success">
                               <input type="submit" name="kategoria_fel_gomb" value="&Lambda;" class="btn btn-warning">
                               <input type="submit" name="kategoria_le_gomb" value="V" class="btn btn-warning">
                               <input type="submit" name="modositas_gomb" value="Módosítás" class="btn btn-primary">
                               <input type="submit" name="torles_gomb" value="Törlés" class="btn btn-danger">';

            if ($kat['statusz'] == 'aktív')
            {
                print '<select name="statusz" onchange="this.form.submit()">
                                               <option>aktív</option>
                                               <option>inaktív</option>
                       </select>';

            }
            else
            {
                print '<select name="statusz" onchange="this.form.submit()">
                                               <option>inaktív</option>
                                               <option>aktív</option>
                       </select>';
            }



        print'             </form>
                       </td>
                   </tr>';
        }
        print '</table>';

    }
    /////////////////////////////////////////////// Galéria kategóriák END //////////////////////////////////////////////////





?>
