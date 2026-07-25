<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Donation Goal — {{ $streamer->display_name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-lao:400,500,600,700,800,900" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 100%;
            height: 100%;
            background: transparent !important;
            overflow: hidden;
            font-family: 'Noto Sans Lao', system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #widget-container {
            width: 760px;
            height: 170px;
            padding: 20px 28px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.5s ease;

            /* ลบพื้นหลัง/ขอบ/เงาของกล่องการ์ดทุกธีม เหลือแค่หลอดกับข้อความ
               (!important เพื่อทับทั้ง theme class และสไตล์ inline ของธีม custom) */
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
        }

        /* Celebration canvas overlay */
        #celebration-canvas {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 40;
        }

        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 26px;
            font-weight: 800;
            line-height: 1.2;
            z-index: 10;
        }
        
        .goal-title-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .goal-amounts {
            font-family: monospace;
            font-size: 22px;
            font-weight: 700;
        }
        
        .currency-symbol {
            font-size: 18px;
            font-weight: 800;
            margin-left: 2px;
        }

        /* Progress Bar Outer */
        .progress-outer {
            width: 100%;
            height: 52px;
            border-radius: 12px;
            padding: 4px;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            z-index: 10;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Progress Bar Inner */
        .progress-inner {
            height: 100%;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            width: 0%; /* Dynamic width */
            transition: width 1s cubic-bezier(0.1, 0.8, 0.2, 1);
        }

        .progress-overlay {
            position: absolute;
            inset: 0;
            opacity: 0.5;
            pointer-events: none;
        }

        /* Percent Text */
        .percent-text {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: end;
            padding-right: 24px;
            font-size: 24px;
            font-weight: 900;
            font-family: monospace;
            z-index: 20;
            pointer-events: none;
        }

        /* Bottom Text */
        .bottom-row {
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            z-index: 10;
        }

        /* Celebration text popup */
        .celebration-popup {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.7);
            z-index: 50;
            border-radius: inherit;
            color: #ffd700;
            font-weight: 900;
            text-align: center;
            opacity: 0;
            transform: scale(0.9);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: none;
        }
        .celebration-popup.show {
            opacity: 1;
            transform: scale(1);
        }
        .celebration-popup h2 {
            font-size: 28px;
            margin-bottom: 4px;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
        }
        .celebration-popup p {
            font-size: 16px;
            color: #ffffff;
        }

        /* ==========================================================================
           THEME DEFINITIONS
           ========================================================================== */

        /* 1. Water Flow */
        .theme-water-flow {
            background: linear-gradient(180deg, #02142d 0%, #05336b 100%);
            border: 2px solid #00b0f0;
            box-shadow: 0 0 30px rgba(0, 176, 240, 0.45);
        }
        .theme-water-flow .header-row, .theme-water-flow .bottom-row { color: #ffffff; }
        .theme-water-flow .goal-title-wrap { color: #80e5ff; }
        .theme-water-flow .currency-symbol { color: #00e5ff; }
        .theme-water-flow .progress-outer { background: #072242; border-color: rgba(0, 176, 240, 0.2); }
        .theme-water-flow .progress-inner { background: linear-gradient(90deg, #0050ef 0%, #00d0ff 100%); }
        .theme-water-flow .percent-text { color: #ffffff; text-shadow: 0 0 8px #0050ef; }

        /* 2. Glassmorphism */
        .theme-glassmorphism {
            background: rgba(18, 10, 36, 0.55);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3), inset 0 1px 1px rgba(255, 255, 255, 0.1);
            border-radius: 20px !important;
        }
        .theme-glassmorphism .header-row, .theme-glassmorphism .bottom-row { color: #ffffff; }
        .theme-glassmorphism .goal-title-wrap { color: #d4bbf9; }
        .theme-glassmorphism .currency-symbol { color: #c084fc; }
        .theme-glassmorphism .progress-outer { background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.05); }
        .theme-glassmorphism .progress-inner { background: linear-gradient(90deg, #7c3aed 0%, #c084fc 100%); }
        .theme-glassmorphism .percent-text { color: #ffffff; text-shadow: 0 0 8px rgba(124, 58, 237, 0.6); }

        /* 3. Golden */
        .theme-golden {
            background: linear-gradient(135deg, #1f1101 0%, #0a0500 100%);
            border: 3px double #d4af37;
            box-shadow: 0 15px 40px rgba(212, 175, 55, 0.25);
        }
        .theme-golden .header-row, .theme-golden .bottom-row { color: #ffffff; }
        .theme-golden .goal-title-wrap { color: #f3c63f; }
        .theme-golden .currency-symbol { color: #d4af37; }
        .theme-golden .progress-outer { background: #231505; border-color: rgba(212, 175, 55, 0.2); }
        .theme-golden .progress-inner { background: linear-gradient(90deg, #b8860b 0%, #ffd700 100%); }
        .theme-golden .percent-text { color: #ffffff; text-shadow: 0 0 8px rgba(184, 134, 11, 0.8); }

        /* 4. Gaming Neon */
        .theme-gaming-neon {
            background: #020702;
            border: 2px solid #00ff66;
            box-shadow: 0 0 25px rgba(0, 255, 102, 0.5);
        }
        .theme-gaming-neon .header-row, .theme-gaming-neon .bottom-row { color: #ffffff; }
        .theme-gaming-neon .goal-title-wrap { color: #39ff14; }
        .theme-gaming-neon .currency-symbol { color: #39ff14; }
        .theme-gaming-neon .progress-outer { background: #081608; border-color: rgba(0, 255, 102, 0.2); }
        .theme-gaming-neon .progress-inner { background: linear-gradient(90deg, #008f11 0%, #00ff66 100%); }
        .theme-gaming-neon .percent-text { color: #ffffff; text-shadow: 0 0 10px #00ff66; }

        /* 5. Space */
        .theme-space {
            background: linear-gradient(135deg, #070114 0%, #010003 100%);
            border: 1px solid #6b21a8;
            box-shadow: 0 0 35px rgba(107, 33, 168, 0.4);
        }
        .theme-space .header-row, .theme-space .bottom-row { color: #ffffff; }
        .theme-space .goal-title-wrap { color: #c084fc; }
        .theme-space .currency-symbol { color: #a855f7; }
        .theme-space .progress-outer { background: #0c051a; border-color: rgba(107, 33, 168, 0.2); }
        .theme-space .progress-inner { background: linear-gradient(90deg, #6b21a8 0%, #ec4899 100%); }
        .theme-space .percent-text { color: #ffffff; text-shadow: 0 0 8px #a855f7; }

        /* 6. Cute Pastel */
        .theme-cute-pastel {
            background: linear-gradient(180deg, #fff2f4 0%, #ffd1da 100%);
            border: 2px solid #ff9ebb;
            box-shadow: 0 10px 25px rgba(255, 158, 187, 0.35);
        }
        .theme-cute-pastel .header-row { color: #ff5e84; }
        .theme-cute-pastel .bottom-row { color: #ff5e84; }
        .theme-cute-pastel .goal-title-wrap { color: #e7365f; }
        .theme-cute-pastel .currency-symbol { color: #ff5e84; }
        .theme-cute-pastel .progress-outer { background: #ffe4e9; border-color: rgba(255, 158, 187, 0.25); }
        .theme-cute-pastel .progress-inner { background: linear-gradient(90deg, #ff7192 0%, #ffa3b9 100%); }
        .theme-cute-pastel .percent-text { color: #ffffff; text-shadow: 0 1px 3px rgba(0,0,0,0.3); }

        /* 7. Lava */
        .theme-lava {
            background: linear-gradient(135deg, #1a0101 0%, #030000 100%);
            border: 2px solid #ea580c;
            box-shadow: 0 0 30px rgba(234, 88, 12, 0.5);
        }
        .theme-lava .header-row, .theme-lava .bottom-row { color: #ffffff; }
        .theme-lava .goal-title-wrap { color: #f97316; }
        .theme-lava .currency-symbol { color: #ea580c; }
        .theme-lava .progress-outer { background: #2c0803; border-color: rgba(234, 88, 12, 0.2); }
        .theme-lava .progress-inner { background: linear-gradient(90deg, #9a3412 0%, #ea580c 100%); }
        .theme-lava .percent-text { color: #ffffff; text-shadow: 0 0 10px #ea580c; }

        /* 8. Minimal */
        .theme-minimal {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 12px !important;
        }
        .theme-minimal .header-row { color: #1e293b; }
        .theme-minimal .bottom-row { color: #64748b; }
        .theme-minimal .goal-title-wrap { color: #0f172a; }
        .theme-minimal .currency-symbol { color: #0284c7; }
        .theme-minimal .progress-outer { background: #f1f5f9; border-color: #e2e8f0; }
        .theme-minimal .progress-inner { background: linear-gradient(90deg, #0284c7 0%, #06b6d4 100%); }
        .theme-minimal .percent-text { color: #ffffff; text-shadow: 0 1px 2px rgba(0,0,0,0.3); }

        /* 9. Nature */
        .theme-nature {
            background: linear-gradient(180deg, #072009 0%, #020c03 100%);
            border: 2px solid #16a34a;
            box-shadow: 0 0 25px rgba(22, 163, 74, 0.4);
        }
        .theme-nature .header-row, .theme-nature .bottom-row { color: #ffffff; }
        .theme-nature .goal-title-wrap { color: #4ade80; }
        .theme-nature .currency-symbol { color: #22c55e; }
        .theme-nature .progress-outer { background: #0b1c0d; border-color: rgba(22, 163, 74, 0.2); }
        .theme-nature .progress-inner { background: linear-gradient(90deg, #15803d 0%, #4ade80 100%); }
        .theme-nature .percent-text { color: #ffffff; text-shadow: 0 0 8px #15803d; }


        /* ==========================================================================
           PROGRESS STYLES & EFFECTS
           ========================================================================== */

        /* Progress style: water (waves) */
        .style-water {
            background: radial-gradient(circle at 50% 120%, rgba(255,255,255,0.4) 0%, transparent 80%);
            background-size: 20px 20px;
            animation: wave-animation 3s linear infinite;
        }

        /* Progress style: wave (diagonals) */
        .style-wave {
            background: repeating-linear-gradient(45deg, rgba(255,255,255,0.15), rgba(255,255,255,0.15) 10px, transparent 10px, transparent 20px);
            background-size: 40px 40px;
            animation: wave-animation 4s linear infinite;
        }

        /* Progress style: pixel */
        .style-pixel {
            background-image: linear-gradient(rgba(255,255,255,0.15) 50%, transparent 50%), linear-gradient(90deg, rgba(255,255,255,0.15) 50%, transparent 50%);
            background-size: 4px 4px;
        }

        /* Progress style: segmented */
        .style-segmented {
            background: repeating-linear-gradient(90deg, transparent, transparent 18px, rgba(0,0,0,0.3) 18px, rgba(0,0,0,0.3) 22px);
        }

        /* Effect: Shine overlay anim */
        .effect-shine::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transform: translateX(-100%) rotate(15deg);
            animation: shine-animation 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        /* Effect: Pulse card animation */
        .effect-pulse-card {
            animation: pulse-effect 2s infinite ease-in-out;
        }

        /* Easing animations */
        @keyframes wave-animation {
            0% { background-position-x: 0px; }
            100% { background-position-x: 400px; }
        }
        @keyframes shine-animation {
            0% { transform: translateX(-100%) rotate(15deg); }
            100% { transform: translateX(100%) rotate(15deg); }
        }
        @keyframes pulse-effect {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.01); }
        }
    </style>
</head>
<body>

    <div id="widget-container" class="theme-minimal">
        <canvas id="celebration-canvas"></canvas>

        {{-- Celebration overlay popup --}}
        <div id="celebration-popup" class="celebration-popup">
            <h2 id="celebration-title">ບັນລຸເປົ້າໝາຍແລ້ວ! 🎉</h2>
            <p>ຂອບໃຈທຸກການສະໜັບສະໜູນ!</p>
        </div>

        {{-- Header row --}}
        <div class="header-row" id="header-row">
            <div class="goal-title-wrap" id="goal-title-wrap">
                <span id="goal-icon">🎯</span>
                <span id="goal-title">ຊື້ໄມໂຄຣໂຟນໃໝ່</span>
            </div>
            <div class="goal-amounts" id="goal-amounts-wrap">
                <span id="current-amount-txt">0</span>
                <span>/</span>
                <span id="target-amount-txt">100,000</span>
                <span class="currency-symbol" id="currency-symbol">LAK</span>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="progress-outer" id="progress-outer">
            <div class="progress-inner" id="progress-inner">
                <div class="progress-overlay" id="progress-overlay"></div>
            </div>
            <div class="percent-text" id="percent-text">0.0%</div>
        </div>

        {{-- Bottom Row --}}
        <div class="bottom-row" id="bottom-row">
            ຂອບໃຈທຸກການສະໜັບສະໜູນ! ❤️
        </div>
    </div>

    <script>
        const API_URL = @json(route('api.widget.goal.check', $streamer->overlay_key));
        const CURRENCY = @json($streamer->currency);
        
        let goalData = {
            title: 'ຊື້ໄມໂຄຣໂຟນໃໝ່',
            target_amount: 100000,
            current_amount: 0,
            bottom_text: 'ຂອບໃຈທຸກການສະໜັບສະໜູນ! ❤️',
            theme_id: 'minimal',
            progress_style: 'fill',
            effects: [],
            show_title: true,
            show_current: true,
            show_target: true,
            show_percentage: true,
            show_bottom_text: true,
            celebration_enabled: true,
            celebration_type: 'confetti',
            celebration_message: 'ບັນລຸເປົ້າໝາຍແລ້ວ! 🎉',
            
            // Custom styles
            primary_color: '#8b5cf6',
            secondary_color: '#ec4899',
            background_style: 'rgba(17,17,27,0.9)',
            border_style: '1px solid rgba(255,255,255,.12)',
            border_radius: 16,
            shadow_style: '0 18px 50px rgba(0,0,0,.5)',
            font_family: "'Noto Sans Lao', sans-serif",
            font_weight: '700',
            font_color: '#ffffff'
        };

        const THEME_ICONS = {
            'water-flow': '💧',
            'glassmorphism': '🎙️',
            'golden': '👑',
            'gaming-neon': '🎮',
            'space': '🚀',
            'cute-pastel': '💗',
            'lava': '🔥',
            'minimal': '🤍',
            'nature': '🌿',
            'custom': '🎯'
        };

        // DOM elements
        const container = document.getElementById('widget-container');
        const headerRow = document.getElementById('header-row');
        const titleWrap = document.getElementById('goal-title-wrap');
        const titleText = document.getElementById('goal-title');
        const iconSpan = document.getElementById('goal-icon');
        const amountsWrap = document.getElementById('goal-amounts-wrap');
        const currentText = document.getElementById('current-amount-txt');
        const targetText = document.getElementById('target-amount-txt');
        const currencyTxt = document.getElementById('currency-symbol');
        const barOuter = document.getElementById('progress-outer');
        const barInner = document.getElementById('progress-inner');
        const barOverlay = document.getElementById('progress-overlay');
        const pctText = document.getElementById('percent-text');
        const bottomRow = document.getElementById('bottom-row');
        const celebrationPopup = document.getElementById('celebration-popup');
        const celebrationTitle = document.getElementById('celebration-title');

        // Animation state variables
        let displayCurrent = 0;
        let animationFrameId = null;
        let isGoalReachedTriggered = false;

        function updateWidgetDOM() {
            // Apply theme class
            container.className = '';
            if (goalData.theme_id !== 'custom') {
                container.classList.add('theme-' + goalData.theme_id);
                // Clear custom styles
                container.style.cssText = '';
                headerRow.style.cssText = '';
                titleWrap.style.cssText = '';
                amountsWrap.style.cssText = '';
                bottomRow.style.cssText = '';
                barOuter.style.cssText = '';
                barInner.style.cssText = '';
            } else {
                // Apply custom styling
                container.style.background = goalData.background_style;
                container.style.border = goalData.border_style;
                container.style.borderRadius = goalData.border_radius + 'px';
                container.style.boxShadow = goalData.shadow_style;
                container.style.fontFamily = goalData.font_family;

                const textStyle = `color: ${goalData.font_color}; font-weight: ${goalData.font_weight};`;
                headerRow.style.cssText = textStyle;
                titleWrap.style.cssText = textStyle;
                amountsWrap.style.cssText = textStyle;
                bottomRow.style.cssText = textStyle;
                pctText.style.color = goalData.font_color;
                
                barOuter.style.background = 'rgba(0,0,0,0.2)';
                barOuter.style.borderColor = 'rgba(255,255,255,0.08)';
                
                barInner.style.background = `linear-gradient(90deg, ${goalData.primary_color} 0%, ${goalData.secondary_color} 100%)`;
            }

            // Apply card effects
            if (goalData.effects.includes('pulse')) {
                container.classList.add('effect-pulse-card');
            }

            // Title and Icon
            titleText.textContent = goalData.title;
            iconSpan.textContent = THEME_ICONS[goalData.theme_id] || '🎯';

            // Show/Hide elements
            titleWrap.style.display = goalData.show_title ? 'flex' : 'none';
            amountsWrap.style.display = (goalData.show_current || goalData.show_target) ? 'block' : 'none';
            currentText.style.display = goalData.show_current ? 'inline' : 'none';
            targetText.style.display = goalData.show_target ? 'inline' : 'none';
            if (amountsWrap.querySelector('span:nth-child(2)')) {
                amountsWrap.querySelector('span:nth-child(2)').style.display = (goalData.show_current && goalData.show_target) ? 'inline' : 'none';
            }
            currencyTxt.textContent = CURRENCY;

            targetText.textContent = new Intl.NumberFormat().format(goalData.target_amount);
            bottomRow.textContent = goalData.bottom_text;
            bottomRow.style.display = goalData.show_bottom_text ? 'block' : 'none';
            pctText.style.display = goalData.show_percentage ? 'flex' : 'none';

            // Progress bar styles
            barOverlay.className = 'progress-overlay';
            if (goalData.progress_style === 'rainbow') {
                barInner.style.background = 'linear-gradient(90deg, #ff0000, #ff7f00, #ffff00, #00ff00, #0000ff, #4b0082, #8b00ff)';
            } else if (goalData.theme_id !== 'custom') {
                // reset to CSS defined background
                barInner.style.background = '';
            }

            // Apply style class to progress overlay
            if (['water', 'wave', 'pixel', 'segmented'].includes(goalData.progress_style)) {
                barOverlay.classList.add('style-' + goalData.progress_style);
            }

            // Glow / Shine effects on progress bar
            if (goalData.effects.includes('glow') || goalData.progress_style === 'glow') {
                const color = goalData.theme_id === 'custom' ? goalData.primary_color : 'rgba(255,255,255,0.8)';
                barInner.style.boxShadow = `0 0 15px ${color}`;
            } else if (goalData.theme_id !== 'custom') {
                barInner.style.boxShadow = '';
            }

            if (goalData.effects.includes('shine') || goalData.progress_style === 'shine') {
                barInner.classList.add('effect-shine');
            } else {
                barInner.classList.remove('effect-shine');
            }
        }

        function formatDisplayNumbers() {
            // Percent
            const pct = goalData.target_amount > 0 ? (displayCurrent / goalData.target_amount) * 100 : 0;
            pctText.textContent = Math.min(100, pct).toFixed(1) + '%';
            currentText.textContent = new Intl.NumberFormat().format(Math.floor(displayCurrent));

            // Bar Width
            const widthPct = Math.min(100, pct);
            barInner.style.width = widthPct + '%';
        }

        // Smooth Counter Animation
        function animateCounter(targetVal) {
            if (animationFrameId) cancelAnimationFrame(animationFrameId);

            const startVal = displayCurrent;
            const diff = targetVal - startVal;
            if (Math.abs(diff) < 0.1) {
                displayCurrent = targetVal;
                formatDisplayNumbers();
                return;
            }

            const duration = 1200; // ms
            let startTime = null;

            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                const elapsed = timestamp - startTime;
                const progress = Math.min(1, elapsed / duration);
                
                // Ease out cubic
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                displayCurrent = startVal + (diff * easeProgress);
                
                formatDisplayNumbers();

                if (progress < 1) {
                    animationFrameId = requestAnimationFrame(step);
                } else {
                    displayCurrent = targetVal;
                    formatDisplayNumbers();
                }
            }

            animationFrameId = requestAnimationFrame(step);
        }

        // Celebration triggers
        function triggerCelebration() {
            if (!goalData.celebration_enabled) return;

            celebrationTitle.textContent = goalData.celebration_message;
            celebrationPopup.classList.add('show');

            startParticles(goalData.celebration_type);

            setTimeout(() => {
                celebrationPopup.classList.remove('show');
                stopParticles();
            }, 6000);
        }

        // Particle System
        const canvas = document.getElementById('celebration-canvas');
        const ctx = canvas.getContext('2d');
        let particles = [];
        let animationId = null;
        let isCelebrating = false;
        let pType = 'confetti';

        function resizeCanvas() {
            canvas.width = container.clientWidth;
            canvas.height = container.clientHeight;
        }

        class Particle {
            constructor(type) {
                this.type = type;
                this.x = Math.random() * canvas.width;
                this.y = type === 'firework' ? canvas.height : -10;
                this.vx = (Math.random() - 0.5) * 4;
                this.vy = type === 'firework' ? -Math.random() * 4 - 6 : Math.random() * 2 + 1;
                this.color = `hsl(${Math.random() * 360}, 90%, 60%)`;
                if (type === 'golden-shine') {
                    this.color = `hsl(${40 + Math.random() * 20}, 100%, ${50 + Math.random() * 20}%)`;
                    this.y = canvas.height + 10;
                    this.vy = -Math.random() * 1.5 - 0.5;
                }
                this.size = Math.random() * 5 + 4;
                this.alpha = 1;
                this.decay = Math.random() * 0.015 + 0.005;
                this.exploded = false;
            }

            update() {
                if (this.type === 'confetti') {
                    this.x += this.vx;
                    this.y += this.vy;
                    this.vy += 0.05; // gravity
                } else if (this.type === 'firework') {
                    if (!this.exploded) {
                        this.x += this.vx;
                        this.y += this.vy;
                        this.vy += 0.08; // gravity
                        if (this.vy >= -1.5) {
                            this.explode();
                        }
                    } else {
                        this.x += this.vx;
                        this.y += this.vy;
                        this.vy += 0.1;
                        this.alpha -= this.decay * 1.5;
                    }
                } else if (this.type === 'golden-shine') {
                    this.x += this.vx * 0.5;
                    this.y += this.vy;
                    this.alpha -= 0.008;
                } else if (this.type === 'sparkle') {
                    this.alpha -= this.decay * 2;
                    this.size *= 0.98;
                }
            }

            explode() {
                this.exploded = true;
                const sparksCount = 18;
                for (let i = 0; i < sparksCount; i++) {
                    const angle = (Math.PI * 2 / sparksCount) * i + Math.random() * 0.5;
                    const speed = Math.random() * 3 + 2;
                    const p = new Particle('firework');
                    p.exploded = true;
                    p.x = this.x;
                    p.y = this.y;
                    p.vx = Math.cos(angle) * speed;
                    p.vy = Math.sin(angle) * speed;
                    p.color = this.color;
                    p.size = this.size * 0.7;
                    p.decay = 0.02;
                    particles.push(p);
                }
            }

            draw() {
                ctx.save();
                ctx.globalAlpha = this.alpha;
                ctx.fillStyle = this.color;
                ctx.beginPath();
                if (this.type === 'confetti') {
                    ctx.fillRect(this.x, this.y, this.size, this.size);
                } else if (this.type === 'golden-shine' || this.type === 'sparkle') {
                    // Draw a star/sparkle shape
                    const spikes = 4;
                    const outerRadius = this.size;
                    const innerRadius = this.size / 2;
                    let rot = Math.PI / 2 * 3;
                    let x = this.x;
                    let y = this.y;
                    let step = Math.PI / spikes;
                    ctx.beginPath();
                    ctx.moveTo(this.x, this.y - outerRadius);
                    for (let i = 0; i < spikes; i++) {
                        x = this.x + Math.cos(rot) * outerRadius;
                        y = this.y + Math.sin(rot) * outerRadius;
                        ctx.lineTo(x, y);
                        rot += step;
                        x = this.x + Math.cos(rot) * innerRadius;
                        y = this.y + Math.sin(rot) * innerRadius;
                        ctx.lineTo(x, y);
                        rot += step;
                    }
                    ctx.lineTo(this.x, this.y - outerRadius);
                    ctx.closePath();
                    ctx.shadowBlur = 8;
                    ctx.shadowColor = this.color;
                    ctx.fill();
                } else {
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fill();
                }
                ctx.restore();
            }
        }

        function loop() {
            if (!isCelebrating) return;
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Spawn rate
            if (pType === 'confetti' && Math.random() < 0.25) {
                particles.push(new Particle('confetti'));
            } else if (pType === 'firework' && Math.random() < 0.03 && particles.filter(p => !p.exploded).length < 2) {
                particles.push(new Particle('firework'));
            } else if (pType === 'golden-shine' && Math.random() < 0.3) {
                particles.push(new Particle('golden-shine'));
            } else if (pType === 'sparkle' && Math.random() < 0.4) {
                const p = new Particle('sparkle');
                p.x = Math.random() * canvas.width;
                p.y = Math.random() * canvas.height;
                p.vy = 0; p.vx = 0;
                particles.push(p);
            }

            particles.forEach((p, idx) => {
                p.update();
                p.draw();
                if (p.y > canvas.height + 20 || p.alpha <= 0 || p.x < -20 || p.x > canvas.width + 20) {
                    particles.splice(idx, 1);
                }
            });

            animationId = requestAnimationFrame(loop);
        }

        function startParticles(type) {
            isCelebrating = true;
            pType = type;
            particles = [];
            resizeCanvas();
            loop();
        }

        function stopParticles() {
            isCelebrating = false;
            cancelAnimationFrame(animationId);
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles = [];
        }

        window.addEventListener('resize', resizeCanvas);

        // Fetch API Polling status
        async function checkGoalStatus() {
            try {
                const res = await fetch(API_URL, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                
                const prevCurrent = goalData.current_amount;
                const prevTarget = goalData.target_amount;
                
                // Copy properties
                goalData = { ...goalData, ...data.goal };
                
                updateWidgetDOM();
                
                // Trigger counter animation
                animateCounter(goalData.current_amount);

                // Check if goal is reached (cross target threshold)
                if (goalData.current_amount >= goalData.target_amount && prevCurrent < goalData.target_amount) {
                    triggerCelebration();
                }
            } catch (e) {
                console.error('[goal-widget] error checking status', e);
            }
        }

        // Initialize
        updateWidgetDOM();
        resizeCanvas();
        checkGoalStatus();

        // Start polling
        setInterval(checkGoalStatus, {{ $pollSeconds * 1000 }});
    </script>
</body>
</html>
