<?php
require_once ("conexao/conecta.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Blog CaracaMlk</title>
    <link rel="stylesheet" type="text/css" href="http://127.0.0.1/caracamlk/_css/estilo.css"/>
    <link href="https://fonts.googleapis.com/css?family=Pangolin" rel="stylesheet">

    <!-- Fav Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="http://127.0.0.1/caracamlk/_imagens/favicons/fav-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="http://127.0.0.1/caracamlk/_imagens/favicons/fav-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="http://127.0.0.1/caracamlk/_imagens/favicons/fav-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="http://127.0.0.1/caracamlk/_imagens/favicons/fav-16x16.png">

    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.8&appId=130424157473053";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>

</head>
<?php
include ("includes/index-body.php");

$sql = "SELECT * from td_postagens WHERE exibir='sim' ORDER BY id DESC";
try {
    $resultado = $conexao->prepare($sql);
    $resultado->execute();
    $contar = $resultado->rowCount();

    if ($contar > 0) {
        while ($exibe = $resultado->FETCH(PDO::FETCH_OBJ)) {
?>
            <div class="interface-post">
                <article class="novo-post">
                    <header class="post-header">
                        <div class="day-post"><?php echo $exibe->dia ?><br/><?php echo $exibe->mes ?></div>
                        <a href="./posts/<?php echo $exibe->datapost ?>/<?php echo $exibe->titulopasta ?>"> <div class="legenda-post"><h2><?php echo $exibe->titulo ?></h2></div></a>
                    </header>
                    <div class="margem-top">
                        <img class="alinhar-centro" src="./posts/<?php echo $exibe->datapost ?>/<?php echo $exibe->titulopasta ?>/<?php echo $exibe->tirinha ?>"/>
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
        }//fechamento do while
    } else {
        echo 'Não existe post cadastrado!';
    }
}catch(PDOException $erro){
    echo "Não foi possível buscar resultados!";
}
include ("includes/index-footer.php");
?>
</body>
</html>