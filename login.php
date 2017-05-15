<?php
ob_start();
session_start();
if(isset($_SESSION['admblog']) AND (isset($_SESSION['passwd']))) {
    header("Location: adm-login.php");exit;
}
include ("conexao/conecta.php");

//RECUPERAÇÃO DE DADOS
if(isset($_POST['logar'])){
    $usuario = trim(strip_tags($_POST['usuario']));
    $senha   = trim(strip_tags($_POST['senha']));

    //SELECIONAR BANCO DE DADOS

    $select = "SELECT * from login WHERE usuario=:usuario AND senha=:senha";

    try{
        $result = $conexao->prepare($select);
        $result -> bindParam(':usuario',$usuario, PDO::PARAM_STR);
        $result -> bindParam(':senha',$senha, PDO::PARAM_STR);
        $result -> execute();
        $contar = $result ->rowCount();
        if ($contar > 0){
            $usuario = $_POST['usuario'];
            $senha   = $_POST['senha'];
            $_SESSION['admblog'] = $usuario;
            $_SESSION['passwd'] = $senha;
            header("Refresh: 0, adm-login.php");
        }
        else
            echo 'Os dados digitados estão incorretos!';

    }catch(PDOException $error){
        echo $error;
    }
}//se clicar no botão entrar no sistema

if(isset($_GET['action'])){
    if(!isset($_POST['logar'])){
        $acao = $_GET['action'];
        if($acao == 'deny'){
            echo 'Você precisa logar para acessar!';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
	<title>Blog CaracaMlk</title>
	<link rel="stylesheet" type="text/css" href="_css/estilo.css"/>
    <link href="https://fonts.googleapis.com/css?family=Pangolin" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <link href="_css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="_css/signin.css" rel="stylesheet" type="text/css">

    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet">


    <!-- Fav Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="_imagens/favicons/fav-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="_imagens/favicons/fav-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="_imagens/favicons/fav-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="_imagens/favicons/fav-16x16.png">

</head>
<body>
    <div class="account-container">

        <div class="content clearfix">

            <form action="#" method="post" enctype="multipart/form-data">

                <h1>Faça seu Login</h1>

                <div class="login-fields">

                    <p>Entre com seus dados:</p>

                    <div class="field">
                        <label for="username">Usuário</label>
                        <input type="text" id="username" name="usuario" value="" placeholder="Usuário" class="login username-field" />
                    </div> <!-- /field -->

                    <div class="field">
                        <label for="password">Senha:</label>
                        <input type="password" id="password" name="senha" value="" placeholder="Senha" class="login password-field"/>
                    </div> <!-- /password -->

                </div> <!-- /login-fields -->

                <div class="login-actions">

                    <input type="submit" name="logar" value="Entrar no Sistema" class="button btn btn-success btn-large"/>

                </div> <!-- .actions -->

            </form>

        </div> <!-- /content -->

    </div> <!-- /account-container -->
</body>
</html>