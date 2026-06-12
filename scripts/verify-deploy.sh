#!/bin/bash

##########################################################
# Verificar que el despliegue fue exitoso
# Uso: ./scripts/verify-deploy.sh <user> <host> <port> <password> <deploy_dir>
##########################################################

set -e

DEPLOY_USER="${1:?Falta: DEPLOY_USER}"
DEPLOY_HOST="${2:?Falta: DEPLOY_HOST}"
DEPLOY_PORT="${3:?Falta: DEPLOY_PORT}"
DEPLOY_PASSWORD="${4:?Falta: DEPLOY_PASSWORD}"
DEPLOY_DIR="${5:/var/www/html/instituto}"

echo "🔍 Verificando despliegue en servidor remoto..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "   Host: $DEPLOY_HOST:$DEPLOY_PORT"
echo "   Directorio: $DEPLOY_DIR"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Crear script expect para verificación
cat > /tmp/verify.expect << 'EXPECT_EOF'
#!/usr/bin/expect -f
set timeout 30
set user [lindex $argv 0]
set host [lindex $argv 1]
set port [lindex $argv 2]
set pass [lindex $argv 3]
set deploy_dir [lindex $argv 4]

puts ""
puts "📊 Información del despliegue:"
puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Comando remoto: listar archivos y tamaño
set remote_cmd "if \[ -d $deploy_dir \]; then \
  echo '📂 Contenido (últimos 30 archivos):'; \
  ls -lah $deploy_dir | tail -30; \
  echo ''; \
  echo '📈 Tamaño total:'; \
  du -sh $deploy_dir; \
  echo ''; \
  echo '📋 Permisos:'; \
  ls -ld $deploy_dir; \
else \
  echo '❌ Directorio no encontrado: $deploy_dir'; \
  exit 1; \
fi"

spawn ssh -o StrictHostKeyChecking=no -p $port $user@$host $remote_cmd

expect {
    "password:" {
        send "$pass\r"
        exp_continue
    }
    "Directorio no encontrado" {
        puts "❌ ERROR: Directorio de despliegue no existe"
        exit 1
    }
    timeout {
        puts "❌ ERROR: Timeout"
        exit 1
    }
    eof {
        puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        puts "✅ Verificación completada"
    }
}
EXPECT_EOF

chmod +x /tmp/verify.expect

echo ""

# Ejecutar el script expect
/tmp/verify.expect "$DEPLOY_USER" "$DEPLOY_HOST" "$DEPLOY_PORT" "$DEPLOY_PASSWORD" "$DEPLOY_DIR"

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Despliegue verificado exitosamente"
    exit 0
else
    echo ""
    echo "❌ Falló la verificación"
    exit 1
fi
