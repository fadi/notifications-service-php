<?php

define('TESTING', true);

// Minimal env vars so requireEnv() in index.php doesn't throw
putenv('DB_DSN=sqlite::memory:');
putenv('DB_USER=test');
putenv('DB_PASS=test');
putenv('API_KEY=test-api-key-12345');

require_once __DIR__ . '/../index.php';
