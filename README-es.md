<a href="README.md">English version</a> | <a href="README-es.md">Versión en español</a>

# RDebug: herramienta para depurar variables o expresiones PHP

¿Te ha pasado alguna vez que has querido depurar algo pero var_dump() se queda colgado? ¿O necesitas depurar rápidamente algo en producción pero por supuesto que no quieres que la depuración salga en los navegadores de los usuarios?

Depura con facilidad aplicaciones web PHP construidas con cualquier framework o CMS: si var_dump() o var_export() no te dan lo que necesitas, o si no puedes o no deseas usar xdebug, ésta puede ser tu herramienta.

**RDebug** es una librería muy sencilla y fácil de usar que te permita volcar el contenido de expresiones o variables PHP en un formato legible. Admite salida en varios formatos: texto plano, html, comentario html o texto ANSI en color. También te permite enviar la salida al navegador, a la consola, o a archivos.

Puedes pensar en RDebug como en un **sustituto de var_dump()**, pero más versátil.

Instalación
-----------
Para instalar RDebug en tu proyecto puedes hacerlo con **composer**:

```bash
$ composer require cachito/rdebug
```

Uso básico: capture()
---------------------
Para empezar a depurar lo primero que tienes que hacer es crear un **objeto RDebug** (normalmente en el index.php de tu proyecto), y para hacer capturas de variables o expresiones, llamar a **capture()** en el sitio en donde desees depurar, tal y como llamarías a var_dump() o a var_export().

```php
use Cachito\RDebug\RDebug;

$rdebug = new RDebug();
...
$rdebug->capture(<variable o expresión>);
```

You can capture any variable, as well as any valid PHP expression.

Sesiones de depuración
----------------------
En el contexto de una aplicación web PHP llamamos sesión de depuración a lo que se inspecciona en una petición HTTP. Podemos crear un objeto RDebug al principio de nuestro **index.php** y después establecer varios puntos de inspección en las partes del código que deseemos.

index.php:

```php
use Cachito\RDebug\RDebug;

$rdebug = new RDebug();
```

otro-archivo.php:

```php
$rdebug->capture($var1);
...
$rdebug->capture($var2);
...
$rdebug->capture($var3);
```

RDebug capturará el valor de las variables y les asignará un orden numérico (el orden en que se capturan). Esto puede ser útil si no conocemos el flujo de una llamada determinada a nuestra aplicación y queremos averiguarlo estableciendo puntos de inspección en distintos sitios.

Formatos de salida
------------------
Cuando se crea un objeto RDebug sin parámetros en el constructor la salida de capture() es la salida estándar de PHP (**STDOUT**) y el formato es de tipo **html-comment** (comentario HTML). Es decir, cada vez que se llama a capture(), RDebug volcará el valor de la variable capturada en un comentario HTML, al estilo de lo que hace, por ejemplo, Drupal para depurar las plantillas twig (debug de twig.config establecido a true).

RDebug maneja actualmente 4 formatos de salida: **ansi** (texto ANSI colereado), **html** (formato HTML), **html-comment** (comentario HTML) y **plain-text** (texto plano). El formato de salida se establece en el primer parámetro del constructor. Ejemplo:

```php
$rdebug = new RDebug('plain-text');
```

Tipos de salida
---------------
RDebug vuelca por defecto su salida a la salida estándar de PHP, tal y como hace var_dump(), pero también puede volcarla a un fichero. Para establecer el tipo de salida de RDebug usamos el segundo parámetro del constructor especificando **file** (salida a fichero) o **stdout** (salida estándar). Ejemplo:

```php
$rdebug = new RDebug('ansi', 'file');
...
$rdebug->capture($var1);
...
$rdebug->capture($var2);
```

En el ejemplo anterior, todas las llamadas a capture() escribirán la salida de las inspecciones en formato ANSI y a ficheros. Los ficheros de salida se explican en el siguiente apartado.

Salida a ficheros
-----------------
En el ejemplo anterior todas las llamadas a capture() posteriores a la creación del objeto RDebug escriben la salida de las inspecciones en ficheros llamados **rdebug.x.y.z**, siendo **x** el número de sesión de depuración (que se incrementará tantas veces como recarguemos la página que estemos depurando), **y** un token aleatorio necesario para evitar problemas con peticiones concurrentes y **z** la extensión del fichero.

Por defecto las capturas se guardan en la carpeta **/tmp** del sistema, pero esto se puede cambiar indicando la carpeta de captura en el tercer parámetro del constructor. Ejemplo:

```php
$rdebug = new RDebug('ansi', 'file', '/home/user/rdebug-captures');
```

En el ejemplo anterior estamos diciendo que queremos capturar en formato ANSI y enviar las capturas al directorio /home/user/rdebug-captures. El directorio tiene que existir y tener permisos de escritura; si no, RDebug no escribirá nada y no mostrará ningún error.

Los ficheros de salida de RDebug tienen esta forma:

```
-rw-rw-r-- 1 user    group    499 nov 12 13:36 rdebug.1.534078.txt
-rw-rw-r-- 1 user    group   1212 nov 12 13:46 rdebug.2.441574.ansi
-rw-rw-r-- 1 user    group   4532 nov 12 13:57 rdebug.3.686799.html
-rw-rw-r-- 1 user    group    761 nov 12 14:02 rdebug.4.830296.txt
```

Depuración de arrays y objetos
------------------------------
Los arrays y los objetos se depuran igual que cualquier otra variable (capture() acepta cualquier expresión PHP), pero si sabemos con certeza que una variable es de tipo array es mejor depurarla con el método **captureByRef()**, en el que pasamos el array por referencia. Podemos depurar arrays igualmente con capture(), pero se ha añadido el método captureByRef() para cuando estemos depurando arrays que puedan ser recursivos.

Por otro lado, RDebug detecta también **ciclos (referencias circulares)** tanto en arrays como en objetos. Ejemplos:

```php
$array = [0, &$array];
$rdebug->captureByRef($array);
...
$object = new stdClass();
$object->prop = $object;
$rdebug->capture($object);
```

Por defecto RDebug no muestra los métodos públicos de los objetos inspeccionados, sólo las propiedades públicas. Si queremos que RDebug muestre también los métodos públicos debemos indicarlo llamanda al método **addMethods()**. Ejemplo:

```php
$rdebug = new RDebug();
$rdebug->addMethods();
...
$object = new SomeClass();
$rdebug->capture($object);
```

Ejemplo de salida de RDebug
---------------------------
Se muestra a continuación un ejemplo de cómo es la salida de RDebug correspondiente a este fragmento de código:

```php
$rdebug = new RDebug('plain-text');
$rdebug->addMethods();

$array = ['peanut', 'crocodile', 'sky', &$array];

$rdebug->captureByRef($array);

class SomeClass {
   public $prop_1;
   public $prop_2;
   public function func_1(){}
   public function func_2(){}
}

$object = new SomeClass();
$object->prop_1 = $object;

$rdebug->capture($object);
```

La salida de RDebug, en formato plain-text (texto plano), correspondiente al ejemplo anterior, es la siguiente:

```
---
PHP 7.4.3 cli (Linux)
---
1) /home/user/projects/some-project/test.php(12) 0.000763 s
array(4)
   [0] => string(6) "peanut"
   [1] => string(9) "cocodrile"
   [2] => string(5) "table"
   [3] => array(4) (CYCLE array)

2) /home/user/projects/some-project/test.php(23) 0.000808 s
object SomeClass #7 /home/user/projects/some-project/test.php(14)
   ->prop_1 : object SomeClass #7 (CYCLE object)
   ->prop_2 : null
   ->func_1()
   ->func_2()
```

La primera línea de la salida es información del entorno en el que se está ejecutando PHP. Los siguientes apartados [1) y 2)] corresponden a cada una de las capturas. En cada captura se muestra:
- El número de captura.
- El fichero y la línea en los que está la llamada a capture().
- El tiempo en segundos transcurrido desde la creación del objeto RDebug hasta la llamada a capture().
- **El volcado de la variable o expresión** (en el ejemplo anterior hay dos capturas: en la primera se muestra un array con una referencia a sí mismo en el cuarto elemento, y en la segunda captura se muestra un objeto con una referencia a sí mismo en la propiedad prop_1).

Las salidas en formato html-comment es muy similar a la salida plain-text, pero con html-comment la salida está dentro de un comentario HTML. La salida ansi es igual a la salida de tipo plain-text, salvo que los tipos y los valores de las variables se muestran con colores, lo que facilita la comprensión de lo que se está depurando.

Más ejemplos
------------
Puedes encontrar más ejemplos en la carpeta **examples/** de este paquete.

Pruebas
-------
RDebug ha sido probado en PHP 7 y Ubuntu Linux (no ha sido probado en Windows, Mac).

Sugerencias
-----------
Eres libre de enviarme comentarios, sugerencias, ideas, bugs... (autor de RDebug: Ricardo Ruiz Martínez <richiruizmartinez@gmail.com>)

Garantía y licencia
-------------------
Esta librería se distribuye sin ninguna garantía. La licencia está en el archivo <a href="https://github.com/richiruizmartinez/rdebug/blob/main/LICENSE">LICENSE</a>.
