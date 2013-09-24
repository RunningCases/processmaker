<?php

class PmSessionHandler implements SessionHandlerInterface
{
    private $savePath;
    private $db;
    private $rstmt;
    private $wstmt;
    private $dstmt;
    private $gcstmt;

    /**
     * $dsn Data source name for session DB Storage
     * @var string
     */
    private $dsn = '';

    private $user = '';
    private $password = '';

    private $dbtable = 'SESSION_STORAGE';

    /**
     * $httponly Session accessibility boolean key
     * By default the session cookie is not accessable via javascript. 
     * @var boolean
     */
    private $httponly = true;

    public function __construct($user, $password, $dsn)
    {
        $this->dbUser = $user;
        $this->dbPassword = $password;
        $this->dsn = $dsn;

        session_set_save_handler($this, true);
 
        // This line prevents unexpected effects when using objects as save handlers.
        register_shutdown_function('session_write_close');

        error_log(" * Session: a new instance was created!!");
    }

    function start_session($sessionName, $secure)
    {

        // Hash algorithm to use for the sessionid. (use hash_algos() to get a list of available hashes.)
        $sessionHash = 'sha512';

        // Check if hash is available
        if (in_array($sessionHash, hash_algos())) {
          // Set the has function.
          ini_set('session.hash_function', $sessionHash);
        }
        // How many bits per character of the hash.
        // The possible values are '4' (0-9, a-f), '5' (0-9, a-v), and '6' (0-9, a-z, A-Z, "-", ",").
        ini_set('session.hash_bits_per_character', 5);

        // Force the session to only use cookies, not URL variables.
        ini_set('session.use_only_cookies', 1);

        // Get session cookie parameters 
        $cookieParams = session_get_cookie_params(); 
        // Set the parameters
        session_set_cookie_params(
            $cookieParams["lifetime"], 
            $cookieParams["path"], 
            $cookieParams["domain"], 
            $secure, 
            $httponly
        ); 
        // Change the session name 
        session_name($sessionName);
        // Now we cat start the session
        session_start();
        // This line regenerates the session and delete the old one. 
        // It also generates a new encryption key in the database. 
        session_regenerate_id(true);

        error_log(" * Session: start_session was executed!!");
    }

    public function open($savePath, $sessionName)
    {
        $this->db = new PDO(
            $this->dsn, // 
            $this->dbUser, 
            $this->dbPassword,
            array(
                /*
                 * The web applications will benefit from making persistent connections to database servers. 
                 * Persistent connections are not closed at the end of the script, but are cached and re-used 
                 * when another script requests a connection using the same credentials. 
                 * The persistent connection cache allows you to avoid the overhead of establishing a new connection 
                 * every time a script needs to talk to a database, resulting in a faster web application.    
                 */
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            )
        );

        error_log(" * Session: open was executed!!");

        return true;
    }


    public function close()
    {
        // Upon successful connection to the database, an instance of the PDO class is returned 
        // to your script. The connection remains active for the lifetime of that PDO object. 
        // To close the connection, you need to destroy the object by ensuring that all 
        // remaining references to it are deleted--you do this by assigning NULL to the variable 
        // that holds the object. If you don't do this explicitly, PHP will automatically 
        // close the connection when your script ends.
        $this->db = null;

        error_log(" * Session: close was executed!!");

        return true;
    }

    public function write($id, $data)
    {
        $time = time();
        
        if(! isset($this->wstmt)) {
            $sql = "REPLACE INTO {$this->dbtable} (ID, SET_TIME, DATA, SESSION_KEY) VALUES (?, ?, ?, ?)";
            $this->wstmt = $this->db->prepare($sql);
        }

        $key = 'K' . rand();
        $data = base64_encode(serialize($data));

        //$this->wstmt->bind_param('siss', $id, $time, $data, $key);
        $this->wstmt->execute(array($id, $time, $data, $key));

        error_log(" * Session: write was executed!!");

        return true;
    }

    public function read($id)
    {
        if(! isset($this->rstmt)) {
            $this->rstmt = $this->db->prepare("SELECT DATA FROM {$this->dbtable} WHERE ID = ? LIMIT 1");
        }

        $this->rstmt->execute(array($id));
        $data = $this->rstmt->fetch();
        $data = unserialize(base64_decode($data['DATA']));

        error_log(" * Session: read was executed!!");

        return $data;
    }

    public function destroy($id)
    {
        if(! isset($this->dstmt)) {
            $this->dstmt = $this->db->prepare("DELETE FROM {$this->dbtable} WHERE ID = ?");
        }

        $this->dstmt->execute(array($id));

        error_log(" * Session: destroy was executed!!");

        return true;
    }

    public function gc($maxlifetime)
    {
        $time = time() - $maxlifetime;

        if(! isset($this->gcstmt)) {
            $thi->gcstmt = $this->db->prepare("DELETE FROM {$this->dbtable} WHERE SET_TIME < ?");
        }

        $thi->gcstmt->execute(array($time));

        error_log(" * Session: gc was executed!!");

        return true;
    }
}