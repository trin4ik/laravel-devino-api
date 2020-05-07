<?php

namespace Trin4ik\DevinoApi\Exceptions;

use Exception;

class ErrorSendNotification extends Exception
{
    /**
     * Thrown, когда по какой-то причине СМС не отправляется.
     *
     * @param  string  $body
     *
     * @return static
     */
    public static function responseError($body): self
    {
        return new static(
            "Ошибка при отправки СМС сообщения': " . $body,
            500,
            $body
        );
    }

    /**
     * Ошибка при недоступности сервисов Devino
     *
     * @param  Exception  $exception
     *
     * @return static
     */
    public static function connectError(Exception $exception): self
    {
        return new static(
            "Нет подключения к сервисам Devino: " . $exception->getMessage(),
            $exception->getCode(),
            $exception
        );
    }
}
