<?php


namespace Onedevs\QDump\Renderer\BasicRenderer;


use Onedevs\QDump\Renderer\BasicRenderer\HtmlCommentRenderer;
use Onedevs\QDump\Renderer\BasicRenderer\HtmlRenderer;


class BasicRenderer {

   /**
    * Renderer configuration.
    *
    * @var array
    */
   protected $config = [
      'expand-arrays' => null, // expand arrays
      'sort'          => null, // order constants, properties and methods
      'verbose'       => null  // verbose
   ];


   /**
    * Left padding length.
    *
    * @var array
    */
   protected $left_pad_length = [];


   /**
    * Constructor.
    *
    * @param array $config
    */
   public function __construct($config)
   {
      $this->config = $config;
   }


   /**
    * Returns the prefix of the indicated element.
    *
    * @param string $what what you want to prefix
    * @return string
    */
   protected function p($what)
   {
      return $this->prefixes[$what];
   }


   /**
    * Returns a string that is suffixed to the content of the capture.
    *
    * @return string
    */
   public function postRender()
   {
      return $this->s('capture');
   }


   /**
    * Returns a string that is prefixed to the content of the capture.
    *
    * @param int $capture_sequence_number
    * @param string $file_line
    * @param string $time
    *
    * @return string
    */
   public function preRender($capture_sequence_number, $file_line, $time)
   {
      if ($this->config['verbose']) {
         $r = $this->p('capture') .
              $capture_sequence_number . ') ' .
              $this->p('file(line)') . $file_line . $this->s('file(line)') . ' ' .
              $this->p('time') . $time . $this->s('time') .
              "\n";
      } else {
         $r = $this->p('capture') .
              $capture_sequence_number . ') ';
      }
      $this->left_pad_length[0] = \mb_strlen($capture_sequence_number . ') ');
      return $r;
   }


   /**
    * Converts the array returned by Core::inspect() to a human-readable
    * representation.
    *
    * @param array $core_var variable returned by Core::inspect()
    * @param int $depth depth level starting from 0
    *
    * @return string
    */
   public function renderCoreVar($core_var, $depth = 0)
   {
      $r = '';

      if ($depth === 0 && $this->config['verbose']) {
         $r = \str_repeat(' ', $this->left_pad_length[0]);
      }

      // unknown
      //
      if (!\is_array($core_var) || !isset($core_var['type'])) {
         $r .= $this->p('unknown') . '(unknown)' . $this->s('unknown');
         return $r;
      }

      // null
      //
      if ($core_var['type'] === 'null') {
         $r .= $this->p('scalar') . $core_var['value'] . $this->s('scalar');
         return $r;
      }

      // bool
      //
      if ($core_var['type'] === 'bool') {
         $r .= $this->p('type') . $core_var['type'] . $this->s('type') . ' ' .
               $this->p('scalar') . $core_var['value'] . $this->s('scalar');
         return $r;
      }

      // int
      //
      if ($core_var['type'] === 'int') {
         $r .= $this->p('type') . $core_var['type'] . $this->s('type') . ' ' .
               $this->p('scalar') . $core_var['value'] . $this->s('scalar');
         return $r;
      }

      // float
      //
      if ($core_var['type'] === 'float') {
         $r .= $this->p('type') . $core_var['type'] . $this->s('type') . ' ' .
               $this->p('scalar') . $core_var['value'] . $this->s('scalar');
         return $r;
      }

      // string
      //
      if ($core_var['type'] === 'string') {
         // escape some sequences
         //
         $string = $core_var['value'];
         if (0) { }
         elseif ($this instanceof HtmlCommentRenderer) {
            $string = \str_replace(['<!--', '-->'], ['[!--', '--]'], $string);
         }
         elseif ($this instanceof HtmlRenderer) {
            $string = \htmlspecialchars($string, \ENT_NOQUOTES);
         }
         $r .= $this->p('type') . $core_var['type'] . '(' . $core_var['length'] . ')' . $this->s('type') . ' ' .
               $this->p('scalar') . $string . $this->s('scalar');
         return $r;
      }

      // array
      //
      if ($core_var['type'] === 'array') {

         $r .= $this->p('type') . $core_var['type'] . '(' . $core_var['size'] . ')' . $this->s('type');

         // do not expand array or empty array
         //
         if (!$this->config['expand-arrays'] || ($core_var['size'] < 1)) {
            return $r;
         }

         // cycle
         //
         if (isset($core_var['cycle'])) {
            $r .= ' ' . $this->p('cycle') . '(CYCLE ' . $core_var['type'] . ')' . $this->s('cycle');
            return $r;
         }

         // elements
         //
         if (isset($core_var['elements'])) {
            if ($this->config['sort']) {
               \ksort($core_var['elements'], \SORT_NATURAL | \SORT_FLAG_CASE);
            }
            foreach ($core_var['elements'] as $array_key => $array_value) {
               $left_blanks = '';
               for ($d=0; $d<=$depth; $d++) {
                  $left_blanks .= \str_repeat(' ', $this->left_pad_length[$d]);
               }
               $array_key_formatted = ( \is_int($array_key) ? $array_key : "'" . \addcslashes($array_key, "'") . "'" );
               $left_string = '[' . $array_key_formatted . '] => ';
               $this->left_pad_length[$depth+1] = \mb_strlen($left_string);
               $r .= "\n" .
                     $left_blanks .
                     '[' . $this->p('key') . $array_key_formatted . $this->s('key') . '] => ' .
                     $this->renderCoreVar($array_value, $depth + 1);
            }
         }

         return $r;
      }

      // enum case
      //
      if ($core_var['type'] === 'enumcase') {
         $r .= $this->p('type') . $core_var['type'] . $this->s('type') . ' ' .
               $this->p('namespace') . $core_var['namespace'] . $this->s('namespace') .
               $this->p('enum') . $core_var['enum'] . $this->s('enum') .
               '::' .
               $this->p('name') . $core_var['case'] . $this->s('name');
         if (isset($core_var['backing-value'])) {
            $r .= ' = ' . $this->renderCoreVar($core_var['backing-value'], $depth + 1);
         }
         if ($this->config['verbose']) {
            $r .= ' ' . $this->p('file(line)') . $core_var['file(line)'] . $this->s('file(line)');
         }
         return $r;
      }

      // object
      //
      if ($core_var['type'] === 'object') {

         // type and object id
         //
         $r .= $this->p('type') . $core_var['type'] . $this->s('type') . ' ' . '#' . $core_var['id'];

         // cycle
         //
         if (isset($core_var['cycle'])) {
            $r .= ' ' . $this->p('cycle') . '(CYCLE ' . $core_var['type'] . ')' . $this->s('cycle');
            return $r;
         }

         // class and ascentor classes
         //
         foreach ($core_var['classes'] as $inheritance_index => $class) {
            $r .= $this->render_class($class, $inheritance_index, $depth);
         }

         // ancestor classes
         //
         $ancestor_classes = [];
         foreach ($core_var['classes'] as $inheritance_index => $class) {
            $ancestor_classes[$inheritance_index] = $class['class'];
         }

         //
         // build array of items
         //

         $items = [];

         // constants
         //
         if (isset($core_var['constants'])) {

            foreach ($core_var['constants'] as $constant) {

               $key = '';

               if ($constant['access'] === 'public') {
                  $key .= '_access0';
               } elseif ($constant['access'] === 'protected') {
                  $key .= '_access1';
               } else {
                  $key .= '_access2';
               }

               $declaring_class_order = 999999 - (int)\array_search($constant['declaring-class'], $ancestor_classes);

               if ($declaring_class_order === 999999) {
                  $declaring_class_refl = new \ReflectionClass($constant['declaring-class']);
                  if ($declaring_class_refl->isInterface()) {
                     foreach ($ancestor_classes as $ancestor_class_index => $ancestor_class) {
                        $ancestor_class_refl = new \ReflectionClass($ancestor_class);
                        if ($ancestor_class_refl->implementsInterface($constant['declaring-class'])) {
                           $declaring_class_order = 999999 - $ancestor_class_index;
                           break;
                        }
                     }
                  }
               }

               $key .= '_declaringclass' . $declaring_class_order;

               $key .= '_itemtype0';

               $key .= '_' . $constant['name'];

               $items[$key] = [
                  'type' => 'constant',
                  'data' => $constant
               ];

            }

         }

         // properties
         //
         if (isset($core_var['properties'])) {

            foreach ($core_var['properties'] as $property) {

               $key = '';

               if ($property['access'] === 'public') {
                  $key .= '_access0';
               } elseif ($property['access'] === 'protected') {
                  $key .= '_access1';
               } else {
                  $key .= '_access2';
               }

               $declaring_class_order = 999999 - (int)\array_search($property['declaring-class'], $ancestor_classes);
               $key .= '_declaringclass' . $declaring_class_order;

               $key .= '_itemtype1';

               // static > readoly > normal > dynamic:
               //
               if (isset($property['static'])) {
                  $key .= '_group0';
               } elseif (isset($property['readonly'])) {
                  $key .= '_group1';
               } elseif (isset($property['dynamic'])) {
                  $key .= '_group3';
               } else {
                  $key .= '_group2';
               }

               $key .= '_' . $property['name'];

               $items[$key] = [
                  'type' => 'property',
                  'data' => $property
               ];

            }

         }

         // methods
         //
         if (isset($core_var['methods'])) {

            foreach ($core_var['methods'] as $method) {

               $key = '';

               if ($method['access'] === 'public') {
                  $key .= '_access0';
               } elseif ($method['access'] === 'protected') {
                  $key .= '_access1';
               } else {
                  $key .= '_access2';
               }

               $declaring_class_order = 999999 - (int)\array_search($method['declaring-class'], $ancestor_classes);
               $key .= '_declaringclass' . $declaring_class_order;

               $key .= '_itemtype2';

               $key .= ( isset($method['static']) ? '_group0' : '_group1' );

               $key .= '_' . $method['name'];

               $items[$key] = [
                  'type' => 'method',
                  'data' => $method
               ];

            }

         }

         // sort array of items
         //
         \ksort($items, \SORT_NATURAL | \SORT_FLAG_CASE);

         // render items
         //
         foreach ($items as $key => $item) {
            switch ($item['type']) {
               case 'constant':
                  $r .= $this->render_constant($item['data'], $ancestor_classes, $depth);
                  break;
               case 'property':
                  $r .= $this->render_property($item['data'], $ancestor_classes, $depth);
                  break;
               case 'method':
                  $r .= $this->render_method($item['data'], $ancestor_classes, $depth);
                  break;
            }
         }

         return $r;
      }

      // resource
      //
      if ($core_var['type'] === 'resource') {
         $r .= $this->p('type') . $core_var['type'] . $this->s('type');
         if (isset($core_var['id'])) {
            $r .= ' ' . '#' . $core_var['id'];
         }
         $r .= ' ' . $this->p('resource-type') . $core_var['resource-type'] . $this->s('resource-type');
         return $r;
      }

      // trace
      //
      if ($core_var['type'] === 'trace') {
         $r .= $this->p('type') . $core_var['type'] . $this->s('type');
         $left_blanks_1 = \str_repeat(' ', $this->left_pad_length[0]);
         foreach ($core_var['trace'] as $stack_index => $line) {
            $left_blanks_2 = $left_blanks_1 . \str_repeat(' ', \strlen($stack_index + 1)) . ' ';
            $file_line = $line['file'] . '(' . $line['line'] . ')';
            if ($file_line === '?(0)') {
               $file_line = '?';
            }
            switch ($line['type']) {
               case '::':
               case '->':
                  $r .= "\n" .
                        $left_blanks_1 . ($stack_index + 1) . ' ' . $this->p('file(line)') . $file_line . $this->s('file(line)') . "\n" .
                        $left_blanks_2 . $this->p('namespace') . $line['class']['namespace'] . $this->s('namespace') .
                                         $this->p('class') . $line['class']['classname'] . $this->s('class') .
                                         $line['type'] .
                                         $this->render_call_method($line['function'], $line['args']);
                  break;
               case 'function':
                  $r .= "\n" .
                        $left_blanks_1 . ($stack_index + 1) . ' ' . $this->p('file(line)') . $file_line . $this->s('file(line)') . "\n" .
                        $left_blanks_2 . $this->render_call_function($line['function'], $line['args']);
                  break;
            }
         }
         return $r;
      }

      // unknown
      //
      $r .= $this->p('unknown') . '(unknown)' . $this->s('unknown');
      return $r;
   }


   /**
    * Render an argument.
    *
    * @param mixed $value
    * @return string
    */
   protected function render_arg($value)
   {
      if ($value['type'] === 'null') {
         return $this->p('scalar') . $value['value'] . $this->s('scalar');
      }
      if ($value['type'] === 'bool') {
         return $this->p('type') . $value['type'] . $this->s('type') . ' ' .
                $this->p('scalar') . $value['value'] . $this->s('scalar');
      }
      if ($value['type'] === 'int') {
         return $this->p('type') . $value['type'] . $this->s('type') . ' ' .
                $this->p('scalar') . $value['value'] . $this->s('scalar');
      }
      if ($value['type'] === 'float') {
         return $this->p('type') . $value['type'] . $this->s('type') . ' ' .
                $this->p('scalar') . $value['value'] . $this->s('scalar');
      }
      if ($value['type'] === 'string') {
         $string = $value['value'];
         if (0) { }
         elseif ($this instanceof HtmlCommentRenderer) {
            $string = \str_replace(['<!--', '-->'], ['[!--', '--]'], $string);
         }
         elseif ($this instanceof HtmlRenderer) {
            $string = \htmlspecialchars($string, \ENT_NOQUOTES);
         }
         return $this->p('type') . $value['type'] . '(' . $value['length'] . ')' . $this->s('type') . ' ' .
                $this->p('scalar') . $string . $this->s('scalar');
      }
      if ($value['type'] === 'array') {
         return ( $value['size'] > 0 ? $this->p('type') . $value['type'] . '(' . $value['size'] . ')' . $this->s('type') : '[]' );
      }
      if ($value['type'] === 'enumcase') {
         return $this->p('type') . $value['type'] . $this->s('type') . ' ' .
                $this->p('namespace') . $value['namespace'] . $this->s('namespace') .
                $this->p('enum') . $value['enum'] . $this->s('enum') .
                '::' .
                $this->p('name') . $value['case'] . $this->s('name');
      }
      if ($value['type'] === 'object') {
         $class_index = \count($value['classes']);
         $namespace = $value['classes'][$class_index]['namespace'];
         $classname = $value['classes'][$class_index]['classname'];
         return $this->p('type') . $value['type'] . $this->s('type') . ' ' .
                $this->p('namespace') . $namespace . $this->s('namespace') .
                $this->p('class') . $classname . $this->s('class');
      }
      if ($value['type'] === 'resource') {
         $resource = $this->p('type') . $value['type'] . $this->s('type');
         if (isset($value['id'])) {
            $resource .= ' ' . '#' . $value['id'];
         }
         $resource .= ' ' . $this->p('resource-type') . $value['resource-type'] . $this->s('resource-type');
         return $resource;
      }
   }


   /**
    * Renders a line of arguments.
    *
    * @param array $parameters inspected parameters
    * @param array $args inspected arguments
    * @return string
    */
   protected function render_args(array $parameters, array $args): string
   {
      $args_ = [];
      foreach ($args as $i_arg => $arg) {
         if (\array_key_exists($i_arg, $parameters)) {
            $parameter = $parameters[$i_arg];
            $parameter_str = '';
            if (isset($parameter['type'])) {
               $parameter_str .= $this->p('type');
               if (!\strpos($parameter['type']['name'], '|')) {
                  if (isset($parameter['type']['null'])) {
                     $parameter_str .= '?';
                  }
               }
               $parameter_str .= $parameter['type']['name'] . $this->s('type') . ' ';
            }
            if (isset($parameter['reference'])) {
               $parameter_str .= '&';
            }
            if (isset($parameter['variadic'])) {
               $parameter_str .= '...';
            }
            $parameter_str .= $this->p('name') . '$' . $parameter['name'] . $this->s('name');
            $args_[] = $parameter_str .
                       ' ' . '=' . ' ' .
                       $this->render_arg($arg);
         } else {
            $args_[] = $this->render_arg($arg);
         }
      }

      return \implode(', ', $args_);
   }


   /**
    * Renders a call to a funcion.
    *
    * @param array|string $function inspected function
    * @param array $args inspected arguments
    * @return string
    */
   protected function render_call_function($function, array $args): string
   {
      if (\is_string($function)) {
         $function = ['name' => $function];
      }
      $r = '';
      if (isset($function['reference'])) {
         $r .= '&';
      }
      $r .= $this->p('method') . $function['name'] . $this->s('method') .
            '(' . $this->render_args(isset($function['parameters']) ? $function['parameters'] : [], $args) . ')';
      if (isset($function['type'])) {
         $return_type_str = $this->p('type');
         if (!\strpos($function['type']['name'], '|')) {
            if (isset($function['type']['null'])) {
               $return_type_str .= '?';
            }
         }
         $return_type_str .= $function['type']['name'] . $this->s('type');
         $r .= ':' . ' ' . $return_type_str;
      }      

      return $r;
   }


   /**
    * Renders a call to a method.
    *
    * @param array|string $method inspected method
    * @param array $args inspected arguments
    * @return string
    */
   protected function render_call_method($method, array $args): string
   {
      if (\is_string($method)) {
         $method = ['name' => $method];
      }
      $r = '';
      if (isset($method['access'])) {
         $r .= $this->p('modifier') . $method['access'] . $this->s('modifier') . ' ';
      }
      if (isset($method['static'])) {
         $r .= $this->p('modifier') . 'static' . $this->s('modifier') . ' ';
      }
      $r .= $this->p('modifier') . 'function' . $this->s('modifier') . ' ';
      if (isset($method['reference'])) {
         $r .= '&';
      }
      $r .= $this->p('method') . $method['name'] . $this->s('method') .
            '(' . $this->render_args(isset($method['parameters']) ? $method['parameters'] : [], $args) . ')';
      if (isset($method['type'])) {
         $return_type_str = $this->p('type');
         if (!\strpos($method['type']['name'], '|')) {
            if (isset($method['type']['null'])) {
               $return_type_str .= '?';
            }
         }
         $return_type_str .= $method['type']['name'] . $this->s('type');
         $r .= ':' . ' ' . $return_type_str;
      }

      return $r;
   }


   /**
    * Render a class.
    *
    * @param array $class
    * @param int $inheritance_index
    * @param int $depth depth level starting from 0
    * @return string
    */
   protected function render_class($class, $inheritance_index, $depth)
   {
      $left_blanks = '';
      for ($d=0; $d<=$depth; $d++) {
         $left_blanks .= \str_repeat(' ', $this->left_pad_length[$d]);
      }
      $r = "\n" .
           $left_blanks .
           $inheritance_index . ' ' .
           $this->p('namespace') . $class['namespace'] . $this->s('namespace') .
           $this->p('class') . $class['classname'] . $this->s('class');
      if (isset($class['abstract'])) {
         $r .= ' ' . $this->p('abstract') . '(abstract)' . $this->s('abstract');
      }
      if ($this->config['verbose']) {
         $r .= ' ' . $this->p('file(line)') . $class['file(line)'] . $this->s('file(line)');
      }

      return $r;
   }


   /**
    * Render a constant and its value.
    *
    * @param array $constant
    * @param array $ancestor_classes
    * @param int $depth depth level starting from 0
    * @return string
    */
   protected function render_constant($constant, $ancestor_classes, $depth)
   {
      $left_blanks = '';
      for ($d=0; $d<=$depth; $d++) {
         $left_blanks .= \str_repeat(' ', $this->left_pad_length[$d]);
      }

      $declaring_class_index_left_string = $declaring_class_index =
         \array_search($constant['declaring-class'], $ancestor_classes);

      if ($declaring_class_index === false) {
         $declaring_class_refl = new \ReflectionClass($constant['declaring-class']);
         if ($declaring_class_refl->isInterface()) {
            foreach ($ancestor_classes as $ancestor_class_index => $ancestor_class) {
               $ancestor_class_refl = new \ReflectionClass($ancestor_class);
               if ($ancestor_class_refl->implementsInterface($constant['declaring-class'])) {
                  $declaring_class_index_left_string =
                     $ancestor_class_index . '(implements ' . $constant['declaring-class'] . ')';
                  $declaring_class_index =
                     $ancestor_class_index . '(' . $this->p('modifier') . 'implements' . $this->s('modifier') . ' ' .
                     $this->p('name') . $constant['declaring-class'] . $this->s('name') . ')';
                  break;
               }
            }
         }
      }

      $left_string = $declaring_class_index_left_string .
                     '::' .
                     $constant['access'] . ' ' .
                     'const' . ' ' .
                     $constant['name'] . ' ' .
                     '=' . ' ';

      $this->left_pad_length[$depth+1] = \mb_strlen($left_string);

      $r = "\n" .
           $left_blanks .
           $declaring_class_index .
           '::' .
           $this->p('modifier') . $constant['access'] . $this->s('modifier') . ' ' .
           $this->p('modifier') . 'const' . $this->s('modifier') . ' ' .
           $this->p('name') . $constant['name'] . $this->s('name') .
           ' = ' .
           $this->renderCoreVar($constant['value'], $depth + 1);

      return $r;
   }


   /**
    * Renders the default value of a parameter.
    *
    * @param array $default_value
    * @return string
    */
   protected function render_default_value($default_value)
   {
      if (\array_key_exists('constant', $default_value)) {
         return $this->p('name') . $default_value['constant'] . $this->s('name');
      }
      if (\array_key_exists('value', $default_value)) {
         return $this->render_default_value_value($default_value['value']);
      }
   }


   /**
    * Renders the default value of a parameter.
    *
    * @param array $value
    * @return string
    */
   protected function render_default_value_value($value)
   {
      if ($value['type'] === 'null') {
         return $this->p('scalar') . $value['value'] . $this->s('scalar');
      }
      if ($value['type'] === 'bool') {
         return $this->p('type') . $value['type'] . $this->s('type') . ' ' .
                $this->p('scalar') . $value['value'] . $this->s('scalar');
      }
      if ($value['type'] === 'int') {
         return $this->p('type') . $value['type'] . $this->s('type') . ' ' .
                $this->p('scalar') . $value['value'] . $this->s('scalar');
      }
      if ($value['type'] === 'float') {
         return $this->p('type') . $value['type'] . $this->s('type') . ' ' .
                $this->p('scalar') . $value['value'] . $this->s('scalar');
      }
      if ($value['type'] === 'string') {
         $string = $value['value'];
         if (0) { }
         elseif ($this instanceof HtmlCommentRenderer) {
            $string = \str_replace(['<!--', '-->'], ['[!--', '--]'], $string);
         }
         elseif ($this instanceof HtmlRenderer) {
            $string = \htmlspecialchars($string, \ENT_NOQUOTES);
         }
         return $this->p('type') . $value['type'] . '(' . $value['length'] . ')' . $this->s('type') . ' ' .
                $this->p('scalar') . $string . $this->s('scalar');
      }
      if ($value['type'] === 'array') {
         $array_str = '[';
         if ($value['size'] > 0) {
            $elements = [];
            if (isset($value['elements'])) {
               foreach ($value['elements'] as $element_key => $element_value) {
                  $element_key = ( \is_int($element_key) ? $element_key : "'" . \addcslashes($element_key, "'") . "'" );
                  $element_key = $this->p('key') . $element_key . $this->s('key');
                  $elements[] = $element_key . ' ' . '=>' . ' ' . $this->render_default_value_value($element_value);
               }
            } else {
               $elements[] = '...';
            }
            $array_str .= \implode(', ', $elements);
         }
         $array_str .= ']';
         return $array_str;
      }
   }


   /**
    * Render a method.
    *
    * @param array $method
    * @param array $ancestor_classes
    * @param int $depth depth level starting from 0
    * @return string
    */
   protected function render_method($method, $ancestor_classes, $depth)
   {
      $left_blanks = '';
      for ($d=0; $d<=$depth; $d++) {
         $left_blanks .= \str_repeat(' ', $this->left_pad_length[$d]);
      }

      $declaring_class_index = \array_search($method['declaring-class'], $ancestor_classes);

      $r = "\n" .
           $left_blanks .
           $declaring_class_index .
           (isset($method['static']) ? '::' : '->') .
           $this->p('modifier') . $method['access'] . $this->s('modifier');
      if (isset($method['static'])) {
         $r .= ' ' . $this->p('modifier') . 'static' . $this->s('modifier');
      }
      $r .= ' ' . $this->p('modifier') . 'function' . $this->s('modifier');
      $r .= ' ';
      if (isset($method['reference'])) {
         $r .= '&';
      }
      $r .= $this->p('method') . $method['name'] . $this->s('method') . '(' .
            ( isset($method['parameters']) ? $this->render_parameters($method['parameters']) : '' ) .
            ')';
      if (isset($method['type'])) {
         $return_type_str = $this->p('type');
         if (!\strpos($method['type']['name'], '|')) {
            if (isset($method['type']['null'])) {
               $return_type_str .= '?';
            }
         }
         $return_type_str .= $method['type']['name'] . $this->s('type');
         $r .= ':' . ' ' . $return_type_str;
      }

      return $r;
   }


   /**
    * Render the parameters of a method.
    *
    * @param array $parameters
    * @return string
    */
   protected function render_parameters($parameters)
   {
      $parameters_ = [];
      foreach ($parameters as $parameter) {
         $parameter_ = '';
         if (isset($parameter['type'])) {
            $parameter_ .= $this->p('type');
            if (!\strpos($parameter['type']['name'], '|')) {
               if (isset($parameter['type']['null'])) {
                  $parameter_ .= '?';
               }
            }
            $parameter_ .= $parameter['type']['name'] . $this->s('type') . ' ';
         }
         if (isset($parameter['reference'])) {
            $parameter_ .= '&';
         }
         if (isset($parameter['variadic'])) {
            $parameter_ .= '...';
         }
         $parameter_ .= $this->p('name') . '$' . $parameter['name'] . $this->s('name');
         if (isset($parameter['default-value'])) {
            $parameter_ .= ' ' . '=' . ' ' . $this->render_default_value($parameter['default-value']);
         }
         $parameters_[] = $parameter_;
      }

      $parameters_ = \implode(', ', $parameters_);

      return $parameters_;
   }


   /**
    * Render a property and its value.
    *
    * @param array $property
    * @param array $ancestor_classes
    * @param int $depth depth level starting from 0
    * @return string
    */
   protected function render_property($property, $ancestor_classes, $depth)
   {
      // left blanks
      //
      $left_blanks = '';
      for ($d=0; $d<=$depth; $d++) {
         $left_blanks .= \str_repeat(' ', $this->left_pad_length[$d]);
      }

      // declaring class index
      //
      $declaring_class_index = \array_search($property['declaring-class'], $ancestor_classes);

      // left string
      //
      $left_string = $declaring_class_index .
                     (isset($property['static']) ? '::' : '->') .
                     (isset($property['dynamic']) ? '(dynamic) ' : '') .
                     $property['access'] .
                     (isset($property['static']) ? ' static' : '') .
                     (isset($property['readonly']) ? ' readonly' : '');
      if (isset($property['type'])) {
         $type = '';
         if (!\strpos($property['type']['name'], '|')) {
            if (isset($property['type']['null'])) {
               $type .= '?';
            }
         }
         $type .= $property['type']['name'];
         $left_string .= ' ' . $type;
      }
      $left_string .= ' $' . $property['name'] . ' = ';
      $this->left_pad_length[$depth+1] = \mb_strlen($left_string);

      // render string
      //
      $r = "\n" .
           $left_blanks .
           $declaring_class_index .
           (isset($property['static']) ? '::' : '->');
      if (isset($property['dynamic'])) {
         $r .= $this->p('modifier') . '(dynamic)' . $this->s('modifier') . ' ';
      }
      $r .= $this->p('modifier') . $property['access'] . $this->s('modifier');
      if (isset($property['static'])) {
         $r .= ' ' . $this->p('modifier') . 'static' . $this->s('modifier');
      }
      if (isset($property['readonly'])) {
         $r .= ' ' . $this->p('modifier') . 'readonly' . $this->s('modifier');
      }
      if (isset($property['type'])) {
         $r_type = $this->p('type');
         if (!\strpos($property['type']['name'], '|')) {
            if (isset($property['type']['null'])) {
               $r_type .= '?';
            }
         }
         $r_type .= $property['type']['name'];
         $r_type .= $this->s('type');
         $r .= ' ' . $r_type;
      }
      $r .= ' ' . $this->p('name') . '$' . $property['name'] . $this->s('name');
      if (isset($property['value'])) {
         $r .= ' = ' . $this->renderCoreVar($property['value'], $depth + 1);
      } else {
         $r .= ' ' . $this->p('uninitialized') . '(uninitialized)' . $this->s('uninitialized');
      }

      return $r;
   }


   /**
    * Returns the rendering of the header.
    *
    * @param array $header_lines header lines to render
    * @return string
    */
   public function renderHeader($header_lines)
   {
      $r = $this->p('header');
      foreach ($header_lines as $header_line) {
         $r .= $header_line . "\n";
      }
      $r .= $this->s('header');

      return $r;
   }


   /**
    * Returns the suffix of the indicated element.
    *
    * @param string $what what you want to suffix
    * @return string
    */
   protected function s($what)
   {
      return $this->suffixes[$what];
   }
}
