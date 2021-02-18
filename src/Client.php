<?php

namespace teamones\rpc;

class Client extends Base
{
    /**
     * 设置路由
     * @param string $route
     * @return $this
     */
    public function setRoute($route = '')
    {
        $this->_route = (string)$route;
        return $this;
    }

    /**
     * 设置host参数
     * @param $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->_host = $host;

        return $this;
    }

    /**
     * 设置body参数
     * @param $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->_body = $body;

        return $this;
    }

    /**
     * 组装请求, 总超时3s，返回数据格式 ["code"=>200, "msg"=>"", "data"=>[]]
     * @return array
     */
    public function request()
    {
        try {
            $response = self::instance()
                ->init($this->_host)
                ->block(true)
                ->timeout(3)
                ->get($this->_route, $this->_body);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }

        if (!empty($response['code']) && (int)$response['code'] !== 0) {
            throw new \RuntimeException($response['msg'], $response['code']);
        }

        return $response['data'];
    }
}