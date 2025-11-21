# DecoWandy

Aplicación Laravel para gestionar catálogo, ventas, compras, gastos e inversiones de DecoWandy. El proyecto está listo para trabajar con datos reales: no incluye semillas de ejemplo y las vistas del panel muestran información calculada con la base de datos.

## Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- Una base de datos MySQL/MariaDB o SQLite

## Puesta en marcha
1. Clona el repositorio y entra a la carpeta del proyecto.
2. Copia el archivo de entorno y genera la llave de aplicación:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Configura las credenciales de tu base de datos en `.env`.
4. Instala dependencias de PHP y JavaScript:
   ```bash
   composer install
   npm install
   ```
5. Crea la estructura limpia de tablas (no hay datos importantes almacenados):
   ```bash
   php artisan migrate:fresh
   ```
6. Compila los assets para desarrollo:
   ```bash
   npm run dev
   ```

## Uso
- Inicia el servidor de desarrollo con `php artisan serve` y abre el panel en `/dashboard` (requiere autenticación).
- El catálogo público está disponible en `/` y las secciones internas incluyen ventas, compras, gastos, inventario y finanzas.
- Las inversiones cuentan con un CRUD completo en `/finanzas/inversiones` y las métricas financieras se calculan en vivo en `/finanzas` y `/reportes`.

## Notas
- Se eliminaron las semillas para trabajar únicamente con información real. Usa migraciones para reconstruir las tablas cuando lo necesites.
- Todos los textos visibles están en español para facilitar el uso del equipo.
