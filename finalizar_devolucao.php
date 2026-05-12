<?php
include("conexao.php");

$id_locacao = $_POST['id_locacao'];
$data_dev_real = $_POST['data_dev_real'];
$entrega = $_POST['entrega'];

$multa = str_replace(",", ".", $_POST['multa']);
$antecipacao = str_replace(",", ".", $_POST['antecipacao']);
$desconto = str_replace(",", ".", $_POST['ajuste_desconto']);
$encargo = str_replace(",", ".", $_POST['ajuste_encargo']);

// 🔷 1) ATUALIZA LOCAÇÃO
mysqli_query($conexao, "
    UPDATE locacao SET
        data_dev_real = '$data_dev_real',
        status = 'FECHADO'
    WHERE id_locacao = $id_locacao
");

// 🔷 2) ATUALIZA TODOS OS ITENS DA LOCAÇÃO
mysqli_query($conexao, "
    UPDATE itens_locacao SET
        entrega = '$entrega',
        multa = '$multa',
        antecipacao = '$antecipacao',
        ajuste_desconto = '$desconto',
        ajuste_encargos = '$encargo'
    WHERE id_locacao = $id_locacao
");

echo "OK";