# Roadmap

Leyenda: [x] hecho · [ ] pendiente

## Fase 1: Análisis y arquitectura
- [x] Análisis de requerimientos y decisiones de alcance
- [x] Modelo entidad-relación (`DATABASE.md`)
- [x] Arquitectura (`ARCHITECTURE.md`)
- [x] Estructura del monorepo y documentación inicial
- [x] Verificación del entorno (PHP 8.3.36, MySQL 8.0.46)
- [x] Configurar `php` de la CLI en la versión 8.3
- [x] Crear el repositorio en GitHub y hacer el primer commit

## Fase 2: Fundamentos de Laravel
- [x] Crear el proyecto Laravel 13 en `api/` con Pest
- [x] Configurar `.env` y las bases `hojadevida` y `hojadevida_test`
- [x] Adaptar las migraciones por defecto (sin `name`, `sessions` ni `password_reset_tokens`)
- [x] Instalar la API y Sanctum (`install:api`)
- [x] Hacer configurable la expiración del token (`SANCTUM_TOKEN_EXPIRATION`)
- [x] Crear los enums
- [x] Crear las migraciones
- [x] Crear los modelos y las relaciones
- [x] Crear las factories y las pruebas de integridad de datos
- [x] Crear los seeders (catálogo de idiomas y usuario demo)
- [x] Crear el primer endpoint: `GET /api/v1/languages`
- [x] Hacer que los errores se respondan en JSON
- [x] Configurar Pint y Larastan (nivel 5)

## Fase 3: Autenticación y usuarios
- [x] Registro
- [ ] Login y logout
- [ ] `GET /me`
- [ ] Cambio de contraseña
- [ ] Eliminación de la cuenta y de todos sus datos
- [ ] Rate limiting

## Fase 4: Gestión de la hoja de vida
- [ ] Información personal
- [ ] Formación académica
- [ ] Experiencia laboral
- [ ] Referencias
- [ ] Habilidades
- [ ] Idiomas (desde el catálogo)
- [ ] Cursos y certificaciones
- [ ] Hoja de vida consolidada

## Fase 5: Documentos
- [ ] Foto de perfil
- [ ] Certificados adjuntos (disco privado)
- [ ] Plantilla Blade del CV
- [ ] Generación y descarga del PDF (dompdf)

## Fase 6: Pruebas y calidad
- [ ] Cobertura de código
- [ ] Subir el nivel de Larastan
- [ ] Refactorizaciones

## Fase 7: CI/CD y despliegue
- [ ] Workflow de Pint y Larastan
- [ ] Workflow de pruebas con MySQL
- [ ] Protección de la rama `main`
- [ ] Preparación para producción
- [ ] Despliegue automatizado

## Fuera de alcance por ahora
- Recuperación de contraseña
- Verificación de correo electrónico
- Rol administrador
- Varias hojas de vida por usuario
- Enlace público para compartir el CV
- Frontend (tecnología por definir)
