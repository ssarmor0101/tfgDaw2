
# Gestor de Tareas en Laravel

Vamos a trasladar nuestro proyecto **Gestor de Proyectos** al framework ***Laravel***.

# 1. ENTORNO

## 1.1. Entorno con Docker

Utilizaremos tres contenedores:

* Servidor web (build php:8.2-apache).
* Base de datos (mysql-8.0).
* Cliente web para la base de datos (phpmyadmin/phpmyadmin:latest).

Adicionalmente utilizaremos un fichero de configuración para que el entorno sea lo más parametrizable posible.

### 1.1.1. Preparación del entorno

Dispondremos de tres contenedores (web, db y phpmyadmin) y un fichero de configuración.

#### 1.1.1.1. Adaptar el fichero de configuración (en principio sólo usuario y grupo). Puedes consultar tu id de usuario y tu id de grupo con los siguientes comandos:
        id -u
        id -g
#### 1.1.1.2. Eliminar todos los contenedores de la solución:
        docker compose --env-file .env.docker down -v
#### 1.1.1.3. Comprobar que no tenemos contenedores activos:
        docker compose --env-file .env.docker ps
#### 1.1.1.4. Construir el contenedor web:
        docker compose --env-file .env.docker build
#### 1.1.1.5. Levantar los contenedores
        docker compose --env-file .env.docker up -d
#### 1.1.1.6. Acceder al contenedor web:
        docker exec -it docker-web-1 bash
#### 1.1.1.7. Comprobar que composer funciona correctamente en el contenedor web:
        composer -v
#### 1.1.1.8. Crear el proyecto/solución Laravel dentro del contenedor web:
        composer create-project laravel/laravel gestor-tareas
#### 1.1.1.9. Comprobar el correcto funcionamiento del entorno:
        http://localhost:8081
        http://localhost:8080/gestor-tareas/public/index.php

### 1.1.2. Restauración del entorno

Si descargamos el proyecto de Git hay que recordar que tanto las dependencias como el fichero de entorno no están trackeados (ver ficheros .gitignore).

#### 1.1.2.1. Acceder al contenedor web:
        docker exec -it docker-web-1 bash
#### 1.1.2.2. Indicar a composer que instale las dependencias en el contenedor web (en la ruta adecuada):
        composer install   
#### 1.1.2.3. Restaurar fichero de configuración en el contenedor web:
##### 1.1.2.3.1. Si no tienes ningún fichero .env toma como referencia el fichero .env.example. Haz una copia del fichero anterior y renómbrala:
        cp .env.example .env
##### 1.1.2.3.2. Genera una nueva clave:
        php artisan key:generate
##### 1.1.2.3.3. Asegúrate de que el nuevo fichero tiene, al menos, las siguientes entradas correctas:
        APP_KEY=base64:[key generada con el comando anterior]
        ...
        DB_CONNECTION=mysql
        DB_HOST=db
        DB_PORT=3306
        DB_DATABASE=[nombre de la base de datos, ver docker]
        DB_USERNAME=[nombre de usuario de la base de datos, ver docker]
        DB_PASSWORD=[password de usuario de la base de datos, ver docker]

### 1.1.3. Utilización habitual del entorno

Lo habitual es que los pasos anteriores los realicemos sólo una vez (al arrancar el proyecto o al crear un nuevo entorno de trabajo).
Para trabajar habitualmente sólo utilizaremos los comandos para levantar y tumbar los contenedores:

        docker compose --env-file .env.docker up -d
        docker compose down

## 1.2. Otros posibles entornos

Para desarrollo de aplicacionew web con *Laravel* existen entornos más automatizados:

* Laragon
* Laravel Sail

## 1.3. Visual Studio Code

Las extensiones recomendadas para trabajar con *Docker* son las siguientes:

COMPLETAR

Las extensiones recomendadas para trabajar con *PHP* y *Laravel* en *VS Code* son las siguientes:

* PHP IntelliSense
* PHP Intelephense
* Laravel Blade Snippets
* Laravel Artisan
* Laravel Extra Intellisense
* Laravel Blade Formatter

## 1.4. Artisan

*Artisan* es la interfaz de línea de comandos (CLI) de *Laravel* (viene incluida por defecto).

        php artisan

Permite ejecutar tareas comunes de desarrollo sin utilizar el navegador ni escribir código. Nosotros la utilizaremos fundamentalmente para crear 
elementos en nuestra aplicación: migraciones, modelos, controladores, etc.

Se ejecuta dentro del servidor web (en nuestro caso en el contenedor web) en la carpeta del proyecto.

# 2. CONFIGURACIÓN Y ESTRUCTURA DEL PROYECTO LARAVEL

app/ → lógica de la aplicación (modelos, controladores)


routes/ → rutas de la aplicación


resources/views/ → vistas Blade


database/migrations/ → migraciones de base de datos


Y otras carpetas necesarias.

public/index.php


COMPLETAR

# 3. MIGRACIONES, DATOS INICIALES Y DATOS DE PRUEBA

## 3.1 Migraciones

Una migración es una forma de versionar la base de datos igual que versionas el código. Te permite:

* Crear tablas
* Modificar columnas
* Añadir claves foráneas
* ...

### 3.1.1 Crear una migración (lanzar siempre en el contenedor web)

        php artisan make:migration [nombre del fichero]

¿Qué nombre utilizo para el fichero? Depende de la operación que quiera incluir:

        php artisan make:migration create_tareas_table
        php artisan make:migration add_descripcion_to_proyectos_table

Todos los nombres en formato **snake_case**. Aquí tienes algunas ideas:

* Usa verbos como create, add, update, remove
* Describe lo que hace:
  * create_proyectos_table
  * add_descripcion_to_proyectos_table
  * create_rols_table
  * add_foreign_key_to_tareas_table

El nombre no afecta a la ejecución interna, pero sí a la legibilidad y comprensión.

Una vez lanzado el comando observaremos que se ha creado un nuevo fichero en la ruta /database/migrations.

El nombre del fichero estará precedido de el instante en el que se generó: 2025_12_05_142358_create_tareas_table.php

### 3.1.2 Estructura básica de una migración

Cada fichero de migración incluye una o varias operaciones sobre el esquema de base de datos, fundamentalmente relacionadas
con las tablas: creación, modificación y eliminación. Dentro de cada tabla podemos añadir, modificar y eliminar columnas, añadir,
modificar o eliminar relaciones... Cualquier operación que hacíamos antes con un script DDL en SQL ahora lo hacemos de manera
más compacta incluyéndolo en la propia solución.

Por defecto Laravel incluye siempre tres migraciones:
* 0001_01_01_000000_create_users_table  // Para usuarios y autenticación, no es conveniente modificarla
* 0001_01_01_000000_create_cache_table  // Caché para la base de datos (probablemente no lo utilicemos)
* 0001_01_01_000000_create_jobs_table   // Cola de trabajos (probablemente no lo utilicemos)

**Regla de oro**: una migración = una tabla

Algunos aspectos a tener en cuenta:
* Es muy recomendable indicar que queremos las columnas *timestamps()* en las tablas de producción (created_at y updated_up).
* Utilizamos *id* para claves primarias.
* Utilizamos *foreignId* para claves foráneas, pero siempre acompañado de *constrained*.
* Si no sigues la convención de nomenclatura de Laravel tendrás que hacer más trabajo manual a la hora de configurar las claves foráneas.
* Es recomendable utilizar *softDeletes* para las tablas de producción. Aplican un borrado lógico en lugar de un borrado real.

### 3.1.3 Lanzar las migraciones pendientes (lanzar siempre en el contenedor web)

Una vez que hemos creado las migraciones el siguiente paso es ejecutarlas, de forma que se creen todos los elementos (fundamentalmente
tablas) en la base de datos. 

El conjunto de migraciones de la aplicación son los ficheros existentes en la ruta /database/migrations (ordenados por fecha). 

Para lanzar todas las migraciones *pendientes* utilizamos el siguiente comando:

        php artisan migrate

¿Por qué decimos *pendientes*? Porque obviamente en el desarrollo de una aplicación web el modelo de datos suele ser cambiante, y por tanto
será perfectamente normal ir añadiendo elementos (tablas) a la base de datos progresivamente.

¿Cómo sabe Laravel qué migraciones están pendientes de ser lanzadas sobre la base de datos? Lo indica la tabla **migrations** (ver migraciones por defecto). Esta tabla se crea automáticamente al lanzar la primera migración y contiene una traza de los ficheros de migración ejecutados.

### 3.1.4 Borrar y lanzar todas las migraciones (lanzar siempre en el contenedor web)

        php artisan migrate:fresh

Se eliminan todas las tablas y se vuelven a crear. Puede verse como una vuelta a los "datos de fábrica" de la base de datos.

        php artisan migrate:reset

Ejecuta el método down de cada migración. No se recomienda.

## 3.2 Datos iniciales

**Laravel** dispone de una herramienta para generar datos iniciales: un usuario, datos estáticos o maestros... Son los llamados *seeders*.

        php artisan make:seeder EstadoSeeder

Una vez lanzado el comando anterior se creará un nuevo fichero en la carpeta *database/seeders* que tendremos que completar con los datos que 
queremos añadir a la tabla correspondiente.

¿Cómo ejecutamos los seeders?

En primer lugar añadimos en el fichero *DatabaseSeeder* todos los seeders que queramos que sean ejecutados. Si no los añadimos aquí no se ejecutarán.

En segundo lugar lanzamos el siguiente comando:

        php artisan db:seed

El comando anterior ni elimina y recrea tablas ni elimina y recrea datos, sólo intenta insertarlos. Si queremos lanzar recrear la migración y lanzar los 
datos iniciales:

        php artisan migrate:fresh --seed

Si queremos ejecutar un seeder en concreto:

        php artisan db:seed --class=EstadoSeeder

## 3.3 Datos de prueba

**Laravel** dispone de una herramienta para generar datos de prueba (no confundir con los datos iniciales). Son los llamados *factories*.

Por ejemplo: queremos crear 20 tareas y 5 proyectos con datos falsos pero realistas.

        php artisan make:factory TareaFactory

El comando anterior creará el fichero */database/factories/TareaFactory*.

No existe un comando propio para lanzar los *factories*. Por ello vamos a lanzarlos conjuntamente con los *seeders*. Es decir, vamos a utilizar los
*seeders* tanto para crear datos iniciales como para ejecutar los *factories*. Siguiendo con el ejemplo anterior, necesitaremos un *TareaSeeder*.

## 3.4 Tinker

**Tinker** es un herramienta muy útil incluida en *Laravel*. Es una consola interactiva que permite "hablar" directamente con nuestra aplicación
*Laravel*:

        php artisan tinker

Puedes escribir código PHP y ver el resultado instantáneamente. Es un laboratorio en el cual podemos probar cualquier sentencia, especialmente
aquellas relativas a la inicialización de la base de datos (*seeders/factories*).

En nuestro entorno de desarrollo con contenedores puede haber algún problema a la hora de usar *Tinker*, casi todos relacionados con la escritura
en una carpeta. Esto ocurre porque nuestro contenedor web no tiene definida la variable de entorno HOME. Para ello podemos optar por una solución
rápida en el bash del contendor web:

        export HOME=/tmp

La solución anterior no persiste, es necesario ejecutar el comando siempre que levantemos el contendedor web. Si queremos que la solución persista
lo más apropiado es aplicarlo en el *environment* de la configuración del contenedor web.

# 4. MODELOS

Eloquent es el *ORM* de *Laravel*, es decir, es el componente que se encarga de realizar el mapeo objeto-relacional en nuestras aplicaciones.
El programador no tiene que programar consultas o actualizaciones en SQL, simplemente manipula los objetos y los obtiene o persiste utilizando
la sintaxis de *Eloquent*.

*Laravel* ya nos aporta de serie el modelo *User*, alineado con su migración. En secciones posteriores lo utilizaremos para autenticación. De momento
se recomienda no modificarlo.

## 4.1 Creación
El comando para la creación de un modelo es el siguiente:

        php artisan make:model Tarea

También podemos aglutinar en el mismo comando la creación de la migración (m), la creación de un controlador (c) y que el controlador sea 
resource (r)*:        
        
        php artisan make:model Tarea -mcr

Los controladores se verán con detalle en las siguientes secciones.

## 4.2 Estructura base

Los modelos extienden (heredan) la clase *Model*. Esta clase proporciona atributos y métodos que nos facilitarán la integración con la base de datos y
con el resto de elementos del proyecto:

* *$timestamps* para indicar que el modelo no maneja marcas de tiempo automáticas.
* *$table* para indicar específicamente el nombre de la tabla asociada (si no queremos que lo haga *Laravel* de forma automática)
* Métodos para manipulación de objetos:
    * Proyecto::create([...])
    * Proyecto::all()
    * Proyecto::find($id)
    * Proyecto::where('codigo', 'P001')->get()
    * $proyecto = Proyecto::find(1); $proyecto->descripcion = 'Nuevo'; $proyecto->save();
    * $proyecto = Proyecto::find(1); $proyecto->delete();

Hay que especificar si el modelo usa *factories* (use HasFactory) y/o *softDeletes* (use SoftDeletes).

## 4.3 Relaciones

Para cada modelo es necesario especificar sus relaciones, creando las funciones necesarias indicando *hasMany*, *hasOne* o *belongsTo*.
En realidad no es del todo necesario, sólo sería estrictamente necesario definir aquellas relaciones que vamos a usar realmente.

¡¡¡Cuidado al acceder a atributos de modelos relacionados que pueden ser nulos!!!

¿Podrían ocurrir cargas recursivas? Una tarea carga su tipo de tarea, que a su vez carga las las tareas relacionadas, que a su vez cargan
sus tipos de tarea, que a su vez...
En principio la respuesta es NO, ya que *Laravel* sólo carga las relaciones cuando queremos acceder a ellas (*Lazy Loading*) o bien cuando se lo indicamos con *with* (*Eager Loading*).

## 4.4 Asignación masiva

Entendemos por asignación masiva crear o actualizar un modelo pasando un array de datos "de golpe".

Indicamos en el atributo protegido *fillable* qué campos provenientes de formularios pueden ser actualizados en la base de datos.

¿No serán siempre todos los campos? No, imagina que la tabla posee algunas columnas que no deben ser actualizadas mediante formularios
por "pertenecer" a otras aplicaciones o porque se insertan/actualizan mediante otros procesos.

Como regla general nunca se incluyen los siguientes campos (ya los maneja *Laravel* internamente):
* id
* created_at
* updated_at
* deleted_at (soft deletes)

## 4.5 Casts

Los *casts* transforman automáticamente los valores de los campos de la base de datos al tipo que queramos cuando accedemos a ellos 
desde el modelo.

Un caso típico podría ser un atributo booleano. En la base de datos no existe este tipo, pero en el código sí queremos tratarlo como
un booleano.

## 4.6 Scopes

Los *Scopes* son la forma que tiene *Laravel* para implementar consultas reutilizables y encadenables.

Se implementan como cualquier otro método, con algunas puntualizaciones:
* El nombre del método siempre empieza por scopeXXX (*Laravel* quita automáticamente el prefijo para usarlos).
* El primer parámetro del método siempre es *$query*. El resto de parámetros a continuación.
* Siempre devuelven el resultado de la consulta.

## 4.7 Métodos de dominio

## 4.8 Atributos calculados

## 4.9 NUNCA EN UN MODELO
* Permisos
* Validaciones
* Tratamiento de la petición (request)




Los modelos están estrechamente relacionados con las migraciones. 

Los campos timestamp Laravel los rellena automáticamente. Si la tabla no los tiene hay que indicarlo:
class Estado extends Model
{
    public $timestamps = false;
}

Softdeletes

use Illuminate\Database\Eloquent\SoftDeletes;

class Tarea extends Model
{
    use SoftDeletes;
}

Delete hace borrado lógico
Select no trae filas borradas lógicamente

# 5. CONTROLADORES

## 5.1 Creación

El comando para crear un controlador es el siguiente:

        php artisan make:controller ProyectoController --resource

Se crea en app/Http/Controllers/ProyectoController.php.

¿En qué afecta el parámetro --resource? Con él le indicamos a *Laravel* que el controlador que estamos creado es un controlador de
recursos, es decir, un típico controlador REST. Gracias a ello *Laravel* genera de forma automática las cabeceras de los métodos:

* index (listado de proyectos)
* create (formulario para nuevo proyecto)
* store (creación de nuevo proyecto)
* show (formulario para visualización de proyecto)
* edit (formulario para edición de proyecto)
* update (actualización de proyecto existente)
* destroy (eliminación de proyecto)

**Sólo** genera las cabeceras de los métodos, no hace nada de lo siguiente: 

* Lógica
* Rutas
* Autorizaciones
* Validaciones
* Redirecciones
* Mensajes de éxito/error
* Relaciones entre modelos (servicios)
* Vistas

¿Cuáles son los controladores que no tienen una típica estructura REST? Por ejemplo el controlador del Dashboard o de la Home (no hay un modelo detrás). En estos controladores *Laravel* no genera ninguna cabecera de ningún método.

Otro buen ejemplo es el controlador para autenticación (lo veremos más adelante).

## 5.2 Rutas de controladores

Una ruta no es más que un punto de entrada a nuestra aplicación. Más concretamente, es la "dirección" de alguno de los métodos de algún
controlador. Por tanto, cada método de cada controlador debe estar enrutado correctamente para que pueda ser accesible.

El objetivo es que si escribimos en el navegador http://XXX/proyectos aparezca una tabla con todos los proyectos.
Para eso necesitamos tres piezas:
* Ruta → conecta la URL con el método correspondiente del controlador.
* Controlador → implementa la lógica de la operación, obtiene los datos e indica cuál es la vista que tiene que mostrarse.
* Vista (Blade) → muestra la tabla con todos los proyectos en HTML.

El fichero que controla las rutas en *Laravel* es routes/web.php.

### 5.2.1 Controladores recurso (resource controller)

Enrutar un controlador recurso es muy sencillo, ya que *Laravel* sabe cómo hacerlo. Sólo hay que añadir la línea correspondiente al controlador:

        Route::resource('proyectos', ProyectoController::class);

Sólo con esto, ya se crean las rutas para todos los puntos de entrada (métodos) del controlador:


|       **Ruta**                        |       **Método HTTP** |       **Acción**      |
|       --------                        |       --------------- |       ----------      |
|       /proyectos                      |       GET             |       index           |
|       /proyectos/create               |       GET             |       create          |
|       /proyectos                      |       POST            |       store           |
|       /proyectos/{proyecto}           |       GET             |       show            |
|       /proyectos/{proyecto}/edit      |       GET             |       edit            |
|       /proyectos/{proyecto}           |       PUT/PATCH       |       update          |
|       /proyectos/{proyecto}           |       DELETE          |       destroy         |

### 5.2.2 Otros controladores

Para los controladores que no son recurso las rutas se definen individualmente:

        Route::get('/', [HomeController::class, 'index']);
        
        Route::get('/about', [HomeController::class, 'about']);
        
        Route::get('/contact', [HomeController::class, 'contact']);

### 5.2.3 Nombres de rutas

¿Qué significa darle un nombre a una ruta? *Laravel* permite identificar rutas no solo por su URL, sino también por un nombre interno.

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Aquí la URL es / pero su nombre es "dashboard".

Esto es de suma importancia: 

* Permite generar rutas identificándolas por nombre en lugar de escribirlas completas, por ejemplo en las vistas:
        `<a href="{{ route('dashboard') }}">Ir al Dashboard</a>`

* Redirecciones más limpias en el controlador:
        return redirect()->route('dashboard');

* Robustez ante los posibles cambios de rutas: / => /inicio. Si usamos la ruta por nombre no tendremos que realizar ningún cambio.

* Compatible y consistente con los diferentes modos de autenticación incluidos en *Laravel*: Breeze, Jetstream, Filament, etc.

## 5.3 Algunas situaciones extrañas durante la creación de controladores recurso

Hay ocasiones en las que al ejecutar el comando de creación de controlador las cabeceras de los métodos generados no incluyen el modelo,
sino sólo el identificador: 
        
        public function update(Request $request, Proyecto $proyecto)

        public function update(Request $request, $id)

Esto ocurre porque durante la creación del controlador no se ha tenido en cuenta el modelo (*Laravel* no es capaz de hacerlo por sí mismo).
Para que esto no ocurra añadimos el parámetro que refleja el modelo:

        php artisan make:controller ProyectoController --resource --model=Proyecto

Si no tenemos en cuenta el modelo, el enrutado será un poco diferente:

|       **Ruta**                  |       **Método HTTP** |       **Acción**      |
|       --------                  |       --------------- |       ----------      |
|       /proyectos                |       GET             |       index           |
|       /proyectos/create         |       GET             |       create          |
|       /proyectos                |       POST            |       store           |
|       /proyectos/{id}           |       GET             |       show            |
|       /proyectos/{id}/edit      |       GET             |       edit            |
|       /proyectos/{id}           |       PUT/PATCH       |       update          |
|       /proyectos/{id}           |       DELETE          |       destroy         |

IMPORTANTE: deben estar en consonancia el enrutado con el método del controlador: o los dos *id* o los dos *Proyecto* (preferido).

# 6. VISTAS

COMPLETAR

# 7. AUTENTICACIÓN

Para manejar la autenticación en una aplicación web PHP sin *Laravel* hemos eran necesarios los siguientes elementos:

* Una tabla de para almacenar los usuarios en la base de datos.
* Un formulario (vista) para hacer el login de usuario.
* Un controlador especial para gestionar la autenticación, p. ej. *AuthController*, con métodos para mostrar el formulario y realizar las operaciones
de login y logout.
* Uso de la sesión para guardar el usuario autenticado.
* Protección de los métodos de los controladores que requieran autenticación.

Con *Laravel* todo se simplifica con el uso de dos nuevos elementos:

* Middleware
* Auth

## 7.1. Tabla *users*

Como ya sabemos, *Laravel* por defecto incluye en la migración una tabla para la gestión de usuarios. En principio podemos seguir utilizando
esa sin ninguna modificación.

## 7.2. El modelo *User*

También proporcionado por *Laravel*. Al extender *Authenticable* le indicamos a *Laravel* que el modelo puede autenticarse, es decir, qué modelo
se va a encargar de la autenticación.

## 7.3. La clase para autenticación *Auth*

Puede verse como el sustituto del manejo de la sesión. Con los métodos proporcionados por esta clase se realizan las siguientes acciones:

* Auth::attempt($credentials);   // login
* Auth::check();                 // ¿hay usuario?
* Auth::user();                  // usuario actual
* Auth::id();                    // id del usuario
* Auth::logout();                // logout

Estos métodos ya están implementados, sólo los usamos. Internamente hacen cosas como:

* Búsqueda del usuario.
* Verificación de password.
* Gestión de sesión.
* Regeneración de sesión.
* Persistencia.
* ...

## 7.4. El controlador *AuthController* y su enrutado

Una vez creado el controlador será necesario añadirle los métodos para mostrar el formulario de login y para realizar el login/logout (utilizando
los métodos ofrecidos por la clase *Auth*).

        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

        Route::post('/login', [AuthController::class, 'login']);

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

## 7.5. Formulario (vista) de inicio de sesión

Es necesario implementar una vista que recoja identificador y password y realice la llamada POST a login.

# 8. MIDDLEWARES

Un *middleware* en *Laravel* es un capa que se superpone a los controladores, con el propósito de permitir o no el acceso a los mismos. 

Ejemplo típico: no puedes acceder a este controlador (o a alguna de sus operaciones) si no estás autenticado en la aplicación.

Entre otros, *Laravel* proporciona el middleware *auth*, que protege el acceso a los controladores frente a accesos no autenticados y redirige de forma automática al formulario de inicio de sesión. Algunos otros son los siguientes:

* guest -> sólo usuarios no autenticados
* verified -> sólo email verificado
* throttle  -> limitación de peticiones
* password.confirm -> pedir contraseña para operaciones sensibles
* ...

Adicionalmente, *Laravel* permite implementar nuestros propios middlewares, tendiendo siempre presente que sólo deben emplearse para realizar comprobaciones
o acciones genéricas, los aspectos más "transversales" de las aplicaciones. No es buena práctica utilizar un middleware para algún propósito o validación específica, para ello disponemos de otros artefactos.

        php artisan make:middleware RoleMiddleware

Se crea el fichero app/Http/Middleware/RoleMiddleware.php.

## 8.1. Middleware aplicado en rutas

        Route::middleware('auth')->group(function () {
                // Controladores completos
                Route::get('/proyectos', ...);
                Route::get('/tareas', ...);

                // Operaciones específicas
                Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        });

El código anterior consigue que antes de enrutar a las operaciones de los controladores indicados se verifica si el usuario está autenticado. En caso
negativo redirige al formulario de inicio de sesión.

## 8.2. Middleware aplicado en controladores

        class ProyectoController extends Controller
        {
                public function __construct()
                {
                        // Controlador completo
                        $this->middleware('auth');

                        // Operaciones específicas
                        $this->middleware('auth')->only(['store', 'update']);
                }
        }

El código anterior consigue que durante la construcción del controlador se verifica si el usuario está autenticado. En caso negativo redirige al formulario de inicio de sesión. Funcionalmente es totalmente equivalente al anterior, 

# 8. VALIDACIONES

Una de las cuestiones más importantes en el desarrollo de una aplicación web son las reglas de validación. Estas reglas van desde
las más simples (tamaño de un campo, si es o no requerido...) a otras más complejas que involucran lógica de negocio (qué rol de usuario
puede realizar qué tareas, validaciones que involucran a varios campos...).

## 8.1. Validaciones individuales de campos

Este tipo de validaciones suelen ser un espejo tanto de la configuración de los campos en la base de datos como de los formularios en los que el usuario introduce los datos: tipo, tamaño, si es requerido, si admite nulos, etc.

Si son muy simples a vces se incluyen en el propio controlador de la operación, pero no es buena práctica. *Laravel* nos proporciona un elemento específico para realizar este tipo validaciones, los llamados *FormRequests*. Como su propio nombre indica, se trata de validar
los datos provenientes de un formulario (vista) a través de la petición (request).

Se crean ejecutando el siguiente comando:

       php artisan make:request StoreTareaRequest

Se creará el fichero en app/Http/Requests/StoreTareaRequest.php.
Rule::unique()
Rule::exists()
Rule::in()
Rule::requiredIf()

Validaciones dependientes



Utilizar Rule:: en validaciones algo más complejas



COMPLETAR




REVISAR INICIO


1) ¿Dónde validar? — Opciones y cuándo usar cada una

Validación inline en el controlador ($request->validate([...]))
        Rápido para reglas simples y prototipos.
        Fácil, pero ensucia el controlador si las reglas crecen.

FormRequest (recomendado) (php artisan make:request TaskRequest)
        Clase dedicada: rules(), authorize(), messages() y attributes().
        Limpia controladores y permite reutilizar reglas.
        Permite lógica de autorización ligada a la validación (authorize()).

Validadores manuales (Validator::make(...))
        Útil para validaciones condicionales complejas o si quieres ejecutar la validación y decidir tú qué hacer.

Validación a nivel de modelo (no nativa en Eloquent)
        Mejor dejarla a FormRequest; en modelos puedes usar mutators, casts y events.

REVISAR FIN






RULES

Puedes usar un Rule::exists()->where(...):

use Illuminate\Validation\Rule;

'proyecto_id' => [
    'required',
    Rule::exists('proyectos', 'id')->where(function ($query) use ($userId) {
        $query->where('user_id', $userId);
    }),
],


O crear una regla personalizada con php artisan make:rule ProyectoPerteneceUsuario.







7) Lógica de negocio: ¿dónde ponerla?

Validaciones y autorizaciones simples → FormRequest (authorize()) y Policy.

Reglas complejas de negocio (ej. si cambio de estado, notificar, generar audit trail, actualizar otras tablas) → Service classes o Domain Services.

Ejemplo de Service:

app/Services/TaskService.php

class TaskService
{
    public function changeStatus(Task $task, string $newStatus, User $actor)
    {
        // reglas de negocio
        if ($task->estado === 'completada' && $newStatus !== 'completada') {
            throw new \Exception('No se puede reabrir una tarea completada.');
        }

        // transaction si hay múltiples operaciones
        \DB::transaction(function () use ($task, $newStatus, $actor) {
            $task->estado = $newStatus;
            $task->save();

            // p.ej. crear registro en audit_log
            // Audit::create([...])
        });
    }
}


Y lo usas en el controlador:

public function update(UpdateTaskRequest $request, Task $tarea, TaskService $service)
{
    $data = $request->validated();

    if (isset($data['estado'])) {
        $service->changeStatus($tarea, $data['estado'], auth()->user());
        // si el servicio lanza excepción, capturar y manejar
    } else {
        $tarea->update($data);
    }

    return back();
}


Ventaja: controlador queda fino y legible; la lógica compleja vive en una clase dedicada y testeable.




















8) Políticas (Policies) para autorización por tarea

Crear policy:

php artisan make:policy TaskPolicy --model=Task


En TaskPolicy:

public function update(User $user, Task $task)
{
    // p.ej. solo puede editar si es el propietario o es admin
    return $user->id === $task->usuario_id || $user->rol === 'admin';
}


Registrar policy en AuthServiceProvider (si no se registra automáticamente):

protected $policies = [
    Task::class => TaskPolicy::class,
];


Usarlo en controlador:

$this->authorize('update', $tarea);


O en vistas blade:

@can('update', $tarea)
    <a href="{{ route('tareas.edit', $tarea) }}">Editar</a>
@endcan

9) Transacciones y consistencia

Si una operación implica varias escrituras (p.ej. actualizar tarea + notificar + actualizar contador), envuélvelas en DB::transaction() para asegurar consistencia.

\DB::transaction(function () use ($tarea, $data) {
    $tarea->update($data);
    // otras operaciones
});





Sí, estás totalmente en lo cierto 👍
Resumen breve y claro:

✅ Middleware de rol
Útil solo para:

permitir o bloquear el acceso a una ruta/controlador según el rol

ej.: “solo admins pueden entrar aquí”

❌ No es buena solución cuando:

el mismo controlador debe comportarse distinto según el rol

ej.: admin ve todo, usuario ve solo lo suyo

En esos casos, lo correcto es:

lógica en el controlador

policies para autorizar acciones

o métodos del modelo (isAdmin(), etc.)

👉 Regla mental:

Middleware = acceso
Policy / lógica = comportamiento