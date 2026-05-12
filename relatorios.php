<?php
include("verifica_login.php");
include("conexao.php");

// 🔎 CAPTURA FILTROS (UMA ÚNICA VEZ)
$tipo = $_GET['tipo'] ?? '';
$nome_cliente = $_GET['nome_cliente'] ?? '';
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$status = $_GET['status'] ?? '';
$categoria = $_GET['categoria'] ?? '';
$equipamento = $_GET['equipamento'] ?? '';

// 🔷 QUERY BASE
$sql = "SELECT 
            l.id_locacao,
            l.data_locacao,
            l.data_dev_real,
            l.status,
            l.qtd_dias,
            c.nome AS nome_cliente,
            e.descricao AS nome_equipamento,
            e.categoria,
            i.quantidade,  
            i.valor_unitario,
            i.valor_total,
            i.multa,
            i.antecipacao,
            i.ajuste_desconto,
            i.ajuste_encargos,

            tot.total_locacao

        FROM locacao l

        INNER JOIN itens_locacao i 
            ON l.id_locacao = i.id_locacao

        INNER JOIN (
            SELECT id_locacao, SUM(valor_total) AS total_locacao
            FROM itens_locacao
            GROUP BY id_locacao
        ) tot 
            ON tot.id_locacao = l.id_locacao

        INNER JOIN clientes c 
            ON l.id_cliente = c.id_cliente

        INNER JOIN equipamentos e 
            ON i.id_equipamento = e.id_equipamento

        WHERE 1=1";

// CRITÉRIOS
if($tipo == "cliente"){
    $sql .= " AND c.nome LIKE '%$nome_cliente%'";
}
if($tipo == "cliente_data"){
    $sql .= " AND c.nome LIKE '%$nome_cliente%'";
    $sql .= " AND l.data_locacao BETWEEN '$data_inicio' AND '$data_fim'";
}
if($tipo == "data"){
    $sql .= " AND l.data_locacao BETWEEN '$data_inicio' AND '$data_fim'";
}
if($tipo == "data_status"){
    $sql .= " AND l.data_locacao BETWEEN '$data_inicio' AND '$data_fim'";
    $sql .= " AND l.status = '$status'";
}
if($tipo == "cliente_status"){
    $sql .= " AND c.nome LIKE '%$nome_cliente%'";
    $sql .= " AND l.status = '$status'";
}
if($tipo == "categoria"){
    $sql .= " AND e.categoria LIKE '%$categoria%'";
}
if($tipo == "categoria_data"){
    $sql .= " AND e.categoria LIKE '%$categoria%'";
    $sql .= " AND l.data_locacao BETWEEN '$data_inicio' AND '$data_fim'";
}
if($tipo == "equipamento"){
    $sql .= " AND e.descricao LIKE '%$equipamento%'";
}
if($tipo == "equipamento_data"){
    $sql .= " AND e.descricao LIKE '%$equipamento%'";
    $sql .= " AND l.data_locacao BETWEEN '$data_inicio' AND '$data_fim'";
}

$sql .= " ORDER BY l.id_locacao, i.id_item";

$resultado = mysqli_query($conexao, $sql);

$tipo = '';
$nome_cliente = '';
$data_inicio = '';
$data_fim = '';
$status = '';
$categoria = '';
$equipamento = '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Relatórios</title>

<style>

body { font-family: "Segoe UI"; margin: 0; background: url('fundo1.png'); }

.container {width: 100%;height: calc(100vh - 80px);padding: 15px;box-sizing: border-box;display: flex;        flex-direction: column;}

h2 { color: white;}

.botoes {display: flex;gap: 20px;margin-bottom: 20px;}

.botoes button {padding: 10px 20px;border: none;cursor: pointer;background: #3498db;color: white;    border-radius: 5px;}

button:active {transform: scale(0.95);box-shadow: inset 0px 3px 8px rgba(0,0,0,0.4);}

.linha-radios {display: flex;align-items: center;gap: 20px;background: white;padding: 10px;border-radius: 10px;
    border: 1px solid black;font-size: 13px;margin-bottom: 15px;flex-wrap: wrap;}

.linha-radios + .linha-radios {margin-top: 10px;}

.input-padrao {height: 40px;padding: 0 12px;border-radius: 6px;border: 1px solid #ccc;font-size: 13px;    min-width: 140px;}

#nome_cliente {min-width: 250px;}

.table-container {width: 100%;min-height: 495px;max-height: 495px;margin: 1px auto;background: white;    border-radius: 10px;overflow-y: auto;}

/* TABELA */
.table-container table {width: 100%;border-collapse: collapse;table-layout: fixed;}

.table-container th {background: white;padding: 10px;border: 1px solid #ccc;text-align: center !important;
    font-size: 13px;position: sticky;top: 0;z-index: 2;}

.table-container td {padding: 6px 7px;border: 1px solid #ccc;text-align: right;font-size: 12px;}

.table-container th:first-child,.table-container td:first-child {width: 150px;text-align: center;}
.table-container th:not(:first-child),.table-container td:not(:first-child) {width: calc((100% - 150px) / 10);
}

.table-container th:nth-child(1),.table-container td:nth-child(1) {width: 60px;text-align: center;}
.table-container th:nth-child(2),.table-container td:nth-child(2) {width: 260px;text-align: left;}
/* 3 - EQUIPAMENTO */
.table-container th:nth-child(3),.table-container td:nth-child(3) {width: 190px;text-align: left;}
.table-container th:nth-child(4),.table-container td:nth-child(4) {width: 120px;text-align: center;}
.table-container th:nth-child(5),.table-container td:nth-child(5) {width: 80px;text-align: center;}
/* 5 - QUANTIDADE */
.table-container th:nth-child(6),.table-container td:nth-child(6) {width: 80px;text-align: center;}
/* 6 - VALOR UNIT */
.table-container th:nth-child(7),.table-container td:nth-child(7) {width: 100px;text-align: right;}
/* 7 - TOTAL */
.table-container th:nth-child(8),.table-container td:nth-child(8) {width: 100px;text-align: right;}
/* 8 - MULTA - ANTECIPAÇÃO*/
.table-container th:nth-child(9),.table-container td:nth-child(9) {width: 100px;text-align: right;}
.table-container th:nth-child(10),.table-container td:nth-child(10) {width: 100px;text-align: right;}
/* 9 - DESCONTO */
.table-container th:nth-child(11),.table-container td:nth-child(11) {width: 100px;text-align: right;}
/* 10 - ENCARGOS */
.table-container th:nth-child(12),.table-container td:nth-child(12) {width: 100px;text-align: right;}
.table-container th:nth-child(13),.table-container td:nth-child(13) {width: 100px;text-align: right;}
/* 11 - DATA LOCAÇÃO */
.table-container th:nth-child(14),.table-container td:nth-child(14) {width: 130px;text-align: center;}
/* 12 - DATA DEVOLUÇÃO */
.table-container th:nth-child(15),.table-container td:nth-child(15) {width: 130px;text-align: center;}
/* 13 - STATUS */
.table-container th:nth-child(16),.table-container td:nth-child(16) {width: 100px;text-align: center;}

/* destaque valor total */
.linha-total {background-color: #fbf7d2;}

</style>

<script>
function controlarCampos() {
    let tipo = document.getElementById("tipo").value;
    let nome_cliente = document.getElementById("nome_cliente");
    let data_inicio = document.getElementById("data_inicio");
    let data_fim = document.getElementById("data_fim");
    let status = document.getElementById("status");
    let categoria = document.getElementById("categoria");
    let equipamento = document.getElementById("equipamento");

    nome_cliente.disabled = true;
    data_inicio.disabled = true;
    data_fim.disabled = true;
    status.disabled = true;
    categoria.disabled = true;
    equipamento.disabled = true;

    if(tipo === "cliente"){ nome_cliente.disabled = false; }
    if(tipo === "cliente_data"){ nome_cliente.disabled = false; data_inicio.disabled = false; data_fim.disabled = false; }
    if(tipo === "cliente_status"){ nome_cliente.disabled = false; status.disabled = false; }
    if(tipo === "data"){ data_inicio.disabled = false; data_fim.disabled = false; }
    if(tipo === "data_status"){ data_inicio.disabled = false; data_fim.disabled = false; status.disabled = false; }
    if(tipo === "categoria"){ categoria.disabled = false; }
    if(tipo === "categoria_data"){ categoria.disabled = false; data_inicio.disabled = false; data_fim.disabled = false; }
    if(tipo === "equipamento"){equipamento.disabled = false;}
    if(tipo === "equipamento_data"){equipamento.disabled = false;data_inicio.disabled = false;data_fim.disabled = false;}

}
</script>

</head>

<body>

<div class="container">
<h2>RELATÓRIOS</h2>

<form method="GET">

<div class="linha-radios">
<select name="tipo" id="tipo" class="input-padrao" onchange="controlarCampos()">
<option value="">Selecione</option>
<option value="cliente">Cliente</option>
<option value="cliente_data">Cliente + Datas</option>
<option value="cliente_status">Cliente + Status</option>
<option value="data">Datas</option>
<option value="data_status">Datas + Status</option>
<option value="equipamento">Equipamentos</option>
<option value="equipamento_data">Equipamentos + Datas</option>
<option value="categoria">Categoria</option>
<option value="categoria_data">Categoria + Datas</option>
</select>

<input type="text" name="nome_cliente" id="nome_cliente" class="input-padrao" placeholder="Nome do cliente" disabled>
<input type="text" name="equipamento" id="equipamento" class="input-padrao" placeholder="Equipamento" disabled>
<input type="text" name="categoria" id="categoria" class="input-padrao" placeholder="Categoria" disabled>
<input type="date" name="data_inicio" id="data_inicio" class="input-padrao" disabled>
<input type="date" name="data_fim" id="data_fim" class="input-padrao" disabled>

<select name="status" id="status" class="input-padrao" disabled>
<option value="">Status</option>
<option value="ABERTO">ABERTO</option>
<option value="FECHADO">FECHADO</option>
</select>
</div>

<div class="botoes">
<button type="submit">PESQUISAR</button>
<button type="button" onclick="window.location.href='relatorios.php'">LIMPAR PESQUISA</button>
<button type="button" onclick="window.location.href='principal.php'">VOLTAR</button>
</div>

</form>

<div class="table-container">
<table>

<thead>
<tr>
<th>LOCAÇÃO</th>
<th>CLIENTE</th>
<th>EQUIPAMENTO</th>
<th>CATEGORIA</th>
<th>DIAS_LOC.</th>
<th>QTD_EQUIP.</th>
<th>VL UNIT</th>
<th>TOTAL</th>
<th>MULTA</th>
<th>ANTECIPAÇÃO</th>
<th>DESCONTO</th>
<th>ENCARGOS</th>
<th>VALOR FINAL</th>
<th>DATA LOCAÇÃO</th>
<th>DATA DEV_REAL</th>
<th>STATUS</th>
</tr>
</thead>

<tbody>

<?php 
$locacao_atual = null;
$linhas = [];

while($row = mysqli_fetch_assoc($resultado)) {

    if($locacao_atual !== null && $locacao_atual != $row['id_locacao']){

        // SOMA DOS ITENS
        $valor_itens = 0;

        foreach($linhas as $l){

            $valor_itens += 
                (float)$l['qtd_dias'] *
                (float)$l['quantidade'] *
                (float)$l['valor_unitario'];
        }

        // VALOR FINAL
        $valor_final =
            $valor_itens
            + (float)$linhas[0]['multa']
            - (float)$linhas[0]['antecipacao']
            + (float)$linhas[0]['ajuste_encargos']
            - (float)$linhas[0]['ajuste_desconto'];

        foreach($linhas as $i => $linha){
?>
<tr class="<?= ($i == 0) ? 'linha-total' : '' ?>">
<td><?= $linha['id_locacao'] ?></td>
<td><?= $linha['nome_cliente'] ?></td>
<td><?= $linha['nome_equipamento'] ?></td>
<td><?= $linha['categoria'] ?></td>

<td><?= $linha['qtd_dias'] ?></td>
<td><?= $linha['quantidade'] ?></td>

<td><?= number_format($linha['valor_unitario'],2,',','.') ?></td>

<td>
<?= number_format(
    $linha['qtd_dias'] *
    $linha['quantidade'] *
    $linha['valor_unitario']
,2,',','.') ?>
</td>

<?php if($i == 0){ ?>
<td><?= number_format($linha['multa'],2,',','.') ?></td>
<td><?= number_format($linha['antecipacao'],2,',','.') ?></td>
<td><?= number_format($linha['ajuste_desconto'],2,',','.') ?></td>
<td><?= number_format($linha['ajuste_encargos'],2,',','.') ?></td>
<td><?= number_format($valor_final,2,',','.') ?></td>
<?php } else { ?>

<td></td><td></td><td></td><td></td><td></td>
<?php } ?>

<td><?= date('d/m/Y', strtotime($linha['data_locacao'])) ?></td>

<td>
<?= $linha['data_dev_real'] 
? date('d/m/Y', strtotime($linha['data_dev_real'])) 
: '' ?>
</td>

<td><?= $linha['status'] ?></td>

</tr>
<?php
        }

        // 🔄 RESET
        $linhas = [];
    }

    $linhas[] = $row;
    $locacao_atual = $row['id_locacao'];
}

if(count($linhas) > 0){

    $valor_itens = 0;

    foreach($linhas as $l){

        $valor_itens += 
            (float)$l['qtd_dias'] *
            (float)$l['quantidade'] *
            (float)$l['valor_unitario'];
    }

    $valor_final =
        $valor_itens
        + (float)$linhas[0]['multa']
        - (float)$linhas[0]['antecipacao']
        + (float)$linhas[0]['ajuste_encargos']
        - (float)$linhas[0]['ajuste_desconto'];

    foreach($linhas as $i => $linha){
?>
<tr>
<td><?= $linha['id_locacao'] ?></td>
<td><?= $linha['nome_cliente'] ?></td>
<td><?= $linha['nome_equipamento'] ?></td>
<td><?= $linha['categoria'] ?></td>

<td><?= $linha['qtd_dias'] ?></td>
<td><?= $linha['quantidade'] ?></td>

<td><?= number_format($linha['valor_unitario'],2,',','.') ?></td>

<td>
<?= number_format(
    $linha['qtd_dias'] *
    $linha['quantidade'] *
    $linha['valor_unitario']
,2,',','.') ?>
</td>

<?php if($i == 0){ ?>
<td><?= number_format($linha['multa'],2,',','.') ?></td>
<td><?= number_format($linha['antecipacao'],2,',','.') ?></td>
<td><?= number_format($linha['ajuste_desconto'],2,',','.') ?></td>
<td><?= number_format($linha['ajuste_encargos'],2,',','.') ?></td>
<td><?= number_format($valor_final,2,',','.') ?></td>
<?php } else { ?>

<td></td><td></td><td></td><td></td><td></td>
<?php } ?>

<td><?= date('d/m/Y', strtotime($linha['data_locacao'])) ?></td>

<td>
<?= $linha['data_dev_real'] 
? date('d/m/Y', strtotime($linha['data_dev_real'])) 
: '' ?>
</td>

<td><?= $linha['status'] ?></td>

</tr>
<?php
    }
}
?>

</tbody>
</table>
</div>
</div>

<script>controlarCampos();</script>
</body>
</html>