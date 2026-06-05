<style>
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

  body {
    background: var(--emis-surface) !important;
  }

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

  .emis-stat-card {
    transition: transform .25s cubic-bezier(.22,1,.36,1), box-shadow .25s;
    position: relative;
    overflow: hidden;
  }

  .emis-stat-card:hover {
    transform: translateY(-5px) scale(1.015);
    box-shadow: 0 20px 50px rgba(0,0,0,.18) !important;
  }

  .emis-stat-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.18) 0%, rgba(255,255,255,0) 60%);
    pointer-events: none;
    border-radius: var(--emis-radius);
  }

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

  .emis-chip.stat-glass {
    background: rgba(255,255,255,.2);
    color: rgba(255,255,255,.95);
    border-color: rgba(255,255,255,.3);
    backdrop-filter: blur(4px);
  }

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

  .emis-fade-up {
    opacity: 0;
    transform: translateY(18px);
    animation: emis-fade-up-anim .55s cubic-bezier(.22, 1, .36, 1) forwards;
  }

  @keyframes emis-fade-up-anim {
    to { opacity: 1; transform: translateY(0); }
  }

  .delay-1 { animation-delay: .08s; }
  .delay-2 { animation-delay: .16s; }
  .delay-3 { animation-delay: .24s; }
  .delay-4 { animation-delay: .32s; }
  .delay-5 { animation-delay: .40s; }
  .delay-6 { animation-delay: .48s; }
  .delay-7 { animation-delay: .56s; }
  .delay-8 { animation-delay: .64s; }

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

  .emis-table tbody tr:hover {
    background: var(--emis-surface);
  }

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
</style>
