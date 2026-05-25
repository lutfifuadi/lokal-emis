@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard Admin — EMIS')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('page-style')
  @vite('resources/assets/vendor/scss/pages/cards-advance.scss')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap"
    rel="stylesheet">

  <style>
    /* ================================================
       EMIS DASHBOARD — Design System
    ================================================ */
    :root {
      --emis-navy: #0f1f3d;
      --emis-navy-mid: #1a3360;
      --emis-emerald: #059669;
      --emis-emerald-lt: #d1fae5;
      --emis-amber: #d97706;
      --emis-amber-lt: #fef3c7;
      --emis-rose: #e11d48;
      --emis-rose-lt: #ffe4e6;
      --emis-sky: #0284c7;
      --emis-sky-lt: #e0f2fe;
      --emis-slate: #64748b;
      --emis-surface: #f8fafc;
      --emis-border: #e2e8f0;
      --emis-white: #ffffff;
      --emis-text-head: #0f172a;
      --emis-text-body: #475569;
      --emis-text-muted: #94a3b8;

      --emis-radius-sm: 5px;
      --emis-radius: 5px;
      --emis-radius-lg: 5px;

      --emis-shadow-sm: 0 1px 3px rgba(15, 31, 61, .06), 0 1px 2px rgba(15, 31, 61, .04);
      --emis-shadow: 0 4px 16px rgba(15, 31, 61, .08), 0 1px 4px rgba(15, 31, 61, .04);
      --emis-shadow-lg: 0 12px 40px rgba(15, 31, 61, .12), 0 4px 12px rgba(15, 31, 61, .06);
    }

    /* ── Global Reset ── */
    body {
      font-family: 'Quicksand', sans-serif !important;
      background: var(--emis-surface) !important;
    }

    /* ── Override default cards ── */
    .card {
      border: 1px solid var(--emis-border) !important;
      border-radius: var(--emis-radius) !important;
      box-shadow: var(--emis-shadow-sm) !important;
      background: var(--emis-white) !important;
      overflow: hidden;
    }

    .card-header {
      background: transparent !important;
      border-bottom: 1px solid var(--emis-border) !important;
      padding: 1.25rem 1.5rem !important;
    }

    .card-body {
      padding: 1.5rem !important;
    }

    .card-footer {
      background: var(--emis-surface) !important;
      border-top: 1px solid var(--emis-border) !important;
    }

    /* ── Dashboard Page Header ── */
    .emis-page-header {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      margin-bottom: 2rem;
      padding: 1.75rem 2rem;
      background: linear-gradient(135deg, var(--emis-navy) 0%, var(--emis-navy-mid) 100%);
      border-radius: var(--emis-radius-lg);
      position: relative;
      overflow: hidden;
    }

    .emis-page-header::before {
      content: '';
      position: absolute;
      inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .emis-page-header::after {
      content: '';
      position: absolute;
      right: -60px;
      top: -60px;
      width: 280px;
      height: 280px;
      background: radial-gradient(circle, rgba(5, 150, 105, .25) 0%, transparent 70%);
      pointer-events: none;
    }

    .emis-page-header-content {
      position: relative;
      z-index: 1;
    }

    .emis-page-header h1 {
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--emis-white);
      margin: 0 0 .25rem;
      letter-spacing: -.02em;
    }

    .emis-page-header p {
      color: rgba(255, 255, 255, .65);
      font-size: .875rem;
      margin: 0;
    }

    .emis-live-badge {
      position: relative;
      z-index: 1;
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      background: rgba(255, 255, 255, .12);
      border: 1px solid rgba(255, 255, 255, .2);
      color: rgba(255, 255, 255, .9);
      font-size: .75rem;
      font-weight: 600;
      padding: .45rem 1rem;
      border-radius: var(--emis-radius-sm);
      backdrop-filter: blur(8px);
    }

    .emis-live-dot {
      width: 7px;
      height: 7px;
      background: #4ade80;
      border-radius: 50%;
      animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {

      0%,
      100% {
        opacity: 1;
        transform: scale(1);
      }

      50% {
        opacity: .6;
        transform: scale(.8);
      }
    }

    /* ── Stat Cards — Colorful Gradient ── */
    .emis-stat-card {
      transition: transform .25s cubic-bezier(.22,1,.36,1), box-shadow .25s;
      position: relative;
      overflow: hidden;
    }

    .emis-stat-card:hover {
      transform: translateY(-5px) scale(1.015);
      box-shadow: 0 20px 50px rgba(0,0,0,.18) !important;
    }

    /* Shimmer overlay on hover */
    .emis-stat-card::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(255,255,255,.18) 0%, rgba(255,255,255,0) 60%);
      pointer-events: none;
      border-radius: var(--emis-radius);
    }

    /* Decorative circle blob */
    .emis-stat-card::before {
      content: '';
      position: absolute;
      right: -30px;
      bottom: -40px;
      width: 130px;
      height: 130px;
      border-radius: 50%;
      background: rgba(255,255,255,.1);
      pointer-events: none;
    }

    /* ── Card Gradient Themes ── */
    .stat-card-navy {
      background: linear-gradient(135deg, #0f1f3d 0%, #1e3a8a 50%, #312e81 100%) !important;
      border: none !important;
      box-shadow: 0 8px 32px rgba(15,31,61,.35) !important;
    }

    .stat-card-emerald {
      background: linear-gradient(135deg, #064e3b 0%, #059669 50%, #0d9488 100%) !important;
      border: none !important;
      box-shadow: 0 8px 32px rgba(5,150,105,.3) !important;
    }

    .stat-card-sky {
      background: linear-gradient(135deg, #0c4a6e 0%, #0284c7 50%, #7c3aed 100%) !important;
      border: none !important;
      box-shadow: 0 8px 32px rgba(2,132,199,.3) !important;
    }

    .stat-card-rose {
      background: linear-gradient(135deg, #881337 0%, #e11d48 50%, #ea580c 100%) !important;
      border: none !important;
      box-shadow: 0 8px 32px rgba(225,29,72,.3) !important;
    }

    .emis-stat-icon {
      width: 52px;
      height: 52px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: var(--emis-radius-sm);
      flex-shrink: 0;
      background: rgba(255,255,255,.18);
      backdrop-filter: blur(6px);
      border: 1px solid rgba(255,255,255,.25);
    }

    .emis-stat-icon i {
      font-size: 1.4rem;
      color: rgba(255,255,255,.95);
    }

    /* Legacy fallback — no longer used on colored cards */
    .emis-stat-icon.navy   { background: rgba(255,255,255,.15); color: #fff; }
    .emis-stat-icon.emerald{ background: rgba(255,255,255,.15); color: #fff; }
    .emis-stat-icon.sky    { background: rgba(255,255,255,.15); color: #fff; }
    .emis-stat-icon.rose   { background: rgba(255,255,255,.15); color: #fff; }

    .emis-stat-number {
      font-size: 2.2rem;
      font-weight: 800;
      color: #ffffff;
      letter-spacing: -.03em;
      line-height: 1;
      margin: .75rem 0 .5rem;
      text-shadow: 0 2px 8px rgba(0,0,0,.15);
    }

    .emis-stat-label {
      font-size: .7875rem;
      font-weight: 700;
      color: rgba(255,255,255,.9);
      letter-spacing: .01em;
    }

    .emis-stat-sub {
      font-size: .7rem;
      color: rgba(255,255,255,.6);
      margin-top: .15rem;
    }

    .emis-chip {
      display: inline-flex;
      align-items: center;
      gap: .3rem;
      padding: .25rem .65rem;
      border-radius: var(--emis-radius-sm);
      font-size: .7rem;
      font-weight: 600;
      border: 1px solid transparent;
    }

    /* Chips inside colorful cards — glassmorphism style */
    .emis-chip.stat-glass {
      background: rgba(255,255,255,.2);
      color: rgba(255,255,255,.95);
      border-color: rgba(255,255,255,.3);
      backdrop-filter: blur(4px);
    }

    /* Chips outside stat cards (in charts/tables) */
    .emis-chip.navy-chip {
      background: rgba(15, 31, 61, .07);
      color: var(--emis-navy);
      border-color: rgba(15, 31, 61, .12);
    }

    .emis-chip.amber-chip {
      background: var(--emis-amber-lt);
      color: var(--emis-amber);
      border-color: #fde68a;
    }

    .emis-chip.emerald-chip {
      background: var(--emis-emerald-lt);
      color: var(--emis-emerald);
      border-color: #a7f3d0;
    }

    .emis-chip.rose-chip {
      background: var(--emis-rose-lt);
      color: var(--emis-rose);
      border-color: #fecdd3;
    }

    /* ── Section Divider Labels ── */
    .emis-section-label {
      font-size: .6875rem;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--emis-text-muted);
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: .75rem;
    }

    .emis-section-label::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--emis-border);
    }

    /* ── Chart card titles ── */
    .emis-card-title {
      font-size: .9375rem;
      font-weight: 700;
      color: var(--emis-text-head);
      margin: 0 0 .2rem;
      letter-spacing: -.01em;
    }

    .emis-card-sub {
      font-size: .75rem;
      color: var(--emis-text-muted);
      margin: 0;
    }

    /* ── Approval Tracker Radial Items ── */
    .approval-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: .75rem 0;
      border-bottom: 1px solid var(--emis-border);
    }

    .approval-item:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .approval-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .approval-dot.rose {
      background: var(--emis-rose);
    }

    .approval-dot.emerald {
      background: var(--emis-emerald);
    }

    .approval-dot.slate {
      background: var(--emis-slate);
    }

    .approval-label {
      font-size: .8125rem;
      font-weight: 600;
      color: var(--emis-text-head);
    }

    .approval-count {
      font-size: .75rem;
      color: var(--emis-text-muted);
    }

    .approval-num {
      font-size: .9375rem;
      font-weight: 700;
      color: var(--emis-text-head);
      margin-left: auto;
    }

    /* ── Master Data List ── */
    .master-row {
      display: flex;
      align-items: center;
      padding: .875rem 0;
      border-bottom: 1px solid var(--emis-border);
      gap: 1rem;
    }

    .master-row:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .master-icon {
      width: 38px;
      height: 38px;
      border-radius: var(--emis-radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
      flex-shrink: 0;
    }

    .master-name {
      font-size: .8125rem;
      font-weight: 600;
      color: var(--emis-text-head);
    }

    .master-desc {
      font-size: .7rem;
      color: var(--emis-text-muted);
    }

    .master-val {
      font-size: 1.05rem;
      font-weight: 800;
      color: var(--emis-text-head);
      letter-spacing: -.02em;
      margin-left: auto;
    }

    /* ── School Detail Card ── */
    .school-badge-icon {
      width: 64px;
      height: 64px;
      background: linear-gradient(135deg, var(--emis-navy) 0%, var(--emis-navy-mid) 100%);
      border-radius: var(--emis-radius);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      box-shadow: 0 8px 24px rgba(15, 31, 61, .25);
    }

    .school-badge-icon i {
      font-size: 1.75rem;
      color: rgba(255, 255, 255, .9);
    }

    .school-info-row {
      display: flex;
      align-items: flex-start;
      gap: .875rem;
      padding: .75rem 0;
      border-top: 1px solid var(--emis-border);
    }

    .school-info-icon {
      width: 32px;
      height: 32px;
      background: var(--emis-surface);
      border-radius: var(--emis-radius-sm);
      border: 1px solid var(--emis-border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .9rem;
      color: var(--emis-slate);
      flex-shrink: 0;
      margin-top: 1px;
    }

    .school-info-label {
      font-size: .6875rem;
      font-weight: 600;
      color: var(--emis-text-muted);
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .school-info-val {
      font-size: .8125rem;
      font-weight: 500;
      color: var(--emis-text-body);
      margin-top: 1px;
    }

    /* ── Pending Table ── */
    .emis-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }

    .emis-table thead th {
      font-size: .6875rem;
      font-weight: 700;
      letter-spacing: .07em;
      text-transform: uppercase;
      color: var(--emis-text-muted);
      padding: .875rem 1.25rem;
      background: var(--emis-surface);
      border-bottom: 1px solid var(--emis-border);
      white-space: nowrap;
    }

    .emis-table thead th:first-child {
      border-radius: 0;
    }

    .emis-table tbody td {
      padding: 1rem 1.25rem;
      font-size: .8125rem;
      color: var(--emis-text-body);
      border-bottom: 1px solid var(--emis-border);
      vertical-align: middle;
    }

    .emis-table tbody tr:last-child td {
      border-bottom: none;
    }

    .emis-table tbody tr {
      transition: background .15s;
    }

    .emis-table tbody tr:hover {
      background: var(--emis-surface);
    }

    .emis-name-cell {
      font-weight: 600;
      color: var(--emis-text-head);
    }

    .emis-field-badge {
      font-family: 'DM Mono', monospace;
      font-size: .7rem;
      background: rgba(15, 31, 61, .06);
      color: var(--emis-navy);
      border: 1px solid rgba(15, 31, 61, .1);
      padding: .2rem .55rem;
      border-radius: 5px;
    }

    .emis-old-val {
      color: var(--emis-text-muted);
    }

    .emis-new-val {
      color: var(--emis-emerald);
      font-weight: 600;
    }

    .emis-date-cell {
      font-size: .75rem;
      color: var(--emis-text-muted);
    }

    .emis-badge-pending {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      background: #fff7ed;
      color: var(--emis-amber);
      border: 1px solid #fed7aa;
      font-size: .6875rem;
      font-weight: 600;
      padding: .3rem .7rem;
      border-radius: var(--emis-radius-sm);
    }

    .emis-badge-pending::before {
      content: '';
      width: 6px;
      height: 6px;
      background: var(--emis-amber);
      border-radius: 50%;
      animation: pulse-dot 1.8s infinite;
    }

    /* ── Btn overrides ── */
    .btn-emis-primary {
      background: var(--emis-navy);
      color: var(--emis-white);
      border: none;
      font-size: .8125rem;
      font-weight: 600;
      padding: .55rem 1.25rem;
      border-radius: var(--emis-radius-sm);
      transition: background .2s, transform .15s;
      display: inline-flex;
      align-items: center;
      gap: .4rem;
    }

    .btn-emis-primary:hover {
      background: var(--emis-navy-mid);
      transform: translateY(-1px);
      color: white;
    }

    .btn-emis-outline {
      background: transparent;
      color: var(--emis-navy);
      border: 1px solid var(--emis-border);
      font-size: .8125rem;
      font-weight: 600;
      padding: .5rem 1.15rem;
      border-radius: var(--emis-radius-sm);
      transition: border-color .2s, background .2s;
      display: inline-flex;
      align-items: center;
      gap: .4rem;
    }

    .btn-emis-outline:hover {
      border-color: var(--emis-navy);
      background: rgba(15, 31, 61, .04);
      color: var(--emis-navy);
    }

    /* ── Entry animations ── */
    .emis-fade-up {
      opacity: 0;
      transform: translateY(18px);
      animation: emis-fade-up-anim .55s cubic-bezier(.22, 1, .36, 1) forwards;
    }

    @keyframes emis-fade-up-anim {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .delay-1 {
      animation-delay: .08s;
    }

    .delay-2 {
      animation-delay: .16s;
    }

    .delay-3 {
      animation-delay: .24s;
    }

    .delay-4 {
      animation-delay: .32s;
    }

    .delay-5 {
      animation-delay: .40s;
    }

    .delay-6 {
      animation-delay: .48s;
    }

    .delay-7 {
      animation-delay: .56s;
    }

    .delay-8 {
      animation-delay: .64s;
    }

    /* ── Empty state ── */
    .emis-empty {
      text-align: center;
      padding: 2.5rem 1rem;
    }

    .emis-empty i {
      font-size: 2.5rem;
      color: var(--emis-emerald);
      display: block;
      margin-bottom: .75rem;
    }

    .emis-empty p {
      font-size: .875rem;
      color: var(--emis-text-muted);
      margin: 0;
    }
  </style>
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-script')
  <script>
    document.addEventListener('DOMContentLoaded', function() {

      // ==========================================
      // Chart 1 — Bar Chart: Pengajuan Per Bulan
      // ==========================================
      const chartLabels = @json($chartLabels);
      const chartData = @json($chartData);

      const barOptions = {
        chart: {
          type: 'bar',
          height: 210,
          toolbar: {
            show: false
          },
          fontFamily: 'Quicksand, sans-serif',
          background: 'transparent'
        },
        series: [{
          name: 'Pengajuan',
          data: chartData
        }],
        xaxis: {
          categories: chartLabels,
          labels: {
            style: {
              fontSize: '11px',
              fontWeight: 500,
              colors: '#94a3b8'
            }
          },
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          }
        },
        yaxis: {
          labels: {
            show: false
          }
        },
        plotOptions: {
          bar: {
            borderRadius: 7,
            columnWidth: '45%',
            distributed: false
          }
        },
        fill: {
          type: 'gradient',
          gradient: {
            type: 'vertical',
            gradientToColors: ['#059669'],
            stops: [0, 100],
            colorStops: [{
                offset: 0,
                color: '#0f1f3d',
                opacity: 1
              },
              {
                offset: 100,
                color: '#1a3360',
                opacity: 1
              }
            ]
          }
        },
        dataLabels: {
          enabled: false
        },
        grid: {
          show: true,
          borderColor: '#e2e8f0',
          strokeDashArray: 4,
          yaxis: {
            lines: {
              show: true
            }
          },
          xaxis: {
            lines: {
              show: false
            }
          }
        },
        tooltip: {
          theme: 'light',
          y: {
            formatter: (val) => val + ' pengajuan'
          },
          style: {
            fontFamily: 'Quicksand, sans-serif',
            fontSize: '12px'
          }
        },
        states: {
          hover: {
            filter: {
              type: 'none'
            }
          }
        }
      };
      new ApexCharts(document.querySelector('#perubahanBarChart'), barOptions).render();

      // ==========================================
      // Chart 2 — Donut Chart: Siswa per Gender
      // ==========================================
      const totalSiswaLaki = {{ $siswaLakilaki }};
      const totalSiswaPerem = {{ $siswaPerempuan }};
      const totalSiswa = totalSiswaLaki + totalSiswaPerem || 1;

      const donutOptions = {
        chart: {
          type: 'donut',
          height: 220,
          fontFamily: 'Quicksand, sans-serif',
          background: 'transparent'
        },
        series: [totalSiswaLaki, totalSiswaPerem],
        labels: ['Laki-laki', 'Perempuan'],
        colors: ['#0f1f3d', '#d97706'],
        plotOptions: {
          pie: {
            donut: {
              size: '74%',
              labels: {
                show: true,
                name: {
                  show: true,
                  fontSize: '13px',
                  fontWeight: 600,
                  color: '#94a3b8'
                },
                value: {
                  show: true,
                  fontSize: '22px',
                  fontWeight: 800,
                  color: '#0f172a',
                  formatter: (val) => val
                },
                total: {
                  show: true,
                  label: 'Total Siswa',
                  fontSize: '12px',
                  fontWeight: 600,
                  color: '#94a3b8',
                  formatter: () => totalSiswa
                }
              }
            }
          }
        },
        legend: {
          show: false
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          width: 3,
          colors: ['#ffffff']
        },
        tooltip: {
          style: {
            fontFamily: 'Quicksand, sans-serif'
          }
        }
      };
      new ApexCharts(document.querySelector('#siswaGenderChart'), donutOptions).render();

      // ==========================================
      // Chart 3 — Radial: Approval Tracker
      // ==========================================
      const approvalTotal = {{ $approvalTotal }};
      const approvalPending = {{ $approvalStats['pending'] }};
      const pctPending = Math.round((approvalPending / approvalTotal) * 100) || 0;

      const radialOptions = {
        chart: {
          type: 'radialBar',
          height: 160,
          fontFamily: 'Quicksand, sans-serif',
          background: 'transparent',
          sparkline: {
            enabled: true
          }
        },
        series: [pctPending],
        labels: ['Pending'],
        colors: ['#e11d48'],
        plotOptions: {
          radialBar: {
            hollow: {
              size: '68%',
              background: 'transparent'
            },
            track: {
              background: '#f1f5f9',
              strokeWidth: '90%'
            },
            dataLabels: {
              name: {
                show: false
              },
              value: {
                fontSize: '20px',
                fontWeight: 800,
                offsetY: 8,
                color: '#0f172a'
              }
            }
          }
        }
      };
      new ApexCharts(document.querySelector('#approvalRadialChart'), radialOptions).render();

    });
  </script>
@endsection

@section('content')

  {{-- ============================================ --}}
  {{-- PAGE HEADER                                 --}}
  {{-- ============================================ --}}
  <div class="emis-page-header emis-fade-up mb-6">
    <div class="emis-page-header-content">
      <h1><i class="ti tabler-layout-dashboard me-2"
          style="font-size:1.3rem;vertical-align:middle;opacity:.8;"></i>Dashboard Admin</h1>
      <p>Selamat datang kembali — Ringkasan sistem EMIS hari ini</p>
    </div>
    <div class="emis-live-badge">
      <span class="emis-live-dot"></span>
      Data Real-time
    </div>
  </div>

  {{-- ============================================ --}}
  {{-- ROW 1: Stat Cards                           --}}
  {{-- ============================================ --}}
  <p class="emis-section-label emis-fade-up delay-1">Metrik Utama</p>

  <div class="row g-4 mb-5">

    {{-- Total Siswa --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card emis-stat-card stat-card-navy h-100 emis-fade-up delay-2">
        <div class="card-body" style="padding: 1.5rem !important; position:relative; z-index:1;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="emis-stat-icon">
              <i class="ti tabler-user-check"></i>
            </div>
            <span class="emis-chip stat-glass">Siswa</span>
          </div>
          <div class="emis-stat-number">{{ number_format($stats['total_siswa']) }}</div>
          <div class="emis-stat-label">Total Siswa</div>
          <div class="emis-stat-sub">Terdaftar aktif di sistem</div>
          <div class="d-flex gap-2 mt-3">
            <span class="emis-chip stat-glass">
              <i class="ti tabler-gender-male" style="font-size:.8rem;"></i>
              {{ $siswaLakilaki }} L
            </span>
            <span class="emis-chip stat-glass">
              <i class="ti tabler-gender-female" style="font-size:.8rem;"></i>
              {{ $siswaPerempuan }} P
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- Total Guru --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card emis-stat-card stat-card-emerald h-100 emis-fade-up delay-3">
        <div class="card-body" style="padding: 1.5rem !important; position:relative; z-index:1;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="emis-stat-icon">
              <i class="ti tabler-users"></i>
            </div>
            <span class="emis-chip stat-glass">Pengajar</span>
          </div>
          <div class="emis-stat-number">{{ number_format($stats['total_guru']) }}</div>
          <div class="emis-stat-label">Total Guru</div>
          <div class="emis-stat-sub">Tenaga pengajar aktif</div>
          <div class="mt-3">
            <span class="emis-chip stat-glass">
              <i class="ti tabler-building-bank" style="font-size:.8rem;"></i>
              {{ $stats['total_sekolah'] }} Madrasah
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- Total Kelas --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card emis-stat-card stat-card-sky h-100 emis-fade-up delay-4">
        <div class="card-body" style="padding: 1.5rem !important; position:relative; z-index:1;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="emis-stat-icon">
              <i class="ti tabler-door-enter"></i>
            </div>
            <span class="emis-chip stat-glass">Kelas</span>
          </div>
          <div class="emis-stat-number">{{ number_format($stats['total_kelas']) }}</div>
          <div class="emis-stat-label">Total Kelas</div>
          <div class="emis-stat-sub">Rombongan belajar aktif</div>
          <div class="mt-3">
            <span class="emis-chip stat-glass">
              <i class="ti tabler-git-branch" style="font-size:.8rem;"></i>
              {{ $stats['total_jurusan'] }} Jurusan
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- Antrian Pending --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card emis-stat-card stat-card-rose h-100 emis-fade-up delay-5">
        <div class="card-body" style="padding: 1.5rem !important; position:relative; z-index:1;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="emis-stat-icon">
              <i class="ti tabler-checklist"></i>
            </div>
            <span class="emis-chip stat-glass">Antrian</span>
          </div>
          <div class="emis-stat-number">{{ number_format($approvalStats['pending']) }}</div>
          <div class="emis-stat-label">Antrian Pending</div>
          <div class="emis-stat-sub">Menunggu persetujuan biodata</div>
          <div class="d-flex gap-2 mt-3">
            <span class="emis-chip stat-glass">
              <i class="ti tabler-check" style="font-size:.8rem;"></i>
              {{ $approvalStats['approved'] }}
            </span>
            <span class="emis-chip stat-glass">
              <i class="ti tabler-x" style="font-size:.8rem;"></i>
              {{ $approvalStats['rejected'] }}
            </span>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- ============================================ --}}
  {{-- ROW 2: Charts                               --}}
  {{-- ============================================ --}}
  <p class="emis-section-label emis-fade-up delay-5">Visualisasi Data</p>

  <div class="row g-4 mb-5">

    {{-- Bar Chart: Pengajuan 6 Bulan --}}
    <div class="col-xl-8 col-12">
      <div class="card h-100 emis-fade-up delay-6">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div>
            <h5 class="emis-card-title">Tren Pengajuan Perubahan Biodata</h5>
            <p class="emis-card-sub">6 Bulan Terakhir</p>
          </div>
          <a href="{{ route('admin.master.siswa') }}" class="btn-emis-outline text-decoration-none">
            <i class="ti tabler-external-link" style="font-size:.85rem;"></i> Master Siswa
          </a>
        </div>
        <div class="card-body" style="padding-top: 1rem !important;">
          <div id="perubahanBarChart"></div>
        </div>
      </div>
    </div>

    {{-- Donut Chart: Gender Siswa --}}
    <div class="col-xl-4 col-md-6">
      <div class="card h-100 emis-fade-up delay-7">
        <div class="card-header">
          <h5 class="emis-card-title">Sebaran Gender Siswa</h5>
          <p class="emis-card-sub">Distribusi Laki-laki & Perempuan</p>
        </div>
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <div id="siswaGenderChart" class="w-100"></div>
          <div class="row w-100 mt-2 g-3">
            <div class="col-6">
              <div
                style="background:rgba(15,31,61,.04);border:1px solid rgba(15,31,61,.1);border-radius:var(--emis-radius-sm);padding:.875rem;text-align:center;">
                <div class="emis-stat-sub mb-1"><i class="ti tabler-gender-male me-1"></i>Laki-laki</div>
                <div style="font-size:1.3rem;font-weight:800;color:var(--emis-text-head);letter-spacing:-.02em;">
                  {{ $siswaLakilaki }}</div>
              </div>
            </div>
            <div class="col-6">
              <div
                style="background:var(--emis-amber-lt);border:1px solid #fde68a;border-radius:var(--emis-radius-sm);padding:.875rem;text-align:center;">
                <div class="emis-stat-sub mb-1" style="color:var(--emis-amber);"><i
                    class="ti tabler-gender-female me-1"></i>Perempuan</div>
                <div style="font-size:1.3rem;font-weight:800;color:var(--emis-amber);letter-spacing:-.02em;">
                  {{ $siswaPerempuan }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- ============================================ --}}
  {{-- ROW 3: Approval + Master Data + Sekolah     --}}
  {{-- ============================================ --}}
  <p class="emis-section-label emis-fade-up delay-6">Ringkasan Sistem</p>

  <div class="row g-4 mb-5">

    {{-- Approval Tracker --}}
    <div class="col-xl-4 col-md-6">
      <div class="card h-100 emis-fade-up delay-6">
        <div class="card-header">
          <h5 class="emis-card-title">Approval Tracker</h5>
          <p class="emis-card-sub">Ringkasan status pengajuan</p>
        </div>
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-center mb-4">
            <div id="approvalRadialChart"></div>
          </div>
          <div>
            <div class="approval-item">
              <span class="approval-dot rose"></span>
              <div>
                <div class="approval-label">Pending</div>
                <div class="approval-count">Menunggu tindakan</div>
              </div>
              <div class="approval-num">{{ $approvalStats['pending'] }}</div>
            </div>
            <div class="approval-item">
              <span class="approval-dot emerald"></span>
              <div>
                <div class="approval-label">Disetujui</div>
                <div class="approval-count">Berhasil diproses</div>
              </div>
              <div class="approval-num">{{ $approvalStats['approved'] }}</div>
            </div>
            <div class="approval-item">
              <span class="approval-dot slate"></span>
              <div>
                <div class="approval-label">Ditolak</div>
                <div class="approval-count">Tidak memenuhi syarat</div>
              </div>
              <div class="approval-num">{{ $approvalStats['rejected'] }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Master Data Overview --}}
    <div class="col-xl-4 col-md-6">
      <div class="card h-100 emis-fade-up delay-7">
        <div class="card-header">
          <h5 class="emis-card-title">Ringkasan Master Data</h5>
          <p class="emis-card-sub">Statistik keseluruhan sistem</p>
        </div>
        <div class="card-body">
          <div class="master-row">
            <div class="master-icon" style="background:rgba(15,31,61,.07);color:var(--emis-navy);">
              <i class="ti tabler-building-bank"></i>
            </div>
            <div>
              <div class="master-name">Total Madrasah</div>
              <div class="master-desc">Sekolah terdaftar</div>
            </div>
            <div class="master-val">{{ $stats['total_sekolah'] }}</div>
          </div>
          <div class="master-row">
            <div class="master-icon" style="background:var(--emis-emerald-lt);color:var(--emis-emerald);">
              <i class="ti tabler-git-branch"></i>
            </div>
            <div>
              <div class="master-name">Total Jurusan</div>
              <div class="master-desc">Program keahlian</div>
            </div>
            <div class="master-val">{{ $stats['total_jurusan'] }}</div>
          </div>
          <div class="master-row">
            <div class="master-icon" style="background:var(--emis-sky-lt);color:var(--emis-sky);">
              <i class="ti tabler-door-enter"></i>
            </div>
            <div>
              <div class="master-name">Total Kelas</div>
              <div class="master-desc">Rombongan belajar</div>
            </div>
            <div class="master-val">{{ $stats['total_kelas'] }}</div>
          </div>
          <div class="master-row">
            <div class="master-icon" style="background:var(--emis-amber-lt);color:var(--emis-amber);">
              <i class="ti tabler-users"></i>
            </div>
            <div>
              <div class="master-name">Total Pengguna</div>
              <div class="master-desc">Akun aktif sistem</div>
            </div>
            <div class="master-val">{{ $stats['total_users'] }}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Detail Madrasah --}}
    <div class="col-xl-4 col-md-12">
      <div class="card h-100 emis-fade-up delay-8">
        <div class="card-header">
          <h5 class="emis-card-title">Detail Madrasah</h5>
          <p class="emis-card-sub">Informasi madrasah utama</p>
        </div>
        <div class="card-body">
          @if ($sekolahDefault)
            <div class="text-center mb-3">
              <div class="school-badge-icon">
                <i class="ti tabler-building-fortress"></i>
              </div>
              <h6 style="font-size:.9375rem;font-weight:700;color:var(--emis-text-head);margin-bottom:.4rem;">
                {{ $sekolahDefault->nama }}</h6>
              <span
                style="font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--emis-slate);background:var(--emis-surface);border:1px solid var(--emis-border);padding:.25rem .75rem;border-radius:100px;">NPSN:
                {{ $sekolahDefault->npsn }}</span>
            </div>
            <div class="school-info-row">
              <div class="school-info-icon"><i class="ti tabler-map-pin"></i></div>
              <div>
                <div class="school-info-label">Alamat</div>
                <div class="school-info-val">{{ $sekolahDefault->alamat }}</div>
              </div>
            </div>
            <div class="school-info-row">
              <div class="school-info-icon"><i class="ti tabler-phone"></i></div>
              <div>
                <div class="school-info-label">Kontak</div>
                <div class="school-info-val">{{ $sekolahDefault->kontak }}</div>
              </div>
            </div>
            <div class="school-info-row">
              <div class="school-info-icon"><i class="ti tabler-mail"></i></div>
              <div>
                <div class="school-info-label">Email</div>
                <div class="school-info-val">{{ $sekolahDefault->email }}</div>
              </div>
            </div>
          @else
            <div class="emis-empty d-flex flex-column align-items-center justify-content-center h-100 py-5">
              <i class="ti tabler-building-off"></i>
              <p>Data madrasah belum tersedia.</p>
              <a href="{{ route('admin.master.sekolah') }}" class="btn-emis-primary text-decoration-none mt-3">
                <i class="ti tabler-plus" style="font-size:.85rem;"></i> Tambah Madrasah
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>

  {{-- ============================================ --}}
  {{-- ROW 4: Tabel Antrian Pending Terbaru        --}}
  {{-- ============================================ --}}
  <p class="emis-section-label emis-fade-up delay-7">Antrian Terbaru</p>

  <div class="row g-4 emis-fade-up delay-8">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div>
            <h5 class="emis-card-title">5 Antrian Persetujuan Terbaru</h5>
            <p class="emis-card-sub">Pengajuan perubahan biodata yang belum ditindaklanjuti</p>
          </div>
          @if ($pendingApprovalsCount > 0)
            <span class="emis-badge-pending">{{ $pendingApprovalsCount }} Pending</span>
          @else
            <span class="emis-chip emerald-chip"><i class="ti tabler-circle-check" style="font-size:.8rem;"></i> Semua
              bersih</span>
          @endif
        </div>

        <div class="table-responsive">
          <table class="emis-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama Siswa</th>
                <th>Field Diubah</th>
                <th>Nilai Lama</th>
                <th>Nilai Baru</th>
                <th>Diajukan Oleh</th>
                <th>Tanggal</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentPending as $i => $item)
                <tr>
                  <td style="font-size:.75rem;color:var(--emis-text-muted);font-weight:600;">{{ $i + 1 }}</td>
                  <td><span class="emis-name-cell">{{ $item->siswa->nama ?? '—' }}</span></td>
                  <td><span class="emis-field-badge">{{ $item->field }}</span></td>
                  <td><span class="emis-old-val">{{ str($item->old_value)->limit(25) }}</span></td>
                  <td><span class="emis-new-val">{{ str($item->new_value)->limit(25) }}</span></td>
                  <td>{{ $item->user->name ?? '—' }}</td>
                  <td><span class="emis-date-cell">{{ $item->created_at->translatedFormat('d M Y') }}</span></td>
                  <td><span class="emis-badge-pending">Pending</span></td>
                </tr>
              @empty
                <tr>
                  <td colspan="8">
                    <div class="emis-empty">
                      <i class="ti tabler-circle-check"></i>
                      <p>Tidak ada antrian pengajuan yang pending.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if ($pendingApprovalsCount > 5)
          <div class="card-footer text-center py-3">
            <a href="{{ route('admin.master.siswa') }}" class="btn-emis-outline text-decoration-none">
              <i class="ti tabler-list" style="font-size:.85rem;"></i>
              Lihat Semua {{ $pendingApprovalsCount }} Pengajuan
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>

@endsection
