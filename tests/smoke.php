<?php
declare(strict_types=1);

putenv('DB_DRIVER=sqlite');
putenv('DB_PATH=' . sys_get_temp_dir() . '/pms-smoke-' . getmypid() . '.sqlite');
require dirname(__DIR__) . '/database/seed.php';

$pdo = Database::connection();
$repository = new CustomerRepository($pdo);

assert(Auth::attempt('admin@example.com', 'ChangeMe123!') === true);
assert(Auth::check() === true);
assert(Csrf::verify(Csrf::token()) === true);

$email = 'smoke-' . getmypid() . '@example.com';
$repository->create(['name' => 'Smoke Test', 'email' => $email, 'phone' => '03-0000-0000', 'company' => 'Test']);
$id = (int) $pdo->lastInsertId();
$created = $repository->find($id);
assert($created !== null && $created['email'] === $email);

$searchResult = $repository->paginate('Smoke', 1);
assert($searchResult['total'] === 1);
assert($searchResult['items'][0]['id'] === $id);

$repository->update($id, ['name' => 'Updated', 'email' => $email, 'phone' => '', 'company' => 'Updated Company']);
assert($repository->find($id)['name'] === 'Updated');
assert($repository->paginate('Updated', 1)['total'] === 1);

assert($repository->delete($id) === true);
assert($repository->find($id) === null);

$path = (string) config('database.sqlite_path');
if (is_file($path)) {
    unlink($path);
}
echo "Smoke test passed.\n";
