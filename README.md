# DecoWandy

Aplicacion Laravel para gestionar catalogo, ventas, compras, gastos, inventario e inversiones de DecoWandy. Incluye panel administrativo, API internas y reportes financieros.

**Última actualización de esta documentación:** 2026-06-13

## Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/MariaDB

## Puesta en marcha (Laragon)

1) Clona el repo y entra al proyecto.

2) Copia `.env` y genera la llave:
```bash
cp .env.example .env
php artisan key:generate
```

3) Configura la base de datos en `.env`:
```env
DB_DATABASE=decowandy
DB_USERNAME=root
DB_PASSWORD=
APP_URL=http://decowandy.test
```

4) Define el administrador inicial en `.env`:
```env
ADMIN_NAME=DecoWandy
ADMIN_EMAIL=tu-correo@gmail.com
ADMIN_PASSWORD=tu_contraseña_segura
```
La contraseña debe tener al menos 8 caracteres.

5) Instala dependencias y compila assets:
```bash
composer install
npm install
npm run build
```

6) Crea la base de datos, migra y provisiona el admin:
```bash
php artisan migrate
php artisan storage:link
php artisan decowandy:ensure-admin
```

7) En Laragon: **Start All** (Apache + MySQL). Acceso local: `http://decowandy.test`

No necesitas `php artisan serve` ni `npm run dev` en uso diario si los assets ya están compilados en `public/build/`.

### VirtualHost local
Laragon debe servir `decowandy/public` con `auto.decowandy.test.conf` y la entrada `127.0.0.1 decowandy.test` en `hosts`. Tras cambios en Apache, reinicia Laragon.

## Usuarios y permisos

| Tipo | Acceso |
|------|--------|
| **Administrador** | Todo el sistema (finanzas, reportes, inversiones, usuarios, catalogo publico) |
| **Personal** | Modulos activables al crear el usuario |

Modulos para personal (`staff`):
- **Operacion** (`can_operate`): ventas, clientes y POS.
- **Inventario** (`can_inventory`): productos, stock y compras.

Se pueden activar uno, otro o ambos. Finanzas, reportes, inversiones y gestion de usuarios quedan reservados para administradores.

Gestion de usuarios: `/ajustes/usuarios` (solo admin).

Comando util si cambias credenciales del admin en `.env`:
```bash
php artisan decowandy:ensure-admin
```

## Testing
- Suite completa: `php artisan test`
- La suite usa la BD `decowandy_testing` (configurada en `phpunit.xml`). Creala antes de correr tests:
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS decowandy_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Funcionalidades clave
- Items/inventario con control de stock, minimos y alertas.
- Ventas con modal POS, validacion de stock y reportes por categoria/periodo.
- Compras y gastos con impacto en inventario y caja.
- Inversiones y reportes de finanzas (cashflow, ingresos vs gastos, utilidades).
- API internas para items, compras, ventas y catalogo publico.
- Permisos modulares por rol (admin / personal con modulos combinables).

## Interfaz (design system)

El panel admin usa un design system propio con tokens Tailwind (`dw-*`) y componentes Blade reutilizables.

**Tokens y estilos:** `tailwind.config.js`, `resources/css/app.css` (colores marca, tipografia Inter/Poppins, bordes hairline, sombras neon).

**Componentes Blade** (`resources/views/components/`):
- `dw-button`, `dw-input`, `dw-card`, `dw-kpi`, `dw-badge`, `dw-nav-link`, `dw-page-header`

**Vistas migradas al nuevo look:** layouts admin/guest, login, dashboard, ventas, clientes, inventario, compras, gastos, finanzas, reportes, inversiones, usuarios, editor de catalogo publico y pantallas auth.

**Graficos:** tema Chart.js en `resources/js/chart-theme.js`; se actualiza al cambiar el tema (`dw-theme-change`).

**Tema claro / oscuro:** selector en el header del panel y en login (Claro, Oscuro, Sistema). La preferencia se guarda en `localStorage` (`dw-theme`). Implementacion:

| Pieza | Ubicacion |
|-------|-----------|
| Variables CSS (`:root` y `[data-theme='dark']`) | `resources/css/app.css` |
| Script anti-flash en `<head>` | `resources/views/partials/dw-theme-init.blade.php` |
| Logica JS (`initTheme`, `setTheme`) | `resources/js/theme.js` |
| Componente selector | `resources/views/components/dw-theme-toggle.blade.php` |

Los colores de superficie (`bg`, `card`, `text`, `muted`, `lilac-soft`, `border`) usan variables CSS; los acentos de marca (`primary`, `rose`, `yellow`) permanecen en hex para compatibilidad con opacidades de Tailwind.

**Tablas:** clase `.dw-table` con bordes hairline, cabecera `bg-dw-lilac-soft` y hover en filas.

**Sin tema oscuro (por ahora):** catalogo publico (`/`, `welcome`) y factura de venta (`sales/show`).

Tras cambios en vistas, CSS o JS del frontend:
```bash
npm run build
```
Los assets compilados van a `public/build/` (no se versionan; cada entorno debe compilar).

## Rutas utiles
- Login: `/login`
- Panel: `/dashboard`
- Inventario: `/items`
- Ventas: `/ventas`
- Compras: `/compras`
- Gastos: `/gastos`
- Finanzas y reportes: `/finanzas`, `/reportes`
- Usuarios: `/ajustes/usuarios`
- Catalogo publico: `/`

## Acceso desde la red local (Laragon / Apache)

Si esta PC también tiene otros proyectos Laravel, conviene **no** depender solo del VirtualHost por defecto.

**Por IP en la LAN (puerto 80 para DecoWandy):**

- VirtualHost dedicado: `C:/laragon/etc/apache2/sites-enabled/decowandy-lan-ip.conf` — `VirtualHost *:80`, `ServerName` = tu IP LAN (ej. `192.168.18.19`), `DocumentRoot` → `decowandy/public`.
- En `.env`: `APP_URL=http://TU_IP_LAN` (sin puerto si usas 80).
- Otro proyecto puede usar la misma IP en **otro puerto** (ej. Beeffresh en **8080**: `http://TU_IP_LAN:8080`), sin compartir la misma URL base que DecoWandy.

**Nombre local:** `http://decowandy.test` con `auto.decowandy.test.conf` y entrada en `hosts`.

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
- El seeder de produccion no carga datos de prueba; usa datos reales del negocio.
- Si cambias configuraciones en `.env`, ejecuta `php artisan config:clear` en local o `php artisan config:cache` al desplegar.
- El proyecto usa traducciones y textos en espanol; mantelos coherentes al contribuir.
- Tras cambios en frontend (`resources/js`, `resources/css`, vistas Blade con clases `dw-*`), compila con `npm run build`.
