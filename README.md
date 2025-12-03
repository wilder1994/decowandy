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

## Notas
- Las seeds crean datos iniciales listos para probar flujos completos (items, ventas, compras, gastos e inversiones).
- Si cambias configuraciones en `.env`, considera `php artisan config:cache` al desplegar.
- El proyecto usa traducciones y textos en espanol; mantelos coherentes al contribuir.
