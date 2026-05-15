@extends('layouts.app')

@section('title', '管理画面 - 問題編集')

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
            <h1 class="text-2xl font-bold text-white">問題を編集</h1>
            <a href="{{ route('problems.show', $problem) }}"
               class="ml-auto text-sm text-green-400 hover:text-green-300 border border-green-700 rounded-lg px-4 py-1.5 transition">
                ゲーム画面を確認
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-800 text-green-200 rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
        @endif

        {{-- 問題設定フォーム --}}
        <form action="{{ route('admin.problems.update', $problem) }}" method="POST" id="main-form">
            @csrf @method('PUT')

            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 mb-6">
                <h2 class="text-white font-bold text-lg mb-4">基本設定</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">問題文</label>
                        <textarea name="question_text" rows="2"
                                  class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-indigo-500 resize-none"
                                  >{{ old('question_text', $problem->question_text) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">カウントダウン秒数</label>
                        <input type="number" name="countdown_seconds" min="5" max="300"
                               value="{{ old('countdown_seconds', $problem->countdown_seconds) }}"
                               class="w-32 bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            {{-- スロット設定 --}}
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 mb-6">
                <h2 class="text-white font-bold text-lg mb-4">番号ごとの設定（1〜10）</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($problem->slots as $slot)
                    <div class="bg-gray-750 border border-gray-600 rounded-xl p-4" style="background:#1e293b;">
                        <div class="flex items-start gap-4">
                            {{-- 画像エリア --}}
                            <div class="shrink-0">
                                <div id="preview-{{ $slot->slot_number }}" class="mb-2">
                                    @if($slot->image_path)
                                        <img src="{{ $slot->imageUrl() }}"
                                             class="slot-img-preview"
                                             id="img-{{ $slot->slot_number }}">
                                    @else
                                        <div class="w-20 h-20 bg-gray-700 rounded-lg flex items-center justify-center text-gray-600"
                                             id="img-{{ $slot->slot_number }}">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <label class="upload-zone block text-center text-xs text-gray-500 hover:text-gray-300 px-2 py-1 cursor-pointer">
                                    <input type="file" accept="image/*" class="hidden"
                                           data-upload-url="{{ route('admin.problems.slots.image.upload', [$problem, $slot]) }}"
                                           data-slot="{{ $slot->slot_number }}"
                                           onchange="uploadImage(this)">
                                    画像を変更
                                </label>
                                @if($slot->image_path)
                                <button type="button"
                                        data-delete-url="{{ route('admin.problems.slots.image.delete', [$problem, $slot]) }}"
                                        data-slot="{{ $slot->slot_number }}"
                                        onclick="deleteImage(this)"
                                        class="w-full text-xs text-red-500 hover:text-red-400 mt-1">
                                    削除
                                </button>
                                @endif
                            </div>

                            {{-- テキスト設定 --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-indigo-400 font-black text-lg w-7">{{ $slot->slot_number }}</span>
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox"
                                               name="slots[{{ $slot->slot_number }}][is_correct]"
                                               value="1"
                                               {{ $slot->is_correct ? 'checked' : '' }}
                                               class="w-4 h-4 accent-green-500">
                                        <span class="text-green-400 text-xs font-semibold">正解</span>
                                    </label>
                                </div>
                                <label class="block text-gray-500 text-xs mb-1">解答テキスト</label>
                                <textarea name="slots[{{ $slot->slot_number }}][answer_text]"
                                          rows="2"
                                          placeholder="この番号の解答を入力..."
                                          class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-1.5 text-white text-sm focus:outline-none focus:border-indigo-500 resize-none"
                                          >{{ old("slots.{$slot->slot_number}.answer_text", $slot->answer_text) }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-between items-center">
                <form action="{{ route('admin.problems.destroy', $problem) }}" method="POST"
                      onsubmit="return confirm('この問題を削除しますか？')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="text-red-400 hover:text-red-300 border border-red-800 hover:border-red-600 rounded-lg px-5 py-2 transition text-sm">
                        問題を削除
                    </button>
                </form>
                <button type="submit" form="main-form"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-8 py-3 rounded-xl transition text-lg">
                    保存する
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function uploadImage(input) {
    if (!input.files[0]) return;
    const slotNum = input.dataset.slot;
    const url = input.dataset.uploadUrl;
    const formData = new FormData();
    formData.append('image', input.files[0]);
    formData.append('_token', CSRF);

    const res = await fetch(url, { method: 'POST', body: formData });
    const data = await res.json();
    if (data.url) {
        const container = document.getElementById(`img-${slotNum}`);
        container.outerHTML = `<img src="${data.url}" class="slot-img-preview" id="img-${slotNum}">`;
    }
}

async function deleteImage(btn) {
    if (!confirm('画像を削除しますか？')) return;
    const slotNum = btn.dataset.slot;
    const url = btn.dataset.deleteUrl;
    const res = await fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
    });
    const data = await res.json();
    if (data.ok) {
        const img = document.getElementById(`img-${slotNum}`);
        img.outerHTML = `<div class="w-20 h-20 bg-gray-700 rounded-lg flex items-center justify-center text-gray-600" id="img-${slotNum}">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>`;
        btn.remove();
    }
}
</script>
@endpush
