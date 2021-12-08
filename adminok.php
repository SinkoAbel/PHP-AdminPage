<?php


session_start();

    if ($_SESSION['admin_belepett'] != 'igen')
    {
        print '<meta http-equiv="refresh" content="0; url=index.php">';
        exit();
    }

    if ($_SESSION['admin_jog'] != 1)
    {
        print '<meta http-equiv="refresh" content="0; url=admin.php">';
        exit();
    }

    print '<h3>Adminok kezelése</h3>';




    if (!isset($_POST['uj_admin_gomb']))
    {
        print '<form method="post">
                    <input type="submit" name="uj_admin_gomb" value="Új admin felvétele" class="btn btn-primary">
               </form>';
    }


    if (isset($_POST['uj_admin_gomb']))
    {
        print '<div class="col-md-5 box1">
                    <h4>Új admin rögzítése</h4>
                    <form method="post">
                        <input type="text" name="nev" placeholder="Név:" required class="form-control">
                        <input type="text" name="email" placeholder="E-mail:" required class="form-control">
                        
                        Jogosultság:<br>
                        <select name="jogosultsag" required>
                            <option value="">Válasszon jogosultságot</option>                    
                            <option value="1">1 - Teljes jogosultság</option>                    
                            <option value="2">2 - Galéria és szöveg</option>                    
                            <option value="3">3 - Csak ügyféladatok</option>                    
                        </select>
                        
                        <br><br>
                        <input type="submit" name="uj_admin_mentes_gomb" value="Új admin mentése" class="btn btn-success">
                   
                    </form>
                </div>';
    }



    if(isset($_POST['uj_admin_mentes_gomb']))
    {
        $nev = mysqli_real_escape_string($l, $_POST['nev']);
        $email = mysqli_real_escape_string($l, $_POST['email']);
        $jogosultsag = mysqli_real_escape_string($l, $_POST['jogosultsag']);

        $van_e_mar_lek = mysqli_query($l, "SELECT * FROM `adminok` WHERE `email` = '".$email."'");
        $van_e_mar_db = mysqli_num_rows($van_e_mar_lek);

        if ($van_e_mar_db == 0)
        {
            $betuk = ["A", "B", "C", "D", "E", "F"];
            $uj_jelszo = $betuk[mt_rand(0, 5)] . $betuk[mt_rand(0, 5)] . mt_rand(10000, 99999);
            $uj_kodolt_jelszo = hash('sha256', $uj_jelszo);


            mysqli_query($l, "INSERT INTO `adminok` SET 
            `id` = NULL,
            `nev` = '".$nev."',
            `email` = '".$email."',
            `jelszo` = '".$uj_kodolt_jelszo."',
            `jogosultsag` = '".$jogosultsag."',
            `statusz` = 'aktív'");


            //email küldés
            include ('class.phpmailer.php');

            $szoveg = ' Tisztelt '.$nev.'!
                    <br><br>
                    Mostantól a következő jelszóval fog tudni belépni az oldalra, 
                    ahol van lehetősége ezt a jelszót megváltoztatni!
                    
                    <p style="color: red">
                        Email cím '.$email.'<br>
                        Jelszava: '.$uj_jelszo.'</p><br>
                    Itt tud belépni: <a href="localhost/honlap0713/sz_admin_4/index.php">localhost/honlap0713/sz_admin_4/index.php</a>

                    <br><br>
                    Üdvözlettel: <br>
                    Tóth és Társa Kft. ';


            //Create a new PHPMailer instance
            $mail = new PHPMailer();
            //Set who the message is to be sent from
            $mail->SetFrom('webnyar@valami.hu', 'Toth es Tarsa Kft.');
            //Set an alternative reply-to address
            $mail->AddReplyTo('webnyar@valami.hu', 'Toth es Tarsa Kft.');
            //Set who the message is to be sent to
            $mail->AddAddress($email);
            //Set the subject line
            $mail->Subject = "Admin felulet hozzaferes";
            //Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
            $mail->MsgHTML($szoveg, dirname(__FILE__));
            //Replace the plain text body with one created manually
            $mail->AltBody = 'This is a plain-text message body';
            //Attach an image file
            //$mail->AddAttachment('images/phpmailer.png');

            //Send the message, check for errors
            if(!$mail->Send())
            {
                print 'E-mail küldési hiba: ' . $mail->ErrorInfo;
            }
            else
            {
                print '<br><br><p class="bg-success text-white">Sikeres rögzítés!</p>';
            }

        }

        else
        {
            print '<p class="bg-danger text-white">Van már ilyen adminisztrátor!</p>';
        }
    }


    if (isset($_POST['modositando_admin_id']))
    {
        $modsitando_admin_id = mysqli_real_escape_string($l, $_POST['modositando_admin_id']);
        $jogosultsag = mysqli_real_escape_string($l, $_POST['jogosultsag']);
        $statusz = mysqli_real_escape_string($l, $_POST['statusz']);

        mysqli_query($l, "UPDATE `adminok` SET 
        `jogosultsag` = '".$jogosultsag."',
         `statusz` = '".$statusz."'
          WHERE `id` = '".$modsitando_admin_id."'");

        print '<br><br><p class="bg-success text-white">Sikeres módosítás!</p>';

    }



    //Adminok listázása

    print '<table class="table">
            <thead>
                <th>Név</th>
                <th>E-mail</th>
                <th>Jogosultság</th>
                <th>Státusz</th>
            </thead>';


    $lek = mysqli_query($l, "SELECT * FROM `adminok` ORDER BY `nev`");
    while ($admin = mysqli_fetch_array($lek))
    {
        
        print '<tr>';
            print '<td>'.$admin['nev'].'</td>';
            print '<td>'.$admin['email'].'</td>';
            print '<td>
                        <form method="post">
                            <input type="hidden" name="modositando_admin_id" value="'.$admin['id'].'">
                            
                            
                            <select name="jogosultsag" onchange="this.form.submit()"">
                                <option>'.$admin['jogosultsag'].'</option>
                                
                                <option value="1">1 - teljes jogosultság</option>
                                <option value="2">2 - galéria és szövegek</option>
                                <option value="3">3 - csak az ügyféladatok</option>
                                
                            </select>


                    </td>';
            print '<td>';
                        if ($admin['statusz'] == "aktív")
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



            print '     </form>
                   </td>';
        print '</tr>';

    }

    print '</table>';











?>
