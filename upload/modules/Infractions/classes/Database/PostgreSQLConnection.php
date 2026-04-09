<?php

class PostgreSQLConnection implements DatabaseConnection {
    private ?PDO $_pdo = null;
    private ?PDOStatement $_statement = null;
    private array $_results = [];
    private int $_count = 0;
    private bool $_error = false;
    private array $_db_details;

    public function __construct(array $db_details) {
        $this->_db_details = $db_details;
    }

    public function connect(): void {
        $port = intval($this->_db_details['port'] ?? 5432);
        $dsn = 'pgsql:host=' . $this->_db_details['address'] . ';port=' . $port . ';dbname=' . $this->_db_details['name'];
        $this->_pdo = new PDO(
            $dsn,
            $this->_db_details['username'],
            $this->_db_details['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    }

    public function query(string $sql, array $params = [], ?bool $isSelect = null): static {
        $this->_error = false;
        $this->_results = [];
        $this->_count = 0;

        $this->_statement = $this->_pdo->prepare($sql);
        $x = 1;
        foreach ($params as $param) {
            if (is_bool($param)) {
                $param = $param ? 1 : 0;
            }
            $this->_statement->bindValue(
                $x,
                $param,
                is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
            $x++;
        }

        if ($this->_statement->execute()) {
            if ($isSelect || str_starts_with(strtoupper(ltrim($sql)), 'SELECT') || str_starts_with(strtoupper(ltrim($sql)), '(SELECT')) {
                $this->_results = $this->_statement->fetchAll(PDO::FETCH_OBJ);
            }
            $this->_count = $this->_statement->rowCount();
        } else {
            $this->_error = true;
        }

        return $this;
    }

    public function first(): ?object {
        return $this->_results[0] ?? null;
    }

    public function results(): array {
        return $this->_results;
    }

    public function count(): int {
        return $this->_count;
    }

    public function quoteIdentifier(string $identifier): string {
        return '"' . $identifier . '"';
    }

    public function getLimitClause(int $offset, int $limit): array {
        return ['LIMIT ? OFFSET ?', [$limit, $offset]];
    }
}