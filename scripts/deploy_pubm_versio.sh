#!/usr/bin/env bash
set -euo pipefail

ROOT="${1:-$(cd "$(dirname "$0")/.." && pwd)}"
REMOTE="${PUBM_VERSIO_SSH:-nol@vserver423.axc.eu}"
DOMAIN="pubm.oerse-kippies.nl"
REPO_DIR="~/domains/${DOMAIN}/publicationManagement"

echo "Deploy pubM to ${REMOTE}:${REPO_DIR}"

ssh "${REMOTE}" "mkdir -p ~/domains/${DOMAIN}"
if ssh "${REMOTE}" "test -d ${REPO_DIR}/.git"; then
  ssh "${REMOTE}" "cd ${REPO_DIR} && git fetch origin && git reset --hard origin/main"
else
  ssh "${REMOTE}" "git clone git@github.com:OerseKippies/publicationManagement.git ${REPO_DIR}"
fi

COMMIT="$(cd "${ROOT}" && git rev-parse --short HEAD 2>/dev/null || echo unknown)"
ssh "${REMOTE}" "echo '${COMMIT}' > ${REPO_DIR}/DEPLOY_COMMIT.txt"
ssh "${REMOTE}" "rm -f ~/domains/${DOMAIN}/public_html && ln -sfn ${REPO_DIR}/public/api ~/domains/${DOMAIN}/public_html"

if ssh "${REMOTE}" "test -f ${REPO_DIR}/config/config.php"; then
  ssh "${REMOTE}" "cd ${REPO_DIR} && php scripts/migrate.php && php scripts/seed.php" || echo "WARN: migrate/seed check"
else
  echo "NOTE: config/config.php missing on server"
fi

echo "Deploy complete: https://${DOMAIN}/health"
