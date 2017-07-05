<?php

// Custom Session Handler Class
class MySessionHandler extends SessionHandler {
    // Default Session Parameters
    private $sessionName = "MYSESSID";          // Session Name
    private $sessionLifeTime = 0;               // Session Expire
    private $sessionSSL = false;                // SSL
    private $sessionHTTPOnly = true;            // HTTP Only
    private $sessionPath = "/";                 // Path
    private $sessionDomain = "";                // Domain
    private $sessionSavePath;                   // Session Path

    // MCRYPT Extension Parameters
    private $CipherAlgo = MCRYPT_BLOWFISH;
    private $CipherMode = MCRYPT_MODE_ECB;
    private $CipherKey = "WYCRYPT0K3Y0H3LL";

    // Session Life Time in Minutes
    private $ttl = 1;

    public function __construct() {
        // Initialize Sessions' Save Path
        $this->sessionSavePath = dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR . 'sessions';

        // Initialize PHP.ini Settings For Session
        ini_set("session.use_cookies", 1);
        ini_set("session.use_only_cookies", 1);
        ini_set("session.use_trans_sid", 0);
        ini_set("session.save_handler", 'files');

        // Session Settings
        session_name($this->sessionName);
        session_save_path($this->sessionSavePath);
        // Set Session Cookie Parameters
        session_set_cookie_params(
            $this->sessionLifeTime, $this->sessionPath,
            $this->sessionDomain, $this->sessionSSL,
            $this->sessionHTTPOnly
        );
        // Make $this (Object) as a Session Handler
        session_set_save_handler($this, true);
    }

    // Get Session Data
    public function __get($key) {
        return false !== $_SESSION[$key] ? $_SESSION[$key] : false;
    }

    // Set Session Data
    public function __set($key, $value) {
        $_SESSION[$key] = $value;
    }

    // Check if Session data exists
    public function __isset($key) {
        return $_SESSION[$key] ? true : false;
    }

    // Encrypt & Write Session Data
    public function write($id, $data) {
        // Encrypt Session Data
        $encryptedData = mcrypt_encrypt($this->CipherAlgo, $this->CipherKey, $data, $this->CipherMode);
        return parent::write($id, $encryptedData);
    }

    // Decrypt & Read Session Data
    public function read($id) {
        // Decrypt Session Data
        $decryptedData = mcrypt_decrypt($this->CipherAlgo, $this->CipherKey, parent::read($id), $this->CipherMode);
        return $decryptedData;
    }

    // Start Session
    public function start() {
        if ('' == session_id()) {
            if (session_start()) {
                $this->setSessionStartTime();
                $this->checkSessionValidity();
            }
        }
    }

    // Set Session Start Time = Current Time.
    public function setSessionStartTime() {
        if (!isset($this->sessionStartTime)) {
            $this->sessionStartTime = time();
        }
        return true;
    }

    /*
     * Check session start time,
     * generate a new session id
     * and finger print if session time expired.
    */
    public function checkSessionValidity() {
        if ((time() - $this->sessionStartTime) > ($this->ttl * 60)) {
            $this->renewSession();
            $this->generateFingerPrint();
        }
        return true;
    }

    // Regenerate a New Session ID
    public function renewSession() {
        $this->sessionStartTime = time();
        return session_regenerate_id(true);
    }

    // Kill the session and clear cookies
    public function kill() {
        session_unset();
        setcookie(
            $this->sessionName, '', time() - 1000,
            $this->sessionPath, $this->sessionDomain,
            $this->sessionSSL, $this->sessionHTTPOnly
        );
        session_destroy();
    }

    /**
     * Session Security Enhancement
     * Generate unique finger print for every session
     * to prevent session hijacking attack
     */
    public function generateFingerPrint() {
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->sessionId = session_id();
        $this->cipherKey = mcrypt_create_iv(32);
        $this->fingerPrint = sha1($this->userAgent . $this->cipherKey . $this->sessionId);
    }

    /**
     * Check session validity
     * @return bool
     * TRUE on success, FALSE on failure.
     */
    public function isValidFingerPrint() {
        // Generate finger print if not exists
        if (!isset($this->fingerPrint)) {
            $this->generateFingerPrint();
        }
        // Check finger print validity
        $fingerPrint = sha1($this->userAgent . $this->cipherKey . session_id());
        if ($fingerPrint === $this->fingerPrint) {
            return true;
        }
        return false;
    }
}

?>
