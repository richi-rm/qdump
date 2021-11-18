# VARDEBUG CHANGELOG

Tags: Added | Changed | Deprecated | Fixed | Removed | Security | Yanked

## [v0.4.1-alpha] - 2021-11-18

### Added

 - Añadidas opciones +mpriv, -mpriv, +mprot, -mprot, +mpub, -mpub, +ppriv,
   -ppriv, +pprot, -pprot, +ppub, -ppub.
 - Añadido a Core la configuración para mostrar métodos y/o propiedades de todos
   los tipos de accesibilidad. De momento esto sólo funciona (en inspect()) con
   los métodos públicos y las propiedades públicas.

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
