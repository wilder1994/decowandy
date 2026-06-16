# DecoWandy

Aplicación Laravel para gestionar catálogo, ventas, compras, gastos, inventario e inversiones de DecoWandy. Incluye panel administrativo, API internas, códigos de barras, etiquetas y reportes financieros.

**Última actualización de esta documentación:** 2025-06-13

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
SANCTUM_STATEFUL_DOMAINS=decowandy.test,localhost,127.0.0.1
SESSION_SECURE_COOKIE=
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

## Módulos del panel

| Módulo | Ruta | Rol |
|--------|------|-----|
| **Compras y catálogo** | `/compras` | Altas por sector, historial de compras, pestaña Catálogo |
| **Inventario** | `/inventario` | Stock, alertas, ajustes y reordenar |
| **Ventas (POS)** | `/ventas` | Venta mixta: impresión, diseño y papelería en un ticket |

- `/items` redirige a `/inventario` (compatibilidad).
- **Papelería** se da de alta desde **Compras** (compra con código de barras); no desde inventario ni alta directa en API.
- **Impresión / Diseño** se registran en Compras → Agregar, o desde el catálogo en la pestaña Catálogo.

## Códigos de barras y etiquetas

- Códigos internos formato `DWY-XXXXXX` (`ItemBarcodeService`).
- Escáner pro en navegador (`resources/js/barcode-scanner.js`, `html5-qrcode`):
  - Pantalla casi fullscreen en móvil, marco de escaneo y línea animada.
  - Soporte QR + códigos 1D (EAN, UPC, Code 128, Code 39, ITF, etc.).
  - Linterna (si el dispositivo la soporta), reintentar y errores visibles en el propio escáner.
  - Toast global (`dw-toast.js`) para feedback inmediato.
  - Requiere **HTTPS** en celular (`getUserMedia`).
- Búsqueda por código en API y POS; compras de papelería crean o actualizan ítems (`PurchasePapeleriaService`).
- Etiquetas PNG/PDF por ítem o lote (`ItemLabelService`).
- Alta rápida papelería: `POST /api/items/papeleria/quick`.
- Dependencias: `picqer/php-barcode-generator`, `chillerlan/php-qrcode`, `html5-qrcode` (npm).

Tras clonar o actualizar, ejecuta migraciones:
```bash
php artisan migrate
```

## Usuarios y permisos

| Tipo | Acceso |
|------|--------|
| **Administrador** | Todo el sistema (finanzas, reportes, inversiones, usuarios, catálogo público) |
| **Personal** | Módulos activables al crear el usuario |

Módulos para personal (`staff`):
- **Operación** (`can_operate`): ventas, clientes y POS.
- **Inventario** (`can_inventory`): inventario y compras.

Se pueden activar uno, otro o ambos. Finanzas, reportes, inversiones y gestión de usuarios quedan reservados para administradores.

Gestión de usuarios: `/ajustes/usuarios` (solo admin).

Comando útil si cambias credenciales del admin en `.env`:
```bash
php artisan decowandy:ensure-admin
```

## Testing
- Suite completa: `php artisan test`
- La suite usa la BD `decowandy_testing` (configurada en `phpunit.xml`). Créala antes de correr tests:
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS decowandy_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Funcionalidades clave
- Inventario con control de stock, mínimos, alertas y ajustes.
- Compras por sector con impacto en inventario (papelería con barcode).
- Ventas con modal POS, filtros por sector, venta mixta y validación de stock.
- Gastos, inversiones y reportes de finanzas (cashflow, ingresos vs gastos, utilidades).
- API internas para ítems, compras, ventas y catálogo público.
- Permisos modulares por rol (admin / personal con módulos combinables).
- Panel responsive optimizado para celular (menú drawer, POS fullscreen, KPIs 2×2, formularios en tarjetas) y toolbar POS en 2 filas en desktop.
- Escáner de códigos con linterna y feedback en pantalla.

## Interfaz (design system)

El panel admin usa un design system propio con tokens Tailwind (`dw-*`) y componentes Blade reutilizables.

**Tokens y estilos:** `tailwind.config.js` (incluye `resources/js/**/*.js` en el scan), `resources/css/app.css`.

**Componentes Blade** (`resources/views/components/`):
- `dw-button`, `dw-input`, `dw-card`, `dw-kpi`, `dw-badge`, `dw-nav-link`, `dw-page-header`

**Tema claro / oscuro:** selector en el header (oculto en pantallas muy pequeñas) y en login. Preferencia en `localStorage` (`dw-theme`).

**Móvil (&lt; 768px):**
- Menú lateral tipo drawer (`dw-admin-sidebar`): ☰ abrir, ✕ / fuera / navegar para cerrar.
- Header compacto con marca **DecoWandy** (sin título largo duplicado).
- **Registrar venta:** icono en header, botón flotante (FAB) y botón destacado en `/ventas`.
- Modales de compra al final del `<body>` (`@stack('modals')`) para evitar problemas de capas.
- Compras: líneas en tarjetas, filtros en 2 columnas, feedback sticky arriba del formulario.
- Dashboard: KPIs en grilla 2×2.

**Modal POS — Registrar venta** (`resources/views/sales/partials/modal-create.blade.php`, estilos en `resources/css/app.css`):

Layout responsive dual (móvil vs desktop):

| Viewport | Toolbar |
|----------|---------|
| **Móvil (< 768px)** | 3 filas: cliente/sectores → búsqueda/escanear → cantidades/agregar (botón ancho, labels visibles). Pantalla completa. |
| **Desktop (≥ 768px)** | 2 filas: (1) cliente + filtros de sector en una línea, repartidos en todo el ancho; (2) búsqueda, escáner, cant., valor, subtotal y botón **+** compacto (32px). |

Detalles de implementación:
- Filtros de sector (`.dw-pos-sector-filters`) separados del grid de método de pago (`.dw-pos-segments`); evita que el cuarto botón (Diseño) haga wrap.
- En desktop, overrides CSS del toolbar van **después** de los estilos base para no ser anulados en cascada.
- Filtros por sector, columna **Sector** en carrito, venta mixta en un ticket.
- Escáner integrado (HTTPS obligatorio en celular).

Tras cambios en vistas, CSS o JS del frontend:
```bash
npm run build
```
Los assets compilados van a `public/build/` (no se versionan; cada entorno debe compilar).

## Rutas útiles
- Login: `/login`
- Panel: `/dashboard`
- Inventario: `/inventario` (alias `/items`)
- Ventas: `/ventas`
- Compras y catálogo: `/compras`
- Gastos: `/gastos`
- Finanzas y reportes: `/finanzas`, `/reportes`
- Usuarios: `/ajustes/usuarios`
- Catálogo público: `/`

## Acceso desde la red local (Laragon / Apache)

**Por nombre local:** `http://decowandy.test` con `auto.decowandy.test.conf` y entrada en `hosts`.

**Por IP en la LAN:** añade `ServerAlias TU_IP_LAN` al VirtualHost de DecoWandy en `C:/laragon/etc/apache2/sites-enabled/auto.decowandy.test.conf`. Apache debe escuchar en `0.0.0.0:80`.

Tras editar la configuración de Apache, **reinicia Apache** en Laragon.

## Acceso por Tailscale (celular / remoto)

Para usar DecoWandy desde el teléfono u otro dispositivo en la red Tailscale:

### 1) VirtualHost Apache

En `C:/laragon/etc/apache2/sites-enabled/`:

- `auto.decowandy.test.conf` — dominio local `decowandy.test`
- `decowandy-tailscale.conf` — IP Tailscale dedicada (no lo regenera Laragon; recomendado para celular)

```apache
ServerAlias *.decowandy.test TU_IP_TAILSCALE
```

El `DocumentRoot` debe apuntar a `C:/laragon/www/decowandy/public` en ambos casos.

### 2) HTTPS (obligatorio para cámara del escáner)

El escáner por cámara (`getUserMedia`) exige contexto seguro: **HTTPS** o `localhost`. En el celular debes usar HTTPS.

Configuración en Laragon (`C:/laragon/etc/ssl/`):

1. **CA local** (`decowandy-ca.crt` / `decowandy-ca.key`) — se instala en el teléfono.
2. **Certificado del sitio** (`decowandy.test.crt` / `decowandy.test.key`) — solo en el servidor Apache.

El VirtualHost `:443` referencia el certificado del sitio. HTTP desde la IP Tailscale puede redirigir a HTTPS.

Generar o renovar certificados (OpenSSL de Laragon):

```bash
# CA (una vez)
openssl genrsa -out decowandy-ca.key 4096
openssl req -x509 -new -nodes -key decowandy-ca.key -sha256 -days 825 \
  -out decowandy-ca.crt -config decowandy-ca.cnf -extensions v3_ca

# Sitio (firmado por la CA)
openssl genrsa -out decowandy.test.key 2048
openssl req -new -key decowandy.test.key -out decowandy.test.csr -config decowandy.test.cnf
openssl x509 -req -in decowandy.test.csr -CA decowandy-ca.crt -CAkey decowandy-ca.key \
  -CAcreateserial -out decowandy.test.crt -days 825 -sha256 \
  -extensions v3_req -extfile decowandy.test.cnf
```

Copia `decowandy-ca.crt` al teléfono como `decowandy-ca.cer` para instalarlo.

### 3) Instalar CA en Android

1. Envía `decowandy-ca.cer` al teléfono (WhatsApp, correo, etc.).
2. **Ajustes → Seguridad → Cifrado y credenciales → Instalar certificado → Certificado CA**.
3. No uses “Certificado de usuario/VPN” (pedirá clave privada).
4. Reinicia el navegador y abre `https://TU_IP_TAILSCALE/login`.

### 4) Variables `.env`

```env
APP_URL=http://decowandy.test
SANCTUM_STATEFUL_DOMAINS=decowandy.test,TU_IP_TAILSCALE,localhost,127.0.0.1
SESSION_SECURE_COOKIE=
```

Laravel genera URLs según el host de la petición (`AppServiceProvider`), así PC y celular pueden coexistir.

```bash
php artisan config:clear
```

### 5) Firewall

Permite los puertos **80** y **443** en Windows (reglas de Apache HTTP Server).

### URLs de ejemplo (Tailscale)

| Uso | URL |
|-----|-----|
| Login | `https://TU_IP_TAILSCALE/login` |
| POS / ventas | `https://TU_IP_TAILSCALE/ventas` |
| Compras | `https://TU_IP_TAILSCALE/compras` |
| Inventario | `https://TU_IP_TAILSCALE/inventario` |

No uses `decowandy.test` desde el celular (ese dominio solo resuelve en la PC con `hosts`).

## Notas
- El seeder de producción no carga datos de prueba; usa datos reales del negocio.
- Si cambias configuraciones en `.env`, ejecuta `php artisan config:clear` en local o `php artisan config:cache` al desplegar.
- El proyecto usa traducciones y textos en español; manténlos coherentes al contribuir.
- Tras cambios en frontend (`resources/js`, `resources/css`, vistas Blade con clases `dw-*`), compila con `npm run build`.
- Los certificados SSL viven en `C:/laragon/etc/ssl/` (fuera del repo). No versiones claves privadas ni `.env`.
