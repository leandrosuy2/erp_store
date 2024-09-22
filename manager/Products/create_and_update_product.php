<?php 
include('../../database/db_connection.php'); 
include('../../login/protect.php'); 

// Inicializa variáveis
$product_id = null;
$name = '';
$description = '';
$price = '';
$category_id = '';
$image = '';

// Verifica se está editando um produto
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $name = $product['name'];
        $description = $product['description'];
        $price = $product['price'];
        $category_id = $product['category_id'];
        $image = $product['image']; // Obtém a imagem do produto
    } else {
        header("Location: index.php"); // Redireciona se o produto não for encontrado
        exit();
    }
}

// Adicionar ou atualizar produto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Verifica se a pasta de uploads existe, caso contrário, cria
$uploadDir = '../uploads/'; // Ajusta para apontar para a raiz do projeto
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true); // Cria a pasta com permissões adequadas
}

// Verifica se uma nova imagem foi enviada
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    // Define o caminho do arquivo
    $image = $uploadDir . basename($_FILES['image']['name']);
    // Move o arquivo para o diretório de uploads
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
        echo "Erro ao fazer upload da imagem.";
    }
} else if ($product_id) {
    $image = $product['image']; // Mantém a imagem atual se não foi enviada nova
}

    if ($product_id) {
        // Atualiza o produto
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssissi", $name, $description, $price, $category_id, $image, $product_id);
    } else {
        // Adiciona um novo produto
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssi", $name, $description, $price, $category_id, $image, $_SESSION['user_id']);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: index.php"); // Redireciona após a operação
    exit();
}

// Obter categorias
$sql = "SELECT * FROM categories WHERE active = 1";
$categories = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product_id ? 'Editar Produto' : 'Adicionar Produto'; ?></title>
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
    </style>
</head>

<body>
    <div class="container-fluid">
        <header class="d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
            <h1 class="h4">Minha Logo</h1>
            <a href="../../login/logout.php" class="btn btn-danger">Logout</a>
        </header>

        <div class="container">
            <h2><?php echo $product_id ? 'Editar Produto' : 'Adicionar Produto'; ?></h2>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea class="form-control" id="description" name="description"
                        required><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Preço</label>
                    <input type="number" class="form-control" id="price" name="price"
                        value="<?php echo htmlspecialchars($price); ?>" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="category_id">Categoria</label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php while ($row = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"
                            <?php echo ($row['id'] == $category_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="image">Imagem</label>
                    <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                    <img id="image-preview" alt="Preview da Imagem" class="img-thumbnail mt-2"
                        style="max-width: 200px; display: none;">
                    <?php if ($product_id && $image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Imagem do Produto"
                        class="img-thumbnail mt-2" style="max-width: 200px;">
                    <?php endif; ?>
                </div>
                <button type="submit"
                    class="btn btn-primary"><?php echo $product_id ? 'Atualizar Produto' : 'Adicionar Produto'; ?></button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    document.getElementById('image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgPreview = document.getElementById('image-preview');
                imgPreview.src = e.target.result;
                imgPreview.style.display = 'block'; // Mostra a imagem
            };
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>

</html>

<?php
$conn->close();
?>