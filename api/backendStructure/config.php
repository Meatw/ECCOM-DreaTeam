<?php
// Application configuration
define('BASE_URL', 'http://localhost/quickbuy/');
define('API_URL', BASE_URL . 'api/');
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/quickbuy/uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

// JWT configuration
define('JWT_SECRET', '634a6b4f72f040323999dd70a6cb27004a38a60cac57b6246db6cf71f6c976f80160082fd9ac7888689e5240254294df1ac3b5514a302c85116b3d9f126a3ebd75fea27393ecb38df3e820f281648f02bf66340da52d50e64b249582fa6e3b0881e6694aa2f1d39b7963c04e6eec4680724f2454a4c43a5df8f2fd5d08a325249d79054d3b1c202a79f1c78c2ba07ef19f4309775f8b2fcd695e9ceea50169446669b82b543f86f65d65a286e9c7dadcdc2437c0fbb3dcdfd193675f0723e451eccd6735e49a9f4a625c160e75699eb74ab5957a5c6bf659d4d81a19fc8eb02d3c4963c69215207cb88681fd9dddcb5a3f9e27743c2095ec8afc125e94c9ddd7');
define('JWT_EXPIRATION', 3600); // 1 hour

// Email configuration
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@example.com');
define('SMTP_PASSWORD', 'your_email_password');
define('SMTP_FROM', 'noreply@quickbuy.com');
define('SMTP_FROM_NAME', 'QuickBuy Marketplace');
?>