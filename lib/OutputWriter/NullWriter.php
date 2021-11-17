<?php


namespace Cachito\VarDebug\OutputWriter;


class NullWriter implements WriterInterface {

   /**
    * Constructor.
    */
   public function __construct($not_used_1, $not_used_2) { }

   /**
    * It does not write $string anywhere, it just returns it.
    *
    * @param string $string
    * @return string
    */
   public function write($string): string
   {
      return $string;
   }
}
