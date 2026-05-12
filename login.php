<?php
session_start();
include("conexao.php");

$erro = "";

// LOGIN
if(isset($_POST['entrar'])){

    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios
            WHERE login='$login'
            AND senha='$senha'";

    $result = mysqli_query($conexao,$sql);

    if(mysqli_num_rows($result) > 0){

        $usuario = mysqli_fetch_assoc($result);

        $_SESSION['logado'] = true;
        $_SESSION['nivel'] = $usuario['nivel'];

        header("Location: principal.php");
        exit();

    } else {

        $erro = "LOGIN INVÁLIDO";
    }
}

// LOGOUT
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>

<title>Login</title>

<style>

html, body{height:100%;margin:0;font-family:"Segoe UI";}

body{background:url('fundo1.png');background-size:cover;background-position:center;background-repeat:no-repeat;}

.login-box{position: fixed;top: 20%;left: 50%;transform: translateX(-50%);background: white;
padding: 20px;border-radius: 10px;width: 350px;text-align: center;box-shadow: 0px 0px 10px black;}

input{width: 90%;padding: 8px;margin: 8px 0;}

.login-box button{width: 100%;padding: 10px;border-radius: 10px;background: #3498db;color: white;border: none;
}

.rodape{position: fixed;bottom: 0;left: 0;width: 100%;background: #2c3e50;color: white;text-align: center;padding: 8px;font-size: 12px;}

</style>
</head>

<body>

<div class="login-box">
<h3>LOGIN DE USUÁRIOS</h3>
<form method="post">
<input type="text" name="login" placeholder="Login">
<br>
<input type="password" name="senha" placeholder="Senha">
<br>
<button name="entrar">Entrar</button>
</form>

<?php
if($erro != ""){
echo "<p style='color:red;'>$erro</p>";
}
?>

</div>

<footer class="rodape">
PROGRAMA LOCAÇÃO DE EQUIPAMENTOS  - Versão: 1.00 - © 2026 - Desenvolvido por Jorge E. L. Machado
</footer>

</body>
</html>