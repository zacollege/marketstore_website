<?php


    $koneksi = mysqli_connect("sql203.infinityfree.com","if0_43516456","merpati58","if0_43516456_warungreza");

    if (!$koneksi) {            
        die("Koneksi gagal: " . mysqli_connect_error());
    }
?>