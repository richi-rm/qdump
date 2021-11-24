<?php


namespace Cachitos\VarDebug\StringFormatter;


class UTF8Formatter extends AbstractStringFormatter {

   /**
    * Returns a shortened and formatted version of $raw_string.
    * Returns the length of the string in $length.
    *
    * @param string $raw_string
    * @param int &$length
    * @return string
    */
   public function format($raw_string, &$length): string
   {
      $string = $raw_string;

      // clean up UTF-8
      //
      $substitute_character = mb_substitute_character();
      mb_substitute_character(ord('?'));
      $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
      mb_substitute_character($substitute_character);

      $length = mb_strlen($string);

      // shorten
      //
      if ($this->max_length >= 0 && $length > $this->max_length) {
         $string = mb_substr($string, 0, $this->max_length) . '...';
      }

      // escaped characters
      //
      $string = addcslashes($string, '\\"');

      // control characters
      //
      $string = str_replace("\x09", "\\t", $string);
      $string = str_replace("\x0a", "\\n", $string);
      $string = str_replace("\x0b", "\\v", $string);
      $string = str_replace("\x0c", "\\f", $string);
      $string = str_replace("\x0d", "\\r", $string);
      $string = str_replace("\x1b", "\\e", $string);

      // numeric control characters
      //

      if (0) { }

      elseif ($this->byte_format === 'hex-lower') {
         $string = str_replace("\0", "\\x0", $string);
         $string = str_replace("\1", "\\x1", $string);
         $string = str_replace("\2", "\\x2", $string);
         $string = str_replace("\3", "\\x3", $string);
         $string = str_replace("\4", "\\x4", $string);
         $string = str_replace("\5", "\\x5", $string);
         $string = str_replace("\6", "\\x6", $string);
         $string = str_replace("\7", "\\x7", $string);

         $string = str_replace("\10", "\\x8", $string);
         $string = str_replace("\11", "\\x9", $string);
         $string = str_replace("\12", "\\xa", $string);
         $string = str_replace("\13", "\\xb", $string);
         $string = str_replace("\14", "\\xc", $string);
         $string = str_replace("\15", "\\xd", $string);
         $string = str_replace("\16", "\\xe", $string);
         $string = str_replace("\17", "\\xf", $string);

         $string = str_replace("\20", "\\x10", $string);
         $string = str_replace("\21", "\\x11", $string);
         $string = str_replace("\22", "\\x12", $string);
         $string = str_replace("\23", "\\x13", $string);
         $string = str_replace("\24", "\\x14", $string);
         $string = str_replace("\25", "\\x15", $string);
         $string = str_replace("\26", "\\x16", $string);
         $string = str_replace("\27", "\\x17", $string);

         $string = str_replace("\30", "\\x18", $string);
         $string = str_replace("\31", "\\x19", $string);
         $string = str_replace("\32", "\\x1a", $string);
         $string = str_replace("\33", "\\x1b", $string);
         $string = str_replace("\34", "\\x1c", $string);
         $string = str_replace("\35", "\\x1d", $string);
         $string = str_replace("\36", "\\x1e", $string);
         $string = str_replace("\37", "\\x1f", $string);

         $string = str_replace("\177", "\\x7f", $string);
      }

      elseif ($this->byte_format === 'hex-upper') {
         $string = str_replace("\0", "\\x0", $string);
         $string = str_replace("\1", "\\x1", $string);
         $string = str_replace("\2", "\\x2", $string);
         $string = str_replace("\3", "\\x3", $string);
         $string = str_replace("\4", "\\x4", $string);
         $string = str_replace("\5", "\\x5", $string);
         $string = str_replace("\6", "\\x6", $string);
         $string = str_replace("\7", "\\x7", $string);

         $string = str_replace("\10", "\\x8", $string);
         $string = str_replace("\11", "\\x9", $string);
         $string = str_replace("\12", "\\xA", $string);
         $string = str_replace("\13", "\\xB", $string);
         $string = str_replace("\14", "\\xC", $string);
         $string = str_replace("\15", "\\xD", $string);
         $string = str_replace("\16", "\\xE", $string);
         $string = str_replace("\17", "\\xF", $string);

         $string = str_replace("\20", "\\x10", $string);
         $string = str_replace("\21", "\\x11", $string);
         $string = str_replace("\22", "\\x12", $string);
         $string = str_replace("\23", "\\x13", $string);
         $string = str_replace("\24", "\\x14", $string);
         $string = str_replace("\25", "\\x15", $string);
         $string = str_replace("\26", "\\x16", $string);
         $string = str_replace("\27", "\\x17", $string);

         $string = str_replace("\30", "\\x18", $string);
         $string = str_replace("\31", "\\x19", $string);
         $string = str_replace("\32", "\\x1A", $string);
         $string = str_replace("\33", "\\x1B", $string);
         $string = str_replace("\34", "\\x1C", $string);
         $string = str_replace("\35", "\\x1D", $string);
         $string = str_replace("\36", "\\x1E", $string);
         $string = str_replace("\37", "\\x1F", $string);

         $string = str_replace("\177", "\\x7F", $string);
      }

      else {
         $string = str_replace("\0", "\\0", $string);
         $string = str_replace("\1", "\\1", $string);
         $string = str_replace("\2", "\\2", $string);
         $string = str_replace("\3", "\\3", $string);
         $string = str_replace("\4", "\\4", $string);
         $string = str_replace("\5", "\\5", $string);
         $string = str_replace("\6", "\\6", $string);
         $string = str_replace("\7", "\\7", $string);

         $string = str_replace("\10", "\\10", $string);
         $string = str_replace("\11", "\\11", $string);
         $string = str_replace("\12", "\\12", $string);
         $string = str_replace("\13", "\\13", $string);
         $string = str_replace("\14", "\\14", $string);
         $string = str_replace("\15", "\\15", $string);
         $string = str_replace("\16", "\\16", $string);
         $string = str_replace("\17", "\\17", $string);

         $string = str_replace("\20", "\\20", $string);
         $string = str_replace("\21", "\\21", $string);
         $string = str_replace("\22", "\\22", $string);
         $string = str_replace("\23", "\\23", $string);
         $string = str_replace("\24", "\\24", $string);
         $string = str_replace("\25", "\\25", $string);
         $string = str_replace("\26", "\\26", $string);
         $string = str_replace("\27", "\\27", $string);

         $string = str_replace("\30", "\\30", $string);
         $string = str_replace("\31", "\\31", $string);
         $string = str_replace("\32", "\\32", $string);
         $string = str_replace("\33", "\\33", $string);
         $string = str_replace("\34", "\\34", $string);
         $string = str_replace("\35", "\\35", $string);
         $string = str_replace("\36", "\\36", $string);
         $string = str_replace("\37", "\\37", $string);

         $string = str_replace("\177", "\\177", $string);
      }

      $string = '"' . $string . '"';

      return $string;
   }

}
