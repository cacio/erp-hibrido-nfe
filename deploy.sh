#!/bin/bash

VERSION=$1

if [ -z "$VERSION" ]; then
  echo "❌ Informe a versão. Ex: ./deploy.sh v1.3.0"
  exit 1
fi

echo "🚀 Deploy da versão $VERSION"

git fetch --all
git checkout $VERSION

composer install --no-dev --optimize-autoloader

echo "✅ Deploy concluído na versão $VERSION"
