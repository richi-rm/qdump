<?php


namespace Onedevs\QDump\StringFormatter;


/**
 * ISO-8859-1 (1 byte):
 *    00..1F : control characters
 *    20..7E : printable characters
 *    7F..A0 : control character
 *    A1..FF : printable characters
 */
class ISO88591ToUTF8Formatter extends AbstractStringFormatter {

   /**
    * Escapes control characters and " and \
    *
    * @param string &$string
    */
   protected function escape_control_chars(&$string)
   {
      $string = \addcslashes($string, '\\"');

      $string = \str_replace("\x09", "\\t", $string);
      $string = \str_replace("\x0a", "\\n", $string);
      $string = \str_replace("\x0b", "\\v", $string);
      $string = \str_replace("\x0c", "\\f", $string);
      $string = \str_replace("\x0d", "\\r", $string);
      $string = \str_replace("\x1b", "\\e", $string);

      if (0) { }
      // hexadecimal lower case
      elseif ($this->byte_format == 'hexlc') {
         for ($c=0; $c<=0x1f; $c++) {
            $string = \str_replace(\chr($c), "\\x" . \strtolower(\dechex($c)), $string);
         }
         for ($c=0x7f; $c<=0xa0; $c++) {
            $string = \str_replace(\chr($c), "\\x" . \strtolower(\dechex($c)), $string);
         }
         $string = \str_replace(\chr(0xad), "\\xad", $string);
      }
      // hexadecimal upper case
      elseif ($this->byte_format == 'hexuc') {
         for ($c=0; $c<=0x1f; $c++) {
            $string = \str_replace(\chr($c), "\\X" . \strtoupper(\dechex($c)), $string);
         }
         for ($c=0x7f; $c<=0xa0; $c++) {
            $string = \str_replace(\chr($c), "\\X" . \strtoupper(\dechex($c)), $string);
         }
         $string = \str_replace(\chr(0xad), "\\XAD", $string);
      }
      // octal
      else {
         for ($c=0; $c<=0x1f; $c++) {
            $string = \str_replace(\chr($c), "\\" . \decoct($c), $string);
         }
         for ($c=0x7f; $c<=0xa0; $c++) {
            $string = \str_replace(\chr($c), "\\" . \decoct($c), $string);
         }
         $string = \str_replace(\chr(0xad), "\\255", $string);
      }
   }


   /**
    * Returns a shortened and formatted ISO-8859-1 version of $raw_string.
    * Returns the length of the string in $length.
    *
    * @param string $raw_string
    * @param int &$length
    * @return string
    */
   public function format($raw_string, &$length): string
   {
      $string = $raw_string;

      // string length
      //
      $length = \strlen($string);

      // shorten
      //
      if (\is_int($this->max_string_length)) {
         if ($this->max_string_length >= 0) {
            if ($length > $this->max_string_length) {
               $string = \substr($string, 0, $this->max_string_length) . '...';
            }
         }
         else {
            if ($length > \abs($this->max_string_length)) {
               $string = '...' . \substr($string, $this->max_string_length);
            }
         }
      }

      // escape control chars
      //
      $this->escape_control_chars($string);

      // convert:
      // the string is assumed to be ISO-8859-1 and converted to UTF-8
      //
      $substitute_character = \mb_substitute_character();
      \mb_substitute_character(\ord('?'));
      $string = \mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
      \mb_substitute_character($substitute_character);

      // double quotation marks
      //
      $string = '"' . $string . '"';

      return $string;
   }

}
