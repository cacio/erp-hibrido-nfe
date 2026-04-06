#!/bin/bash

VERSION=$1

if [ -z "$VERSION" ]; then
<<<<<<< HEAD
  echo "❌ Informe a versão. Ex: ./deploy.sh v1.1.0"
  exit 1
fi

echo "🚀 Iniciando deploy da versão $VERSION"

# Atualiza repositório
git fetch --all
git checkout $VERSION

# Dependências (se usar composer)
if [ -f composer.json ]; then
  composer install --no-dev --optimize-autoloader
fi

# Gera arquivo de versão para o PHP
VERSION_FILE="config/version.php"

echo "<?php" > $VERSION_FILE
echo "define('APP_VERSION', '$VERSION');" >> $VERSION_FILE

echo "✅ version.php gerado com versão $VERSION"

echo "🎉 Deploy concluído com sucesso"
=======
  echo "❌ Informe a versão. Ex: ./deploy.sh v1.3.0"
  exit 1
fi

echo "🚀 Deploy da versão $VERSION"

git fetch --all
git checkout $VERSION

composer install --no-dev --optimize-autoloader

echo "✅ Deploy concluído na versão $VERSION"
>>>>>>> 42446163d85696c8b224a42684a04a4ebb57d7ef
