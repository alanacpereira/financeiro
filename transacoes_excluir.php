<?php
require_once 'config.php';
require_once 'mensagens.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$id_transacao = $_GET['id'] ?? null;

if ($id_transacao) {
    // Verificar se a transação pertence ao usuário
    $sql = "SELECT id_transacao FROM transacao WHERE id_transacao = :id_transacao AND id_usuario = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_transacao', $id_transacao);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        // Excluir transação
        $sql = "DELETE FROM transacao WHERE id_transacao = :id_transacao AND id_usuario = :usuario_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_transacao', $id_transacao);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        if ($stmt->execute()) {
            set_mensagem('Transação excluída com sucesso!', 'sucesso');
        } else {
            set_mensagem('Erro ao excluir transação.', 'erro');
        }
    } else {
        set_mensagem('Transação não encontrada.', 'erro');
    }
} else {
    set_mensagem('ID da transação não informado.', 'erro');
}

header('Location: transacoes_listar.php');
exit;
?>
