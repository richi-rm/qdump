# TODO


## Future major features

 - usar ReflectionClass en lugar de lo que se está usando ahora, para ver los elementos public, protected y private
   (que se muestre el valor de las propiedades y si su accesibilidad, de los métodos sólo su accesibilidad)
 - [x] nuevo renderer: http-header-json, salida de un json a una cabecera HTTP [NO]


## Future medium features

 - que se puedan elegir los colores de entre unos básicos (green, blue-bright)


## Future minor features

 - si sapi == cli entonces default ansi,stdout, si sapi != cli entonces default html-comment,stdout
 - los métodos capture() pasan a llamarse dump() y dump_byref() 
 - nuevo outputwriter: ReturnWriter
 - [x] añadir métodos var_export() y var_export_byref() [NO]
 - opción 'respect-http-headers', no envía si es stdout y no se han enviado las cabeceras (para evitar warnings)
 - volver a poner lo de la profundidad máxima, en arrays y en objetos, cuando se haga la lo de ReflectionClass
 - que la carpeta por defecto sea /tmp/vardebug/user <- que la cree


## Bugs

 - ver cómo arreglar el error del cierre de strings en todos los modos


## Tasks

 - cambio de nombre del paquete: RDebug -> VarDebug
 - hacer un CHANGELOG.md
 - subir el código al nuevo repo
 - investigar los distintos tipos de licencias
 - repasar los estilos y los colores CSS del modo 'html'
 - repasar examples/ y tests/
 - [x] nuevo repo en github: vardebug


## Tests

 - construir tests unitarios y ejecutarlos
 - hacer test funcionales de todas las combinaciones
 - probar con nuevos objetos capturas concurrentes con salida a 'file'


## Documentation

 - poner en la ayuda una sección que sea: características futuras
 - poner en la documentación que sólo vuelva los elementos públicos, ni protected, ni private
 - descripción del paquete: Enhanced var_dump()
 - nueva sección: si no quieres hacerlo con composer puedes hacerlo manualmente
 - var_dump_byref es una de las últimas secciones de ayuda, y explicar por qué (el 99% de los casos no necesitarás...)
 - sección "verbose"
 - sección "múltiples objetos vardebugger"
 - meter casos de uso (ver más abajo)
   - depurar de aplicaciones PHP en el lado del servidor:
      - php de servidor (facil)
      - twig
      - flujos ajax
      - depurar en pre o en pro
   - depuración de scripts PHP cli:
      - script php cli
 - crear índice de contenidos


## Publicity

 - cuando estén hechos los tests crear una release (1.0.1-stable) sin beta ni alpha, porque beta o alpha puede echar para atrás
 - linkedin: PHP Developers
 - postear en PHP community
 - postear en comentarios de PHP
 - postear en Nateevo
