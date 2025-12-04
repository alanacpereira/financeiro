<?php
require_once 'config.php';

//Verificar se o usuário está logado
if (isset ($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Financeiro</title>
</head>
<body>
    <h1>Login - Sistema Financeiro</h1>
    <form action="autenticar.php" method="post">
        <div>
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required>
        </div>
        <div>
            <button type="submit">Entrar</button>
        </div>
    </form>
    
    <p>Não tem conta? <a href="registro.php">Cadastre-se aqui.</a></p>
    
</body>
</html>