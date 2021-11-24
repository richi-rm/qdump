# VARDEBUG CHANGELOG

Tags: Added | Changed | Deprecated | Fixed | Removed | Security | Yanked


## [v0.4.6-alpha] - 2021-11-24

### Changed

 - Cambiado el nombre del renderer ansi por color-text y cambiada la clase
   AnsiRenderer por AnsiTextRenderer.


## [v0.4.5-alpha] - 2021-11-23

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


## [v0.4.4-alpha] - 2021-11-22

### Added

 - Añadido a Core saneado de caracteres UTF-8 mal formados en las cadenas.


## [v0.4.3-alpha] - 2021-11-21

### Added

 - Añadido a Core acortamiento y formateado de cadenas.

### Changed

 - Trasladado de Core a AbstractRenderer el acortamiento y formateado de
   cadenas.


## [v0.4.2-alpha] - 2021-11-19

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


## [v0.4.1-alpha] - 2021-11-18

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


## [v0.4.0-alpha] - 2021-11-17

### Added

 - Añadido null writer.

### Changed

 - Cambiado addObjectMethods() por addClassMethods().
 - Cambiados capture() y captureByRef() por dump() y dumpByRef().
