# TODO


## Future major features

 - Usar ReflectionClass en lugar de lo que se está usando ahora, para ver los elementos public, protected y private
   (que se muestre el valor de las propiedades y su accesibilidad, de los métodos sólo su accesibilidad).
 - [x] Nuevo renderer: http-header-json, salida de un json a una cabecera HTTP. [NO]


## Future medium features

 - Que se puedan elegir los colores de entre unos básicos (green, blue-bright, etc.).


## Future minor features

 - Los métodos capture() pasan a llamarse dump() y dumpByRef().
 - Nuevo outputwriter: ReturnWriter
 - Si sapi == cli entonces default = "ansi,stdout", si sapi != cli entonces default = "html-comment,stdout".
 - Opción 'respect-http-headers', no envía si es stdout y no se han enviado las cabeceras (para evitar warnings).
 - Volver a poner lo de la profundidad máxima, en arrays y en objetos, cuando se haga la lo de ReflectionClass.
 - Que la carpeta por defecto sea /tmp/vardebug/user (que se cree si no existe).
 - [x] Añadir métodos var_export() y var_export_byref() [NO]


## Bugs

 - Ver cómo arreglar el error del cierre de strings en todos los modos


## Tasks

 - Hacer un CHANGELOG.md
 - Repasar los estilos y los colores CSS del modo 'html'
 - Cambio de nombre: faltan por cambiar los README, los examples y los tests.
 - Repasar examples/ y tests/
 - Investigar los distintos tipos de licencias.
 - [x] Nuevo repo en github: vardebug.
 - [x] Subir el código al nuevo repo vardebug.
 - [x] Cambio de nombre del paquete: de RDebug a VarDebug.
 

## Tests

 - Comprobar si actualmente se obtienen los métodos estáticos y documentar.
 - Construir tests unitarios y ejecutarlos.
 - Hacer test funcionales de todas las combinaciones.
 - Probar con nuevos objetos capturas concurrentes con salida a 'file'.


## README.md

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
