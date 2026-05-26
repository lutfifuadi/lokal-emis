#!/usr/bin/env bash
set -e

# ============================================================
#  update.sh — Update Aplikasi Lokal EMIS dari GitHub
#  Jalankan di live site setiap ada perubahan dari repo
# ============================================================

BOLD='\033[1m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

info()  { echo -e "${GREEN}[INFO]${NC} $1"; }
warn()  { echo -e "${YELLOW}[WARN]${NC} $1"; }
error() { echo -e "${RED}[ERROR]${NC} $1"; }
step()  { echo -e "\n${BOLD}━━━ $1 ━━━${NC}"; }

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$PROJECT_DIR"

if [ ! -f artisan ]; then
    error "Tidak dapat menemukan artisan. Jalankan dari root project Laravel."
    exit 1
fi

info "Project dir: $PROJECT_DIR"

# ── 1. Git Pull ──────────────────────────────────────────────
step "1. Tarik perubahan dari GitHub"

if [ ! -d .git ]; then
    error "Bukan repository git. Clone dulu: git clone <url> ."
    exit 1
fi

git pull origin main
info "Kode terbaru dari GitHub ✓"

# ── 2. Composer Install ─────────────────────────────────────
step "2. Update Dependencies PHP"

if command -v composer >/dev/null 2>&1; then
    composer install --no-dev --optimize-autoloader --no-interaction
    info "Composer dependencies siap ✓"
else
    warn "Composer tidak ditemukan, skip."
fi

# ── 3. Frontend Build ───────────────────────────────────────
step "3. Build Frontend (jika perlu)"

HAS_ASSETS=false
[ -f public/build/manifest.json ] && HAS_ASSETS=true

if command -v node >/dev/null 2>&1 && [ -f package.json ]; then
    if [ ! -d node_modules ]; then
        info "node_modules tidak ada, install dependencies..."
        npm install --legacy-peer-deps
    fi
    info "Build frontend assets..."
    npm run build
    info "Frontend build selesai ✓"
elif [ "$HAS_ASSETS" = true ]; then
    info "public/build sudah ada, lewati build frontend."
else
    warn "Node.js tidak tersedia dan public/build tidak ditemukan."
    warn "Frontend mungkin tidak tampil sempurna."
fi

# ── 4. Maintenance Mode ON ──────────────────────────────────
step "4. Maintenance mode ON"
php artisan down --retry=30 2>/dev/null || true
info "Maintenance mode aktif"

# ── 5. Migrate Database ─────────────────────────────────────
step "5. Migrasi Database"
php artisan migrate --force
info "Migrasi selesai ✓"

# ── 6. Optimasi ─────────────────────────────────────────────
step "6. Optimasi Cache"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
info "Optimasi selesai ✓"

# ── 7. Storage Link ─────────────────────────────────────────
step "7. Storage Link"
php artisan storage:link --force 2>/dev/null || true
info "Storage link siap ✓"

# ── 8. Permission ──────────────────────────────────────────
step "8. Set Permission"
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
info "Permission OK ✓"

# ── 9. Maintenance Mode OFF ────────────────────────────────
step "9. Maintenance mode OFF"
php artisan up
info "Aplikasi kembali aktif ✓"

# ── Selesai ─────────────────────────────────────────────────
step "Selesai!"
echo ""
echo -e "  ${GREEN}Aplikasi Lokal EMIS berhasil diupdate!${NC}"
echo ""
echo "  Perubahan yang dilakukan:"
echo "    - Code: git pull dari GitHub"
echo "    - PHP dependencies: composer install"
echo "    - Frontend: npm run build"
echo "    - Database: migrate"
echo "    - Cache: dioptimasi ulang"
echo ""

exit 0
