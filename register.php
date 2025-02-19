<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Crittografia password
    $email = $_POST['email'];

    $conn = connectDB();

    // Verifica se l'utente esiste già
    $query = "SELECT * FROM Utenti WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = 'Username già preso!';
    } else {
        // Inserisci nuovo utente
        $query = "INSERT INTO Utenti (username, password, email) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $password, $email);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Registrazione riuscita! Puoi ora accedere.';
            header("Location: index.php");
            exit;
        } else {
            $_SESSION['error'] = 'Errore nella registrazione. Riprova.';
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
</head>
<body>
    <h1>Registrati</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Registrati</button>
    </form>

    <p>Hai già un account? <a href="index.php">Accedi qui</a></p>
</body>
</html>
