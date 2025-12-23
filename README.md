# QDump
*Improved var_dump() for PHP projects.*
## Requirements
The **php-mbstring** extension must be installed for **QDump** to work.
## Install
You can install **QDump** into your project using **composer**:
```
cd your_project_directory
composer require --dev richirm/qdump
```
Or, if you don't use composer, you can download it from https://github.com/richi-rm/qdump and include the following line in your **index.php**:
```
include 'qdump/include.php';
```
## Usage of dump()
First of all, you must instantiate a **QDumper** object:
```
require 'vendor/autoload.php';

$qd = new \RichiRM\QDump\QDumper();
```
or
```
$qd = new \RichiRM\QDump\QDumper('verbose');
```
QDump brings you two main methods: **dump()** and **trace()**
To dump (to standard output or to a file, see **Documentation**) any expression or variable use the **dump()** method:
```
$foo = 'bar';
$qd->dump($foo);
```
You can also call **dump()** with more than one argument:
```
$foo = 'bar';
$baz = 'qux';
$qd->dump($foo, $baz);
```
## Usage of trace()
You can view the call stack calling the **trace()** method:
```
$qd->trace();
```
## Documentation
The documentation is in the **doc/** folder of the project. At the moment it is only in Spanish (English translator is needed).
## License
[MIT License](https://opensource.org/license/mit)
## Contact
You can contact the author of **QDump** at **richiruizmartinez@gmail.com**. Comments and suggestions are welcome.
