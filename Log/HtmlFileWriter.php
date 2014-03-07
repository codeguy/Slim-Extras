<?php
/**
 * Html Log File Writer
 *
 * Use this custom log writer to output log messages
 * to a daily, weekly, monthly, or yearly log file about
 * the users operations in fresh HTML. Log files will
 * inherently rotate based on the specified log file
 * name format and the current time.
 *
 * USAGE
 *
 * $app = new \Slim\Slim(array(
 *     'log.writer' => new \Slim\Extras\Log\HtmlFileWriter()
 * ));
 *
 * SETTINGS
 *
 * You may customize this log writer by passing an array of
 * settings into the class constructor. Available options
 * are shown above the constructor method below.
 *
 * @author Davide Pastore <pasdavide@gmail.com>
 * @copyright 2014 Davide Pastore
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

class HtmlFileWriter
{
    /**
     * @var \DOMDocument
     */
    protected $doc;

    /**
     * @var array
     */
    protected $settings;

    /**
     * The base html skeleton.
     * @var unknown
     */
    protected $htmlSkeleton = '<html><head><title>Logger</title><style type="text/css">.emergency{color:#FF0000;} .alert{color:#FF0000;} .critical{color:#D80000;} .error{color:#B00000;} .warn{color:#880000;} .notice{color:#600000;} .info{color:#380000;} .debug{color:#080000;} .</style></head><body><table border="1"><thead><tr><th>Label</th><th>Date</th><th>Message</th></tr></thead><tbody id="log"></tbody></table></body></html>';
    
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
     *     %label%      Replaced with the log message level (e.g. FATAL, ERROR, WARN).
     *     %date%       Replaced with a date string for current timezone (default is ISO8601).
     *     %message%    Replaced with the log message, coerced to a string.
     *     %class%      Replaced with the CSS class (the log message level in the lower case).
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
            'extension' => 'html',
            'date_message_format' => 'c',
            'message_format' => '<tr class="%class%"><td>%label%</td><td>%date%</td><td>%message%</td></tr>'
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
        
        //Get formatted log message
        $message = str_replace(array(
            '%label%',
            '%date%',
            '%message%',
            '%class%'
        ), array(
            $label,
            date($this->settings['date_message_format']),
            htmlspecialchars((string)$object),
            strtolower($label)
        ), $this->settings['message_format']);
        
        
        //Set the filename
        $filename = date($this->settings['name_format']);
        if (! empty($this->settings['extension'])) {
            $filename .= '.' . $this->settings['extension'];
        }

        //Open resource handle to log file
        if (!$this->doc) {
        	
            $this->doc = new \DOMDocument();
        	
        	//Try to open the file
        	if(file_exists($this->settings['path'] . DIRECTORY_SEPARATOR . $filename)){
        	    $this->doc->loadHTMLFile($this->settings['path'] . DIRECTORY_SEPARATOR . $filename);
        	}
        	else{
        	    $this->doc = new \DOMDocument();
        	    $this->doc->loadHTML($this->htmlSkeleton);
        	}
        }

        //Output to resource
        $element = $this->doc->getElementById('log');
        $fragment = $this->doc->createDocumentFragment();
        $fragment->appendXML($message);
        $element->appendChild($fragment);
        $this->doc->saveHTMLFile($this->settings['path'] . DIRECTORY_SEPARATOR . $filename);
    }
}
