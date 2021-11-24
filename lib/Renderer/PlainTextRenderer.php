<?php


namespace Cachitos\VarDebug\Renderer;


class PlainTextRenderer extends Renderer {

   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => "---\n",
      'capture'       => '',

      'access'        => '',
      'class'         => '',
      'cycle'         => '',
      'file-line'     => '',
      'key'           => '',
      'method'        => '',
      'namespace'     => '',
      'property'      => '',
      'resource-type' => '',
      'time'          => '',
      'type'          => '',
      'unknown'       => '',
      'value'         => ''
   ];


   /**
    * Data suffixes.
    *
    * @var array
    */
   protected $suffixes = [
      'header'        => '---',
      'capture'       => '',

      'access'        => '',
      'class'         => '',
      'cycle'         => '',
      'file-line'     => '',
      'key'           => '',
      'method'        => '',
      'namespace'     => '',
      'property'      => '',
      'resource-type' => '',
      'time'          => '',
      'type'          => '',
      'unknown'       => '',
      'value'         => ''
   ];
}
