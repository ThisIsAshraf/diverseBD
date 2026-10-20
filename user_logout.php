<?php
require_once __DIR__ . '/app/bootstrap.php';

unset($_SESSION['user_id']);
flash('success', 'You have been logged out successfully.');
redirect('index.php');
