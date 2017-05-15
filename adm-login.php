<?php
ob_start();
session_start();
if(!isset($_SESSION['admblog']) AND (!isset($_SESSION['passwd']))) {
    header("Location: login.php?action=deny");exit;
}
include ("conexao/conecta.php");
include ("logout.php");
?>
<?php include("includes/adm-header.php");
include ("includes/adm-body.php");
?>

<?php
   if(isset($_POST['postar'])) {
       $titulo = trim(strip_tags($_POST['titulo']));
       $mes = trim(strip_tags($_POST['mes']));
       $dia = trim(strip_tags($_POST['dia']));
       $exibir = trim(strip_tags($_POST['exibir']));
       $legenda = $_POST['legenda'];

       //INFO IMAGEM
       $file = $_FILES['tirinha'];
       $numFile = count(array_filter($file['name']));

       //nome da pasta

       $titulopost = $titulo;
       $titulopost = preg_replace('/[ -]+/' , '-' , $titulopost);
       $titulopasta = $titulopost = preg_replace('/[ -]+/' , '-' , $titulopost);

       //PASTA
       $datapost = date("d-m-y");
       mkdir ("posts/$datapost", 0755);
       mkdir ("posts/$datapost/$titulopasta", 0755);
       $folder = 'posts/' .  $datapost .'/' . $titulopasta;

       //REQUISITOS
       $permite = array('image/jpeg', 'image/png', 'image/gif');
       $maxSize = 1024 * 1024 * 1;

       //MENSAGENS
       $msg = array();
       $errorMsg = array(
           1 => 'O arquivo no upload é maior do que o limite definido em upload_max_filesize no php.ini.',
           2 => 'O arquivo ultrapassa o limite de tamanho em MAX_FILE_SIZE que foi especificado no formulário HTML',
           3 => 'o upload do arquivo foi feito parcialmente',
           4 => 'Não foi feito o upload do arquivo'
       );

       if ($numFile <= 0) {
           echo 'Nenhum arquivo foi selecionado!';
       } else if ($numFile > 1) {
           echo 'Você ultrapassou o limite de upload!';
       } else {
           for ($i = 0; $i < $numFile; $i++) {
               $name = $file['name'][$i];
               $type = $file['type'][$i];
               $size = $file['size'][$i];
               $error = $file['error'][$i];
               $tmp = $file['tmp_name'][$i];

               $extensao = @end(explode('.', $name));
               $novoNome = $titulopost = preg_replace('/[ -]+/' , '-' , $titulopost) . ".$extensao";
               $titulopasta = $titulo = preg_replace('/[ -]+/' , '-' , $titulo);

               if ($error != 0)
                   $msg[] = "<b>$name :</b> " . $errorMsg[$error];
               else if (!in_array($type, $permite))
                   $msg[] = "<b>$name :</b> Erro imagem não suportada!";
               else if ($size > $maxSize)
                   $msg[] = "<b>$name :</b> Erro imagem ultrapassa o limite de 5MB";
               else {
                   if (move_uploaded_file($tmp, $folder . '/' . $novoNome)) {
                       //$msg[] = "<b>$name :</b> Upload Realizado com Sucesso!";
                       //CORRIGIR BUG DO TÍTULO
                       $titulo = trim(strip_tags($_POST['titulo']));
                       $insert = "INSERT into td_postagens (titulo, mes, dia, tirinha, exibir, legenda, titulopasta, datapost) VALUES (:titulo, :mes, :dia, :tirinha, :exibir, :legenda, :titulopasta, :datapost)";

                       try {
                           $result = $conexao->prepare($insert);
                           $result->bindParam(':titulo', $titulo, PDO::PARAM_STR);
                           $result->bindParam(':mes', $mes, PDO::PARAM_STR);
                           $result->bindParam(':dia', $dia, PDO::PARAM_STR);
                           $result->bindParam(':tirinha', $novoNome, PDO::PARAM_STR);
                           $result->bindParam(':exibir', $exibir, PDO::PARAM_STR);
                           $result->bindParam(':legenda', $legenda, PDO::PARAM_STR);
                           $result->bindParam(':titulopasta', $titulopasta, PDO::PARAM_STR);
                           $result->bindParam(':datapost', $datapost, PDO::PARAM_STR);
                           $result->execute();
                           $contar = $result->rowCount();
                           if ($contar > 0) {
                               echo 'Sucesso ao cadastrar!';
                                  $sql = "SELECT * from td_postagens WHERE exibir='sim' ORDER BY id DESC ";
                                  try {
                                      $resultado = $conexao->prepare($sql);
                                      $resultado -> execute();
                                      $count = $resultado->rowCount();
                                      $exibe = $resultado->FETCH(PDO::FETCH_OBJ);

                                      if ($contar > 0) {
                                          $id = $exibe->id;
                                          $fp = fopen("$folder/index.php", "a");

                                          $written = "<?php
require_once (\$_SERVER['DOCUMENT_ROOT'].\"/caracamlk/conexao/conecta.php\");
include(\$_SERVER['DOCUMENT_ROOT'].\"/caracamlk/includes/index-header.php\");
include(\$_SERVER['DOCUMENT_ROOT'].\"/caracamlk/includes/index-body.php\");
?>
<?php
\$sql = \"SELECT * from td_postagens WHERE id='$id'\";
try {
\$resultado = \$conexao->prepare(\$sql);
\$resultado -> execute();
\$contar = \$resultado->rowCount();
\$exibe = \$resultado->FETCH(PDO::FETCH_OBJ);

if (\$contar > 0) {
?>
    <div class=\"interface-post\">
        <article class=\"novo-post\">
        <header class=\"post-header\">
            <div class=\"day-post\"><?php echo \$exibe->dia ?><br/><?php echo \$exibe->mes ?></div>
            <a href=\"./\"> <div class=\"legenda-post\"><h2><?php echo \$exibe->titulo ?></h2></div></a>
        </header>
     <div class=\"margem-top\">
         <img class=\"alinhar-centro\" src=\"<?php echo \$exibe->tirinha ?>\"/>
     </div>
     <div class=\"post-comment\">
         <p><?php echo \$exibe ->legenda ?></p>
     </div>
     <div class=\"post-footer\">
            <div class=\"fb-like\" data-href=\"https://www.facebook.com/caracamlkblog/\" data-width=\"100\" data-layout=\"button_count\"   data-action=\"like\" data-size=\"large\" data-show-faces=\"false\" data-share=\"true\">
            </div>
        </div>
        </article>
    </div>
<?php
} else {
    echo 'Não existe post cadastrado!';
    echo \$id;
}
}catch(PDOException \$erro){
    echo \$erro;
}
?>
<?php include (\$_SERVER['DOCUMENT_ROOT'].\"/caracamlk/includes/index-footer.php\"); ?>
</body>
</html>";

                                          $escreve = fwrite($fp, $written);
                                      } else {
                                          echo 'Não Foi possível gerar o ID!';
                                      }
                                  }catch(PDOException $erro){
                                      echo $erro;
                                  }

                           } else
                               echo 'Os dados digitados estão incorretos!';

                       } catch (PDOException $error) {
                           echo $error;
                       }

                       foreach ($msg as $pop)
                           echo '';
                       //echo $pop.'<br>';
                   }
               }
           }
       }
   }
?>

<div class="div-post">
   <form action="" method="post" enctype="multipart/form-data">

       <h1>Criar Nova Postagem</h1>

        <div class="post-fields">

            <fieldset>
                <legend><label for="firstname">Título da Postagem: </label></legend>
                <input type="text" id="firstname" name="titulo" placeholder="Título da Postagem"/>
            </fieldset>

            <fieldset>
                <legend><label for="mes">Mês:</label></legend>
                <input type="text" id="mes" name="mes" maxlength="3" placeholder="Ex.: 10"/>
            </fieldset>

            <fieldset>
                <legend><label for="dia">Dia: </label></legend>
                <input type="text" id="dia" name="dia" maxlength="2" placeholder="Ex.: Jan"/>
            </fieldset>

            <fieldset>
                <legend><label for="tirinha">Tirinha: </label></legend>
                <input type="file" id="tirinha" name="tirinha[]"/>
            </fieldset>

            <fieldset>
                <legend>Exibir post no site?</legend>
                <p><input type="radio" id="exibir" name="exibir" value="sim" checked />Sim<br/></p>
                <p><input type="radio" id="exibir" name="exibir" value="não"/>Não</p>
            </fieldset>

            <fieldset>
                <legend><label for="legenda">Legenda: </label></legend>
                <textarea name="legenda" id="legenda" cols="40" rows="4" placeholder="Digite uma Legenda..." ></textarea>
            </fieldset>

    </div> <!-- /post-fields -->

        <div class="form-actions">
            <input type="submit" name="postar" value="Postar" class="botao">
        </div> <!-- /form-actions -->

</div>

    <div class="fb-page" id="like-box" data-href="https://www.facebook.com/caracamlkblog" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote  cite="https://www.facebook.com/caracamlkblog" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/caracamlkblog">Blog CaracaMlk</a></blockquote></div>

<?php
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
                              <div class="legenda-post"><h2><?php echo $exibe->titulo ?></h2></div>
                         </header>
                    <div class="margem-top">
                         <img class="alinhar-centro" src="_uploads/postagens/<?php echo $exibe->tirinha ?>"/>
                    </div>
                    <div class="post-comment">
                          <p><?php echo $exibe ->legenda ?></p>
                    </div>
                    <div class="post-footer">
                        <div class="fb-like" data-href="https://www.facebook.com/caracamlkblog/" data-width="100"        data-layout="button_count"   data-action="like" data-size="large" data-show-faces="false" data-share="true">
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
    echo $erro;
}
?>

<?php include ("includes/adm-footer.php"); ?>
</body>
</html>