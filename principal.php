<?php 
include("verifica_login.php");
include("conexao.php"); 
$nivel = $_SESSION['nivel'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Locação</title>

    <style>
        html, body {height: 100%;margin: 0;font-family: "Segoe UI";}

        body {background: url('fundo1.png');background-size: cover;background-position: center;            background-repeat: no-repeat;}

/* MENU */
.menu {display: flex;flex-wrap: wrap;justify-content: center;gap: 10px;position: absolute;top: 50%;            left: 50%;transform: translate(-50%, -50%);width: 650px;background: white;padding: 20px 40px 40px 40px;border-radius: 10px;text-align: center;border: 3px solid #3498db;opacity: 1}

.menu h2 {font-family: 'Franklin Gothic Medium';font-size: 16px;margin-bottom: 20px;margin-top: 0;            letter-spacing: 1px;}
.menu h1 {font-family: "Segoe UI";font-size: 40px;margin-bottom: 1px;letter-spacing: 3px;color: #f02c50;}

.menu button {width: 45%;padding: 15px;border-radius: 12px;border: 1px solid #f5f98c;background:linear-gradient(135deg, #3498db, #2980b9);color: white;font-weight: bold;cursor: pointer;}
.menu button:hover {background: linear-gradient(135deg, #6573b8, #6193de);}
.menu button:active {transform: scale(0.95);box-shadow: inset 0px 3px 8px rgba(0,0,0,0.4);}
button:disabled{background: #999;cursor: not-allowed;opacity: 0.6;}

input {width: 90%;padding: 8px;margin: 8px 0;}

.rodape {position: fixed;bottom: 0;left: 0;width: 100%;background: #2c3e50;color: white;Text-align: center;    padding: 8px;font-size: 12px;}

.menu-topo {display: flex;align-items: center;gap: 15px;justify-content: center;}
.menu-icon {width: 60px;height: 60px;}

    </style>
</head>

<body>

<!-- MENU -->
<div class="menu">

    <div class="menu-topo">
        <img src="icone.png" class="menu-icon">
        
        <div>
            <h1>DELTA COMPUTADORES</h1>
            <h2>LOCAÇÃO DE EQUIPAMENTOS DE INFORMÁTICA</h2>
        </div>
    </div>
    <button type="button" onclick="window.location.href='clientes.php'">CLIENTES</button>
    <button type="button" onclick="window.location.href='equipamentos.php'">EQUIPAMENTOS</button>
    <button type="button" onclick="window.location.href='locacao.php'">LOCAÇÃO</button>
    <button type="button"<?php if($nivel == 'ADM'){ ?>onclick="window.location.href='relatorios.php'"<?php } else { ?>disabled<?php } ?>>RELATÓRIOS</button>
    <button type="button"<?php if($nivel == 'ADM'){ ?>onclick="window.location.href='resultados.php'"<?php } else { ?>disabled<?php } ?>>RESULTADOS</button>
    <button type="button" onclick="window.location.href='login.php?logout=1'">SAIR</button>

</div>

<footer class="rodape">
    PROGRAMA LOCAÇÃO DE EQUIPAMENTOS  - Versão: 1.1.0 - © 2026 - Desenvolvido por Jorge E. L. Machado
   
    | 
    <a href="sobre.php" style="color:#ffffff; font-weight:bold;"> SOBRE O SISTEMA </a>
</footer>

</body>
</html>