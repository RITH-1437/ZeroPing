# Database

ZeroPing ships with a multi-driver database layer. **SQLite is the default** so a brand-new project works with zero configuration — no server, no credentials, no setup. When you grow, switch to MySQL, MariaDB, or PostgreSQL by editing a few environment variables. No application code changes.

Four drivers ship today:

| Driver | `DB_CONNECTION` | PDO extension | Notes |
|--------|------------------|---------------|-------|
| SQLite | `sqlite` | `pdo_sqlite` | Default. File-based or in-memory. Zero config. |
| MySQL | `mysql` | `pdo_mysql` | Recommended for production. |
| MariaDB | `mariadb` | `pdo_mysql` | MySQL-compatible; reuses the MySQL driver + grammar. |
| PostgreSQL | `pgsql` | `pdo_pgsql` | Uses `BIGSERIAL` surrogate keys. |

## How it is wired

The ORM, Query Builder, Schema, and Migrations never talk to a specific engine. They only ever talk to the `DatabaseManager`, which resolves a `Connection` (a PDO + a `DriverInterface` + a `Grammar`) through the `ConnectionFactory`.

```
App code → Database facade → DatabaseManager → ConnectionFactory → Driver + Grammar → PDO
```

To support a new engine (SQL Server, Oracle, …) you write **one** class that implements `DriverInterface` and register it:

```php
use App\Core\Database\Database;

Database::manager()->registerDriver('sqlserver', SqlServerDriver::class);
```

Nothing else in the framework has to change.

## Configuration

`config/database.php` holds the default connection and a `connections` map keyed by name. Every value is read from the environment so it stays out of version control.

```php
'default' => env('DB_CONNECTION', 'sqlite'),
```

### SQLite (default)

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

That is the entire configuration for a working application. With no `DB_DATABASE` set, ZeroPing falls back to `database/database.sqlite`. Use `DB_DATABASE=:memory:` for tests.

### MySQL / MariaDB

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zero_ping
DB_USERNAME=root
DB_PASSWORD=secret
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

MariaDB uses the identical block with `DB_CONNECTION=mariadb`.

### PostgreSQL

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=zero_ping
DB_USERNAME=postgres
DB_PASSWORD=secret
DB_CHARSET=utf8
DB_SCHEMA=public
```

## Switching drivers

Switching engines is a **pure configuration change**. Update `.env` and the migration tracking table is created for the new engine automatically:

```dotenv
DB_CONNECTION=mysql
```

No model, migration, or query-builder code needs to change. The `Grammar` compiled per connection emits the correct SQL (`AUTO_INCREMENT` for MySQL, `BIGSERIAL` for PostgreSQL, `AUTOINCREMENT` for SQLite).

## Schema & migrations

Schema definitions are engine-agnostic. The grammar decides the SQL:

```php
use App\Core\Database\Schema;

Schema::create('users', function ($table) {
    $table->id();
    $table->string('email', 255);
    $table->boolean('active')->default(true);
    $table->timestamps();
});

Schema::table('users', function ($table) {
    $table->string('phone', 100)->nullable();
});

Schema::dropIfExists('users');
```

Run them with the CLI:

```bash
php zero migrate        # run pending migrations
php zero migrate:rollback
php zero migrate:fresh  # drop everything and re-run
```

Migration SQL is driver-aware: the migration tracking table, `FOREIGN_KEY_CHECKS` toggling, and column add/drop statements are compiled by the active grammar.

## Models & the query builder

Models extend `App\Core\Database\Model` and only ever reach the database through the `DatabaseManager`.

```php
use App\Core\Database\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['email', 'active'];
}

$user = User::create(['email' => 'ada@example.com', 'active' => true]);
$found = User::where('active', true)->orderBy('email')->get();
$user->update(['active' => false]);
$user->delete();
```

## Testing

The testing traits are driver-aware. `RefreshDatabase` and `DatabaseAssertions` resolve the active connection and grammar, so the same tests run against SQLite in CI and MySQL locally.

```php
use App\Core\Testing\Database\RefreshDatabase;
use App\Core\Testing\Database\DatabaseAssertions;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseAssertions;

    public function test_it_stores_a_user(): void
    {
        User::create(['email' => 'ada@example.com']);

        $this->assertDatabaseHas('users', ['email' => 'ada@example.com']);
    }
}
```

In `phpunit.xml` you normally run against an in-memory SQLite database:

```xml
<php>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
</php>
```

## CLI: install & doctor

`php zero install` guides you through choosing a driver (SQLite, MySQL, MariaDB, or PostgreSQL) and validates the connection before finishing.

`php zero doctor` reports the active driver, the required PDO extension, and (for SQLite) whether the database file exists, or (for servers) whether the credentials connect.

## Common mistakes

- **Using MySQL syntax in SQLite.** Surrogate keys are `INTEGER PRIMARY KEY AUTOINCREMENT` in SQLite, not `AUTO_INCREMENT`. Define tables with `$table->id()` and let the grammar decide.
- **Forgetting the PDO extension.** Each driver needs its extension (`pdo_sqlite`, `pdo_mysql`, `pdo_pgsql`). `php zero doctor` tells you which one is missing.
- **Editing `config/database.php` with secrets.** Keep credentials in `.env`; the config file only reads them via `env()`.
- **Assuming MySQL and MariaDB differ.** They share a driver and grammar; pick either name in `DB_CONNECTION`.
