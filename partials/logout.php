<?php
require_once '../config/config.php';      // Add this line
require_once '../config/constants.php';    // Add this line

session_start();
session_destroy();

// Fix the URL concatenation
header("Location: " . SITE_URL . "/index.php");  // Added forward slash
exit();