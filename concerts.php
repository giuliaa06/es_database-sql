<?php
session_start();
require_once 'config.php';

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Connessione al database
function connectDB() {
    global $db;
    $connection = new mysqli($db['host'], $db['username'], $db['password'], $db['database']);
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    return $connection;
}

// Recupera concerti
function getConcerts() {
    $conn = connectDB();
    $query = "SELECT * FROM Concerti";
    $result = $conn->query($query);
    $concerts = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $concerts[] = $row;
        }
    }
    $conn->close();
    return $concerts;
}

// Aggiungi concerto ai preferiti
if (isset($_POST['concert_like'])) {
    $concert_id = $_POST['concert_id'];
    $user_id = $_SESSION['user_id'];

    $conn = connectDB();
    $query = "INSERT INTO Concerti_Piaciuti (user_id, concert_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $concert_id);
    $stmt->execute();
    $conn->close();
}

$concerts = getConcerts();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Concerti</title>
</head>
<body>
    <h1>Benvenuto, <?php echo $_SESSION['username']; ?>!</h1>
    <h2>I Concerti Disponibili</h2>

    <table>
        <tr>
            <th>Data</th>
            <th>Titolo</th>
            <th>Descrizione</th>
            <th>Azioni</th>
        </tr>
        <?php foreach ($concerts as $concert): ?>
            <tr>
                <td><?php echo $concert['data']; ?></td>
                <td><?php echo $concert['titolo']; ?></td>
                <td><?php echo $concert['descrizione']; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="concert_id" value="<?php echo $concert['id']; ?>">
                        <button type="submit" name="concert_like">Aggiungi ai preferiti</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="logout.php">Esci</a>
</body>
</html>
