# DecoWandy

Aplicacion Laravel para gestionar catalogo, ventas, compras, gastos, inventario e inversiones de DecoWandy. Incluye panel administrativo, API internas y reportes financieros.

**Última actualización de esta documentación:** 2026-05-10

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

## Acceso desde la red local (Laragon / Apache)

Si esta PC también tiene otros proyectos Laravel (p. ej. Beeffresh), conviene **no** depender solo del VirtualHost por defecto.

**Por IP en la LAN (puerto 80 para DecoWandy):**

- VirtualHost dedicado: `C:/laragon/etc/apache2/sites-enabled/decowandy-lan-ip.conf` — `VirtualHost *:80`, `ServerName` = tu IP LAN (ej. `192.168.18.19`), `DocumentRoot` → `decowandy/public`.
- En `.env`: `APP_URL=http://TU_IP_LAN` (sin puerto si usas 80).
- Otro proyecto puede usar la misma IP en **otro puerto** (ej. Beeffresh en **8080**: `http://TU_IP_LAN:8080`), sin compartir la misma URL base que DecoWandy.

**Nombre local:** sigue disponible `http://decowandy.test` si Laragon generó `auto.decowandy.test.conf` y tienes la entrada en `hosts`.

Tras editar la configuración de Apache, **reinicia Apache** en Laragon.

## Acceso interno por VPN (Tailscale + Laragon)

Para permitir acceso desde otros dispositivos sin hosting público, puedes usar Tailscale además de Laragon.

Requisitos:

- Tailscale instalado y conectado en la PC servidor y en cada cliente.
- Apache activo (Laragon).

Pasos:

1) Obtener la IP de Tailscale en la PC servidor:

```bash
tailscale ip -4
```

2) Configurar `.env` con esa IP (ajusta `APP_ENV` / `APP_DEBUG` según entorno):

```
APP_URL=http://TU_IP_TAILSCALE
```

Luego:

```bash
php artisan config:clear
```

3) Apache: el sitio debe resolver el `DocumentRoot` de DecoWandy para la URL que uses (IP Tailscale o IP LAN). Revisa `decowandy-lan-ip.conf` / `auto.decowandy.test.conf` y, si aplica, `00-default.conf` en `C:/laragon/etc/apache2/sites-enabled/`.

4) Firewall de Windows: permitir el puerto que uses (80 u otro).

Acceso de ejemplo: `http://TU_IP_TAILSCALE/login`

Nota: si al abrir la IP ves otro proyecto, revisa qué `ServerName` y puerto tiene cada `VirtualHost` (`httpd -S` en Apache) y que `APP_URL` coincida con la URL real.

## Notas
- Las seeds crean datos iniciales listos para probar flujos completos (items, ventas, compras, gastos e inversiones).
- Si cambias configuraciones en `.env`, considera `php artisan config:cache` al desplegar.
- El proyecto usa traducciones y textos en espanol; mantelos coherentes al contribuir.
