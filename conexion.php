<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "base_chatbotwpp"; 
file_put_contents("text3.txt", "Esta entrando a la conexión") ;
$conn = new mysqli($servername, $username, $password , $dbname); 