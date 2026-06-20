<?php

include("verifica_login.php");
include("conexao.php"); 

// VISUALIZAÇÃO
$itens_visualizar = null;

if(isset($_GET['ver_itens'])){
    $id_loc = $_GET['ver_itens'];

    $sql_itens = "SELECT i.*, e.descricao 
                  FROM itens_locacao i
                  JOIN equipamentos e ON e.id_equipamento = i.id_equipamento
                  WHERE i.id_locacao = $id_loc";

    $itens_visualizar = mysqli_query($conexao, $sql_itens);
}

// valor total locação na modal vizualizar
$valor_total_locacao = 0;

if($itens_visualizar){
    while($item = mysqli_fetch_assoc($itens_visualizar)){
        $valor_total_locacao += $item['valor_total'];
        $itens_array[] = $item;
    }
}

// PEGAR CLIENTES PARA O SELECT
$clientes = mysqli_query($conexao, "SELECT id_cliente, nome FROM clientes");

// PEGAR EQUIPAMENTOS DISPONÍVEIS
$equipamentos = mysqli_query($conexao, "SELECT id_equipamento, descricao, quantidade_total, valor_locacao_dia FROM equipamentos");

if(isset($_GET['devolver'])){
    $id = $_GET['devolver'];

    $itens = mysqli_query($conexao,"
        SELECT i.*, e.descricao 
        FROM itens_locacao i
        JOIN equipamentos e ON e.id_equipamento = i.id_equipamento
        WHERE i.id_locacao = $id
    ");

    echo "<script>
        document.addEventListener('DOMContentLoaded', function(){
            abrirModal('modalDevolucao');
        });
    </script>";
}

if(isset($_GET['devolver'])){
    $id = $_GET['devolver'];

    $itens_devolucao = mysqli_query($conexao,"
        SELECT i.*, e.descricao 
        FROM itens_locacao i
        JOIN equipamentos e ON e.id_equipamento = i.id_equipamento
        WHERE i.id_locacao = $id
    ");
}

if(isset($_GET['devolver'])){

    $id = $_GET['devolver'];

    // 🔷 BUSCA STATUS + DATA PREVISTA
    $resLoc = mysqli_query($conexao,"
        SELECT status, data_devolucao
        FROM locacao 
        WHERE id_locacao = $id
    ");

    $dadosLoc = mysqli_fetch_assoc($resLoc);

    $status_loc = $dadosLoc['status'];
    $data_prevista = $dadosLoc['data_devolucao'];

    echo "<script>
        window.onload = function(){
            abrirModal('modalDevolucao');
        }
    </script>";
}

// DEVOLUÇÃO (já existe)
if(isset($_GET['devolver'])){
    $id = $_GET['devolver'];
    
}

if(isset($_GET['ver_devolucao'])){
    echo "<script>
        document.addEventListener('DOMContentLoaded', function(){
            abrirModal('modalVisualizarDevolucao');
        });
    </script>";
}

// PESQUISA
$tipo_pesquisa = $_GET['tipo_pesquisa'] ?? '';
$valor_pesquisa = $_GET['valor_pesquisa'] ?? '';

$filtro = "";

// CLIENTE
if($tipo_pesquisa == 'cliente' && $valor_pesquisa != ''){

    $filtro = "
    AND c.nome LIKE '%$valor_pesquisa%'
    ";
}

// DATA LOCAÇÃO
if($tipo_pesquisa == 'data_locacao' && $valor_pesquisa != ''){

    $filtro = "
    AND DATE(l.data_locacao) = '$valor_pesquisa'
    ";
}

// DATA DEVOLUÇÃO
if($tipo_pesquisa == 'data_devolucao' && $valor_pesquisa != ''){

    $filtro = "
    AND DATE(l.data_dev_real) = '$valor_pesquisa'
    ";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Locação de Equipamentos</title>
<style>
body{font-family:"Segoe UI";margin:0;background: url('fundo1.png');}
.container{width:100%;padding:15px;box-sizing:border-box;}
h2{color:white;}

.botoes{margin-bottom:15px;}
.botoes button{padding:10px 20px;margin-right:10px;border:none;cursor:pointer;background:#3498db;color:white;border-radius:5px;}
button:active{transform:scale(0.95);box-shadow:inset 0px 3px 8px rgba(0,0,0,0.4);}

/* TABELA LOCAÇÃO */
.table-container{height:600px;border-radius: 10px;max-height:calc(100vh - 150px);overflow-y:auto;background:white;}
table{width:100%;border-collapse:collapse;}
th{background:white; color:black;text-align:center;padding:10px;border:1px solid #ccc;font-size:13px;}
td{padding:5px 7px;border:1px solid #ccc;font-size:13px;}

/* COLUNAS */
th:nth-child(1),td:nth-child(1){width:120px;text-align:center;}
th:nth-child(2),td:nth-child(2){width:250px;}
th:nth-child(3),td:nth-child(3){width:110px;text-align:center;}
th:nth-child(4),td:nth-child(4){width:110px;text-align:center;}
th:nth-child(5),td:nth-child(5){width:90px;text-align:center;}
th:nth-child(6),td:nth-child(6){width:120px;0px;text-align:center;}
th:nth-child(7),td:nth-child(7){width:110px;0px;text-align:center;}

/* LINHAS */
tr:hover{background:#f2f2f2;}
tr.selecionado{background:#483ba0 !important;color:white;}
.status-fechado{background:#fbf7d2 !important;color:#000;}

/* MODAL */
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);overflow-y:auto;}
.modal-content{background:#f4f6f7;width:800px;max-width:90%;margin:5% auto;padding:30px;border-radius:12px;}

/* FORM */
.form-linha{display:flex;gap:20px;margin-bottom:15px;}
.form-grupo{flex:1;}
.form-grupo_1{flex:1;text-align: center;}
.form-grupo label{display:block;margin-bottom:6px;}
.form-grupo input,.form-grupo select{width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;box-sizing:border-box;}
input[readonly]{background:#ecf0f1;cursor:not-allowed;}

/* BOTÕES MODAL */
.botoes-modal{margin-top:20px;display:flex;justify-content:space-between;}
.salvar{background:#3498db;color:white;border:none;padding:10px;width:45%;border-radius:10px;font-weight:bold;}
.salvar:hover{background:#2c80b4;}
.cancelar{background:#bdc3c7;color:black;border:none;padding:10px;width:45%;border-radius:10px;font-weight:bold;}
.cancelar:hover{background:#95a5a6;}

/* TABELA ITENS */
.table-itens{width:100%;border-collapse:collapse;margin-top:15px;}
.table-itens th,.table-itens td{padding:8px;border:1px solid #ccc;font-size:13px;text-align:center;}

/* VALOR TOTAL */
.valor-total-container{margin-top:15px;display:flex;justify-content:flex-end;align-items:center;gap:10px;}
.valor-total-container label{font-weight:bold;font-size:14px;color:#2c3e50;}
.valor-total-container input{width:180px;padding:10px;font-size:14px;font-weight:bold;text-align:right;border-radius:10px;border:1px solid #ccc;background:#759fd6;color:#021d37;}

/* BOTÃO EXCLUIR */
.excluir{background:#e74c3c;color:white;border:none;padding:10px;width:48%;border-radius:10px;font-weight:bold;cursor:pointer;}
.excluir:hover{background:#c0392b;}

/* CAMPO QUANT. DIAS  - modal locacao*/
.qtd-dias {margin-top:15px;display:flex;justify-content:flex-end;align-items:center;gap:10px;}
.qtd-dias label{font-weight:bold;font-size:14px;color:#2c3e50;}
.qtd-dias input{width:250px;padding:10px;font-size:14px;font-weight:bold;text-align:center;border-radius:10px;border:1px solid #ccc;background:#f4d03f;color:#021d37;}

/* QUANTIDADE → centralizado */
#modalItens #quantidade {text-align: center;}

/* VALOR UNITÁRIO → alinhado à esquerda */
#modalItens #valor_unitario {text-align: right;}

/* VALOR TOTAL MODAL VISUALIZAR ITENS*/
.valor-total input {background: #f4d03f;color: #000;font-weight: bold;text-align: right;border: 1px solid #58d68d;width: 250px;}

.valor-total {margin-top:15px;display:flex;justify-content:flex-end;align-items:center;gap:10px;}
.valor-total label{font-weight:bold;font-size:14px;color:#2c3e50;}

/* CSS MODAL ITENS */
#modalItens .modal-content {width: 1000px;max-width: 95%;}

/* TABELA MODAL MODAL ITENS */
#modalItens .table-itens th:nth-child(1),
#modalItens .table-itens td:nth-child(1) {width: 40%; text-align: left;}
#modalItens .table-itens th:nth-child(2),
#modalItens .table-itens td:nth-child(2) {width: 8%;text-align: center;}
#modalItens .table-itens th:nth-child(3),
#modalItens .table-itens td:nth-child(3) {width: 8%;text-align: center;}
#modalItens .table-itens th:nth-child(4),
#modalItens .table-itens td:nth-child(4) {width: 15%;text-align: right;}
#modalItens .table-itens th:nth-child(5),
#modalItens .table-itens td:nth-child(5) {width: 40%;text-align: right;}
#modalItens .table-itens th:nth-child(6),
#modalItens .table-itens td:nth-child(6) {width: 5%;text-align: center;}

/* ===== TABELA MODAL VISUALIZAR ITENS ===== */
#modalVisualizarItens .table-itens th:nth-child(1),
#modalVisualizarItens .table-itens td:nth-child(1) {width: 370px; text-align: left;}
#modalVisualizarItens .table-itens th:nth-child(2),
#modalVisualizarItens .table-itens td:nth-child(2) {width: 80px;text-align: center;}
#modalVisualizarItens .table-itens th:nth-child(3),
#modalVisualizarItens .table-itens td:nth-child(3) {width: 80px;text-align: center;}
#modalVisualizarItens .table-itens th:nth-child(4),
#modalVisualizarItens .table-itens td:nth-child(4) {width: 130px;text-align: right;}
#modalVisualizarItens .table-itens th:nth-child(5),
#modalVisualizarItens .table-itens td:nth-child(5) {width: 130px;text-align: right;}

/* VALOR TOTAL À DIREITA NA MODAL DEVOLUÇÃO*/
.valor-direita input {text-align: right;font-weight: bold;}

/* DATA PREVISTA (visual diferente) MODAL DEVOLUÇÃO*/
#data_prevista {background: #ecf0f1;text-align: center;}

/* TAMNHO DA TABELA NAS MODAIS */
.table-itens-container {max-height: 230px;overflow-y: auto;border: 1px solid #ccc;}

#modalDevolucao .form-linha {margin-top: 15px;}

#modalDevolucao .modal-content {margin: 3% auto;}

/* SOMENTE CAMPOS DE VALOR À DIREITA */
#modalDevolucao #multa,
#modalDevolucao #antecipacao,
#modalDevolucao #ajuste_desconto,
#modalDevolucao #ajuste_encargo,
#modalDevolucao #valor_total,
#modalDevolucao #valor_final {text-align: right;}

/* ===== TABELA MODAL DEVOLUÇÃO ===== */
#modalDevolucao .table-itens th:nth-child(1),
#modalDevolucao .table-itens td:nth-child(1) {width: 370px;text-align: left;}
#modalDevolucao .table-itens th:nth-child(2),
#modalDevolucao .table-itens td:nth-child(2) {width: 80px;text-align: center;}
#modalDevolucao .table-itens th:nth-child(3),
#modalDevolucao .table-itens td:nth-child(3) {width: 80px;text-align: center;}
#modalDevolucao .table-itens th:nth-child(4),
#modalDevolucao .table-itens td:nth-child(4) {width: 130px;text-align: right;}
#modalDevolucao .table-itens th:nth-child(5),
#modalDevolucao .table-itens td:nth-child(5) {width: 130px;text-align: right;} 

#modalDevolucao #debug_dias {text-align: center;font-size:14px;}

/* STATUS CENTRALIZADO NA MODAL DEVOLUÇÃO*/
.status-central input {text-align: center;font-weight: bold;}

#valor_final {font-weight: bold;background: #f4d03f;font-size:14px;}

/* ===== SOMENTE MODAL VISUALIZAR ===== */
#modalVisualizarDevolucao .form-linha {margin-top: 15px;}

#modalVisualizarDevolucao .modal-content {margin: 3% auto;}

/* valores à direita (somente visualizar) */
#modalVisualizarDevolucao #multa,
#modalVisualizarDevolucao #antecipacao,
#modalVisualizarDevolucao #ajuste_desconto,
#modalVisualizarDevolucao #ajuste_encargo,
#modalVisualizarDevolucao #valor_total,
#modalVisualizarDevolucao #valor_final {text-align: right;}

#modalVisualizarDevolucao .table-itens th:nth-child(1),
#modalVisualizarDevolucao .table-itens td:nth-child(1) {width: 370px;text-align: left;}
#modalVisualizarDevolucao .table-itens th:nth-child(2),
#modalVisualizarDevolucao .table-itens td:nth-child(2) {width: 80px;text-align: center;}
#modalVisualizarDevolucao .table-itens th:nth-child(3),
#modalVisualizarDevolucao .table-itens td:nth-child(3) {width: 80px;text-align: center;}
#modalVisualizarDevolucao .table-itens th:nth-child(4),
#modalVisualizarDevolucao .table-itens td:nth-child(4) {width: 130px;text-align: right;}
#modalVisualizarDevolucao .table-itens th:nth-child(5),
#modalVisualizarDevolucao .table-itens td:nth-child(5) {width: 130px;text-align: right;}

/* status centralizado */
#modalVisualizarDevolucao .status-central input {text-align: center;font-weight: bold;}

/* valor final destaque */
#modalVisualizarDevolucao #valor_final {font-weight: bold;background: #f4d03f;font-size: 14px;}

/* botão centralizado */
#modalVisualizarDevolucao .botoes-modal {display: flex;justify-content: center;}
#modalVisualizarDevolucao #data_prevista {background: #ecf0f1;text-align: center;}

#btnCancelarDev {margin-left: auto;background: #e74c3c !important;color: white;font-weight: bold;}
.botoes {display: flex;gap: 10px;}

</style>

<script>
let itensCarrinho = [];
let idSelecionado = null;
let nomeClienteSelecionado = "";
let qtdDiasLocacao = 1;
let posicaoScroll = 0;

// MODAIS
function abrirModal(id){
    document.getElementById(id).style.display = "block";

    if(id === "modalIncluirLocacao"){
        let hoje = new Date();
        let dia = String(hoje.getDate()).padStart(2,'0');
        let mes = String(hoje.getMonth()+1).padStart(2,'0');
        let ano = hoje.getFullYear();

        document.getElementById("data_locacao").value = dia+"/"+mes+"/"+ano;
        document.getElementById("status").value = "ABERTO";
    }
}

function fecharModal(id){
    document.getElementById(id).style.display = "none";

// recarregar com seleção
    if(idSelecionado){
        window.location.href = "locacao.php?selecionar=" + idSelecionado;
    }
}

function fecharModalFoco(modalId){
    document.getElementById(modalId).style.display = "none";

    setTimeout(function(){
        if(typeof idSelecionado === "undefined" || idSelecionado === null) return;
        let linhas = document.querySelectorAll("#tabelaLocacoes tr");
        for(let i = 1; i < linhas.length; i++){ // ignora header
            let idLinha = linhas[i].cells[0]?.innerText.trim();
            if(idLinha == idSelecionado){
                linhas[i].scrollIntoView({
                    behavior: "auto",
                    block: "center"
                });

                break;
            }
        }

    }, 50);
}

function abrirModalItens(){
    let cliente = document.querySelector("select[name='id_cliente']").value;
    let dataLocacao = document.getElementById("data_locacao").value;
    let dataDevolucao = document.querySelector("input[name='data_devolucao']").value;
    let dias = document.getElementById("qtd_dias").value;

    if(!cliente){
        alert("SELECIONE UM CLIENTE!");
        return;
    }
    if(!dataDevolucao){
        alert("INFORME A DATA DE DEVOLUÇÃO!");
        return;
    }
    let partes = dataLocacao.split("/");
    let dataLocFormatada = partes[2] + "-" + partes[1] + "-" + partes[0];
    if(dataDevolucao < dataLocFormatada){
        alert("DATA DE DEVOLUÇÃO INCOMPATÍVEL!");
        return;
    }

    qtdDiasLocacao = parseInt(dias) || 1;

    atualizarTabelaItens();
    abrirModal('modalItens');
}

// ITENS
function adicionarItem(){
    let selectEq = document.getElementById("equipamento");
    let option = selectEq.options[selectEq.selectedIndex];
    let equipamento = option.text;
    let idEquip = selectEq.value;
    let quantidade = parseInt(document.getElementById("quantidade").value);
    let valorUnit = parseFloat(document.getElementById("valor_unitario").value);

    if(!idEquip){
        alert("SELECIONE UM EQUIPAMENTO!");
        return;
    }
    if(!quantidade || quantidade <= 0){
        alert("INFORME UMA QUANTIDADE VÁLIDA!");
        return;
    }

    // valida estoque
    let estoque = parseInt(option.dataset.estoque);
    let jaSelecionado = 0;

    itensCarrinho.forEach(item => {
        if(item.id_equipamento == idEquip){
            jaSelecionado += item.quantidade;
        }
    });

    let disponivel = estoque - jaSelecionado;

    if(quantidade > disponivel){
        alert("QUANTIDADE INDISPONÍVEL! DISPONIBILIDADE: " + disponivel);
        return;
    }

    itensCarrinho.push({
        id_equipamento: idEquip,
        descricao: equipamento,
        quantidade: quantidade,
        valor_unitario: valorUnit,
        valor_total: 0 // agora é calculado dinamicamente
    });

    atualizarTabelaItens();

    // limpar campos
    selectEq.value = "";
    document.getElementById("quantidade").value = "";
    document.getElementById("valor_unitario").value = "";
}

// TABELA ITENS
function atualizarTabelaItens(){
    let tbody = document.getElementById("tbodyItens");
    tbody.innerHTML = "";
    let total = 0;

    itensCarrinho.forEach((item, index) => {

        // 🔴 cálculo usando variável global
        let valorTotalItem = item.quantidade * item.valor_unitario * qtdDiasLocacao;
        let tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${item.descricao}</td>
            <td>${item.quantidade}</td>
            <td>${qtdDiasLocacao}</td>
            <td>${item.valor_unitario.toFixed(2)}</td>
            <td>${valorTotalItem.toFixed(2)}</td>
            <td>
                <button onclick="deletarItem(${index})"
                style="background:red;color:white;border:none;border-radius:4px;cursor:pointer;">
                X
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        total += valorTotalItem;
    });

    document.getElementById("valor_total_itens").value = total.toFixed(2);
}

function deletarItem(index){
    itensCarrinho.splice(index, 1);
    atualizarTabelaItens();
}

// AUXILIARES
function atualizarValorUnitario(){
    let select = document.getElementById("equipamento");
    let valor = select.selectedOptions[0].dataset.valor;
    document.getElementById("valor_unitario").value = valor;
}

function limparQuantidade(){
    document.getElementById("quantidade").value = "";
}

function limparModalItens(){
    document.getElementById("equipamento").selectedIndex = 0;
    document.getElementById("quantidade").value = "";
    document.getElementById("valor_unitario").value = "";
    document.getElementById("valor_total_itens").value = "0.00";
    document.getElementById("tbodyItens").innerHTML = "";
    itensCarrinho = [];
}

function limparModalLocacao(){
    document.querySelector("select[name='id_cliente']").selectedIndex = 0;
    document.getElementById("data_locacao").value = "";
    document.getElementById("status").value = "";
    document.querySelector("input[name='data_devolucao']").value = "";
}

// SELEÇÃO DE LINHA
function selecionarLinha(linha, id){
    posicaoScroll = window.scrollY; // GUARDA POSIÇÃO
    document.querySelectorAll("table tr").forEach(l => l.classList.remove("selecionado"));
    linha.classList.add("selecionado");
    idSelecionado = id;
    nomeClienteSelecionado = linha.cells[1].innerText;
}

// AÇÕES
function visualizarItens(){
    let linha = document.querySelector("tr.selecionado");
    if(!linha){
        alert("SELECIONE UMA LOCAÇÃO!");
        return;
    }
    let id = linha.cells[0].innerText.trim();

    window.location.href =
    "locacao.php?ver_itens=" + id;
}

function excluirLocacao(){
    if(idSelecionado == null){
        alert("SELECIONE A LOCAÇÃO A SER EXCLUÍDA!");
        return;
    }

    // 🔎 pega o status da linha selecionada (coluna 5)
    let status = document.querySelector("tr.selecionado").cells[5].innerText;

    // 🔴 valida se está FECHADO
    if(status == "FECHADO"){
        alert("ESTA LOCAÇÃO JÁ FOI FECHADA!");
        return;
    }

    // 🔴 se estiver ABERTO, confirma exclusão
    if(confirm("DESEJA EXCLUIR A LOCAÇÃO DO CLIENTE: " + nomeClienteSelecionado + "?")){
        window.location.href = "excluir_locacao.php?id=" + idSelecionado;
    }
}

// ONLOAD (UMA VEZ SÓ)
window.onload = function(){

    <?php if(isset($_GET['ver_itens'])){ ?>
        document.getElementById("modalVisualizarItens").style.display = "block";

        let id = <?php echo $_GET['ver_itens']; ?>;
        let linhas = document.querySelectorAll("table tr");

        linhas.forEach(l => {
            if(l.innerHTML.includes("<td>"+id+"</td>")){
                l.classList.add("selecionado");
                idSelecionado = id;
                nomeClienteSelecionado = l.cells[1].innerText;
            }
        });
    <?php } 
    ?>

    <?php if(isset($_GET['devolver'])){ ?>
    let id = <?php echo $_GET['devolver']; ?>;

    let linhas = document.querySelectorAll("table tr");

    linhas.forEach(l => {
        if(l.cells && l.cells[0] && l.cells[0].innerText == id){
            l.classList.add("selecionado");
            idSelecionado = id;
            nomeClienteSelecionado = l.cells[1].innerText;
        }
    });
    <?php } 
    ?>

// LINHA SELECIONADA
    let url = new URL(window.location.href);
    let idUrl = url.searchParams.get("selecionar");

    let primeiraLinha = document.querySelector("table tr:nth-child(2)");

        if(primeiraLinha && idSelecionado == null && !idUrl){
        primeiraLinha.classList.add("selecionado");
        idSelecionado = primeiraLinha.cells[0].innerText;
        nomeClienteSelecionado = primeiraLinha.cells[1].innerText;
    }
    }

// SALVAR LOCAÇÃO
function salvarLocacao(){
    if(itensCarrinho.length === 0){
        alert("ADICIONE ITENS DE LOCAÇÃO!");
        return;
    }
    let form = document.getElementById("formLocacao");

// remove inputs antigos
    document.querySelectorAll(".itens_hidden").forEach(el => el.remove());

    itensCarrinho.forEach((item, index) => {

        let inputId = document.createElement("input");
        inputId.type = "hidden";
        inputId.name = `itens[${index}][id_equipamento]`;
        inputId.value = item.id_equipamento;
        inputId.classList.add("itens_hidden");
        form.appendChild(inputId);

        let inputQtd = document.createElement("input");
        inputQtd.type = "hidden";
        inputQtd.name = `itens[${index}][quantidade]`;
        inputQtd.value = item.quantidade;
        inputQtd.classList.add("itens_hidden");
        form.appendChild(inputQtd);

        let inputValor = document.createElement("input");
        inputValor.type = "hidden";
        inputValor.name = `itens[${index}][valor_unitario]`;
        inputValor.value = item.valor_unitario.toFixed(2);
        inputValor.classList.add("itens_hidden");
        form.appendChild(inputValor);
    });

    form.submit();
}

function calcularDias(){
    let dataLoc = document.getElementById("data_locacao").value;
    let dataDev = document.querySelector("input[name='data_devolucao']").value;
    if(!dataLoc || !dataDev) return;

// converte dd/mm/yyyy → yyyy-mm-dd
    let partes = dataLoc.split("/");
    let dataLocFormatada = partes[2]+"-"+partes[1]+"-"+partes[0];

// pega hora atual da locação (igual PHP)
    let agora = new Date(dataLocFormatada);
    let hora = agora.getHours();

// cria datas completas
    let dataHoraLoc = new Date(dataLocFormatada + "T" + String(hora).padStart(2,'0') + ":00:00");
    let dataHoraDev = new Date(dataDev + "T" + String(hora).padStart(2,'0') + ":00:00");

// diferença em horas
    let diffHoras = (dataHoraDev - dataHoraLoc) / (1000 * 60 * 60);

    if(diffHoras >= 0){
        let dias = Math.ceil(diffHoras / 24);
        if(dias <= 0) dias = 1;

        document.getElementById("qtd_dias").value = dias;

        qtdDiasLocacao = dias;
    }else{
        document.getElementById("qtd_dias").value = "";
    }
}

// ABRIR MODAL DEVOLUÇÃO
function abrirModalDevolucao(){

    let linha = document.querySelector("tr.selecionado");

    if(!linha){
        alert("SELECIONE UMA LOCAÇÃO!");
        return;
    }

    let status = linha.cells[5].innerText;

// data/hora atual
    let agora = new Date();
    let data = agora.toISOString().slice(0,16);

    document.getElementById("data_dev_real").value = data;
    document.getElementById("status_devolucao").value = status;

    abrirModal('modalDevolucao');

    controlarCampos(); // sua função já existente

    document.querySelector("#modalDevolucao .salvar").disabled = false;
    }


// FINALIZAR DEVOLUÇÃO
function abrirDevolucao(){
    let linha = document.querySelector("tr.selecionado");

    if(!linha){
        alert("SELECIONE UMA LOCAÇÃO!");
        return;
    }

    let id = linha.cells[0].innerText;
    let status = linha.cells[5].innerText;

    if(status === "FECHADO"){
    alert("A DEVOLUÇÃO ESTÁ FINALIZADA!");
    return;
}

    // 🔷 PREENCHE STATUS
    document.getElementById("status_dev").value = status;

    // 🔷 REDIRECIONA (carrega itens)
    window.location.href = "locacao.php?devolver=" + id;
}

function controlarEntrega(){
    let tipo = document.getElementById("entrega").value;
    if(tipo === "EFETIVA"){
        document.getElementById("multa").value = 0;
        document.getElementById("antecipacao").value = 0;
    }
    if(tipo === "MULTA"){
        document.getElementById("antecipacao").value = 0;
    }
    if(tipo === "ANTECIPADA"){
        document.getElementById("multa").value = 0;
    }
}

function evitarNegativo(id){
    let campo = document.getElementById(id);
    if(!campo) return;

    if(parseFloat(campo.value) < 0){
        campo.value = 0;
    }
}

function atualizarCalculo(){
    let tipo = document.getElementById("entrega").value;

    let valorTotal = document.getElementById("valor_total").value;
    valorTotal = valorTotal.replace(/\./g, "").replace(",", ".");
    valorTotal = parseFloat(valorTotal) || 0;

    let desconto = document.getElementById("ajuste_desconto").value;
    desconto = desconto.replace(",", ".");
    desconto = parseFloat(desconto) || 0;

    let encargos = document.getElementById("ajuste_encargo").value;
    encargos = encargos.replace(",", ".");
    encargos = parseFloat(encargos) || 0;

    let multaInput = document.getElementById("multa");
    let antecipacaoInput = document.getElementById("antecipacao");

    let multa = 0;
    let antecipacao = 0;

    let dataPrev = "<?php echo isset($data_prevista) ? $data_prevista : ''; ?>";
    let dataReal = document.getElementById("data_dev_real").value;

// diferença correta
function diffHorasCorreto(dataReal, dataPrev){
        if(!dataReal || !dataPrev) return 0;

        let r = dataReal.split(/[-T:]/);
        let p = dataPrev.replace(" ", "T").split(/[-T:]/);

        let dtReal = new Date(r[0], r[1]-1, r[2], r[3], r[4]);
        let dtPrev = new Date(p[0], p[1]-1, p[2], p[3], p[4]);

        return (dtReal - dtPrev) / 3600000;
    }

    if(dataReal){

        let horas = diffHorasCorreto(dataReal, dataPrev);
        let dias = parseFloat((horas / 24).toFixed(2));

        let qtdDias = Number(document.getElementById("qtd_dias_hidden").value);

        console.log("VALOR TOTAL:", valorTotal);
        console.log("QTD_DIAS (PHP):", qtdDias);

        let valorDia = valorTotal / qtdDias;

        console.log("VALOR DIA:", valorDia);

        if(tipo === "MULTA" && dias > 0){
            multa = parseFloat((dias * valorDia).toFixed(2));
        }

        if(tipo === "ANTECIPADA" && dias < 0){
            antecipacao = parseFloat(Math.abs(dias * valorDia).toFixed(2));
        }

        if(tipo === "EFETIVA"){
            multa = 0;
            antecipacao = 0;
        }

//  MOSTRAR NA TELA TAMBÉM
    document.getElementById("debug_dias").value = (horas/24).toFixed(3); 
    }

    multaInput.value = multa.toFixed(2);
    antecipacaoInput.value = antecipacao.toFixed(2);

    let valorFinal = valorTotal + multa - antecipacao - desconto + encargos;

    document.getElementById("valor_final").value =
        valorFinal.toFixed(2).replace(".", ",");

    controlarCampos();
}
    document.addEventListener("DOMContentLoaded", function(){
    // ENTREGA
    document.getElementById("entrega")?.addEventListener("change", atualizarCalculo);
    // DATA
    document.getElementById("data_dev_real")?.addEventListener("change", atualizarCalculo);
    // DESCONTO
    document.getElementById("ajuste_desconto")?.addEventListener("input", atualizarCalculo);
    // ENCARGO
    document.getElementById("ajuste_encargo")?.addEventListener("input", atualizarCalculo);
    // MULTA (editável)
    document.getElementById("multa")?.addEventListener("input", atualizarCalculo);
    // ANTECIPAÇÃO (editável)
    document.getElementById("antecipacao")?.addEventListener("input", atualizarCalculo);

});

function controlarCampos(){
    let tipo = document.getElementById("entrega")?.value;
    let multa = document.getElementById("multa");
    let antecipacao = document.getElementById("antecipacao");

    if(!multa || !antecipacao) return;

    if(tipo === "EFETIVA"){
        multa.disabled = true;
        antecipacao.disabled = true;

        multa.value = "0.00";
        antecipacao.value = "0.00";
    }

    if(tipo === "MULTA"){
        multa.disabled = false;
        antecipacao.disabled = true;

        antecipacao.value = "0.00";
    }

    if(tipo === "ANTECIPADA"){
        multa.disabled = true;
        antecipacao.disabled = false;

        multa.value = "0.00";
    }
}

document.getElementById("entrega")?.addEventListener("change", function(){
    controlarCampos();
    atualizarCalculo();
});

document.addEventListener("DOMContentLoaded", function(){

// formatar campos desconto e encargos
function formatarCampo(campo){
        campo.addEventListener("blur", function(){
            let valor = campo.value.replace(",", ".");
            valor = parseFloat(valor);

            if(isNaN(valor) || valor < 0){
                valor = 0;
            }
            campo.value = valor.toFixed(2).replace(".", ",");
        });
    }

    let desconto = document.getElementById("ajuste_desconto");
    let encargo = document.getElementById("ajuste_encargo");

    if(desconto) formatarCampo(desconto);
    if(encargo) formatarCampo(encargo);

    });

function alterarCampoPesquisa(){
    let tipo = document.getElementById("tipo_pesquisa").value;
    let campoTexto = document.getElementById("campo_texto");
    let campoData = document.getElementById("campo_data");

    if(tipo === "cliente"){
        campoTexto.style.display = "block";
        campoData.style.display = "none";
    } else {
        campoTexto.style.display = "none";
        campoData.style.display = "block";
    }
    }

// SALVAR DADOS DEVOLUÇÃO
function finalizarDevolucao(){
    let linha = document.querySelector("tr.selecionado");

    if(!linha){
        alert("SELECIONE UMA LOCAÇÃO!");
        return;
    }

    let id_locacao = linha.cells[0].innerText;
    let data_dev_real = document.getElementById("data_dev_real").value;
    let entrega = document.getElementById("entrega").value;
    let multa = document.getElementById("multa").value;
    let antecipacao = document.getElementById("antecipacao").value;
    let desconto = document.getElementById("ajuste_desconto").value;
    let encargo = document.getElementById("ajuste_encargo").value;

    fetch("finalizar_devolucao.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            "id_locacao=" + id_locacao +
            "&data_dev_real=" + data_dev_real +
            "&entrega=" + entrega +
            "&multa=" + multa +
            "&antecipacao=" + antecipacao +
            "&ajuste_desconto=" + desconto +
            "&ajuste_encargo=" + encargo
    })
    .then(res => res.text())
    .then(res => {

        if(res === "OK"){
            alert("DEVOLUÇÃO FINALIZADA!");
                window.location.href = "locacao.php?selecionar=" + id_locacao;
        } else {
            alert("Erro ao salvar devolução");
        }

    })
    .catch(err => {
        console.error(err);
        alert("Erro na comunicação com o servidor");
    });
}

function visualizarDevolucao(){
    let linha = document.querySelector("tr.selecionado");
    if(!linha){
        alert("SELECIONE UMA LOCAÇÃO!");
        return;
    }
    let status = linha.cells[5].innerText.trim(); // ajuste conforme sua coluna

    // INFORMA SE A LOCAÇÃO NÃO ESTIVER FECHADA
    if(status !== "FECHADO"){
        alert("ESTA LOCAÇÃO NÃO ESTÁ FECHADA!");
        return;
    }
    let id = linha.cells[0].innerText;

    // 🔷 REDIRECIONA (igual devolução)
    window.location.href = "locacao.php?ver_devolucao=" + id + "&selecionar=" + id;
}

// MUDAR STATUS = BOTÃO CANCELAR DEVOLUÇÃO
function cancelarDevolucao(){
    let linha = document.querySelector("tr.selecionado");
    if(!linha){
        alert("SELECIONE UMA LOCAÇÃO!");
        return;
    }

    let status = linha.cells[5].innerText.trim();
    // só pode se estiver FECHADO
    if(status !== "FECHADO"){
        alert("A LOCAÇÃO NÃO ESTÁ FECHADA!");
        return;
    }

    let id = linha.cells[0].innerText;
    if(confirm("DESEJA REALMENTE REABRIR ESTA DEVOLUÇÃO?")){
        window.location.href = "reabrir_devolucao.php?id=" + id + "&voltar=1";
    }
}

    document.addEventListener("DOMContentLoaded", function(){

    let btn = document.getElementById("btnCancelarDev");
    let nivel = btn.getAttribute("data-nivel");

    if(nivel !== "ADM"){
        btn.style.opacity = "0.5";
        btn.style.cursor = "not-allowed";

        btn.onclick = function(){
            alert("SEM PERMISSÃO!");
        };
    }

});

// voltar para a linha selecionada
window.addEventListener("load", function(){

    setTimeout(function(){ // garante execução por último

        let url = new URL(window.location.href);
        let id = url.searchParams.get("selecionar");

        if(id){
            let linhas = document.querySelectorAll("table tr");
            linhas.forEach(function(linha, index){
                if(index === 0) return; // ignora header

            let idLinha = linha.cells[0]?.innerText.trim();
                if(idLinha === id){

                    // limpa seleção anterior
                    document.querySelectorAll("tr.selecionado")
                    .forEach(l => l.classList.remove("selecionado"));

                    linha.classList.add("selecionado");

                    linha.scrollIntoView({
                        behavior: "auto",
                        block: "center"
                    });
                }

            });
        }

    }, 200); 

});

function alterarCampoPesquisa(){
    let tipo = document.getElementById("tipo_pesquisa").value;
    let texto = document.getElementById("campo_texto");
    let data = document.getElementById("campo_data");

    if(tipo == "cliente"){
        texto.style.display = "block";
        data.style.display = "none";

        texto.value = "";
    }
    else{
        texto.style.display = "none";
        data.style.display = "block";

        data.value = "";
    }
}

function pesquisarLocacao(){
    let tipo = document.getElementById("tipo_pesquisa").value;
    let valor = "";
    if(tipo == "cliente"){
        valor = document.getElementById("campo_texto").value;
    }
    else{
        valor = document.getElementById("campo_data").value;
    }
    if(valor == ""){
        alert("PREENCHA O CAMPO!");
        return;
    }

    window.location.href =
        "locacao.php?tipo_pesquisa=" + tipo +
        "&valor_pesquisa=" + valor;
}

function focarCampoPesquisa(){
    let tipo = document.getElementById("tipo_pesquisa").value;
    if(tipo == "cliente"){
        document.getElementById("campo_texto").focus();
    }

    // DATAS
    else{
        document.getElementById("campo_data").focus();
    }
}

</script>
</head>

<body>
<div class="container">
<h2>MÓDULO LOCAÇÃO</h2>

<div class="botoes">
    <button onclick="abrirModal('modalIncluirLocacao')">INCLUIR LOCAÇÃO</button>
    <button onclick="visualizarItens('modalVisualizarItens')">VISUALIZAR / EXCLUIR </button>
    <button onclick="abrirDevolucao()">DEVOLUÇÃO</button>
    <button onclick="visualizarDevolucao()">DADOS DEVOLUÇÃO</button>
    <button onclick="abrirModal('modalPesquisa')">PESQUISA</button>
    <button onclick="window.location.href='principal.php'">VOLTAR</button>
    <button id="btnCancelarDev" class="cancelar-dev" onclick="cancelarDevolucao()" data-nivel="<?php echo $_SESSION['nivel']; ?>"> CANCELAR DEVOLUÇÃO </button>
</div>

<div class="table-container">
  
    <table id="tabelaLocacoes">
        <tr>
            <th>ID LOCAÇÃO</th>
            <th>CLIENTE</th>
            <th>DATA LOCAÇÃO</th>
            <th>DATA DEVOLUÇÃO</th>
            <th>QUANT. DIAS</th>
            <th>STATUS</th>
            <th>DATA REAL DEV.</th>
        </tr>

        <?php
        $sql = "SELECT l.id_locacao, c.nome as cliente, l.data_locacao, l.data_devolucao, l.qtd_dias, l.status, l.data_dev_real
                FROM locacao l
                JOIN clientes c ON c.id_cliente = l.id_cliente

                WHERE 1=1
                $filtro

                ORDER BY l.id_locacao DESC";
              
        $result = mysqli_query($conexao, $sql);
        while($row = mysqli_fetch_assoc($result)){
            $statusClass = ($row['status'] == 'FECHADO') ? 'status-fechado' : '';
            echo 
            "<tr class='".$statusClass."' onclick='selecionarLinha(this, ".$row['id_locacao'].")'>         
            <td>".$row['id_locacao']."</td>
            <td>".$row['cliente']."</td>
            <td>".date('d/m/Y', strtotime($row['data_locacao'])) . " - Hora: " . date('H:i', strtotime($row['data_locacao']))."</td>
            <td>".date('d/m/Y', strtotime($row['data_devolucao'])) . " - Hora: " . date('H:i', strtotime($row['data_devolucao']))."</td>
            <td>".$row['qtd_dias']."</td>
            <td>".$row['status']."</td>
            <td>".($row['data_dev_real'] ? date('d/m/Y', strtotime($row['data_dev_real'])) . " - Hora: " . date('H:i', strtotime($row['data_dev_real'])): "-" )."</td>
        </tr>";
}
        ?>

</table>
</div>
</div>

<!-- FORM PRINCIPAL -->
<form method="post" action="salvar_locacao.php" id="formLocacao"></form>

<!-- MODAL INCLUIR LOCAÇÃO -->
<div class="modal" id="modalIncluirLocacao">
    <div class="modal-content">
        <h3>INCLUIR LOCAÇÃO</h3>

        <!-- Linha 1: Cliente + Data Locação -->
        <div class="form-linha">
            <div class="form-grupo">
                <label>Cliente</label>
                <select name="id_cliente" class="input-padrao" form="formLocacao" required>
                    <option value="">Selecione</option>
                    <?php mysqli_data_seek($clientes,0); while($row = mysqli_fetch_assoc($clientes)){
                        echo "<option value='".$row['id_cliente']."'>".$row['nome']."</option>";
                    } ?>
                </select>
            </div>

            <div class="form-grupo">
                <label>Data Locação</label>
                <input type="text" id="data_locacao" readonly form="formLocacao">
            </div>
        </div>

        <!-- Linha 2: Status + Data Devolução -->
        <div class="form-linha">
            <div class="form-grupo">
                <label>Status</label>
                <input type="text" id="status" readonly form="formLocacao">
            </div>

            <div class="form-grupo">
                <label>Data Devolução</label>
                <input type="date" name="data_devolucao" class="input-padrao" form="formLocacao" required   onchange="calcularDias()">
            </div>
        </div>

            <div class="form-grupo qtd-dias">
                <label>Qtd. Dias</label>
                <input type="text" id="qtd_dias" name="qtd_dias" readonly form="formLocacao">
            </div>

        <div class="botoes-modal">
            <button type="button" class="salvar" onclick="abrirModalItens()">ADICIONAR ITENS</button>
            <button type="button" class="cancelar" onclick="fecharModal('modalIncluirLocacao');         limparModalLocacao();">CANCELAR</button>
        </div>
    </div>
</div>

<!-- MODAL ITENS -->
<div class="modal" id="modalItens">
    <div class="modal-content">
        <h3>ITENS DA LOCAÇÃO</h3>

        <!-- Linha com 3 campos: Equipamento, Quantidade, Valor Unitário -->
        <div class="form-linha">
            <div class="form-grupo">
                <label>Equipamento</label>
                <select id="equipamento" onchange="atualizarValorUnitario(); limparQuantidade()" class="input-padrao">
                    <option value="">Selecione</option>
                    <?php
                    mysqli_data_seek($equipamentos,0);
                    while($row = mysqli_fetch_assoc($equipamentos)){
                        echo "<option value='".$row['id_equipamento']."' 
                        data-valor='".$row['valor_locacao_dia']."' 
                        data-estoque='".$row['quantidade_total']."'>
                        ".$row['descricao']." (Disponível: ".$row['quantidade_total'].")
                        </option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-grupo">
                <label>Quantidade</label>
                <input type="number" id="quantidade" class="input-padrao" min="1">
            </div>
            <div class="form-grupo">
                <label>Valor Unitário</label>
                <input type="text" id="valor_unitario" readonly>
            </div>
        </div>

        <!-- NOVO CAMPO VIRTUAL: Valor Total -->
        <div class="form-linha">
            <div class="form-grupo valor-total">
                <label>VALOR FINAL DA LOCAÇÃO</label>
                <input type="text" id="valor_total_itens" readonly>
            </div>
        </div>

        <div class="botoes-modal">
            <button type="button" class="salvar" onclick="adicionarItem()">ADICIONAR ITEM</button>
        </div>

        <div class="table-itens-container">
        <table class="table-itens">
            <thead>
                <tr>
                    <th>Equipamento</th>
                    <th>Quant.</th>
                    <th>Dias</th>
                    <th>Vl. Unitário</th>
                    <th>Vl. Total (qtd * dias * Vl. unit.)</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody id="tbodyItens"></tbody>
        </table>
        </div>

        <div class="botoes-modal">
            <button type="button" class="salvar" onclick="salvarLocacao()">SALVAR LOCAÇÃO</button>
            <button type="button" class="cancelar" onclick="fecharModal('modalItens'); limparModalItens();">CANCELAR</button>
        </div>
    </div>
</div>

<!-- MODAL VISUALIZAR ITENS / EXCLUSÃO DE LOCAÇÃO-->
<div class="modal" id="modalVisualizarItens">
    <div class="modal-content">
        <h3>ITENS DA LOCAÇÃO</h3>

        <?php
        $total_geral = 0;
        $qtd_dias = 1;

        if(isset($_GET['ver_itens'])){
            $id = $_GET['ver_itens'];

            // BUSCA DIAS DA LOCAÇÃO (FONTE OFICIAL)
            $resLoc = mysqli_query($conexao, "SELECT qtd_dias FROM locacao WHERE id_locacao = $id");
            $dadosLoc = mysqli_fetch_assoc($resLoc);

            if($dadosLoc){
                $qtd_dias = $dadosLoc['qtd_dias'];
            }
        }
        ?>

        <div class="table-itens-container">
        <table class="table-itens">
            <thead>
                <tr>
                    <th>Equipamento</th>
                    <th>Quantidade</th>
                    <th>Dias</th>
                    <th>Valor Unitário</th>
                    <th>Valor Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(isset($itens_array)){
                    foreach($itens_array as $item){

                        $total_item = $item['quantidade'] * $item['valor_unitario'] * $qtd_dias;
                        $total_geral += $total_item;

                        echo "<tr>
                            <td>".$item['descricao']."</td>
                            <td>".$item['quantidade']."</td>
                            <td>".$qtd_dias."</td>
                            <td>".number_format($item['valor_unitario'],2,',','.')."</td>
                            <td>".number_format($total_item,2,',','.')."</td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
        </div>

        <!-- TOTAL -->
        <div class="valor-total-container">
            <div class="valor-total">
                <label>VALOR TOTAL:</label>
                <input type="text" value="<?php echo number_format($total_geral,2,',','.'); ?>" readonly>
            </div>
        </div>

        <div class="botoes-modal">
            <button class="excluir" onclick="excluirLocacao()">EXCLUIR</button>
            <button class="cancelar" onclick="fecharModal('modalVisualizarItens')">CANCELAR</button>
        </div>
    </div>
</div>

<!-- MODAL DEVOLUÇÃO -->
<div class="modal" id="modalDevolucao">
<div class="modal-content" style="width: 1100px; max-width:95%;">

<h3>DEVOLUÇÃO</h3>

<?php
$total_locacao = 0;
$status_loc = "";

if(isset($_GET['devolver'])){

    $id = $_GET['devolver'];

// BUSCA LOCAÇÃO
    $resLoc = mysqli_query($conexao,"
        SELECT status, qtd_dias, data_locacao, data_devolucao
        FROM locacao 
        WHERE id_locacao = $id
    ");

    $dadosLoc = mysqli_fetch_assoc($resLoc);
    $status_loc = $dadosLoc['status'];
    $qtd_dias = $dadosLoc['qtd_dias'];
    $data_locacao = $dadosLoc['data_locacao'];    
    $data_prevista = $dadosLoc['data_devolucao'];   
    
// BUSCA ITENS
    $itens_devolucao = mysqli_query($conexao,"
        SELECT i.*, e.descricao 
        FROM itens_locacao i
        JOIN equipamentos e ON e.id_equipamento = i.id_equipamento
        WHERE i.id_locacao = $id
    ");

// CALCULA TOTAL
    while($item = mysqli_fetch_assoc($itens_devolucao)){
        $valor_corrigido = $item['quantidade'] * $item['valor_unitario'] * $qtd_dias;
        $total_locacao += $valor_corrigido;

    }

// REPOSICIONA O PONTEIRO
    mysqli_data_seek($itens_devolucao, 0);
}

?>

<input type="hidden" id="qtd_dias_hidden" value="<?php echo $qtd_dias; ?>">
<!-- LINHA PRINCIPAL -->
<div class="form-linha">

    <div class="form-grupo">
        <label>Data Prevista</label>
        <input type="text" id="data_prevista" value="<?php echo date('d/m/Y H:i', strtotime($data_prevista)); ?>" readonly>
    </div>

    <div class="form-grupo">
        <label>Data Devolução</label>
        <input type="datetime-local" id="data_dev_real" class="input-padrao">
    </div>

    <div class="form-grupo">
        <label>Dias atraso/antecipação</label>
        <input type="text" id="debug_dias" readonly>
    </div>

    <div class="form-grupo status-central">
        <label>Status</label>
        <input type="text" id="status_dev" value="<?php echo $status_loc; ?>" readonly>
    </div>

    <div class="form-grupo valor-direita">
        <label>Valor Total</label>
        <input type="text" id="valor_total"
        value="<?php echo number_format($total_locacao,2,',','.'); ?>"
        readonly>
    </div>

</div>

<!-- TABELA -->
<div class="table-itens-container">
<table class="table-itens">
<thead>
<tr>
    <th>Equipamento</th>
    <th>Quantidade</th>
    <th>Dias</th>
    <th>Valor Unitário</th>
    <th>Valor Total</th>
</tr>
</thead>

<tbody>

<?php
if(isset($itens_devolucao)){
    while($item = mysqli_fetch_assoc($itens_devolucao)){

        echo "<tr>
            <td>".$item['descricao']."</td>
            <td>".$item['quantidade']."</td>
            <td>".$qtd_dias."</td>
            <td>".number_format($item['valor_unitario'],2,',','.')."</td>
            <td>".number_format(($item['quantidade'] * $item['valor_unitario'] * $qtd_dias),2,',','.')."</td>
        </tr>";
    }
}
?>

</tbody>
</table>
</div>

<!-- CAMPOS ABAIXO -->
<div class="form-linha">

    <div class="form-grupo">
        <label>Entrega</label>
        <select id="entrega" class="input-padrao" onchange="atualizarCalculo()">
            <option value="EFETIVA">Efetiva</option>
            <option value="MULTA">Multa</option>
            <option value="ANTECIPADA">Antecipada</option>
        </select>
    </div>

    <div class="form-grupo">
        <label>Multa</label>
        <input type="number" id="multa" class="input-padrao" value="0.00" readonly>
    </div>

    <div class="form-grupo">
        <label>Antecipação</label>
        <input type="number" id="antecipacao" class="input-padrao" value="0.00" readonly>
    </div>

</div>

<div class="form-linha">

    <div class="form-grupo">
        <label>Descontos</label>
        <input type="text" id="ajuste_desconto" class="input-padrao" value="0,00">
    </div>

    <div class="form-grupo">
        <label>Encargos</label>
        <input type="text" id="ajuste_encargo" class="input-padrao" value="0,00">
    </div>

    <div class="form-grupo">
        <label>Valor Final</label>
        <input type="text" id="valor_final" readonly>
    </div>

</div>

<!-- BOTÕES -->
<div class="botoes-modal">
    <button class="salvar" onclick="finalizarDevolucao()">FINALIZAR</button>
    <button class="cancelar" onclick="fecharModal('modalDevolucao')">CANCELAR</button>
</div>

</div>
</div>

<!-- MODAL PESQUISA -->
<div class="modal" id="modalPesquisa">
    <div class="modal-content">

        <h3>PESQUISAR LOCAÇÃO</h3>

        <div class="form-linha">

            <!-- TIPO DE PESQUISA -->
            <div class="form-grupo">
                <label>Tipo de Pesquisa</label>
                <select id="tipo_pesquisa" class="input-padrao" onchange="alterarCampoPesquisa(); focarCampoPesquisa();">
                    <option value="cliente">Nome do Cliente</option>
                    <option value="data_locacao">Data de Locação</option>
                    <option value="data_devolucao">Data de Devolução</option>
                </select>
            </div>

            <!-- CAMPO DINÂMICO -->
            <div class="form-grupo">
                <label>Pesquisar</label>

                <!-- INPUT TEXTO -->
                <input type="text" id="campo_texto" class="input-padrao">

                <!-- INPUT DATA (inicia oculto) -->
                <input type="date" id="campo_data" class="input-padrao" style="display:none;">
            </div>

        </div>

        <div class="botoes-modal">
            <button class="salvar" onclick="pesquisarLocacao()">PESQUISAR</button>
            <button class="cancelar" onclick="fecharModal('modalPesquisa')">CANCELAR</button>
        </div>

    </div>
</div>


<!-- MODAL VISUALIZAR DEVOLUÇÃO -->
<div class="modal" id="modalVisualizarDevolucao">
<div class="modal-content" style="width: 1100px; max-width:95%;">

<h3>VISUALIZAR DEVOLUÇÃO</h3>

<?php
$total_locacao_v = 0;
$status_loc_v = "";

if(isset($_GET['ver_devolucao'])){

    $id = $_GET['ver_devolucao'];

    // 🔷 BUSCA LOCAÇÃO
    $resLoc = mysqli_query($conexao,"
    SELECT data_locacao, data_devolucao, data_dev_real, status, qtd_dias
    FROM locacao
    WHERE id_locacao = $id
    ");

    $dadosLoc = mysqli_fetch_assoc($resLoc);

    $data_locacao_v = $dadosLoc['data_locacao'];
    $data_prevista_v = $dadosLoc['data_devolucao']; // prevista
    $data_dev_real_v = $dadosLoc['data_dev_real']; // real
    $status_loc_v = $dadosLoc['status'];
    $qtd_dias_v = $dadosLoc['qtd_dias'];

 //   $dias_locacao = (strtotime($data_prevista_v) - strtotime($data_locacao_v)) / (60 * 60 * 24);

//  BUSCA ITENS
    $itens_dev_v = mysqli_query($conexao,"
    SELECT i.*, e.descricao 
    FROM itens_locacao i
    JOIN equipamentos e 
    ON e.id_equipamento = i.id_equipamento
    WHERE i.id_locacao = $id
");

// 🔥 TOTAL
while($item = mysqli_fetch_assoc($itens_dev_v)){

    $total_locacao_v += $item['valor_total'];
}

mysqli_data_seek($itens_dev_v, 0);

// PEGA VALORES DO PRIMEIRO ITEM (igual sistema já usa)
    $item_valor = mysqli_fetch_assoc($itens_dev_v);

    $multa_v = $item_valor['multa'] ?? 0;
    $antecipacao_v = $item_valor['antecipacao'] ?? 0;
    $desconto_v = $item_valor['ajuste_desconto'] ?? 0;
    $encargo_v = $item_valor['ajuste_encargos'] ?? 0;
    $entrega_v = $item_valor['entrega'] ?? 'EFETIVA';

    mysqli_data_seek($itens_dev_v, 0);
}
?>

<!-- LINHA PRINCIPAL -->
<div class="form-linha">

    <div class="form-grupo">
        <label>Data Prevista</label>
        <input type="text" id="data_prevista" value="<?php echo date('d/m/Y H:i', strtotime($data_prevista_v)); ?>" readonly>
    </div>
  
    <div class="form-grupo">
        <label>Data Devolução</label>
        <input type="datetime-local" value="<?php echo $data_dev_real_v ? date('Y-m-d\TH:i', strtotime($data_dev_real_v)) : ''; ?>" readonly>
    </div>

    <div class="form-grupo status-central">
        <label>Status</label>
        <input type="text" value="<?php echo $status_loc_v; ?>" readonly>
    </div>

    <div class="form-grupo valor-direita">
        <label>Valor Total</label>
        <input type="text"
        value="<?php echo number_format($total_locacao_v,2,',','.'); ?>"
        readonly>
    </div>

</div>

<!-- TABELA -->
<div class="table-itens-container">
<table class="table-itens">
<thead>
<tr>
    <th>Equipamento</th>
    <th>Quantidade</th>
    <th>Dias</th>
    <th>Valor Unitário</th>
    <th>Valor Total</th>
</tr>
</thead>

<tbody>
    
<?php
if(isset($itens_dev_v)){
    while($item = mysqli_fetch_assoc($itens_dev_v)){
        echo "<tr>
            <td>".$item['descricao']."</td>
            <td>".$item['quantidade']."</td>
            <td>".$qtd_dias_v."</td>
            <td>".number_format($item['valor_unitario'],2,',','.')."</td>
            <td>".number_format($item['valor_total'],2,',','.')."</td>
        </tr>";
    }
}
?>

</tbody>
</table>
</div>

<!-- CAMPOS -->
<div class="form-linha">

    <div class="form-grupo">
        <label>Entrega</label>
        <input type="text" value="<?php echo $entrega_v; ?>" readonly>
    </div>

    <div class="form-grupo">
        <label>Multa</label>
        <input type="text" id="multa" value="<?php echo number_format($multa_v,2,',','.'); ?>" readonly>
    </div>

    <div class="form-grupo">
        <label>Antecipação</label>
        <input type="text" id="antecipacao" value="<?php echo number_format($antecipacao_v,2,',','.'); ?>" readonly>
    </div>

</div>

<div class="form-linha">

    <div class="form-grupo">
        <label>Descontos</label>
        <input type="text" id="ajuste_desconto" value="<?php echo number_format($desconto_v,2,',','.'); ?>" readonly>
    </div>

    <div class="form-grupo">
        <label>Encargos</label>
        <input type="text" id="ajuste_encargo" value="<?php echo number_format($encargo_v,2,',','.'); ?>" readonly>
    </div>

    <?php 
        $valor_final_v = ($total_locacao_v + $multa_v - $antecipacao_v - $desconto_v + $encargo_v);
    ?>

    <div class="form-grupo">
        <label>Valor Final</label>
        <input type="text" id="valor_final" value="<?php echo number_format($valor_final_v,2,',','.'); ?>" readonly>
    </div>

</div>

<div class="botoes-modal">
    <button class="cancelar" onclick="fecharModalFoco('modalVisualizarDevolucao')"> FECHAR </button>
</div>
</div>

</body>
</html>



