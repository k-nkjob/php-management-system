# PHP Management System

既存業務システムの修正・機能追加を想定して自主制作した、PHP/PDOベースの顧客管理システムです。実在するクライアント案件ではありません。

## 実装機能

- セッションベースのログイン認証・ログアウト
- 顧客一覧、検索、ページネーション、登録、編集、削除
- サーバー側入力バリデーション
- SQLite（Codespaces）/ MySQL（公開環境）の切り替え
- Codespacesですぐに起動できる構成

## セキュリティ

password_hash / password_verify、ログイン成功時のセッションID再生成、HttpOnly・SameSite Cookie、HTTPS時Secure Cookie、CSRF検証、PDO Prepared Statement、htmlspecialcharsによる出力エスケープ、POST限定削除、CSP等のセキュリティヘッダー、public外へのDB・設定配置、簡易ログイン試行制限を実装しています。

## Codespacesで起動

GitHubの Code → Codespaces → Create codespace on main を選びます。作成後、SQLite DBと初期データが自動生成され、ポート8000のPreviewが開きます。

手動実行:

    php database/seed.php
    php -S 0.0.0.0:8000 -t public

初期デモアカウント:

- Email: admin@example.com
- Password: ChangeMe123!

これは開発確認用です。公開時は DEMO_ADMIN_EMAIL と DEMO_ADMIN_PASSWORD を設定し、初期値を使用しないでください。

## 設定

実値を含む config.php は.gitignore対象です。DB_DRIVERをsqliteまたはmysqlへ設定し、MySQLではDB_HOST、DB_PORT、DB_NAME、DB_USER、DB_PASSWORDを環境変数で指定します。サブディレクトリ配置ではAPP_BASE_PATHを設定します。

MySQL用DDLは database/schema.mysql.sql、SQLite用DDLは database/schema.sql に分離しています。アプリケーション本体はPDOを利用し、DB固有差分をスキーマへ閉じ込めています。

## 公開時の注意

- publicディレクトリをDocumentRootにしてください。
- 本番用のDB認証情報やパスワードをGitHubへコミットしないでください。
- 無料ホスティングの仕様・制限は変更される可能性があるため、公開前に最新条件を確認してください。
