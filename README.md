# DecoWandy

Aplicación Laravel para gestionar catálogo, ventas, compras, gastos, inventario e inversiones de DecoWandy. Incluye panel administrativo, API internas, códigos de barras, etiquetas y reportes financieros.

**Última actualización de esta documentación:** 2026-06-16 (catálogo: lista PDF, toolbar compacta, modal papelería unificado, cabecera Compras en una línea)

## Requisitos
- PHP 8.2+
- Composer
- Node.js 20+ (`pdfjs-dist` exige Node ≥ 20; ver `.nvmrc`)
- MySQL/MariaDB

## Puesta en marcha (Laragon)

1) Clona el repo y entra al proyecto.

2) Copia `.env` y genera la llave:
```bash
cp .env.example .env
php artisan key:generate
```

3) Configura la base de datos y dominios en `.env` (copia desde `.env.example`):
```env
DB_DATABASE=decowandy
DB_USERNAME=root
DB_PASSWORD=
APP_URL=http://decowandy.test
SANCTUM_STATEFUL_DOMAINS=decowandy.test,TU_IP_TAILSCALE,TU_HOST_TAILSCALE,localhost,127.0.0.1
SESSION_SECURE_COOKIE=
```
`TU_IP_TAILSCALE` y `TU_HOST_TAILSCALE` (MagicDNS, p. ej. `yuri.tailXXXX.ts.net`) son opcionales al inicio; son **obligatorios** si usarás el celular por Tailscale (ver sección más abajo).

4) Define el administrador inicial en `.env` (**no** uses `DB_PASSWORD` para esto):
```env
ADMIN_NAME=DecoWandy
ADMIN_EMAIL=tu-correo@gmail.com
ADMIN_PASSWORD=tu_contraseña_segura
```
La contraseña debe tener al menos 8 caracteres. En Laragon, `DB_PASSWORD` suele quedar vacío (`root` sin clave).

5) Instala dependencias y compila assets (Node 20):
```bash
composer install
npm install
npm run build
```
En Laragon con varias versiones de Node, usa la 20 para este proyecto (`.nvmrc`). Ejemplo Windows:
```powershell
$env:Path = "C:\laragon\bin\nodejs\node-v20;$env:Path"
npm install
npm run build
```

6) Crea la base de datos, migra y provisiona el administrador:
```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

El seeder lee `ADMIN_NAME`, `ADMIN_EMAIL` y `ADMIN_PASSWORD` del `.env` y crea (o actualiza) el usuario administrador. Si ya migraste sin `--seed`, ejecuta:
```bash
php artisan decowandy:ensure-admin
```

7) En Laragon: **Start All** (Apache + MySQL). Acceso local: `http://decowandy.test`

No necesitas `php artisan serve` ni `npm run dev` en uso diario si los assets ya están compilados en `public/build/`.

### VirtualHost local
Laragon debe servir `decowandy/public` con `auto.decowandy.test.conf` y la entrada `127.0.0.1 decowandy.test` en `hosts`. Tras cambios en Apache, reinicia Laragon.

## Módulos del panel

| Módulo | Ruta | Rol |
|--------|------|-----|
| **Compras y catálogo** | `/compras` | Pestañas **Compras** (historial y filtros) y **Catálogo** (ficha POS por sector). Alta papelería vía **Agregar**; impresión/diseño como servicio o insumo. |
| **Inventario** | `/inventario` | Stock, alertas, ajustes (no papelería) y **Comprar más** para reposición papelería |
| **Ventas (POS)** | `/ventas` | Venta mixta: impresión, diseño y papelería en un ticket |

- `/items` redirige a `/inventario` (compatibilidad).
- **Papelería** se da de alta desde **Compras → Agregar** (modal unificado de compra con código de barras); la ficha comercial (precio, stock, código, visibilidad) se edita en **Compras → Catálogo**.
- **Reposición papelería** desde **Inventario → Comprar más** (mismo modal de compra, modo reposición).
- **Impresión / Diseño** se registran en Compras → Agregar, o desde el catálogo en la pestaña Catálogo.

### Catálogo (`/compras?tab=catalogo`)

- Toolbar compacta por sector (segment control + buscador + acciones).
- Tabla paginada con edición de ficha completa en modal (stock, mínimo, código DWY, color, precios).
- **Generar código** (`DWY-XXXX`) en edición: previsualiza con `GET /api/items/next-barcode` y persiste con `generate_barcode` en `PUT /api/items/{id}`.
- **Lista** — modal con selección múltiple, filtro en vivo y **Descargar PDF** de productos del sector activo.
- **Etiquetas** (solo papelería) — wizard de impresión por lote (mismo flujo que Inventario).

## Códigos de barras y etiquetas

- Códigos internos formato `DWY-XXXXXX` (`ItemBarcodeService`).
- Escáner pro en navegador (`resources/js/barcode-scanner.js`, `html5-qrcode`):
  - Pantalla casi fullscreen en móvil, marco de escaneo y línea animada.
  - Soporte QR + códigos 1D (EAN, UPC, Code 128, Code 39, ITF, etc.).
  - Linterna (si el dispositivo la soporta), reintentar y errores visibles en el propio escáner.
  - Toast global (`dw-toast.js`) para feedback inmediato.
  - Requiere **HTTPS** en celular (`getUserMedia`).
- Búsqueda por código en API y POS; compras de papelería crean o actualizan ítems (`PurchasePapeleriaService`).
- Etiquetas PNG/PDF por ítem o lote (`ItemLabelService`): compactas (nombre + Code 128 + número), **5 por fila** en carta, máx. **200 etiquetas**.
- **Imprimir etiquetas** (wizard en Compras → Catálogo (papelería) e Inventario): grilla con checkbox, nombre/código, cantidad; filtro en vivo; vista previa PDF con **PDF.js** (720px); descargar o imprimir.
- API de etiquetas:
  - `GET /api/items/labels/candidates?search=` — productos con barcode (papelería activa)
  - `POST /api/items/labels/preview` — PDF inline para vista previa
  - `POST /api/items/labels/sheet` — descarga del PDF
- **Lista de catálogo (PDF):**
  - `GET /api/items/catalog-export?sector=` — productos activos del sector (hasta 500, para el modal Lista)
  - `POST /api/items/catalog-list/pdf` — PDF con `sector` + `item_ids[]` de los seleccionados
- Alta rápida papelería: `POST /api/items/papeleria/quick`.
- Dependencias: `picqer/php-barcode-generator`, `pdfjs-dist` (npm, vista previa), `html5-qrcode` (npm).

Tras clonar o actualizar el esquema:
```bash
php artisan migrate:fresh --seed
```
En entornos con datos reales, usa solo `php artisan migrate` (sin `--seed` ni `--fresh`).

### Esquema de base de datos

Las migraciones están **consolidadas**: una migración `create_*` por tabla, con el esquema final (roles/capabilities en `users`, barcode en `items`, `customer_id` en `sales`, FKs `restrictOnDelete` en historial de ventas/movimientos). No hay migraciones `add_*` ni parches sueltos. Instalación limpia = `migrate:fresh --seed`.

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

La lógica está en `AdminUserProvisioner` (comando `decowandy:ensure-admin` y `AdminUserSeeder`).

## Testing
- Suite completa: `php artisan test`
- La suite usa la BD `decowandy_testing` (configurada en `phpunit.xml`). Créala antes de correr tests:
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS decowandy_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Funcionalidades clave
- Inventario con control de stock, mínimos, alertas y ajustes (papelería: solo reposición vía compra, sin ajuste manual).
- Compras por sector con impacto en inventario; modal unificado papelería (alta y reposición).
- Catálogo POS por sector: edición de ficha, lista exportable a PDF y etiquetas (papelería).
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
- Compras: líneas en tarjetas, filtros en 2 columnas, feedback sticky arriba del formulario; cabecera en una línea (título + pestañas Compras/Catálogo + Agregar).
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

**Por nombre local:** `http://decowandy.test` con `auto.decowandy.test.conf` y entrada `127.0.0.1 decowandy.test` en `hosts`.

**Por IP en la LAN:** archivo dedicado `C:/laragon/etc/apache2/sites-enabled/decowandy-lan-ip.conf` con `ServerName TU_IP_LAN` y `DocumentRoot` apuntando a `decowandy/public`. Apache escucha en `0.0.0.0:80`.

> **Escáner en celular por LAN:** HTTP en la IP local **no** activa la cámara (`getUserMedia`). Para escanear desde el teléfono usa **HTTPS por Tailscale** (sección siguiente).

Tras editar Apache, **reinicia Apache** en Laragon.

## Acceso por Tailscale (celular / remoto)

Para usar DecoWandy desde el teléfono (POS, escáner de códigos, compras) u otro dispositivo en la red Tailscale.

**Obtén tu IP y hostname Tailscale** (en la PC servidor):
```bash
tailscale ip -4
tailscale status
```
Anota `TU_IP_TAILSCALE` (p. ej. `100.x.x.x`) y `TU_HOST_TAILSCALE` (MagicDNS, p. ej. `yuri.tailXXXX.ts.net`).

### 1) VirtualHost Apache

En `C:/laragon/etc/apache2/sites-enabled/` (fuera del repo; no lo regenera Laragon):

| Archivo | Uso |
|---------|-----|
| `auto.decowandy.test.conf` | PC local: `http://decowandy.test` |
| `decowandy-lan-ip.conf` | LAN: `http://TU_IP_LAN` (sin escáner) |
| `decowandy-tailscale.conf` | Celular/remoto: HTTPS + redirect desde HTTP |

Ejemplo `decowandy-tailscale.conf` (HTTP → HTTPS, solo Tailscale):

```apache
# HTTP: redirige a HTTPS (obligatorio para escáner)
<VirtualHost *:80>
    ServerName TU_HOST_TAILSCALE
    ServerAlias TU_IP_TAILSCALE
    DocumentRoot "C:/laragon/www/decowandy/public"
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
</VirtualHost>

# HTTPS: certificado local (ver paso 2)
<VirtualHost *:443>
    ServerName TU_HOST_TAILSCALE
    ServerAlias TU_IP_TAILSCALE
    DocumentRoot "C:/laragon/www/decowandy/public"
    SSLEngine on
    SSLCertificateFile "C:/laragon/etc/ssl/decowandy.test.crt"
    SSLCertificateKeyFile "C:/laragon/etc/ssl/decowandy.test.key"
    SSLCertificateChainFile "C:/laragon/etc/ssl/decowandy-ca.crt"
    <Directory "C:/laragon/www/decowandy/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

El `DocumentRoot` debe ser `C:/laragon/www/decowandy/public`. Apache ya incluye `Listen 443` vía `C:/laragon/etc/apache2/httpd-ssl.conf`.

### 2) HTTPS (obligatorio para cámara del escáner)

El escáner (`resources/js/barcode-scanner.js`, `html5-qrcode`) usa `getUserMedia`, que exige **contexto seguro**: HTTPS o `localhost`. En el celular debes usar **HTTPS** con la IP o MagicDNS de Tailscale.

Certificados en `C:/laragon/etc/ssl/` (no versionar claves ni `.env`):

| Archivo | Rol |
|---------|-----|
| `decowandy-ca.crt` / `.key` | CA local (instalar `.cer` en Android) |
| `decowandy.test.crt` / `.key` | Certificado del sitio (Apache `:443`) |
| `decowandy-ca.cnf` | Config OpenSSL de la CA |
| `decowandy.test.cnf` | Config del sitio con SAN (dominio + IP Tailscale) |

El certificado del sitio debe incluir en **SAN** al menos: `decowandy.test`, `TU_HOST_TAILSCALE`, `TU_IP_TAILSCALE`, `localhost`, `127.0.0.1`.

Ejemplo `decowandy.test.cnf` (sección `[alt_names]`):
```ini
DNS.1 = decowandy.test
DNS.2 = TU_HOST_TAILSCALE
DNS.3 = localhost
IP.1 = TU_IP_TAILSCALE
IP.2 = 127.0.0.1
```

Generar o renovar (OpenSSL de Laragon: `C:/laragon/bin/git/mingw64/bin/openssl.exe`):

```bash
cd C:/laragon/etc/ssl

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

# Para Android
copy decowandy-ca.crt decowandy-ca.cer
```

### 3) Instalar CA en Android

1. Envía `decowandy-ca.cer` al teléfono (WhatsApp, correo, etc.).
2. **Ajustes → Seguridad → Cifrado y credenciales → Instalar certificado → Certificado CA**.
3. No uses “Certificado de usuario/VPN” (pedirá clave privada).
4. Reinicia el navegador.
5. Con **Tailscale activo** en el celular, abre `https://TU_IP_TAILSCALE/login`.

### 4) Variables `.env`

```env
APP_URL=http://decowandy.test
SANCTUM_STATEFUL_DOMAINS=decowandy.test,TU_IP_TAILSCALE,TU_HOST_TAILSCALE,localhost,127.0.0.1
SESSION_SECURE_COOKIE=
```

- `APP_URL` queda en `decowandy.test` para la PC local.
- Laravel adapta URLs al host de la petición (`AppServiceProvider::boot`), así PC (`http://decowandy.test`) y celular (`https://TU_IP_TAILSCALE`) coexisten.
- Tras cambiar `.env`: `php artisan config:clear`.

### 5) Firewall

Permite los puertos **80** y **443** en Windows (reglas de Apache HTTP Server).

### URLs de ejemplo (Tailscale)

| Uso | URL |
|-----|-----|
| Login | `https://TU_IP_TAILSCALE/login` |
| POS / escáner | `https://TU_IP_TAILSCALE/ventas` |
| Compras | `https://TU_IP_TAILSCALE/compras` |
| Inventario | `https://TU_IP_TAILSCALE/inventario` |

Alternativa MagicDNS: `https://TU_HOST_TAILSCALE/ventas`.

**No uses** `decowandy.test` desde el celular (solo resuelve en la PC con `hosts`).

**Checklist rápido escáner en celular:**
1. Tailscale conectado en PC y celular.
2. CA instalada en Android.
3. URL con `https://` (no `http://`).
4. Permiso de cámara concedido en el navegador.

## Notas
- El seeder de producción solo crea el **administrador** desde `.env`; no carga datos de prueba del negocio.
- Si cambias configuraciones en `.env`, ejecuta `php artisan config:clear` en local o `php artisan config:cache` al desplegar.
- El proyecto usa traducciones y textos en español; manténlos coherentes al contribuir.
- Tras cambios en frontend (`resources/js`, `resources/css`, vistas Blade con clases `dw-*`), compila con `npm run build`.
- Los certificados SSL viven en `C:/laragon/etc/ssl/` (fuera del repo). No versiones claves privadas ni `.env`.
