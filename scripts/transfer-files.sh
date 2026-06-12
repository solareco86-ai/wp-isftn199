#!/bin/bash

##########################################################
# Transferir archivos al servidor remoto vía SCP
# Uso: ./scripts/transfer-files.sh <user> <host> <port> <password> <source_dir> <dest_dir>
##########################################################

set -e

DEPLOY_USER="${1:?Falta: DEPLOY_USER}"
DEPLOY_HOST="${2:?Falta: DEPLOY_HOST}"
DEPLOY_PORT="${3:?Falta: DEPLOY_PORT}"
DEPLOY_PASSWORD="${4:?Falta: DEPLOY_PASSWORD}"
SOURCE_DIR="${5:-.}"
DEST_DIR="${6:/var/www/html/instituto}"

echo "📤 Iniciando transferencia de archivos..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "   Origen: $SOURCE_DIR"
echo "   Destino: $DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PORT:$DEST_DIR"
echo ""

# Mostrar estadísticas locales
echo "📊 Estadísticas locales:"
if [ -d "$SOURCE_DIR" ]; then
    TOTAL_FILES=$(find "$SOURCE_DIR" -type f 2>/dev/null | wc -l)
    TOTAL_SIZE=$(du -sh "$SOURCE_DIR" 2>/dev/null | cut -f1)
    echo "   Total de archivos: $TOTAL_FILES"
    echo "   Tamaño total: $TOTAL_SIZE"
    echo ""
    echo "📋 Primeros 10 archivos:"
    ls -lah "$SOURCE_DIR" | head -11
else
    echo "❌ ERROR: Directorio de origen no encontrado: $SOURCE_DIR"
    exit 1
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Crear script expect para SCP
cat > /tmp/scp_deploy.expect << 'EXPECT_EOF'
#!/usr/bin/expect -f
set timeout 600
set user [lindex $argv 0]
set host [lindex $argv 1]
set port [lindex $argv 2]
set pass [lindex $argv 3]
set source [lindex $argv 4]
set dest [lindex $argv 5]

puts "🚀 Transferencia SCP iniciada"
puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

spawn scp -v -o StrictHostKeyChecking=no -o ConnectTimeout=15 -P $port -r $source $user@$host:$dest

set transfer_started 0

expect {
    "password:" {
        puts "✓ Autenticando con contraseña..."
        send "$pass\r"
        exp_continue
    }
    "Sending file" {
        if { !$transfer_started } {
            set transfer_started 1
            puts "✓ Iniciando envío de archivos..."
        }
        exp_continue
    }
    "100%" {
        exp_continue
    }
    "Connection refused" {
        puts "❌ ERROR: Conexión rechazada"
        puts "   - Verifica host y puerto"
        exit 1
    }
    "No such file or directory" {
        puts "❌ ERROR: Ruta no válida en destino"
        exit 1
    }
    "Permission denied" {
        puts "❌ ERROR: Permiso denegado o contraseña incorrecta"
        exit 1
    }
    "Connection timed out" {
        puts "❌ ERROR: Timeout - conexión perdida"
        exit 1
    }
    timeout {
        puts "❌ ERROR: Timeout en transferencia (>600s)"
        exit 1
    }
    eof {
        if { $transfer_started } {
            puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
            puts "✅ Transferencia completada exitosamente"
            puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        } else {
            puts "⚠️  Transferencia completada (sin cambios detectados?)"
        }
    }
}
EXPECT_EOF

chmod +x /tmp/scp_deploy.expect

echo ""
echo "🔄 Ejecutando transferencia..."
echo ""

# Ejecutar el script expect
/tmp/scp_deploy.expect "$DEPLOY_USER" "$DEPLOY_HOST" "$DEPLOY_PORT" "$DEPLOY_PASSWORD" "$SOURCE_DIR" "$DEST_DIR"

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Transferencia completada"
    exit 0
else
    echo ""
    echo "❌ Falló la transferencia de archivos"
    exit 1
fi
