<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Không tìm thấy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; overflow: hidden; background: #0c0e14; }

        canvas { position: fixed; inset: 0; width: 100%; height: 100%; }

        /* Overlay card */
        .card {
            position: fixed;
            bottom: 2.5rem;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 18px;
            padding: 1.2rem 2rem;
            text-align: center;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            font-family: 'Inter', sans-serif;
            z-index: 10;
        }
        .card p {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.55);
            font-weight: 500;
        }
        .card a {
            font-size: 0.85rem;
            font-weight: 700;
            color: #fff;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.18);
            padding: 7px 18px;
            border-radius: 10px;
            text-decoration: none;
            transition: background 0.15s;
        }
        .card a:hover { background: rgba(255,255,255,0.24); }
    </style>
</head>
<body>

<canvas id="c"></canvas>

<div class="card">
    <p>Trang này không tồn tại</p>
    <a href="{{ url('/') }}">Trang chủ</a>
</div>

<script>
const canvas = document.getElementById('c');
const ctx    = canvas.getContext('2d');

/* ── Palette ───────────────────────────────────────────── */
const COLORS = [
    '#6366f1','#8b5cf6','#ec4899','#f59e0b',
    '#10b981','#06b6d4','#f97316','#a78bfa'
];
let colorIdx = 0;
const nextColor = () => { colorIdx = (colorIdx + 1) % COLORS.length; return COLORS[colorIdx]; };

/* ── Text metrics ───────────────────────────────────────── */
const FONT_SIZE = Math.min(window.innerWidth * 0.18, 160);
const FONT      = `900 ${FONT_SIZE}px 'Inter', sans-serif`;
ctx.font = FONT;
const TEXT      = '404';
const TW        = ctx.measureText(TEXT).width;
const TH        = FONT_SIZE * 0.85;          // approx cap height

/* ── Resize ─────────────────────────────────────────────── */
function resize() {
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;
}
resize();
window.addEventListener('resize', resize);

/* ── Bouncer state ──────────────────────────────────────── */
let x  = Math.random() * (canvas.width  - TW);
let y  = Math.random() * (canvas.height - TH) + TH;
let vx = (Math.random() > 0.5 ? 1 : -1) * (2.2 + Math.random());
let vy = (Math.random() > 0.5 ? 1 : -1) * (2.2 + Math.random());
let curColor = COLORS[0];

/* ── Particles ──────────────────────────────────────────── */
const particles = [];

function burst(px, py) {
    const COUNT = 80;
    for (let i = 0; i < COUNT; i++) {
        const angle = (Math.PI * 2 * i) / COUNT + Math.random() * 0.3;
        const speed = 3 + Math.random() * 6;
        particles.push({
            x: px, y: py,
            vx: Math.cos(angle) * speed,
            vy: Math.sin(angle) * speed,
            alpha: 1,
            size:  2 + Math.random() * 3,
            color: COLORS[Math.floor(Math.random() * COLORS.length)],
            trail: []
        });
    }
}

/* ── Corner threshold ───────────────────────────────────── */
const CORNER_R = Math.min(canvas.width, canvas.height) * 0.06 + 30;

function isCorner(cx, cy) {
    const W = canvas.width, H = canvas.height;
    const corners = [[0,0],[W,0],[0,H],[W,H]];
    for (const [cx2,cy2] of corners) {
        const d = Math.hypot(cx - cx2, cy - cy2);
        if (d < CORNER_R) return [cx2, cy2];
    }
    return null;
}

/* ── Main loop ──────────────────────────────────────────── */
function tick() {
    const W = canvas.width, H = canvas.height;

    /* clear */
    ctx.fillStyle = '#0c0e14';
    ctx.fillRect(0, 0, W, H);

    /* move */
    x += vx;
    y += vy;

    /* wall collision */
    let hitX = false, hitY = false;
    if (x <= 0)       { x  = 0;    vx = Math.abs(vx);  hitX = true; }
    if (x + TW >= W)  { x  = W-TW; vx = -Math.abs(vx); hitX = true; }
    if (y - TH <= 0)  { y  = TH;   vy = Math.abs(vy);  hitY = true; }
    if (y >= H)       { y  = H;    vy = -Math.abs(vy); hitY = true; }

    if (hitX || hitY) {
        curColor = nextColor();

        /* corner check: both walls at once */
        if (hitX && hitY) {
            const center = [x + TW/2, y - TH/2];
            burst(...center);
        }
    }

    /* draw 404 with glow */
    ctx.save();
    ctx.font        = FONT;
    ctx.textBaseline = 'alphabetic';
    ctx.shadowColor = curColor;
    ctx.shadowBlur  = 28;
    ctx.fillStyle   = curColor;
    ctx.fillText(TEXT, x, y);
    ctx.restore();

    /* draw particles */
    for (let i = particles.length - 1; i >= 0; i--) {
        const p = particles[i];
        p.trail.push({ x: p.x, y: p.y });
        if (p.trail.length > 6) p.trail.shift();

        p.x  += p.vx;
        p.y  += p.vy;
        p.vy += 0.18;     // gravity
        p.vx *= 0.97;
        p.vy *= 0.97;
        p.alpha -= 0.018;

        if (p.alpha <= 0) { particles.splice(i, 1); continue; }

        /* trail */
        for (let t = 0; t < p.trail.length; t++) {
            const a = (t / p.trail.length) * p.alpha * 0.4;
            ctx.beginPath();
            ctx.arc(p.trail[t].x, p.trail[t].y, p.size * 0.5, 0, Math.PI*2);
            ctx.fillStyle = p.color;
            ctx.globalAlpha = a;
            ctx.fill();
        }
        ctx.globalAlpha = 1;

        /* dot */
        ctx.save();
        ctx.globalAlpha  = p.alpha;
        ctx.shadowColor  = p.color;
        ctx.shadowBlur   = 8;
        ctx.fillStyle    = p.color;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size, 0, Math.PI*2);
        ctx.fill();
        ctx.restore();
    }

    requestAnimationFrame(tick);
}

tick();
</script>
</body>
</html>
