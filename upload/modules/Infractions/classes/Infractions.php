<?php

abstract class Infractions {
    protected array $_data;
    protected array $_db_details;
    protected Cache $_cache;
    protected ?DatabaseConnection $_db = null;
    protected Language $_language;

    public function __construct(array $inf_db, Language $language){
        $this->_db_details = $inf_db;
        $this->_language = $language;
        $this->_cache = new Cache(array('name' => 'nameless', 'extension' => '.cache', 'path' => ROOT_PATH . '/cache/infractions/'));
    }

    protected function initDB() {
        require_once ROOT_PATH . '/modules/Infractions/classes/Database/DatabaseConnection.php';

        $db_type = $this->_db_details['db_type'] ?? 'mariadb';

        if ($db_type === 'postgresql') {
            require_once ROOT_PATH . '/modules/Infractions/classes/Database/PostgreSQLConnection.php';
            $this->_db = new PostgreSQLConnection($this->_db_details);
        } else {
            require_once ROOT_PATH . '/modules/Infractions/classes/Database/MariaDBConnection.php';
            $this->_db = new MariaDBConnection($this->_db_details);
        }

        $this->_db->connect();
    }

    protected function date_compare($a, $b): int {
        if (!isset($a->created) || !isset($b->created)) {
            $a->created = $this->getCreationTime($a);
            $b->created = $this->getCreationTime($b);
        }

        if ($a->created == $b->created) return 0;
        return ($a->created < $b->created) ? 1 : -1;
    }

    abstract public function listInfractions(int $page, int $limit): array;
    abstract protected function getTotal(): int;
}