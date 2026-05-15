# AI アシスタントへの指示書

このプロジェクトは **プログラミング経験のないユーザー** が AI と対話しながら Web アプリを作るためのテンプレートです。ユーザーは技術用語を使いません。「お問い合わせフォームを作って」「トップページをかっこよくして」のような自然言語で要求してきます。

**あなたの役割**: ユーザーの自然言語の要求を理解し、セットアップから開発、テストまで全て自律的に実行すること。

**GitHub 組織**: `HaregakeLab`（https://github.com/HaregakeLab）

---

## superpowers skills（必須）

このプロジェクトには `.claude/settings.json` で **superpowers プラグイン** の自動有効化が設定されています。Claude Code がプロジェクトを開いたタイミングで marketplace 登録 + プラグイン有効化が走るはずです。

もし `superpowers:brainstorming` 等のスキルが利用可能リストに見えない場合は、以下を実行してインストールしてください:

```
/plugin marketplace add obra/superpowers-marketplace
/plugin install superpowers@superpowers-marketplace
```

インストール後は `/reload-plugins` で有効化。利用可能になる主なスキル:

- `superpowers:brainstorming` — 新機能や設計の壁打ち（実装前に必ず通すこと）
- `superpowers:writing-plans` — 仕様から実装計画を作る
- `superpowers:executing-plans` — 計画を別セッションで実行
- `superpowers:test-driven-development` — TDD でバグ修正・機能追加
- `superpowers:systematic-debugging` — バグ・テスト失敗時の原因究明
- `superpowers:verification-before-completion` — 完了報告前に検証

該当する作業に当たったら、必ず対応する skill を呼ぶこと。

---

## GitHub 認証（初回のみ）

ユーザーが「GitHub にログインしたい」と言ったら、以下を実行する。

1. `gh` CLI がインストールされているか確認: `gh --version`
2. 未インストールなら **公式リリース（zip）をダウンロード→展開→所定の場所に配置** する。Homebrew や winget には依存しないこと（ユーザー環境に入っていないことが多い）。
   - **macOS**:
     ```bash
     GH_VERSION=$(curl -sL https://api.github.com/repos/cli/cli/releases/latest | grep -o '"tag_name": "v[^"]*"' | cut -d'"' -f4 | sed 's/v//')
     ARCH=$(uname -m); [ "$ARCH" = "x86_64" ] && ARCH=amd64
     curl -sL "https://github.com/cli/cli/releases/download/v${GH_VERSION}/gh_${GH_VERSION}_macOS_${ARCH}.zip" -o /tmp/gh.zip
     unzip -q -o /tmp/gh.zip -d /tmp/gh
     sudo install -m 0755 /tmp/gh/gh_${GH_VERSION}_macOS_${ARCH}/bin/gh /usr/local/bin/gh
     rm -rf /tmp/gh /tmp/gh.zip
     ```
     `sudo` のパスワード入力が必要になるので、ユーザーに「Mac のログインパスワードを入力してください」と伝える。
   - **Windows**（PowerShell）:
     ```powershell
     $v = (Invoke-RestMethod https://api.github.com/repos/cli/cli/releases/latest).tag_name -replace '^v',''
     $url = "https://github.com/cli/cli/releases/download/v$v/gh_${v}_windows_amd64.zip"
     Invoke-WebRequest $url -OutFile "$env:TEMP\gh.zip"
     Expand-Archive -Force "$env:TEMP\gh.zip" "$env:TEMP\gh"
     $dest = "$env:USERPROFILE\bin"
     New-Item -Force -ItemType Directory $dest | Out-Null
     Copy-Item -Force "$env:TEMP\gh\bin\gh.exe" "$dest\"
     [Environment]::SetEnvironmentVariable('Path', "$dest;" + [Environment]::GetEnvironmentVariable('Path','User'), 'User')
     $env:Path = "$dest;$env:Path"
     ```
3. 認証を実行: `gh auth login`
   - 「GitHub.com」を選択
   - 「HTTPS」を選択
   - 「Login with a web browser」を選択
   - ユーザーに「ブラウザが開くので GitHub にログインしてください」と伝える
4. 認証完了後、`gh auth status` で確認

ユーザーにはコマンドを見せず、「ブラウザが開くのでログインしてください」とだけ伝える。

---

## 初回セットアップ

プロジェクトを初めて開いたとき、以下を実行する。

```bash
docker compose up -d
```

これだけで Apache + PHP 環境の構築、composer install、.env 作成、APP_KEY 生成、DB マイグレーションが全て完了する。http://localhost:8080 でアクセス可能になる。

ソースコードはホストからマウントされているため、ファイルを編集すると即座に反映される。

---

## 技術スタック（ユーザーに説明不要）

- **Laravel 11** (PHP 8.2+) — Web フレームワーク
- **Blade** — テンプレートエンジン（`resources/views/` 以下の `.blade.php` ファイル）
- **Tailwind CSS** — ユーティリティ CSS。開発時は CDN で自動読み込み。ビルド不要
- **SQLite** — ファイルベースのデータベース（`database/database.sqlite`）
- **Docker** — ローカル開発環境（Apache + PHP 8.2）

---

## 開発サーバー

```bash
docker compose up -d        # 起動（http://localhost:8080）
docker compose down          # 停止
docker compose logs -f app   # ログ確認
```

開発時は `APP_DEBUG=true` で Tailwind CDN が自動読み込みされるため、どんな Tailwind クラスでも即座に反映される。

---

## ユーザーの要求に応えるレシピ

### 「〇〇なページを作って」

1. **ルート追加**: `routes/web.php` に `Route::get('/パス', ...)` を追加
2. **ビュー作成**: `resources/views/ページ名.blade.php` を作成
3. レイアウトは `@extends('layouts.app')` で継承し、`@section('content')` にコンテンツを書く
4. Tailwind CSS のクラスで見た目を整える

```php
// routes/web.php に追加
Route::get('/about', function () {
    return view('about');
});
```

```blade
{{-- resources/views/about.blade.php --}}
@extends('layouts.app')

@section('title', 'このサイトについて')

@section('content')
<div class="max-w-2xl mx-auto px-6 py-16">
    <h1 class="text-3xl font-bold mb-4">このサイトについて</h1>
    <p class="text-gray-600">ここに内容を書きます。</p>
</div>
@endsection
```

### 「〇〇なフォームを作って」

1. ルートを GET（表示用）と POST（送信用）の 2 つ作る
2. Blade テンプレートに `<form>` を作成。`@csrf` を必ず含める
3. コントローラーで送信データを処理する

```php
// routes/web.php
Route::get('/contact', [ContactController::class, 'show']);
Route::post('/contact', [ContactController::class, 'submit']);
```

コントローラー作成: `php artisan make:controller ContactController`

### 「データを保存したい」「一覧を表示したい」

1. モデルとマイグレーション作成: `php artisan make:model Item -m`
2. マイグレーションファイル（`database/migrations/` 内）でカラムを定義
3. マイグレーション実行: `php artisan migrate`
4. コントローラーで CRUD 操作を実装

```php
// マイグレーションの例
Schema::create('items', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->timestamps();
});
```

### 「見た目を変えて」「デザインを直して」

- `resources/views/` 以下の Blade ファイルを編集
- Tailwind CSS のクラスを使う（例: `text-blue-600`, `bg-white`, `rounded-lg`, `shadow`）
- 共通レイアウトは `resources/views/layouts/app.blade.php`

### 「トップページを変えて」

- `resources/views/welcome.blade.php` を編集

### 「公開して」「デプロイして」

main ブランチに push すれば、サーバーが自動でコードを取得してデプロイする。

```bash
git add -A
git commit -m "変更内容の説明"
git push origin main
```

push 後 1〜2 分で自動デプロイが完了する。
URL は `https://app.haregake-lab.com/sandbox/リポジトリ名/` になる。
ユーザーには「公開しました。1〜2 分後にアクセスできます」と伝える。
アクセスにはサンドボックスのユーザー名とパスワードが必要。

### 「新しいプロジェクトを作りたい」

このテンプレートから新しいリポジトリを作る手順をユーザーに案内する:

1. https://github.com/HaregakeLab/vibe-scaffold-laravel を開く
2. 「Use this template」→「Create a new repository」
3. Owner で **HaregakeLab** を選択
4. Repository name に英小文字とハイフンで名前を入力（例: my-puzzle）
   - この名前がそのまま公開 URL のパスになる
5. 「Create repository」を押す

リポジトリ名は **英字・数字・ハイフン・アンダースコアのみ** 使える。スペースや日本語は不可。

### 「API キーを設定して」「本番で〇〇を使えるようにして」

本番環境で必要なシークレット（API キー等）は SOPS で暗号化してリポジトリにコミットする。
デプロイ時にサーバーが自動で復号する。

まず sops と age がインストールされているか確認する:

- macOS: `brew install sops age`
- Windows: `scoop install sops age`

シークレットの登録:

```bash
# 初回: .env.enc を作成
sops .env.enc

# エディタが開くので KEY=VALUE 形式で入力して保存
# 例:
# OPENAI_API_KEY=sk-xxx
# MAIL_PASSWORD=password123

# 追加・変更: 既存の .env.enc を編集
sops .env.enc

# コミット & push（暗号化されているのでコミットして安全）
git add .env.enc
git commit -m "シークレットを更新"
git push origin main
```

ユーザーには「API キーを教えてください」と聞き、受け取ったら sops で暗号化してコミットする。
キーの値を .env や他のファイルに平文で書かないこと。.env.enc のみに入れる。

---

## ファイル構成

| やりたいこと | 触るファイル |
|-------------|-------------|
| ページを追加 | `routes/web.php` + `resources/views/` に .blade.php |
| ロジックを追加 | `app/Http/Controllers/` にコントローラー |
| データを扱う | `app/Models/` + `database/migrations/` |
| 見た目を変える | `resources/views/` 内の .blade.php |
| 共通レイアウト | `resources/views/layouts/app.blade.php` |
| 設定変更 | `.env` ファイル |
| デプロイ設定 | `deploy.sh`（セットアップ手順）、`nginx.conf.template`（サーバー配信設定） |

---

## よく使うコマンド

```bash
docker compose up -d                                    # 開発環境起動
docker compose exec app php artisan make:controller Foo # コントローラー作成
docker compose exec app php artisan make:model Foo -m   # モデル + マイグレーション作成
docker compose exec app php artisan migrate             # DBマイグレーション実行
docker compose exec app php artisan test                # テスト実行
```

---

## デプロイ関連ファイル

デプロイはサーバーが自動で実行する。以下のファイルがデプロイの動作を制御する。

- **`deploy.sh`** — サーバーでのセットアップ手順。composer install、マイグレーション、Tailwind ビルド等。デプロイ時にサーバーがこのスクリプトを実行する
- **`nginx.conf.template`** — 本番サーバーの nginx 設定テンプレート。`{{REPO_NAME}}` がリポジトリ名に置換される。PHP 以外のアプリに変更する場合はこのファイルを編集する
- **`docker/nginx.conf.template`** — ローカル開発用の nginx 設定テンプレート。本番と同じパス構成（`/sandbox/{repo-name}/`）で配信する。`nginx.conf.template` を変更したら、こちらも合わせて変更すること

---

## 注意事項

- **Node.js は不要**。Vite も使わない。npm/yarn コマンドは実行しないこと
- **vendor/ はコミットしない**（.gitignore に含まれている）
- Tailwind CSS は開発時 CDN 自動読み込み。本番ビルドはデプロイ時に自動実行される
- ロケール: 日本語（`APP_LOCALE=ja`, `APP_TIMEZONE=Asia/Tokyo`）
- フォームには必ず `@csrf` を含めること
- 画像等の静的ファイルは `public/` に配置し、`asset('ファイルパス')` で参照

---

## ユーザーとのコミュニケーション

ユーザーはプログラマーではありません。以下を心がけてください:

- 技術用語の説明は不要。黙って実装して結果を見せる
- 「〇〇を作りました。http://localhost:8080/パス で確認できます」のように伝える（Docker 未使用時は 8000）
- エラーが出たら自分で解決する。ユーザーにデバッグを求めない
- 選択肢がある場合は技術的な選択肢ではなく「見た目」や「動作」の違いで提示する

---

## 自分で解決できない問題に当たった場合

何度か試しても直せない問題（デプロイが通らない、サーバー側の権限・インフラに起因しそうな挙動、認証基盤の不具合、原因不明の 500 エラーなど）に当たったら、ユーザーに **野田さん（noda.sin@gmail.com）に Chatwork で相談する** ことを提案してください。

提案するときは、ユーザーがそのままコピペで送れる形の相談文を一緒に出すこと。最低限以下を含める:

- 何をしようとしていたか（ユーザー視点の言葉で）
- 実際に何が起きたか（エラーメッセージ、URL、画面の表示など）
- リポジトリ名と該当 URL（例: `https://app.haregake-lab.com/sandbox/<repo>/...`）
- これまでに自分（AI）が試したこと
- 関連するコミット SHA やデプロイログの抜粋（あれば）

例:

> このエラーは私（AI）では原因を特定できませんでした。野田さん（noda.sin@gmail.com）に Chatwork で以下をそのまま送って相談してみてください:
>
> ```
> 【相談】X_mark のデプロイが通りません
>
> URL: https://app.haregake-lab.com/sandbox/X_mark/
> リポジトリ: HaregakeLab/X_mark
> 最新コミット: fb7a4f5
>
> 起きていること:
> - ブラウザで開くと「500 Internal Server Error」が表示される
>
> AI が試したこと:
> - .env を確認 → APP_KEY などは入っている
> - storage の権限を直そうとしたが root 権限が必要だった
>
> デプロイログ抜粋:
> [ERROR] Permission denied: storage/logs/laravel.log
> ```
