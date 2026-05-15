@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="flex items-center justify-center min-h-screen">
    <div class="max-w-2xl mx-auto px-6 py-16 text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            {{ config('app.name') }}
        </h1>
        <p class="text-lg text-gray-600 mb-8">
            Vibe Coding で作る、あなただけの Web アプリケーション
        </p>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-8 text-left">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">はじめかた</h2>
            <ol class="space-y-3 text-gray-600">
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">1</span>
                    <span><a href="https://github.com/HaregakeLab/vibe-scaffold-laravel" class="text-blue-600 hover:underline" target="_blank">GitHub</a> で「Use this template」から新しいリポジトリを作る</span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">2</span>
                    <span>AI ツールで開発を始める</span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">3</span>
                    <span>「公開して」と言えば自動でデプロイ</span>
                </li>
            </ol>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-2">Laravel 11</h3>
                <p class="text-sm text-gray-600">PHP のモダンなフレームワーク。シンプルで強力。</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-2">Tailwind CSS</h3>
                <p class="text-sm text-gray-600">クラスを書くだけで見た目を整えられる CSS。</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-2">SQLite</h3>
                <p class="text-sm text-gray-600">設定不要のデータベース。すぐに使い始められる。</p>
            </div>
        </div>

        <p class="mt-8 text-sm text-gray-400">
            このページは <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-500">resources/views/welcome.blade.php</code> を編集して変更できます
        </p>
    </div>
</div>
@endsection
