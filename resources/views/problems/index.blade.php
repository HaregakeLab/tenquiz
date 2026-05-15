@extends('layouts.app')

@section('title', '問題選択')

@section('content')
<div class="min-h-screen bg-gray-900 flex flex-col items-center justify-center px-4 py-12">
    <h1 class="text-4xl font-extrabold text-white mb-2 tracking-widest drop-shadow-lg">QUIZ GAME</h1>
    <p class="text-gray-400 mb-10 text-lg">問題を選んでください</p>

    @if($problems->isEmpty())
        <div class="text-gray-500 text-center">
            <p class="text-xl mb-4">問題がまだありません</p>
            <a href="{{ route('admin.problems.create') }}"
               class="inline-block bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-8 rounded-xl transition">
                管理画面で問題を作成する
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-4xl">
            @foreach($problems as $problem)
            <a href="{{ route('problems.show', $problem) }}"
               class="group bg-gray-800 hover:bg-indigo-700 border border-gray-700 hover:border-indigo-500 rounded-2xl p-6 flex flex-col items-center transition-all duration-200 shadow-lg hover:shadow-indigo-500/30 hover:scale-105 cursor-pointer">
                <div class="text-5xl font-black text-indigo-400 group-hover:text-white mb-4 transition">
                    Q{{ $loop->iteration }}
                </div>
                <p class="text-white text-center text-base font-semibold leading-relaxed mb-4 line-clamp-3">
                    {{ $problem->question_text }}
                </p>
                <div class="flex items-center gap-2 text-gray-400 group-hover:text-indigo-200 text-sm mt-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $problem->countdown_seconds }}秒
                </div>
            </a>
            @endforeach
        </div>
    @endif

    <a href="{{ route('admin.problems.index') }}"
       class="mt-12 text-gray-600 hover:text-gray-400 text-sm transition underline">
        管理画面
    </a>
</div>
@endsection
