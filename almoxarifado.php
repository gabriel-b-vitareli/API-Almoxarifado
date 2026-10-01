<?php 

// Indicando que vamos trabalhar com JSON:
header("Content-Type: application/json");

// Conectando ao arquivo de conexão para conseguir importar as variáveis de conexão e o PDO:
require "conexao.php";

// Salvando o método de requisição que o servidor receber na variável de método:
$metodo = $_SERVER["REQUEST_METHOD"];

// ---------------------------- MÉTODOS -------------------------------- 

//  Código para executar caso seja recebido um GET:
if($metodo == "GET"){
    $sql = "SELECT * FROM pecas ORDER BY id";

    $comando = $pdo -> query($sql);

    $pecas = $comando -> fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($pecas);
};

// Código para executar caso seja recebido um POST:
if($metodo == "POST"){

    $json = file_get_contents("php://input"); // Armazenando os dados recebidos

    $dados = json_decode($json,true); // Decodifidando o JSON

    if($dados['categoria'] == 'eletrica' or $dados['categoria'] == 'mecanica' or $dados['categoria'] == 'hidraulica'){
        if($dados['quantidade'] >= 0){
            if($dados['preco_unitario'] > 0){
            // Guardando o comando SQL na variável de SQL e preparando o comando:
            $sql = "INSERT INTO pecas (nome,categoria,fornecedor,quantidade,preco_unitario) VALUES (?,?,?,?,?)"; 
            $comando = $pdo -> prepare($sql);
        
            // Executando o comando SQL
            $comando -> execute([
                $dados["nome"],
                $dados["categoria"],
                $dados["fornecedor"],
                $dados["quantidade"],
                $dados["preco_unitario"]
            ]);
        
            echo json_encode(["Mensagem"=>"Peça cadastrada com sucesso!"]);
            } else{
                echo json_encode(["Mensagem"=>"O preço unitário da peça deve ser maior que zero."]);
            }
        } else{
            echo json_encode(["Mensagem"=>"A quantidade de uma peça não deve ser negativa."]);
        }
        } else{
        echo json_encode(["Mensagem"=>"Por favor, insira a categoria da peça como 'eletrica', 'mecanica' ou 'hidraulica', sem acentos ou letras maiúsculas."]);
        }
}

// Código para executar caso seja recebido um PUT:
if ($metodo == 'PUT') {
    $json = file_get_contents('php://input'); 
    $dados = json_decode($json, true); 

    if ($dados['categoria'] == 'eletrica' || $dados['categoria'] == 'mecanica' || $dados['categoria'] == 'hidraulica') {
        if ($dados['quantidade'] >= 0) {
            if ($dados['preco_unitario'] > 0) {
                
                // CORREÇÃO AQUI: Mudado para os nomes reais das colunas da tabela "pecas"
                $sql = "UPDATE pecas SET nome=?, categoria=?, fornecedor=?, quantidade=?, preco_unitario=? WHERE id=?"; 
                $comando = $pdo->prepare($sql);
                
                $comando->execute([
                    $dados['nome'],           // ? 1
                    $dados['categoria'],      // ? 2
                    $dados['fornecedor'],     // ? 3
                    $dados['quantidade'],     // ? 4
                    $dados['preco_unitario'], // ? 5
                    $dados['id']              // ? 6
                ]); 
                
                echo json_encode(['Mensagem' => 'Peça atualizada com sucesso!']);
            } else {
                echo json_encode(['Mensagem' => 'O preço unitário da peça deve ser maior que zero.']);
            }
        } else {
            echo json_encode(['Mensagem' => 'A quantidade de uma peça não deve ser negativa.']);
        }
    } else {
        echo json_encode(['Mensagem' => 'Por favor, insira a categoria da peça como eletrica, mecanica ou hidraulica, sem acentos ou letras maiúsculas.']);
    }
}



// Código para executar caso o método seja DELETE:
if($metodo == "DELETE"){
    $json = file_get_contents("php://input");

    $dados = json_decode($json,true);

    $sql = "DELETE FROM pecas WHERE id=?";

    $comando = $pdo -> prepare($sql);

    $comando -> execute([
        $dados["id"]
    ]);

    echo json_encode(["Mensagem"=>"Peça excluída com sucesso!"]);
}
?>