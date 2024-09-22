<?php 
include('../../database/db_connection.php'); 
include('../../login/protect.php'); 

// Excluir um produto
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php"); 
    exit();
}

// Listar produtos
$sql = "SELECT p.id, p.name, p.description, p.price, p.image, c.name AS category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .description-cell {
        max-width: 150px;
        /* Largura máxima para a descrição */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        /* Impede a quebra de linha */
    }

    .table td,
    .table th {
        vertical-align: middle;
        /* Centraliza o conteúdo verticalmente */
    }

    .action-btn {
        width: 100px;
        /* Largura fixa para os botões */
        margin-right: 5px;
        /* Espaçamento entre os botões */
    }

    @media (max-width: 576px) {
        .product-name {
            font-size: 0.8rem;
            /* Tamanho menor para o nome do produto em telas pequenas */
            white-space: nowrap;
            /* Impede a quebra de linha no nome */
            overflow: hidden;
            text-overflow: ellipsis;
            /* Adiciona reticências se o texto for longo */
        }
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
            <a href="create_and_update_product.php" class="btn btn-primary">Adicionar Novo Produto</a>
            <a href="/store/admin/dashboard.php" class="btn btn-secondary">Voltar para a Dashboard</a>
        </div>

        <h2 class="mb-4">Lista de Produtos</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td class="product-name"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td class="description-cell"><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>R$ <?php echo number_format($row['price'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                <a href="create_and_update_product.php?id=<?php echo $row['id']; ?>"
                                    class="btn btn-warning btn-sm action-btn mr-2">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="index.php?delete=<?php echo $row['id']; ?>"
                                    class="btn btn-danger btn-sm action-btn"
                                    onclick="return confirm('Tem certeza que deseja excluir este produto?');">
                                    <i class="fas fa-trash"></i> Excluir
                                </a>
                            </div>
                        </td>

                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Nenhum produto encontrado.</td>
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