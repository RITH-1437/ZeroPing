<?php

declare(strict_types=1);

namespace App\Core\Database;

/**
 * Validates SQL identifiers and the finite set of operators emitted by the
 * query builder. PDO placeholders cannot bind identifiers, so validating them
 * before interpolation is required to keep dynamic queries safe.
 */
final class Identifier
{
    /** @var list<string> */
    private const OPERATORS = ['=', '!=', '<>', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE'];

    /** @var list<string> */
    private const JOIN_TYPES = ['INNER', 'LEFT', 'RIGHT'];

    public static function table(string $identifier): string
    {
        return self::qualified($identifier, false);
    }

    public static function column(string $identifier, bool $allowWildcard = false): string
    {
        return self::qualified($identifier, $allowWildcard);
    }

    public static function operator(string $operator): string
    {
        $operator = strtoupper(trim($operator));

        if (!in_array($operator, self::OPERATORS, true)) {
            throw new \InvalidArgumentException("Unsupported SQL operator: {$operator}");
        }

        return $operator;
    }

    public static function joinType(string $type): string
    {
        $type = strtoupper(trim($type));

        if (!in_array($type, self::JOIN_TYPES, true)) {
            throw new \InvalidArgumentException("Unsupported SQL join type: {$type}");
        }

        return $type;
    }

    private static function qualified(string $identifier, bool $allowWildcard): string
    {
        $identifier = trim($identifier);
        $parts = explode('.', $identifier);

        if ($identifier === '') {
            throw new \InvalidArgumentException('SQL identifier must not be empty.');
        }

        foreach ($parts as $index => $part) {
            $isWildcard = $allowWildcard && $part === '*' && $index === count($parts) - 1;
            if ($isWildcard) {
                continue;
            }

            if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $part) !== 1) {
                throw new \InvalidArgumentException("Invalid SQL identifier: {$identifier}");
            }
        }

        return implode('.', $parts);
    }
}
