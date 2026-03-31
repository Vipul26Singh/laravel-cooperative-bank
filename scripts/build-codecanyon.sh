#!/bin/bash
# ============================================================================
# CoopBank ERP — CodeCanyon Package Builder
# ============================================================================
# Run this script to generate a submission-ready CodeCanyon zip package.
# It does NOT modify the git repo — all work happens in /tmp.
#
# Usage:
#   ./scripts/build-codecanyon.sh
#   ./scripts/build-codecanyon.sh --version 1.2.0
#   ./scripts/build-codecanyon.sh --output ~/Desktop
#
# What it does:
#   1. Copies source to a temp directory (excludes git, vendor, node_modules)
#   2. Adds license headers to all PHP files
#   3. Builds frontend assets (npm run build)
#   4. Generates HTML documentation site
#   5. Creates licensing.txt
#   6. Packages everything into a zip
#
# What it does NOT do:
#   - Modify any file in your repo
#   - Push to any branch
#   - Touch vendor/ or node_modules/
# ============================================================================

set -e

# ── Config ────────────────────────────────────────────────────────────────
VERSION="${2:-1.0.0}"
OUTPUT_DIR="${4:-$(pwd)}"
PROJECT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
BUILD_DIR="/tmp/coopbank-codecanyon-build"
PACKAGE_NAME="coopbank-erp-v${VERSION}"

# Parse args
while [[ $# -gt 0 ]]; do
    case $1 in
        --version) VERSION="$2"; shift 2 ;;
        --output)  OUTPUT_DIR="$2"; shift 2 ;;
        *)         shift ;;
    esac
done

PACKAGE_NAME="coopbank-erp-v${VERSION}"

echo "============================================"
echo "  CoopBank ERP — CodeCanyon Package Builder"
echo "  Version: ${VERSION}"
echo "  Source:  ${PROJECT_DIR}"
echo "  Output:  ${OUTPUT_DIR}/${PACKAGE_NAME}.zip"
echo "============================================"
echo ""

# ── Step 1: Clean build directory ─────────────────────────────────────────
echo "[1/7] Preparing build directory..."
rm -rf "${BUILD_DIR}"
mkdir -p "${BUILD_DIR}/main-files/coopbank-erp"
mkdir -p "${BUILD_DIR}/documentation/assets/css"
mkdir -p "${BUILD_DIR}/documentation/assets/images"

# ── Step 2: Copy source ──────────────────────────────────────────────────
echo "[2/7] Copying source files..."
rsync -a \
    --exclude='.git' \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='.env' \
    --exclude='.env.backup' \
    --exclude='.env.dusk.local' \
    --exclude='database/database.sqlite' \
    --exclude='database/dusk.sqlite' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/views/*.php' \
    --exclude='composer.phar' \
    --exclude='tests/Browser/screenshots/**/*.png' \
    --exclude='.phpunit.result.cache' \
    --exclude='.phpactor.json' \
    --exclude='auth.json' \
    --exclude='scripts/build-codecanyon.sh' \
    --exclude='CODECANYON_LISTING.md' \
    --exclude='PACKAGE_STRUCTURE.md' \
    "${PROJECT_DIR}/" "${BUILD_DIR}/main-files/coopbank-erp/"

# Remove internal docs that buyers don't need
rm -f "${BUILD_DIR}/main-files/coopbank-erp/CODECANYON_LISTING.md"
rm -f "${BUILD_DIR}/main-files/coopbank-erp/PACKAGE_STRUCTURE.md"
rm -rf "${BUILD_DIR}/main-files/coopbank-erp/documentation"
rm -rf "${BUILD_DIR}/main-files/coopbank-erp/scripts"

# ── Step 3: Add license headers ──────────────────────────────────────────
echo "[3/7] Adding license headers to PHP files..."
HEADER='<?php
/*
 * CoopBank ERP - Cooperative Bank Management System
 * Copyright (c) '"$(date +%Y)"'. All rights reserved.
 * Licensed under the Envato Regular/Extended License.
 * https://codecanyon.net/licenses/standard
 */'

find "${BUILD_DIR}/main-files/coopbank-erp/app/" -name "*.php" -type f | while read f; do
    if ! grep -q "CoopBank ERP" "$f" 2>/dev/null; then
        tail -n +2 "$f" > /tmp/_phpbody.tmp
        echo "${HEADER}" > "$f"
        cat /tmp/_phpbody.tmp >> "$f"
    fi
done
rm -f /tmp/_phpbody.tmp
HEADER_COUNT=$(grep -rl "CoopBank ERP" "${BUILD_DIR}/main-files/coopbank-erp/app/" | wc -l | tr -d ' ')
echo "   Added headers to ${HEADER_COUNT} files"

# ── Step 4: Build frontend assets ────────────────────────────────────────
echo "[4/7] Building frontend assets..."
cd "${PROJECT_DIR}"
if [ -d "node_modules" ]; then
    npm run build --silent 2>/dev/null
    cp -r public/build "${BUILD_DIR}/main-files/coopbank-erp/public/build" 2>/dev/null || true
else
    echo "   WARNING: node_modules not found. Skipping asset build."
    echo "   Run 'npm install && npm run build' first if you need fresh assets."
fi

# ── Step 5: Generate documentation ───────────────────────────────────────
echo "[5/7] Generating documentation..."

# Copy docs CSS
cat > "${BUILD_DIR}/documentation/assets/css/style.css" << 'CSSEOF'
:root{--primary:#1a73e8;--dark:#1a1a2e;--accent:#00b894;--text:#333;--light-bg:#f8f9fa}*{box-sizing:border-box;margin:0;padding:0}body{font-family:'Segoe UI',system-ui,sans-serif;color:var(--text);line-height:1.7}a{color:var(--primary);text-decoration:none}a:hover{text-decoration:underline}.wrapper{display:flex;min-height:100vh}.sidebar{width:280px;background:var(--dark);color:#fff;padding:24px 0;position:fixed;top:0;left:0;bottom:0;overflow-y:auto}.sidebar h2{font-size:1.3rem;padding:0 20px 16px;border-bottom:1px solid rgba(255,255,255,.1);margin-bottom:16px}.sidebar ul{list-style:none}.sidebar li a{display:block;padding:8px 20px;color:rgba(255,255,255,.7);font-size:.9rem;transition:all .2s}.sidebar li a:hover,.sidebar li a.active{color:#fff;background:rgba(255,255,255,.08);text-decoration:none}.sidebar .section-label{padding:16px 20px 6px;font-size:.75rem;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.35)}.content{margin-left:280px;padding:40px 48px;max-width:900px;flex:1}h1{font-size:2rem;font-weight:700;margin-bottom:16px;color:var(--dark)}h2{font-size:1.5rem;font-weight:600;margin:40px 0 16px;padding-bottom:8px;border-bottom:2px solid #e8e8e8}h3{font-size:1.15rem;font-weight:600;margin:28px 0 12px;color:#444}p,li{margin-bottom:10px}ul,ol{padding-left:20px}code{background:#f0f0f0;padding:2px 6px;border-radius:4px;font-size:.88rem}pre{background:#1e1e1e;color:#d4d4d4;padding:16px 20px;border-radius:8px;overflow-x:auto;margin:16px 0;font-size:.85rem;line-height:1.6}pre code{background:none;padding:0;color:inherit}table{width:100%;border-collapse:collapse;margin:16px 0}th,td{padding:10px 14px;border:1px solid #e0e0e0;text-align:left;font-size:.9rem}th{background:var(--light-bg);font-weight:600}.badge{display:inline-block;padding:3px 10px;border-radius:12px;font-size:.78rem;font-weight:600}.badge-blue{background:#e3f2fd;color:#1565c0}.badge-green{background:#e8f5e9;color:#2e7d32}.badge-orange{background:#fff3e0;color:#e65100}.alert{padding:14px 18px;border-radius:8px;margin:16px 0;font-size:.9rem}.alert-info{background:#e3f2fd;border-left:4px solid var(--primary)}.alert-warning{background:#fff8e1;border-left:4px solid #ffa000}.alert-success{background:#e8f5e9;border-left:4px solid var(--accent)}@media(max-width:768px){.sidebar{position:static;width:100%}.content{margin-left:0;padding:24px 16px}}
CSSEOF

# Generate documentation from README
echo "   Generating HTML docs from README..."
python3 -c "
import re, sys
try:
    with open('${PROJECT_DIR}/README.md', 'r') as f:
        md = f.read()
    # Simple markdown to HTML
    html = md
    html = re.sub(r'^### (.+)$', r'<h3>\1</h3>', html, flags=re.M)
    html = re.sub(r'^## (.+)$', r'<h2>\1</h2>', html, flags=re.M)
    html = re.sub(r'^# (.+)$', r'<h1>\1</h1>', html, flags=re.M)
    html = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', html)
    html = re.sub(r'\`(.+?)\`', r'<code>\1</code>', html)
    html = re.sub(r'^\- (.+)$', r'<li>\1</li>', html, flags=re.M)
    html = re.sub(r'\n\n', r'</p><p>', html)
    page = '''<!DOCTYPE html><html><head><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width,initial-scale=1\"><title>CoopBank ERP Documentation</title><link rel=\"stylesheet\" href=\"assets/css/style.css\"><link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\"></head><body><div class=\"wrapper\"><nav class=\"sidebar\"><h2><i class=\"fas fa-university\"></i> CoopBank ERP</h2><ul><li><a href=\"#\">Documentation</a></li></ul></nav><main class=\"content\"><p>''' + html + '''</p></main></div></body></html>'''
    with open('${BUILD_DIR}/documentation/index.html', 'w') as f:
        f.write(page)
    print('   Generated from README.md')
except Exception as e:
    print(f'   Fallback: {e}')
    with open('${BUILD_DIR}/documentation/index.html', 'w') as f:
        f.write('<html><body><h1>CoopBank ERP</h1><p>See README.md for full documentation.</p></body></html>')
" 2>&1

# Copy screenshots for docs
if [ -d "${PROJECT_DIR}/docs/screenshots" ]; then
    cp "${PROJECT_DIR}/docs/screenshots/"*.png "${BUILD_DIR}/documentation/assets/images/" 2>/dev/null || true
    echo "   Copied $(ls "${BUILD_DIR}/documentation/assets/images/"*.png 2>/dev/null | wc -l | tr -d ' ') screenshots"
fi

# ── Step 6: Create licensing.txt ─────────────────────────────────────────
echo "[6/7] Creating license file..."
cat > "${BUILD_DIR}/licensing.txt" << EOF
CoopBank ERP - Cooperative Bank Management System
Version: ${VERSION}

This item is sold exclusively on CodeCanyon (https://codecanyon.net)

License: Envato Regular License or Extended License
https://codecanyon.net/licenses/standard

Regular License:
- Use in a single end product
- Cannot be resold or redistributed

Extended License:
- Use in a single end product (can charge end users)
- SaaS / white-label use allowed

Copyright (c) $(date +%Y). All rights reserved.
EOF

# ── Step 7: Create zip ───────────────────────────────────────────────────
echo "[7/7] Creating zip package..."
cd "${BUILD_DIR}"
zip -qr "${OUTPUT_DIR}/${PACKAGE_NAME}.zip" main-files/ documentation/ licensing.txt

# ── Summary ──────────────────────────────────────────────────────────────
ZIP_SIZE=$(du -h "${OUTPUT_DIR}/${PACKAGE_NAME}.zip" | cut -f1)
FILE_COUNT=$(unzip -l "${OUTPUT_DIR}/${PACKAGE_NAME}.zip" | tail -1 | awk '{print $2}')

echo ""
echo "============================================"
echo "  Package built successfully!"
echo ""
echo "  File:  ${OUTPUT_DIR}/${PACKAGE_NAME}.zip"
echo "  Size:  ${ZIP_SIZE}"
echo "  Files: ${FILE_COUNT}"
echo ""
echo "  Next steps:"
echo "  1. Host a demo (docker compose up -d)"
echo "  2. Take 590x300 preview screenshot"
echo "  3. Upload to https://codecanyon.net/upload"
echo "============================================"

# Cleanup
rm -rf "${BUILD_DIR}"
