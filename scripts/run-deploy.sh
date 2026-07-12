#!/bin/bash

##########################################################
# Verificar y ejecutar deploy.sh en el servidor remoto (FTP)
# Nota: Con FTP no se pueden ejecutar comandos remotamente
# El deploy.sh debe ejecutarse mediante cron, webhook, o manualmente en el servidor
# Uso: ./scripts/run-deploy.sh <user> <host> <port> <password> <deploy_dir>
##########################################################

set -e

DEPLOY_USER="${1:?Falta: DEPLOY_USER}"
DEPLOY_HOST="${2:?Falta: DEPLOY_HOST}"
DEPLOY_PORT="${3:?Falta: DEPLOY_PORT}"
DEPLOY_PASSWORD="${4:?Falta: DEPLOY_PASSWORD}"
DEPLOY_DIR="${5:-/var/www/html/instituto}"

echo "⚙️  Verificando script de despliegue en servidor remoto (FTP)..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "   Usuario: $DEPLOY_USER"
echo "   Host: $DEPLOY_HOST:$DEPLOY_PORT (FTP)"
echo "   Directorio: $DEPLOY_DIR"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Verificar que deploy.sh existe vía FTP
echo "🔍 Buscando deploy.sh en el servidor remoto..."
echo ""

DEPLOY_EXISTS=$(lftp -e "
  set ftp:ssl-allow no
  set net:timeout 30
  connect -u $DEPLOY_USER,$DEPLOY_PASSWORD $DEPLOY_HOST:$DEPLOY_PORT
  cd $DEPLOY_DIR
  if test -f deploy.sh; then
    echo 'YES'
  else
    echo 'NO'
  fi
  quit
" 2>/dev/null | tail -1)

if [ "$DEPLOY_EXISTS" = "YES" ]; then
    echo "✓ Script deploy.sh encontrado en el servidor"
    echo ""
    echo "📋 Información del archivo:"
    
    lftp -e "
      set ftp:ssl-allow no
      set net:timeout 30
      connect -u $DEPLOY_USER,$DEPLOY_PASSWORD $DEPLOY_HOST:$DEPLOY_PORT
      cd $DEPLOY_DIR
      ls -lh deploy.sh
      quit
    " 2>/dev/null || true
    
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "⚠️  IMPORTANTE - PRÓXIMOS PASOS:"
    echo ""
    echo "1️⃣  El script 'deploy.sh' está en el servidor pero NO SE EJECUTÓ"
    echo "    (FTP no permite ejecutar comandos remotamente)"
    echo ""
    echo "2️⃣  Debes ejecutar deploy.sh usando UNA de estas opciones:"
    echo ""
    echo "   📌 OPCIÓN A - Cron Job (automático):"
    echo "      Accede al panel de control (cPanel/Plesk)"
    echo "      Añade una tarea cron que ejecute:"
    echo "      bash /var/www/html/instituto/deploy.sh"
    echo ""
    echo "   📌 OPCIÓN B - Webhook (GitHub Actions trigger):"
    echo "      Configura un webhook que ejecute deploy.sh"
    echo "      Cuando se complete la transferencia FTP"
    echo ""
    echo "   📌 OPCIÓN C - Manual:"
    echo "      Conecta vía SSH o panel de control"
    echo "      Ejecuta: bash /var/www/html/instituto/deploy.sh"
    echo ""
    echo "3️⃣  Estado de transferencia:"
    echo "    ✓ Archivos transferidos vía FTP"
    echo "    ✓ Script deploy.sh presente"
    echo "    ⏳ Esperando ejecución manual del deploy"
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    exit 0
else
    echo "❌ ERROR: Script deploy.sh NO encontrado"
    echo ""
    echo "📂 Contenido del directorio remoto:"
    echo ""
    
    lftp -e "
      set ftp:ssl-allow no
      set net:timeout 30
      connect -u $DEPLOY_USER,$DEPLOY_PASSWORD $DEPLOY_HOST:$DEPLOY_PORT
      cd $DEPLOY_DIR
      ls -lah | head -20
      quit
    " 2>/dev/null || true
    
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "🔧 SOLUCIÓN:"
    echo ""
    echo "1. Verifica que deploy.sh fue transferido correctamente"
    echo "   - Busca el archivo en el directorio: $DEPLOY_DIR"
    echo ""
    echo "2. Revisa los logs de transferencia anteriores"
    echo ""
    echo "3. Alternativas:"
    echo "   - Crea deploy.sh en el servidor manualmente"
    echo "   - O incluye el script en tu repositorio"
    echo ""
    exit 1
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
