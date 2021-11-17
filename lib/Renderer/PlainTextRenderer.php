<?php


namespace Cachito\VarDebug\Renderer;


class PlainTextRenderer extends AbstractRenderer {

   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => "---\n",
      'capture'       => '',

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
