<?php

namespace Tests;

class MockPhpStream
{
    protected $index = 0;
    protected $length = null;
    protected $data = '';
    public $context;

    /**
     * Constructor of the class.
     */
    function __construct()
    {
        if (file_exists($this->buffer_filename())) {
            $this->data = file_get_contents($this->buffer_filename());
        } else {
            $this->data = '';
        }
        $this->index = 0;
        $this->length = strlen($this->data);
    }

    /**
     * Override buffer filename.
     * @return string
     */
    protected function buffer_filename()
    {
        return sys_get_temp_dir() . '/php_input';
    }

    /**
     * Override stream open.
     * @param string $path
     * @param string $mode
     * @param string $options
     * @param string $opened_path
     * @return boolean
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        return true;
    }

    /**
     * Override stream close.
     */
    public function stream_close()
    {
        
    }

    /**
     * Override stream stat.
     * @return array
     */
    public function stream_stat()
    {
        return [];
    }

    /**
     * Override stream flush.
     * @return boolean
     */
    public function stream_flush()
    {
        return true;
    }

    /**
     * Override stream read.
     * @param integer $count
     * @return integer
     */
    public function stream_read($count)
    {
        if (is_null($this->length) === true) {
            $this->length = strlen($this->data);
        }
        $length = min($count, $this->length - $this->index);
        $data = substr($this->data, $this->index);
        $this->index = $this->index + $length;
        return $data;
    }

    /**
     * Override stream eof.
     * @return boolean
     */
    public function stream_eof()
    {
        return ($this->index >= $this->length ? true : false);
    }

    /**
     * Override stream write.
     * @param string $data
     * @return string
     */
    public function stream_write($data)
    {
        return file_put_contents($this->buffer_filename(), $data);
    }

    /**
     * Override unlink method.
     */
    public function unlink()
    {
        if (file_exists($this->buffer_filename())) {
            unlink($this->buffer_filename());
        }
        $this->data = '';
        $this->index = 0;
        $this->length = 0;
    }
}
