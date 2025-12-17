<?php
require_once 'config.php';
require_once 'mensagens.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

// Filtros
$filtro_tipo = $_GET['tipo'] ?? '';
$filtro_categoria = $_GET['categoria'] ?? '';

// Buscar todas as transações do usuário
$sql = "SELECT t.*, c.nome as categoria_nome 
        FROM transacao t 
        LEFT JOIN categoria c ON t.id_categoria = c.id_categoria 
        WHERE t.id_usuario = :usuario_id";

$params = [':usuario_id' => $usuario_id];

// Aplicar filtros
if ($filtro_tipo && in_array($filtro_tipo, ['receita', 'despesa'])) {
    $sql .= " AND t.tipo = :tipo";
    $params[':tipo'] = $filtro_tipo;
}

if ($filtro_categoria) {
    $sql .= " AND t.id_categoria = :categoria";
    $params[':categoria'] = $filtro_categoria;
}

$sql .= " ORDER BY t.data_transacao DESC, t.id_transacao DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$transacoes = $stmt->fetchAll();

// Buscar categorias para o filtro
$sql_categorias = "SELECT * FROM categoria WHERE id_usuario = :usuario_id ORDER BY nome";
$stmt_categorias = $conn->prepare($sql_categorias);
$stmt_categorias->bindParam(':usuario_id', $usuario_id);
$stmt_categorias->execute();
$categorias = $stmt_categorias->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transações - Sistema Financeiro</title>
    <link rel="stylesheet" href="styletranlistar.css">
</head>

<body>
    <h1>Sistema Financeiro Pessoal</h1>

    <header class="topbar">
        <div class="user-area">
            <p class="user">Bem-vindo, <strong><?= htmlspecialchars($usuario_nome) ?></strong></p>
            <a class="sair" href="logout.php">Sair</a>
        </div>
    </header>

    <?php exibir_mensagem(); ?>

    <!-- HERO / MENU SUPERIOR -->
    <section class="hero-area">
        <nav class="menu-wrapper">
            <ul class="menu menu-wide">
                <li class="menu-item menu-card">
                    <a href="index.php">
                        <span class="icon">📊</span>
                        <span class="text">Dashboard</span>
                    </a>
                </li>

                <li class="menu-item menu-card">
                    <a href="categorias_listar.php">
                        <span class="icon">📁</span>
                        <span class="text">Categorias</span>
                    </a>
                </li>

                <li class="menu-item menu-card">
                    <a href="transacoes_listar.php">
                        <span class="icon">💸</span>
                        <span class="text">Transações</span>
                    </a>
                </li>
            </ul>
        </nav>
    </section>


    <h2>Transações</h2>

    <div>
        <a href="transacoes_formulario.php" class="nov">Nova Transação</a>
    </div>

    <h3>Filtros</h3>
    <form method="GET" action="transacoes_listar.php" class="form-container">
        <div>
            <label for="tipo">Tipo:</label>
            <select id="tipo" name="tipo">
                <option value="">Todos</option>
                <option value="receita" <?php echo $filtro_tipo === 'receita' ? 'selected' : ''; ?>>Receita</option>
                <option value="despesa" <?php echo $filtro_tipo === 'despesa' ? 'selected' : ''; ?>>Despesa</option>
            </select>
        </div>

        <div>
            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria['id_categoria']; ?>"
                        <?php echo $filtro_categoria == $categoria['id_categoria'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categoria['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="filtro">
            <button>Filtrar</button>
            <a href="transacoes_listar.php" class="limpar">Limpar Filtros</a>
        </div>
    </form>

    <?php if (count($transacoes) > 0): ?>
        <table border="1" class="table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transacoes as $transacao): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($transacao['data_transacao'])); ?></td>
                        <td><?php echo htmlspecialchars($transacao['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($transacao['categoria_nome'] ?? 'Sem categoria'); ?></td>
                        <td><?php echo ucfirst($transacao['tipo']); ?></td>
                        <td>R$ <?php echo number_format($transacao['valor'], 2, ',', '.'); ?></td>
                        <td>
                            <a href="transacoes_formulario.php?id=<?php echo $transacao['id_transacao']; ?>">Editar</a>
                            <a href="transacoes_excluir.php?id=<?php echo $transacao['id_transacao']; ?>"
                                onclick="return confirm('Tem certeza que deseja excluir esta transação?');">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhuma transação encontrada.</p>
    <?php endif; ?>
</body>

</html>