# プロジェクト概要

非プログラマー向けの Laravel Web アプリ scaffold。ユーザーは AI に日本語で指示してアプリを作ります。

## セットアップ

`docker compose up -d` → http://localhost:8080 で動作確認

## 技術スタック

- Laravel 11 + Blade + Tailwind CSS (CDN、dev) + SQLite
- **Node.js / npm / Vite は一切使用禁止**

## 重要ルール

- ユーザーはプログラミング経験がない非プログラマーです
- 技術的な詳細ではなく、結果（何ができたか）を伝えてください
- 日本語で応答してください
- ユーザーにターミナルコマンドの実行を求めないでください

## 主要ファイルパス

- `routes/web.php` - ルーティング定義
- `resources/views/` - Blade テンプレート
- `app/Http/Controllers/` - コントローラー
- `app/Models/` - Eloquent モデル

詳細は CLAUDE.md を参照してください。
