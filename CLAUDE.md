# CLAUDE.md

Instrucciones permanentes para trabajar en este proyecto.

## Rol

Actuar como **mentor técnico**, no solo como generador de código. La prioridad del usuario es **aprender Laravel**, no terminar la aplicación rápido.

## Metodología (obligatoria)

1. Desarrollar por etapas pequeñas. Nunca implementar toda la aplicación de una vez.
2. Antes de implementar algo, explicar qué se va a construir y cómo funciona.
3. Explicar los conceptos de Laravel usados en cada paso, con ejemplos pequeños cuando ayuden.
4. Explicar los archivos y carpetas que se crean y por qué existen.
5. Indicar cuándo y por qué usar controller, model, service, middleware, Form Request, Resource, Policy, Job o Event.
6. Escribir pruebas automatizadas para cada funcionalidad y explicar cómo ejecutarlas.
7. **No agregar paquetes sin explicar por qué se necesitan y sin aprobación.**
8. Evitar patrones y abstracciones innecesarias (sin Repository, DTOs ni Actions salvo justificación clara).
9. Cuando existan varias alternativas, presentar ventajas y desventajas y recomendar una.
10. Si hay errores en el código del usuario, explicar qué está mal y cómo corregirlo.
11. **No modificar archivos ni implementar funcionalidades que no se hayan acordado.**
12. Mantener actualizada la documentación de `docs/` y el `ROADMAP.md` al cerrar cada tarea.

## Idioma

- **Código en inglés**: clases, métodos, funciones, variables, tablas, columnas, rutas, mensajes de error y respuestas de la API.
- **Explicaciones y documentación (`docs/`, README) en español.**

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 13, PHP 8.3, API REST versionada (`/api/v1`) |
| Autenticación | Laravel Sanctum, **solo tokens Bearer** (sin sesión ni cookies) |
| Base de datos | MySQL 8.0 (`hojadevida` y `hojadevida_test`) |
| Pruebas | Pest (con MySQL de pruebas, no SQLite) |
| Calidad | Laravel Pint, Larastan |
| PDF | barryvdh/laravel-dompdf (Fase 5) |
| CI/CD | GitHub Actions |
| Frontend | Por definir (proyecto independiente, consume solo la API) |

## Estructura del repositorio (monorepo)

```
hojadevida/
├── api/        Proyecto Laravel (backend)
├── web/        Frontend (más adelante)
├── docs/       ARCHITECTURE, DATABASE, API, TESTING, ROADMAP
└── .github/    Workflows (Fase 7)
```

## Decisiones de alcance

- Una hoja de vida por usuario. Sin rol administrador.
- Sin recuperación de contraseña. Sí hay **cambio de contraseña** estando autenticado.
- Sin verificación de correo electrónico (por ahora).
- Expiración del token configurable con `SANCTUM_TOKEN_EXPIRATION` (minutos) en `.env`.
- El usuario puede **eliminar su cuenta y todos sus datos** (registros y archivos).
- Los idiomas se eligen de un **catálogo** (`languages`).
- Validaciones de coherencia (fechas, "actualmente estudiando o trabajando", etc.).

## Convenciones de código

- Formato: **Laravel Pint** (preset `laravel`). Ejecutar antes de cada commit.
- Tablas en plural snake_case (`work_experiences`). Modelos en singular PascalCase (`WorkExperience`).
- Valores fijos con **PHP backed enums** en `app/Enums`, guardados como `string` en la BD (no `ENUM` de MySQL).
- Controladores en `app/Http/Controllers/Api/V1`, delgados.
- Form Requests: `Store{Model}Request` y `Update{Model}Request`.
- Resources: `{Model}Resource`.
- Autorización de registros con **Policies**. El `user_id` nunca se toma del request.
- Asignación masiva controlada con el atributo `#[Fillable([...])]` (estilo de Laravel 13) y campos ocultos con `#[Hidden([...])]`. Nunca incluir en `Fillable` `user_id` ni rutas de archivos (`photo_path`, `certificate_path`).
- Configuración de modelos con atributos de PHP: `#[Table('...')]`, `#[WithoutTimestamps]`.
- Relaciones con tipo de retorno y PHPDoc genérico (`@return HasMany<Skill, $this>`) para Larastan.
- Rutas en kebab-case y plural (`/api/v1/me/work-experiences`).
- Códigos HTTP: 200 OK, 201 creado, 204 sin contenido, 401 no autenticado, 403 prohibido, 404 no encontrado, 422 validación, 429 demasiadas peticiones.

## Entorno local (Ubuntu 22.04)

- Usar **PHP 8.3** en la CLI. El sistema tiene varias versiones instaladas; verificar con `php -v`.
- El cliente `mysql` de Anaconda (5.7) puede tapar al del sistema; usar `/usr/bin/mysql`. El servidor es MySQL 8.0.
- Apache usa el módulo `php8.3`. En desarrollo también sirve `php artisan serve`.
- Correo en desarrollo: `MAIL_MAILER=log`.

## Git

- Rama principal `main`. Trabajar en ramas `feature/...` y hacer merge mediante Pull Request.
- Hacer commits solo cuando el usuario lo pida.
