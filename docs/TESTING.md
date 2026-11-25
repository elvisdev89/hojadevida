# Pruebas

## Herramientas

- **Pest**: framework de pruebas (funciona sobre PHPUnit).
- **Laravel Pint**: estilo de código.
- **Larastan**: análisis estático. Empieza en nivel 5 y sube progresivamente.

## Base de datos de pruebas

Se usa una base MySQL separada, **`hojadevida_test`**, configurada en `api/phpunit.xml`. El trait `RefreshDatabase` la deja limpia en cada prueba.

Usar el mismo motor que en producción evita falsos positivos (enums, FK, funciones de fecha).

## Tipos de prueba

| Tipo | Carpeta | Qué prueba |
|---|---|---|
| Unit | `tests/Unit` | Clases aisladas (enums, helpers), sin HTTP ni BD si es posible |
| Feature | `tests/Feature` | Endpoints completos: HTTP → BD → JSON |

En cada endpoint se cubren como mínimo:

1. El caso exitoso (código y estructura del JSON).
2. La validación (422).
3. Sin autenticación (401).
4. **Acceso a recursos de otro usuario (403 o 404)**.

## Organización actual

| Carpeta | Contenido |
|---|---|
| `tests/Feature/Database` | Integridad de datos (FK, cascadas, índices únicos) y seeders |
| `tests/Feature/Api/V1` | Endpoints, un archivo por recurso |

`tests/Pest.php` aplica `TestCase` y `RefreshDatabase` a todo `tests/Feature`.

## Comandos

```bash
cd api
composer test                          # todas las pruebas
php8.3 artisan test --filter=Language  # solo las que coinciden con el nombre
composer lint                          # Pint: corrige el formato
composer analyse                       # Larastan (configuración en phpstan.neon)
```

Antes de cada commit: `composer lint && composer analyse && composer test`.

## Larastan

- Configuración: `api/phpstan.neon`, **nivel 5**. Analiza `app`, `database` y `routes`. `tests/` queda fuera, porque Pest usa `$this` dentro de closures.
- No se silencian errores con `@phpstan-ignore` ni con un *baseline*: se corrige la causa.
- Ayuda a Larastan: PHPDoc genérico en las relaciones (`@return HasMany<Skill, $this>`), `@mixin Model` en los Resources y `@extends Factory<Model>` en las factories.

## Rendimiento (entorno local)

Con disco HDD, `RefreshDatabase` tarda unos 25 s en recrear el esquema. MySQL de desarrollo usa `/etc/mysql/mysql.conf.d/zz-development.cnf` (`innodb_flush_log_at_trx_commit = 2`, sin binlog) para bajar de 69 s a 25 s. **Nunca usar esa configuración en producción.**
