#!/bin/bash
set -e

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONTAINER_NAME="zenonhub-znnd-1"
DATA_PATH="/root/.znn"
BACKUP_DIR="${SCRIPT_DIR}/backups"
MAX_BACKUPS=5

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Logging functions
info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if container exists
check_container() {
    if ! docker ps -a --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
        error "Container ${CONTAINER_NAME} not found"
        exit 1
    fi
}

# Start the znnd service
start_service() {
    info "Starting znnd service..."
    docker compose up -d znnd
    info "Service started"
}

# Stop the znnd service
stop_service() {
    info "Stopping znnd service..."
    docker compose stop znnd
    info "Service stopped"
}

# Restart the znnd service
restart_service() {
    info "Restarting znnd service..."
    docker compose restart znnd
    info "Service restarted"
}

# Monitor service logs
monitor_service() {
    info "Monitoring znnd logs (Ctrl+C to exit)..."
    docker logs -f "${CONTAINER_NAME}"
}

# Get service status
status_service() {
    check_container
    info "Container status:"
    docker ps -a --filter "name=${CONTAINER_NAME}" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

    echo ""
    info "Checking RPC availability..."
    if curl -s -X POST http://localhost:35997 -H "Content-Type: application/json" \
        -d '{"jsonrpc":"2.0","id":1,"method":"ledger.getFrontierMomentum","params":[]}' > /dev/null 2>&1; then
        info "RPC is responding"
    else
        warn "RPC is not responding"
    fi

    echo ""
    info "Data directory size:"
    docker exec "${CONTAINER_NAME}" du -sh "${DATA_PATH}" 2>/dev/null || warn "Could not check data size"
}

# Backup the node data
backup_node() {
    check_container
    mkdir -p "${BACKUP_DIR}"

    local timestamp=$(date +%Y%m%d_%H%M%S)
    local backup_file="${BACKUP_DIR}/znnd_backup_${timestamp}.tar.gz"

    info "Creating backup: ${backup_file}"
    info "Stopping service for backup..."
    stop_service

    info "Creating backup archive..."
    docker run --rm \
        -v zenonhub_znnd-data:${DATA_PATH}:ro \
        -v "${BACKUP_DIR}:/backup" \
        alpine:latest \
        sh -c "cd ${DATA_PATH} && tar czf /backup/znnd_backup_${timestamp}.tar.gz consensus network nom \$([ -d cache ] && echo cache || true)"

    if [ -f "${backup_file}" ]; then
        info "Backup created successfully: ${backup_file}"
        info "Generating checksum..."
        sha256sum "${backup_file}" > "${backup_file}.sha256"
        info "Checksum saved: ${backup_file}.sha256"

        # Prune old backups
        prune_backups
    else
        error "Backup failed"
        start_service
        exit 1
    fi

    start_service
    info "Backup complete"
}

# Restore from backup
restore_node() {
    check_container

    if [ ! -d "${BACKUP_DIR}" ] || [ -z "$(ls -A ${BACKUP_DIR}/*.tar.gz 2>/dev/null)" ]; then
        error "No backups found in ${BACKUP_DIR}"
        exit 1
    fi

    echo "Available backups:"
    local backups=($(ls -t ${BACKUP_DIR}/*.tar.gz 2>/dev/null))
    local i=1
    for backup in "${backups[@]}"; do
        local size=$(du -h "$backup" | cut -f1)
        echo "  $i) $(basename $backup) (${size})"
        ((i++))
    done

    read -p "Select backup number to restore (1-${#backups[@]}): " selection

    if ! [[ "$selection" =~ ^[0-9]+$ ]] || [ "$selection" -lt 1 ] || [ "$selection" -gt "${#backups[@]}" ]; then
        error "Invalid selection"
        exit 1
    fi

    local backup_file="${backups[$((selection-1))]}"
    info "Selected backup: $(basename ${backup_file})"

    # Verify checksum if it exists
    if [ -f "${backup_file}.sha256" ]; then
        info "Verifying backup integrity..."
        if sha256sum -c "${backup_file}.sha256" > /dev/null 2>&1; then
            info "Backup integrity verified"
        else
            error "Backup integrity check failed!"
            exit 1
        fi
    else
        warn "No checksum file found, skipping verification"
    fi

    warn "This will replace all current node data!"
    read -p "Are you sure you want to continue? (yes/no): " confirm
    if [ "$confirm" != "yes" ]; then
        info "Restore cancelled"
        exit 0
    fi

    info "Stopping service..."
    stop_service

    info "Backing up current data..."
    backup_current="${BACKUP_DIR}/znnd_pre_restore_$(date +%Y%m%d_%H%M%S).tar.gz"
    docker run --rm \
        -v zenonhub_znnd-data:${DATA_PATH}:ro \
        -v "${BACKUP_DIR}:/backup" \
        alpine:latest \
        sh -c "cd ${DATA_PATH} && tar czf /backup/$(basename ${backup_current}) consensus network nom \$([ -d cache ] && echo cache || true)" 2>/dev/null || warn "Could not backup current data"

    info "Removing existing data directories..."
    docker run --rm \
        -v zenonhub_znnd-data:${DATA_PATH} \
        alpine:latest \
        sh -c "rm -rf ${DATA_PATH}/consensus ${DATA_PATH}/network ${DATA_PATH}/nom && [ -d ${DATA_PATH}/cache ] && rm -rf ${DATA_PATH}/cache || true"

    info "Restoring from backup..."
    docker run --rm \
        -v zenonhub_znnd-data:${DATA_PATH} \
        -v "${backup_file}:/backup.tar.gz:ro" \
        alpine:latest \
        sh -c "cd ${DATA_PATH} && tar xzf /backup.tar.gz"

    if [ $? -eq 0 ]; then
        info "Restore completed successfully"
    else
        error "Restore failed"
        exit 1
    fi

    start_service
    info "Node restored and restarted"
}

# Prune old backups
prune_backups() {
    local backup_count=$(ls -1 ${BACKUP_DIR}/znnd_backup_*.tar.gz 2>/dev/null | wc -l)
    if [ "$backup_count" -gt "$MAX_BACKUPS" ]; then
        info "Pruning old backups (keeping last ${MAX_BACKUPS})..."
        ls -t ${BACKUP_DIR}/znnd_backup_*.tar.gz | tail -n +$((MAX_BACKUPS + 1)) | while read -r old_backup; do
            info "Removing: $(basename ${old_backup})"
            rm -f "${old_backup}" "${old_backup}.sha256"
        done
    fi
}

# Resync node (clear data and restart)
resync_node() {
    check_container

    warn "This will delete all node data and resync from scratch!"
    read -p "Are you sure you want to continue? (yes/no): " confirm
    if [ "$confirm" != "yes" ]; then
        info "Resync cancelled"
        exit 0
    fi

    info "Creating backup before resync..."
    backup_node

    info "Stopping service..."
    stop_service

    info "Removing node data..."
    docker volume rm zenonhub_znnd-data || error "Failed to remove volume"
    docker volume create zenonhub_znnd-data || error "Failed to create volume"

    info "Starting service to begin resync..."
    start_service
    info "Node is now resyncing from genesis"
}

# Show help
show_help() {
    cat << EOF
Zenon Node Management Script for Docker

Usage: $0 [COMMAND]

Commands:
    start       Start the znnd service
    stop        Stop the znnd service
    restart     Restart the znnd service
    status      Show service status and information
    monitor     Monitor service logs (follow mode)
    backup      Create a backup of node data
    restore     Restore from a backup
    resync      Clear data and resync from scratch
    help        Show this help message

Examples:
    $0 start
    $0 backup
    $0 restore

EOF
}

# Main script
case "${1:-}" in
    start)
        start_service
        ;;
    stop)
        stop_service
        ;;
    restart)
        restart_service
        ;;
    status)
        status_service
        ;;
    monitor)
        monitor_service
        ;;
    backup)
        backup_node
        ;;
    restore)
        restore_node
        ;;
    resync)
        resync_node
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        error "Unknown command: ${1:-}"
        echo ""
        show_help
        exit 1
        ;;
esac
