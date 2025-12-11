<?php
require_once 'config.php';
require_once 'mensagens.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $id_transacao = $_POST['id_transacao'] ?? null;
    $descricao = $_POST['descricao'] ?? '';
    $valor = $_POST['valor'] ?? '';
    $data_transacao = $_POST['data_transacao'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $id_categoria = $_POST['id_categoria'] ?? '';
    
    // Validar campos
    if (empty($descricao) || empty($valor) || empty($data_transacao) || empty($tipo) || empty($id_categoria)) {
        set_mensagem('Preencha todos os campos.','erro');
        header('Location: transacoes_formulario.php' . ($id_transacao ? '?id=' . $id_transacao : ''));
        exit;
    }
    
    // Validar tipo
    if (!in_array($tipo, ['receita', 'despesa'])) {
        set_mensagem('Tipo inválido.','erro');
        header('Location: transacoes_formulario.php' . ($id_transacao ? '?id=' . $id_transacao : ''));
        exit;
    }
    
    // Validar valor
    if (!is_numeric($valor) || $valor <= 0) {
        set_mensagem('Valor inválido.','erro');
        header('Location: transacoes_formulario.php' . ($id_transacao ? '?id=' . $id_transacao : ''));
        exit;
    }
    
    // Verificar se a categoria pertence ao usuário
    $sql = "SELECT id_categoria FROM categoria WHERE id_categoria = :id_categoria AND id_usuario = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_categoria', $id_categoria);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        set_mensagem('Categoria inválida.','erro');
        header('Location: transacoes_formulario.php' . ($id_transacao ? '?id=' . $id_transacao : ''));
        exit;
    }
    
    if ($id_transacao) {
        // Atualizar transação existente
        $sql = "UPDATE transacao 
                SET descricao = :descricao, valor = :valor, data_transacao = :data_transacao, 
                    tipo = :tipo, id_categoria = :id_categoria 
                WHERE id_transacao = :id_transacao AND id_usuario = :usuario_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':data_transacao', $data_transacao);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':id_categoria', $id_categoria);
        $stmt->bindParam(':id_transacao', $id_transacao);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        if ($stmt->execute()) {
            set_mensagem('Transação atualizada com sucesso!', 'sucesso');
        } else {
            set_mensagem('Erro ao atualizar transação.','erro');
        }
    } else {
        // Inserir nova transação
        $sql = "INSERT INTO transacao (id_usuario, descricao, valor, data_transacao, tipo, id_categoria) 
                VALUES (:usuario_id, :descricao, :valor, :data_transacao, :tipo, :id_categoria)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':data_transacao', $data_transacao);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':id_categoria', $id_categoria);
        
        if ($stmt->execute()) {
            set_mensagem('Transação cadastrada com sucesso!', 'sucesso');
        } else {
            set_mensagem('Erro ao cadastrar transação.','erro');
        }
    }
    
    header('Location: transacoes_listar.php');
    exit;
} else {
    header('Location: transacoes_listar.php');
    exit;
}
?>
