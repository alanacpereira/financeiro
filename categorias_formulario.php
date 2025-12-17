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

// Verificar se está editando
$id_categoria = $_GET['id'] ?? null;
$categoria = null;

if ($id_categoria) {
    $sql = "SELECT * FROM categoria 
            WHERE id_categoria = :id_categoria 
            AND id_usuario = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_categoria', $id_categoria);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    $categoria = $stmt->fetch();

    if (!$categoria) {
        set_mensagem('Categoria não encontrada.', 'erro');
        header('Location: categorias_listar.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Categorias - Sistema Financeiro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylefor.css">
</head>

<body>

<h1>Sistema Financeiro</h1>

<header class="topbar">
    <div class="user-area">
        <p class="user">Bem-vindo, <strong><?= htmlspecialchars($usuario_nome) ?></strong></p>
        <a class="sair" href="logout.php">Sair</a>
    </div>
</header>

<?php exibir_mensagem(); ?>

<!-- MENU -->
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

<!-- TÍTULO -->
<h2 class="titulo-form">
    <?= $categoria ? 'Editar' : 'Nova'; ?> Categoria
</h2>

<!-- FORMULÁRIO CENTRALIZADO -->
<div class="form-wrapper">
    <form action="categorias_salvar.php" method="POST">

        <?php if ($categoria): ?>
            <input type="hidden" name="id_categoria" value="<?= $categoria['id_categoria']; ?>">
        <?php endif; ?>

        <div class="form-container">

            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome"
                       value="<?= $categoria ? htmlspecialchars($categoria['nome']) : ''; ?>"
                       required>
            </div>

            <div>
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Selecione...</option>
                    <option value="receita" <?= ($categoria && $categoria['tipo'] === 'receita') ? 'selected' : ''; ?>>
                        Receita
                    </option>
                    <option value="despesa" <?= ($categoria && $categoria['tipo'] === 'despesa') ? 'selected' : ''; ?>>
                        Despesa
                    </option>
                </select>
            </div>

            <!-- BOTÕES CENTRALIZADOS -->
            <div class="buttons">
                <button type="submit" class="btn-salvar">Salvar</button>
                <a href="categorias_listar.php" class="btn-cancelar">Cancelar</a>
            </div>

        </div>
    </form>
</div>

</body>
</html>
