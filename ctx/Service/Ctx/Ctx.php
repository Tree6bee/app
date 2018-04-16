<?php

namespace Ctx\Service\Ctx;

use Ctx\Basic\Ctx as BasicCtx;
use Tree6bee\Support\Ctx\Config\Config;
use Ctx\Service\Ctx\Child\Queue\Job;

/**
 * 模块接口声明文件
 * 备注：文件命名跟模块中的其他类不同，因为要防止模块声明类只能被实例化一次
 * 也就是只能用ctx->模块 来实例化，不能用loadC来实例化更多
 */
class Ctx extends BasicCtx
{
    /**
     * 配置对象
     * 私有化config属性，防止外部模块直接使用此属性调用(不通过Ctx中的方法)config子类的方法，
     * 如:$this->ctx->Ctx->config->getConfig()
     *
     * @var Config
     */
    private $config;

    /**
     * 存储对象
     * 私有化storage属性
     *
     * @var \Ctx\Service\Ctx\Child\Storage
     */
    private $storage;

    /**
     * 队列对象
     *
     * @var \Ctx\Service\Ctx\Child\Queue
     */
    private $queue;

    public function init()
    {
        // 如果为公有属性可以等效于直接赋值给ctx节点
        // $this->ctx->Ctx->config = new Config(__DIR__ . '/../../config');
        $this->config = new Config(__DIR__ . '/../../config');
        $this->storage = $this->loadC('Storage');
        $this->queue = $this->loadC('Queue');
    }

    /*--- 配置相关 不建议使用 ---*/
    /**
     * 获取配置
     *
     * @deprecated 不建议使用
     * 建议使用 CtxModel 基础类中的 $this->getItem(avatar.url) 和 $this->getCItem(upload.host) 来获取app相关配置信息
     * @example    getConf('upload.ip@common/main')
     *
     * @param $item
     * @param mixed $default
     * @return mixed
     */
    public function getConf($item, $default = null)
    {
        return $this->config->getConfig($item, $default);
    }

    /**
     * 设置配置
     * @deprecated 不建议使用
     * @example setConf('upload.ip@common/main', '192.168.0.1');
     *
     * @param $item
     * @param mixed $config
     * @return void
     */
    public function setConf($item, $config = null)
    {
        $this->config->setConfig($item, $config);
    }

    /*--- 存储相关 ---*/
    /**
     * 获取mysql
     */
    public function loadDB($database = 'default.master')
    {
        return $this->storage->db($database);
    }

    /**
     * 获取redis
     */
    public function loadRedis($redis = 'default')
    {
        return $this->storage->redis($redis = 'default');
    }

    /**
     * @param Job $job
     * @return mixed
     */
    public function dispatch(Job $job)
    {
        return $this->queue->dispatch($job);
    }

    /**
     * @param $delay
     * @param Job $job
     * @return mixed
     */
    public function laterDispatch($delay, Job $job)
    {
        return $this->queue->laterDispatch($delay, $job);
    }

    /**
     *
     * Listen to the given queue in a loop.
     *
     * @param $conn
     * @param string $queueTube
     * @param int $sleep 没有新的有效任务产生时的休眠时间 (单位: 秒)
     * @param int $memoryLimit worker 内存限制 (单位: mb)
     */
    public function queueDaemon($conn = 'default', $queueTube = '', $sleep = 60, $memoryLimit = 128)
    {
        $this->queue->daemon($conn, $queueTube, $sleep, $memoryLimit);
    }

    /*---其它---*/
    /**
     * @deprecated 调试代码
     */
    // protected $rpc = array(
    //     'host'      => 'http://ctx.sh7ne.dev/public/rpc.php',
    //     'method'    => array(
    //         'debug',
    //     ),
    // );

    /**
     * rpc 测试代码
     */
    // private function debug($var = array()) { }
}
