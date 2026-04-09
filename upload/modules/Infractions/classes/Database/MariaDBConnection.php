<?php

class MariaDBConnection implements DatabaseConnection {
    private ?DB $_db = null;
    private array $_db_details;

    public function __construct(array $db_details) {
        $this->_db_details = $db_details;
    }

    public function connect(): void {
        $this->_db = DB::getCustomInstance(
            $this->_db_details['address'],
            $this->_db_details['name'],
            $this->_db_details['username'],
            $this->_db_details['password'],
            intval($this->_db_details['port'] ?? 3306),
            null,
            null,
            ''
        );
    }

    public function query(string $sql, array $params = [], ?bool $isSelect = null): static {
        $this->_db->query($sql, $params, $isSelect);
        return $this;
    }

    public function first(): ?object {
        return $this->_db->first();
    }

    public function results(): array {
        return $this->_db->results();
    }

    public function count(): int {
        return $this->_db->count();
    }

    public function quoteIdentifier(string $identifier): string {
        return '`' . $identifier . '`';
    }

    public function getLimitClause(int $offset, int $limit): array {
        return ['LIMIT ?,?', [$offset, $limit]];
    }
}