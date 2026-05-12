<?php
include("conexao.php");

if(isset($_GET['id'])){
    $id = $_GET['id'];

    // 🔎 1. VERIFICAR STATUS PRIMEIRO
    $result = mysqli_query($conexao, "SELECT status FROM locacao WHERE id_locacao = $id");
    $row = mysqli_fetch_assoc($result);

    if($row['status'] == 'FECHADO'){
    header("Location: locacao.php?erro=fechado");
    exit();
    }

    // 🔴 2. DEVOLVER ESTOQUE
    $itens = mysqli_query($conexao, "SELECT * FROM itens_locacao WHERE id_locacao = $id");

    while($item = mysqli_fetch_assoc($itens)){
        $id_equip = $item['id_equipamento'];
        $qtd = $item['quantidade'];

        mysqli_query($conexao, "UPDATE equipamentos 
                                SET quantidade_total = quantidade_total + $qtd
                                WHERE id_equipamento = $id_equip");
    }

    // 🔴 3. EXCLUIR ITENS
    mysqli_query($conexao, "DELETE FROM itens_locacao WHERE id_locacao = $id");

    // 🔴 4. EXCLUIR LOCAÇÃO
    mysqli_query($conexao, "DELETE FROM locacao WHERE id_locacao = $id");
}

header("Location: locacao.php");
exit();
?>