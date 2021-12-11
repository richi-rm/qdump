<?php


namespace Cachitos\VarDebug\Renderer\BasicRenderer;


class BasicRenderer {

   /**
    * String formatters.
    */
   const STRING_FORMATTERS = [
      'ascii'      => 'Cachitos\VarDebug\StringFormatter\AsciiFormatter',
      'bytes'      => 'Cachitos\VarDebug\StringFormatter\ByteSequenceFormatter',
      'iso-8859-1' => 'Cachitos\VarDebug\StringFormatter\ISO88591Formatter',
      'json'       => 'Cachitos\VarDebug\StringFormatter\JsonFormatter',
      'utf-8'      => 'Cachitos\VarDebug\StringFormatter\UTF8Formatter'
   ];


   /**
    * Renderer configuration.
    *
    * @var array
    */
   protected $config = [
      'byte-format'   => null, // byte format
      'expand-arrays' => null, // expand arrays
      'max-length'    => null, // maximum visible string length
      'sort'          => null, // order constants, properties and methods
      'string-format' => null, // string format
      'verbose'       => null  // verbose
   ];


   /**
    * Left padding length.
    *
    * @var array
    */
   protected $left_pad_length = [];


   /**
    * String formatter.
    */
   protected $string_formatter = null;


   /**
    * Constructor.
    *
    * @param array $config
    */
   public function __construct($config)
   {
      $this->config = $config;
      $string_formatter_class = self::STRING_FORMATTERS[$config['string-format']];
      $this->string_formatter = new $string_formatter_class($config['byte-format'], $config['max-length']);
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
      $this->left_pad_length[0] = mb_strlen($capture_sequence_number . ') ');
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
         $r = str_repeat(' ', $this->left_pad_length[0]);
      }

      // unknown
      //
      if (!is_array($core_var) || !isset($core_var['type'])) {
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
         $length = 0;
         $string_formatted = $this->string_formatter->format($core_var['value'], $length);
         $r .= $this->p('type') . $core_var['type'] . '(' . $length . ')' . $this->s('type') . ' ' .
               $this->p('scalar') . $string_formatted . $this->s('scalar');
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
               ksort($core_var['elements'], SORT_NATURAL | SORT_FLAG_CASE);
            }
            foreach ($core_var['elements'] as $array_key => $array_value) {
               $left_blanks = '';
               for ($d=0; $d<=$depth; $d++) {
                  $left_blanks .= str_repeat(' ', $this->left_pad_length[$d]);
               }
               $array_key_formatted = (is_int($array_key) ? $array_key : "'" . addcslashes($array_key, "'") . "'");
               $left_string = '[' . $array_key_formatted . '] => ';
               $this->left_pad_length[$depth+1] = mb_strlen($left_string);
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

         // namespace, class and file(line)
         //
         $r .= ' ' .
               $this->p('namespace') . $core_var['namespace'] . $this->s('namespace') .
               $this->p('class') . $core_var['class'] . $this->s('class');
         if ($this->config['verbose']) {
            $r .= ' ' . $this->p('file(line)') . $core_var['file(line)'] . $this->s('file(line)');
         }

         // constants
         //
         if (isset($core_var['constants'])) {

            // sort constants
            //
            if ($this->config['sort']) {

               $constants = [];
               // sort
               foreach ($core_var['constants'] as $constant) {
                  $constants[$constant['name']] = $constant;
               }
               ksort($constants, SORT_NATURAL | SORT_FLAG_CASE);

               // private constants
               //
               foreach ($constants as $constant) {
                  if ($constant['access'] === 'private') {
                     $r .= $this->render_constant($constant, $depth);
                  }
               }

               // protected constants
               //
               foreach ($constants as $constant) {
                  if ($constant['access'] === 'protected') {
                     $r .= $this->render_constant($constant, $depth);
                  }
               }

               // public constants
               //
               foreach ($constants as $constant) {
                  if ($constant['access'] === 'public') {
                     $r .= $this->render_constant($constant, $depth);
                  }
               }

            // don't sort constants
            //
            } else {

               foreach ($core_var['constants'] as $constant) {
                  $r .= $this->render_constant($constant, $depth);
               }

            }
         }

         // properties
         //
         if (isset($core_var['properties'])) {

            // sort properties
            //
            if ($this->config['sort']) {

               $properties = [];
               // sort
               foreach ($core_var['properties'] as $property) {
                  $properties[$property['name']] = $property;
               }
               ksort($properties, SORT_NATURAL | SORT_FLAG_CASE);

               // private static properties
               //
               foreach ($properties as $property) {
                  if (isset($property['static']) && $property['access'] === 'private') {
                     $r .= $this->render_property($property, $depth);
                  }
               }

               // protected static properties
               //
               foreach ($properties as $property) {
                  if (isset($property['static']) && $property['access'] === 'protected') {
                     $r .= $this->render_property($property, $depth);
                  }
               }

               // public static properties
               //
               foreach ($properties as $property) {
                  if (isset($property['static']) && $property['access'] === 'public') {
                     $r .= $this->render_property($property, $depth);
                  }
               }

               // private properties
               //
               foreach ($properties as $property) {
                  if (!isset($property['static']) && $property['access'] === 'private') {
                     $r .= $this->render_property($property, $depth);
                  }
               }

               // protected properties
               //
               foreach ($properties as $property) {
                  if (!isset($property['static']) && $property['access'] === 'protected') {
                     $r .= $this->render_property($property, $depth);
                  }
               }

               // non-dynamic public properties
               //
               foreach ($properties as $property) {
                  if (!isset($property['dynamic']) && !isset($property['static']) && $property['access'] === 'public') {
                     $r .= $this->render_property($property, $depth);
                  }
               }

               // dynamic properties
               //
               foreach ($properties as $property) {
                  if (isset($property['dynamic'])) {
                     $r .= $this->render_property($property, $depth);
                  }
               }

            // don't sort properties
            //
            } else {

               foreach ($core_var['properties'] as $property) {
                  $r .= $this->render_property($property, $depth);
               }

            }

         }

         // methods
         //
/*
         foreach ($core_var['methods'] as $method) {
            $r .= "\n" .
                  str_repeat($this->level_prefix, $depth + 1) . '->' .
                  $this->p('access') . $property['access'] . $this->s('access') . ' ' .
                  $this->p('method') . $method['name'] . '()' . $this->s('method');
         }
*/
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

      // unknown
      //
      $r .= $this->p('unknown') . '(unknown)' . $this->s('unknown');
      return $r;
   }


   /**
    * Render a constant and its value.
    *
    * @param array $constant
    * @param int $depth depth level starting from 0
    * @return string
    */
   protected function render_constant($constant, $depth)
   {
      $left_blanks = '';
      for ($d=0; $d<=$depth; $d++) {
         $left_blanks .= str_repeat(' ', $this->left_pad_length[$d]);
      }
      $left_string = '::' . $constant['access'] . ' const ' . $constant['name'] . ' = ';
      $this->left_pad_length[$depth+1] = mb_strlen($left_string);
      $r = "\n" .
           $left_blanks .
           '::' .
           $this->p('modifier') . $constant['access'] . $this->s('modifier') . ' ' .
           $this->p('modifier') . 'const' . $this->s('modifier') . ' ' .
           $this->p('name') . $constant['name'] . $this->s('name') .
           ' = ' .
           $this->renderCoreVar($constant['value'], $depth + 1);

      return $r;
   }


   /**
    * Render a property and its value.
    *
    * @param array $property
    * @param int $depth depth level starting from 0
    * @return string
    */
   protected function render_property($property, $depth)
   {
      // left blanks
      //
      $left_blanks = '';
      for ($d=0; $d<=$depth; $d++) {
         $left_blanks .= str_repeat(' ', $this->left_pad_length[$d]);
      }

      // left string
      //
      $left_string = (isset($property['static']) ? '::' : '->') .
                     (isset($property['dynamic']) ? '(dynamic) ' : '') .
                     $property['access'] .
                     (isset($property['static']) ? ' static' : '') .
                     (isset($property['readonly']) ? ' readonly' : '');
      if (isset($property['type'])) {
         $type = '';
         if (isset($property['type']['null'])) {
            $type .= '?';
         }
         $type .= $property['type']['name'];
         $left_string .= ' ' . $type;
      }
      $left_string .= ' $' . $property['name'] . ' = ';
      $this->left_pad_length[$depth+1] = mb_strlen($left_string);

      // render string
      //
      $r = "\n" .
           $left_blanks .
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
         if (isset($property['type']['null'])) {
            $r_type .= '?';
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
