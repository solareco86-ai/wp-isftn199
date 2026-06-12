#!/bin/bash

##########################################################
# Validar variables de entorno para despliegue
# Uso: ./scripts/validate-env.sh
##########################################################

set -e

echo "🔐 Validando credenciales de despliegue..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Verificar cada variable
ERRORS=0

if [ -n "$DEPLOY_USER" ]; then
    echo "✓ DEPLOY_USER: $DEPLOY_USER"
else
    echo "✗ DEPLOY_USER: (vacío)"
    ERRORS=$((ERRORS + 1))
fi

if [ -n "$DEPLOY_HOST" ]; then
    echo "✓ DEPLOY_HOST: $DEPLOY_HOST"
else
    echo "✗ DEPLOY_HOST: (vacío)"
    ERRORS=$((ERRORS + 1))
fi

if [ -n "$DEPLOY_PORT" ]; then
    echo "✓ DEPLOY_PORT: $DEPLOY_PORT"
    
    # Advertencia si es puerto FTP
    if [ "$DEPLOY_PORT" = "21" ]; then
        echo "⚠️  ADVERTENCIA: Puerto 21 (FTP) detectado. SSH normalmente usa puerto 22"
    fi
else
    echo "✗ DEPLOY_PORT: (vacío)"
    ERRORS=$((ERRORS + 1))
fi

if [ -n "$DEPLOY_PASSWORD" ]; then
    echo "✓ DEPLOY_PASSWORD: (configurado)"
else
    echo "✗ DEPLOY_PASSWORD: (vacío)"
    ERRORS=$((ERRORS + 1))
fi

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ $ERRORS -gt 0 ]; then
    echo "❌ ERROR: $ERRORS variable(s) faltante(s)"
    exit 1
fi

echo "✅ Todas las variables configuradas correctamente"
exit 0
