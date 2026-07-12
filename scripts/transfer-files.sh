#!/bin/bash

##########################################################
# Transferir archivos al servidor remoto vía FTP
# Uso: ./scripts/transfer-files.sh <user> <host> <port> <password> <source_dir> <dest_dir>
##########################################################

set -e

DEPLOY_USER="${1:?Falta: DEPLOY_USER}"
DEPLOY_HOST="${2:?Falta: DEPLOY_HOST}"
DEPLOY_PORT="${3:?Falta: DEPLOY_PORT}"
DEPLOY_PASSWORD="${4:?Falta: DEPLOY_PASSWORD}"
SOURCE_DIR="${5:-.}"
DEST_DIR="${6:/var/www/html/instituto}"

echo "📤 Iniciando transferencia de archivos vía FTP..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "   Origen: $SOURCE_DIR"
echo "   Destino: ftp://$DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PORT$DEST_DIR"
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

# Crear script FTP usando lftp
cat > /tmp/ftp_deploy.sh << 'LFTP_EOF'
#!/bin/bash

DEPLOY_USER="$1"
DEPLOY_HOST="$2"
DEPLOY_PORT="$3"
DEPLOY_PASSWORD="$4"
SOURCE_DIR="$5"
DEST_DIR="$6"

echo "🚀 Transferencia FTP iniciada"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Usar lftp para transferencia FTP
lftp -e "
  set ftp:ssl-allow no
  set net:timeout 30
  set net:max-retries 3
  set xfer:clobber on
  set cmd:interactive no
  
  echo 'Conectando a $DEPLOY_HOST:$DEPLOY_PORT...'
  connect -u $DEPLOY_USER,$DEPLOY_PASSWORD $DEPLOY_HOST:$DEPLOY_PORT
  
  echo 'Cambiando a directorio remoto: $DEST_DIR'
  cd $DEST_DIR
  
  echo 'Iniciando mirror (subida recursiva)...'
  mirror -R --continue --parallel=4 $SOURCE_DIR .
  
  echo 'Cerrando conexión...'
  quit
" 2>&1 | tee /tmp/ftp_transfer.log

# Verificar si la transferencia fue exitosa
if [ ${PIPESTATUS[0]} -eq 0 ]; then
    echo ""
    echo "✅ Transferencia FTP completada exitosamente"
    exit 0
else
    echo ""
    echo "❌ ERROR en transferencia FTP"
    echo "📋 Revisar logs:"
    cat /tmp/ftp_transfer.log
    exit 1
fi
LFTP_EOF

chmod +x /tmp/ftp_deploy.sh

echo ""
echo "🔄 Ejecutando transferencia FTP..."
echo ""

# Ejecutar el script FTP
/tmp/ftp_deploy.sh "$DEPLOY_USER" "$DEPLOY_HOST" "$DEPLOY_PORT" "$DEPLOY_PASSWORD" "$SOURCE_DIR" "$DEST_DIR"

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Transferencia completada"
    exit 0
else
    echo ""
    echo "❌ Falló la transferencia de archivos"
    exit 1
fi
