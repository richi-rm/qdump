<?php


namespace Cachitos\VarDebug\Renderer;


class ConsoleLogJsonRenderer extends AbstractRenderer {

   /**
    * Returns a string that is suffixed to the content of the capture.
    *
    * @return string
    */
   public function postRender()
   {
      return ");</script>\n";
   }


   /**
    * Returns a string that is prefixed to the content of the capture.
    *
    * @param integer $capture_sequence_number
    * @param string $file_line
    * @param string $time
    *
    * @return string
    */
   public function preRender($capture_sequence_number, $file_line = null, $time = null)
   {
      if ($file_line === null) {
         $r = '<script>' .
              'console.log("VarDebug ' . $capture_sequence_number . ')");' .
              'console.log(';
      } else {
         $r = '<script>' .
              'console.log("VarDebug ' . $capture_sequence_number . ') ' . $file_line . ' ' . $time . '");' .
              'console.log(';
      }
      return $r;
   }


   /**
    * Converts the array returned by Core::inspect() to a json representation.
    *
    * @param array $core_var variable returned by Core::inspect()
    * @return string (json)
    */
   public function renderCoreVar($core_var, $not_used = 0)
   {
      return json_encode($core_var);
   }


   /**
    * Returns the rendering of the header.
    *
    * @param array $header_lines header lines to render
    * @return string
    */
   public function renderHeader($header_lines)
   {
      $header = '<script>console.log("VarDebug");';
      foreach ($header_lines as $header_line) {
         $header .= 'console.log(' . json_encode($header_line) . ');';
      }
      $header .= '</script>';

      return $header;
   }
}
