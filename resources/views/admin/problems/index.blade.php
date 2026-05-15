@extends('layouts.app')

@section('title', '管理画面 - 問題一覧')

@section('content')
<div class="min-h-screen bg-gray-900 py-10 px-4">
    <div class="max-w-4xl mx-auto">

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-white">問題一覧</h1>
            <div class="flex gap-3">
                <a href="{{ route('problems.index') }}"
                   class="text-gray-400 hover:text-white text-sm border border-gray-600 hover:border-gray-400 rounded-lg px-4 py-2 transition">
                    ゲーム画面を見る
                </a>
                <a href="{{ route('admin.problems.create') }}"
                   class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-5 py-2 rounded-lg transition">
                    + 新しい問題を作成
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-800 text-green-200 rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
        @endif

        @if($problems->isEmpty())
            <div class="text-center text-gray-500 py-20">
                <p class="text-xl mb-4">まだ問題がありません</p>
                <a href="{{ route('admin.problems.create') }}"
                   class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-8 rounded-xl transition">
                    最初の問題を作成する
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($problems as $i => $problem)
                <div class="bg-gray-800 border border-gray-700 rounded-xl px-5 py-4 flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-semibold truncate">Q{{ $i+1 }}. {{ $problem->question_text }}</p>
                        <p class="text-gray-500 text-sm mt-1">カウントダウン {{ $problem->countdown_seconds }}秒</p>
                    </div>
                    <div class="flex gap-2 ml-4 shrink-0">
                        <a href="{{ route('problems.show', $problem) }}"
                           class="text-xs text-green-400 hover:text-green-300 border border-green-700 hover:border-green-500 rounded-lg px-3 py-1.5 transition">
                            プレイ
                        </a>
                        <a href="{{ route('admin.problems.edit', $problem) }}"
                           class="text-xs text-blue-400 hover:text-blue-300 border border-blue-700 hover:border-blue-500 rounded-lg px-3 py-1.5 transition">
                            編集
                        </a>
                        <form action="{{ route('admin.problems.destroy', $problem) }}" method="POST"
                              onsubmit="return confirm('この問題を削除しますか？')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-400 hover:text-red-300 border border-red-800 hover:border-red-600 rounded-lg px-3 py-1.5 transition">
                                削除
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
