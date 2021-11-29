<?php


namespace Cachitos\VarDebug\Renderer\BasicRenderer;


class PlainTextRenderer extends BasicRenderer {

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
      'constant'      => '',
      'constant-type' => '',
      'cycle'         => '',
      'file(line)'    => '',
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
      'constant'      => '',
      'constant-type' => '',
      'cycle'         => '',
      'file(line)'    => '',
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
