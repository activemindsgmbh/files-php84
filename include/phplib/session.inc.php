<?php
declare(strict_types=1);

require_once('mysql.inc.php');
require_once('stdlib.inc.php');

/**
 * Session handling class
 */
class Session 
{
    private static ?Session $instance = null;
    private bool $started = false;

    /**
     * Get singleton instance
     * @return Session
     */
    public static function getInstance(): Session 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor for singleton
     */
    private function __construct() 
    {
        $this->start();
    }

    /**
     * Start session
     * @return bool
     */
    public function start(): bool 
    {
        if (!$this->started) {
            $this->started = session_start();
        }
        return $this->started;
    }

    /**
     * Set session value
     * @param string $key Key
     * @param mixed $value Value
     * @return void
     */
    public function set(string $key, mixed $value): void 
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get session value
     * @param string $key Key
     * @param mixed $default Default value
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed 
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session key exists
     * @param string $key Key
     * @return bool
     */
    public function has(string $key): bool 
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session value
     * @param string $key Key
     * @return void
     */
    public function remove(string $key): void 
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy session
     * @return bool
     */
    public function destroy(): bool 
    {
        $this->started = false;
        return session_destroy();
    }
}