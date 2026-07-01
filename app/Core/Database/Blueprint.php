<?php

namespace App\Core\Database;

class Blueprint
{
    protected string $table;

    protected array $columns = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function id(): static
    {
        $this->columns[] =
            "id INT AUTO_INCREMENT PRIMARY KEY";

        return $this;
    }

    public function string(
        string $name,
        int $length = 255
    ): static {

        $this->columns[] =
            "{$name} VARCHAR({$length})";

        return $this;
    }

    public function integer(string $name): static
    {
        $this->columns[] =
            "{$name} INT";

        return $this;
    }

    public function boolean(string $name): static
    {
        $this->columns[] =
            "{$name} TINYINT(1)";

        return $this;
    }

    public function text(string $name): static
    {
        $this->columns[] =
            "{$name} TEXT";

        return $this;
    }

    public function timestamps(): static
    {
        $this->columns[] =
            "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";

        $this->columns[] =
            "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";

        return $this;
    }

    public function build(): string
    {
        return implode(",\n", $this->columns);
    }
}