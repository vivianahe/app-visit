# APP VISIT

## Descripción

Este proyecto corresponde a la **Prueba Técnica** para el puesto de **Desarrollador FullStack**. El sistema permite ingresar las visitas, incluyendo los datos del cliente, de longitud y latitud, y se plasman en el mapa.

## Requisitos

1. Clona el repositorio: **git clone https://github.com/vivianahe/app-visit.git** o Descarga el .zip

2. Instala composer al proyecto: **composer install** 

3. Instala las dependencias del proyecto: **npm install**


## Configuración del entorno 

Copia el archivo .env.example a un nuevo archivo llamado .env:

**cp .env.example .env**

DB_DATABASE=app-visit

DB_USERNAME=tu_usuario

DB_PASSWORD=tu_constraseña


## Generar la clave de la aplicación
Ejecuta el siguiente comando para generar la clave de la aplicación:
**php artisan key:generate**

## Ejecutar migraciones y seeder
Ejecuta los siguientes comandos para crear las tablas en la base de datos y ejecutar los seeder:

**php artisan migrate**

**php artisan db:seed**

Ejecuta el command para crear una visita o lo puedes hacer directamente en el sistema:

**php artisan visit:create**

## Compilar los assets
Compila los archivos de Vue.js y los archivos CSS con el siguiente comando:
**npm run dev**

## Ejecución de la Aplicación

Finalmente, ejecuta el servidor de desarrollo de Laravel:
**php artisan serve**

Accede al sistema a través de http://127.0.0.1:8000

## Acceso a la Aplicación
Para ingresar al sistema, utiliza las siguientes credenciales:

Correo: **admin@gmail.com**
Contraseña: **Admin-1234**
