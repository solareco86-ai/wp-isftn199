#!/bin/bash

##########################################################
# Probar conectividad SSH al servidor remoto
# Uso: ./scripts/ssh-test.sh <user> <host> <port> <password>
##########################################################

set -e

DEPLOY_USER="${1:?Falta: DEPLOY_USER}"
DEPLOY_HOST="${2:?Falta: DEPLOY_HOST}"
DEPLOY_PORT="${3:?Falta: DEPLOY_PORT}"
DEPLOY_PASSWORD="${4:?Falta: DEPLOY_PASSWORD}"

echo "🔗 Probando conectividad SSH..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "   Usuario: $DEPLOY_USER"
echo "   Host: $DEPLOY_HOST"
echo "   Puerto: $DEPLOY_PORT"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Crear script expect para SSH
cat > /tmp/ssh_test.expect << 'EXPECT_EOF'
#!/usr/bin/expect -f
set timeout 15
set user [lindex $argv 0]
set host [lindex $argv 1]
set port [lindex $argv 2]
set pass [lindex $argv 3]

puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
puts "🔐 Iniciando conexión SSH..."
puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

spawn ssh -v -o StrictHostKeyChecking=no -o ConnectTimeout=10 -p $port $user@$host "echo '✓ Conexión exitosa'; pwd; whoami"

expect {
    "password:" {
        puts "✓ Servidor solicita contraseña (esperado)"
        send "$pass\r"
        exp_continue
    }
    "Permission denied" {
        puts "❌ ERROR: Contraseña incorrecta o usuario no válido"
        exit 1
    }
    "Connection refused" {
        puts "❌ ERROR: Conexión rechazada"
        puts "   - Verifica que el host y puerto sean correctos"
        puts "   - Puerto SSH es usualmente 22, no 21"
        exit 1
    }
    "Name or service not known" {
        puts "❌ ERROR: Host no encontrado"
        puts "   - Verifica que el host sea correcto"
        exit 1
    }
    "Connection timed out" {
        puts "❌ ERROR: Timeout en la conexión"
        exit 1
    }
    timeout {
        puts "❌ ERROR: Timeout esperando respuesta"
        exit 1
    }
    eof {
        puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        puts "✅ Conexión SSH exitosa"
        puts "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    }
}
EXPECT_EOF

chmod +x /tmp/ssh_test.expect

# Ejecutar el script expect
/tmp/ssh_test.expect "$DEPLOY_USER" "$DEPLOY_HOST" "$DEPLOY_PORT" "$DEPLOY_PASSWORD"

if [ $? -eq 0 ]; then
    echo "✅ Prueba de conectividad completada exitosamente"
    exit 0
else
    echo "❌ Falló la prueba de conectividad"
    exit 1
fi
