<?php
include("verifica_login.php");
include("conexao.php");

$id = $_GET['id'];

// 🔷 REABRE LOCAÇÃO
mysqli_query($conexao,"
    UPDATE locacao
    SET status = 'ABERTO'
    WHERE id_locacao = $id
");

// 🔷 ZERA CAMPOS FINANCEIROS DOS ITENS
mysqli_query($conexao,"
    UPDATE itens_locacao
    SET multa = 0,
        antecipacao = 0,
        ajuste_desconto = 0,
        ajuste_encargos = 0
    WHERE id_locacao = $id
");

// 🔷 REDIRECIONA
header("Location: locacao.php?selecionar=".$id);

exit;
?>
