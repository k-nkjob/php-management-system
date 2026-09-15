<?php
declare(strict_types=1);

final class CustomerRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function paginate(string $query, int $page, int $perPage = 10): array
    {
        $search = mb_substr(trim($query), 0, 100);
        $search = str_replace(['%', '_'], '', $search);
        $where = '';
        $params = [];

        if ($search !== '') {
            // 前方一致にすることで、各検索列のインデックスを利用できる余地を残す。
            $where = ' WHERE name LIKE :name OR email LIKE :email OR company LIKE :company';
            $prefix = $search . '%';
            $params = ['name' => $prefix, 'email' => $prefix, 'company' => $prefix];
        }

        $countStatement = $this->pdo->prepare('SELECT COUNT(*) FROM customers' . $where);
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $statement = $this->pdo->prepare(
            'SELECT id, name, email, phone, company, created_at
             FROM customers' . $where . '
             ORDER BY id DESC
             LIMIT :limit OFFSET :offset'
        );
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'items' => $statement->fetchAll(),
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
        ];
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM customers WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function create(array $data): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO customers (name, email, phone, company, created_at, updated_at)
             VALUES (:name, :email, :phone, :company, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)'
        );
        $statement->execute($this->fields($data));
    }

    public function update(int $id, array $data): void
    {
        $fields = $this->fields($data);
        $fields['id'] = $id;
        $statement = $this->pdo->prepare(
            'UPDATE customers
             SET name = :name, email = :email, phone = :phone,
                 company = :company, updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $statement->execute($fields);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM customers WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->rowCount() > 0;
    }

    private function fields(array $data): array
    {
        return [
            'name' => trim((string) $data['name']),
            'email' => mb_strtolower(trim((string) $data['email'])),
            'phone' => trim((string) ($data['phone'] ?? '')),
            'company' => trim((string) ($data['company'] ?? '')),
        ];
    }
}
