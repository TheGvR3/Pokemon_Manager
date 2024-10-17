<?php
include('components\config.php');

$conn = mysqli_connect($host,$user,$password,$database);
if(false === $conn){
    exit("Errore: Impossibile stabilire una connessione" . mysqli_connect_error());
}
