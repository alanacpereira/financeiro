<?php

//Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'financeiro');
define('DB_USER', 'root');
define('DB_PASS', 'mysql');

//Conexão com o Banco de Dados
try {
   $conn = new PDO("mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";charset=utf8mb4",DB_USER,DB_PASS);
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

//Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
}
?>