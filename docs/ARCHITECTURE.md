# Arquitectura

## Visión general

Arquitectura **desacoplada**: el backend (`api/`) expone una API REST JSON y el frontend (`web/`) es un proyecto independiente que la consume. Toda la lógica de negocio vive en la API.

```
[ Cliente: Postman / frontend ] --HTTPS + Bearer token--> [ API Laravel ] ---> [ MySQL ]
                                                                 └-----------> [ Storage (fotos, certificados) ]
```

## Flujo de una petición

```
HTTP request
  → routes/api.php          (prefijo /api/v1)
  → Middleware              (auth:sanctum, throttle)
  → Controller              (coordina, sin lógica compleja)
  → Form Request            (authorize() + rules())
  → Policy                  (¿este usuario puede actuar sobre este registro?)
  → Modelo Eloquent         (persistencia y relaciones)
  → API Resource            (da forma al JSON de salida)
HTTP response (JSON)
```

## Estructura del backend

```
api/app/
├── Enums/                    Valores fijos (IdentificationType, EducationLevel...)
├── Http/
│   ├── Controllers/Api/V1/   Un controlador por recurso
│   ├── Requests/             Validación de entrada
│   └── Resources/            Forma de la salida JSON
├── Models/                   Eloquent
├── Policies/                 Autorización por registro
└── Services/                 Solo cuando la lógica lo justifique (p. ej. generación de PDF)
```

## Cuándo usar cada pieza

| Pieza | Responsabilidad |
|---|---|
| Controller | Recibir la petición, delegar y devolver la respuesta |
| Form Request | Validar los datos de entrada |
| Policy | Autorizar acciones sobre un registro concreto |
| Gate | Permisos que no dependen de un modelo |
| Resource | Transformar modelos a JSON |
| Middleware | Reglas que aplican a muchas rutas (autenticación, rate limit) |
| Service | Lógica reutilizable o compleja que no pertenece al modelo |
| Job | Trabajo lento que se ejecuta en segundo plano |
| Event | Avisar que algo ocurrió para que otras partes reaccionen sin acoplarse |

## Decisiones técnicas (ADR resumido)

| # | Decisión | Alternativa descartada | Motivo |
|---|---|---|---|
| 1 | Monorepo (`api/`, `web/`, `docs/`) | Dos repositorios | Un solo desarrollador; CI más simple |
| 2 | Sanctum con **tokens Bearer** | Cookies SPA | Funciona con Postman y con cualquier frontend; sin sesión |
| 3 | Expiración del token en `.env` (`SANCTUM_TOKEN_EXPIRATION`, minutos) | Valor fijo | El desarrollador lo ajusta según el entorno |
| 4 | Pest | PHPUnit puro | Sintaxis más legible; funciona sobre PHPUnit |
| 5 | MySQL para las pruebas | SQLite en memoria | Se prueba el mismo motor que en producción |
| 6 | PHP enums guardados como `string` | `ENUM` de MySQL o tablas catálogo | Cambiar un valor no requiere `ALTER TABLE`; tipado en PHP |
| 7 | Catálogo de idiomas (`languages`) | Texto libre | Evita duplicados ("Inglés" frente a "ingles") |
| 8 | Tablas hijas ligadas a `user_id` | Ligadas a `profile_id` | La autorización es directa (`user_id === auth id`) |
| 9 | Sin patrón Repository | Repository sobre Eloquent | Eloquent ya abstrae la BD; evitar sobreingeniería |
| 10 | dompdf para PDF | Browsershot (Chrome headless) | Solo PHP; despliegue simple |
| 11 | Sin recuperación de contraseña ni verificación de correo (por ahora) | Flujo forgot/reset y verificación | Decisión de alcance; sí hay cambio de contraseña. No se envían correos |
| 12 | Código, rutas y mensajes en inglés | Español | Convención del proyecto |

## Seguridad

- **IDOR**: cada acción sobre un registro pasa por una Policy.
- **Asignación masiva**: `$fillable`; el `user_id` se asigna desde el usuario autenticado.
- **Rate limiting** en registro y login.
- **Tokens**: expiran y se revocan en el logout.
- **Archivos**: validación de MIME y tamaño; los certificados van en un disco privado y se descargan por un endpoint autorizado.
- **Eliminación de cuenta**: borra registros, archivos y tokens (Ley 1581 de 2012, habeas data).
- **PDF**: escapar siempre con `{{ }}` en Blade y desactivar los recursos remotos en dompdf.
