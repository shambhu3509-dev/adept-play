<?php require_once __DIR__.'/common/config.php'; session_unset(); session_destroy(); header('Location: login.php'); exit; ?>
