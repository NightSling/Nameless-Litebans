<?php

interface DatabaseConnection {
    public function connect(): void;
    public function query(string $sql, array $params = [], ?bool $isSelect = null): static;
    public function first(): ?object;
    public function results(): array;
    public function count(): int;
    public function quoteIdentifier(string $identifier): string;
    public function getLimitClause(int $offset, int $limit): array;
}