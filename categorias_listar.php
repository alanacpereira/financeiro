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

// Buscar todas as categorias do usuário
$sql = "SELECT * FROM categoria WHERE id_usuario = :usuario_id ORDER BY tipo, nome";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();
$categorias = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Sistema Financeiro</title>
    <link rel="stylesheet" href="stylelistar.css">
</head>


<body>
    <div class="container">

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


        <h2>Categorias</h2>

        <div>
            <a class="btn btn-primary" href="categorias_formulario.php">Nova Categoria</a>
        </div>

        <?php if (count($categorias) > 0): ?>
            <table class="table">
                <thead>
                    <tr class="tabe">
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                            <td><?php echo ucfirst($categoria['tipo']); ?></td>
                            <td>
                                <a class="btn btn-success" href="categorias_formulario.php?id=<?php echo $categoria['id_categoria']; ?>">Editar</a>
                                <a class="btn btn-danger" href="categorias_excluir.php?id=<?php echo $categoria['id_categoria']; ?>"
                                    onclick="return confirm('Tem certeza que deseja excluir esta categoria?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhuma categoria cadastrada ainda.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>