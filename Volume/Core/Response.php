<?php

namespace Core;

use Core\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    private mixed $data;
    private string $headers = '';

    public function __construct(mixed $data)
    {
        $this->setData($data);
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    public function setHeaders(array $headers): void
    {
        $this->addHeader = $headers;
    }

    public function sendResponse(): void
    {
        header($this->headers);
        echo json_encode($this->data);
        exit();
    }

    public function download(): void
    {
        header($this->headers);
        readfile($this->data);
        exit();
    }

    public function view(): void
    {
        header($this->headers);
        echo $this->data;
        exit();
    }

    public function addHeader(array $headers): void
    {
        $preparedHeaders = '';

        foreach ($headers as $key => $value) {
            $preparedHeaders .= $key . ': '. $value. ', ';
        }

        $this->headers = $preparedHeaders;
    }
}