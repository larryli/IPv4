<?php
/**
 * FileQuery.php
 *
 * Author: Larry Li <larryli@qq.com>
 */

namespace larryli\ipv4\Query;


/**
 * Class FileQuery
 *
 * query data use file
 *
 * @package larryli\ipv4\Query
 */
abstract class FileQuery extends Query
{
    /**
     * data filename
     *
     * @var string
     */
    protected $filename = '';
    /**
     * @var bool
     */
    protected $is_initFile = false;
    /**
     * @var array
     */
    static protected $divisions = [];

    /**
     * @param string $filename
     * @throws \Exception
     */
    public function __construct($filename)
    {
        self::initDivisions();
        if (empty($filename)) {
            throw new \Exception("must have a database file.");
        }
        if (file_exists($filename)) {
            if (!is_writable($filename)) {
                throw new \Exception("{$filename} is not writable.");
            }
        } else {
            $dir = dirname($filename);
            if (!is_dir($dir)) {
                throw new \Exception("{$dir} is not a directory.");
            }
            if (!is_writable($dir)) {
                throw new \Exception("{$dir} is not writable.");
            }
        }
        $this->filename = $filename;
    }

    /**
     *
     */
    static protected function initDivisions()
    {
        if (empty(self::$divisions)) {
            self::$divisions = [
                '中国' => ['id' => 1],
                '本机地址' => ['id' => 2],
                '局域网' => ['id' => 3],
                '保留地址' => ['id' => 4],
                'IPIP.NET' => ['id' => 4],    // alias
                'IANA机构' => ['id' => 4],    // alias
                'CZ88.NET' => ['id' => 4],    // alias
                '纯真网络' => ['id' => 4],    // alias
                '本地链路' => ['id' => 5],
            ];
            self::$divisions = array_merge(self::$divisions, require('guess_world.php'));
            self::$divisions = array_merge(self::$divisions, require('guess_china.php'));
            self::$divisions = array_merge(self::$divisions, require('guess_college.php'));
        }
    }

    /**
     * @param string $filename
     * @return string
     * @throws \Exception
     */
    static protected function getRuntime($filename = '')
    {
        $runtime = realpath(dirname(dirname(__DIR__)) . '/runtime');
        if (empty($runtime)) {
            throw new \Exception('larryli\\ipv4 runtime must not empty!');
        }
        return $runtime . '/' . $filename;
    }

    /**
     * @return bool
     */
    protected function initFile()
    {
        if ($this->is_initFile) {
            return true;
        }
        $this->is_initFile = true;
        return false;
    }

    /**
     * @return string
     */
    public function name()
    {
        return basename($this->filename);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->filename);
    }

    /**
     * @return void
     */
    public function clean()
    {
        if ($this->exists()) {
            unlink($this->filename);
        }
    }

    /**
     * @param $ip
     * @return integer
     * @throws \Exception
     */
    public function division_id($ip)
    {
        return $this->integer($this->division($ip));
    }
}