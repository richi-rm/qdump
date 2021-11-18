<?php


namespace Cachito\VarDebug;


use Cachito\VarDebug\Renderer\HtmlRenderer;


class VarDebugger {

   /**
    * Default render type.
    */
   const DEFAULT_OUTPUT_DIR_PATH = '/tmp/';


   /**
    * Default render type.
    */
   const DEFAULT_OUTPUT_TYPE = 'stdout';


   /**
    * Default render type.
    */
   const DEFAULT_RENDER_TYPE = 'html-comment';


   /**
    * Default verbose.
    */
   const DEFAULT_VERBOSE = false;


   /**
    * Output identifiers and corresponding handler classes.
    */
   const OUTPUT_WRITERS = [
      'file'    => 'Cachito\VarDebug\OutputWriter\FileWriter',
      'no-dump' => 'Cachito\VarDebug\OutputWriter\NullWriter',
      'stdout'  => 'Cachito\VarDebug\OutputWriter\StdoutWriter'
   ];


   /**
    * Render identifiers and corresponding handler classes.
    */
   const RENDERERS = [
      'ansi'             => 'Cachito\VarDebug\Renderer\AnsiRenderer',
      'console-log-json' => 'Cachito\VarDebug\Renderer\ConsoleLogJsonRenderer',
      'html'             => 'Cachito\VarDebug\Renderer\HtmlRenderer',
      'html-comment'     => 'Cachito\VarDebug\Renderer\HtmlCommentRenderer',
      'plain-text'       => 'Cachito\VarDebug\Renderer\PlainTextRenderer'
   ];


   /**
    * Times dump() or dumpByRef() have been called.
    *
    * @var integer
    */
   protected $capture_sequence_number = 1;


   /**
    * Context.
    *
    * @var Context
    */
   protected $context = null;


   /**
    * Core.
    *
    * @var Core
    */
   protected $core = null;


   /**
    * First dump() / dumpByRef() done.
    *
    * @var boolean
    */
   protected $first_dump_done = false;


   /**
    * Options specified when calling constructor.
    *
    * @var array
    */
   protected $options = null;


   /**
    * Output writer.
    *
    * @var OutputWriter
    */
   protected $output_writer = null;


   /**
    * Renderer.
    *
    * @var Renderer
    */
   protected $renderer = null;


   /**
    * Constructor.
    *
    * @param $options render, output and verbose options
    * @param $output_dir_path directory where the data will be saved in case
    *                         output type == 'file'
    */
   public function __construct($options = '', $output_dir_path = self::DEFAULT_OUTPUT_DIR_PATH)
   {
      $this->options = $this->parse_options($options);

      if (!is_string($output_dir_path)) {
         $output_dir_path = self::DEFAULT_OUTPUT_DIR_PATH;
      }

      // instantiate objects
      //
      $renderer_class = self::RENDERERS[$this->options['render-type']];
      $output_writer_class = self::OUTPUT_WRITERS[$this->options['output-type']];

      $this->context = new Context();
      $this->core = new Core();
      $this->renderer = new $renderer_class();
      $this->output_writer = new $output_writer_class($output_dir_path, $this->options['render-type']);
   }


   /**
    * Add class methods to the output.
    *
    * @param boolean $add true | false
    */
   public function addClassMethods($add = true)
   {
      $this->core->addClassMethods($add);
   }


   /**
    * Main public method: inspect the passed variable and send the result to
    * the output.
    *
    * @param $var variable to inspect
    */
   public function dump($var = null, &$output = null)
   {
      $written = '';

      // initial write
      //
      if (!$this->first_dump_done) {
         $written .= $this->initial_write();
         $this->first_dump_done = true;
      }

      // capture
      //
      $capture = '';
      if ($this->options['verbose']) {
         $capture .= $this->renderer->preRender($this->capture_sequence_number,
                                                $this->context->getTraceFileLine(),
                                                $this->context->getElapsedTime());
      } else {
         $capture .= $this->renderer->preRender($this->capture_sequence_number);
      }
      $capture .= $this->renderer->renderCoreVar($this->core->inspect($var));
      $capture .= $this->renderer->postRender();

      // dump
      //
      $written .= $this->output_writer->write($capture);

      // output
      //
      $output = $written;

      $this->capture_sequence_number++;
   }


   /**
    * This method is the same as dump(), except that the parameter is passed by
    * reference to properly inspect recursive arrays.
    *
    * @param $var variable to inspect
    */
   public function dumpByRef(&$var = null, &$output = null)
   {
      $written = '';

      // initial write
      //
      if (!$this->first_dump_done) {
         $written .= $this->initial_write();
         $this->first_dump_done = true;
      }

      // capture
      //
      $capture = '';
      if ($this->options['verbose']) {
         $capture .= $this->renderer->preRender($this->capture_sequence_number,
                                                $this->context->getTraceFileLine(),
                                                $this->context->getElapsedTime());
      } else {
         $capture .= $this->renderer->preRender($this->capture_sequence_number);
      }
      $capture .= $this->renderer->renderCoreVar($this->core->inspect($var));
      $capture .= $this->renderer->postRender();

      // dump
      //
      $written .= $this->output_writer->write($capture);

      // output
      //
      $output = $written;

      $this->capture_sequence_number++;
   }


   /**
    * Function to parse the first parameter of the constructor.
    *
    * @param string $options
    * @return array
    */
   protected function parse_options($options)
   {
      $r = [
         'render-type' => self::DEFAULT_RENDER_TYPE,
         'output-type' => self::DEFAULT_OUTPUT_TYPE,
         'verbose'     => self::DEFAULT_VERBOSE
      ];

      if (!is_string($options)) {
         return $r;
      }

      $options = explode(',', $options);
      foreach ($options as $option) {
         $option = trim($option);
         if (in_array($option, array_keys(self::RENDERERS))) {
            $r['render-type'] = $option;
         } elseif (in_array($option, array_keys(self::OUTPUT_WRITERS))) {
            $r['output-type'] = $option;
         } elseif ($option === 'verbose') {
            $r['verbose'] = true;
         }
      }

      return $r;
   }


   /**
    * Write output (like HTML styles or verbose headers) before doing the first
    * dump().
    *
    * @return string the written string
    */
   protected function initial_write(): string
   {
      $written = '';
      if ($this->options['render-type'] === 'html') {
         $written .= $this->output_writer->write(HtmlRenderer::CSS_STYLES . "\n");
      }
      if ($this->options['verbose']) {
         $header_lines = [$this->context->getEnvironmentInfo()];
         if (!$this->context->sapiIsCli()) {
            $header_lines[] = $this->context->getRequestInfo();
         }
         $written .= $this->output_writer->write($this->renderer->renderHeader($header_lines));
      }
      return $written;
   }
}
