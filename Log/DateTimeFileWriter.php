<?php
/**
 * DateTime Log File Writer
 *
 * Use this custom log writer to output log messages
 * to a daily, weekly, monthly, or yearly log file. Log
 * files will inherently rotate based on the specified
 * log file name format and the current time.
 *
 * USAGE
 *
 * $app = new \Slim\Slim(array(
 *     'log.writer' => new \Slim\Extras\Log\DateTimeFileWriter()
 * ));
 *
 * SETTINGS
 *
 * You may customize this log writer by passing an array of
 * settings into the class constructor. Available options
 * are shown above the constructor method below.
 *
 * @author Josh Lockhart <info@slimframework.com>
 * @copyright 2012 Josh Lockhart
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Slim\Extras\Log;

class DateTimeFileWriter
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Constructor
     *
     * Prepare this log writer. Available settings are:
     *
     * path:
     * (string) The relative or absolute filesystem path to a writable directory.
     *
     * name_format:
     * (string) The log file name format; parsed with `date()`.
     *
     * extension:
     * (string) The file extention to append to the filename`.
     * 
     * date_message_format:
     * (string) The date format to use in the %date% parameter in message_format; parsed with `date()`.
     *
     * message_format:
     * (string) The log message format; available tokens are...
     *     %label%           Replaced with the log message level (e.g. FATAL, ERROR, WARN).
     *     %date%            Replaced with a date string for current timezone (default is ISO8601).
     *     %message%         Replaced with the log message, coerced to a string.
     *     %root_uri%        Replaced with the root URI of the request.
     *     %resurce_uri%     Replaced with the resource URI of the request.
     *     %content_type%    Replaced with the content type of the request.
     *     %media_type%      Replaced with the media type of the request.
     *     %content_charset% Replaced with the content charset of the request.
     *     %content_length%  Replaced with the content length of the request.
     *     %host%	         Replaced with the host of the request.
     *     %host_with_port%  Replaced with the host with the port of the request.
     *     %port%            Replaced with the port of the request.
     *     %scheme%          Replaced with the scheme of the request.
     *     %path%            Replaced with the path of the request.
     *     %url%             Replaced with the url of the request.
     *     %ip_address%	     Replaced with the ip address of the request.
     *     %referer%         Replaced with the referer of the request.
     *     %user_agent%      Replaced with the user agent of the request.
     *
     * @param   array $settings
     * @return  void
     */
    public function __construct($settings = array())
    {
        //Merge user settings
        $this->settings = array_merge(array(
            'path' => './logs',
            'name_format' => 'Y-m-d',
            'extension' => 'log',
            'date_message_format' => 'c',
            'message_format' => '%label% - %date% - %message%'
        ), $settings);

        //Remove trailing slash from log path
        $this->settings['path'] = rtrim($this->settings['path'], DIRECTORY_SEPARATOR);
    }

    /**
     * Write to log
     *
     * @param   mixed $object
     * @param   int   $level
     * @return  void
     */
    public function write($object, $level)
    {
        //Determine label
        $label = 'DEBUG';
        switch ($level) {
            case \Slim\Log::EMERGENCY:
                $label = 'EMERGENCY';
                break;
            case \Slim\Log::ALERT:
                $label = 'ALERT';
                break;
            case \Slim\Log::CRITICAL:
                $label = 'CRITICAL';
                break;
            case \Slim\Log::ERROR:
                $label = 'ERROR';
                break;
            case \Slim\Log::WARN:
                $label = 'WARN';
                break;
            case \Slim\Log::NOTICE:
                $label = 'NOTICE';
                break;
            case \Slim\Log::INFO:
                $label = 'INFO';
                break;
        }
        
        //Get the request
        $request = \Slim\Slim::getInstance()->request;

        //Get formatted log message
        $message = str_replace(array(
            '%label%',
            '%date%',
            '%message%',
            '%root_uri%',
            '%resurce_uri%',
            '%content_type%',
            '%media_type%',
            '%content_charset%',
            '%content_length%',
            '%host%',
            '%host_with_port%',
            '%port%',
            '%scheme%',
            '%path%',
            '%url%',
            '%ip_address%',
            '%referer%',
            '%user_agent%'
        ), array(
            $label,
            date($this->settings['date_message_format']),
            (string)$object,
            $request->getRootUri(),
            $request->getResourceUri(),
            $request->getContentType(),
            $request->getMediaType(),
            $request->getContentCharset(),
            $request->getContentLength(),
            $request->getHost(),
            $request->getHostWithPort(),
            $request->getPort(),
            $request->getScheme(),
            $request->getPath(),
            $request->getUrl(),
            $request->getIp(),
            $request->getReferer(),
            $request->getUserAgent()
        ), $this->settings['message_format']);

        //Open resource handle to log file
        if (!$this->resource) {
            $filename = date($this->settings['name_format']);
            if (! empty($this->settings['extension'])) {
                $filename .= '.' . $this->settings['extension'];
            }

            $this->resource = fopen($this->settings['path'] . DIRECTORY_SEPARATOR . $filename, 'a');
        }

        //Output to resource
        fwrite($this->resource, $message . PHP_EOL);
    }
}
