<?php
require __DIR__ . '/../src/bootstrap.php';
redirect(current_user() ? 'inicio.php' : 'login.php');
