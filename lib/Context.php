<?php


namespace Cachito\VarDebug;


class Context {

   /**
    * Context variables.
    *
    * @var array
    */
   protected $context = [];


   /**
    * Time in which this object is created.
    *
    * @var float
    */
   protected $start_time = null;


   /**
    * Constructor.
    */
   public function __construct()
   {
      $this->start_time = $this->get_now_time();
      $this->context['php_version'] = phpversion();
      $this->context['server_api_name'] = php_sapi_name();
      $this->context['docker_environment'] = file_exists('/.dockerenv');
      $this->context['operating_system'] = PHP_OS;

      // HTTP request variables
      $this->context['remote_address'] = '';
      $this->context['request_method'] = '';
      $this->context['protocol'] = '';
      $this->context['host_port'] = '';
      $this->context['request_uri'] = '';
      $this->context['ajax'] = false;

      // HTTP request
      if ($this->context['server_api_name'] != 'cli') {
         $this->context['remote_address'] = $_SERVER['REMOTE_ADDR'];
         $this->context['request_method'] = $_SERVER['REQUEST_METHOD'];
         $protocol = 'http';
         if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $protocol = 'https';
         }
         $this->context['protocol'] = $protocol;
         $this->context['host_port'] = $_SERVER['HTTP_HOST'];
         $this->context['request_uri'] = $_SERVER['REQUEST_URI'];
         $ajax = false;
         if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $ajax = true;
         }
         $this->context['ajax'] = $ajax;
      }
   }


   /**
    * Returns the UNIX time in seconds.microseconds.
    *
    * @return float
    */
   protected function get_now_time()
   {
      $time = explode(' ', microtime(), 2);
      return (float)$time[0] + (float)$time[1];
   }


   /**
    * Returns the elapsed time from start time to now, in seconds.
    *
    * @return string
    */
   public function getElapsedTime()
   {
      return number_format($this->get_now_time() - $this->start_time, 6) . ' s';
   }


   /**
    * Returns environment information.
    *
    * @return string
    */
   public function getEnvironmentInfo()
   {
      if ($this->context['docker_environment']) {
         $environment_info = 'PHP ' . $this->context['php_version'] . ' ' . $this->context['server_api_name'] . ' ' .
                             '(docker ' . $this->context['operating_system'] . ')';
      } else {
         $environment_info = 'PHP ' . $this->context['php_version'] . ' ' . $this->context['server_api_name'] . ' ' .
                             '(' . $this->context['operating_system'] . ')';
      }

      return $environment_info;
   }


   /**
    * Returns request information.
    *
    * @return string
    */
   public function getRequestInfo()
   {
      if ($this->context['ajax']) {
         $request_info = 'HTTP request: ' .
                         $this->context['remote_address'] . ' -> ' .
                         $this->context['request_method'] . ' ajax ' .
                         $this->context['protocol'] . '://' .
                         $this->context['host_port'] .
                         $this->context['request_uri'];
      } else {
         $request_info = 'HTTP request: ' .
                         $this->context['remote_address'] . ' -> ' .
                         $this->context['request_method'] . ' ' .
                         $this->context['protocol'] . '://' .
                         $this->context['host_port'] .
                         $this->context['request_uri'];
      }

      return $request_info;
   }


   /**
    * Returns where dump() or dumpbyref() was called from.
    *
    * @return string
    */
   public function getTraceFileLine()
   {
      $callers = debug_backtrace();
      return $callers[1]['file'] . '(' . $callers[1]['line'] . ')';
   }


   /**
    * Returns true if the server api is cli.
    *
    * @return boolean
    */
   public function sapiIsCli()
   {
      return ($this->context['server_api_name'] === 'cli');
   }
}