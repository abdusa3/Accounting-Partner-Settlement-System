<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
start_session();
session_destroy();
header('Location: login.php');
