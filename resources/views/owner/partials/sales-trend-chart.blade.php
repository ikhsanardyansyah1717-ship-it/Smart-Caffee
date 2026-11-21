@php
    $trendValues = collect($chartData ?? [])->values();
    $trendMaximum = max((float) $trendValues->max('total'), 1);
    $trendCount = $trendValues->count();
    $chartLeft = 70;
    $chartRight = 870;
    $chartTop = 24;
    $chartBottom = 238;
    $chartHeight = $chartBottom - $chartTop;
    $chartWidth = $chartRight - $chartLeft;
    $trendPoints = $trendValues->map(function ($day, $index) use ($trendMaximum, $trendCount, $chartLeft, $chartWidth, $chartTop, $chartHeight, $chartBottom) {
        $x = $trendCount > 1 ? $chartLeft + ($chartWidth * $index / ($trendCount - 1)) : $chartLeft + ($chartWidth / 2);
        $total = (float) ($day['total'] ?? 0);
        $y = $chartBottom - ($total / $trendMaximum * $chartHeight);
        return ['x' => round($x, 2), 'y' => round($y, 2), 'total' => $total, 'label' => $day['label'] ?? '', 'date' => $day['date'] ?? null];
    });
    $trendLinePath = $trendPoints->map(fn ($point, $index) => ($index === 0 ? 'M ' : 'L ') . $point['x'] . ' ' . $point['y'])->implode(' ');
    $firstPoint = $trendPoints->first();
    $lastPoint = $trendPoints->last();
    $latestVisibleLabel = $lastPoint && !empty($lastPoint['date'])
        ? \Illuminate\Support\Carbon::parse($lastPoint['date'])->translatedFormat('d M Y')
        : null;
    $trendAreaPath = $trendPoints->isNotEmpty()
        ? $trendLinePath . ' L ' . $lastPoint['x'] . ' ' . $chartBottom . ' L ' . $firstPoint['x'] . ' ' . $chartBottom . ' Z'
        : '';
    $labelInterval = max(1, (int) ceil($trendCount / 7));
@endphp

<div class="owner-trend-chart" role="img" aria-label="Grafik tren omzet harian">
    <div class="trend-chart-meta"><span><i class="fa-solid fa-chart-line" aria-hidden="true"></i> Omzet harian</span><small>{{ $latestVisibleLabel ? 'Data hingga ' . $latestVisibleLabel : 'Belum ada tanggal data' }}</small></div>
    @if($trendPoints->isNotEmpty())
        <div class="trend-chart-scroll">
            <svg class="trend-chart-svg" viewBox="0 0 900 292" preserveAspectRatio="none" aria-hidden="true">
                <defs><linearGradient id="{{ $chartId ?? 'trend-chart' }}-fill" x1="0" x2="0" y1="0" y2="1"><stop offset="0%" stop-color="#b97931" stop-opacity=".23"/><stop offset="100%" stop-color="#b97931" stop-opacity=".015"/></linearGradient></defs>
                @for($gridLine = 0; $gridLine <= 4; $gridLine++)
                    @php
                        $gridY = $chartTop + ($chartHeight * $gridLine / 4);
                        $axisValue = $trendMaximum * (4 - $gridLine) / 4;
                        $axisLabel = $axisValue >= 1000000
                            ? 'Rp ' . rtrim(rtrim(number_format($axisValue / 1000000, 1, ',', '.'), '0'), ',') . ' jt'
                            : ($axisValue >= 1000 ? 'Rp ' . number_format($axisValue / 1000, 0, ',', '.') . ' rb' : 'Rp ' . number_format($axisValue, 0, ',', '.'));
                    @endphp
                    <line x1="{{ $chartLeft }}" y1="{{ $gridY }}" x2="{{ $chartRight }}" y2="{{ $gridY }}" class="trend-grid-line"/>
                    <text x="{{ $chartLeft - 12 }}" y="{{ $gridY + 4 }}" class="trend-axis-label" text-anchor="end">{{ $axisLabel }}</text>
                @endfor
                <path d="{{ $trendAreaPath }}" class="trend-area-path" fill="url(#{{ $chartId ?? 'trend-chart' }}-fill)"/>
                <path d="{{ $trendLinePath }}" class="trend-line-path"/>
                @foreach($trendPoints as $index => $point)
                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="{{ $index === $trendCount - 1 ? 5 : 3.2 }}" style="--point-index:{{ min($index, 30) }}" class="trend-point {{ $index === $trendCount - 1 ? 'is-current' : '' }}"><title>{{ $point['label'] }} · Rp {{ number_format($point['total'], 0, ',', '.') }}</title></circle>
                    @if($index % $labelInterval === 0 || $index === $trendCount - 1)
                        <text x="{{ $point['x'] }}" y="{{ $chartBottom + 24 }}" class="trend-date-label" text-anchor="middle">{{ $point['label'] }}</text>
                    @endif
                @endforeach
            </svg>
        </div>
    @else
        <div class="trend-chart-empty"><i class="fa-solid fa-chart-line"></i><span>Belum ada data penjualan pada periode ini.</span></div>
    @endif
</div>
