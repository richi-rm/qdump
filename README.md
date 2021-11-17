<a href="README.md">English version</a> | <a href="README-es.md">Versión en español</a>

# RDebug: tool to debug PHP variables or expressions

Has it ever happened to you that you've wanted to debug something but var_dump() hangs? Or do you need to quickly debug something in production but of course you don't want debugging to appear in users' browsers?

Easily debug PHP web applications built with any framework or CMS: if var_dump() or var_export() don't give you what you need, or if you can't or don't want to use xdebug, this may be your tool.

**RDebug** is a very simple and easy-to-use library that allows you to dump the content of PHP expressions or variables in a readable format. Supports output in various formats: plain text, html, html comment, or ANSI color text. It also allows you to send the output to the browser, to the console, or to files.

You can think of RDebug as a **substitute for var_dump()**, but more versatile.

# Installation

To install RDebug in your project you can do it with **composer**:

```bash
$ composer require cachito/rdebug
```

Basic usage: capture()
----------------------
To start debugging, the first thing you have to do is create an **RDebug object** (usually in the index.php of your project), and to capture variables or expressions, call **capture()** in the place where you want to debug, such as you would call var_dump() or var_export().

```php
use Cachito\RDebug\RDebug;

$rdebug = new RDebug();
...
$rdebug->capture(<variable or expression>);
```

Debugging sessions
------------------
In the context of a PHP web application we call a debug session what is inspected in an HTTP request. We can create an RDebug object at the beginning of our **index.php** and then set various inspection points in the parts of the code we want.

index.php:

```php
use Cachito\RDebug\RDebug;

$rdebug = new RDebug();
```

another-file.php:

```php
$rdebug->capture($var1);
...
$rdebug->capture($var2);
...
$rdebug->capture($var3);
```

RDebug will capture the value of the variables and assign them a numerical order (the order in which they are captured). This can be useful if we do not know the flow of a particular call to our application and want to find out by setting inspection points at different sites.

Output formats
--------------
When an RDebug object is created with no parameters in the constructor the output of capture() is the standard PHP output (**STDOUT**) and the format is of type **html-comment** (HTML comment). That is, every time capture() is called, RDebug will dump the value of the captured variable into an HTML comment, much like what, for example, Drupal does to debug twig templates (debug of twig.config set to true).

RDebug currently handles 4 output formats: **ansi** (ANSI colored text), **html** (HTML format), **html-comment** (HTML comment) and **plain-text** (plain text). The output format is set in the first parameter of the constructor. Example:

```php
$rdebug = new RDebug('plain-text');
```

Output types
------------
RDebug dumps its output to PHP standard output by default, just like var_dump () does, but it can also dump it to a file. To set the output type of RDebug we use the second parameter of the constructor specifying **file** (output to file) or **stdout** (standard output). Example:

```php
$rdebug = new RDebug('ansi', 'file');
...
$rdebug->capture($var1);
...
$rdebug->capture($var2);
```

In the above example, all calls to capture() will write the output of the inspections in ANSI format and to files. The output files are explained in the next section.

Output to files
---------------
In the previous example, all the calls to capture() after the creation of the RDebug object write the output of the inspections in files called **rdebug.xyz**, where **x** is the debugging session number (which is it will increase as many times as we reload the page we are debugging), **y** is a random token necessary to avoid problems with concurrent requests and **z** is the file extension.

By default the captures are saved in the folder **/tmp** of the system, but this can be changed by indicating the capture folder in the third parameter of the constructor. Example:

```php
$rdebug = new RDebug('ansi', 'file', '/home/user/rdebug-captures');
```

In the above example we are saying that we want to capture in ANSI format and send the captures to the /home/user/rdebug-captures directory. The directory must exist and have write permissions; if not, RDebug will not write anything and will not show any errors.

The RDebug output files have this form:

```
-rw-rw-r-- 1 user    group    499 nov 12 13:36 rdebug.1.534078.txt
-rw-rw-r-- 1 user    group   1212 nov 12 13:46 rdebug.2.441574.ansi
-rw-rw-r-- 1 user    group   4532 nov 12 13:57 rdebug.3.686799.html
-rw-rw-r-- 1 user    group    761 nov 12 14:02 rdebug.4.830296.txt
```

Debugging arrays and objects
----------------------------
Arrays and objects are debugged just like any other variable (capture () accepts all types of PHP expression), but if we know for sure that a variable is of type array it is better to debug it with the **captureByRef()** method, in which we pass the array by reference. We can also debug arrays with capture(), but the captureByRef() method has been added for when we are debugging arrays that can be recursive.

On the other hand, RDebug also detects **cycles (circular references)** in both arrays and objects. Examples:

```php
$array = [0, &$array];
$rdebug->captureByRef($array);
...
$object = new stdClass();
$object->prop = $object;
$rdebug->capture($object);
```

By default RDebug does not show the public methods of the inspected objects, only the public properties. If we want RDebug to show public methods as well, we must indicate it, call the method **addMethods()**. Example:

```php
$rdebug = new RDebug();
$rdebug->addMethods();
...
$object = new SomeClass();
$rdebug->capture($object);
```

RDebug output example
---------------------
Here's an example of what the RDebug output looks like for this code snippet:

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

The output of RDebug, in plain-text format, corresponding to the previous example, is the following:

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

The first line of the output is information about the environment in which PHP is running. The following sections [1) and 2)] correspond to each of the captures. In each capture it is shown:
- The capture number.
- The file and line that the capture() call is on.
- The time in seconds from the creation of the RDebug object until the call to capture().
- **The dump of the variable or expression** (in the previous example there are two captures: the first shows an array with a reference to itself in the fourth element, and the second capture shows an object with a reference to itself in property prop_1).

Output in html-comment format is very similar to plain-text output, but with html-comment the output is inside an HTML comment. The ansi output is the same as the plain-text output, except that the types and values of the variables are shown in colors, which makes it easier to understand what is being debugged.

More examples
-------------
You can find more examples in the **examples/** folder of this package.

Tests
-----
RDebug has been tested on PHP 7 and Ubuntu Linux (not tested on Windows, Mac).

Suggestions
-----------
Comments and suggestions are welcome (RDebug author: richiruizmartinez@gmail.com)

Warranty and license
--------------------
This software is distributed without any warranty. The license is in the file <a href="https://github.com/richiruizmartinez/rdebug/blob/main/LICENSE">LICENSE</a>.
