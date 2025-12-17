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

//Verificar se está editando
$id_categoria = $_GET['id'] ?? null;
$categoria = null;

if ($id_categoria) {
    // Buscar categoria para editar
    $sql = "SELECT * FROM categoria WHERE id_categoria = :id_categoria AND id_usuario = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_categoria', $id_categoria);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    $categoria = $stmt->fetch();

    // Se não encontrou ou não pertence ao usuário, redireciona
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Sistema Financeiro</title>
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


    <h2 style="color:  #FFD100; text-align: center;"><?php echo $categoria ? 'Editar' : 'Nova'; ?> Categoria</h2>


    <form action="categorias_salvar.php" method="POST" class="centro">
        <?php if ($categoria): ?>
            <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">
        <?php endif; ?>

        <div class="form-container">
            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome"
                    value="<?php echo $categoria ? htmlspecialchars($categoria['nome']) : ''; ?>"
                    required>
            </div>

            <div>
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Selecione...</option>
                    <option value="receita" <?php echo ($categoria && $categoria['tipo'] === 'receita') ? 'selected' : ''; ?>>Receita</option>
                    <option value="despesa" <?php echo ($categoria && $categoria['tipo'] === 'despesa') ? 'selected' : ''; ?>>Despesa</option>
                </select>
            </div>
        </div>

        <div class="buttons">
            <button type="submit" class="btn-salvar">Salvar</button>
            <a href="categorias_listar.php" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</body>

</html>