<?php


namespace Cachito\VarDebug\OutputWriter;


interface WriterInterface {

   /**
    * Write $string to something.
    *
    * @param string $string string to write
    */
   public function write($string): string;

}
