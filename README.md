# DecoWandy

Aplicacion Laravel para gestionar catalogo, ventas, compras, gastos, inventario e inversiones de DecoWandy. Incluye panel administrativo, API internas y reportes financieros.

## Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/MariaDB (o SQLite para pruebas)

## Puesta en marcha
1) Clona el repo y entra al proyecto.  
2) Copia env y genera la llave:
```bash
cp .env.example .env
php artisan key:generate
```
3) Configura la base de datos en `.env` (credenciales de MySQL).  
4) Instala dependencias:
```bash
composer install
npm install
```
5) Levanta tablas y datos base:
```bash
php artisan migrate:fresh --seed
```
6) Arranca frontend y backend de desarrollo:
```bash
npm run dev
php artisan serve
```

## Testing
- Suite completa: `php artisan test`
- Si necesitas un ambiente aislado, crea `.env.testing` apuntando a SQLite o una BD de pruebas y ejecuta `php artisan test` (las migraciones de testing corren automaticamente).

## Funcionalidades clave
- Items/inventario con control de stock, minimos y alertas.
- Ventas con modal POS, validacion de stock y reportes por categoria/periodo.
- Compras y gastos con impacto en inventario y caja.
- Inversiones y reportes de finanzas (cashflow, ingresos vs gastos, utilidades).
- API internas para items, compras, ventas y catalogo publico.

## Rutas utiles
- Panel: `/dashboard`
- Inventario: `/items`
- Ventas: `/ventas`
- Compras: `/compras`
- Gastos: `/gastos`
- Finanzas y reportes: `/finanzas`, `/reportes`
- Catalogo publico: `/`

## Acceso interno por VPN (Tailscale + Laragon)
Para permitir acceso desde otros dispositivos sin hosting, se puede usar VPN con Tailscale y Laragon/Apache en la misma PC.

Requisitos:
- Tailscale instalado y conectado en la PC servidor y en cada dispositivo cliente.
- Apache activo (Laragon).

Pasos:
1) Obtener la IP de Tailscale en la PC servidor:
```bash
tailscale ip -4
```
2) Configurar `.env` con esa IP:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=http://TU_IP_TAILSCALE
```
Luego:
```bash
php artisan config:clear
```
3) Apache: el VirtualHost por defecto para acceso por IP debe apuntar al `public` de este proyecto.
   Archivo típico en Laragon: `C:/laragon/etc/apache2/sites-enabled/00-default.conf`
   Asegurar:
```
DocumentRoot "C:/laragon/www/decowandy/public"
<Directory "C:/laragon/www/decowandy/public">
    AllowOverride All
    Require all granted
</Directory>
```
4) Firewall de Windows: permitir puerto 80 para la red de Tailscale.

Acceso:
- Desde otro dispositivo con Tailscale: `http://TU_IP_TAILSCALE`
- Login: `http://TU_IP_TAILSCALE/login`

Nota:
- Si el acceso por IP muestra otro proyecto, revisa el `DocumentRoot` y el bloque `<Directory>` del VirtualHost por defecto.

## Notas
- Las seeds crean datos iniciales listos para probar flujos completos (items, ventas, compras, gastos e inversiones).
- Si cambias configuraciones en `.env`, considera `php artisan config:cache` al desplegar.
- El proyecto usa traducciones y textos en espanol; mantelos coherentes al contribuir.
