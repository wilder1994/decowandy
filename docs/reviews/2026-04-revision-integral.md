# Revision Integral

## Metadatos
- Fecha: 2026-04-03
- Proyecto: DecoWandy
- Alcance: revision completa de todos los archivos versionados del repositorio
- Estado general: en seguimiento

## Resumen Ejecutivo
- El proyecto tiene riesgos criticos en permisos, gestion de usuarios y exposicion de areas sensibles.
- Hay riesgo alto de ruptura funcional en ventas, integridad historica de inventario y consistencia de reportes financieros.
- La suite de pruebas no es reproducible en un clon limpio y hoy no sirve como red de seguridad confiable.
- El storefront publico tiene errores funcionales visibles en enlaces, anchors y configuracion de contacto.

## Cobertura De La Revision
- La revision se particiono en 6 bloques funcionales con cobertura completa del repo versionado.
- Resultado de cobertura: 0 archivos sin asignar y 0 traslapes en `git ls-files`.
- La revision excluyo `vendor/` y `node_modules/` por no estar versionados.

## Permisos Y Acceso
- Hallazgo: no hay autorizacion real en rutas privilegiadas; varias areas sensibles dependen solo de `auth`.
- Hallazgo: `ManageUserRequest` autoriza a cualquier usuario autenticado.
- Hallazgo: `AuthServiceProvider` no registra gates o policies efectivos para la gestion de usuarios.
- Hallazgo: el registro publico con autologin permite que un usuario nuevo alcance paneles que deberian ser restringidos.
- Riesgo: escalacion de privilegios, exposicion de datos financieros y administracion no autorizada.
- Evidencia: `routes/web.php`, `app/Http/Requests/ManageUserRequest.php`, `app/Providers/AuthServiceProvider.php`, `app/Http/Controllers/Auth/RegisteredUserController.php`.
- Accion pendiente: definir matriz de autorizacion, gates/policies y pruebas de acceso por rol.

## Gestion De Usuarios
- Hallazgo: al crear usuarios desde ajustes, el rol enviado no se persiste correctamente y la BD puede dejarlos como `admin` por defecto.
- Hallazgo: `User` no incluye `role` ni `active` en `$fillable`.
- Hallazgo: la politica de contrasenas en gestion de usuarios es mas debil que la del resto del sistema.
- Riesgo: promocion involuntaria de usuarios y debilitamiento de seguridad operativa.
- Evidencia: `app/Models/User.php`, `app/Http/Controllers/SettingsUserController.php`, `database/migrations/2025_10_17_030731_add_role_to_users_table.php`.
- Accion pendiente: corregir persistencia de rol/estado y unificar reglas de credenciales.

## Ventas
- Hallazgo: `SaleController::store()` usa `$isAdmin` dentro de una transaccion sin capturarlo en la closure.
- Hallazgo: el metodo de pago esta forzado a `cash` en request, UI y persistencia.
- Hallazgo: la vista filtrada de ventas puede ocultar datos validos por activar una seccion distinta a la filtrada.
- Riesgo: errores `500`, ventas mal clasificadas y experiencia inconsistente para operacion diaria.
- Evidencia: `app/Http/Controllers/SaleController.php`, `app/Http/Requests/StoreSaleRequest.php`, `resources/views/sales/index.blade.php`, `resources/views/sales/partials/modal-create.blade.php`.
- Accion pendiente: corregir flujo de alta, permitir metodo de pago real y cubrir el caso con pruebas.

## Inventario, Compras E Items
- Hallazgo: la UI fuerza borrado duro de items y puede eliminar historial comercial y movimientos por cascada.
- Hallazgo: compras, ventas y alertas usan reglas distintas para determinar que items afectan inventario.
- Hallazgo: `syncStock()` modifica stock sin registrar `StockMovement`, rompiendo la trazabilidad del kardex.
- Hallazgo: el rollback de inventario reintroduce columnas legacy y deja dos fuentes potenciales de verdad.
- Riesgo: perdida de trazabilidad, stock inconsistente y datos historicos destruidos.
- Evidencia: `resources/views/items/index.blade.php`, `app/Http/Controllers/Api/ItemController.php`, `app/Services/InventoryService.php`, migraciones de `sale_items`, `stock_movements` y `cleanup_items_inventory_columns`.
- Accion pendiente: eliminar borrado duro para historial, unificar reglas de inventario y restaurar fuente unica de verdad.

## Finanzas, Reportes Y Dashboard
- Hallazgo: `cashflowDataset()` compara fechas casteadas a `Carbon` contra strings y puede subreportar salidas.
- Hallazgo: el filtro por categoria en reportes no se aplica de forma consistente a todos los widgets y datasets.
- Hallazgo: no hay autorizacion fina para gastos, inversiones, finanzas y reportes.
- Hallazgo: el costo de venta del reporte depende del costo actual del item y no de un costo historico congelado.
- Hallazgo: el KPI de ganancia diaria es enganoso porque ignora costos relevantes y oculta perdidas.
- Riesgo: decisiones de negocio sobre cifras incorrectas y exposicion de informacion financiera sensible.
- Evidencia: `app/Services/FinanceService.php`, `app/Http/Controllers/FinanceController.php`, `app/Http/Controllers/ReportController.php`, `app/Http/Controllers/InvestmentController.php`, `resources/views/dashboard.blade.php`.
- Accion pendiente: corregir calculos, alinear filtros y restringir acceso por rol.

## Storefront Publico Y Catalogo
- Hallazgo: el CTA global de WhatsApp del layout publico usa un numero hardcodeado y no la configuracion activa.
- Hallazgo: existen anchors rotos entre tarjetas, footer y paginas de categoria.
- Hallazgo: slugs invalidos en catalogo devuelven `200` con pagina vacia en lugar de `404`.
- Hallazgo: la UI del editor de catalogo tiene categorias hardcodeadas mientras el backend usa config.
- Riesgo: navegacion rota, errores SEO, incoherencia entre admin y sitio publico.
- Evidencia: `resources/views/layouts/public.blade.php`, `resources/views/welcome.blade.php`, `resources/views/welcome/partials/*`, `app/Http/Controllers/CatalogController.php`, `app/Support/CatalogView.php`.
- Accion pendiente: unificar origen de configuracion, arreglar enlaces y validar slugs inexistentes.

## Perfil, Auth Y Verificacion
- Hallazgo: `ProfileUpdateRequest` no define `authorize()`.
- Hallazgo: el modelo `User` no implementa `MustVerifyEmail` aunque el flujo de verificacion esta montado.
- Riesgo: flujo de perfil bloqueado e incoherencia en acceso a rutas `verified`.
- Evidencia: `app/Http/Requests/ProfileUpdateRequest.php`, `app/Models/User.php`, `tests/Feature/Auth/EmailVerificationTest.php`.
- Accion pendiente: corregir request de perfil y alinear verificacion de email con el comportamiento real esperado.

## Console Y Automatizacion
- Hallazgo: el comando `customers:archive-inactive` archiva tambien clientes nuevos sin compras.
- Riesgo: clientes vigentes pueden desaparecer del flujo comercial por automatizacion diaria.
- Evidencia: `app/Console/Commands/ArchiveInactiveCustomers.php`, `app/Console/Kernel.php`.
- Accion pendiente: redefinir criterio de inactividad y cubrir el comando con pruebas.

## Testing Y Entorno
- Hallazgo: la suite falla en un entorno limpio porque la configuracion de testing no esta aislada de MySQL local.
- Hallazgo: la documentacion no cubre completamente pasos criticos de despliegue como `storage:link` y assets de Vite.
- Hallazgo: la configuracion global sigue en defaults genericos que no coinciden con el dominio en espanol del proyecto.
- Riesgo: falta de red de seguridad, onboarding fragil y despliegues incompletos.
- Evidencia: `phpunit.xml`, `.env.example`, `README.md`, `config/app.php`, `config/filesystems.php`.
- Accion pendiente: aislar entorno de test, completar documentacion operativa y alinear configuracion base.

## Prioridad Sugerida
1. Permisos, roles y gestion de usuarios.
2. Flujo de ventas y proteccion del historial de items.
3. Aislamiento y recuperacion de la suite de pruebas.
4. Correccion de inventario y kardex.
5. Correccion de finanzas y reportes.
6. Ajustes funcionales del storefront y catalogo.

## Backlog Inmediato
- [ ] Restringir acceso a rutas sensibles con gates o policies reales.
- [ ] Corregir creacion de usuarios para persistir rol y estado correctamente.
- [ ] Arreglar `SaleController::store()` para eliminar el error por `$isAdmin`.
- [ ] Quitar borrado duro de items con impacto historico.
- [ ] Definir una unica fuente de verdad para stock y movimientos.
- [ ] Corregir `cashflowDataset()` y alinear filtros de reportes.
- [ ] Habilitar un entorno de testing aislado y reproducible.
- [ ] Corregir CTA de WhatsApp, anchors y respuesta `404` del catalogo.
