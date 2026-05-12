<?php
include("verifica_login.php");
include("conexao.php");

// 🔷 CAPTURA FILTROS
$status = $_GET['status'] ?? null;
$tipo = $_GET['tipo'] ?? null;
$ano = $_GET['ano'] ?? null;
$mes = $_GET['mes'] ?? null;
$ano_mes = $_GET['ano_mes'] ?? null;

$dados = [];

if($status && $tipo){

    $where = "WHERE 1=1";

    if($status == "fechado"){
    $where .= " AND l.status = 'FECHADO'";
    }
    elseif($status == "aberto"){
    $where .= " AND l.status = 'ABERTO'";
    }
    // FILTRO POR ANO
    if($ano){
    $where .= " AND YEAR(l.data_locacao) = ".intval($ano);
    }

{

        $sql = "
SELECT 
    'TOTAL' as periodo,
    SUM(sub.locacao) as locacao,
    SUM(sub.multa) as multa,
    SUM(sub.antecipacao) as antecipacao,
    SUM(sub.encargos) as encargos,
    SUM(sub.descontos) as descontos,
    SUM(sub.locacao + sub.multa - sub.antecipacao - sub.descontos + sub.encargos) as total
FROM (

    SELECT 
        l.id_locacao,

        SUM(i.quantidade * i.valor_unitario * l.qtd_dias) as locacao,

        MAX(i.multa) as multa,
        MAX(i.antecipacao) as antecipacao,
        MAX(i.ajuste_encargos) as encargos,
        MAX(i.ajuste_desconto) as descontos

    FROM locacao l
    JOIN itens_locacao i ON i.id_locacao = l.id_locacao

    $where

    GROUP BY l.id_locacao

) as sub
";
    }

    if($tipo == "ano"){

        $sql = "
SELECT 
    sub.periodo,
    SUM(sub.locacao) as locacao,
    SUM(sub.multa) as multa,
    SUM(sub.antecipacao) as antecipacao,
    SUM(sub.encargos) as encargos,
    SUM(sub.descontos) as descontos,
    SUM(sub.locacao + sub.multa - sub.antecipacao - sub.descontos + sub.encargos) as total
FROM (

    SELECT 
        l.id_locacao,
        YEAR(l.data_locacao) as periodo,

        SUM(i.quantidade * i.valor_unitario * l.qtd_dias) as locacao,

        MAX(i.multa) as multa,
        MAX(i.antecipacao) as antecipacao,
        MAX(i.ajuste_encargos) as encargos,
        MAX(i.ajuste_desconto) as descontos

    FROM locacao l
    JOIN itens_locacao i ON i.id_locacao = l.id_locacao

    $where

    GROUP BY l.id_locacao

) as sub

GROUP BY sub.periodo
ORDER BY sub.periodo
";

    }

if($tipo == "mes"){

$sql = "

SELECT 
    meses.periodo,

    COALESCE(SUM(sub.locacao),0) as locacao,
    COALESCE(SUM(sub.multa),0) as multa,
    COALESCE(SUM(sub.antecipacao),0) as antecipacao,
    COALESCE(SUM(sub.encargos),0) as encargos,
    COALESCE(SUM(sub.descontos),0) as descontos,

    COALESCE(SUM(
        sub.locacao 
        + sub.multa 
        - sub.antecipacao 
        - sub.descontos 
        + sub.encargos
    ),0) as total

FROM (

    SELECT '01/".$ano_mes."' as periodo
    UNION SELECT '02/".$ano_mes."'
    UNION SELECT '03/".$ano_mes."'
    UNION SELECT '04/".$ano_mes."'
    UNION SELECT '05/".$ano_mes."'
    UNION SELECT '06/".$ano_mes."'
    UNION SELECT '07/".$ano_mes."'
    UNION SELECT '08/".$ano_mes."'
    UNION SELECT '09/".$ano_mes."'
    UNION SELECT '10/".$ano_mes."'
    UNION SELECT '11/".$ano_mes."'
    UNION SELECT '12/".$ano_mes."'

) meses

LEFT JOIN (

    SELECT 
        DATE_FORMAT(l.data_locacao, '%m/%Y') as periodo,

        SUM(i.quantidade * i.valor_unitario * l.qtd_dias) as locacao,

        MAX(i.multa) as multa,
        MAX(i.antecipacao) as antecipacao,
        MAX(i.ajuste_encargos) as encargos,
        MAX(i.ajuste_desconto) as descontos

    FROM locacao l
    JOIN itens_locacao i ON i.id_locacao = l.id_locacao

    $where

    GROUP BY l.id_locacao

) sub ON sub.periodo = meses.periodo

GROUP BY meses.periodo

ORDER BY CAST(LEFT(meses.periodo,2) AS UNSIGNED)

";
}

    $res = mysqli_query($conexao, $sql);

    while($row = mysqli_fetch_assoc($res)){
        $dados[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Resultados</title>

<style>
body { font-family: "Segoe UI"; margin: 0; background: url('fundo1.png');}
.container {width: 100%; height: calc(100vh - 80px); padding: 15px; box-sizing: border-box;}
h2 {color: white;}

.botoes {display: flex; gap: 15px; margin-bottom: 20px;}

.botoes button {padding: 10px 20px;border: none;cursor: pointer;background: #3498db;color: white;    border-radius: 5px;}

button:active {transform: scale(0.95);box-shadow: inset 0px 3px 8px rgba(0,0,0,0.4);}

.linha-radios {display: flex;align-items: center;gap: 40px;background: white;padding: 8px;border-radius: 10px;
border: 1px solid black;font-size: 13px;}
.linha-radios + .linha-radios {margin-top: 10px;}
.input-padrao {padding: 8px;border-radius: 6px;border: 1px solid #ccc;}

.table-container {width: 90%;margin: 15px auto;background: white;border-radius: 10px;overflow: hidden;}
.table-container table {width: 100%;border-collapse: collapse;table-layout: fixed;}
.table-container th {background: #fbf7d2;padding: 8px;border: 1px solid #ccc;text-align: center;    font-size: 14px;}
.table-container td {padding: 8px;border: 1px solid #ccc;text-align: right;font-size: 12px;}
.table-container th:first-child,.table-container td:first-child {width: 150px;text-align: center;}
.table-container th:not(:first-child),.table-container td:not(:first-child) {width: calc((100% - 120px) / 6);}
.table-container tfoot th {background: #fbf7d2;font-weight: bold;text-align: right;}
.table-container tfoot th:first-child {text-align: center;}
</style>

<script>
function alterarTipo(){
    let tipo = document.querySelector('input[name="tipo"]:checked').value;

    document.getElementById("ano").disabled = true;
    document.getElementById("ano_mes").disabled = true;

    if(tipo === "ano"){
        document.getElementById("ano").disabled = false;
    }
    if(tipo === "mes"){
        document.getElementById("ano_mes").disabled = false;
    }
}

// BOTÃO GERAR
function gerarResultado(){
    let status = document.querySelector('input[name="status_filtro"]:checked').value;
    let tipo = document.querySelector('input[name="tipo"]:checked').value;
    let ano = document.getElementById("ano").value;
    let ano_mes = document.getElementById("ano_mes").value;
    let url = "resultados.php?status="+status+"&tipo="+tipo;

    if(tipo === "ano"){
        url += "&ano="+ano;
    }
    if(tipo === "mes"){
        url += "&ano_mes="+ano_mes;
    }

    window.location.href = url;
}
</script>

</head>
<body>

<div class="container">
<h2>APURAÇÃO DE RESULTADOS</h2>

<div class="botoes">
    <button onclick="gerarResultado()">GERAR RESULTADO</button>
    <button onclick="window.location.href='principal.php'">VOLTAR</button>
</div>

<!-- STATUS -->
<div class="linha-radios">
    <div><input type="radio" name="status_filtro" value="fechado" checked> STATUS FECHADO</div>
    <div><input type="radio" name="status_filtro" value="todos"> STATUS ABERTO / FECHADO</div>
    <div><input type="radio" name="status_filtro" value="aberto"> STATUS ABERTO</div>
</div>

<!-- TIPO -->
<div class="linha-radios">
    <div><input type="radio" name="tipo" value="geral" onclick="alterarTipo()"> RESULTADO GERAL</div>

    <div>
        <input type="radio" name="tipo" value="ano" onclick="alterarTipo()" checked> RESULTADO POR ANO
        <select id="ano" class="input-padrao">
            <?php for($i = date('Y'); $i >= 2025; $i--) echo "<option>$i</option>"; ?>
        </select>
    </div>

    <div>
        <input type="radio" name="tipo" value="mes" onclick="alterarTipo()"> RESULTADO MÊS/ANO
        <select id="ano_mes" class="input-padrao" disabled>
            <?php for($i = date('Y'); $i >= 2025; $i--) echo "<option>$i</option>"; ?>
        </select>
    </div>
</div>

<!-- TABELA -->
<div class="table-container">
<table>

<thead>
<tr>
    <th>PERÍODO</th>
    <th>LOCAÇÃO</th>
    <th>MULTA</th>
    <th>ANTECIPAÇÃO</th>
    <th>ENCARGOS</th>
    <th>DESCONTOS</th>
    <th>TOTAL</th>
</tr>
</thead>

<tbody>

<?php
$total_locacao=0;
$total_multa=0;
$total_antecipacao=0;
$total_encargos=0;
$total_descontos=0;
$total_geral=0;

foreach($dados as $row){

    echo "<tr>
        <td>".$row['periodo']."</td>
        <td>".number_format($row['locacao'],2,',','.')."</td>
        <td>".number_format($row['multa'],2,',','.')."</td>
        <td>".number_format($row['antecipacao'],2,',','.')."</td>
        <td>".number_format($row['encargos'],2,',','.')."</td>
        <td>".number_format($row['descontos'],2,',','.')."</td>
        <td>".number_format($row['total'],2,',','.')."</td>
    </tr>";

    $total_locacao += $row['locacao'];
    $total_multa += $row['multa'];
    $total_antecipacao += $row['antecipacao'];
    $total_encargos += $row['encargos'];
    $total_descontos += $row['descontos'];
    $total_geral += $row['total'];
}
?>

</tbody>

<tfoot>
<tr>
    <th>TOTAL</th>
    <th><?=number_format($total_locacao,2,',','.')?></th>
    <th><?=number_format($total_multa,2,',','.')?></th>
    <th><?=number_format($total_antecipacao,2,',','.')?></th>
    <th><?=number_format($total_encargos,2,',','.')?></th>
    <th><?=number_format($total_descontos,2,',','.')?></th>
    <th><?=number_format($total_geral,2,',','.')?></th>
</tr>
</tfoot>

</table>
</div>

</div>

</body>
</html>