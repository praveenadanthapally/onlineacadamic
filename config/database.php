<?php
/**
 * Database Configuration
 * Uses PDO for secure database connections
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'online_academic');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get database connection
 * @return PDO
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci"
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }
    
    return $pdo;
}

/**
 * Execute a prepared statement
 * @param string $sql
 * @param array $params
 * @return PDOStatement
 */
function executeQuery($sql, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch single row
 * @param string $sql
 * @param array $params
 * @return array|false
 */
function fetchOne($sql, $params = []) {
    return executeQuery($sql, $params)->fetch();
}

/**
 * Fetch all rows
 * @param string $sql
 * @param array $params
 * @return array
 */
function fetchAll($sql, $params = []) {
    return executeQuery($sql, $params)->fetchAll();
}

/**
 * Get last inserted ID
 * @return string
 */
function getLastInsertId() {
    return getDBConnection()->lastInsertId();
}

/**
 * Begin transaction
 */
function beginTransaction() {
    getDBConnection()->beginTransaction();
}

/**
 * Commit transaction
 */
function commitTransaction() {
    getDBConnection()->commit();
}

/**
 * Rollback transaction
 */
function rollbackTransaction() {
    getDBConnection()->rollBack();
}
