<?php

//cPanelen linux parancs a cron-hoz -> wget -O - https://akarmi.hu/sz_admin_4/cron_email.php

$l = mysqli_connect('localhost', 'root', '', 'gyakorlas1');

$holnaputan = date("Y-m-d", strtotime(date("Y-m-d") . " +2 day"));
$datum_lek = mysqli_query($l, "SELECT * FROM `naptar` WHERE `nap`='".$holnaputan."' AND `foglalas_id`>'0'");
while($datum = mysqli_fetch_array($datum_lek))
{
    $foglalas_lek = mysqli_query($l,"SELECT * FROM `foglalas` WHERE `id`='".$datum['foglalas_id']."'");
    $foglalas = mysqli_fetch_array($foglalas_lek);

    $ugyfel_lek = mysqli_query($l, "SELECT * FROM `ugyfel` WHERE `id`='".$foglalas['ugyfel_id']."'");
    $ugyfel = mysqli_fetch_array($ugyfel_lek);

    $szoveg = '<h3>Tisztelt '.$ugyfel['vezeteknev'].' '.$ugyfel['keresztnev'].'!</h3>
                <br>
                <p>Szeretnénk emlékeztetni, hogy Önnek időpont-foglalása van szervízünkbe az alábbi adatokkal:</p>
                <p>Rendszám: <span style="color: #0a53be; font-weight: bold;">'.$foglalas['rendszam'].'</span></p>
                <p>Dátum: <span style="color: #0a53be; font-weight: bold;">'.$datum['nap'].'</span></p>
                <p>Időpont: <span style="color: #0a53be; font-weight: bold;">'.substr($datum['ido'], 0, 5).'</span></p>
                
                <br> <br>
                Szeretettel várjuk!<br>
                Tóth és Társa Szervíz';


    //email küldése
    include ('class.phpmailer.php');

    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    //Set who the message is to be sent from
    $mail->SetFrom('webnyar@gmail.com', 'Toth es Tarsa Kft.');
    //Set an alternative reply-to address
    $mail->AddReplyTo('webnyar@gmail.com', 'Toth es Tarsa Kft.');
    //Set who the message is to be sent to
    $mail->AddAddress($ugyfel['email']);
    //Set the subject line
    $mail->Subject = "Idopont emlekezteto";


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
        print '<br><br><p class="bg-success text-white">Sikeres küldés!</p>';
    }

}

mysqli_close($l);







?>