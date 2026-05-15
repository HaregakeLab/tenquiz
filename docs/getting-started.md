# はじめかたガイド

このページでは、最初のセットアップ手順を説明します。難しい操作はすべて AI がやってくれるので安心してください。

## 必要なもの

3 つのソフトをインストールしてください。どれも画面の指示に従うだけでインストールできます。

### 1. Docker Desktop

アプリの動作環境を自動で作ってくれるソフトです。

- **Windows**: [Docker Desktop for Windows](https://www.docker.com/products/docker-desktop/) からダウンロード
  - インストール中に「WSL 2 を使う」のような選択肢が出たら、そのまま「OK」で進めてください
  - インストール後、PC の再起動が必要な場合があります
  - 再起動後、Docker Desktop を起動して、画面左下に「Engine running」と表示されれば準備完了です
- **Mac**: [Docker Desktop for Mac](https://www.docker.com/products/docker-desktop/) からダウンロード
  - ダウンロードした .dmg ファイルを開いて、Docker を Applications にドラッグするだけです
  - インストール後、Docker Desktop を起動して、画面左下に「Engine running」と表示されるまで待ってください

### 2. AI コーディングツール

どれかひとつをインストールしてください。

| ツール | インストール |
|--------|------------|
| Claude Code | [インストール方法](https://docs.anthropic.com/en/docs/claude-code/overview) を参照 |
| Cowork | [Claude Desktop](https://claude.ai/download) をダウンロード |
| Cursor | [cursor.com](https://cursor.com/) からダウンロード |
| Codex | [openai.com](https://openai.com/index/introducing-codex/) から利用 |
| Antigravity | [antigravity.codes](https://antigravity.codes/) からインストール |
| GitHub Copilot | [VS Code](https://code.visualstudio.com/) に拡張機能をインストール |

どれを選んでも大丈夫です。ダウンロードして、画面の指示に従ってインストールするだけです。

## 手順

### ステップ 1：GitHub アカウントを作る

[GitHub](https://github.com/) のアカウントを持っていない場合は、先に作ってください（無料です）。

管理者から組織への招待メールが届いたら、承認してください。

### ステップ 2：テンプレートから新しいプロジェクトを作る

1. [テンプレートの GitHub ページ](https://github.com/HaregakeLab/vibe-scaffold-laravel) を開く
2. 緑色の **「Use this template」** ボタンを押す
3. **「Create a new repository」** を選ぶ
4. **Owner** のところで **HaregakeLab** を選ぶ
5. **Repository name** に、作りたいアプリの名前を英語で入力する
   - 使える文字: **英小文字、数字、ハイフン（-）** のみ
   - 例: `my-puzzle`, `sales-tool`, `event-page`
   - この名前がそのまま公開時の URL になります
6. **「Create repository」** を押す

### ステップ 3：GitHub にログインする（初回のみ）

AI ツールを起動して、こう伝えてください:

> **「GitHub にログインして」**

AI が GitHub CLI（`gh`）を自動でダウンロード・インストールします。途中で Mac のログインパスワードを聞かれたり、ブラウザが開いたりするので、画面の指示に従ってください。

（この作業は最初の 1 回だけです。次回以降は不要です）

### ステップ 4：プロジェクトを AI ツールで開く

1. ステップ 2 で作ったリポジトリの GitHub ページを開く
2. ブラウザの **アドレスバーの URL をコピー** する（`https://github.com/...` で始まる文字列）
3. AI に、コピーした URL を貼り付けて、こう伝える:

> **「この URL のリポジトリをクローンして開いて: https://github.com/...」**

AI がリポジトリをダウンロードして開いてくれます。

### ステップ 5：AI に「セットアップして」と言う

Docker Desktop が起動していることを確認してください。

プロジェクトを開いたら、AI に次のように伝えてください。

> **「このプロジェクトをセットアップして、開発サーバーを起動して」**

AI が必要な準備をすべて自動で行います。セットアップが完了すると http://localhost:8080 でアプリが表示されます。

もし AI に何か聞かれたら、「はい」や「OK」と答えてください。

### ステップ 6：作りたいものを AI に伝える

セットアップが終わったら、あとは作りたいものを日本語で伝えるだけです。

**例：**
- 「トップページをかっこよくして」
- 「お問い合わせフォームを作って」
- 「商品の一覧ページを作って」

詳しくは [AI で開発する方法](development.md) を読んでください。

### ステップ 7：公開する

アプリが完成したら、AI に **「公開して」** と伝えてください。

AI がコードを GitHub にアップロードし、2〜3 分後に自動でサーバーに公開されます。

詳しくは [公開する方法](deployment.md) を読んでください。
