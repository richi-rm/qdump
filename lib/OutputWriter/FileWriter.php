<?php


namespace Cachitos\VarDebug\OutputWriter;


use Cachitos\VarDebug\Context;


class FileWriter implements WriterInterface {

   /**
    * VarDebug file extensions corresponding to each render type.
    */
   const VARDEBUG_FILE_EXTENSIONS = [
      'color-text'         => '.ansi.txt',
      'console-log-json'   => '.js.html',
      'html'               => '.html',
      'html-comment'       => '.comment.html',
      'plain-text'         => '.txt'
   ];


   /**
    * Variable that indicates whether the output directory can be written to or
    * not.
    */
   protected $output_dir_can_write = false;


   /**
    * Directory where the files with the data of the inspected variables will
    * be saved.
    *
    * @var string
    */
   protected $output_dir_path = null;


   /**
    * Name of the output file.
    *
    * @var string
    */
   protected $output_file_name = null;


   /**
    * Render type.
    *
    * @var string
    */
   protected $render_type = null;


   /**
    * Constructor.
    *
    * @param string $output_dir_path
    * @param string $render_type it is needed to calculate the extension of
    *                            the output file
    */
   public function __construct($output_dir_path, $render_type)
   {
      $this->render_type = $render_type;

      if ($output_dir_path === '') {
         $output_dir_path = './';
      } else {
         $output_dir_path = rtrim($output_dir_path, '/') . '/';
      }

      $username = Context::getUserName();
      if ($username === -1) {
         $username = 'common';
      }
      $output_dir_path = str_replace('*username*', $username, $output_dir_path);

      if (is_dir($output_dir_path) && is_writable($output_dir_path)) {
         $this->output_dir_can_write = true;
         $this->output_dir_path = $output_dir_path;
         $this->output_file_name = $this->get_next_output_file_name();
         return;
      }

      @mkdir($output_dir_path, 0777, true);

      if (is_dir($output_dir_path) && is_writable($output_dir_path)) {
         $this->output_dir_can_write = true;
         $this->output_dir_path = $output_dir_path;
         $this->output_file_name = $this->get_next_output_file_name();
         return;
      }

      $this->output_dir_can_write = false;
   }


   /**
    * Returns the name of the next output file. Example: vardebug.1.420954
    *
    * @return string
    */
   protected function get_next_output_file_name()
   {
      // look for vardebug files
      //
      $vardebug_files = glob($this->output_dir_path . 'vardebug.*');

      // no vardebug files
      //
      if (empty($vardebug_files)) {
         return 'vardebug.1.' . $this->get_random_token();
      }

      // there are vardebug files
      //
      $sequence = [];
      foreach ($vardebug_files as $vardebug_file) {
         $vardebug_file = explode('.', basename($vardebug_file), 3);
         $sequence[] = (int)$vardebug_file[1];
      }
      sort($sequence);

      return 'vardebug.' . (end($sequence)+1) . '.' . $this->get_random_token();
   }


   /**
    * Returns a token made up of random numeric characters. The random token is
    * appended to the name of the vardebug file to avoid problems of
    * overwriting the same file by concurrent vardebug requests or executions.
    *
    * @return string
    */
   protected function get_random_token()
   {
      return rand(0, 9) . rand(0, 9) . rand(0, 9) .
             rand(0, 9) . rand(0, 9) . rand(0, 9);
   }


   /**
    * Write $string to the vardebug file.
    *
    * @param string $string string to write
    * @return string the written output
    */
   public function write($string): string
   {
      if (!$this->output_dir_can_write) {
         return '';
      }
      file_put_contents(
         $this->output_dir_path . $this->output_file_name . self::VARDEBUG_FILE_EXTENSIONS[$this->render_type],
         $string,
         FILE_APPEND | LOCK_EX
      );
      return $string;
   }
}
