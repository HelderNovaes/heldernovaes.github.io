FROM php:8.1-apache

# Habilita mod_rewrite
RUN a2enmod rewrite

# Copia todos os arquivos para o diretório público
COPY ./public/ /var/www/html/

# Exponha a porta padrão do Apache
EXPOSE 80
