<?php


namespace Cachito\VarDebug\OutputWriter;


class NullWriter implements WriterInterface {

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
