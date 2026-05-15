@extends('layouts.app')

@section('title', '管理画面 - 問題作成')

@section('content')
<div class="min-h-screen bg-gray-900 py-10 px-4">
    <div class="max-w-4xl mx-auto">

        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('admin.problems.index') }}" class="text-gray-500 hover:text-white transition">← 一覧へ</a>
            <h1 class="text-2xl font-bold text-white">新しい問題を作成</h1>
        </div>

        <form action="{{ route('admin.problems.store') }}" method="POST">
            @csrf

            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 mb-6">
                <h2 class="text-white font-bold text-lg mb-4">基本設定</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">問題文</label>
                        <textarea name="question_text" rows="2"
                                  placeholder="例：この写真は誰でしょう？"
                                  class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-indigo-500 resize-none"
                                  >{{ old('question_text') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">カウントダウン秒数</label>
                        <input type="number" name="countdown_seconds" min="5" max="300"
                               value="{{ old('countdown_seconds', 60) }}"
                               class="w-32 bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 mb-6">
                <h2 class="text-white font-bold text-lg mb-2">解答テキスト（1〜10）</h2>
                <p class="text-gray-500 text-sm mb-4">問題作成後に各番号の画像・解答・正解フラグを設定できます。ここでは解答テキストと正解フラグだけ入力してください。</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @for($i = 1; $i <= 10; $i++)
                    <div class="flex items-center gap-3 bg-gray-700 rounded-xl px-4 py-3">
                        <span class="text-indigo-400 font-black text-lg w-7 shrink-0">{{ $i }}</span>
                        <input type="text" name="slots[{{ $i }}][answer_text]"
                               placeholder="解答テキスト"
                               value="{{ old("slots.{$i}.answer_text") }}"
                               class="flex-1 bg-gray-600 border border-gray-500 rounded-lg px-3 py-1.5 text-white text-sm focus:outline-none focus:border-indigo-500">
                        <label class="flex items-center gap-1.5 shrink-0 cursor-pointer">
                            <input type="checkbox"
                                   name="slots[{{ $i }}][is_correct]"
                                   value="1"
                                   {{ old("slots.{$i}.is_correct") ? 'checked' : '' }}
                                   class="w-4 h-4 accent-green-500">
                            <span class="text-green-400 text-xs">正解</span>
                        </label>
                    </div>
                    @endfor
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-8 py-3 rounded-xl transition text-lg">
                    作成する（次に画像を設定）
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
