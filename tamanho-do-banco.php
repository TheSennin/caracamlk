<?php
require_once ("conexao/conecta.php");
?>
<?php
$sql = "SELECT table_schema 'admlogin', round(sum( data_length + index_length ) / 1024 / 1024, 2) 'Size in MB'";
FROM information_schema.TABLES;
GROUP BY table_schema like 'admlogin';
?>