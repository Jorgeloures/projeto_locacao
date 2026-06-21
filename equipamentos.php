<?php 
include("verifica_login.php");
include("conexao.php"); 

if(isset($_GET['erro'])){
    echo "
    <script>
        alert('EQUIPAMENTO NÃO PODE SER EXCLUÍDO POIS POSSUI LOCAÇÕES!');
    </script>
    ";
}

?>

<?php

$proximo_id = 1;
$result_id = mysqli_query($conexao, "SELECT MAX(id_equipamento) as max_id FROM equipamentos");
$row_id = mysqli_fetch_assoc($result_id);

if($row_id['max_id'] != null){
    $proximo_id = $row_id['max_id'] + 1;
}

// PESQUISA
$campo = isset($_GET['campo']) ? $_GET['campo'] : '';
$valor = isset($_GET['valor']) ? trim($_GET['valor']) : '';

if($valor != "" && ($campo == "descricao" || $campo == "categoria")){
    $valor = mysqli_real_escape_string($conexao, $valor);
    $sql = "SELECT * FROM equipamentos 
            WHERE $campo LIKE '%$valor%'";
} else {
    $sql = "SELECT * FROM equipamentos";
}

$result = mysqli_query($conexao, $sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Equipamentos</title>

<style>
body { font-family: "Segoe UI"; margin: 0; background: url('fundo1.png');; }

.container { width: 100%; height: calc(100vh - 80px); padding: 15px; box-sizing: border-box; }

h2 { color: white; }

/* BOTÕES */
.botoes {margin-bottom: 15px; display: flex; align-items: center;}
.botoes button { padding: 10px 20px; margin-right: 20px; border: none; cursor: pointer; background: #3498db; color: white; border-radius: 5px; }
button:active { transform: scale(0.95); box-shadow: inset 0px 3px 8px rgba(0,0,0,0.4); }

/* TABELA */
.table-container { height: 100%; border-radius: 10px;max-height:calc(100vh - 150px); overflow-y: auto; background: white; }

table { width: 100%; border-collapse: collapse; }

th { background: whiye; color: black; text-align: center; padding: 10px; border: 1px solid #ccc; font-size: 13px; }

td { padding: 5px 7px; border: 1px solid #ccc; font-size: 13px; }

th:nth-child(1), td:nth-child(1) { width: 120px; text-align: center; }
th:nth-child(2), td:nth-child(2) { width: 150px; text-align: center; }
th:nth-child(3), td:nth-child(3) { width: 460px; }
th:nth-child(4), td:nth-child(4) { width: 360px; }
th:nth-child(5), td:nth-child(5) { width: 120px; text-align: center; }
th:nth-child(6), td:nth-child(6) { width: 120px; text-align: center; }

tr:hover { background: #f2f2f2; }
tr.selecionado { background: #483ba0 !important; color: white; }

/* MODAL */
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); }

.modal-content { background: #f4f6f7; width: 800px; max-width: 90%; margin: 10% auto; padding: 30px; border-radius: 12px; }

/* FORM */
.form-linha { display: flex; gap: 20px; margin-bottom: 15px; }
.form-grupo { flex: 1; }
.form-grupo label { display: block; margin-bottom: 6px; }
.form-grupo input { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; }

/* BOTÕES MODAL */
.botoes-modal { margin-top: 20px; display: flex; justify-content: space-between; }
.botao-voltar{ margin-left: auto; width: 150px !important;}
.salvar { background: #3498db; color: white; border: none; padding: 10px; width: 48%; border-radius: 10px; font-weight: bold; }
.salvar:hover { background: #2c80b4; }
.cancelar { background: #bdc3c7; color: black; border: none; padding: 10px; width: 48%; border-radius: 10px; font-weight: bold; }
.cancelar:hover { background: #95a5a6; }

/* INPUTS READONLY */
#modalIncluirEquipamento input[readonly],
#modalEditar input[readonly] { background: #ecf0f1; cursor: not-allowed; }

/* INPUT PADRÃO */
.input-padrao { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; font-size: 14px; }

select.input-padrao {background: white; }

/* CENTRALIZAÇÃO CAMPOS */
#i_id, #i_data, input[name="quantidade_total"], input[name="valor_locacao_dia"] { text-align: center; }

/* MODAL PESQUISA */
#modalPesquisar .modal-content { width: 800px; margin: 12% auto; }
        
</style>

<script>
let idSelecionado = null;

function selecionarLinha(linha, id){
    let linhas = document.querySelectorAll("tr");
    linhas.forEach(l => l.classList.remove("selecionado"));

    linha.classList.add("selecionado");
    idSelecionado = id;
}

function fecharModal(id){
    document.getElementById(id).style.display = "none";
}

function excluir(){
    if(idSelecionado == null){
        alert("SELECIONE UM EQUIPAMENTO!");
        return;
    }

    let linha = document.querySelector("tr.selecionado");
    let descricao = linha.children[2].innerText;
    let idFormatado = String(idSelecionado).padStart(6, '0');

    if(confirm(
        "DESEJA REALMENTE EXCLUIR O EQUIPAMENTO?\n\n" +
        "ID: " + idFormatado + "\n" +
        "Descrição: " + descricao
    )){
        window.location.href = "equipamentos.php?excluir=" + idSelecionado;
    }
}

function abrirModal(id){
    document.getElementById(id).style.display = "block";
    // se for modal de inclusão de equipamento
    if(id === "modalIncluirEquipamento"){

        // Data atual
        let hoje = new Date();
        let dia = String(hoje.getDate()).padStart(2, '0');
        let mes = String(hoje.getMonth()+1).padStart(2, '0');
        let ano = hoje.getFullYear();
        document.getElementById("i_data").value = dia + "/" + mes + "/" + ano;
    }
}

 
// PESQUISA - FOCO

window.onload = function(){
    let primeira = document.querySelector("#tabelaEquipamentos tr:nth-child(2)");
    if(primeira){
        selecionarLinha(primeira, primeira.getAttribute("onclick").match(/\d+/)[0]);
    }
}

// EDITAR
function abrirEditar(){
    if(idSelecionado == null){
        alert("SELECIONE UM EQUIPAMENTO!");
        return;
    }

    let linha = document.querySelector("tr.selecionado");
    document.getElementById("e_id").value = linha.children[0].innerText;
    document.getElementById("e_data").value = linha.children[1].innerText;
    document.getElementById("e_descricao").value = linha.children[2].innerText;
    document.getElementById("e_categoria").value = linha.children[3].innerText;
    document.getElementById("e_quantidade").value = linha.children[4].innerText;
    document.getElementById("e_valor").value = linha.children[5].innerText;

    document.getElementById("id_editar").value = idSelecionado;

    abrirModal("modalEditar");
}

function focarPesquisaEquipamento(){
    document.getElementById("valor_pesquisa").focus();
}

</script>

</head>
<body>

<div class="container">

<h2>MÓDULO EQUIPAMENTOS</h2>

<div class="botoes">
    <button onclick="abrirModal('modalIncluirEquipamento')">INCLUIR</button>
    <button onclick="abrirEditar()">EDITAR</button>
    <button onclick="excluir()">EXCLUIR</button>
    <button onclick="abrirModal('modalPesquisar')">PESQUISAR</button>
    <button onclick="window.location.href='equipamentos.php'">LIMPAR PESQUISA</button>
    <button class="botao-voltar" onclick="window.location.href='principal.php'">VOLTAR</button>
</div>

<div class="table-container">
<table id="tabelaEquipamentos">
<tr>
    <th>ID</th>
    <th>DATA</th>
    <th>DESCRIÇÃO</th>
    <th>CATEGORIA</th>
    <th>QUANT. INICIAL</th>
    <th>VALOR/DIA</th>
</tr>

<?php

while($row = mysqli_fetch_assoc($result)){
    echo "<tr onclick='selecionarLinha(this, ".$row['id_equipamento'].")'>
        <td>".str_pad($row['id_equipamento'], 6, '0', STR_PAD_LEFT)."</td>
        <td>".date('d/m/Y', strtotime($row['data_cadastro']))."</td>
        <td>".$row['descricao']."</td>
        <td>".$row['categoria']."</td>
        <td>".$row['quantidade_total']."</td>
        <td>".$row['valor_locacao_dia']."</td>
    </tr>";
}
?>

</table>
</div>

</div>

<!-- MODAL INCLUIR -->
<div class="modal" id="modalIncluirEquipamento">
    <div class="modal-content">
        <h3>NOVO EQUIPAMENTO</h3>

        <form method="post">

            <div class="form-linha">
                <div class="form-grupo"><label>ID</label>
                    <input type="text" id="i_id" value="<?= str_pad($proximo_id, 6, '0', STR_PAD_LEFT) ?>" readonly>
                </div>

                <div class="form-grupo"> <label>Data</label>
                    <input type="text" id="i_data" readonly>
                </div>
            </div>

            <div class="form-linha">
                <div class="form-grupo"> <label>Descrição</label>
                    <input type="text" name="descricao">
                </div>

                <div class="form-grupo"> <label>Categoria</label>
                    <select name="categoria" class="input-padrao">
                        <option value="">Selecione</option>
                        <option value="Computadores">Computadores</option>
                        <option value="Notebooks">Notebooks</option>
                        <option value="Impressoras">Impressoras</option>
                        <option value="Utilitários">Utilitários</option>
                    </select>                         
                </div>
            </div>

            <div class="form-linha">
                <div class="form-grupo"> <label>Quant. Inicial</label>
                    <input type="number" name="quantidade_total" min="0" required>
                </div>

                <div class="form-grupo"> <label>Valor locação/dia</label>
                    <input type="text" name="valor_locacao_dia">
                </div>
            </div>

            <div class="botoes-modal">
                <button type="submit" class="salvar" name="salvarEquipamento">SALVAR</button>
                <button type="button" class="cancelar" onclick="fecharModal('modalIncluirEquipamento')">CANCELAR</button>
            </div>

        </form>
    </div>
</div>

<!-- MODAL PESQUISA -->

<div class="modal" id="modalPesquisar">
    <div class="modal-content">
        <h3>PESQUISAR</h3>

        <form method="get">
            <div class="form-linha">
                <div class="form-grupo">
                    <label>CRITÉRIO</label>
                    <select name="campo" class="input-padrao" onchange="focarPesquisaEquipamento()">
                        <option value="">Selecione...</option>
                        <option value="descricao">Descrição</option>
                        <option value="categoria">Categoria</option>
                    </select>
                </div>
                <div class="form-grupo">
                    <label>PESQUISA</label>
                    <input type="text" name="valor" id="valor_pesquisa" class="input-padrao">
                </div>
            </div>

            <div class="botoes-modal">
                <button type="submit" class="salvar">PESQUISAR</button>
                <button type="button" class="cancelar" onclick="fecharModal('modalPesquisar')">CANCELAR</button>
            </div>

        </form>
    </div>
</div>

<!-- MODAL EDITAR -->

<div class="modal" id="modalEditar">
    <div class="modal-content">
        <h3>EDITAR EQUIPAMENTO</h3>
        <form method="post">
            <input type="hidden" name="id_editar" id="id_editar">
            <div class="form-linha">
                <div class="form-grupo">
                    <label>ID</label>
                    <input type="text" id="e_id" readonly>
                </div>
                <div class="form-grupo">
                    <label>Data</label>
                    <input type="text" id="e_data" readonly>
                </div>
            </div>

            <div class="form-linha">
                <div class="form-grupo">
                    <label>Descrição</label>
                    <input type="text" name="descricao" id="e_descricao">
                </div>

                <div class="form-grupo">
                    <label>Categoria</label>
                    <select name="categoria" id="e_categoria" class="input-padrao">
                        <option value="Computadores">Computadores</option>
                        <option value="Notebooks">Notebooks</option>
                        <option value="Impressoras">Impressoras</option>
                        <option value="Utilitários">Utilitários</option>
                    </select>
                </div>
            </div>

            <div class="form-linha">
                <div class="form-grupo">
                    <label>Quantidade</label>
                    <input type="number" name="quantidade_total" id="e_quantidade">
                </div>

                <div class="form-grupo">
                    <label>Valor/Dia</label>
                    <input type="text" name="valor_locacao_dia" id="e_valor">
                </div>
            </div>

            <div class="botoes-modal">
                <button type="submit" name="atualizar" class="salvar">SALVAR</button>
                <button type="button" class="cancelar" onclick="fecharModal('modalEditar')">CANCELAR</button>
            </div>

        </form>
    </div>
</div>

<?php

// INSERIR
if(isset($_POST['salvarEquipamento'])){
    $descricao = $_POST['descricao'];
    $categoria = $_POST['categoria'];
    $quantidade = $_POST['quantidade_total'];
    $valor = $_POST['valor_locacao_dia'];

    mysqli_query($conexao,"INSERT INTO equipamentos 
    (data_cadastro, descricao, categoria, quantidade_total, valor_locacao_dia)
    VALUES (NOW(),'$descricao','$categoria','$quantidade','$valor')");

    echo "<script>location.href='equipamentos.php';</script>";
}

// EXCLUIR
if(isset($_GET['excluir'])){

    $id = $_GET['excluir'];

    try{

        $sql = "
        DELETE FROM equipamentos
        WHERE id_equipamento = $id
        ";
        mysqli_query($conexao, $sql);

        // ✅ SUCESSO
        echo "
        <script>
            window.location.href='equipamentos.php';
        </script>
        ";
        exit();

    }catch(mysqli_sql_exception $e){

        // 🔥 FOREIGN KEY
        if($e->getCode() == 1451){

            echo "
            <script>
                window.location.href='equipamentos.php?erro=1';
            </script>
            ";
            exit();

        }else{

            echo "Erro: " . $e->getMessage();
        }
    }
}

// EDITAR
if(isset($_POST['atualizar'])){
    $id = $_POST['id_editar'];
    $descricao = $_POST['descricao'];
    $categoria = $_POST['categoria'];
    $quantidade = $_POST['quantidade_total'];
    $valor = $_POST['valor_locacao_dia'];

    mysqli_query($conexao, "UPDATE equipamentos SET
        descricao = '$descricao',
        categoria = '$categoria',
        quantidade_total = '$quantidade',
        valor_locacao_dia = '$valor'
    WHERE id_equipamento = $id");

    echo "<script>location.href='equipamentos.php';</script>";
}

?>

</body>
</html>