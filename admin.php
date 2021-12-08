<?php
    session_start();

    if ($_SESSION['admin_belepett'] != 'igen')
    {
        print '<meta http-equiv="refresh" content="0; url=index.php">';
        exit();
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

    <!-- CK editor (Word-ös szerkesztői felület) beépítése -->
    <script src="ckeditor/ckeditor.js"></script>
    <link rel="stylesheet" href="ckeditor/samples/sample.css">


</head>

<body>

<?php


function kepgenerator ($kepfajl, $maxmeret, $ujfajlnev)
{
    if (!file_exists($kepfajl))
    {
        return (false);
    }

    // Megfelelő méret kiválasztása
    list($width, $height, $type) = getimagesize($kepfajl);
    $nagyobb = ($width > $height) ? $width : $height;
    $kisebb = ($width > $height) ? $height : $width;
    if ($nagyobb <= $maxmeret)
    {
        $new_nagyobb = $nagyobb;
        $new_kisebb = $kisebb;
    }
    else
    {
        $szorzo = $maxmeret / $nagyobb; // Mennyire (milyen aránnyal) kicsinyítjük le a képet - ez egy 0 és 1 közötti szám lesz
        $new_nagyobb = $maxmeret; // A nagyobb oldalszélesség lesz a maximális
        $new_kisebb = $kisebb * $szorzo; // A nagyobb oldalméret kicsinyítésével ($szorzo) arányosan kicsinyítjük le a kisebb oldalt is
    }
    $new_width = ($width > $height) ? $new_nagyobb : $new_kisebb; // Az eredeti méretek alapján összepárosítjuk az új szélességet és magasságot a kissebb-nagyobb értékekkel
    $new_height = ($width > $height) ? $new_kisebb : $new_nagyobb;

    // Kép generálása
    switch ($type) // A kép formátumától függően más-más függvénnyel dolgozzuk fel a képet
    {
        case 1:
            $kep = imagecreatefromgif ($kepfajl);
            break;
        case 2:
            $kep = imagecreatefromjpeg ($kepfajl);
            break;
        case 3:
            $kep = imagecreatefrompng ($kepfajl);
            break;
    }

    $ujkep = imagecreatetruecolor ($new_width, $new_height);
    imagecopyresampled ($ujkep, $kep, 0, 0, 0, 0, $new_width, $new_height, $width, $height); // A lényeg - most generáljuk az új képet
    imagejpeg ($ujkep, $ujfajlnev, 100); // És végül egy (lehető legjobb minoségű) jpeg képet generálunk az egészből, és azt elmentjük a megadott néven
    return (array($new_width, $new_height)); // Visszaadjuk a generált kép szélességét és magasságát
}

?>






<nav class="navbar navbar-expand-md bg-dark navbar-dark">

    <!-- Toggler/collapsibe Button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav">

            <?php
                if ($_SESSION['admin_jog'] == 1)
                {
                    print '<li class="nav-item">
                                <a class="nav-link" href="admin.php?oldal=adminok">Adminok</a>
                            </li>';
                }

            if ($_SESSION['admin_jog'] == 1 or $_SESSION['admin_jog'] == 2)
            {
                print '<li class="nav-item">
                            <a class="nav-link" href="admin.php?oldal=kezdolap">Kezdőlap kezelése</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php?oldal=felugro">Felugró ablak</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php?oldal=galeria">Galéria</a>
                        </li>';
            }

            if ($_SESSION['admin_jog'] == 1 or $_SESSION['admin_jog'] == 3)
            {
                print ' <li class="nav-item">
                            <a class="nav-link" href="admin.php?oldal=foglalasok">Foglalások</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php?oldal=ugyfelek">Ügyfelek</a>
                        </li>';
            }



            print '<li class="nav-item">
                        <a class="nav-link" href="admin.php?oldal=kilepes">Kilépés</a>
                   </li>';



            ?>






        </ul>
    </div>
</nav>




<div class="container">

    <?php


    $l = mysqli_connect('localhost', 'root', '', 'gyakorlas1');


    switch($_GET['oldal'])
    {
        case 'adminok': include 'adminok.php'; break;
        case 'kilepes': include  'kilepes.php'; break;
        case 'kezdolap': include  'kezdolap.php'; break;
        case 'galeria': include  'galeria.php'; break;
        case 'felugro': include  'felugro.php'; break;
        case 'ugyfelek': include  'ugyfelek.php'; break;
        case 'foglalasok': include  'foglalasok.php'; break;

        default: include 'kezdolap.php'; break;

    }

    mysqli_close($l)

    ?>


</div>






</body>
</html>
