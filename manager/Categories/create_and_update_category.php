<?php 
include('../../database/db_connection.php'); 
include('../../login/protect.php'); 

// Inicializa variáveis
$category_id = null;
$name = '';
$description = '';

// Verifica se está editando uma categoria
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
        $name = $category['name'];
        $description = $category['description'];
    } else {
        header("Location: index.php"); // Redireciona se a categoria não for encontrada
        exit();
    }
}

// Adicionar ou atualizar categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    if ($category_id) {
        // Atualiza a categoria
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $category_id);
    } else {
        // Adiciona uma nova categoria
        $stmt = $conn->prepare("INSERT INTO categories (name, description, active) VALUES (?, ?, 1)");
        $stmt->bind_param("ss", $name, $description);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: index.php"); // Redireciona após a operação
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $category_id ? 'Editar Categoria' : 'Adicionar Categoria'; ?></title>
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
        }
        .btn-secondary {
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <header class="d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
        <h1 class="h4">Minha Logo</h1>
        <a href="../../login/logout.php" class="btn btn-danger">Logout</a>
    </header>

    <div class="container">
        <h2><?php echo $category_id ? 'Editar Categoria' : 'Adicionar Categoria'; ?></h2>

        <form method="POST">
            <div class="form-group">
                <label for="name">Nome</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="d-flex">
                <button type="submit" class="btn btn-primary"><?php echo $category_id ? 'Atualizar Categoria' : 'Adicionar Categoria'; ?></button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
