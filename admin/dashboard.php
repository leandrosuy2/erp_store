<?php 
include('../database/db_connection.php'); 
include('../login/protect.php'); // Verifica se o usuário está logado e é provider e active

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="container-fluid">
        <header class="d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
            <h1 class="h4">Minha Logo</h1>
            <a href="dashboard.php?logout=true" class="btn btn-danger">Logout</a>
        </header>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-4">
                    <div class="card-body">
                        <i class="fas fa-box fa-2x"></i>
                        <h5 class="card-title">Produtos</h5>
                        <p class="card-text">Gerencie seus produtos aqui.</p>
                        <a href="../manager/Products/index.php" class="btn btn-light">Ver Produtos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-4">
                    <div class="card-body">
                        <i class="fas fa-tags fa-2x"></i>
                        <h5 class="card-title">Categorias</h5>
                        <p class="card-text">Gerencie suas categorias aqui.</p>
                        <a href="../manager/Categories/index.php" class="btn btn-light">Ver Categorias</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-4">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x"></i>
                        <h5 class="card-title">Usuários</h5>
                        <p class="card-text">Gerencie os usuários aqui.</p>
                        <a href="../manager/Users/index.php" class="btn btn-light">Ver Usuários</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-4">
                    <div class="card-body">
                        <i class="fas fa-chart-line fa-2x"></i>
                        <h5 class="card-title">Usuários Analytics</h5>
                        <p class="card-text">Veja as análises dos usuários.</p>
                        <a href="../manager/Users/Analytics/index.php" class="btn btn-light">Ver Análises</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
