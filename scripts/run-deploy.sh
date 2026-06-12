#!/bin/bash

##########################################################
# Ejecutar deploy.sh en el servidor remoto
# Uso: ./scripts/run-deploy.sh <user> <host> <port> <password> <deploy_dir>
##########################################################

set -e

DEPLOY_USER="${1:?Falta: DEPLOY_USER}"
DEPLOY_HOST="${2:?Falta: DEPLOY_HOST}"
DEPLOY_PORT="${3:?Falta: DEPLOY_PORT}"
DEPLOY_PASSWORD="${4:?Falta: DEPLOY_PASSWORD}"
DEPLOY_DIR="${5:/var/www/html/instituto}"

echo "⚙️  Ejecutando script de despliegue en servidor remoto..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "   Usuario: $DEPLOY_USER"
echo "   Host: $DEPLOY_HOST:$DEPLOY_PORT"
echo "   Directorio: $DEPLOY_DIR"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Crear script expect para ejecutar deploy.sh
cat > /tmp/run_deploy.expect << 'EXPECT_EOF'
#!/usr/bin/expect -f
set timeout 300
set user [lindex $argv 0]
set host [lindex $argv 1]
set port [lindex $argv 2]
set pass [lindex $argv 3]
set deploy_dir [lindex $argv 4]

puts "🚀 Ejecutando deploy remoto..."
puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Comando remoto: verificar deploy.sh, mostrarlo, y ejecutarlo
set remote_cmd "cd $deploy_dir && \
  if \[ -f deploy.sh \]; then \
    echo '✓ Script deploy.sh encontrado'; \
    echo '📋 Contenido:'; \
    head -20 deploy.sh; \
    echo '...'; \
    echo ''; \
    echo '🔄 Ejecutando...'; \
    bash -x deploy.sh 2>&1; \
  else \
    echo '❌ deploy.sh NO encontrado en $deploy_dir'; \
    echo '📂 Contenido del directorio:'; \
    ls -lah; \
    exit 1; \
  fi"

spawn ssh -v -o StrictHostKeyChecking=no -p $port $user@$host $remote_cmd

expect {
    "password:" {
        puts "✓ Autenticando..."
        send "$pass\r"
        exp_continue
    }
    "deploy.sh NO encontrado" {
        puts "❌ ERROR: Script deploy.sh no existe"
        puts "   - Verifica que el archivo fue transferido correctamente"
        exit 1
    }
    "Permission denied" {
        puts "❌ ERROR: Contraseña incorrecta o permiso denegado"
        exit 1
    }
    "Connection refused" {
        puts "❌ ERROR: Conexión rechazada"
        exit 1
    }
    timeout {
        puts "❌ ERROR: Timeout en ejecución (>300s)"
        exit 1
    }
    eof {
        puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        puts "✅ Script de despliegue ejecutado"
        puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    }
}
EXPECT_EOF

chmod +x /tmp/run_deploy.expect

echo ""
echo "🔄 Ejecutando deploy.sh..."
echo ""

# Ejecutar el script expect
/tmp/run_deploy.expect "$DEPLOY_USER" "$DEPLOY_HOST" "$DEPLOY_PORT" "$DEPLOY_PASSWORD" "$DEPLOY_DIR"

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Despliegue completado"
    exit 0
else
    echo ""
    echo "❌ Falló la ejecución del despliegue"
    exit 1
fi
