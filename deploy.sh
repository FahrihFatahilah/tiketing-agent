#!/bin/bash
set -e

# ─────────────────────────────────────────
#  Agent Bus — Deploy Script
#  Usage:
#    ./deploy.sh          → first time / fresh deploy
#    ./deploy.sh update   → update (pull + rebuild + migrate)
# ─────────────────────────────────────────

APP_NAME="agent-bus"
ENV_FILE=".env"
ENV_TEMPLATE=".env.docker"

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

info()    { echo -e "${GREEN}[✔] $1${NC}"; }
warn()    { echo -e "${YELLOW}[!] $1${NC}"; }
error()   { echo -e "${RED}[✘] $1${NC}"; exit 1; }

# ── Cek docker tersedia ──────────────────
command -v docker >/dev/null 2>&1 || error "Docker tidak ditemukan. Install dulu: https://docs.docker.com/get-docker/"
command -v docker compose >/dev/null 2>&1 || error "Docker Compose tidak ditemukan."

MODE=${1:-"fresh"}

# ── MODE: UPDATE ─────────────────────────
if [ "$MODE" = "update" ]; then
    info "Mode: update"

    info "Rebuild image..."
    docker compose build --no-cache

    info "Restart container..."
    docker compose up -d

    info "Jalankan migrasi..."
    docker compose exec app php artisan migrate --force

    info "Clear cache..."
    docker compose exec app php artisan config:cache
    docker compose exec app php artisan route:cache
    docker compose exec app php artisan view:cache

    info "Deploy selesai!"
    exit 0
fi

# ── MODE: FRESH DEPLOY ───────────────────
info "Mode: fresh deploy"

# 1. Siapkan .env
if [ ! -f "$ENV_FILE" ]; then
    if [ ! -f "$ENV_TEMPLATE" ]; then
        error "$ENV_TEMPLATE tidak ditemukan."
    fi
    cp "$ENV_TEMPLATE" "$ENV_FILE"
    warn ".env dibuat dari $ENV_TEMPLATE"
    warn "Edit .env terlebih dahulu: DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_URL"
    warn "Setelah selesai, jalankan ulang: ./deploy.sh"
    exit 0
fi

# 2. Cek APP_KEY
APP_KEY=$(grep "^APP_KEY=" "$ENV_FILE" | cut -d '=' -f2)
if [ -z "$APP_KEY" ]; then
    info "Generate APP_KEY..."
    KEY=$(docker run --rm php:8.3-alpine php -r "echo 'base64:'.base64_encode(random_bytes(32));")
    sed -i "s|^APP_KEY=.*|APP_KEY=$KEY|" "$ENV_FILE"
    info "APP_KEY di-set."
fi

# 3. Build image
info "Build Docker image..."
docker compose build --no-cache

# 4. Jalankan container
info "Jalankan container..."
docker compose up -d

# 5. Tunggu container siap
info "Tunggu container siap..."
sleep 5

# 6. Migrate & seed
info "Jalankan migrasi..."
docker compose exec app php artisan migrate --force

read -p "Jalankan seeder? (data awal: admin, jadwal, armada) [y/N]: " RUN_SEED
if [[ "$RUN_SEED" =~ ^[Yy]$ ]]; then
    info "Jalankan seeder..."
    docker compose exec app php artisan db:seed --force
fi

# 7. Cache untuk production
info "Optimasi cache..."
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# 8. Set storage permissions
docker compose exec app php artisan storage:link 2>/dev/null || true

info "═══════════════════════════════════════"
info " Deploy selesai!"
info " App berjalan di: $(grep '^APP_URL=' $ENV_FILE | cut -d'=' -f2)"
info "═══════════════════════════════════════"
echo ""
echo "  Login default (jika seed dijalankan):"
echo "  Admin    → admin@agentbus.id / password"
echo "  Pengurus → pengurus@agentbus.id / password"
echo "  Agen     → agen@agentbus.id / password"
echo ""
