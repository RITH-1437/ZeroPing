<?php
/**
 * ZeroPing performance benchmark (before/after).
 *
 * Exercises hot internal paths without touching a database:
 *  - Container::make (reflection)
 *  - ConfigRepository::get (key lookup)
 *  - CacheRepository (array driver) get/put
 *  - Router dynamic dispatch (regex compile + match)
 *  - Env::load parse
 *  - SessionGuard access
 *  - ORM attribute access (HasAttributes)
 *
 * Usage: php bench/run.php
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Container\Container;
use App\Core\Config\ConfigRepository;
use App\Core\Cache\CacheRepository;
use App\Core\Cache\Drivers\ArrayCacheDriver;
use App\Core\Auth\SessionGuard;
use App\Core\Config\Env;
use App\Core\Routing\Router;
use App\Core\ORM\Concerns\HasAttributes;

if (!function_exists('hrtime')) {
    function hrtime(bool $ns = true) { return $ns ? microtime(true) * 1e9 : microtime(true); }
}

function bench(string $label, int $iters, callable $fn): array
{
    // warmup
    for ($i = 0; $i < 5; $i++) $fn();
    $start = hrtime(true);
    for ($i = 0; $i < $iters; $i++) {
        $fn();
    }
    $end = hrtime(true);
    $ms = ($end - $start) / 1e6;
    return [$label, $iters, round($ms, 2), round($ms / $iters, 4)];
}

function fmt(array $r): string
{
    [$label, $iters, $ms, $per] = $r;
    return sprintf("%-34s %8d iters %10.2f ms %12.4f ms/op", $label, $iters, $ms, $per);
}

/* ---------- 1. Container reflection ---------- */
class BenchDepA {}
class BenchDepB { public function __construct(BenchDepA $a) {} }
class BenchDepC { public function __construct(BenchDepB $b, BenchDepA $a) {} }

$c = new Container();
$rows[] = bench('Container::make(BenchDepC)', 20000, fn() => $c->make(BenchDepC::class));

/* ---------- 2. Config repository ---------- */
$cfg = new ConfigRepository();
$cfg->set([
    'app' => ['name' => 'ZeroPing', 'providers' => array_fill(0, 30, 'X')],
    'db'  => ['host' => 'localhost', 'connections' => ['a' => 1, 'b' => 2]],
    'cache' => ['stores' => ['file' => ['driver' => 'file']]],
]);
$rows[] = bench('ConfigRepository::get (nested)', 50000, fn() => $cfg->get('cache.stores.file.driver'));

/* ---------- 3. Cache repository (array) ---------- */
$repo = new CacheRepository(new ArrayCacheDriver());
$repo->put('k', 'v', 60);
$rows[] = bench('CacheRepository::get (array)', 50000, fn() => $repo->get('k'));

/* ---------- 4. Router dynamic dispatch ---------- */
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
for ($i = 0; $i < 25; $i++) {
    Router::get("/resource{$i}/{id}", fn($id) => $id);
}
Router::get('/exact', fn() => 'ok');
$rows[] = bench('Router::dispatch (dynamic)', 20000, function () {
    $_SERVER['REQUEST_URI'] = '/resource12/42';
    Router::dispatch(sys_get_temp_dir());
});

/* ---------- 5. Env::load parse ---------- */
$envFile = sys_get_temp_dir() . '/bench_env_' . getmypid() . '.env';
file_put_contents($envFile, "APP_NAME=ZeroPing\nAPP_ENV=local\nAPP_DEBUG=true\nDB_HOST=127.0.0.1\nDB_NAME=db\nDB_USER=root\nDB_PASS=secret\n");
// Force re-parse by clearing cache between iterations is skipped; steady-state reflects caching.
$rows[] = bench('Env::load (parse/.env)', 5000, fn() => Env::load($envFile));
@unlink($envFile);

/* ---------- 6. SessionGuard access ---------- */
$_SESSION = [];
$rows[] = bench('SessionGuard::get', 50000, fn() => SessionGuard::get('foo', 'def'));

/* ---------- 7. ORM attribute access ---------- */
class BenchModel
{
    use HasAttributes;
    public function __construct(array $attrs = [])
    {
        $this->attributes = $attrs;
        $this->casts = ['age' => 'int', 'name' => 'string', 'meta' => 'array'];
    }
    public function getAgeAttribute($v) { return (int) $v; }
}
$m = new BenchModel(['age' => '30', 'name' => 'Ada', 'meta' => '{"x":1}']);
$rows[] = bench('ORM __get (casts+accessor)', 100000, fn() => [$m->age, $m->name, $m->meta]);

/* ---------- report ---------- */
echo "=== ZeroPing benchmark ===\n";
foreach ($rows as $r) {
    echo fmt($r) . "\n";
}
echo "peak memory: " . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . " MB\n";
