<?php

namespace teamones\rpc\socket;

class Socket
{

    protected $fp = null;

    /**
     * 初始化客户端
     * @param string $url
     * @return $this
     */
    public function init($url = 'tcp://127.0.0.1:8081')
    {
        $this->fp = \stream_socket_client($url);

        return $this;
    }

    /**
     * 为资源流设置阻塞或者阻塞模式
     * @param bool $mode
     * @return $this
     */
    public function block($mode = true)
    {
        \stream_set_blocking($this->fp, $mode);

        return $this;
    }

    /**
     * 设置超时时长, 默认3s超时
     * @param $time
     * @return $this
     */
    public function timeout($time = 3)
    {
        \stream_set_timeout($this->fp, $time);

        return $this;
    }


    /**
     * 处理请求的URL
     * @param string $url
     * @return array
     * @throws \Exception
     */
    protected function parseRequestUrl($url = '')
    {
        $urlArray = explode('/', $url);
        if (count($urlArray) !== 2) {
            throw new \Exception("URL format error.");
        }

        return $urlArray;
    }

    /**
     * 处理请求主体
     * @param $url
     * @param $requestBody
     * @param string $contentType
     * @return string
     * @throws \Exception
     */
    protected function parseRequestBody($url, $requestBody, $contentType = 'text')
    {
        list($class, $method) = $this->parseRequestUrl($url);

        $request = [
            'class' => $class,
            'method' => $method,
            'args' => $requestBody, // 100 是 $uid
        ];

        return json_encode($request) . "\n"; // text协议末尾有个换行符"\n"
    }


    /**
     * 请求数据
     * @param null $url 请求链接
     * @param null $requestBody 请求数据
     * @param string $contentType 协议类型，目前仅仅支持text协议
     * @return array
     * @throws \Exception
     */
    public function get($url = null, $requestBody = null, $contentType = 'text')
    {
        $data = $this->parseRequestBody($url, $requestBody, $contentType);

        if (empty($this->fp)) {
            throw new \Exception("Stream_socket_client fail.");
        }

        $startTime = time();

        \fwrite($this->fp, $data);

        $result = '';
        while (!\feof($this->fp)) {
            $result = \fgets($this->fp, 10240000);

            $diff = time() - $startTime;

            if ($diff > 24) {
                throw new \Exception("Timeout!n $diff");
            }

            $status = stream_get_meta_data($this->fp);
            if ($status['timed_out']) {
                throw new \Exception("Stream Timeout!n $diff");
            }
        }

        fclose($this->fp);

        $result = json_decode($result, true);

        return $result;

    }
}