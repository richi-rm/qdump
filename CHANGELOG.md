# VARDEBUG CHANGELOG

Tags: Added | Changed | Deprecated | Fixed | Removed | Security | Yanked

## [v0.4.2-alpha] - 2021-11-19

### Added

 - Añadidas a VarDebugger las opciones +-priv, +-prot, +-pub, +-all.
 - Añadido especificador de acceso para las propiedades y los métodos de los
   objetos, así como su formato.
 - Añadido método getUserName() a Context.
 - Añadida opción file y file: a VarDebugger, y adaptado FileWriter.

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
