<?php 
session_start();
include('../../database/db_connection.php'); 
include('../../login/protect.php'); 

// Excluir um usuário
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php"); 
    exit();
}

// Listar usuários
$sql = "SELECT * FROM users WHERE active = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .action-btn {
            width: 50px; /* Largura reduzida para os botões */
            margin-right: 5px; /* Espaçamento entre os botões */
        }
        .table-responsive {
            max-height: 400px; /* Ajuste conforme necessário */
            overflow-y: auto; /* Ativa a rolagem vertical */
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <header class="d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
            <h1 class="h4">Minha Logo</h1>
            <a href="../../login/logout.php" class="btn btn-danger">Logout</a>
        </header>

        <div class="d-flex justify-content-between mb-3 mt-3">
            <a href="create_and_update.php" class="btn btn-primary">Adicionar Novo Usuário</a>
            <a href="../../admin/dashboard.php" class="btn btn-secondary">Voltar para a Dashboard</a>
        </div>

        <h2 class="mb-4">Lista de Usuários</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                    <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['provider'] ? 'Provider' : 'Usuário'; ?></td>
                        <td><?php echo $user['active'] ? 'Ativo' : 'Inativo'; ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                <a href="create_and_update.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm action-btn">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?delete=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm action-btn"
                                    onclick="return confirm('Tem certeza que deseja excluir este usuário?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <a href="Analytics/index.php?user_id=<?php echo $user['id']; ?>" class="btn btn-info btn-sm action-btn">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Nenhum usuário encontrado.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
