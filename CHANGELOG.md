# VARDEBUG CHANGELOG

Tags: Added | Changed | Deprecated | Fixed | Removed | Security | Yanked


## [v0.5.0-prealpha] - 2021-12-07

### Added

 - Añadido soporte para enums, propiedades readonly y resources ids (PHP 8).


## [v0.4.14-prealpha] - 2021-12-05

### Fixed

 - Sorteado un segmentation fault de PHP que no permite obtener el tipo de las
   propiedades dinámicas.
 - Mejorada la inspección de propiedades dinámicas en PHP 7.4 y 8.1.


## [v0.4.13-prealpha] - 2021-12-04

### Added

 - Añadido soporte para propiedades dinámicas.

### Changed

 - Cambiada la opción +arrays por expand-arrays.
 - Mejorado el renderizado de arrays.
 - Renombrada la opción color-text por ansi.

### Removed

 - Elimnada la oción -arrays
 - Eliminado el método dumpByRef()


## [v0.4.12-prealpha] - 2021-12-02

### Added
 - Añadidas opciones +arrays, -arrays, para expandir o no los arrays a la
   salida.

### Changed

 - Actualizados los sufijos usados por BasicRenderer.
 - Salida por defecto limitada: expand-arrays false, max-length: 50


## [v0.4.11-prealpha] - 2021-11-30

### Added

 - Core distingue los métodos que devuelven referencias.
 - Añadidos a los parámetros sus valores por defecto.


## [v0.4.10-prealpha] - 2021-11-29

### Added

 - Añadido renderizado de constantes de clase a BasicRenderer.


## [v0.4.9-prealpha] - 2021-11-28

### Added

 - Añadida a Core inspección de constantes y de propiedades y métodos private y
   protected.

### Changed

 - Movidos todos los renderers (menos ConsoleLogJsonRenderer) a BasicRenderer.

### Removed

 - Eliminadas las opciones +-all, +-priv, +-prot, +-pub, +-privm, +-privp,
   +-protm, +-protp, +-pubm, +-pubp.


## [v0.4.7-prealpha] - 2021-11-25

### Added

 - Añadido el formateador de cadenas BytesFormatter.
 - Añadido el formateador de cadenas JsonFormatter.
 - Añadido el formateador de cadenas ISO88591Formatter.

### Changed

 - Cambiadas las opciones hex-lower y hex-upper por hexlc y hexuc.
 - Refactorizados AsciiFormatter y UTF8Formatter.

### Removed

 - Eliminada la opción max-length. Ahora se puede especificar una longitud
   máxima de cadenas indicando sólo el número.


## [v0.4.6-prealpha] - 2021-11-24

### Added

 - Añadidos los formateadores de cadenas AsciiFormatter y UTF8Formatter.

### Changed

 - Cambiado el nombre del renderer ansi por color-text y cambiada la clase
   AnsiRenderer por AnsiTextRenderer.
 - Refactorizada la configuración de VarDebugger.
 - Ahora no se usa bin2hex() en la opción binary, sino una función manual para
   que los bytes estén separados por espacios en blanco.


## [v0.4.5-prealpha] - 2021-11-23

### Added

 - Añadido acortado de strings a ConsoleLogJsonRenderer.
 - Añadido soporte para representar el carácter \u007f en
   ConsoleLogJsonRenderer.
 - Añadida la posibilidad de que se muestren las cadenas como secuencias
   hexadecimales (propiedad hex-strings).

### Changed

 - Cambiado el nombre de la propiedad max-strlen a max-vistrlen.
 - Cambiada propiedad hex-strings por binary, y refactorizado el código.
 - Propiedad max-vistrlen por defecto a -1 (sin límite).
 - Cambiado el nombre de la propiedad max-vistrlen a max-length.


## [v0.4.4-prealpha] - 2021-11-22

### Added

 - Añadido a Core saneado de caracteres UTF-8 mal formados en las cadenas.


## [v0.4.3-prealpha] - 2021-11-21

### Added

 - Añadido a Core acortamiento y formateado de cadenas.

### Changed

 - Trasladado de Core a AbstractRenderer el acortamiento y formateado de
   cadenas.


## [v0.4.2-prealpha] - 2021-11-19

### Added

 - Añadidas a VarDebugger las opciones +-priv, +-prot, +-pub, +-all.
 - Añadido especificador de acceso para las propiedades y los métodos de los
   objetos, así como su formato.
 - Añadido método getUserName() a Context.
 - Añadida opción file y file: a VarDebugger, y adaptado FileWriter.
 - Añadida diferenciación de opciones por defecto dependiendo de si sapi es cli
   o no cli.

### Changed

 - Cambiadas las opciones +-mpriv, +-mprot, +-mpub, +-ppriv, +-pprot, +-ppub
   por: +-privm, +-privp, +-protm, +-protp, +-pubm, +-pubp.
 - Cambiado el namespace de las clases de Cachito a Cachitos.


## [v0.4.1-prealpha] - 2021-11-18

### Added

 - Añadidas a VarDebugger las opciones +-mpriv, +-mprot, +-mpub, +-ppriv,
   +-pprot, +-ppub.
 - Añadida a Core la configuración para mostrar métodos y/o propiedades de todos
   los tipos de accesibilidad. De momento sólo funciona (en inspect()) con los
   métodos públicos y las propiedades públicas.

### Changed

 - Cambiado identificador de Writer: 'null' por 'no-dump'.
 - Constructor de WriterInterface eliminado (los OutputWriter ya no tienen que
   adaptarse a FileWriter).


## [v0.4.0-prealpha] - 2021-11-17

### Added

 - Añadido null writer.

### Changed

 - Cambiado addObjectMethods() por addClassMethods().
 - Cambiados capture() y captureByRef() por dump() y dumpByRef().
