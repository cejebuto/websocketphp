# Usar la imagen oficial de PHP 8 con Apache
FROM php:8.0-apache

# Instalar extensiones adicionales de PHP si es necesario
# RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar el mod_rewrite para Apache
RUN a2enmod rewrite

# Definir el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiar el código fuente al directorio de trabajo (opcional, si prefieres usar volúmenes, no necesitas esta línea)
 COPY src/ /var/www/html

# Exponer el puerto 80 para acceder al servidor Apache
EXPOSE 80