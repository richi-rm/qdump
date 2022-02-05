<?php


namespace Onedevs\QDump\OutputWriter;


use Onedevs\QDump\Context;


class FileWriter implements OutputWriterInterface {

   /**
    * QDump file extensions corresponding to each render type.
    */
   const QDUMP_FILE_EXTENSIONS = [
      'ansi'             => '.ansi',
      'console-log-json' => '.js.html',
      'html'             => '.html',
      'html-comment'     => '.comment.html',
      'plain-text'       => '.txt'
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
         $output_dir_path = \rtrim($output_dir_path, '/') . '/';
      }

      $username = Context::getUserName();
      if ($username === -1) {
         $username = 'common';
      }
      $output_dir_path = \str_replace('*username*', $username, $output_dir_path);

      if (\is_dir($output_dir_path) && \is_writable($output_dir_path)) {
         $this->output_dir_can_write = true;
         $this->output_dir_path = $output_dir_path;
         $this->output_file_name = $this->get_next_output_file_name();
         return;
      }

      @\mkdir($output_dir_path, 0777, true);

      if (\is_dir($output_dir_path) && \is_writable($output_dir_path)) {
         $this->output_dir_can_write = true;
         $this->output_dir_path = $output_dir_path;
         $this->output_file_name = $this->get_next_output_file_name();
         return;
      }

      $this->output_dir_can_write = false;
   }


   /**
    * Returns the name of the next output file. Example: qdump.1.420954
    *
    * @return string
    */
   protected function get_next_output_file_name()
   {
      // look for qdump files
      //
      $qdump_files = \glob($this->output_dir_path . 'qdump.*');

      // no qdump files
      //
      if (empty($qdump_files)) {
         return 'qdump.1.' . $this->get_random_token();
      }

      // there are qdump files
      //
      $sequence = [];
      foreach ($qdump_files as $qdump_file) {
         $qdump_file = \explode('.', \basename($qdump_file), 3);
         $sequence[] = (int)$qdump_file[1];
      }
      \sort($sequence);

      return 'qdump.' . (\end($sequence)+1) . '.' . $this->get_random_token();
   }


   /**
    * Returns a token made up of random numeric characters. The random token is
    * appended to the name of the qdump file to avoid problems of
    * overwriting the same file by concurrent qdump requests or executions.
    *
    * @return string
    */
   protected function get_random_token()
   {
      return \rand(0, 9) . \rand(0, 9) . \rand(0, 9) .
             \rand(0, 9) . \rand(0, 9) . \rand(0, 9);
   }


   /**
    * Write $string to the qdump file.
    *
    * @param string $string string to write
    * @return string the written output
    */
   public function write($string): string
   {
      if (!$this->output_dir_can_write) {
         return '';
      }
      \file_put_contents(
         $this->output_dir_path . $this->output_file_name . self::QDUMP_FILE_EXTENSIONS[$this->render_type],
         $string,
         \FILE_APPEND | \LOCK_EX
      );
      return $string;
   }
}
