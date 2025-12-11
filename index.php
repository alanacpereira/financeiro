<?php
require_once 'config.php';
require_once 'mensagens.php';

//Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

//Buscar Resumo Financeiro
$sql_receitas = "SELECT SUM(valor) as total FROM transacao 
                 WHERE id_usuario = :usuario_id AND tipo = 'receita'";
$stmt_receitas = $conn->prepare($sql_receitas);
$stmt_receitas->bindParam(':usuario_id', $usuario_id);
$stmt_receitas->execute();
$total_receitas = $stmt_receitas->fetch()['total'] ?? 0;

$sql_despesas = "SELECT SUM(valor) as total FROM transacao 
                 WHERE id_usuario = :usuario_id AND tipo = 'despesa'";
$stmt_despesas = $conn->prepare($sql_despesas);
$stmt_despesas->bindParam(':usuario_id', $usuario_id);
$stmt_despesas->execute();
$total_despesas = $stmt_despesas->fetch()['total'] ?? 0;

$saldo = $total_receitas - $total_despesas;

// Buscar últimas transações
$sql_ultimas = "SELECT t.*, c.nome as categoria_nome 
                FROM transacao t 
                LEFT JOIN categoria c ON t.id_categoria = c.id_categoria 
                WHERE t.id_usuario = :usuario_id 
                ORDER BY t.data_transacao DESC, t.id_transacao DESC 
                LIMIT 5";
$stmt_ultimas = $conn->prepare($sql_ultimas);
$stmt_ultimas->bindParam(':usuario_id', $usuario_id);
$stmt_ultimas->execute();
$ultimas_transacoes = $stmt_ultimas->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Financeiro</title>
    <link rel="stylesheet" href="stylecat.css">
</head>

<body>
        <h1>Sistema Financeiro</h1>

        <!-- <?php include 'navbar.php'; ?> -->

        <div class=for>
        <div>
            <p classe="user">Bem-Vindo, <strong> <?php echo $usuario_nome ?></strong></p>
            <a class="sair" href="logout.php">Sair</a>
        </div>

        <?php exibir_mensagem(); ?>

        <nav>
            <ul class="menu">
                <li class="qua"><a href="index.php">📊Dashboard</a></li>
                <li class="quad"><a href="categorias_listar.php">📁Categorias</a></li>
                <li class="quadr"><a href="transacoes_listar.php">💸Transações</a></li>
            </ul>
        </nav>
        </div>

        <h2>Resumo Financeiro</h2>

        <div class="ret">
        <div>
            <div>
                <h3>Receitas</h3>
                <p>R$ <?php echo number_format($total_receitas, 2, ',', '.') ?></p>
            </div>
        </div>

        <div>
            <div>
                <h3>Despesas</h3>
                <p>R$ <?php echo number_format($total_despesas, 2, ',', '.') ?></p>
            </div>
        </div>

        <div>
            <div>
                <h3>Saldo</h3>
                <p>R$ <?php echo number_format($saldo, 2, ',', '.') ?></p>
            </div>
        </div>
        </div>

        <h2>Últimas Transações</h2>

        <?php if (count($ultimas_transacoes) > 0): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimas_transacoes as $transacao): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($transacao['data_transacao'])); ?></td>
                            <td><?php echo htmlspecialchars($transacao['descricao']); ?></td>
                            <td><?php echo htmlspecialchars($transacao['categoria_nome'] ?? 'Sem categoria'); ?></td>
                            <td><?php echo ucfirst($transacao['tipo']); ?></td>
                            <td>R$ <?php echo number_format($transacao['valor'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p><a href="transacoes_listar.php">Ver todas as transações</a></p>
        <?php else: ?>
            <p>Nenhuma transação cadastrada ainda.</p>
            <p><a href="transacoes_formulario.php">Cadastrar primeira transação</a></p>
        <?php endif; ?>
   

</body>

</html>