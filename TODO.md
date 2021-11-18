# TODO


## Future major features

 - Usar ReflectionClass en lugar de lo que se está usando ahora, para ver los elementos public, protected y private
   (que se muestre el valor de las propiedades y su accesibilidad, de los métodos sólo su accesibilidad, también
    si son estáticos o no).
 - [x] Nuevo renderer: http-header-json, salida de un json a una cabecera HTTP. [NO]


## Future medium features

 - Que se puedan elegir los colores de entre unos básicos (green, blue-bright, etc.).


## Future minor features

 - Si sapi == cli entonces default = "ansi,stdout", si sapi != cli entonces default = "html-comment,stdout".
 - Puedo meter también que si la petición es ajax, default = "ansi,file"
 - Opción 'respect-http-headers', no envía si es stdout y no se han enviado las cabeceras (para evitar warnings).
 - Volver a poner lo de la profundidad máxima, en arrays y en objetos (ppción max-depth en el contructor de VarDebugger).
 - Que la carpeta por defecto sea /tmp/vardebug/user (que se cree si no existe).
 - [x] Nuevo outputwriter: NullWriter
 - [x] Añadir métodos var_export() y var_export_byref() [NO]


## Changes

 - [x] addClassMethods() debe estar en el constructor de VarDebugger, es más fácil para el usuario.
 - [x] Corregir el constructor de los OutputWriter (no adaptarlo a las necesidades de FileWriter).
 - [x] Opción "no-dump" en vez de "null".
 - [x] Los métodos capture() pasan a llamarse dump() y dumpByRef().
 - [x] Los métodos addObjectMethods() se deben llamar addClassMethods().


## Bugs

 - Ver cómo arreglar el error del cierre de strings en todos los modos


## Tasks

 - Pensar qué hacer con los objetos grandes: strings, arrays y objetos
 - Pensar qué hacer con los caracteres < 32 de los strings
 - Core es quien se encarga de recolectar lo que diga el usuario y Render
   renderiza lo devuelto por Core sin filtro alguno.
 - Pensar cómo el usuario puede decir lo que quiere ver de los objetos:
   +ppub,+ppriv,-pprot,+mpub,-mprot
   Por defecto: +ppub,+mpub
 - Repasar los estilos y los colores CSS del modo 'html'
 - Cambio de nombre: faltan por cambiar los README, los examples y los tests.
 - Repasar examples/ y tests/
 - Investigar los distintos tipos de licencias.
 - [x] Hacer un CHANGELOG.md
 - [x] Nuevo repo en github: vardebug.
 - [x] Subir el código al nuevo repo vardebug.
 - [x] Cambio de nombre del paquete: de RDebug a VarDebug.
 

## Tests

 - Construir tests unitarios y ejecutarlos.
 - Hacer test funcionales de todas las combinaciones.
 - Probar con nuevos objetos capturas concurrentes con salida a 'file'.
 - [x] Comprobar si actualmente se obtienen los métodos estáticos.


## README.md

 - Sección: "A quién va dirigida esta herramienta".
 - Abrir nueva sección "null writer", con sus posibles usos.
 - Documentar que también saca los métodos estáticos, pero sólo los públicos.
 - Poner en la ayuda una sección que sea: "Características futuras".
 - Poner en la documentación que actualmente sólo devuelve los elementos públicos.
 - Nueva sección: instalación manual en lugar de con composer.
 - dumpByRef es una de las últimas secciones de ayuda, y explicar por qué (el 99% de los casos no necesitarás...).
 - Sección "verbose".
 - Sección "Múltiples objetos VarDebugger".
 - Meter casos de uso:
   - Depuración de aplicaciones PHP en el lado del servidor:
      - PHP de servidor
      - Twig
      - Flujos ajax
      - Depurar en PRE o en PRO
   - Depuración de scripts PHP cli.
 - Crear índice de contenidos.
 - [x] Descripción del paquete: Enhanced var_dump().


## Publicity

 - Cuando estén construidos y ejecutados los tests unitarios y funcionales crear una release (1.0.1-stable) sin beta
   ni alpha, porque beta o alpha puede echar para atrás.
 - Linkedin: PHP Developers.
 - Postear en PHP community.
 - Postear en Nateevo.
