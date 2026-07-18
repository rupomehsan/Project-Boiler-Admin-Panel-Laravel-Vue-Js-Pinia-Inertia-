#!/bin/bash

# Config
VPS_USER="root"
VPS_IP="144.79.133.233"
VPS_DEST="/www/wwwroot/myvocab.softvanta.info"
ZIP_FILE="app.zip"
IGNORE_FILE=".deploy_tools/.zip_ignore"
SSH_KEY="$HOME/.ssh/id_ed25519" # Change if different

TRACKING_FILE=".deploy_tools/.modify_tracking/modified.json"

echo "🏗️ Running production build before deployment..."
if command -v wslpath >/dev/null 2>&1 && command -v powershell.exe >/dev/null 2>&1; then
    BUILD_DIR=$(wslpath -w "$PWD")
    powershell.exe -NoProfile -ExecutionPolicy Bypass -Command "Set-Location '$BUILD_DIR'; npm run build"
else
    npm run build
fi
echo "✅ Build finished. Continuing with tracked file upload..."

# Check file
if [ ! -f "$TRACKING_FILE" ]; then
    echo "❌ $TRACKING_FILE not found"
    exit 1
fi

# Create temp file list
TMP_LIST=$(mktemp)
jq -r '.[]' "$TRACKING_FILE" > "$TMP_LIST"

# Check file list
if [ ! -f "$TMP_LIST" ]; then
    echo "❌ $TMP_LIST not found"
    exit 1
fi

echo "📦 Uploading modified files via rsync..."
rsync -avz --files-from="$TMP_LIST" ./ -e "ssh -i $SSH_KEY" "$VPS_USER@$VPS_IP:$VPS_DEST"

rm "$TMP_LIST"
echo "✅ Done: Modified files uploaded using rsync"

# Clear the modified.json file with an empty array
echo "[]" > "$TRACKING_FILE"
echo "✅ Cleared modified.json after upload."

# === SSH into VPS, extract and run ===
echo "📡 Connecting to VPS and setting up project..."

ssh -i "$SSH_KEY" "$VPS_USER@$VPS_IP" bash <<EOF
set -e
echo "📂 Step 4: Changing directory to $VPS_DEST..."
cd "$VPS_DEST"

# Inside ssh block on VPS
echo "🚀 Running project update scripts..."
if [ -f .deploy_tools/deploy_setup_commands.sh ]; then
    chmod +x .deploy_tools/deploy_setup_commands.sh
    ./.deploy_tools/deploy_setup_commands.sh && echo "✅ deploy_setup_commands.sh executed successfully." || echo "❌ deploy_setup_commands.sh ran but returned error."
else
    echo "⚠️ .deploy_tools/deploy_setup_commands.sh not found, skipping custom setup."
fi
EOF
