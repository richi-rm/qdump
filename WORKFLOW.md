# WORKFLOW


**Al corregir un bug o hacer un cambio menor:**

 - Hacer las modificaciones en develop
 - Probar
 - Si está todo ok cambiar la versión (patch) en composer.json
 - Mergear a main
 - Crear la tag de la nueva versión
 - Subir a github: main y la nueva tag


**Al añadir una característica nueva:**

 - Desarrollar en develop
 - Probar
 - Si está todo ok cambiar la versión (minor o major) en composer.json
 - Mergear a main
 - Crear la tag de la nueva versión
 - Subir a github: main y la nueva tag
 - Crear release en github
