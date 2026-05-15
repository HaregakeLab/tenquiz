<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>CLEAR!</title>

    @if(config('app.debug'))
        <script src="https://cdn.tailwindcss.com"></script>
    @else
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @endif

    <style>
        @keyframes floatUp {
            0%   { opacity: 0; transform: translateY(40px) scale(0.8); }
            100% { opacity: 1; transform: translateY(0)    scale(1); }
        }
        @keyframes starSpin {
            0%   { transform: rotate(0deg)   scale(0); opacity: 0; }
            50%  { transform: rotate(180deg) scale(1.3); opacity: 1; }
            100% { transform: rotate(360deg) scale(1); opacity: 1; }
        }
        @keyframes confettiFall {
            0%   { transform: translateY(-20px) rotate(0deg);   opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
        .float-up  { animation: floatUp 0.7s cubic-bezier(0.34,1.56,0.64,1) forwards; }
        .star-spin { animation: starSpin 0.8s cubic-bezier(0.34,1.56,0.64,1) 0.2s both; }
        .confetti  { position: fixed; top: -20px; animation: confettiFall linear infinite; pointer-events: none; }
    </style>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center overflow-hidden select-none">

    <div class="text-center px-6" id="clear-content" style="opacity:0">
        <div class="text-8xl mb-6 star-spin">⭐</div>
        <h1 class="text-7xl font-extrabold text-amber-400 tracking-widest mb-4 drop-shadow-lg">CLEAR!</h1>
        <p class="text-2xl text-white mb-12 font-semibold">正解です！おめでとう！</p>

        <a
            href="{{ rtrim(parse_url(url('/'), PHP_URL_PATH), '/') }}/"
            class="inline-block px-12 py-5 bg-amber-500 hover:bg-amber-400 active:scale-95
                   text-gray-900 text-xl font-bold rounded-full transition-all duration-150
                   shadow-lg shadow-amber-500/40"
        >もう一度挑戦する</a>
    </div>

    <script>
        // コンテンツをフェードイン
        setTimeout(() => {
            const el = document.getElementById('clear-content');
            el.style.opacity = '1';
            el.classList.add('float-up');
        }, 100);

        // 正解音
        (function() {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            [[523.25,0],[659.25,0.15],[783.99,0.3],[1046.50,0.45],[1318.51,0.65]].forEach(([freq, t]) => {
                const osc  = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.value = freq;
                const at = ctx.currentTime + t;
                gain.gain.setValueAtTime(0, at);
                gain.gain.linearRampToValueAtTime(0.4, at + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.001, at + 0.6);
                osc.start(at);
                osc.stop(at + 0.6);
            });
        })();

        // 紙吹雪
        const colors = ['#fbbf24','#34d399','#60a5fa','#f87171','#a78bfa','#fb923c'];
        for (let i = 0; i < 60; i++) {
            const el = document.createElement('div');
            el.className = 'confetti';
            el.style.left        = Math.random() * 100 + 'vw';
            el.style.width       = (8 + Math.random() * 8) + 'px';
            el.style.height      = (8 + Math.random() * 8) + 'px';
            el.style.background  = colors[Math.floor(Math.random() * colors.length)];
            el.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
            el.style.animationDuration  = (2 + Math.random() * 3) + 's';
            el.style.animationDelay     = (Math.random() * 2) + 's';
            document.body.appendChild(el);
        }
    </script>
</body>
</html>
