# Próximos pasos — Fases 4 y 5

Documento de planificación para completar las funcionalidades avanzadas y el pulido final de Stardust Money.

---

## FASE 4 — Lógica de negocio avanzada

### 4.1 Cron de gastos recurrentes
**Objetivo:** que el sistema genere automáticamente gastos `recurring_child` en estado `draft = true` según las plantillas de `recurring_expenses`.

- Crear un `Command` (ej. `php artisan make:command GenerateRecurringExpenses`) o un `Job` (`GenerateRecurringExpenseDrafts`).
- Programarlo en `routes/console.php` para que corra diariamente a una hora fija.
- Lógica del comando:
  1. Buscar `recurring_expenses` donde `is_active = true` y `next_due_date <= hoy`.
  2. Por cada plantilla:
     - Crear un `expense` con:
       - `type = 'recurring_child'`
       - `draft = true`
       - `description`, `amount`, `category_id` copiados de la plantilla
       - `expense_date = next_due_date`
       - `recurring_expense_id` apuntando a la plantilla
       - `code` generado automáticamente
     - Actualizar `next_due_date = next_due_date + custom_interval_value custom_interval_unit`.
- Asegurar autorización: los gastos creados deben pertenecer al `user_id` de la plantilla.
- Crear tests de feature que verifiquen:
  - Se genera un draft cuando `next_due_date` venció.
  - No se genera si la plantilla está pausada.
  - `next_due_date` avanza correctamente según la frecuencia.
  - No se generan duplicados si el comando corre más de una vez el mismo día.

### 4.2 Gestión de cuotas
**Objetivo:** facilitar la creación de cada cuota como un `expense` vinculado a su `installment_group`.

- Desde la tabla de grupos de cuotas, agregar una acción **"Registrar cuota"** que abra el modal de gasto con:
  - `type` preseleccionado en `installment`.
  - `installment_group_id` preseleccionado.
  - `installment_number` sugerido (siguiente cuota no registrada).
- Opcional: mostrar un indicador de cuántas cuotas faltan por registrar.
- Opcional: al crear el grupo, ofrecer la opción de generar automáticamente todos los `expense` de cuotas con fechas estimadas.
- Tests:
  - Crear un gasto de tipo `installment` actualiza el progreso del grupo.
  - El número de cuota no puede superar `total_installments`.

### 4.3 Splits robustos
**Objetivo:** mejorar la experiencia y validación de gastos compartidos.

- Validar en el backend que la suma de splits no supere el monto total (ya existe validación básica).
- Mejorar la UI de splits:
  - Mostrar un indicador de cuánto falta por asignar.
  - Resaltar en rojo cuando la suma supere el total.
  - Permitir splits sin monto (solo nombre informativo).
- Permitir editar splits al editar un gasto (ya funciona parcialmente: se reemplazan todos).
- Tests:
  - No se permite guardar si splits > amount.
  - Al editar, los splits antiguos se reemplazan correctamente.

### 4.4 Webhook bancario (futuro, no urgente)
**Objetivo:** marcarlo como investigación pendiente.

- Evaluar si Unicredit o Wise exponen webhooks/API para detectar transferencias.
- Si es viable, crear un endpoint `/webhooks/bank` que reciba eventos y genere `expense` en `draft = true`.
- Por ahora: dejar un comentario en el modelo o un issue para no olvidarlo.

---

## FASE 5 — Pulido y producción

### 5.1 Progressive Web App (PWA)
**Objetivo:** que la app sea instalable en móviles y escritorio.

- Crear `public/manifest.json` con:
  - `name`, `short_name`, `start_url`, `display: standalone`
  - iconos en varios tamaños
  - tema y fondo
- Crear un service worker básico en `public/sw.js` para cachear assets estáticos.
- Registrar el service worker en el frontend.
- Generar iconos a partir de un logo base (pueden usarse herramientas online o scripts).
- Verificar con Lighthouse que cumpla los requisitos de PWA.

### 5.2 Responsive final
**Objetivo:** que todas las pantallas se vean bien en móvil.

- Revisar cada tabla DataTable en pantallas pequeñas:
  - Considerar `scrollX: true` o mostrar menos columnas.
  - Ajustar botones de acción para no amontonarse.
- Revisar modales:
  - Asegurar que no se desborden en móvil.
  - Scroll interno si el formulario es largo.
- Revisar el sidebar offcanvas en móvil.
- Probar al menos en 3 tamaños: móvil, tablet y desktop.

### 5.3 Tests finales
**Objetivo:** aumentar la cobertura de tests de las nuevas funcionalidades.

- Tests del cron de recurrentes.
- Tests de cuotas (creación y progreso).
- Tests de splits (límites y edición).
- Tests de la ruta pública `/share/{token}` con filtros variados.
- Tests de accesibilidad básica (contraste, labels en inputs).
- Ejecutar toda la suite con `php artisan test --compact`.

### 5.4 Optimizaciones
**Objetivo:** mejorar rendimiento y mantenibilidad.

- Revisar N+1 en DataTables (usar `with()` donde sea necesario).
- Considerar cachear categorías del usuario para no consultar en cada render.
- Revisar la consistencia de versiones en `package.json` (`@tailwindcss/vite` vs `tailwindcss`).
- Revisar logs de errores y corregir warnings menores.
- Limpiar estilos CSS que ya no se usan (sidebar-categories quedó obsoleto en FASE 1).

### 5.5 Preparación para deploy
**Objetivo:** dejar la app lista para producción.

- Verificar variables de entorno necesarias.
- Configurar correctamente `APP_URL`, `ASSET_URL` y `FORCE_HTTPS` si aplica.
- Asegurar que `php artisan optimize` funcione sin errores.
- Documentar comando del cron para producción.
- Revisar que las rutas públicas (`/share/{token}`) no requieran auth ni verificación.

---

## Recomendación de orden

1. **Prioridad alta:** implementar el cron de recurrentes (4.1). Es la funcionalidad que más valor le da a las plantillas.
2. **Prioridad alta:** mejorar la gestión de cuotas (4.2) para que los grupos no queden estáticos.
3. **Prioridad media:** pulir splits (4.3) y tests (5.3).
4. **Prioridad baja/media:** PWA (5.1) y responsive (5.2).
5. **Al final:** optimizaciones y preparación para deploy (5.4 y 5.5).
