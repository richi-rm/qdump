<?php


namespace Onedevs\QDump\StringFormatter;


/**
 * UTF-8 (1 byte):
 *    00..1F : control characters
 *    20..7E : printable characters
 *    7F     : control character
 *    80..FF : invalid
 */
class UTF8Formatter extends AbstractStringFormatter {

   /**
    * Escapes the control characters and " and \
    *
    * @param string &$string
    */
   protected function escape(&$string)
   {
      $string = \addcslashes($string, '\\"');

      $string = \str_replace("\x09", "\\t", $string);
      $string = \str_replace("\x0a", "\\n", $string);
      $string = \str_replace("\x0b", "\\v", $string);
      $string = \str_replace("\x0c", "\\f", $string);
      $string = \str_replace("\x0d", "\\r", $string);
      $string = \str_replace("\x1b", "\\e", $string);

      if (0) { }
      elseif ($this->byte_format == 'hexlc') {
         for ($c=0; $c<=0x1f; $c++) {
            $string = \str_replace(\chr($c), "\\x" . \strtolower(\dechex($c)), $string);
         }
         $string = \str_replace(\chr(0x7f), "\\x7f", $string);
      }
      elseif ($this->byte_format == 'hexuc') {
         for ($c=0; $c<=0x1f; $c++) {
            $string = \str_replace(\chr($c), "\\X" . \strtoupper(\dechex($c)), $string);
         }
         $string = \str_replace(\chr(0x7f), "\\X7F", $string);
      }
      else {
         for ($c=0; $c<=0x1f; $c++) {
            $string = \str_replace(\chr($c), "\\" . \decoct($c), $string);
         }
         $string = \str_replace(\chr(0x7f), "\\177", $string);
      }
   }


   /**
    * Returns a shortened and formatted UTF-8 version of $raw_string.
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
      $substitute_character = \mb_substitute_character();
      \mb_substitute_character(\ord('?'));
      $string = \mb_convert_encoding($string, 'UTF-8', 'UTF-8');
      \mb_substitute_character($substitute_character);

      // string length
      //
      $length = \mb_strlen($string);

      // shorten
      //
      if ($this->max_length >= 0 && $length > $this->max_length) {
         $string = \mb_substr($string, 0, $this->max_length) . '...';
      }

      // escape
      //
      $this->escape($string);

      // double quotation marks
      //
      $string = '"' . $string . '"';

      return $string;
   }

}
