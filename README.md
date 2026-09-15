# PHP Management System

PHPとPDOで構築した、ログイン認証付きの顧客管理システムです。

既存業務システムの修正・機能追加を想定し、認証、顧客検索、登録、編集、削除といった管理業務の基本機能を、保守・拡張しやすい構成で実装しています。

このシステムは自主制作ポートフォリオです。実在する企業・顧客向けに納品したシステムではありません。

## このシステムでできること

- 管理ユーザーのログイン・ログアウト
- 顧客情報の一覧表示
- 氏名、メールアドレス、会社名による前方一致検索
- 一覧のページネーション
- 顧客情報の新規登録
- 登録済み顧客の編集
- 確認ダイアログを伴う顧客削除
- 入力内容のサーバー側バリデーション
- SQLiteとMySQLの切り替え
- Codespacesを利用したブラウザ上での起動・確認

## 制作方針

最初から機能を増やしすぎず、顧客管理に必要な一連の業務を実際に完了できるMVP（Minimum Viable Product）として設計しました。

MVPでありながら、機能追加時に一つのファイルへ処理が集中しないよう、画面、認証、DB接続、入力検証、CSRF対策、データアクセスを分離しています。既存機能への影響を抑えながら、権限管理、顧客詳細、CSV出力、操作履歴などを追加できる構成を意識しています。

## アーキテクチャ

処理の責務を次のように分けています。

| レイヤー・機能 | 主なファイル | 責務 |
|---|---|---|
| エントリーポイント | public/index.php | ログイン状態に応じた画面遷移 |
| 認証 | src/Auth.php | ログイン判定、セッション管理、ログアウト |
| DB接続 | src/Database.php | PDO接続とSQLite／MySQL切り替え |
| データアクセス | src/CustomerRepository.php | 顧客CRUD、検索、ページネーション |
| 入力検証 | src/Validator.php | 顧客データの検証 |
| CSRF対策 | src/Csrf.php | トークン生成・検証 |
| 共通処理 | src/helpers.php | エスケープ、URL生成、画面共通部、フラッシュメッセージ |
| 画面 | public/login.php、public/customers | HTTP入力の受け取りと表示 |
| DB定義 | database | SQLite／MySQLのスキーマと初期データ |
| 自動検証 | tests、.github/workflows | 構文検査と主要機能のスモークテスト |

各クラスは内部処理をカプセル化し、画面側がSQL、パスワード検証、CSRFトークン生成などの詳細を直接扱わない構成にしています。

## 保守性・拡張性の工夫

- 顧客データへのアクセスをCustomerRepositoryへ集約
- DB接続処理をDatabaseへ集約
- 認証処理をAuthへ集約
- 入力検証をValidatorへ分離
- CSRF処理をCsrfへ分離
- PHPの型宣言とstrict_typesを使用
- DB設定を環境変数・設定ファイルへ分離
- SQLite／MySQL固有のDDLを別ファイルで管理
- CSS、JavaScript、PHP、DB定義を役割別に分離
- GitHub Actionsで全PHPファイルの構文と主要処理を自動確認

## 検索・一覧処理の工夫

検索対象の氏名、メールアドレス、会社名にはインデックスを設定しています。

検索は先頭にワイルドカードを置かない前方一致方式とし、データ件数が増えた場合にもDBがインデックスを利用できる余地を残しています。検索文字列は長さを制限し、SQLのワイルドカード文字が無制限に使われないよう正規化しています。

一覧は必要な件数だけをLIMITとOFFSETで取得します。また、ページネーションは全ページ番号をループせず、現在ページ周辺と先頭・末尾だけを描画します。

## セキュリティ対策

- password_hash／password_verifyによるパスワード処理
- ログイン成功時のセッションID再生成
- HttpOnly・SameSite Cookie
- HTTPS接続時のSecure Cookie
- CSRFトークンによる登録・編集・削除・ログアウトの保護
- PDO Prepared StatementによるSQLインジェクション対策
- htmlspecialcharsによる出力エスケープ
- 削除処理をPOSTに限定
- Content Security Policyなどのセキュリティヘッダー
- DBファイルと設定ファイルをpublicディレクトリ外へ配置
- 簡易ログイン試行回数制限
- 本番用DB認証情報をリポジトリへ保存しない構成

## 使用技術

- PHP 8.3
- PDO
- SQLite
- MySQL対応
- HTML
- CSS
- JavaScript
- GitHub Codespaces
- GitHub Actions

## Codespacesでの起動方法

GitHubの「Code」から「Codespaces」を選び、「Create codespace on main」を実行します。

コンテナ作成後、SQLiteデータベースと初期データが自動生成され、ポート8000でPHPの開発サーバーが起動します。

手動で実行する場合:

    php database/seed.php
    php -S 0.0.0.0:8000 -t public

初期確認用アカウント:

- Email: admin@example.com
- Password: ChangeMe123!

このアカウントは開発確認用です。公開環境ではDEMO_ADMIN_EMAILとDEMO_ADMIN_PASSWORDを設定し、初期値を使用しないでください。

## DB設定

実際の認証情報を含むconfig.phpはGit管理対象外です。

SQLiteとMySQLはDB_DRIVERで切り替えます。MySQLではDB_HOST、DB_PORT、DB_NAME、DB_USER、DB_PASSWORDを環境変数で指定します。サブディレクトリへ配置する場合はAPP_BASE_PATHを設定します。

- SQLite用DDL: database/schema.sql
- MySQL用DDL: database/schema.mysql.sql

## 自動テスト

GitHub Actionsで次の処理を実行します。

- 全PHPファイルの構文検査
- SQLiteデータベースの作成
- ログイン認証
- CSRFトークン検証
- 顧客の登録・検索・取得・編集・削除

## 公開時の注意

- publicディレクトリをDocumentRootに設定してください。
- 公開環境では初期パスワードを必ず変更してください。
- 本番用のDB認証情報をGitHubへコミットしないでください。
- ホスティングサービスの仕様や制限は変更される可能性があるため、公開前に最新情報を確認してください。
