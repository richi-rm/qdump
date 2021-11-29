<?php


namespace Cachitos\VarDebug\Renderer\BasicRenderer;


class HtmlCommentRenderer extends BasicRenderer {

   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => "<!-- VarDebug\n",
      'capture'       => "<!-- VarDebug\n",

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
      'header'        => "-->\n",
      'capture'       => "\n-->",

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
