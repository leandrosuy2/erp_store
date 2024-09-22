<?php 
include('../../database/db_connection.php'); 
include('../../login/protect.php'); 

// Inicializa variáveis
$user_id = null;
$name = '';
$email = '';
$is_provider = 0; // Valor padrão para não provider
$hashed_password = ''; // Inicializa a variável de senha

// Verifica se está editando um usuário
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $name = $user['name'];
        $email = $user['email'];
        $is_provider = $user['provider']; // Armazena se é provider
        $hashed_password = $user['password']; // Captura a senha atual
    } else {
        header("Location: index.php"); // Redireciona se o usuário não for encontrado
        exit();
    }
}

// Adicionar ou atualizar usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_is_provider = isset($_POST['is_provider']) ? 1 : 0; // 1 é "Sim", 0 "Não"

    // Debugging: verificar valores
    var_dump($new_name, $new_email, $new_is_provider);

    // Verifica se uma nova senha foi fornecida e faz o hash
    $new_password = $_POST['password'];
    $hashed_password = !empty($new_password) ? password_hash($new_password, PASSWORD_DEFAULT) : $hashed_password;

    // Usando PDO para atualizar ou inserir o usuário
    if ($user_id) {
        // Atualiza o usuário
        $sql = "UPDATE users SET name = ?, email = ?, provider = ?" . (!empty($new_password) ? ", password = ?" : "") . " WHERE id = ?";
        $stmt = $conn->prepare($sql);

        // Debugging: verificar SQL
        var_dump($sql);

        if (!empty($new_password)) {
            $stmt->execute([$new_name, $new_email, $new_is_provider, $hashed_password, $user_id]);
        } else {
            $stmt->execute([$new_name, $new_email, $new_is_provider, $user_id]);
        }

        // Captura os detalhes da atualização, verificando quais campos mudaram
        $details = [];
        if ($name !== $new_name) {
            $details[] = "Nome: de '$name' para '$new_name'";
        }
        if ($email !== $new_email) {
            $details[] = "Email: de '$email' para '$new_email'";
        }
        if ($is_provider !== $new_is_provider) {
            $details[] = "Provider: de " . ($is_provider ? 'Sim' : 'Não') . " para " . ($new_is_provider ? 'Sim' : 'Não');
        }
        if (!empty($new_password)) {
            $details[] = "Senha: alterada";
        }
        
        $details_string = implode(", ", $details);
        
    } else {
        // Adiciona um novo usuário
        $active = 1; // Usuário começa ativo
        $sql = "INSERT INTO users (name, email, password, provider, active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$new_name, $new_email, $hashed_password, $new_is_provider, $active]);

        // Captura os detalhes da adição
        $details_string = "Adicionado: Nome = $new_name, Email = $new_email, Provider = " . ($new_is_provider ? 'Sim' : 'Não');
    }

    // Insere a atividade no banco
    $activity_sql = "INSERT INTO user_activity (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())";
    $activity_stmt = $conn->prepare($activity_sql);
    $action = $user_id ? 'Atualização' : 'Criação';
    $activity_stmt->execute([$user_id ?? $stmt->insert_id, $action, $details_string]);

    header("Location: index.php"); // Redireciona após a operação
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $user_id ? 'Editar Usuário' : 'Adicionar Usuário'; ?></title>
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
            <h2><?php echo $user_id ? 'Editar Usuário' : 'Adicionar Usuário'; ?></h2>
            <form method="POST">
                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="<?php echo $user_id ? 'Deixe em branco para manter a mesma senha' : 'Digite sua senha'; ?>">
                </div>
                <div class="form-group">
                    <label for="is_provider">É Provider?</label>
                    <select class="form-control" id="is_provider" name="is_provider">
                        <option value="0" <?php echo $is_provider == 0 ? 'selected' : ''; ?>>Não</option>
                        <option value="1" <?php echo $is_provider == 1 ? 'selected' : ''; ?>>Sim</option>
                    </select>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary"><?php echo $user_id ? 'Atualizar Usuário' : 'Adicionar Usuário'; ?></button>
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
