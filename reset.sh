#!/usr/bin/env bash
set -e

# ============================================================
#  reset.sh — Reset Aplikasi Lokal EMIS ke Default
#  Hapus semua data + migrasi ulang + seed
# ============================================================

BOLD='\033[1m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

info()  { echo -e "${GREEN}[INFO]${NC} $1"; }
warn()  { echo -e "${YELLOW}[WARN]${NC} $1"; }
error() { echo -e "${RED}[ERROR]${NC} $1"; }

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$PROJECT_DIR"

if [ ! -f artisan ]; then
    error "Tidak dapat menemukan artisan. Jalankan dari root project Laravel."
    exit 1
fi

echo ""
echo -e "${RED}${BOLD}╔══════════════════════════════════════════════╗${NC}"
echo -e "${RED}${BOLD}║       RESET APLIKASI LOKAL EMIS             ║${NC}"
echo -e "${RED}${BOLD}║  SEMUA DATA AKAN DIHAPUS!                   ║${NC}"
echo -e "${RED}${BOLD}╚══════════════════════════════════════════════╝${NC}"
echo ""
echo "  Data yang akan di-reset:"
echo "    - Seluruh tabel database"
echo "    - File upload (storage/app/public)"
echo "    - Cache & session"
echo "    - Log files"
echo ""

# ── Konfirmasi ──────────────────────────────────────────────
read -p "Ketik 'reset' untuk lanjut: " CONFIRM
if [ "$CONFIRM" != "reset" ]; then
    warn "Dibatalkan."
    exit 0
fi

echo ""
echo -e "${YELLOW}Konfirmasi terakhir! Semua data akan hilang permanen.${NC}"
read -p "Ketik nama database ini untuk konfirmasi: " DB_CONFIRM

# Ambil DB_DATABASE dari .env
DB_NAME=$(grep -oP '^DB_DATABASE=\K.*' .env 2>/dev/null || echo "")

if [ "$DB_CONFIRM" != "$DB_NAME" ]; then
    error "Nama database tidak cocok. Dibatalkan."
    exit 1
fi

# ── Mulai Reset ─────────────────────────────────────────────
echo ""
info "Memulai reset..."

# 1. Cache clear
info "Clear cache..."
php artisan optimize:clear 2>/dev/null || true

# 2. Hapus file upload
if [ -d storage/app/public ] && [ "$(ls -A storage/app/public 2>/dev/null)" ]; then
    info "Hapus file upload..."
    find storage/app/public -type f ! -name '.gitignore' -delete 2>/dev/null || true
fi

# 3. Hapus log
if [ -f storage/logs/laravel.log ]; then
    info "Hapus log..."
    > storage/logs/laravel.log
fi

# 4. Hapus session
if [ -d storage/framework/sessions ]; then
    info "Hapus session..."
    find storage/framework/sessions -type f ! -name '.gitignore' -delete 2>/dev/null || true
fi

# 5. Hapus cache
if [ -d bootstrap/cache ]; then
    info "Hapus bootstrap cache..."
    find bootstrap/cache -type f ! -name '.gitignore' -delete 2>/dev/null || true
fi

# 6. Migrate fresh + seed
info "Migrate fresh + seed..."
php artisan migrate:fresh --force --seed --quiet 2>&1 || {
    error "Migrasi gagal! Cek koneksi database."
    exit 1
}

# 7. Storage link
php artisan storage:link --force 2>/dev/null || true

# 8. Optimize
info "Optimize..."
php artisan optimize 2>/dev/null || true

echo ""
echo -e "${GREEN}${BOLD}━━━ Reset Selesai! ━━━${NC}"
echo ""
echo "  Database  : $DB_NAME"
echo "  Status    : ${GREEN}Bersih + siap pakai${NC}"
echo ""

APP_URL=$(grep -oP '^APP_URL=\K.*' .env 2>/dev/null || echo "http://localhost:8000")
echo "  Buka aplikasi: $APP_URL"
echo ""

exit 0
