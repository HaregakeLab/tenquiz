#!/bin/bash
set -e

export COMPOSER_ALLOW_SUPERUSER=1

# リポジトリ名を検出
if git config --get remote.origin.url > /dev/null 2>&1; then
    REPO_NAME=$(basename -s .git "$(git config --get remote.origin.url)")
else
    REPO_NAME=$(basename "$(pwd)")
fi
export REPO_NAME

# ローカル用 nginx.conf を生成（テンプレートからリポジトリ名を置換）
if [ -f "docker/nginx.conf.template" ]; then
    sed "s/{{REPO_NAME}}/${REPO_NAME}/g" docker/nginx.conf.template > docker/nginx.conf
    echo "nginx.conf 生成: /sandbox/${REPO_NAME}/"
fi

# vendor が無ければ composer install
if [ ! -f "vendor/autoload.php" ]; then
    echo "composer install を実行中..."
    composer install --no-interaction --no-progress
fi

# .env が無ければ作成
if [ ! -f ".env" ]; then
    echo ".env を作成中..."
    cp .env.example .env
    php artisan key:generate --no-interaction --quiet
fi

# APP_URL をローカルのパスに合わせる
sed -i "s|^APP_URL=.*|APP_URL=http://localhost:8080/sandbox/${REPO_NAME}|" .env

# SQLite DB の準備
if [ ! -f "database/database.sqlite" ]; then
    touch database/database.sqlite
fi

# 権限設定
chown -R www-data:www-data storage bootstrap/cache database/database.sqlite
chmod -R 775 storage bootstrap/cache

# マイグレーション
php artisan migrate --force --no-interaction --quiet || true

# キャッシュクリア
php artisan config:clear --quiet 2>/dev/null || true
php artisan cache:clear --quiet 2>/dev/null || true
php artisan storage:link --no-interaction --quiet 2>/dev/null || true

echo "準備完了！ http://localhost:8080/sandbox/${REPO_NAME}/ でアクセスできます"

exec "$@"
