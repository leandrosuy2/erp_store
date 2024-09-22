<?php 
session_start();
include('../../../database/db_connection.php'); 
include('../../../login/protect.php'); 

// Inicializa a variável para armazenar as atividades
$activities = [];

// Verifica se o user_id foi passado pela URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Busca todas as atividades do usuário especificado, incluindo todos os campos necessários
    $sql = "SELECT ua.id, ua.user_id, ua.action, ua.details, ua.created_at, u.name AS user_name 
            FROM user_activity ua 
            JOIN users u ON ua.user_id = u.id 
            WHERE ua.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $activities = $result->fetch_all(MYSQLI_ASSOC);
} else {
    header("Location: index.php"); // Redireciona se user_id não estiver presente
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise de Atividades do Usuário</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        margin-top: 30px;
        padding: 30px;
        border-radius: 8px;
        background-color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        margin-bottom: 30px;
        text-align: center;
        color: #007bff;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f2f2f2;
    }

    .table-striped tbody tr:hover {
        background-color: #e2e6ea;
    }

    .btn-back {
        margin-top: 20px;
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <div class="container">
        <?php if (!empty($activities)): ?>
        <h2>Análise de Atividades de <?php echo htmlspecialchars($activities[0]['user_name']); ?></h2>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Ação</th>
                    <th>Detalhes</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity): ?>
                <tr>
                    <td><?php echo htmlspecialchars($activity['id']); ?></td>
                    <td><?php echo htmlspecialchars($activity['action']); ?></td>
                    <td><?php echo htmlspecialchars($activity['details']); ?></td>
                    <td><?php echo htmlspecialchars($activity['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <h2 class="text-center">Nenhuma atividade encontrada para este usuário.</h2>
        <?php endif; ?>
        <a href="../index.php" class="btn btn-secondary btn-back">Voltar</a>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>
