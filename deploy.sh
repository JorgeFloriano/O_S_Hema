#!/bin/bash
set -e

echo "🚀 Iniciando Deploy..."

# 1. Entrar no modo de manutenção do Laravel
php artisan down || true

# 2. Puxar as alterações mais recentes do GitHub
# (O servidor vai baixar apenas os arquivos de código alterados)
git pull origin dev-main

# 3. Instalar dependências do PHP (sem mexer nas pastas de upload)
composer install --no-dev --optimize-autoloader

# 5. Limpar e Otimizar Caches
php artisan optimize:clear

# 6. Garantir que o link do storage existe
php artisan storage:link || true

# 7. Sair do modo de manutenção
php artisan up

echo "✅ Deploy finalizado com sucesso!"