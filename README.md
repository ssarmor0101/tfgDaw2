# ClassicGames — TFG DAW 2

> Plataforma web de videojuegos clásicos con sistema de puntuaciones, logros, amigos y panel de administración.

---

## Índice

1. [Vista rápida](#-vista-rápida)
2. [Instalación](#-instalación)
   - [Frontend](#frontend)
   - [Backend](#backend)
3. [Vídeo-manual](#-vídeo-manual)
4. [Documentación técnica](#-documentación-técnica)
   - [Arquitectura general](#arquitectura-general)
   - [Frontend](#frontend-1)
   - [Backend](#backend-1)
   - [Base de datos](#base-de-datos)
   - [API REST](#api-rest)
   - [Despliegue](#despliegue)
5. [Bitácora](#-bitácora)
6. [Bibliografía](#-bibliografía)
7. [Autor](#autor)

---

## 🔗 Vista rápida

| Recurso | Enlace |
|---|---|
| **Aplicación en producción** | [https://tfg-daw2.vercel.app](https://tfg-daw2.vercel.app/) |
| **Prototipado de alta fidelidad (Figma)** | [Ver en Figma](https://www.figma.com/design/eZSOtx0nape547XsrcyTN5/TFG-DAW-2?node-id=0-1&t=UHWYyBZbmt8j8V9C-1) |
| **Esquema entidad-relación** | [Wiki — Entidad-Relación](https://github.com/ssarmor0101/tfgDaw2/wiki/EntidadRelacion) |
| **Repositorio** | [github.com/ssarmor0101/tfgDaw2](https://github.com/ssarmor0101/tfgDaw2) |
| **Vídeo del proyecto** | [Ver](https://youtu.be/BL265je3uDE) |

---

## 🛠 Instalación

### Requisitos previos

| Herramienta | Versión mínima |
|---|---|
| Node.js | 20 LTS |
| npm | 10 |
| PHP | 8.2 |
| Composer | 2 |
| MySQL | 8 |

---

### Frontend

El frontend es una SPA con Vite. Se encuentra en `Frontend/tfg-front-app`.

```bash
# 1. Entrar al directorio
cd Frontend/tfg-front-app

# 2. Instalar dependencias
npm install

# 3. Crear el archivo de variables de entorno
cp .env.example .env
# Editar .env y apuntar VITE_API_URL a la URL de tu backend local:
#   VITE_API_URL=http://localhost:8000/api

# 4. Arrancar el servidor de desarrollo
npm run dev
```

La aplicación estará disponible en `http://localhost:5173`.

Para generar el build de producción:

```bash
npm run build
# Los archivos estáticos se generan en dist/
```

---

### Backend

El backend es una API REST con Laravel 11. Se encuentra en `Server/tfg-server-app`.

```bash
# 1. Entrar al directorio
cd Server/tfg-server-app

# 2. Instalar dependencias PHP
composer install

# 3. Crear el archivo de entorno
cp .env.example .env

# 4. Configurar .env:
#   DB_DATABASE=classicgames
#   DB_USERNAME=root
#   DB_PASSWORD=tu_password
#   APP_URL=http://localhost:8000
#   FRONTEND_URL=http://localhost:5173   (para CORS)

# 5. Generar la clave de aplicación
php artisan key:generate

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Arrancar el servidor de desarrollo
php artisan serve
```

La API estará disponible en `http://localhost:8000/api`.

> **Credenciales por defecto** generadas por los seeders:
> - Admin: `admin@classicgames.com` / `password`
> - Usuario: `user@classicgames.com` / `password`

---

## 🎬 Vídeo

[Video](https://youtu.be/BL265je3uDE)

---

## 📐 Documentación técnica

### Arquitectura general

ClassicGames es una aplicación **desacoplada** compuesta por dos capas independientes:

```
┌─────────────────────────────────────────────────────┐
│              CLIENTE (Navegador / Móvil)            │
│                                                     │
│   React SPA (Vite + TypeScript + TailwindCSS)       │
│   Desplegado en Vercel                              │
└───────────────────┬─────────────────────────────────┘
                    │  HTTP / JSON  (Bearer Token)
┌───────────────────▼─────────────────────────────────┐
│              API REST (Laravel 11)                  │
│                                                     │
│   Autenticación: Laravel Sanctum                    │
│   Arquitectura: Repository → Service → Controller  │
│   Desplegado en Railway                             │
└───────────────────┬─────────────────────────────────┘
                    │  Eloquent ORM
┌───────────────────▼─────────────────────────────────┐
│              Base de datos (MySQL 8)                │
└─────────────────────────────────────────────────────┘
```

Toda la comunicación entre frontend y backend se realiza mediante **fetch nativo** sobre endpoints JSON. Las respuestas siempre siguen la misma envoltura:

```json
{
  "data": { ... },
  "status": {
    "success": true,
    "message": "Operación realizada correctamente"
  }
}
```

---

### Frontend

#### Stack

| Tecnología | Versión | Rol |
|---|---|---|
| React | 19 | UI y gestión de estado local |
| TypeScript | 6 | Tipado estático |
| Vite | 8 | Bundler y servidor de desarrollo |
| TailwindCSS | 3.4 | Estilos utilitarios |
| React Router | 7 | Enrutado en cliente |
| react-paginate | 8 | Paginación de tablas |

#### Estructura de carpetas

```
src/
├── assets/             Imágenes y recursos estáticos
├── components/
│   ├── header/         Header con nav desktop y menú móvil
│   └── ui/             Componentes reutilizables (ErrorMessage,
│                       GameCard, LoadingSpinner, ...)
├── config/
│   └── apiRoutes.js    Centralización de todas las URLs de la API
├── context/
│   └── AuthContext.tsx Contexto de autenticación (token + user)
├── games/
│   ├── brick-breaker.tsx   Implementación del juego (Canvas API)
│   └── registry.ts         Registro de juegos disponibles por slug
├── hooks/
│   └── useGames.ts     Hook genérico para cargar listas de juegos
├── types/              Interfaces TypeScript compartidas
├── utils/
│   └── friendlyError.ts    Traducción de errores HTTP a mensajes
│                           amigables en español
└── views/
    ├── admin/          Panel de administración completo
    ├── auth/           Login y Registro
    ├── friends/        Lista de amigos y puntuaciones de jugadores
    ├── games/          Catálogo, detalle, logros y puntuaciones
    ├── home/           Página de inicio
    ├── profile/        Perfil de usuario
    └── scores/         Tabla global de puntuaciones
```

#### Flujo de autenticación

1. El usuario hace login → el backend devuelve un token Sanctum.
2. El token y los datos del usuario se guardan en `localStorage`.
3. `AuthContext` los expone mediante `useAuth()` a toda la app.
4. Las rutas protegidas (`ProtectedRoute`, `AdminRoute`) redirigen a `/login` si no hay sesión activa.
5. Todas las peticiones autenticadas incluyen la cabecera `Authorization: Bearer <token>`.

#### Sistema de juegos

Los juegos se implementan como componentes React de carga diferida (lazy). Un registro central (`games/registry.ts`) mapea el slug del juego al importador dinámico:

```ts
// Ejemplo simplificado
const LOADERS: Record<string, () => Promise<{ default: ComponentType }>> = {
  'brick-breaker': () => import('./brick-breaker'),
}
```

Las puntuaciones se guardan en `localStorage` bajo la clave `classicgames:score:{gameId}` y se emiten también como evento `classicgames:score` sobre `window` para que `GameDetail` las detecte en tiempo real sin depender del ciclo de render del juego.

Los juegos soportan **teclado, ratón y pantalla táctil** (drag para controlar la pala en Brick Breaker).

#### Manejo de errores

El helper `friendlyError(err)` convierte cualquier error de red o respuesta HTTP en un mensaje legible en español. El componente `<ErrorMessage>` llama automáticamente a `humanizeMessage()` por lo que todos los errores quedan normalizados sin necesidad de transformarlos en cada vista.

---

### Backend

#### Stack

| Tecnología | Versión | Rol |
|---|---|---|
| PHP | 8.2 | Lenguaje |
| Laravel | 11 | Framework MVC / API |
| Laravel Sanctum | 4 | Autenticación por token |
| Eloquent ORM | — | Acceso a base de datos |
| MySQL | 8 | Base de datos relacional |
| Laravel Socialite | — | OAuth (preparado) |

#### Estructura de carpetas

```
app/
├── DTOs/               Objetos de transferencia de datos
│   ├── Amigo/
│   ├── Auth/
│   ├── Juego/
│   ├── Logro/
│   ├── Puntuacion/
│   ├── Resultado/
│   └── User/
├── Enums/              Enumeraciones PHP
├── Helpers/            Funciones auxiliares
├── Http/
│   ├── Controllers/
│   │   └── Api/        Un controlador por recurso
│   ├── Middleware/      RolMiddleware (comprueba rol del usuario)
│   └── Requests/       Form Requests para validación
├── Models/             Modelos Eloquent
├── Policies/           Políticas de autorización por recurso
├── Providers/
├── Repositories/       Capa de acceso a datos (consultas Eloquent)
└── Services/           Lógica de negocio
```

#### Arquitectura en capas

El backend sigue un patrón **Repository → Service → Controller → DTO**:

```
Petición HTTP
     │
     ▼
Controller          Valida Request, llama al Service, devuelve JSON
     │
     ▼
Service             Orquesta la lógica de negocio, usa Repositories
     │
     ▼
Repository          Encapsula las consultas Eloquent/SQL
     │
     ▼
Model (Eloquent)    Mapeo ORM a la tabla MySQL
     │
     ▼
DTO (salida)        Serializa los datos antes de enviarlos al cliente
```

Esta separación garantiza que:
- Los controladores son finos (solo orquestan request → response).
- La lógica de negocio reside en los servicios (testeable de forma aislada).
- Los repositorios son el único punto de contacto con la base de datos.
- Los DTOs controlan exactamente qué campos se exponen al cliente.

#### Roles y autorización

Existen dos roles definidos por `RolSeeder`:

| ID | Slug | Descripción |
|---|---|---|
| 1 | `admin` | Acceso total, panel de administración |
| 2 | `user` | Jugador registrado |

Las rutas de administración están protegidas por el middleware `rol:admin`. Las rutas de usuario autenticado por `auth:sanctum`. Adicionalmente, las `Policies` de Laravel controlan acciones específicas (ej.: un usuario solo puede editar su propio perfil).

#### Convenciones de respuesta

Todos los endpoints devuelven la misma estructura:

```json
// Éxito
{ "data": { ... }, "status": { "success": true, "message": "..." } }

// Error de validación (422)
{ "errors": { "campo": ["mensaje"] }, "message": "..." }

// Error de aplicación (4xx / 5xx)
{ "data": null, "status": { "success": false, "message": "..." } }
```

Los métodos de colección usan paginación o límite configurable desde el servicio.

---

### Base de datos

#### Entidades principales

| Tabla | Descripción |
|---|---|
| `users` | Usuarios de la plataforma |
| `roles` | Roles (admin / user) |
| `juegos` | Catálogo de juegos |
| `logros` | Logros asociados a cada juego |
| `puntuaciones` | Puntuaciones publicadas por usuarios |
| `resultados` | Logros desbloqueados por usuarios |
| `amigos` | Relaciones de amistad (y solicitudes pendientes) |
| `partidas` | Sesiones de juego registradas |
| `personal_access_tokens` | Tokens Sanctum |

#### Relaciones clave

```
users  ──< puntuaciones >── juegos
users  ──< resultados   >── logros
users  ──< amigos       >── users    (auto-relación)
juegos ──< logros
juegos ──< partidas >── users
```

La tabla `amigos` gestiona tres estados: **pendiente** (solo existe la fila con `sender_id` + `receiver_id`), **aceptada** y **rechazada**, controlados mediante la relación bidireccional y los métodos del `AmigoService`.

---

### API REST

Base URL: `https://<backend>/api`

#### Públicas (sin autenticación)

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/login` | Iniciar sesión |
| POST | `/register` | Registrar cuenta |
| GET | `/juegos` | Listado de juegos |
| GET | `/juegos/populares` | Juegos más jugados |
| GET | `/juegos/recientes` | Juegos recientes |
| GET | `/juegos/buscar` | Buscar por nombre |
| GET | `/juegos/{id}` | Detalle de un juego |
| GET | `/logros/juego/{id}` | Logros de un juego |
| GET | `/puntuaciones` | Tabla global de puntuaciones |
| GET | `/puntuaciones/juego/{id}` | Puntuaciones de un juego |
| GET | `/puntuaciones/user/{uid}/juego/{jid}` | Puntuaciones de un jugador en un juego |
| GET | `/users/search/{name}` | Buscar usuarios por nombre |

#### Autenticadas (`auth:sanctum`)

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/logout` | Cerrar sesión |
| GET | `/account` | Obtener perfil propio |
| PUT | `/account` | Actualizar perfil propio |
| DELETE | `/account` | Eliminar cuenta |
| GET | `/puntuaciones/me/juego/{id}` | Mis puntuaciones en un juego |
| POST | `/puntuaciones/juego/{id}` | Publicar puntuación |
| GET | `/resultados/juego/{id}` | Mis logros en un juego |
| POST | `/resultados/logro/{id}` | Desbloquear logro |
| GET | `/amigos` | Lista de amigos |
| GET | `/amigos/solicitudes` | Solicitudes pendientes |
| POST | `/amigos/solicitud` | Enviar solicitud de amistad |
| PUT | `/amigos/{id}/aceptar` | Aceptar solicitud |
| DELETE | `/amigos/{id}/rechazar` | Rechazar solicitud |
| DELETE | `/amigos/{id}` | Eliminar amistad |

#### Administración (`rol:admin`)

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/admin/dashboard` | Estadísticas del dashboard |
| GET/POST | `/users` | Listar / crear usuarios |
| GET/PUT/DELETE | `/users/{id}` | Ver / editar / eliminar usuario |
| POST/PUT/DELETE | `/juegos` | Gestión de juegos |
| POST/PUT/DELETE | `/logros` | Gestión de logros |
| GET/POST/PUT/DELETE | `/puntuaciones` | Gestión de puntuaciones |
| GET/POST/PUT/DELETE | `/resultados` | Gestión de resultados |
| GET/POST/PUT/DELETE | `/admin/amigos` | Gestión de amistades |

---

### Despliegue

| Capa | Plataforma | Notas |
|---|---|---|
| Frontend | **Vercel** | Build automático desde rama `main` |
| Backend | **Railway** | Contenedor PHP con Artisan serve |
| Base de datos | **Railway** (MySQL 8) | Instancia gestionada |

Variables de entorno necesarias en producción:

**Frontend (Vercel)**
```
VITE_API_URL=https://<tu-backend>.railway.app/api
```

**Backend (Railway)**
```
APP_ENV=production
APP_KEY=<generada>
APP_URL=https://<tu-backend>.railway.app
FRONTEND_URL=https://tfg-daw2.vercel.app
DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD   (provistos por Railway)
SANCTUM_STATEFUL_DOMAINS=tfg-daw2.vercel.app
SESSION_DOMAIN=.vercel.app
```

---

## 📋 Bitácora

| Semana | Actividades |
|---|---|
| **Sem 1–2** (Ene 2026) | Definición del proyecto, elección de tecnologías, bocetos iniciales y esquema E-R |
| **Sem 3–4** (Ene 2026) | Prototipado de alta fidelidad en Figma. Setup de repositorio monorepo |
| **Sem 5–6** (Feb 2026) | Scaffolding Laravel + migraciones + seeders. Scaffolding React + Vite + Tailwind |
| **Sem 7–8** (Feb 2026) | Módulo de autenticación completo (registro, login, logout, Sanctum) |
| **Sem 9–10** (Mar 2026) | CRUD de juegos y logros. API pública de puntuaciones |
| **Sem 11–12** (Mar 2026) | Frontend: catálogo de juegos, detalle, tabla de puntuaciones |
| **Sem 13–14** (Abr 2026) | Implementación de Brick Breaker (Canvas API). Sistema de guardado de puntuación en localStorage |
| **Sem 15** (Abr 2026) | Módulo social: amigos, solicitudes, puntuaciones de amigos |
| **Sem 16** (May 2026) | Panel de administración: CRUD usuarios, juegos, logros, puntuaciones |
| **Sem 17** (May 2026) | Dashboard de administración con estadísticas en tiempo real |
| **Sem 18** (May 2026) | Mejoras UX: menú móvil responsive, soporte táctil en Brick Breaker, manejo centralizado de errores, notificación temporal de puntuación publicada |
| **Próximamente** | Vídeo-manual, pruebas E2E, posibles nuevos juegos |

---

## 📚 Bibliografía

### Documentación oficial

- **React 19** — [react.dev](https://react.dev/)
- **React Router v7** — [reactrouter.com/docs](https://reactrouter.com/docs)
- **TypeScript** — [typescriptlang.org/docs](https://www.typescriptlang.org/docs/)
- **Vite** — [vitejs.dev/guide](https://vitejs.dev/guide/)
- **TailwindCSS v3** — [tailwindcss.com/docs](https://tailwindcss.com/docs)
- **Laravel 11** — [laravel.com/docs/11.x](https://laravel.com/docs/11.x)
- **Laravel Sanctum** — [laravel.com/docs/11.x/sanctum](https://laravel.com/docs/11.x/sanctum)
- **Eloquent ORM** — [laravel.com/docs/11.x/eloquent](https://laravel.com/docs/11.x/eloquent)

### Recursos adicionales

- **Canvas API (MDN)** — [developer.mozilla.org/en-US/docs/Web/API/Canvas_API](https://developer.mozilla.org/en-US/docs/Web/API/Canvas_API)
- **Touch Events (MDN)** — [developer.mozilla.org/en-US/docs/Web/API/Touch_events](https://developer.mozilla.org/en-US/docs/Web/API/Touch_events)
- **Vercel Docs** — [vercel.com/docs](https://vercel.com/docs)
- **Railway Docs** — [docs.railway.app](https://docs.railway.app)
- **Figma** — [figma.com](https://figma.com)
- **React Paginate** — [github.com/AdeleD/react-paginate](https://github.com/AdeleD/react-paginate)

---

## Autor

**Sergio Sarmiento Moreno**  
Desarrollo de Aplicaciones Web — DAW 2  
Curso 2025–2026
