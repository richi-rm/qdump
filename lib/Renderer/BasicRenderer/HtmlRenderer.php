<?php


namespace Cachitos\VarDebug\Renderer\BasicRenderer;


class HtmlRenderer extends BasicRenderer {

   /**
    * CSS styles.
    */
   const CSS_STYLES = <<<HTML
<style type="text/css">
   .vardebug-pre-header    { color: #a0a0a0; background-color: #f0f0f0; padding: 10px; font-size: 11pt; margin: 5px; }
   .vardebug-pre-capture   { color: black; background-color: #f0f0f0; padding: 10px; font-size: 11pt; margin: 5px; }
   .vardebug-access        { color: #004000; }
   .vardebug-class         { color: #008000; font-weight: bold; }
   .vardebug-cycle         { color: #ff0000; }
   .vardebug-file-line     { text-decoration: underline; }
   .vardebug-key           { color: #d08000; }
   .vardebug-method        { color: #008000; font-weight: bold; }
   .vardebug-namespace     { color: #004000; }
   .vardebug-property      { color: #008000; }
   .vardebug-resource-type { color: #ff00ff; }
   .vardebug-time          { color: #a0a0a0; }
   .vardebug-type          { font-style: italic; }
   .vardebug-unknown       { color: #ff0000; }
   .vardebug-value         { color: #0080ff; font-weight: bold; }
</style>
HTML;


   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => '<pre class="vardebug vardebug-pre-header">',
      'capture'       => '<pre class="vardebug vardebug-pre-capture">',

      'access'        => '<span class="vardebug vardebug-access">',
      'class'         => '<span class="vardebug vardebug-class">',
      'cycle'         => '<span class="vardebug vardebug-cycle">',
      'file-line'     => '<span class="vardebug vardebug-file-line">',
      'key'           => '<span class="vardebug vardebug-key">',
      'method'        => '<span class="vardebug vardebug-method">',
      'namespace'     => '<span class="vardebug vardebug-namespace">',
      'property'      => '<span class="vardebug vardebug-property">',
      'resource-type' => '<span class="vardebug vardebug-resource-type">',
      'time'          => '<span class="vardebug vardebug-time">',
      'type'          => '<span class="vardebug vardebug-type">',
      'unknown'       => '<span class="vardebug vardebug-unknown">',
      'value'         => '<span class="vardebug vardebug-value">'
   ];


   /**
    * Data suffixes.
    *
    * @var array
    */
   protected $suffixes = [
      'header'        => '</pre>',
      'capture'       => '</pre>',

      'access'        => '</span>',
      'class'         => '</span>',
      'cycle'         => '</span>',
      'file-line'     => '</span>',
      'key'           => '</span>',
      'method'        => '</span>',
      'namespace'     => '</span>',
      'property'      => '</span>',
      'resource-type' => '</span>',
      'time'          => '</span>',
      'type'          => '</span>',
      'unknown'       => '</span>',
      'value'         => '</span>'
   ];
}
