# Dockerfile

# Usamos a imagem oficial do PHP com FPM (FastCGI Process Manager)
FROM php:8.2-fpm

# Define o diretório de trabalho dentro do container
WORKDIR /var/www/html

# Instala dependências do sistema e extensões do PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Instala o Composer (gerenciador de dependências do PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia os arquivos da aplicação para o container
# (Vamos usar volumes para desenvolvimento, mas isso é bom para produção)
COPY . .

# Instala as dependências do projeto com o Composer
RUN composer install

# Expõe a porta 9000 para o PHP-FPM
EXPOSE 9000
