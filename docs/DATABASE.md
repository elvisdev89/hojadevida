# Base de datos

Motor: **MySQL 8.0**, charset `utf8mb4`.
Bases: `hojadevida` (desarrollo) y `hojadevida_test` (pruebas).

## Diagrama entidad-relación

```
users 1 ── 1 profiles
users 1 ── N educations
users 1 ── N work_experiences
users 1 ── N cv_references
users 1 ── N skills
users 1 ── N courses
users N ── M languages   (pivote: language_user, con level y certification)
```

Todas las FK hacia `users` usan `ON DELETE CASCADE`.

## Tablas

### users (Laravel)
| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK |
| email | varchar(255) | UNIQUE. Única fuente del correo |
| email_verified_at | timestamp | NULL. Viene con Laravel; no se usa mientras la verificación esté fuera de alcance |
| password | varchar(255) | Hash bcrypt |
| timestamps | | |

Sin `name` (el nombre vive en `profiles`, una sola fuente) ni `remember_token` (solo sirve para login con sesión).

### profiles
| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK users, **UNIQUE** (relación 1:1), cascade |
| first_name | varchar(100) | |
| last_name | varchar(100) | |
| identification_type | varchar(10) | Enum `IdentificationType` |
| identification_number | varchar(30) | |
| birth_date | date | |
| phone | varchar(30) | |
| address | varchar(255) | NULL |
| city | varchar(100) | |
| photo_path | varchar(255) | NULL |
| summary | text | NULL |
| timestamps | | |

Índice: `UNIQUE(identification_type, identification_number)`.

### educations
| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK users, índice, cascade |
| institution | varchar(150) | |
| title | varchar(150) | |
| level | varchar(30) | Enum `EducationLevel` |
| start_date | date | |
| end_date | date | NULL |
| is_current | boolean | default false |
| description | text | NULL |
| timestamps | | |

### work_experiences
| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK users, índice, cascade |
| company | varchar(150) | |
| position | varchar(150) | |
| start_date | date | |
| end_date | date | NULL |
| is_current | boolean | default false |
| responsibilities | text | |
| achievements | text | NULL |
| timestamps | | |

### cv_references
> No se llama `references` porque es una palabra reservada de MySQL.

| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK users, índice, cascade |
| full_name | varchar(150) | |
| type | varchar(20) | Enum `ReferenceType` |
| company | varchar(150) | NULL (obligatorio si `type = professional`) |
| position | varchar(150) | NULL |
| phone | varchar(30) | |
| email | varchar(255) | NULL |
| timestamps | | |

### skills
| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK users, cascade |
| name | varchar(100) | |
| category | varchar(30) | Enum `SkillCategory` |
| level | varchar(30) | Enum `SkillLevel` |
| timestamps | | |

Índice: `UNIQUE(user_id, name)`.

### languages (catálogo, cargado con seeder)
| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK |
| code | char(2) | UNIQUE, ISO 639-1 (`en`, `es`, `fr`...) |
| name | varchar(100) | Nombre en inglés |

### language_user (pivote)
| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK users, cascade |
| language_id | bigint unsigned | FK languages, restrict |
| level | varchar(10) | Enum `LanguageLevel` |
| certification | varchar(150) | NULL (p. ej. "IELTS 7.0") |
| timestamps | | |

Índice: `UNIQUE(user_id, language_id)`.

### courses
| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK users, índice, cascade |
| name | varchar(150) | |
| institution | varchar(150) | |
| completed_at | date | |
| expires_at | date | NULL |
| certificate_path | varchar(255) | NULL |
| timestamps | | |

### Tablas de soporte (Laravel y Sanctum)
| Tabla | Uso |
|---|---|
| `migrations` | Registro de migraciones ejecutadas (la crea Laravel) |
| `cache`, `cache_locks` | Caché (`CACHE_STORE=database`), necesaria para el rate limiting |
| `jobs`, `job_batches`, `failed_jobs` | Colas (`QUEUE_CONNECTION=database`) |
| `personal_access_tokens` | Tokens de Sanctum |

Se eliminaron `sessions` (la API no usa sesión: `SESSION_DRIVER=array`) y `password_reset_tokens` (sin recuperación de contraseña).

> `personal_access_tokens` es **polimórfica** (`tokenable_type`, `tokenable_id`) y **no tiene FK** hacia `users`. Al eliminar una cuenta, los tokens se borran explícitamente.

## Enums

| Enum | Valores |
|---|---|
| IdentificationType | `CC`, `CE`, `TI`, `PA`, `PPT` |
| EducationLevel | `high_school`, `technical`, `technologist`, `undergraduate`, `specialization`, `master`, `doctorate` |
| ReferenceType | `personal`, `professional` |
| SkillCategory | `technical`, `soft`, `tool`, `other` |
| SkillLevel | `basic`, `intermediate`, `advanced`, `expert` |
| LanguageLevel | `A1`, `A2`, `B1`, `B2`, `C1`, `C2`, `native` |

## Reglas de coherencia

| Regla | Dónde se valida |
|---|---|
| `end_date >= start_date` | Form Request (`after_or_equal:start_date`) |
| Si `is_current = true`, entonces `end_date` es NULL | Form Request (`prohibited_if`) |
| Si `is_current = false`, entonces `end_date` es obligatoria | Form Request (`required_if`) |
| `start_date` y `end_date` no son futuras | Form Request (`before_or_equal:today`) |
| `birth_date` en el pasado y edad razonable | Form Request |
| `expires_at > completed_at` | Form Request |
| `company` obligatoria en referencias profesionales | Form Request (`required_if:type,professional`) |
| Un idioma no se repite por usuario | Índice UNIQUE + Form Request |
| Una habilidad no se repite por usuario | Índice UNIQUE + Form Request |
| Un documento de identidad no se repite | Índice UNIQUE + Form Request |

Regla general: la **BD garantiza la integridad** (FK, UNIQUE, NOT NULL) y el **Form Request da mensajes claros** al cliente.
