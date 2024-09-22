<?php include('../database/db_connection.php'); ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $provider = isset($_POST['provider']) ? 1 : 0;
    $active = 1; // Usuário começa ativo

    // Usando PDO
    $sql = "INSERT INTO users (name, email, password, provider, active) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // Executando a consulta
    if ($stmt->execute([$name, $email, $password, $provider, $active])) {
        // Capturando o ID do usuário inserido
        $user_id = $conn->lastInsertId();

        // Registrando a atividade do usuário
        $activity_sql = "INSERT INTO user_activity (user_id, action, details) VALUES (?, ?, ?)";
        $activity_stmt = $conn->prepare($activity_sql);
        $activity_stmt->execute([$user_id, 'create', 'Usuário cadastrado.']);

        echo "<div class='alert alert-success'>Usuário cadastrado com sucesso!</div>";
        header("Location: login.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Erro ao cadastrar usuário: " . implode(", ", $stmt->errorInfo()) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Cadastro de Usuário</h4>
                    </div>
                    <div class="card-body">
                        <form action="register.php" method="POST">
                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Digite seu nome" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Senha</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="login.php">Já tem uma conta? Faça login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
