# アプリケーション名
coachtech-flea-market-app

## プロジェクトの概要
このアプリケーションは、ユーザー間で商品を売買できるフリーマーケットプラットフォームです。

主な機能:
- ユーザー登録・ログイン
- 商品の出品
- 商品の購入
- コメント・いいね機能
- 商品のカテゴリー管理

---
## 環境構築

### Docker ビルド
1. リポジトリをクローンします:
    ```bash
    git clone git@github.com:shun1019/coachtech-flea-market-app.git
    cd coachtech-flea-market-app
    ```

2. DockerDesktop アプリを立ち上げる:
    ```bash
    docker-compose up -d --build
    ```

3. (MacのM1/M2チップでエラーが発生する場合)
   - 以下の設定を `docker-compose.yml` に追加してください:
    ```yaml
    mysql:
        platform: linux/amd64
    phpmyadmin:
        platform: linux/amd64
    ```

### Laravel 環境構築
1. PHPコンテナに入ります:
    ```bash
    docker-compose exec php bash
    ```

2. 依存関係をインストール:
    ```bash
    composer install
    ```

3. `.env` ファイルを作成:
    ```bash
    cp .env.example .env
    ```

4. 環境変数を設定:
    ```text
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=laravel_db
    DB_USERNAME=laravel_user
    DB_PASSWORD=laravel_pass
    ```

5. アプリケーションキーを生成:
    ```bash
    php artisan key:generate
    ```

6. マイグレーションを実行
    ``` bash
    php artisan migrate
    ```

7. シーディングを実行
    ``` bash
    php artisan db:seed
    ```

## 使用技術(実行環境)
- PHP 7.4.9
- Laravel 8.83.8
- MySQL 8.0.26
- Laravel Fortify: 1.19

## ER図
![alt](erd.png)

## URL
-   開発環境: [http://localhost/](http://localhost/)
-   phpMyAdmin: [http://localhost:8080/](http://localhost:8080/)
