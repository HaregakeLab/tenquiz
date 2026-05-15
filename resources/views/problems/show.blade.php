@extends('layouts.app')

@section('title', 'クイズゲーム')

@push('styles')
<style>
    body { background: #0f0f1a; overflow: hidden; }

    /* グリッドセル */
    .slot-cell {
        position: relative;
        cursor: pointer;
        border-radius: 12px;
        overflow: hidden;
        background: #1e1e33;
        border: 2px solid #2d2d50;
        transition: border-color 0.2s, transform 0.15s;
        aspect-ratio: 1;
    }
    .slot-cell:hover { border-color: #6366f1; transform: scale(1.02); }

    .slot-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .slot-no-img {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4b4b70;
        font-size: 2rem;
    }

    /* 番号バッジ */
    .slot-number {
        position: absolute;
        top: 6px;
        left: 6px;
        background: rgba(0,0,0,0.75);
        color: #fff;
        font-weight: 800;
        font-size: 0.85rem;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
        z-index: 2;
    }

    /* 番号ボタン */
    .answer-btn {
        position: absolute;
        bottom: 6px;
        right: 6px;
        background: rgba(99,102,241,0.85);
        color: #fff;
        font-weight: 900;
        font-size: 0.8rem;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        backdrop-filter: blur(4px);
        z-index: 3;
        transition: background 0.15s, transform 0.1s;
    }
    .answer-btn:hover { background: rgba(99,102,241,1); transform: scale(1.1); }

    /* カウントダウン */
    .countdown-bar {
        height: 8px;
        border-radius: 4px;
        background: linear-gradient(to right, #10b981, #f59e0b, #ef4444);
        transition: width 1s linear;
    }

    /* ライトボックス */
    #lightbox {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.92);
        z-index: 100;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    #lightbox.open { display: flex; }
    #lightbox img { max-width: 90vw; max-height: 85vh; object-fit: contain; border-radius: 12px; box-shadow: 0 0 60px rgba(99,102,241,0.4); }

    /* 結果オーバーレイ */
    #result-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 90;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
        padding: 2rem;
        backdrop-filter: blur(8px);
    }
    #result-overlay.open { display: flex; }
    #result-overlay.correct { background: rgba(16,185,129,0.85); }
    #result-overlay.incorrect { background: rgba(239,68,68,0.85); }
    #result-overlay.timeout { background: rgba(107,114,128,0.85); }

    @keyframes popIn {
        0% { transform: scale(0.3); opacity: 0; }
        70% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }
    .pop-in { animation: popIn 0.4s ease forwards; }

    @keyframes shake {
        0%,100% { transform: translateX(0); }
        20% { transform: translateX(-12px); }
        40% { transform: translateX(12px); }
        60% { transform: translateX(-8px); }
        80% { transform: translateX(8px); }
    }
    .shake { animation: shake 0.5s ease; }

    /* タイムアップ警告 */
    @keyframes pulse-red {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
        50% { box-shadow: 0 0 0 8px rgba(239,68,68,0.4); }
    }
    .timer-danger { animation: pulse-red 0.7s infinite; border-color: #ef4444 !important; color: #ef4444; }
</style>
@endpush

@section('content')
<div class="min-h-screen flex flex-col" id="game-root">

    {{-- ヘッダー: 問題文 + タイマー --}}
    <div class="bg-gray-900 border-b border-gray-700 px-4 py-3">
        <div class="max-w-3xl mx-auto">
            <p class="text-white text-center text-lg sm:text-xl font-bold leading-snug mb-3">
                {{ $problem->question_text }}
            </p>
            <div class="flex items-center gap-3">
                <span id="timer-display"
                      class="text-2xl font-black text-green-400 w-16 text-center border-2 border-green-400 rounded-lg py-0.5 transition-all">
                    {{ $problem->countdown_seconds }}
                </span>
                <div class="flex-1 bg-gray-700 rounded-full overflow-hidden h-3">
                    <div id="timer-bar" class="countdown-bar" style="width:100%"></div>
                </div>
                <button onclick="toggleBgm()" id="bgm-btn"
                        class="text-xs text-gray-500 hover:text-white border border-gray-600 hover:border-gray-400 rounded px-2 py-1 transition">
                    ♪ BGM
                </button>
            </div>
        </div>
    </div>

    {{-- 画像グリッド 5×2 --}}
    <div class="flex-1 flex items-center justify-center bg-gray-900 p-4">
        <div class="w-full max-w-3xl">
            <div class="grid grid-cols-5 gap-2 sm:gap-3">
                @foreach($problem->slots as $slot)
                <div class="slot-cell" id="cell-{{ $slot->slot_number }}">
                    {{-- 番号バッジ --}}
                    <div class="slot-number">{{ $slot->slot_number }}</div>

                    {{-- 画像または空欄 --}}
                    @if($slot->image_path)
                        <img src="{{ $slot->imageUrl() }}"
                             alt="{{ $slot->slot_number }}"
                             class="slot-img"
                             onclick="openLightbox('{{ $slot->imageUrl() }}')"
                             style="cursor:zoom-in;">
                    @else
                        <div class="slot-no-img">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    {{-- 番号ボタン（解答） --}}
                    <button class="answer-btn" onclick="answerSlot({{ $slot->slot_number }})">
                        {{ $slot->slot_number }}
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- フッター --}}
    <div class="bg-gray-900 border-t border-gray-700 py-2 text-center">
        <a href="{{ route('problems.index') }}" class="text-gray-600 hover:text-gray-400 text-xs transition">
            ← 問題選択に戻る
        </a>
    </div>
</div>

{{-- ライトボックス --}}
<div id="lightbox" onclick="closeLightbox()">
    <img id="lightbox-img" src="" alt="">
</div>

{{-- 結果オーバーレイ --}}
<div id="result-overlay">
    <div class="pop-in">
        <div id="result-emoji" class="text-8xl mb-4"></div>
        <div id="result-label" class="text-4xl font-black text-white mb-4 drop-shadow-lg"></div>
        <div id="result-answer" class="text-2xl font-bold text-white bg-black bg-opacity-30 rounded-2xl px-8 py-4 max-w-lg"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const TOTAL_SECONDS = {{ $problem->countdown_seconds }};
const CORRECT_SLOTS = @json($problem->slots->where('is_correct', true)->pluck('slot_number')->values());
const ANSWERS = @json($problem->slots->pluck('answer_text', 'slot_number'));

let timeLeft = TOTAL_SECONDS;
let timerInterval = null;
let answered = false;
let bgmPlaying = false;
let audioCtx = null;
let bgmNodes = [];
let bgmScheduled = false;

// ---- タイマー ----
function startTimer() {
    const bar = document.getElementById('timer-bar');
    const display = document.getElementById('timer-display');
    timerInterval = setInterval(() => {
        timeLeft--;
        const pct = (timeLeft / TOTAL_SECONDS) * 100;
        bar.style.width = pct + '%';
        display.textContent = timeLeft;

        if (timeLeft <= 10) {
            display.classList.add('timer-danger');
            display.classList.remove('text-green-400');
        }

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            if (!answered) showTimeout();
        }
    }, 1000);
}

// ---- 解答 ----
function answerSlot(num) {
    if (answered) return;
    answered = true;
    clearInterval(timerInterval);
    stopBgm();

    const isCorrect = CORRECT_SLOTS.includes(num);
    const answerText = ANSWERS[num] || '';

    if (isCorrect) {
        showResult('correct', '正　解！', answerText);
        playCorrectSound();
    } else {
        showResult('incorrect', 'ざんねん…', answerText);
        playIncorrectSound();
    }
}

function showResult(type, label, answer) {
    const overlay = document.getElementById('result-overlay');
    const emoji = document.getElementById('result-emoji');
    const labelEl = document.getElementById('result-label');
    const ansEl = document.getElementById('result-answer');

    overlay.className = 'open ' + type;
    emoji.textContent = type === 'correct' ? '⭐' : (type === 'timeout' ? '⏰' : '✗');
    labelEl.textContent = label;
    ansEl.textContent = answer || '　';

    if (type === 'incorrect') {
        document.getElementById('game-root').classList.add('shake');
        setTimeout(() => document.getElementById('game-root').classList.remove('shake'), 600);
    }

    const delay = type === 'correct' ? 3500 : 2800;
    setTimeout(() => {
        overlay.className = '';
        answered = false;
        timeLeft = TOTAL_SECONDS;
        document.getElementById('timer-display').textContent = TOTAL_SECONDS;
        document.getElementById('timer-bar').style.width = '100%';
        document.getElementById('timer-display').classList.remove('timer-danger');
        document.getElementById('timer-display').classList.add('text-green-400');
        startTimer();
        startBgm();
    }, delay);
}

function showTimeout() {
    answered = true;
    stopBgm();
    const correct = CORRECT_SLOTS.map(n => ANSWERS[n] || '').filter(Boolean).join('・');
    showResult('timeout', '時間切れ！', correct ? '答え: ' + correct : '');
    playIncorrectSound();
}

// ---- ライトボックス ----
function openLightbox(url) {
    document.getElementById('lightbox-img').src = url;
    document.getElementById('lightbox').classList.add('open');
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

// ---- BGM (Web Audio API) ----
// ゲームショー風・カウントダウン系の明るいループメロディ
const MELODY = [
    // 音符: [note, octave, duration(秒)]
    ['C',5,0.25],['E',5,0.25],['G',5,0.25],['C',6,0.25],
    ['B',5,0.125],['A',5,0.125],['G',5,0.25],['E',5,0.25],
    ['F',5,0.25],['A',5,0.25],['C',6,0.25],['A',5,0.25],
    ['G',5,0.25],['B',5,0.25],['D',6,0.25],['G',5,0.5],
    ['E',5,0.25],['G',5,0.25],['C',6,0.25],['E',6,0.25],
    ['D',6,0.25],['C',6,0.25],['B',5,0.25],['A',5,0.25],
    ['G',5,0.25],['A',5,0.25],['B',5,0.25],['C',6,0.25],
    ['G',5,0.5],['C',6,0.5],['rest',0,0.5],
];

const NOTE_FREQ = { C:261.63, D:293.66, E:329.63, F:349.23, G:392.00, A:440.00, B:493.88 };

function getNoteFreq(note, octave) {
    if (note === 'rest') return 0;
    const base = NOTE_FREQ[note];
    const diff = octave - 4;
    return base * Math.pow(2, diff);
}

function getLoopDuration() {
    return MELODY.reduce((s, n) => s + n[2], 0);
}

function scheduleBgm(ctx, startTime) {
    let t = startTime;
    MELODY.forEach(([note, oct, dur]) => {
        if (note !== 'rest') {
            const freq = getNoteFreq(note, oct);
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(freq, t);
            gain.gain.setValueAtTime(0.18, t);
            gain.gain.exponentialRampToValueAtTime(0.001, t + dur * 0.9);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(t);
            osc.stop(t + dur);
            bgmNodes.push(osc);
        }
        t += dur;
    });
    return t;
}

function startBgm() {
    if (bgmPlaying) return;
    if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    bgmPlaying = true;
    document.getElementById('bgm-btn').textContent = '♪ 停止';

    const loopDur = getLoopDuration();
    let nextStart = audioCtx.currentTime;

    function scheduleLoop() {
        if (!bgmPlaying) return;
        nextStart = scheduleBgm(audioCtx, nextStart);
        bgmScheduled = setTimeout(scheduleLoop, (nextStart - audioCtx.currentTime - 0.3) * 1000);
    }
    scheduleLoop();
}

function stopBgm() {
    bgmPlaying = false;
    clearTimeout(bgmScheduled);
    bgmNodes.forEach(n => { try { n.stop(); } catch(e){} });
    bgmNodes = [];
    const btn = document.getElementById('bgm-btn');
    if (btn) btn.textContent = '♪ BGM';
}

function toggleBgm() {
    if (bgmPlaying) stopBgm();
    else startBgm();
}

// ---- サウンドエフェクト ----
function playCorrectSound() {
    const ctx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
    [[523,0],[659,0.15],[784,0.3],[1047,0.45]].forEach(([f, d]) => {
        const osc = ctx.createOscillator();
        const g = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.value = f;
        g.gain.setValueAtTime(0.3, ctx.currentTime + d);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + d + 0.4);
        osc.connect(g); g.connect(ctx.destination);
        osc.start(ctx.currentTime + d);
        osc.stop(ctx.currentTime + d + 0.5);
    });
}

function playIncorrectSound() {
    const ctx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const g = ctx.createGain();
    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(300, ctx.currentTime);
    osc.frequency.exponentialRampToValueAtTime(80, ctx.currentTime + 0.6);
    g.gain.setValueAtTime(0.25, ctx.currentTime);
    g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
    osc.connect(g); g.connect(ctx.destination);
    osc.start(); osc.stop(ctx.currentTime + 0.7);
}

// ---- 初期化 ----
// BGMはユーザー操作後に開始（ブラウザ制限対応）
document.addEventListener('click', function startOnce() {
    startTimer();
    startBgm();
    document.removeEventListener('click', startOnce);
}, { once: true });

// BGMボタン以外の最初のクリックで自動スタート
window.addEventListener('load', () => {
    // タイマーはページ読み込み後すぐ開始（操作なしでも）
    setTimeout(() => {
        if (!timerInterval) startTimer();
    }, 500);
});
</script>
@endpush
