# Stardust Money — Lógica de Negocio

## ¿Qué es?

Aplicación web de registro de gastos personales. Solo registra **egresos**, no ingresos. Uso propio con arquitectura preparada para multi-usuario futuro. Deployable en producción, instalable como PWA.

**Stack:** Laravel 11 · MySQL 8.1 · Blade + Livewire · Bootstrap

---

## Entidades principales

### `categories`
Clasificación de los gastos.

- Hay un set de categorías **default** seedeadas (Alimentación, Transporte, Vivienda, etc.) que **nadie puede eliminar** — controlado por `CategoryPolicy`
- Los usuarios pueden crear sus propias categorías custom
- Las categorías custom pertenecen a un usuario (`user_id`)
- Las default tienen `user_id = null` e `is_default = true`

---

### `expenses`
La tabla central. Representa cada gasto real.

**Campos clave:**
- `code` — identificador corto legible, alternativa al UUID para el usuario
- `draft` — boolean. `true` = pendiente de confirmación por el usuario, `false` = confirmado. El usuario ve los drafts en el dashboard y decide si confirmarlos o ajustarlos
- `type` — enum que clasifica el origen del gasto
- `expense_date` — fecha del gasto (datetime en DB, date en front)
- `amount` — monto total que salió del bolsillo del usuario

**Tipos de gasto (`type`):**

| Tipo | Descripción |
|------|-------------|
| `one_time` | Gasto puntual, sin vínculo a nada |
| `recurring_child` | Generado por el cron desde un `recurring_expense` padre |
| `installment` | Cuota manual vinculada a un `installment_group` |

**El tipo se elige desde un select en el formulario.** Según la opción elegida, el formulario muestra u oculta campos adicionales:
- `one_time` → no aparece nada extra
- `installment` → aparece select de grupo de cuotas y campo número de cuota
- `recurring_child` → aparece select de recurrentes

---

### `recurring_expenses`
Plantilla de un gasto que se repite periódicamente. **No es un gasto en sí** — es el padre que genera gastos hijos.

**Campos clave:**
- `custom_interval_value` + `custom_interval_unit` — definen la frecuencia. Ej: `2 weeks`, `1 month`, `1 year`
- `next_due_date` — fecha en que el cron debe generar el próximo draft
- `is_active` — permite pausar sin eliminar

**Flujo:**
1. Usuario crea el `recurring_expense` con descripción, monto referencial, frecuencia y `next_due_date`
2. El cron diario detecta registros donde `next_due_date <= hoy` y `is_active = true`
3. Crea un `expense` con `type = 'recurring_child'` y `draft = true`
4. Actualiza `next_due_date = next_due_date + interval`
5. Usuario ve el draft, lo confirma o ajusta el monto si cambió

---

### `installment_groups`
Agrupa las cuotas de un mismo gasto. Ej: "Notebook en 12 cuotas".

**Campos clave:**
- `total_amount` — monto total del bien o servicio
- `total_installments` — cuántas cuotas en total

**Flujo:**
1. Usuario crea el `installment_group` con descripción, monto total y cantidad de cuotas
2. Mes a mes crea manualmente cada `expense` con `type = 'installment'`, vinculándolo al grupo y seteando `installment_number` (ej: 3 de 12)
3. Puede ver el progreso del grupo (cuántas cuotas pagadas vs total)

---

### `expense_splits`
Detalle informativo de gastos compartidos. Cuando el usuario pagó todo y otros corresponden parte.

**Reglas:**
- `person_name` — requerido si existe el split
- `amount` — opcional, cuánto le corresponde a esa persona
- La suma de todos los splits **no puede superar** el `amount` total del gasto
- Es **informativo**, no es un sistema de deudas ni registra pagos recibidos
- El usuario puede registrar que Pedro debe €30 de una cena de €90, pero si Pedro paga no se registra como ingreso

---

### `shared_reports`
Links públicos para compartir un subconjunto de gastos sin autenticación.

**Campos clave:**
- `token` — string único (64 chars), es la URL pública (`/share/{token}`)
- `filters` — JSON con los filtros aplicados (categoría, fechas, tipo, etc.)
- `label` — nombre descriptivo. Ej: "Clases de violín para el profesor"
- `expires_at` — expiración opcional

**Flujo:**
1. Usuario crea un `shared_report` con label, filtros y expiración opcional
2. Sistema genera un token único
3. Usuario comparte la URL `/share/{token}`
4. El visitante ve los gastos filtrados con la UI normal, sin poder modificar nada

**Caso de uso real:** El usuario toma clases de violín y paga cada clase en efectivo. Comparte un link con el profesor para que pueda ver el historial de pagos sin necesidad de cuenta.

---

## Flujos principales

### Creación de gasto manual
1. Usuario completa el formulario y presiona Guardar
2. El `expense` se crea en el `submit` con todos los datos + `draft = false` (es un gasto confirmado)
3. Si hay splits, se crean los registros en `expense_splits`
4. El formulario se resetea para permitir registrar otro gasto

### Limpieza diaria (cron)
Un único cron diario se encarga de:
- Eliminación definitiva de todos los registros con `deleted_at` con más de 30 días

### Creación por cron (gastos recurrentes)
1. Job diario busca `recurring_expenses` donde `next_due_date <= hoy` y `is_active = true`
2. Crea un `expense` con `type = 'recurring_child'` y `draft = true` con los datos de la plantilla
3. Actualiza `next_due_date` sumando el intervalo configurado
4. El usuario ve el draft en el dashboard, lo revisa y lo confirma (o ajusta el monto)
5. Al confirmar, `draft` pasa a `false`

### Confirmación de draft
- Acción `approve()` en el modelo base — setea `draft = false`
- Disponible para cualquier modelo que tenga columna `draft`
- Controlada por `ExpensePolicy` — solo el dueño puede confirmar sus gastos

---

## Autenticación y autorización

- Auth con credentials de Laravel
- Multi-usuario preparado — todos los registros tienen `user_id`
- **`CategoryPolicy`** — solo el dueño puede eliminar sus categorías custom. Las default no se pueden eliminar nunca
- **`ExpensePolicy`** — solo el dueño puede ver, editar, eliminar y confirmar sus gastos
- El scope `byAuthor` en `BaseModel` filtra por `user_id` en queries internas (crons, jobs, comandos)

---

## Webhook bancario (futuro, no urgente)

Integración con Unicredit o Wise para crear drafts automáticamente cuando se detecta una transferencia. El expense se crea en `draft = true` para que el usuario lo revise y categorice antes de confirmar. Pendiente de verificar si Unicredit expone una API pública.

---

## Páginas

### Autenticadas
| Página | Descripción |
|--------|-------------|
| Dashboard | Resumen del mes, últimos gastos, drafts pendientes, estadísticas |
| Gastos | Listado con filtros colapsables, formulario en modal |
| Recurrentes | Gestión de plantillas, crear/pausar/activar |
| Cuotas | Listado de grupos, progreso por grupo |
| Categorías | Gestión de categorías custom |
| Reportes compartidos | Gestión de links públicos |

### Públicas
| Página | Descripción |
|--------|-------------|
| `/share/{token}` | Vista pública de un reporte compartido, read-only |

---

## Filtros disponibles (ExpenseFilter)

| Filtro | Tipo | Descripción |
|--------|------|-------------|
| `user_id` | string | Siempre inyectado desde el servidor, nunca del cliente |
| `type` | enum | `one_time`, `recurring_child`, `installment` |
| `draft` | boolean | `true` = pendientes, `false` = confirmados |
| `category_id` | uuid | Filtrar por categoría |
| `date_from` | date | Desde fecha |
| `date_to` | date | Hasta fecha |