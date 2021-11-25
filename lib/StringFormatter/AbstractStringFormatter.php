<?php


namespace Cachitos\VarDebug\StringFormatter;


abstract class AbstractStringFormatter {

   /**
    * Byte format ('bits', decimal', 'hex-lower', 'hex-upper', 'octal').
    *     
    * @var string
    */
   protected $byte_format = null;


   /**
    * Maximum visible string length.
    *
    * @var int
    */
   protected $max_length = null;


   /**
    * Constructor.
    *
    * @param string $byte_format byte format
    * @param int $max_length string max length
    */
   public function __construct($byte_format, $max_length)
   {
      $this->byte_format = $byte_format;
      $this->max_length = $max_length;
   }


   /**
    * Returns a shortened and formatted version of $raw_string.
    * Returns the length of the string in $length.
    *
    * @param string $raw_string
    * @param int &$length
    * @return string
    */
   abstract public function format($raw_string, &$length): string;

}
