<?php


namespace Cachitos\VarDebug\Renderer;


class HtmlCommentRenderer extends AbstractRenderer {

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
