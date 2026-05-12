<?php
session_start();
include("conexao.php");

if(!isset($_SESSION['logado'])){
    header("Location: principal.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $id_cliente = intval($_POST['id_cliente']);
    $status = "ABERTO";

    date_default_timezone_set('America/Sao_Paulo');

// DATA LOCAÇÃO (ARREDONDADA)
    $agora = new DateTime();

    if($agora->format('i') > 0 || $agora->format('s') > 0){
        $agora->modify('+1 hour');
    }
    $agora->setTime($agora->format('H'), 0, 0);
    $data_locacao = $agora->format('Y-m-d H:i:s');

  
// DATA DEVOLUÇÃO (MESMA HORA)
    $data_devolucao_data = $_POST['data_devolucao'];

    // pega a mesma hora da locação
    $hora_locacao = date('H:i:s', strtotime($data_locacao));
    $data_devolucao = $data_devolucao_data . " " . $hora_locacao;

 
// CÁLCULO CORRETO DOS DIAS
    $data_locacao_obj = new DateTime($data_locacao);
    $data_devolucao_obj = new DateTime($data_devolucao);

    $intervalo = $data_locacao_obj->diff($data_devolucao_obj);

    $horas = ($intervalo->days * 24) + $intervalo->h;

    $qtd_dias = ceil($horas / 24);

// segurança: mínimo 1 dia
    if($qtd_dias <= 0){
        $qtd_dias = 1;
    }

// INSERIR LOCAÇÃO
    $sqlLocacao = "INSERT INTO locacao 
        (id_cliente, data_locacao, data_devolucao, qtd_dias, status)
        VALUES 
        ($id_cliente, '$data_locacao', '$data_devolucao', '$qtd_dias', '$status')";

    $resLocacao = mysqli_query($conexao, $sqlLocacao);

    if(!$resLocacao){
        die("ERRO AO SALVAR LOCAÇÃO: ".mysqli_error($conexao));
    }

    $id_locacao = mysqli_insert_id($conexao);

// INSERIR ITENS DA LOCAÇÃO
    if(isset($_POST['itens']) && count($_POST['itens']) > 0){

        foreach($_POST['itens'] as $item){

            $id_equip = intval($item['id_equipamento']);
            $qtd = intval($item['quantidade']);
            $valor_unit = floatval($item['valor_unitario']);

// valor base (sem dias ainda — cálculo final você já faz no sistema)
            $valor_total = $qtd * $valor_unit * $qtd_dias;

// inserir item
            $sqlItem = "INSERT INTO itens_locacao 
                (id_locacao, id_equipamento, quantidade, valor_unitario, valor_total)
                VALUES 
                ($id_locacao, $id_equip, $qtd, $valor_unit, $valor_total)";

            $resItem = mysqli_query($conexao, $sqlItem);

            if(!$resItem){
                die("Erro ao salvar item: ".mysqli_error($conexao));
            }

// atualizar estoque
            $sqlEstoque = "UPDATE equipamentos 
                           SET quantidade_total = quantidade_total - $qtd
                           WHERE id_equipamento = $id_equip";

            $resEstoque = mysqli_query($conexao, $sqlEstoque);

            if(!$resEstoque){
                die("Erro ao atualizar estoque: ".mysqli_error($conexao));
            }
        }
    }

    header("Location: locacao.php");
    exit();
}
?>