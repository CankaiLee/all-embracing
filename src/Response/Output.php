<?php

namespace WormOfTime\Response;

trait Output
{
    private $code = 0;
    private $message = '请求成功';

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return Output
     */
    public function setCode(int $code): Output
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Output
     */
    public function setMessage(string $message): Output
    {
        $this->message = $message;
        return $this;
    }
}