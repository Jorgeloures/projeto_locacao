<?php
include("verifica_login.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sobre o Sistema</title>

<style>

html, body{height:100%; margin:0; font-family:"Segoe UI";}
body{ background:url('fundo1.png'); background-size:cover; background-position:center;   background-repeat:no-repeat;}

/* MODAL */
.modal-content{ width: 900px; max-width: 95%; background: #ffffff; border-radius: 10px; border: 3px solid #3498db; padding: 25px; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);    max-height: 85vh; overflow-y: auto; box-shadow: 0 0 15px rgba(0,0,0,0.4);}
h2{text-align:center; color:#f02c50; margin-bottom:5px;}
h3{text-align:center; color:#2c3e50; margin-top:0; margin-bottom:25px;}
p{text-align:justify; line-height:1.7; margin-bottom:15px;}

ul{line-height:1.8;}
.botoes{text-align:center; margin-top:30px;}
.botoes button{padding:12px 40px; border:none; border-radius:10px; background:linear-gradient(135deg,#3498db,#2980b9); color:#fff; font-weight:bold; cursor:pointer;}
.botoes button:hover{ background:linear-gradient(135deg,#6573b8,#6193de);}
.rodape{text-align:center; margin-top:25px; font-size:12px; color:#666;}

</style>
</head>

<body>
<div class="modal-content">
    <h2>DELTA COMPUTADORES</h2>
    <h3>SISTEMA DE LOCAÇÃO DE EQUIPAMENTOS DE INFORMÁTICA</h3>
    <p>
        Este sistema foi desenvolvido para realizar o controle completo de
        locação de equipamentos de informática, permitindo o cadastro de
        clientes, equipamentos, locações e devoluções de forma simples e segura.
    </p>

    <h4>COMO UTILIZAR O SISTEMA</h4>
    <ul>
        <li><b>CLIENTES:</b> cadastrar, editar, pesquisar e excluir clientes.</li>
        <li><b>EQUIPAMENTOS:</b> cadastrar equipamentos, controlar estoque e valores de locação.</li>
        <li><b>LOCAÇÃO:</b> registrar locações, selecionar equipamentos e definir período de utilização.</li>
        <li><b>DEVOLUÇÃO:</b> finalizar devoluções, aplicar multas por atraso ou descontos por antecipação.</li>
        <li><b>CANCELAR DEVOLUÇÃO:</b> Desfazer todo o cálculo da devolução de equipamentos - recurso limitado somente ao administrador.</li>
        <li><b>RESULTADOS:</b> consultar informações financeiras geradas pelas locações.</li>
        <li><b>RELATÓRIOS:</b> emitir relatórios gerenciais do sistema.</li>
    </ul>

    <h4>REGRAS DE UTILIZAÇÃO</h4>

    <ul>
        <li>Cadastre inicialmente os clientes.</li>
        <li>Cadastre os equipamentos disponíveis para locação, informando principalmente a quantidade de equipamentos disponíveis para locação</li>
        <li>Realize a locação informando cliente e equipamentos, posteriormente, selecione o equipamentos para locação, indicando a quantidade. </li>
        <li>Ao devolver os equipamentos, utilize a opção DEVOLUÇÃO, indique a data real da devolução, onde será calculado o valor. Nos casos de devolução em atraso ou antecipada, o operador deverá indicar o tipo de entrega ocorrido. Também é possível fazer fazer ajustes manuais de multas e descontos.</li>
        <li>O estoque é atualizado automaticamente pelo sistema.</li>
    </ul>

    <h4>NÍVEIS DE ACESSO</h4>

    <ul>
        <li><b>ADM:</b> acesso completo ao sistema.</li>
        <li><b>OPERADOR:</b> acesso operacional sem acesso aos relatórios e resultados.</li>
    </ul>

    <h4>INFORMAÇÕES DO PROJETO</h4>

    <p>
        Projeto acadêmico desenvolvido para demonstração de técnicas de
        programação utilizando PHP, JavaScript, HTML, CSS e MySQL.
    </p>

    <div class="botoes">
        <button onclick="window.location.href='principal.php'">
            VOLTAR
        </button>
    </div>

    <div class="rodape">
        Versão 1.1.0<br>
        © 2026 - Desenvolvido por Jorge E. L. Machado
    </div>

</div>

</body>
</html>