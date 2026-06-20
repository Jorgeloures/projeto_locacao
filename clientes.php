<?php 
include("verifica_login.php");
include("conexao.php"); 

?>

<?php 
if(isset($_GET['erro'])){

    echo "
    <script>
        alert('CLIENTE NÃO PODE SER EXCLUÍDO POIS POSSUI LOCAÇÕES!');
    </script>
    ";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clientes</title>

    <style>
body { font-family: "Segoe UI"; margin: 0; background: url('fundo1.png');; }
.container { width: 100%; height: calc(100vh - 80px); padding: 15px; box-sizing: border-box; }
h2 { color: white; }

/* BOTÕES */
.botoes { margin-bottom: 15px; }
.botoes button { padding: 10px 20px; margin-right: 20px; border: none; cursor: pointer; background: #3498db; color: white; border-radius: 5px; }

/* TABELA */
.table-container { height: 100%; border-radius: 10px;max-height:calc(100vh - 150px); overflow-y: auto; background: white; }
table { width: 100%; border-collapse: collapse; }
th { background: white; color: black; font-size: 13px; text-align: center; padding: 10px; border: 1px solid #ccc; }
td { padding: 5px 7px; border: 1px solid #ccc; font-size: 13px; }

th:nth-child(1), td:nth-child(1) { width: 50px; text-align: center; }
th:nth-child(2), td:nth-child(2) { width: 70px; text-align: center; }
th:nth-child(3), td:nth-child(3) { width: 220px; }
th:nth-child(4), td:nth-child(4) { width: 120px; text-align: center; }
th:nth-child(5), td:nth-child(5) { width: 130px; }
th:nth-child(6), td:nth-child(6) { width: 70px; }
th:nth-child(7), td:nth-child(7) { width: 100px; text-align: center; }
th:nth-child(8), td:nth-child(8) { width: 100px; text-align: center; }
th:nth-child(9), td:nth-child(9) { width: 200px; }

tr:hover { background: #f2f2f2; }
thead th { position: sticky; top: 0; background: #2c3e50; z-index: 2; }
th { text-align: center !important; }
tr.selecionado { background: #483ba0 !important; color: white; }

/* MODAL */
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); }
.modal-content { background: #f4f6f7; width: 1100px; max-width: 90%; margin: 10% auto; padding: 30px; border-radius: 12px; }
.fechar { float: right; cursor: pointer; color: red; }

input { width: 95%; padding: 8px; margin: 5px 0; }
button.salvar { width: 100%; padding: 10px; background: #3498db; color: white; border: none; }
button:active { transform: scale(0.95); box-shadow: inset 0px 3px 8px rgba(0,0,0,0.4); }

#modalVisualizar input { background: #ecf0f1; cursor: not-allowed; }

/* MODAL DE INCLUSÃO */
.form-linha { display: flex; gap: 30px; margin-bottom: 20px; }
.form-grupo { flex: 1; }
.form-grupo label { display: block; margin-bottom: 5px; font-weight: bold; }
.form-grupo input { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; }

/* BOTÕES MODAL */
.botoes-modal { margin-top: 20px; display: flex; justify-content: space-between; }
.botoes-modal button { width: 48%; padding: 10px; border-radius: 10px; border: none; font-weight: bold; cursor: pointer; }

/* SALVAR */
.salvar { background: #3498db; color: white; }
.salvar:hover { background: #2c80b4; }

/* CANCELAR */
.cancelar { background: #bdc3c7; color: black; }
.cancelar:hover { background: #95a5a6; }

/* INPUT PADRÃO */
.input-padrao { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; font-size: 14px; }
select.input-padrao {background: white; }

/* MODAL VISUALIZAR */
#modalVisualizar .modal-content { position: absolute; top: 30%; left: 50%; transform: translate(-50%, -50%); max-height: 90vh; overflow-y: auto; }
#modalVisualizar .botoes-modal { justify-content: center; }
#modalVisualizar .botoes-modal button { width: 400px; }

/* MODAL EDITAR */
#modalEditar .modal-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 1100px; max-width: 95%; max-height: 90vh; overflow-y: auto; margin: 0; }
#modalEditar input[readonly] { background: #ecf0f1; cursor: not-allowed; }

</style>
<script>
        
function fecharModal(id){
        document.getElementById(id).style.display = "none";
    }
    let idSelecionado = null;
    let nomeSelecionado = "";

        function selecionarLinha(linha, id){
          let linhas = document.querySelectorAll("tr");
          linhas.forEach(l => l.classList.remove("selecionado"));

          linha.classList.add("selecionado");

          idSelecionado = id;

    // 👇 pega o nome diretamente da coluna 3 (Nome)
    nomeSelecionado = linha.children[2].innerText;
    }

function excluir(){
    if(idSelecionado == null){
        alert("SELECIONE O CLIENTE A SER EXCLUÍDO!");
        return;
    }

    if(confirm("DESEJA REALMENTE EXCLUIR O CLIENTE?\n\n" + nomeSelecionado + "\n\n(ID: " + idSelecionado + ")?")){window.location.href = "clientes.php?excluir=" + idSelecionado;}
    }

function formatarTelefone(campo){
    let valor = campo.value.replace(/\D/g, "");

    if(valor.length <= 10){
        valor = valor.replace(/(\d{2})(\d)/, "($1) $2");
        valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
    } else {
        valor = valor.replace(/(\d{2})(\d)/, "($1) $2");
        valor = valor.replace(/(\d{5})(\d)/, "$1-$2");
    }

    campo.value = valor;
    }

function formatarCpfCnpj(campo){
    let valor = campo.value.replace(/\D/g, "");

    if(valor.length <= 11){
        // CPF: 000.000.000-00
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    } else {
        // CNPJ: 00.000.000/0000-00
        valor = valor.replace(/(\d{2})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)/, "$1/$2");
        valor = valor.replace(/(\d{4})(\d{1,2})$/, "$1-$2");
    }

    campo.value = valor;
    }

function formatarCEP(campo){
    let valor = campo.value.replace(/\D/g, "");
    valor = valor.replace(/(\d{5})(\d)/, "$1-$2");
    campo.value = valor;
    }

window.onload = function(){
    let primeira = document.querySelector("table tr:nth-child(2)");
    if(primeira){
        primeira.click();}
    }

function abrirModal(id){
    document.getElementById(id).style.display = "block";
    if(id === "modalPesquisar"){
        let campo = document.getElementById("campo");
        campo.value = "";
        campo.focus();
    }
}

function abrirVisualizar(){
    if(idSelecionado == null){
        alert("SELECIONE UM CLIENTE!");
        return;
    }

    let linha = document.querySelector("tr.selecionado");
    document.getElementById("v_data").value = linha.children[1].innerText;
    document.getElementById("v_nome").value = linha.children[2].innerText;
    document.getElementById("v_cpf").value = linha.children[3].innerText;
    document.getElementById("v_cidade").value = linha.children[4].innerText;
    document.getElementById("v_estado").value = linha.children[5].innerText;
    document.getElementById("v_tel1").value = linha.children[6].innerText;
    document.getElementById("v_tel2").value = linha.children[7].innerText;
    document.getElementById("v_email").value = linha.children[8].innerText;
    document.getElementById("v_endereco").value = linha.children[9].innerText;
    document.getElementById("v_cep").value = linha.children[10].innerText;

    abrirModal("modalVisualizar");   
}

function abrirEditar(){
    if(idSelecionado == null){
        alert("SELECIONE UM CLIENTE!");
        return;
    }

    let linha = document.querySelector("tr.selecionado");
    document.getElementById("e_id").value = idSelecionado;
    document.getElementById("e_data").value = linha.children[1].innerText;
    document.getElementById("e_nome").value = linha.children[2].innerText;
    document.getElementById("e_cpf").value = linha.children[3].innerText;
    document.getElementById("e_cidade").value = linha.children[4].innerText;
    document.getElementById("e_estado").value = linha.children[5].innerText;
    document.getElementById("e_tel1").value = linha.children[6].innerText;
    document.getElementById("e_tel2").value = linha.children[7].innerText;
    document.getElementById("e_email").value = linha.children[8].innerText;

    // campos escondidos
    document.getElementById("e_endereco").value = linha.children[9].innerText;
    document.getElementById("e_cep").value = linha.children[10].innerText;

    abrirModal("modalEditar");
}

function focarPesquisaCliente(){
    document.getElementById("valor_pesquisa_cliente").focus();}

function buscarCEP(tipo){
    let cep = document.getElementById(tipo + "_cep").value;

    // remove máscara
    cep = cep.replace(/\D/g,'');
    if(cep.length != 8){
        return;
    }

    fetch("https://viacep.com.br/ws/" + cep + "/json/")
    .then(response => response.json())
    .then(dados => {
        if(dados.erro){
            alert("CEP NÃO ENCONTRADO!");
            return;
        }

        document.getElementById(tipo + "_cidade").value = dados.localidade;
        document.getElementById(tipo + "_estado").value = dados.uf;
        document.getElementById(tipo + "_endereco").value = dados.logradouro;
    })

    .catch(function(){

        alert("ERRO AO CONSULTAR CEP!");
    });
}

function limparModalIncluir(){

    document.getElementById("i_cep").value = "";
    document.getElementById("i_endereco").value = "";
    document.getElementById("i_cidade").value = "";
    document.getElementById("i_estado").value = "";
    document.querySelector("#modalIncluir input[name='nome']").value = "";
    document.querySelector("#modalIncluir input[name='cpf']").value = "";
    document.querySelector("#modalIncluir input[name='telefone1']").value = "";
    document.querySelector("#modalIncluir input[name='telefone2']").value = "";
    document.querySelector("#modalIncluir input[name='email']").value = "";
}

</script>
   
</head>
<body>

<div class="container">

    <h2>MÓDULO CLIENTES</h2>

    <!-- BOTÕES -->
    <div class="botoes">
        <button onclick="abrirModal('modalIncluir')">INCLUIR</button>
        <button onclick="abrirEditar()">EDITAR</button>
        <button onclick="excluir()">EXCLUIR</button>
        <button onclick="abrirModal('modalPesquisar')">PESQUISAR</button>
        <button onclick="window.location.href='clientes.php'">LIMPAR PESQUISA</button>
        <button onclick="abrirVisualizar()">VISUALIZAR</button>
        <button onclick="window.location.href='principal.php'">VOLTAR</button>
    </div>

    <!-- TABELA -->
    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>DATA</th>
                <th>NOME</th>
                <th>CPF/CNPJ</th>
                <th>CIDADE</th>
                <th>ESTADO</th>
                <th>TEL/WHATSAPP</th>
                <th>TELEFONE 2</th>
                <th>EMAIL</th>
            </tr>

            <?php
            
$campo = isset($_GET['campo']) ? $_GET['campo'] : '';
$valor = isset($_GET['valor']) ? trim($_GET['valor']) : '';

$sql = "SELECT * FROM clientes";
$result = mysqli_query($conexao, $sql);

while($row = mysqli_fetch_assoc($result)){

    if($valor != ""){
    $texto = strtolower($valor);

    // pega o campo escolhido
    if($campo == "nome"){$comparar = strtolower($row['nome']);}
    elseif($campo == "Cidade"){$comparar = strtolower($row['cidade']);}
    elseif($campo == "email"){$comparar = strtolower($row['email']);
    } else {
        $comparar = "";}

    // lógica de comparação (igual você já usava)
    if(strpos($comparar, $texto) !== 0){
        continue;
    }
}
    echo "<tr onclick='selecionarLinha(this, ".$row['id_cliente'].")'>
            <td>".str_pad($row['id_cliente'], 4, '0', STR_PAD_LEFT)."</td>
            <td>".date('d/m/Y', strtotime($row['data_cadastro']))."</td>
            <td>".$row['nome']."</td>
            <td>".$row['cpf']."</td>
            <td>".$row['cidade']."</td>
            <td>".$row['estado']."</td>
            <td>".$row['telefone1']."</td>
            <td>".$row['telefone2']."</td>
            <td>".$row['email']."</td>
            <td style='display:none'>".$row['endereco']."</td>
            <td style='display:none'>".$row['cep']."</td>
          </tr>";
}
            ?>
        </table>
    </div>
</div>

<!-- MODAL INCLUIR -->
<div class="modal" id="modalIncluir">
    <div class="modal-content">
        <h3>NOVO CLIENTE</h3>

        <form method="post">
            <div class="form-linha">
                <div class="form-grupo"> Nome <input type="text" name="nome"></div>
                <div class="form-grupo"> CPF/CNPJ <input type="text" name="cpf" id="cpf" onkeyup="formatarCpfCnpj(this)"></div>
                <div class="form-grupo"> Endereço <input type="text" id="i_endereco" name="endereco"></div>
            </div>

            <div class="form-linha">            
                <div class="form-grupo"> Cidade <input type="text" id="i_cidade" name="cidade"></div>
                <div class="form-grupo"> Estado <input type="text" id="i_estado" name="estado"></div>
                <div class="form-grupo"> CEP
                    <input type="text" id="i_cep" name="cep" onkeyup="formatarCEP(this)" onblur="buscarCEP('i')"></div> 
            </div>

            <div class="form-linha">       
                <div class="form-grupo"> Telefone 1 <input type="text" id="telefone1" name="telefone1" onkeyup="formatarTelefone(this)"></div>
                <div class="form-grupo"> Telefone 2 <input type="text" id="telefone2" name="telefone2" onkeyup="formatarTelefone(this)"></div>
                <div class="form-grupo"> Email <input type="text" name="email"></div>
            </div>

            <div class="botoes-modal">
                <button type="submit" class="salvar" name="salvar">SALVAR</button>
                <button type="button" class="cancelar" onclick="limparModalIncluir(); fecharModal('modalIncluir')"> CANCELAR</button>                
            </div>

        </form>
    </div>
</div>

<!-- MODAL PESQUISA -->
<div class="modal" id="modalPesquisar">
    <div class="modal-content">
        <h3>PESQUISAR CLIENTE</h3>
        <form method="get" action="clientes.php">
            <div class="form-linha">
                <div class="form-grupo">
                    <label>CRITÉRIO</label>
                    <select name="campo" id="campo" class="input-padrao" onchange="focarPesquisaCliente()">
                        <option value="">Selecione...</option>
                        <option value="nome">Nome</option>
                        <option value="Cidade">Cidade</option>
                        <option value="email">Email</option>
                    </select>
                </div>

                <div class="form-grupo">
                    <label>PESQUISA</label>
                    <input type="text" name="valor" id="valor_pesquisa_cliente" class="input-padrao">
                </div>

            </div>

            <div class="botoes-modal">
                <button type="submit" class="salvar">PESQUISAR</button>
                <button type="button" class="cancelar" onclick="fecharModal('modalPesquisar')">CANCELAR</button>
            </div>

        </form>
    </div>
</div>

<!-- MODAL VISUALIZAR -->

<div class="modal" id="modalVisualizar">
    <div class="modal-content">
        <h3>VISUALIZAR CLIENTE</h3>
        <form>
            <div class="form-linha">
                <div class="form-grupo"> Nome <input type="text" id="v_nome" readonly></div>
                <div class="form-grupo"> CPF/CNPJ <input type="text" id="v_cpf" readonly></div>
            </div>

            <div class="form-linha">
                <div class="form-grupo"> Endereço <input type="text" id="v_endereco" readonly></div>
                <div class="form-grupo"> Cidade <input type="text" id="v_cidade" readonly></div>
            </div>

            <div class="form-linha">
                <div class="form-grupo"> Estado <input type="text" id="v_estado" readonly></div>
                <div class="form-grupo"> CEP <input type="text" id="v_cep" readonly></div>
            </div>

            <div class="form-linha">
                <div class="form-grupo"> Telefone 1 <input type="text" id="v_tel1" readonly></div>
                <div class="form-grupo"> Telefone 2 <input type="text" id="v_tel2" readonly></div>
            </div>

            <div class="form-linha">
                <div class="form-grupo"> Email <input type="text" id="v_email" readonly></div>
                <div class="form-grupo"> Data Cadastro <input type="text" id="v_data" readonly></div>
            </div>

            <div class="botoes-modal">
                <button type="button" class="cancelar" onclick="fecharModal('modalVisualizar')"> SAIR
                </button>
            </div>

        </form>
    </div>
</div>

<!-- MODAL EDITAR -->

<div class="modal" id="modalEditar">
    <div class="modal-content">
        <h3>EDITAR CLIENTE</h3>
        <form method="post">
            <div class="form-linha">
                <div class="form-grupo"> ID <input type="text" id="e_id" name="id" readonly></div>
                <div class="form-grupo"> Data <input type="text" id="e_data" readonly></div>
            </div>

            <div class="form-linha">
                <div class="form-grupo"> Nome <input type="text" id="e_nome" name="nome"></div>
                <div class="form-grupo"> CPF/CNPJ <input type="text" id="e_cpf" name="cpf" onkeyup="formatarCpfCnpj(this)"></div>
            </div>

            <div class="form-linha">
                <div class="form-grupo"> Endereço <input type="text" id="e_endereco" name="endereco"></div>
                <div class="form-grupo"> Cidade <input type="text" id="e_cidade" name="cidade"></div>
            </div>

            <div class="form-linha">
                <div class="form-grupo"> Estado <input type="text" id="e_estado" name="estado"></div>
                <div class="form-grupo"> CEP <input type="text" id="e_cep" name="cep" onkeyup="formatarCEP(this)" onblur="buscarCEP('e')"></div>
            </div>

            <div class="form-linha">
                <div class="form-grupo"> Telefone 1 <input type="text" id="e_tel1" name="telefone1" onkeyup="formatarTelefone(this)"></div>

                <div class="form-grupo"> Telefone 2 <input type="text" id="e_tel2" name="telefone2" onkeyup="formatarTelefone(this)"></div>
            </div>

            <div class="form-linha">
                <div class="form-grupo"> Email <input type="text" id="e_email" name="email"></div>
            </div>

            <div class="botoes-modal">
                <button type="submit" class="salvar" name="atualizar">SALVAR</button>
                <button type="button" class="cancelar" onclick="fecharModal('modalEditar')">CANCELAR</button>
            </div>

        </form>
    </div>
</div>


<?php

// INSERIR CLIENTE
if(isset($_POST['salvar'])){
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $endereco = $_POST['endereco'];

    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $cep = $_POST['cep'];

    $tel1 = $_POST['telefone1'];
    $tel2 = $_POST['telefone2'];
    $email = $_POST['email'];

    mysqli_query($conexao,"INSERT INTO clientes 
    (data_cadastro, nome, cpf, endereco, cidade, estado, cep, telefone1, telefone2, email)
    VALUES (NOW(),'$nome','$cpf','$endereco','$cidade','$estado','$cep','$tel1','$tel2','$email')");

    echo "<script>location.href='clientes.php';</script>";
}

// EXCLUIR CLIENTE
if(isset($_GET['excluir'])){

    $id = $_GET['excluir'];

    try{

        $sql = "
        DELETE FROM clientes
        WHERE id_cliente = $id
        ";

        mysqli_query($conexao, $sql);

        // ✅ SUCESSO
        echo "
        <script>
        window.location.href='clientes.php';
        </script>
        ";
        exit();

    }catch(mysqli_sql_exception $e){

        // 🔥 ERRO FOREIGN KEY
        if($e->getCode() == 1451){

            echo "
            <script>
            window.location.href='clientes.php?erro=1';
            </script>
            ";

        exit();
        }else{

            echo "Erro: " . $e->getMessage();
        }
    }
}

// Visualizar
if(isset($_POST['atualizar'])){
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $endereco = $_POST['endereco'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $cep = $_POST['cep'];
    $tel1 = $_POST['telefone1'];
    $tel2 = $_POST['telefone2'];
    $email = $_POST['email'];

    mysqli_query($conexao,"UPDATE clientes SET 
        nome='$nome',
        cpf='$cpf',
        endereco='$endereco',
        cidade='$cidade',
        estado='$estado',
        cep='$cep',
        telefone1='$tel1',
        telefone2='$tel2',
        email='$email'
        WHERE id_cliente = $id");

    echo "<script>location.href='clientes.php';</script>";
}

?>

</body>
</html>