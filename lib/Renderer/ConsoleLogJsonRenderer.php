<?php


namespace Onedevs\QDump\Renderer;


class ConsoleLogJsonRenderer {

   /**
    * Replacement token for character 0x7f.
    *
    * @var string
    */
   protected $replacement_token_for_character_0x7f = '__qdump.replacement_token_for_character_0x7f__';


   /**
    * Cut the strings and replace the character 0x7f if they are not binary.
    *
    * @param array $core_var variable returned by Core::inspect()
    * @param int $depth depth level starting from 0
    */
   protected function format_strings_core_var(&$core_var, $depth = 0)
   {
      if ($core_var['type'] === 'string') {
         if ($this->config['max-length'] >= 0 && $core_var['length'] > $this->config['max-length']) {
            if ($this->config['binary']) {
               $core_var['value'] = substr($core_var['value'], 0, $this->config['max-length']*3) . '...';
            } else {
               $core_var['value'] = mb_substr($core_var['value'], 0, $this->config['max-length']) . '...';
            }
         }
         if (!$this->config['binary']) {
            $core_var['value'] = str_replace(chr(0x7f), $this->replacement_token_for_character_0x7f, $core_var['value']);
         }
         return;
      }
      if ($core_var['type'] === 'array') {
         foreach ($core_var['elements'] as $array_key => &$array_value) {
            $this->format_strings_core_var($array_value, $depth + 1);
         }
         return;
      }
      if ($core_var['type'] === 'object') {
         foreach ($core_var['properties'] as &$property) {
            $this->format_strings_core_var($property['value'], $depth + 1);
         }
         return;
      }
   }


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
    * @param int $capture_sequence_number
    * @param string $file_line
    * @param string $time
    *
    * @return string
    */
   public function preRender($capture_sequence_number, $file_line = null, $time = null)
   {
      if ($file_line === null) {
         $r = '<script>' .
              'console.log("QDump ' . $capture_sequence_number . ')");' .
              'console.log(';
      } else {
         $r = '<script>' .
              'console.log("QDump ' . $capture_sequence_number . ') ' . $file_line . ' ' . $time . '");' .
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
      $this->format_strings_core_var($core_var);

      $json = json_encode($core_var);

      if (!$this->config['binary']) {
         $json = str_replace($this->replacement_token_for_character_0x7f, '\\u007f', $json);
      }

      return $json;
   }


   /**
    * Returns the rendering of the header.
    *
    * @param array $header_lines header lines to render
    * @return string
    */
   public function renderHeader($header_lines)
   {
      $header = '<script>console.log("QDump");';
      foreach ($header_lines as $header_line) {
         $header .= 'console.log(' . json_encode($header_line) . ');';
      }
      $header .= '</script>';

      return $header;
   }
}
