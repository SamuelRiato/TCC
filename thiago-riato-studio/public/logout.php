<?php
require __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid($_POST['csrf'] ?? null)) {
    logout_user();
    redirect('login.php');
}
redirect(current_user() ? 'inicio.php' : 'login.php');
