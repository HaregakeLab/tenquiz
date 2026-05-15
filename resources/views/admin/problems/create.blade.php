@extends('layouts.app')

@section('title', '管理画面 - 問題作成')

@push('styles')
<style>
    .slot-img-preview { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
    .upload-zone { border: 2px dashed #4b5563; border-radius: 8px; transition: border-color 0.2s; }
    .upload-zone:hover { border-color: #6366f1; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-900 py-10 px-4">
    <div class="max-w-4xl mx-auto">

        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('admin.problems.index') }}" class="text-gray-500 hover:text-white transition">← 一覧へ</a>
            <h1 class="text-2xl font-bold text-white">新しい問題を作成</h1>
        </div>

        <form action="{{ route('admin.problems.store') }}" method="POST" enctype="multipart/form-data">
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
                <h2 class="text-white font-bold text-lg mb-4">番号ごとの設定（1〜10）</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @for($i = 1; $i <= 10; $i++)
                    <div class="border border-gray-600 rounded-xl p-4" style="background:#1e293b;">
                        <div class="flex items-start gap-4">
                            {{-- 画像エリア --}}
                            <div class="shrink-0">
                                <div class="mb-2">
                                    <div class="w-20 h-20 bg-gray-700 rounded-lg flex items-center justify-center text-gray-600"
                                         id="preview-{{ $i }}">
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <label class="upload-zone block text-center text-xs text-gray-500 hover:text-gray-300 px-2 py-1 cursor-pointer">
                                    <input type="file" accept="image/*" name="images[{{ $i }}]"
                                           data-preview="preview-{{ $i }}"
                                           onchange="previewImage(this)"
                                           class="hidden">
                                    画像を選択
                                </label>
                            </div>

                            {{-- テキスト設定 --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-indigo-400 font-black text-lg w-7">{{ $i }}</span>
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox"
                                               name="slots[{{ $i }}][is_correct]"
                                               value="1"
                                               {{ old("slots.{$i}.is_correct") ? 'checked' : '' }}
                                               class="w-4 h-4 accent-green-500">
                                        <span class="text-green-400 text-xs font-semibold">正解</span>
                                    </label>
                                </div>
                                <label class="block text-gray-500 text-xs mb-1">解答テキスト</label>
                                <textarea name="slots[{{ $i }}][answer_text]"
                                          rows="2"
                                          placeholder="この番号の解答を入力..."
                                          class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-1.5 text-white text-sm focus:outline-none focus:border-indigo-500 resize-none"
                                          >{{ old("slots.{$i}.answer_text") }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-8 py-3 rounded-xl transition text-lg">
                    作成する
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (!input.files[0]) return;
    const previewId = input.dataset.preview;
    const reader = new FileReader();
    reader.onload = e => {
        const container = document.getElementById(previewId);
        container.outerHTML = `<img src="${e.target.result}" class="slot-img-preview" id="${previewId}">`;
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
