<?php

    session_start();

    if ($_SESSION['admin_belepett'] != 'igen')
    {
        print '<meta http-equiv="refresh" content="0; url=index.php">';
        exit();
    }

    if ($_SESSION['admin_jog'] == 3)
    {
        print '<meta http-equiv="refresh" content="0; url=index.php">';
        exit();
    }




    if (isset($_POST['statusz']))
    {
        $statusz = mysqli_real_escape_string($l, $_POST['statusz']);
        mysqli_query($l, "UPDATE `felugro` SET `statusz` = '".$statusz."'");
    }

    if (isset($_POST['mentes_gomb']))
    {
        $tartalom = mysqli_real_escape_string($l, $_POST['tartalom']);
        $cim = mysqli_real_escape_string($l, $_POST['cim']);
        mysqli_query($l, "UPDATE `felugro` SET `cim` = '".$cim."', `tartalom` = '".$tartalom."'");

        print '<p class="bg-success text-white">Sikeres módosítás!</p>';
    }


    print '<h3>Felugró ablak kezelése</h3>';


    $lek = mysqli_query($l, "SELECT * FROM `felugro`");
    $adat = mysqli_fetch_array($lek);

    print '<form method="post">
               <br><br>
               <h4>A felugró ablak státusza</h4>
               <select name="statusz" onchange="this.form.submit()" class="felugro_statusz">';
                    if ($adat['statusz'] == 'aktív')
                    {
                        print '<option>aktív</option>
                               <option>inaktív</option>';
                    }
                    else
                    {
                        print '<option>inaktív</option>
                               <option>aktív</option>';
                    }

        print '</select>   
                
               <br>
               Cím: <input type="text" name="cim" value="'.$adat['cim'].'" class="form-control">            
               
               <textarea name="tartalom" class="ckeditor">'.$adat['tartalom'].'</textarea>
               <input type="submit" name="mentes_gomb" value="Mentés" class="btn btn-success">
               
           </form>';




?>