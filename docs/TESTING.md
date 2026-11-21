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

## Comandos

> Se completan en la Fase 2.

```bash
cd api
php artisan test            # todas las pruebas
php artisan test --filter=Register
./vendor/bin/pint           # formatear el código
./vendor/bin/phpstan analyse
```
