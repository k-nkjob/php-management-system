<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
Auth::requireLogin();

$repository = new CustomerRepository(Database::connection());
$query = trim((string) ($_GET['q'] ?? ''));
$page = max(1, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1);
$result = $repository->paginate($query, $page);
$startPage = max(1, $result['page'] - 2);
$endPage = min($result['pages'], $result['page'] + 2);

render_header('顧客一覧');
?>
<div class="page-heading">
  <div><p class="eyebrow">CUSTOMER DATABASE</p><h1>顧客一覧</h1><p class="muted">全<?= e((string) $result['total']) ?>件</p></div>
  <a class="button button-primary" href="<?= e(url('customers/create.php')) ?>">＋ 新規登録</a>
</div>
<form class="search" method="get">
  <input type="search" name="q" value="<?= e($query) ?>" placeholder="氏名・メール・会社名を先頭から検索">
  <button class="button">検索</button>
  <?php if ($query !== ''): ?><a class="button button-ghost" href="<?= e(url('customers/index.php')) ?>">解除</a><?php endif; ?>
</form>
<div class="table-wrap"><table>
  <thead><tr><th>ID</th><th>氏名</th><th>会社名</th><th>連絡先</th><th>登録日</th><th>操作</th></tr></thead>
  <tbody>
  <?php foreach ($result['items'] as $customer): ?>
    <tr>
      <td><?= e((string) $customer['id']) ?></td>
      <td><strong><?= e($customer['name']) ?></strong></td>
      <td><?= e($customer['company'] ?: '—') ?></td>
      <td><?= e($customer['email']) ?><br><span class="muted"><?= e($customer['phone']) ?></span></td>
      <td><?= e(substr($customer['created_at'], 0, 10)) ?></td>
      <td class="actions">
        <a href="<?= e(url('customers/edit.php?id=' . $customer['id'])) ?>">編集</a>
        <form method="post" action="<?= e(url('customers/delete.php')) ?>" data-confirm="この顧客を削除しますか？">
          <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
          <input type="hidden" name="id" value="<?= e((string) $customer['id']) ?>">
          <button class="danger-link">削除</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$result['items']): ?><tr><td colspan="6" class="empty">該当する顧客がありません。</td></tr><?php endif; ?>
  </tbody>
</table></div>
<?php if ($result['pages'] > 1): ?>
<nav class="pagination" aria-label="ページ送り">
  <?php if ($startPage > 1): ?>
    <a href="?q=<?= urlencode($query) ?>&page=1">1</a>
    <?php if ($startPage > 2): ?><span>…</span><?php endif; ?>
  <?php endif; ?>
  <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
    <a class="<?= $i === $result['page'] ? 'active' : '' ?>" href="?q=<?= urlencode($query) ?>&page=<?= $i ?>"><?= $i ?></a>
  <?php endfor; ?>
  <?php if ($endPage < $result['pages']): ?>
    <?php if ($endPage < $result['pages'] - 1): ?><span>…</span><?php endif; ?>
    <a href="?q=<?= urlencode($query) ?>&page=<?= $result['pages'] ?>"><?= $result['pages'] ?></a>
  <?php endif; ?>
</nav>
<?php endif; ?>
<?php render_footer(); ?>
