<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@novaframework.com
 * @version 2.2
 */

/**
 * View class to load template and views files.
 */
class View
{
    /**
     * @var array Array of HTTP headers
     */
    private static $headers = array();

    /**
     * @var array Asset templates
     */
    protected static $templates = array
    (
        'js'  => '    <script src="%s"></script>',
        'css' => '    <link href="%s" rel="stylesheet">'
    );

    /**
     * load css scripts
     * @param  String|Array  $files      paths to file/s
     * @param  boolean       $cache      if set to true a cache will be created and serverd
     * @param  boolean       $refresh    if true the cache will be updated
     * @param  string        $cachedMins minutes to hold the cache
     */
    public static function css($files, $cache = false, $refresh = false, $cachedMins = '1440')
    {
        $path = APPDIR.Url::relativeTemplatePath().'css/compressed.min.css';
        $type = 'css';

        if ($cache == false) {
            static::resource($files, $type);
        } else {
            if ($refresh == false && file_exists($path) && (filemtime($path) > (time() - 60 * $cachedMins))) {
                $path = str_replace(APPDIR, null, $path);

                $path = Inflector::tableize($path);
                static::resource(DIR.$path, $type);
            } else {
                $source = static::collect($files, $type);
                $source = static::compress($source);
                file_put_contents($path, $source);

                $path = str_replace(APPDIR, null, $path);
                $path = Inflector::tableize($path);

                static::resource(DIR.$path, $type);
            }
        }
    }

    /**
     * load js scripts
     * @param  String|Array  $files      paths to file/s
     * @param  boolean       $cache      if set to true a cache will be created and serverd
     * @param  boolean       $refresh    if true the cache will be updated
     * @param  string        $cachedMins minutes to hold the cache
     */
    public static function js($files, $cache = false, $refresh = false, $cachedMins = '1440')
    {
        $path = APPDIR.Url::relativeTemplatePath().'js/compressed.min.js';
        $type = 'js';

        if ($cache == false) {
            static::resource($files, $type);
        } else {
            if ($refresh == false && file_exists($path) && (filemtime($path) > (time() - 60 * $cachedMins))) {

                $path = str_replace(APPDIR, null, $path);
                $path = Inflector::tableize($path);

                static::resource(DIR.$path, $type);
            } else {
                $source = static::collect($files, $type);
                $source = JsMin::minify($source);
                file_put_contents($path, $source);

                $path = str_replace(APPDIR, null, $path);
                $path = Inflector::tableize($path);

                static::resource(DIR.$path, $type);
            }
        }
    }

    /**
     * Common templates for assets.
     *
     * @param string|array $files
     * @param string       $template
     */
    protected static function resource($files, $template)
    {
        $template = self::$templates[$template];

        if (is_array($files)) {
            foreach ($files as $file) {
                if (!empty($file)) {
                    echo sprintf($template, $file) . "\n";
                }
            }
        } else {
            if (!empty($files)) {
                echo sprintf($template, $files) . "\n";
            }
        }
    }

    /**
     * Include template file.
     *
     * @param  string $path  path to file from views folder
     * @param  array  $data  array of data
     * @param  array  $error array of errors
     */
    public static function render($path, $data = false, $error = false)
    {
        self::sendHeaders();

        //pass data to check and store it
        $data = self::dataLoadandConvert($data);

        foreach ($data as $name => $value) {
            ${$name} = $value;
        }

        require APPDIR . "views/$path.php";
    }

    /**
     * Include template file.
     *
     * @param  string  $path  path to file from Modules folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function renderModule($path, $data = false, $error = false)
    {
        self::sendHeaders();

        //pass data to check and store it
        $data = self::dataLoadandConvert($data);

        foreach ($data as $name => $value) {
            ${$name} = $value;
        }

        require APPDIR . "modules/$path.php";
    }

    /**
     * Return absolute path to selected template directory.
     *
     * @param  string  $path  path to file from views folder
     * @param  array   $data  array of data
     * @param  string  $custom path to template folder
     */
    public static function renderTemplate($path, $data = false, $custom = TEMPLATE)
    {
        self::sendHeaders();

        //pass data to check and store it
        $data = self::dataLoadandConvert($data);

        foreach ($data as $name => $value) {
            ${$name} = $value;
        }

        require APPDIR . "templates/$custom/$path.php";
    }

    /**
     * place hook calls into the relevant data array call
     * @param  array $data
     * @return array $data
     */
    public static function dataLoadandConvert($data)
    {
        // $hooks = Hooks::get();
        // $data['afterBody']  = $hooks->run('afterBody', $data['afterBody']);
        // $data['css']        = $hooks->run('css', $data['css']);
        // $data['js']         = $hooks->run('js', $data['js']);

        return $data;
    }

    /**
     * Add HTTP header to headers array.
     *
     * @param  string  $header HTTP header text
     */
    public function addHeader($header)
    {
        self::$headers[] = $header;
    }

    /**
     * Add an array with headers to the view.
     *
     * @param array $headers
     */
    public function addHeaders(array $headers = array())
    {
        self::$headers = array_merge(self::$headers, $headers);
    }

    /**
     * Send headers
     */
    public static function sendHeaders()
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
    }
}
