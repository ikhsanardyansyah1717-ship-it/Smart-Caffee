@php
    $ppService = \App\Services\FinancialReportService::class;
    [$ppStart, $ppEnd] = $ppService::resolvePeriod(request('month'), request('start'), request('end'));
    $ppLabel = $ppService::periodLabel($ppStart, $ppEnd);
@endphp

<div class="pp" id="periodPicker"
     data-start="{{ $ppStart->toDateString() }}"
     data-end="{{ $ppEnd->toDateString() }}">

    <button type="button" class="pp-trigger" id="ppTrigger" aria-haspopup="dialog" aria-expanded="false">
        <i class="fa-solid fa-calendar-days"></i>
        <span id="ppLabel">{{ $ppLabel }}</span>
        <i class="fa-solid fa-chevron-down pp-chev"></i>
    </button>

    <div class="pp-pop" id="ppPop" role="dialog" aria-label="Pilih periode" hidden>
        <div class="pp-year">
            <button type="button" class="pp-nav" id="ppPrevYear" aria-label="Tahun sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
            <strong id="ppYear"></strong>
            <button type="button" class="pp-nav" id="ppNextYear" aria-label="Tahun berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
        </div>

        <div class="pp-months" id="ppMonths"></div>

        <div class="pp-sep"></div>

        <div class="pp-year">
            <button type="button" class="pp-nav" id="ppPrevMonth" aria-label="Bulan sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
            <strong id="ppMonthTitle"></strong>
            <button type="button" class="pp-nav" id="ppNextMonth" aria-label="Bulan berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        <div class="pp-dow"><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span></div>
        <div class="pp-days" id="ppDays"></div>

        <div class="pp-foot">
            <span class="pp-summary" id="ppSummary"></span>
            <div class="pp-actions">
                <button type="button" class="pp-btn pp-light" id="ppThisMonth">Bulan ini</button>
                <button type="button" class="pp-btn pp-primary" id="ppApply">Terapkan</button>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ url()->current() }}" id="ppForm" style="display:none;">
        <input type="hidden" name="start" id="ppStartInput">
        <input type="hidden" name="end" id="ppEndInput">
    </form>
</div>

<style>
    .pp{position:relative;display:inline-block}
    .pp-trigger{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid #e5ddd0;border-radius:12px;background:#fff;font:inherit;font-size:13px;font-weight:600;color:#3b2a1a;cursor:pointer;max-width:270px}
    .pp-trigger span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .pp-trigger i{color:#7a4100}
    .pp-chev{font-size:11px}
    .pp-pop{position:absolute;top:calc(100% + 8px);right:0;z-index:60;width:330px;max-width:calc(100vw - 32px);background:#fff;border:1px solid #eadfcf;border-radius:18px;box-shadow:0 18px 44px rgba(60,35,10,.18);padding:16px}
    .pp-pop[hidden]{display:none}
    .pp-year{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
    .pp-year strong{font-size:15px;color:#2b1d10}
    .pp-nav{width:30px;height:30px;border-radius:9px;border:1px solid #eadfcf;background:#fff;color:#7a4100;cursor:pointer}
    .pp-nav:hover{background:#f4ede2}
    .pp-months{display:grid;grid-template-columns:repeat(4,1fr);gap:6px}
    .pp-month{padding:8px 0;border-radius:10px;border:1px solid transparent;background:#faf6ef;font:inherit;font-size:13px;font-weight:600;color:#5b4631;cursor:pointer}
    .pp-month:hover{background:#f1e6d3}
    .pp-month.is-view{border-color:#d9c6a8}
    .pp-month.is-active{background:#7a4100;color:#fff}
    .pp-sep{height:1px;background:#f0e6d6;margin:14px 0}
    .pp-dow,.pp-days{display:grid;grid-template-columns:repeat(7,1fr)}
    .pp-dow span{text-align:center;font-size:11px;font-weight:600;color:#9a8a76;padding:4px 0}
    .pp-days{row-gap:2px}
    .pp-day{height:34px;border:0;background:transparent;font:inherit;font-size:13px;color:#3b2a1a;cursor:pointer;border-radius:0}
    .pp-day:hover:not(.is-in){background:#f4ede2;border-radius:10px}
    .pp-day.is-in{background:#f1e2cc}
    .pp-day.is-edge{background:#7a4100;color:#fff;font-weight:700}
    .pp-day.is-first{border-radius:10px 0 0 10px}
    .pp-day.is-last{border-radius:0 10px 10px 0}
    .pp-day.is-first.is-last{border-radius:10px}
    .pp-day.is-today:not(.is-edge){font-weight:800;color:#7a4100}
    .pp-day.is-blank{visibility:hidden;cursor:default}
    .pp-foot{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:14px;flex-wrap:wrap}
    .pp-summary{font-size:12px;color:#7d6e5c;flex:1;min-width:120px}
    .pp-actions{display:flex;gap:8px}
    .pp-btn{padding:8px 14px;border-radius:10px;border:1px solid #e5ddd0;font:inherit;font-size:13px;font-weight:700;cursor:pointer}
    .pp-light{background:#fff;color:#5b4631}
    .pp-primary{background:#7a4100;border-color:#7a4100;color:#fff}
</style>

<script>
(function () {
    const root = document.getElementById('periodPicker');
    if (!root) return;

    const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const PENDEK = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    const $ = (id) => document.getElementById(id);
    const pop = $('ppPop'), trigger = $('ppTrigger');
    const pad = (n) => String(n).padStart(2, '0');
    const toStr = (d) => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    const parse = (s) => { const [y, m, d] = s.split('-').map(Number); return new Date(y, m - 1, d); };

    const applied = { start: parse(root.dataset.start), end: parse(root.dataset.end) };
    let start, end, picking, view;

    function reset() {
        start = new Date(applied.start);
        end = new Date(applied.end);
        picking = false;
        view = { y: start.getFullYear(), m: start.getMonth() };
    }

    function isFullMonth(y, m) {
        return toStr(start) === toStr(new Date(y, m, 1)) && toStr(end) === toStr(new Date(y, m + 1, 0));
    }

    function describe() {
        if (isFullMonth(start.getFullYear(), start.getMonth()) && start.getMonth() === end.getMonth() && start.getFullYear() === end.getFullYear()) {
            return BULAN[start.getMonth()] + ' ' + start.getFullYear();
        }
        const f = (d) => d.getDate() + ' ' + PENDEK[d.getMonth()] + ' ' + d.getFullYear();
        return toStr(start) === toStr(end) ? f(start) : f(start) + ' – ' + f(end);
    }

    function renderMonths() {
        $('ppYear').textContent = view.y;
        const wrap = $('ppMonths');
        wrap.innerHTML = '';
        PENDEK.forEach((name, i) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.textContent = name;
            b.className = 'pp-month' + (isFullMonth(view.y, i) ? ' is-active' : '') + (view.m === i ? ' is-view' : '');
            b.addEventListener('click', () => {
                start = new Date(view.y, i, 1);
                end = new Date(view.y, i + 1, 0);
                view.m = i;
                picking = false;
                render();
            });
            wrap.appendChild(b);
        });
    }

    function renderDays() {
        $('ppMonthTitle').textContent = BULAN[view.m] + ' ' + view.y;
        const wrap = $('ppDays');
        wrap.innerHTML = '';
        const blanks = (new Date(view.y, view.m, 1).getDay() + 6) % 7;
        const total = new Date(view.y, view.m + 1, 0).getDate();
        const todayStr = toStr(new Date());

        for (let i = 0; i < blanks; i++) {
            const e = document.createElement('button');
            e.type = 'button';
            e.className = 'pp-day is-blank';
            e.tabIndex = -1;
            wrap.appendChild(e);
        }

        for (let d = 1; d <= total; d++) {
            const date = new Date(view.y, view.m, d);
            const s = toStr(date);
            const b = document.createElement('button');
            b.type = 'button';
            b.textContent = d;
            let cls = 'pp-day';
            const isStart = s === toStr(start), isEnd = s === toStr(end);
            if (date >= start && date <= end) cls += ' is-in';
            if (isStart || isEnd) cls += ' is-edge';
            if (isStart) cls += ' is-first';
            if (isEnd) cls += ' is-last';
            if (s === todayStr) cls += ' is-today';
            b.className = cls;
            b.addEventListener('click', () => pickDay(date));
            wrap.appendChild(b);
        }
    }

    function pickDay(d) {
        if (!picking) {
            start = d; end = d; picking = true;
        } else {
            if (d < start) { end = start; start = d; } else { end = d; }
            picking = false;
        }
        render();
    }

    function render() {
        renderMonths();
        renderDays();
        $('ppSummary').textContent = picking ? 'Pilih tanggal akhir…' : describe();
    }

    function open() {
        reset();
        render();
        pop.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
    }

    function close() {
        pop.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
    }

    function submit(s, e) {
        $('ppStartInput').value = toStr(s);
        $('ppEndInput').value = toStr(e);
        $('ppForm').submit();
    }

    trigger.addEventListener('click', () => (pop.hidden ? open() : close()));
    // Pakai composedPath: tombol yang diklik bisa sudah terhapus dari DOM (karena render ulang),
    // jadi root.contains(e.target) salah menganggap klik itu "di luar" lalu menutup kalender.
    document.addEventListener('click', (e) => {
        if (!e.composedPath().includes(root)) close();
    });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });

    $('ppPrevYear').addEventListener('click', () => { view.y--; render(); });
    $('ppNextYear').addEventListener('click', () => { view.y++; render(); });
    $('ppPrevMonth').addEventListener('click', () => { view.m--; if (view.m < 0) { view.m = 11; view.y--; } render(); });
    $('ppNextMonth').addEventListener('click', () => { view.m++; if (view.m > 11) { view.m = 0; view.y++; } render(); });

    $('ppThisMonth').addEventListener('click', () => {
        const n = new Date();
        submit(new Date(n.getFullYear(), n.getMonth(), 1), new Date(n.getFullYear(), n.getMonth() + 1, 0));
    });
    $('ppApply').addEventListener('click', () => submit(start, end));

    reset();
})();
</script>