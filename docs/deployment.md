# デプロイ（公開）

## 仕組み

main ブランチに push すると、サーバーが自動でデプロイします。

```mermaid
sequenceDiagram
    participant Dev as 開発者/AI
    participant GH as GitHub
    participant App as GitHub App<br>(HaregakeLab Deploy)
    participant WH as Webhook Receiver<br>(index.php)
    participant DP as Deploy Script<br>(deploy.php)
    participant SV as サーバー<br>(sakura VPS)

    Dev->>GH: git push origin main
    GH->>App: push イベント通知
    App->>WH: POST /deployer/webhook/<br>(署名付き)

    WH->>WH: 署名検証 (HMAC-SHA256)
    WH->>WH: org チェック (HaregakeLab のみ)
    WH->>WH: main ブランチ / 非テンプレート確認
    WH->>DP: バックグラウンドで起動

    Note over WH: HTTP 200 を即座に返す

    DP->>GH: GitHub App トークン取得
    DP->>GH: git clone / pull

    DP->>DP: deploy.sh / nginx.conf.template<br>の存在を確認

    alt どちらも存在しない
        DP->>DP: デプロイ対象外と判断<br>clone を削除して終了
    else デプロイ対象
        alt .env.enc が存在する
            DP->>DP: SOPS + age で復号 → .env
        end

        alt deploy.sh が存在する
            DP->>DP: bash deploy.sh を実行
        end

        DP->>SV: nginx conf 生成 + reload
        Note over SV: https://app.haregake-lab.com/sandbox/{repo}/ で公開
    end
```

## 公開 URL

`https://app.haregake-lab.com/sandbox/リポジトリ名/`

- リポジトリ名がそのまま URL のパスになります
- サンドボックスなのでアクセスにはユーザー名とパスワードが必要です

## やること

AI に「公開して」と伝えるだけ。AI が以下を実行します:

```bash
git add -A
git commit -m "変更内容の説明"
git push origin main
```

push 後 1〜2 分で自動デプロイが完了します。

## デプロイの確認

### デプロイログ

デプロイの進行状況やエラーは、ブラウザで確認できます。

`https://app.haregake-lab.com/deployer/logs/リポジトリ名/`

サンドボックスのユーザー名とパスワードが必要です。

### サーバー上のファイル確認

デプロイされたアプリのファイル（`.env` やログファイル等）をブラウザで閲覧できます（読み取り専用）。

`https://app.haregake-lab.com/deployer/files/リポジトリ名/`

デプロイ後にアプリが動かないときは、ここで `.env` の設定値や `storage/logs/laravel.log` のエラーを確認してください。
