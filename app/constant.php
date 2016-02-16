<?php
// absolute filesystem path to the web root
define('WWW_DIR', dirname(__DIR__));

// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/app');

// absolute filesystem path to the asset root
define('ASSET_DIR', WWW_DIR . '/asset');

// absolute filesystem path to the temporary files
define('TEMP_DIR', WWW_DIR. '/temp');

// absolute filesystem path to the log files
define('LOG_DIR', WWW_DIR . '/log');

// absolute filesystem path to the tracy log files
define('TRACY_DIR', LOG_DIR . '/tracy');

// absolute filesystem path to the storage root
define('STORAGE_DIR', WWW_DIR . '/storage');

// IP adresa vyvojaru
define('REMOTE_IP', '127.0.0.1');

// IP adresa serveru
define('SERVER_IP', '127.0.0.1');

// BUGREPORT mail
define('BUGREPORT_MAIL', '');

// admin mail
define('ADMIN_MAIL', '');

// backup dir
define('BACKUP_DIR', '');
