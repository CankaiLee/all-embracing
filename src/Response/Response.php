<?php

namespace WormOfTime\Response;

trait Response
{
    use Output;

    /**
     * @param null $data
     * @return array
     */
    public function json($data = null): array
    {
        return array(
            'code' => $this->getCode(),
            'data' => $data,
            'message' => $this->getMessage(),
            'success' => $this->getCode() == 0
        );
    }

    /**
     * @param int $code
     * @param string $message
     * @param null $data
     * @return array
     */
    public function error($code = 500, $message = '', $data = null): array
    {
        $this->setCode($code);
        $this->setMessage($message);
        return $this->json($data);
    }

    /**
     * @param null $data
     * @return array
     */
    public function success($data = null): array
    {
        $this->setCode(0);
        $this->setMessage('请求成功');
        return $this->json($data);
    }
}