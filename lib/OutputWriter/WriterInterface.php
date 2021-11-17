<?php


namespace Cachito\VarDebug\OutputWriter;


interface WriterInterface {

   /**
    * Constructor.
    *
    * @param string $output_dir_path
    * @param string $render_type
    */
   public function __construct($output_dir_path, $render_type);


   /**
    * Write $string to something.
    *
    * @param string $string string to write
    */
   public function write($string);

}
