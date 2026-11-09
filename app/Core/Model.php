<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

/**
 * Modelo base: cada modelo representa una tabla y usa estas ayudas
 * para ejecutar consultas preparadas (los valores nunca se pegan al SQL).
 */
abstract class Model
{
    protected function db(): PDO
    {
        return Database::connection();
    }

    /** @param array<string, mixed> $params */
    protected function run(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->db()->prepare($sql);
        foreach ($params as $name => $value) {
            if (is_bool($value)) {
                $value = (int) $value;
            }
            $type = is_int($value) ? PDO::PARAM_INT : (is_null($value) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $statement->bindValue(':' . ltrim((string) $name, ':'), $value, $type);
        }
        $statement->execute();

        return $statement;
    }

    /**
     * @param array<string, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    protected function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>|null
     */
    protected function one(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @param array<string, mixed> $params
     * @return mixed
     */
    protected function value(string $sql, array $params = [])
    {
        return $this->run($sql, $params)->fetchColumn();
    }

    protected function lastId(): int
    {
        return (int) $this->db()->lastInsertId();
    }
}
