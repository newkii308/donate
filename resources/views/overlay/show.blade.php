<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Overlay — {{ $streamer->display_name }}</title>
    {{-- Load a Lao font so OBS renders Lao text clearly. --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-lao:400,500,600,700,800" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; background: transparent !important; overflow: hidden; font-family: 'Noto Sans Lao', system-ui, sans-serif; }
        #stage { position: fixed; inset: 0; pointer-events: none; }
        .alert-wrap { position: absolute; display: flex; }
        .pos-top-left      { top: 24px; left: 24px; }
        .pos-top-center    { top: 24px; left: 50%; transform: translateX(-50%); }
        .pos-top-right     { top: 24px; right: 24px; }
        .pos-middle-center { top: 50%; left: 50%; transform: translate(-50%, -50%); }
        .pos-bottom-left   { bottom: 24px; left: 24px; }
        .pos-bottom-center { bottom: 24px; left: 50%; transform: translateX(-50%); }
        .pos-bottom-right  { bottom: 24px; right: 24px; }

        .alert-card { overflow: hidden; padding: 22px 26px; text-align: center; backdrop-filter: blur(4px); display: flex; flex-direction: column; align-items: center; gap: 2px; }
        .alert-card .accent-icon { display: none; color: var(--accent, currentColor); }
        .alert-card .accent-icon svg { display: block; width: 100%; height: 100%; }
        .alert-card .media { display: block; max-width: 100%; margin: 0 auto 14px; max-height: 220px; border-radius: 12px; }
        .alert-card .avatar { width: 72px; height: 72px; border-radius: 9999px; object-fit: cover; margin: 0 auto 12px; display: block; border: 3px solid rgba(255,255,255,.55); }
        .alert-card .body { width: 100%; }
        .alert-card .headline { line-height: 1.35; }
        .alert-card .headline .donor { font-weight: 800; }
        .alert-card .headline .amount { font-weight: 800; }
        .alert-card .message { margin-top: 8px; font-size: .82em; opacity: .92; word-break: break-word; font-weight: 500; }

        /* ===== โครงการ์ดแจ้งเตือน (alert styles) ===== */
        /* classic = ค่าเริ่มต้นด้านบน */

        /* banner: แถบแนวนอน ไอคอน/avatar ซ้าย ข้อความขวา + ขอบสีไฮไลต์ */
        .alert-card.as-banner { flex-direction: row; align-items: center; text-align: left; gap: 16px; padding: 18px 24px; border-left: 5px solid var(--accent, currentColor); }
        .alert-card.as-banner .accent-icon { display: block; width: 44px; height: 44px; flex: 0 0 auto; }
        .alert-card.as-banner .avatar { margin: 0; flex: 0 0 auto; }
        .alert-card.as-banner .media { display: none; }
        .alert-card.as-banner .body { width: auto; }

        /* pill: แคปซูลบรรทัดเดียว กะทัดรัด */
        .alert-card.as-pill { flex-direction: row; align-items: center; gap: 10px; padding: 10px 20px; border-radius: 9999px !important; text-align: left; }
        .alert-card.as-pill .accent-icon { display: block; width: 26px; height: 26px; flex: 0 0 auto; }
        .alert-card.as-pill .avatar { width: 34px; height: 34px; margin: 0; flex: 0 0 auto; }
        .alert-card.as-pill .media, .alert-card.as-pill .message { display: none; }
        .alert-card.as-pill .body { width: auto; }
        .alert-card.as-pill .headline { white-space: nowrap; }

        /* hero: การ์ดใหญ่ ยอดเงินตัวเบิ้ม */
        .alert-card.as-hero { padding: 28px 32px; gap: 4px; }
        .alert-card.as-hero .accent-icon { display: block; width: 56px; height: 56px; margin: 0 auto 6px; }
        .alert-card.as-hero .amount { display: block; font-size: 1.9em; margin-top: 6px; letter-spacing: .5px; }
        .alert-card.as-hero .media { max-height: 260px; }

        /* side-accent: มินิมอล แถบสีตั้งด้านซ้าย */
        .alert-card.as-side-accent { flex-direction: row; align-items: center; text-align: left; gap: 14px; padding: 16px 20px; position: relative; }
        .alert-card.as-side-accent::before { content: ''; position: absolute; left: 8px; top: 12px; bottom: 12px; width: 5px; border-radius: 4px; background: var(--accent, currentColor); }
        .alert-card.as-side-accent .accent-icon { display: block; width: 38px; height: 38px; flex: 0 0 auto; margin-left: 8px; }
        .alert-card.as-side-accent .avatar { margin: 0; flex: 0 0 auto; }
        .alert-card.as-side-accent .media { display: none; }
        .alert-card.as-side-accent .body { width: auto; }

        @keyframes nl-fade-in   { from { opacity: 0 } to { opacity: 1 } }
        @keyframes nl-fade-out  { from { opacity: 1 } to { opacity: 0 } }
        @keyframes nl-zoom-in   { from { opacity: 0; transform: scale(.7) } to { opacity: 1; transform: scale(1) } }
        @keyframes nl-zoom-out  { from { opacity: 1; transform: scale(1) } to { opacity: 0; transform: scale(.7) } }
        @keyframes nl-slide-in  { from { opacity: 0; transform: translateY(-40px) } to { opacity: 1; transform: translateY(0) } }
        @keyframes nl-slide-out { from { opacity: 1; transform: translateY(0) } to { opacity: 0; transform: translateY(-40px) } }
        @keyframes nl-slideleft-in   { from { opacity: 0; transform: translateX(-60px) } to { opacity: 1; transform: translateX(0) } }
        @keyframes nl-slideleft-out  { from { opacity: 1; transform: translateX(0) } to { opacity: 0; transform: translateX(-60px) } }
        @keyframes nl-slideright-in  { from { opacity: 0; transform: translateX(60px) } to { opacity: 1; transform: translateX(0) } }
        @keyframes nl-slideright-out { from { opacity: 1; transform: translateX(0) } to { opacity: 0; transform: translateX(60px) } }
        @keyframes nl-bounce-in { 0% { opacity: 0; transform: scale(.3) } 60% { opacity: 1; transform: scale(1.08) } 100% { transform: scale(1) } }
        @keyframes nl-pop-in    { 0% { opacity: 0; transform: scale(.5) } 55% { opacity: 1; transform: scale(1.12) } 75% { transform: scale(.96) } 100% { transform: scale(1) } }
        @keyframes nl-pop-out   { from { opacity: 1; transform: scale(1) } to { opacity: 0; transform: scale(.5) } }
        @keyframes nl-flip-in   { from { opacity: 0; transform: perspective(700px) rotateY(90deg) } to { opacity: 1; transform: perspective(700px) rotateY(0) } }
        @keyframes nl-flip-out  { from { opacity: 1; transform: perspective(700px) rotateY(0) } to { opacity: 0; transform: perspective(700px) rotateY(-90deg) } }
        @keyframes nl-glitch-in { 0% { opacity: 0; transform: translate(0) } 20% { opacity: 1; transform: translate(-6px, 3px) } 40% { transform: translate(6px, -3px) } 60% { transform: translate(-4px, -2px) } 80% { transform: translate(4px, 2px) } 100% { opacity: 1; transform: translate(0) } }
    </style>
</head>
<body>
    <div id="stage"></div>

    <script>
        const CONFIG = {
            checkUrl: @json(route('api.overlay.check', $streamer->overlay_key)),
            completeUrl: @json(route('api.overlay.complete', $streamer->overlay_key)),
            pollSeconds: {{ (int) $pollSeconds }},
        };

        // ชุดธีมสำเร็จ (มาจาก config/newlab.php) — ใช้กำหนดหน้าตาการ์ดแจ้งเตือน
        const THEMES = @json($themes);

        // ไอคอน SVG ประดับการ์ด (กล่องของขวัญ) — ใช้แทนอิโมจิในสไตล์ banner/pill/hero/side-accent
        const ACCENT_SVG = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7"/><path d="M12 8S10.5 4 8 4a2 2 0 0 0 0 4h4zM12 8s1.5-4 4-4a2 2 0 0 1 0 4h-4z"/></svg>';

        const CURRENCY_LABEL = { LAK: 'ກີບ' };

        const stage = document.getElementById('stage');
        let busy = false;

        // บางเบราว์เซอร์ (เช่น เปิดพรีวิวในแท็บ Chrome ธรรมดา ไม่ใช่ OBS) บล็อกเสียง
        // อัตโนมัติจนกว่าจะมีการคลิกในหน้านี้สักครั้ง — ปุ่มนี้จะโผล่เฉพาะตอนที่โดนบล็อกจริง
        // (ใน OBS Browser Source เสียงเล่นอัตโนมัติได้อยู่แล้ว ปุ่มนี้จะไม่โผล่)
        function showUnlockBanner() {
            if (document.getElementById('nl-unlock')) return;
            const b = document.createElement('div');
            b.id = 'nl-unlock';
            b.textContent = '🔊 ຄລິກເພື່ອເປີດສຽງແຈ້ງເຕືອນ';
            b.style.cssText = "position:fixed;left:50%;bottom:20px;transform:translateX(-50%);padding:10px 20px;background:rgba(0,0,0,.78);color:#fff;border-radius:999px;font-family:'Noto Sans Lao',system-ui,sans-serif;font-size:14px;font-weight:600;cursor:pointer;z-index:99999;box-shadow:0 10px 30px rgba(0,0,0,.45);pointer-events:auto";
            b.onclick = () => b.remove();
            document.body.appendChild(b);
        }
        document.addEventListener('pointerdown', () => {
            const b = document.getElementById('nl-unlock');
            if (b) b.remove();
        }, { once: true });

        async function poll() {
            if (busy) return;
            try {
                const res = await fetch(CONFIG.checkUrl, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                if (data.notification) {
                    busy = true;
                    await playAlert(data.notification);
                    await complete(data.notification.id);
                    busy = false;
                }
            } catch (e) {
                busy = false;
            }
        }

        async function complete(id) {
            try {
                await fetch(CONFIG.completeUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ id }),
                });
            } catch (e) { /* ignore */ }
        }

        function buildCard(n) {
            const s = n.style || {};
            const themeKey = s.theme || 'neon-purple';
            const theme = THEMES[themeKey] || {};
            const isCustom = themeKey === 'custom';

            const wrap = document.createElement('div');
            wrap.className = 'alert-wrap pos-' + (s.position || 'top-center');

            const card = document.createElement('div');
            card.className = 'alert-card';
            card.style.width = (s.width || 480) + 'px';
            if (s.height) card.style.minHeight = s.height + 'px';
            card.style.fontFamily = s.font_family || "'Noto Sans Lao', sans-serif";
            card.style.fontSize = (s.font_size || 30) + 'px';
            card.style.fontWeight = s.font_weight || '700';
            card.style.borderRadius = (s.border_radius ?? 16) + 'px';

            // หน้าตาตามธีม (หรือใช้สีที่ตั้งเองเมื่อเลือก custom)
            if (isCustom) {
                card.style.background = s.background_color;
                card.style.color = s.font_color;
                if (s.shadow) card.style.boxShadow = '0 20px 50px rgba(0,0,0,.45)';
            } else {
                card.style.background = theme.background;
                card.style.color = s.font_color || '#ffffff';
                if (theme.border) card.style.border = theme.border;
                if (s.shadow !== false && theme.glow) card.style.boxShadow = theme.glow;
            }

            // โครงการ์ด (layout) + สีไฮไลต์ + ไอคอน SVG ประดับ
            const alertStyle = s.alert_style || 'classic';
            card.classList.add('as-' + alertStyle);
            const accentColor = isCustom ? (s.font_color || '#ffffff') : (theme.name_color || s.font_color || '#ffffff');
            card.style.setProperty('--accent', accentColor);
            if (alertStyle !== 'classic') {
                const acc = document.createElement('div');
                acc.className = 'accent-icon';
                acc.innerHTML = ACCENT_SVG;
                card.appendChild(acc);
            }

            if (n.avatar_url) {
                const img = document.createElement('img');
                img.className = 'avatar'; img.src = n.avatar_url;
                card.appendChild(img);
            }

            if (n.image && n.image.url) {
                if (n.image.type === 'animation' && /\.webm($|\?)/i.test(n.image.url)) {
                    const v = document.createElement('video');
                    v.className = 'media'; v.src = n.image.url;
                    v.autoplay = true; v.muted = true; v.loop = false; v.playsInline = true;
                    card.appendChild(v);
                } else {
                    const img = document.createElement('img');
                    img.className = 'media'; img.src = n.image.url;
                    card.appendChild(img);
                }
            }

            const curLabel = CURRENCY_LABEL[n.currency] || n.currency;
            const donorColor = isCustom ? '' : (theme.name_color || '');
            const amountColor = isCustom ? '' : (theme.amount_color || '');

            // กล่องข้อความ (จัดกลุ่มพาดหัว + ข้อความ ให้สไตล์แนวนอนวางข้างไอคอน/avatar ได้)
            const body = document.createElement('div');
            body.className = 'body';

            const headline = document.createElement('div');
            headline.className = 'headline';
            headline.innerHTML =
                '<span class="donor" style="' + (donorColor ? 'color:' + donorColor : '') + '">' + escapeHtml(n.donor_name) + '</span> ໂດເນດ ' +
                '<span class="amount" style="' + (amountColor ? 'color:' + amountColor : '') + '">' + escapeHtml(n.amount_formatted) + ' ' + escapeHtml(curLabel) + '</span>';
            body.appendChild(headline);

            if (n.message) {
                const msg = document.createElement('div');
                msg.className = 'message';
                if (!isCustom && theme.message_color) msg.style.color = theme.message_color;
                msg.textContent = n.message;
                body.appendChild(msg);
            }

            card.appendChild(body);
            wrap.appendChild(card);
            return { wrap, card };
        }

        function escapeHtml(str) {
            const d = document.createElement('div');
            d.textContent = str == null ? '' : String(str);
            return d.innerHTML;
        }

        function animate(card, name, dir, duration) {
            return new Promise((resolve) => {
                // [เข้า, ออก] ต่อแอนิเมชัน — bounce/glitch ใช้ fade-out ตอนออก
                const map = {
                    'fade':        ['nl-fade-in', 'nl-fade-out'],
                    'zoom':        ['nl-zoom-in', 'nl-zoom-out'],
                    'slide':       ['nl-slide-in', 'nl-slide-out'],
                    'slide-left':  ['nl-slideleft-in', 'nl-slideleft-out'],
                    'slide-right': ['nl-slideright-in', 'nl-slideright-out'],
                    'bounce':      ['nl-bounce-in', 'nl-fade-out'],
                    'pop':         ['nl-pop-in', 'nl-pop-out'],
                    'flip':        ['nl-flip-in', 'nl-flip-out'],
                    'glitch':      ['nl-glitch-in', 'nl-fade-out'],
                };
                const pair = map[name] || map.fade;
                const anim = dir === 'in' ? pair[0] : pair[1];
                card.style.animation = anim + ' ' + duration + 'ms ease both';
                setTimeout(resolve, duration);
            });
        }

        function playSound(sound) {
            if (!sound || !sound.url) return Promise.resolve();
            return new Promise((resolve) => {
                setTimeout(() => {
                    try {
                        const audio = new Audio(sound.url);
                        audio.volume = Math.min(1, Math.max(0, sound.volume ?? 0.8));
                        audio.onended = resolve;
                        audio.onerror = resolve;
                        audio.play().catch((err) => { console.warn('[overlay] sound play blocked', err); showUnlockBanner(); resolve(); });
                    } catch (e) { resolve(); }
                }, sound.delay || 0);
            });
        }

        // Prefer a Lao voice installed in the browser.
        function pickLaoVoice(tts) {
            if (!('speechSynthesis' in window)) return null;
            const voices = speechSynthesis.getVoices();
            if (tts.voice) {
                const v = voices.find(x => x.name === tts.voice);
                if (v) return v;
            }
            const lang2 = (tts.language || 'lo').slice(0, 2).toLowerCase();
            const matched = voices.filter(x => x.lang && x.lang.toLowerCase().startsWith(lang2));
            return matched.find(x => /natural|online|neural|enhanced|premium|keomany|chanthavong|google/i.test(x.name)) || matched[0] || null;
        }

        // เสียงไทยออนไลน์ผ่านเซิร์ฟเวอร์เรา (same-origin เล่นได้ชัวร์ + แคชไว้ใช้ซ้ำ)
        const TTS_URL = @json(route('tts.speak'));
        // เสียงผ่านเซิร์ฟเวอร์: Edge neural (voice = 'edge:...') หรือ Google (voice ว่าง/สำรอง)
        function serverTts(text, voice, volume, rate, pitch, lang) {
            return new Promise((resolve) => {
                try {
                    const isEdge = voice && voice.indexOf('edge:') === 0;
                    const ratePct = Math.max(-100, Math.min(100, Math.round(((rate || 1) - 1) * 100)));
                    const pitchHz = Math.max(-100, Math.min(100, Math.round(((pitch || 1) - 1) * 50)));
                    const url = TTS_URL + '?q=' + encodeURIComponent(text)
                        + (voice ? '&voice=' + encodeURIComponent(voice) : '')
                        + '&lang=' + (lang || 'lo') + '&rate=' + ratePct + '&pitch=' + pitchHz;
                    const a = new Audio(url);
                    a.volume = Math.min(1, Math.max(0, volume ?? 1));
                    // edge: ปรับความเร็ว/โทนใน SSML แล้ว; google: ปรับความเร็วด้วย playbackRate
                    if (!isEdge) a.playbackRate = Math.min(2, Math.max(0.5, rate || 1));
                    let done = false; const finish = () => { if (!done) { done = true; resolve(); } };
                    a.onended = finish; a.onerror = finish;
                    setTimeout(finish, 25000); // กันค้าง
                    a.play().catch((err) => { console.warn('[overlay] tts play blocked', err); showUnlockBanner(); finish(); });
                } catch (e) { resolve(); }
            });
        }

        function speak(tts) {
            if (!tts || !tts.enabled || !tts.text) return Promise.resolve();
            const lang2 = (tts.language || 'lo').slice(0, 2).toLowerCase();
            // เสียง Edge neural → ใช้เซิร์ฟเวอร์เสมอ (ไม่ต้องมีเสียงในเครื่อง)
            if (tts.voice && tts.voice.indexOf('edge:') === 0) {
                return serverTts(tts.text, tts.voice, tts.volume ?? 1, tts.rate, tts.pitch, lang2);
            }
            const voice = pickLaoVoice(tts);
            if (voice && 'speechSynthesis' in window) {
                return new Promise((resolve) => {
                    try {
                        const u = new SpeechSynthesisUtterance(tts.text);
                        u.voice = voice;
                        u.lang = tts.language || 'lo-LA';
                        u.rate = tts.rate || 1;
                        u.pitch = tts.pitch || 1;
                        u.volume = tts.volume ?? 1;
                        u.onend = resolve;
                        u.onerror = (e) => { console.warn('[overlay] speechSynthesis blocked', e); showUnlockBanner(); resolve(); };
                        setTimeout(resolve, 15000); // กันกรณี engine ไม่ยิง onend
                        speechSynthesis.speak(u);
                    } catch (e) { resolve(); }
                });
            }
            // ไม่มีเสียงไทยในเครื่อง → เสียงออนไลน์ผ่านเซิร์ฟเวอร์ (Google สำรอง)
            return serverTts(tts.text, '', tts.volume ?? 1, tts.rate, tts.pitch, lang2);
        }

        async function playAlert(n) {
            const { wrap, card } = buildCard(n);
            stage.appendChild(wrap);

            const inDur = n.animation?.duration || 500;
            await animate(card, n.animation?.name || 'fade', 'in', inDur);

            const soundP = playSound(n.sound);
            const ttsP = speak(n.tts);
            const holdP = new Promise(r => setTimeout(r, n.display_duration || 8000));

            await Promise.all([holdP, soundP, ttsP]);

            await animate(card, n.animation?.name || 'fade', 'out', inDur);
            wrap.remove();
        }

        if ('speechSynthesis' in window) {
            speechSynthesis.getVoices();
            speechSynthesis.onvoiceschanged = () => speechSynthesis.getVoices();
        }

        poll();
        setInterval(poll, CONFIG.pollSeconds * 1000);
    </script>
</body>
</html>
