# coachtech-flea-market-apps

## 環境構築
### Docker ビルド
1.  リポジトリをクローンします:
    ```bash
    'git clone git@github.com:shun1019/coachtech-flea-market-app.git'
    ```
2.  DockerDesktop アプリを立ち上げる:
    ```bash
    'docker-compose up -d --build'
    ```
3.  > _Mac の M1・M2 チップの PC の場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`の
    > メッセージが表示されビルドができないことがあります。
    > エラーが発生する場合は、docker-compose.yml ファイルの「mysql,phpmyadmin」内に「platform」の項目を追加で記載してください。

```bash
mysql:
        image: mysql:8.0.26
        platform: linux/amd64
        environment:
```
phpmyadmin:
        image: phpmyadmin/phpmyadmin
        platform: linux/amd64
        environment:
```

### Laravel 環境構築
1.   `docker-compose exec php bash`
2.   `composer install`
3.  「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.env ファイルを作成
4.  .env に以下の環境変数を追加

```text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

5.  アプリケーションキーを作成:
    ```bash
    php artisan key:generate
    ```
6.  データベースマイグレーションを作成:
    ```bash
    php artisan migrate
    ```
7.  データベースシーディングを作成:
    ```bash
    php artisan db:seed
    ```

## ER図
![alt](erd.png)

## URL

-   開発環境: [http://localhost/](http://localhost/)
-   phpMyAdmin: [http://localhost:8080/](http://localhost:8080/)
