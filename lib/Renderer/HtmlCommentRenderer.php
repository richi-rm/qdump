<?php


namespace Cachito\VarDebug\Renderer;


class HtmlCommentRenderer extends AbstractRenderer {

   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => "<!-- VarDebug\n",
      'capture'       => "<!-- VarDebug\n",

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
      'header'        => "-->\n",
      'capture'       => "\n-->",

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
