<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/caracamlk/conexao/conecta.php");
include($_SERVER['DOCUMENT_ROOT']."/caracamlk/includes/index-header.php");
include($_SERVER['DOCUMENT_ROOT']."/caracamlk/includes/index-body.php");
?>
<?php
$sql = "SELECT * from td_postagens WHERE id='102'";
try {
$resultado = $conexao->prepare($sql);
$resultado -> execute();
$contar = $resultado->rowCount();
$exibe = $resultado->FETCH(PDO::FETCH_OBJ);

if ($contar > 0) {
?>
    <div class="interface-post">
        <article class="novo-post">
        <header class="post-header">
            <div class="day-post"><?php echo $exibe->dia ?><br/><?php echo $exibe->mes ?></div>
            <a href="./"> <div class="legenda-post"><h2><?php echo $exibe->titulo ?></h2></div></a>
        </header>
     <div class="margem-top">
         <img class="alinhar-centro" src="<?php echo $exibe->tirinha ?>"/>
     </div>
     <div class="post-comment">
         <p><?php echo $exibe ->legenda ?></p>
     </div>
     <div class="post-footer">
            <div class="fb-like" data-href="https://www.facebook.com/caracamlkblog/" data-width="100" data-layout="button_count"   data-action="like" data-size="large" data-show-faces="false" data-share="true">
            </div>
        </div>
        </article>
    </div>
<?php
} else {
    echo 'Não existe post cadastrado!';
    echo $id;
}
}catch(PDOException $erro){
    echo $erro;
}
?>
<?php include ($_SERVER['DOCUMENT_ROOT']."/caracamlk/includes/index-footer.php"); ?>
</body>
</html>