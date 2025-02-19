<?php
session_start();
require_once 'config.php';

// Controlla se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = connectDB();

    // Verifica se l'utente esiste
    $query = "SELECT * FROM Utenti WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verifica la password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['error'] = 'Password errata!';
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['error'] = 'Utente non trovato!';
        header("Location: index.php");
        exit;
    }

    $conn->close();
}
?>
