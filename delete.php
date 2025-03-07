<?php
include 'db.php';
if(isset($_GET['deletid'])){
    $id==$_GET['deletid'];

    $sql="delete from 'MYSTORE' where id=$id";
    $result=mysqli_query($con,$sql);
    if($result){
        //echo "Deleted successfully";
        header('location:attacktype.php');
     } else {
        die(mysqli_error($con));
     }
}

?>