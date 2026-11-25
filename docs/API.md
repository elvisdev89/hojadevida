# API

- URL base: `/api/v1`
- Formato: JSON (`Accept: application/json`)
- Autenticación: `Authorization: Bearer {token}`
- Idioma de mensajes y errores: inglés
- Expiración de tokens: `SANCTUM_TOKEN_EXPIRATION` en minutos (por defecto 1440 = 24 h; `0` = no expira). Un token vencido responde `401`.

> Borrador. Cada endpoint se documenta en detalle (request, response, errores) cuando se implementa.

## Formato de errores

```json
// 422 Validation error
{
  "message": "The email field is required.",
  "errors": { "email": ["The email field is required."] }
}

// 401, 403, 404, 429
{ "message": "Unauthenticated." }
```

## Endpoints planeados

Leyenda de **Auth**: — público · 🔑 token

### Autenticación y cuenta
| Método | Ruta | Auth | Descripción |
|---|---|---|---|
| POST | `/auth/register` | — | Registro ✅ |
| POST | `/auth/login` | — | Devuelve un token |
| POST | `/auth/logout` | 🔑 | Revoca el token actual |
| GET | `/me` | 🔑 | Usuario autenticado |
| PUT | `/me/password` | 🔑 | Cambia la contraseña (pide la actual) |
| DELETE | `/me` | 🔑 | Elimina la cuenta y todos sus datos (pide la contraseña) |

### Hoja de vida
| Método | Ruta | Auth | Descripción |
|---|---|---|---|
| GET / PUT | `/me/profile` | 🔑 | Información personal |
| POST / DELETE | `/me/profile/photo` | 🔑 | Foto de perfil |
| CRUD | `/me/educations` | 🔑 | Formación académica |
| CRUD | `/me/work-experiences` | 🔑 | Experiencia laboral |
| CRUD | `/me/references` | 🔑 | Referencias |
| CRUD | `/me/skills` | 🔑 | Habilidades |
| CRUD | `/me/courses` | 🔑 | Cursos y certificaciones |
| GET | `/me/courses/{course}/certificate` | 🔑 | Descarga el certificado adjunto |
| GET | `/languages` | — | Catálogo de idiomas ✅ |
| GET / POST | `/me/languages` | 🔑 | Listar y agregar idiomas |
| PUT / DELETE | `/me/languages/{language}` | 🔑 | Editar y quitar un idioma |
| GET | `/me/cv` | 🔑 | Hoja de vida consolidada |
| GET | `/me/cv/pdf` | 🔑 | Descarga en PDF |

CRUD = `GET` (listar), `POST` (crear), `GET /{id}`, `PUT /{id}`, `DELETE /{id}`.

✅ = implementado.

## Endpoints implementados

### `POST /auth/register`

Crea una cuenta y devuelve un token, así que el usuario queda autenticado sin un login adicional.

**Body**
| Campo | Reglas |
|---|---|
| `email` | Obligatorio, email válido, máx. 255, único. Se guarda sin espacios y en minúsculas |
| `password` | Obligatorio, 8 a 72 caracteres, con letras y números (`Password::defaults()` en `AppServiceProvider`) |
| `password_confirmation` | Debe coincidir con `password` |

**201 Created**
```json
{
  "data": {
    "token": "1|Xk9...",
    "user": { "id": 1, "email": "ana@example.com", "created_at": "2026-11-25T17:00:00.000000Z" }
  }
}
```

**422**: datos inválidos o email ya registrado (sin importar mayúsculas).

### `GET /languages`

Catálogo de idiomas ordenado por `name`. Público y sin paginación.

**200 OK**
```json
{
  "data": [
    { "id": 1, "code": "en", "name": "English" },
    { "id": 2, "code": "es", "name": "Spanish" }
  ]
}
```
