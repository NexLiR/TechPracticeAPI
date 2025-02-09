# [Документація](https://documenter.getpostman.com/view/41938275/2sAYX8K1z7)

# Запуск і підготовка проєкту
Для роботи програми необхідена версія **PHP 8.1.31** і новіше.

Ми маємо впевнитися, що всі потрібні для роботи програми пакети встановлено:
```
composer install
```
Для генерації SSL-ключів доступу необхідно ввести наступну команду:
```
php bin/console lexik:jwt:generate-keypair
```
Після чого в файлі .env прописати PASSPHRASE для ключів:
```
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=
```
Далі запускаємо сервер:
```
symfony server:start
```
