<?php 
session_start();
include('./database/db_connection.php'); 

// Consultar produtos com base na consulta de pesquisa
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Inicializa a variável de produtos
$products = [];

// Se houver uma consulta, realizar a busca
if ($query) {
    $sql = "SELECT products.*, users.name AS owner_name FROM products 
            JOIN users ON products.user_id = users.id 
            WHERE products.name LIKE ? OR products.description LIKE ? OR users.name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    // Se não houver consulta, pegar todos os produtos
    $sql = "SELECT products.*, users.name AS owner_name FROM products JOIN users ON products.user_id = users.id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Lista de Produtos</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .description {
            overflow: hidden; /* Oculta o texto que excede o limite */
            display: -webkit-box; /* Usa flexbox para criar uma caixa */
            -webkit-box-orient: vertical; /* Define a orientação vertical */
            -webkit-line-clamp: 4; /* Limita a 4 linhas */
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <header class="d-flex justify-content-between align-items-center p-3 bg-light border-bottom" style="width: 100%;">
            <h1 class="h4">Minha Logo</h1>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo ($_SESSION['provider'] == 1) ? 'admin/dashboard.php' : 'user/dashboard.php'; ?>" class="btn btn-secondary">Perfil</a>
                    <a href="login/logout.php" class="btn btn-danger">Logout</a>
                <?php else: ?>
                    <a href="login/login.php" class="btn btn-primary">Login</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="container">
            <!-- Campo de pesquisa -->
        <div class="mt-3 mb-4">
            <form class="form-inline" method="GET" action="">
                <div class="input-group w-100">
                    <input class="form-control" type="search" placeholder="Pesquisar produtos, categorias ou dono" aria-label="Pesquisar" name="query" value="<?= htmlspecialchars($query) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($query && empty($products)): ?>
            <div class="alert alert-danger text-center">
                <strong>Produto não encontrado!</strong> Tente outra busca.
            </div>
        <?php endif; ?>

        <div class="row mt-4">
            <?php if (empty($products) && !$query): ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        <strong>Nenhum produto cadastrado!</strong> Adicione novos produtos para visualizar aqui.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <?php if ($product['image']): ?>
                                <img src="./uploads/<?= htmlspecialchars(basename($product['image'])) ?>" class="card-img-top px-1 py-1" alt="<?= htmlspecialchars($product['name']) ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x200.png?text=Imagem+Indisponível" class="card-img-top" alt="Imagem Indisponível">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text description"><?= htmlspecialchars($product['description']) ?></p>
                                <p class="card-text"><strong>Preço:</strong> R$ <?= number_format($product['price'], 2, ',', '.') ?></p>
                                <p class="card-text"><small>Por: <?= htmlspecialchars($product['owner_name']) ?></small></p>
                                <a href="#" class="btn btn-primary">Ver Detalhes</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
