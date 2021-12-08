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


    if (isset($_POST['torles_gomb']))
    {
        $foglalas_id = mysqli_real_escape_string($l, $_POST['foglalas_id']);

        $foglalas_lek = mysqli_query($l, "SELECT * FROM `foglalas` WHERE `id` = '".$foglalas_id."'");
        $foglalas = mysqli_fetch_array($foglalas_lek);

        print '<h3>Biztosan törli a(z) '.$foglalas['rendszam'].' rendszámú autó foglalását?</h3>';

        print '<form method="post">
                   <input type="hidden" name="foglalas_id" value="'.$foglalas_id.'">
                   <input type="submit" name="megerositett_torles_gomb" value="Igen" class="btn btn-danger">
                   <input type="submit" name="nem_gomb" value="Nem" class="btn btn-primary">
               </form>';
    }



    if (isset($_POST['megerositett_torles_gomb']))
    {
        $foglalas_id = mysqli_real_escape_string($l, $_POST['foglalas_id']);

        mysqli_query($l, "DELETE FROM `igenyelt_muveletek` WHERE `foglalas_id` = '".$foglalas_id."'");
        mysqli_query($l, "UPDATE `naptar` SET `foglalas_id` = '0' WHERE `foglalas_id` = '".$foglalas_id."'");
        mysqli_query($l, "DELETE FROM `foglalas` WHERE `id` = '".$foglalas_id."'");

        print '<p class="bg-success text-white">Sikeres törlés!</p>';

    }



    print '<br><h3>Foglalások kezelése</h3><br><br>';

    $ma = date("Y-m-d");
    $kov_honap = date("Y-m-d", strtotime($ma . "+30 day"));  //1 hónapnyi adat a lekérdezésehez


    print '
        <div class="row">
           <div class="col-md-3 box1">
               Keresés dátum alapján
               <form method="post">
                   <input name="kereses_datum" list="datum_lista" onchange="this.form.submit()">
                   <datalist id="datum_lista">';
                        $datum_lek = mysqli_query($l, "SELECT `nap` FROM `naptar` WHERE `nap` >= '".$ma."' AND `nap`<= '".$kov_honap."' GROUP BY `nap`");
                        while ($datum = mysqli_fetch_array($datum_lek))
                        {
                            print '<option value="'.str_replace("-", ".", $datum['nap']).'">';
                        }
    print '        </datalist>
               </form>
           </div>
           
           
           
           <div class="col-md-3 box1">
               Keresés szerelési művelet alapján
               <form method="post">
                   <input name="kereses_muvelet" list="muvelet_lista" onchange="this.form.submit()">
                   <datalist id="muvelet_lista">';
                        $muvelet_lek = mysqli_query($l, "SELECT * FROM `muveletek` ORDER BY `nev`");
                        while ($muvelet = mysqli_fetch_array($muvelet_lek))
                        {
                            print '<option value="'.$muvelet['nev'].'">';
                        }
print '            </datalist>
               </form>
           </div>        
        </div>';


    if (isset($_POST['kereses_muvelet']))
    {
        $kereses_muvelet = mysqli_real_escape_string($l, $_POST['kereses_muvelet']);
        $muvelet_lek = mysqli_query($l, "SELECT * FROM `muveletek` WHERE `nev` = '".$kereses_muvelet."'");
        $muvelet = mysqli_fetch_array($muvelet_lek);

        $igenyelt_muveletek_lek_osszes = mysqli_query($l, "SELECT * FROM `igenyelt_muveletek` WHERE `muvelet_id` = '".$muvelet['id']."'");
        while ($igenyelt_muveletek_osszes = mysqli_fetch_array($igenyelt_muveletek_lek_osszes))
        {
            $ma = date("Y-m-d");
            $datum_lek = mysqli_query($l, "SELECT `nap` FROM `naptar` WHERE `nap` >= '".$ma."' AND `foglalas_id` = '".$igenyelt_muveletek_osszes['foglalas_id']."' GROUP BY `nap`");

            while ($datum = mysqli_fetch_array($datum_lek))
            {
                print '<table class="table">
                    <tr class="datum_sor">
                        <td colspan="6" class="text-center">'.str_replace("-", ". ", $datum['nap']).'</td>
                    </tr>
                    <tr class="cim_sor">
                        <td>Időpont</td>
                        <td>Ügyfél név</td>
                        <td>Rendszám</td>
                        <td>Megjegyzés</td>
                        <td>Műveletek</td>
                        <td></td>
                    </tr>';


                $idopont_lek = mysqli_query($l, "SELECT * FROM `naptar` WHERE `nap` = '".$datum['nap']."' AND `foglalas_id` > '0' ORDER BY `ido`");
                while ($idopont = mysqli_fetch_array($idopont_lek))
                {
                    $foglalas_lek = mysqli_query($l, "SELECT * FROM `foglalas` WHERE `id` = '".$idopont['foglalas_id']."'");
                    $foglalas = mysqli_fetch_array($foglalas_lek);

                    $ugyfel_lek = mysqli_query($l, "SELECT * FROM `ugyfel` WHERE `id` = '".$foglalas['ugyfel_id']."'");
                    $ugyfel = mysqli_fetch_array($ugyfel_lek);


                    print '<tr>';
                    print '    <td>'.substr($idopont['ido'], 0, 5).'</td>';
                    print '    <td>'.$ugyfel['vezeteknev'].' '.$ugyfel['keresztnev'].'</td>';
                    print '    <td>'.$foglalas['rendszam'].'</td>';
                    print '    <td>'.$foglalas['megjegyzes'].'</td>';

                    print '    <td>';
                    $igenyelt_muveletek_lek = mysqli_query($l, "SELECT * FROM `igenyelt_muveletek` WHERE  `foglalas_id` = '".$foglalas['id']."'");
                    while ($igenyelt_muveletek = mysqli_fetch_array($igenyelt_muveletek_lek))
                    {
                        $muvelet_lek = mysqli_query($l, "SELECT * FROM `muveletek` WHERE `id` = '".$igenyelt_muveletek['muvelet_id']."'");
                        $muvelet = mysqli_fetch_array($muvelet_lek);
                        print $muvelet['nev'] . '<br>';
                    }

                    print '    </td>';

                    print '<td>
                       <form method="post">
                           <input type="hidden" name="foglalas_id" value="'.$foglalas['id'].'">
                           <input type="submit" name="torles_gomb" value="Foglalás törlése" class="btn btn-danger">
                       </form>

                   </td>';
                    print '</tr>';

                }

                print'     </table>';

            }

        }
    }






    if(isset($_POST['kereses_datum']))
    {
        $datum = mysqli_real_escape_string($l, $_POST['kereses_datum']);

        $van_e_foglalas = mysqli_query($l, "SELECT * FROM `naptar` WHERE `nap` = '".$datum."' AND `foglalas_id` > '0'");
        $db = mysqli_num_rows($van_e_foglalas);
        if ($db > 0)
        {
            $van_datum_keresesi_eredmeny = true;
        }
        else
        {
            print '<h4>A megadott dátumra még nincs foglalás!</h4>';
        }
    }



    // Ha kerestünk és van eredmény, akkor más lesz az SQL lekérdezés!
    if ($van_datum_keresesi_eredmeny == true)
    {
        $datum_lek = mysqli_query($l, "SELECT `nap` FROM `naptar` WHERE `nap` = '".$datum."' GROUP BY `nap`");

    }
    elseif (!isset($_POST['kereses_muvelet']))  //minden foglalást ki akarunk listázni
    {
        $ma = date("Y-m-d");
        $kov_honap = date("Y-m-d", strtotime($ma . "+30 day"));  //1 hónapnyi adat a lekérdezésehez
        $datum_lek = mysqli_query($l, "SELECT `nap` FROM `naptar` WHERE `nap` >= '".$ma."' AND `nap`<= '".$kov_honap."' AND `foglalas_id` > '0' GROUP BY `nap`");

    }


    while ($datum = mysqli_fetch_array($datum_lek))
    {
        print '<table class="table">
                    <tr class="datum_sor">
                        <td colspan="6" class="text-center">'.str_replace("-", ". ", $datum['nap']).'</td>
                    </tr>
                    <tr class="cim_sor">
                        <td>Időpont</td>
                        <td>Ügyfél név</td>
                        <td>Rendszám</td>
                        <td>Megjegyzés</td>
                        <td>Műveletek</td>
                        <td></td>
                    </tr>';


        $idopont_lek = mysqli_query($l, "SELECT * FROM `naptar` WHERE `nap` = '".$datum['nap']."' AND `foglalas_id` > '0' ORDER BY `ido`");
        while ($idopont = mysqli_fetch_array($idopont_lek))
        {
            $foglalas_lek = mysqli_query($l, "SELECT * FROM `foglalas` WHERE `id` = '".$idopont['foglalas_id']."'");
            $foglalas = mysqli_fetch_array($foglalas_lek);

            $ugyfel_lek = mysqli_query($l, "SELECT * FROM `ugyfel` WHERE `id` = '".$foglalas['ugyfel_id']."'");
            $ugyfel = mysqli_fetch_array($ugyfel_lek);


            print '<tr>';
            print '    <td>'.substr($idopont['ido'], 0, 5).'</td>';
            print '    <td>'.$ugyfel['vezeteknev'].' '.$ugyfel['keresztnev'].'</td>';
            print '    <td>'.$foglalas['rendszam'].'</td>';
            print '    <td>'.$foglalas['megjegyzes'].'</td>';

            print '    <td>';
                            $igenyelt_muveletek_lek = mysqli_query($l, "SELECT * FROM `igenyelt_muveletek` WHERE  `foglalas_id` = '".$foglalas['id']."'");
                            while ($igenyelt_muveletek = mysqli_fetch_array($igenyelt_muveletek_lek))
                            {
                                $muvelet_lek = mysqli_query($l, "SELECT * FROM `muveletek` WHERE `id` = '".$igenyelt_muveletek['muvelet_id']."'");
                                $muvelet = mysqli_fetch_array($muvelet_lek);
                                print $muvelet['nev'] . '<br>';
                            }

            print '    </td>';

            print '<td>
                       <form method="post">
                           <input type="hidden" name="foglalas_id" value="'.$foglalas['id'].'">
                           <input type="submit" name="torles_gomb" value="Foglalás törlése" class="btn btn-danger">
                       </form>

                   </td>';
            print '</tr>';

        }

    print'     </table>';

    }



?>
