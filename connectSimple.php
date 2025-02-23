<?php 
$con=new mysqli('localhost', 'root', 'root', 'gestion_usuarios');
if(!$con){
    die(mysql_error($con));
}
?>
