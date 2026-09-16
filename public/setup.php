<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

$error = null;
$completed = false;

try {
    $pdo = Database::connection();
    $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($userCount > 0) {
        http_response_code(410);
        exit('Initial setup is already complete.');
    }
} catch (Throwable) {
    http_response_code(500);
    exit('Database connection or schema setup is incomplete.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::requireValid($_POST['_token'] ?? null);

    $configuredKey = (string) config('install.key', '');
    $submittedKey = (string) ($_POST['install_key'] ?? '');
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if ($configuredKey === '' || str_starts_with($configuredKey, 'CHANGE_ME')) {
        $error = 'config.phpのインストールキーを変更してください。';
    } elseif (!hash_equals($configuredKey, $submittedKey)) {
        $error = 'インストールキーが正しくありません。';
    } elseif ($name === '' || mb_strlen($name) > 100) {
        $error = '管理者名を1〜100文字で入力してください。';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '有効なメールアドレスを入力してください。';
    } elseif (mb_strlen($password) < 12) {
        $error = 'パスワードは12文字以上で入力してください。';
    } else {
        $statement = $pdo->prepare(
            'INSERT INTO users (name, email, password_hash, created_at)
             VALUES (:name, :email, :password_hash, CURRENT_TIMESTAMP)'
        );
        $statement->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        if (isset($_POST['sample_data'])) {
            $repository = new CustomerRepository($pdo);
            $repository->create([
                'name' => '山田 太郎',
                'email' => 'taro.yamada@example.com',
                'phone' => '03-1234-5678',
                'company' => '株式会社サンプル',
            ]);
            $repository->create([
                'name' => '佐藤 花子',
                'email' => 'hanako.sato@example.com',
                'phone' => '06-2345-6789',
                'company' => 'テスト商事',
            ]);
        }

        $completed = true;
    }
}

render_header('初期セットアップ');
?>
<section class="auth-card">
  <p class="eyebrow">ONE-TIME SETUP</p>
  <h1>管理者アカウント作成</h1>
  <?php if ($completed): ?>
    <div class="flash flash-success">管理者アカウントを作成しました。この画面は自動的に無効になりました。</div>
    <a class="button button-primary" href="<?= e(url('login.php')) ?>">ログイン画面へ</a>
  <?php else: ?>
    <p class="muted">公開環境で最初の管理者を作成します。登録後、この画面は再実行できません。</p>
    <?php if ($error): ?><div class="flash flash-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="stack">
      <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
      <label>インストールキー<input type="password" name="install_key" required autocomplete="off"></label>
      <label>管理者名<input type="text" name="name" maxlength="100" required></label>
      <label>ログイン用メールアドレス<input type="email" name="email" required autocomplete="username"></label>
      <label>ログイン用パスワード<input type="password" name="password" minlength="12" required autocomplete="new-password"></label>
      <label><input type="checkbox" name="sample_data" value="1" checked> サンプル顧客を登録する</label>
      <button class="button button-primary" type="submit">管理者を作成する</button>
    </form>
  <?php endif; ?>
</section>
<?php render_footer(); ?>
