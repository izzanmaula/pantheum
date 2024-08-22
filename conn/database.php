<?php

//resep bahan-bahan
$servername = 'sql110.infinityfree.com';
$username = 'if0_37134783';
$password = 'bN4qrElq9K';
$database = 'if0_37134783_pantheum';

//koneksi 
$koneksi = new mysqli($servername, $username, $password, $database);

//debug 
if ($koneksi->connect_error){
    die('koneksi gagal: ' . $koneksi->connect_error);
}else{
    echo'';
}
?>
