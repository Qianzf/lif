<?php

// --------------------------
//     View engine of LiF
// --------------------------

namespace Lif\Core\Web;

class View
{
    protected $layout   = null;
    protected $template = null;
    protected $tplPath  = null;
    protected $includes = [];
    protected $data     = [];
    protected $output   = '';
    protected $outputed = false;
    protected $cache    = false;

    public function __construct(
        string $template,
        array $data = [],
        $cache = null
    ) {
        $path = pathOf('view').$template.'.php';
        if (! file_exists($path)) {
            $this->outputed = true;
            excp('Template `'.$template.'` not exists.');
        }

        $this->tplPath  = $path;
        $this->template = $template;
        $this->data     = $data;
        
        $this->cache = $cache ?? (
            conf('app')['view']['cache'] ?? false
        );

        unset($data);
    }

    public function cache($cache = null)
    {
        if (is_bool($cache)) {
            $this->cache = $cache;
        }

        return $this->cache;
    }

    public function render() : string
    {
        if ($this->data) {
            extract($this->data, EXTR_OVERWRITE);
        }

        $content = $__MAIN__ = $this->include($this->tplPath);

        if ($this->layout) {
            $content = $this->include($this->layout, [
                '__MAIN__' => $__MAIN__,
            ]);
        }

        return $content;
    }

    protected function include($path, $data = []): string
    {
        if (! $path || !file_exists($path)) {
            excp('View path not exists.');
        }

        if ($data) {
            $this->data($data);
        }
        if ($this->data) {
            extract($this->data, EXTR_OVERWRITE);
        }

        $this->includes[] = $path;

        try {
            $level = ob_get_level();
            ob_start();

            include $path;

            return ob_get_clean();
        } catch (Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            exception($e);
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            exception($e);
        } catch (\Error $e) {
            exception($e);
        }
    }

    public function layout(string $layout): void
    {
        $path = pathOf('view').'__layout/'.$layout.'.php';
        if (! file_exists($path)) {
            excp('Layout `'.$layout.'` not exists.');
        }

        $this->layout = $path;
    }

    public function section($section, array $data = []): string
    {
        $path = pathOf('view', '__section/'.$section.'.php');
        if (! file_exists($path)) {
            excp('Section `'.$section.'` not exists.');
        }

        $this->data($data);

        return $this->include($path);
    }

    public function set($key, $val): void
    {
        $this->with($key, $val);
    }

    public function css($css)
    {
        return _css($css);
    }

    public function js($js)
    {
        return _js($js);
    }

    protected function data(array $data = []): View
    {
        if ($data) {
            $this->data = array_merge($this->data, $data);
        }

        return $this;
    }

    public function with($key, $val = null): View
    {
        if (is_string($key) && $val) {
            $this->data([$key => $val]);
        } elseif (is_array($key)) {
            $this->data($key);
        }

        return $this;
    }

    public function __call($name, $args)
    {
        if ('with' == mb_substr($name, 0, 4)) {
            $rest = mb_substr($name, 4);
            if ($rest && $args) {
                // Support one word variable only, `userId` not supported
                $rest = preg_replace_callback('/[A-Z]/u', function ($match) {
                    if (isset($match[0]) && is_string($match[0])) {
                        return '.'.strtolower($match[0]);
                    }
                }, $rest);

                $vars   = array_values(array_filter(explode('.', $rest)));
                $varCnt = count($vars);
                $argCnt = count($args);

                if ($argCnt > $varCnt) {
                    excp(
                        'Arguments count can not greater than variable count.'
                    );
                }

                $lastArg = $args[$argCnt-1];
                $data = [];
                foreach ($vars as $idx => $var) {
                    $data[$var] = ($argCnt < $varCnt)
                    ? ($args[$idx] ?? $lastArg)
                    : $args[$idx];
                }

                $this->data($data);

                return $this;
            }
        } else {
            $value = isset($args[0]) ? (
                is_string($args[0]) ? $args[0] : (
                    is_array($args[0])
                    ? implode(' - ', $args[0])
                    : (string) $args[0]
                )
            ) : '';

            $this->data[$name] = $value;
        }
    }

    public function output(): void
    {
        $cache = pathOf('cache').'/view/'.$this->template.'.html';
        if (file_exists($cache)) {
            if ($this->cache) {
                $this->output = file_get_contents($cache);
            } else {
                @unlink($cache);
            }
        }

        if (empty($this->output)) {
            $this->output = $this->render();
            if ($this->cache) {
                file_put_contents($cache, $this->output);
            }
        }

        if (! headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
        }

        echo $this->output;

        $this->outputed = true;
    }

    public function escape(string $text = null) : string
    {
        return htmlspecialchars($text);
    }

    public function __destruct()
    {
        if (!$this->outputed
            && (!isset($GLOBALS['LIF_EXCP'])
                || (isset($GLOBALS['LIF_EXCP'])
                    && (true !== $GLOBALS['LIF_EXCP'])
                )
            )
            && (!isset($GLOBALS['LIF_DEBUGGING'])
                || (isset($GLOBALS['LIF_DEBUGGING'])
                    && (true !== $GLOBALS['LIF_DEBUGGING'])
                )
            )
        ) {
            $this->output();
        }

        exit;
    }
}
