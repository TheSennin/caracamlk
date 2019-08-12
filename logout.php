<?php
if(isset($_REQUEST['sair'])){
    session_destroy();
    session_unset($_SESSION['admblog']);
    session_unset($_SESSION['passwd']);
    header("Location: index.php");
}
?>