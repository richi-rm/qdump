<?php


namespace Cachitos\VarDebug\Renderer;


class AbstractRenderer {


   /**
    * Configuration of renderer.
    *
    * @var array
    */
   protected $config = [
      'max-strlen' => null // max string length
   ];


   /**
    * Level prefix.
    *
    * @var string
    */
   protected $level_prefix = '   ';


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
    * Converts the string $str to its representation in PHP and shortens it to
    * config['max-strlen'] characters. If ['max-strlen'] is negative the string
    * is not shortened.
    *
    * @param string $string
    * @return string
    */
   protected function format_string($str)
   {
      // shorten
      //
      $shortened = false;
      if ($this->config['max-strlen'] >= 0) {
         if (mb_strlen($str) > $this->config['max-strlen']) {
            $str = mb_substr($str, 0, $this->config['max-strlen']);
            $shortened = true;
         }
      }

      //
      // format
      //

      $str = addcslashes($str, '\\"');

      $str = str_replace("\x09", "\\t", $str);
      $str = str_replace("\x0a", "\\n", $str);
      $str = str_replace("\x0b", "\\v", $str);
      $str = str_replace("\x0c", "\\f", $str);
      $str = str_replace("\x0d", "\\r", $str);
      $str = str_replace("\x1b", "\\e", $str);

      $str = str_replace("\0", "\\0", $str);
      $str = str_replace("\1", "\\1", $str);
      $str = str_replace("\2", "\\2", $str);
      $str = str_replace("\3", "\\3", $str);
      $str = str_replace("\4", "\\4", $str);
      $str = str_replace("\5", "\\5", $str);
      $str = str_replace("\6", "\\6", $str);
      $str = str_replace("\7", "\\7", $str);

      $str = str_replace("\10", "\\10", $str);
      $str = str_replace("\11", "\\11", $str);
      $str = str_replace("\12", "\\12", $str);
      $str = str_replace("\13", "\\13", $str);
      $str = str_replace("\14", "\\14", $str);
      $str = str_replace("\15", "\\15", $str);
      $str = str_replace("\16", "\\16", $str);
      $str = str_replace("\17", "\\17", $str);

      $str = str_replace("\20", "\\20", $str);
      $str = str_replace("\21", "\\21", $str);
      $str = str_replace("\22", "\\22", $str);
      $str = str_replace("\23", "\\23", $str);
      $str = str_replace("\24", "\\24", $str);
      $str = str_replace("\25", "\\25", $str);
      $str = str_replace("\26", "\\26", $str);
      $str = str_replace("\27", "\\27", $str);

      $str = str_replace("\30", "\\30", $str);
      $str = str_replace("\31", "\\31", $str);
      $str = str_replace("\32", "\\32", $str);
      $str = str_replace("\33", "\\33", $str);
      $str = str_replace("\34", "\\34", $str);
      $str = str_replace("\35", "\\35", $str);
      $str = str_replace("\36", "\\36", $str);
      $str = str_replace("\37", "\\37", $str);

      $str = str_replace("\x7f", "\\177", $str);

      $str = '"' . $str . '"';

      if ($shortened) {
         $str .= ' ...';
      }

      return $str;
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
      return $this->s('capture') . "\n\n";
   }


   /**
    * Returns a string that is prefixed to the content of the capture.
    *
    * @param integer $capture_sequence_number
    * @param string $file_line
    * @param string $time
    *
    * @return string
    */
   public function preRender($capture_sequence_number, $file_line = null, $time = null)
   {
      if ($file_line === null) {
         return $this->p('capture') . $capture_sequence_number . ') ';
      } else {
         return $this->p('capture') .
                $capture_sequence_number . ') ' .
                $this->p('file-line') . $file_line . $this->s('file-line') . ' ' .
                $this->p('time') . $time . $this->s('time') . "\n";
      }
   }


   /**
    * Converts the array returned by Core::inspect() to a human-readable representation.
    *
    * @param array $core_var variable returned by Core::inspect()
    * @param integer $depth depth level starting from 0
    *
    * @return string
    */
   public function renderCoreVar($core_var, $depth = 0)
   {
      if (!is_array($core_var) || !isset($core_var['type'])) {
         return $this->p('unknown') . '(unknown)' . $this->s('unknown');
      }

      if ($core_var['type'] === 'null') {
         return $this->p('value') . 'null' . $this->s('value');
      }

      if ($core_var['type'] === 'boolean') {
         return $this->p('type') . 'boolean' . $this->s('type') . ' ' .
                $this->p('value') . $core_var['value'] . $this->s('value');
      }

      if ($core_var['type'] === 'integer') {
         return $this->p('type') . 'integer' . $this->s('type') . ' ' .
                $this->p('value') . $core_var['value'] . $this->s('value');
      }

      if ($core_var['type'] === 'float') {
         return $this->p('type') . 'float' . $this->s('type') . ' ' .
                $this->p('value') . $core_var['value'] . $this->s('value');
      }

      if ($core_var['type'] === 'string') {
         return $this->p('type') . 'string(' . $core_var['length'] . ')' . $this->s('type') . ' ' .
                $this->p('value') . $this->format_string($core_var['value']) . $this->s('value');
      }

      if ($core_var['type'] === 'array') {
         $r = $this->p('type') . 'array(' . $core_var['size'] . ')' . $this->s('type');
         if ($core_var['cycle'] === true) {
            $r .= ' ' . $this->p('cycle') . '(CYCLE array)' . $this->s('cycle');
            return $r;
         }
         foreach ($core_var['elements'] as $array_key => $array_value) {
            $array_key_formatted = (is_int($array_key) ? $array_key : '\'' . addcslashes($array_key, '\'') . '\'');
            $r .= "\n" .
                  str_repeat($this->level_prefix, $depth + 1) .
                  '[' . $this->p('key') . $array_key_formatted . $this->s('key') . '] => ' .
                  $this->renderCoreVar($array_value, $depth + 1);
         }
         return $r;
      }

      if ($core_var['type'] === 'object') {
         $r = $this->p('type') . 'object' . $this->s('type') . ' ' .
              $this->p('namespace') . $core_var['class-namespace'] . $this->s('namespace') .
              $this->p('class') . $core_var['class-name'] . $this->s('class') . ' ' .
              '#' . $core_var['object-id'];
         if ($core_var['cycle'] === true) {
            $r .= ' ' . $this->p('cycle') . '(CYCLE object)' . $this->s('cycle');
            return $r;
         }
         $r .= ' ' . $this->p('file-line') . $core_var['class-file-line'] . $this->s('file-line');
         foreach ($core_var['properties'] as $property) {
            $r .= "\n" .
                  str_repeat($this->level_prefix, $depth + 1) . '->' .
                  $this->p('access') . $property['access'] . ':' . $this->s('access') .
                  $this->p('property') . $property['name'] . $this->s('property') . ' = ' .
                  $this->renderCoreVar($property['value'], $depth + 1);
         }
         foreach ($core_var['methods'] as $method) {
            $r .= "\n" .
                  str_repeat($this->level_prefix, $depth + 1) . '->' .
                  $this->p('access') . $method['access'] . ':' . $this->s('access') .
                  $this->p('method') . $method['name'] . '()' . $this->s('method');
         }
         return $r;
      }

      if ($core_var['type'] === 'resource') {
         return $this->p('type') . 'resource' . $this->s('type') . ' ' .
                $this->p('resource-type') . $core_var['resource-type'] . $this->s('resource-type');
      }

      return $this->p('unknown') . '(unknown)' . $this->s('unknown');
   }


   /**
    * Returns the rendering of the header.
    *
    * @param array $header_lines header lines to render
    * @return string
    */
   public function renderHeader($header_lines)
   {
      $header = $this->p('header');
      foreach ($header_lines as $header_line) {
         $header .= $header_line . "\n";
      }
      $header .= $this->s('header') . "\n";

      return $header;
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
