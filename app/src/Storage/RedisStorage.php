<?php

namespace Playkot\PhpTestTask\Storage;

use Playkot\PhpTestTask\Payment\IPayment;
use Playkot\PhpTestTask\Payment\Payment;
use Playkot\PhpTestTask\Storage\Exception;

class RedisStorage extends Storage
{
    private $redis;

    /**
     * Фабричный метод для создания экземпляра хранилища
     *
     * @param array $config
     * @return IStorage
     */
    public static function instance(array $config = null): IStorage
    {
        return new self();
    }

    private function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect('redis');
    }

    /**
     * Сохранение существующего платежа или создание нового
     *
     * @param IPayment $payment
     * @return IStorage
     */
    public function save(IPayment $payment): IStorage
    {
        $paymentId = $payment->getId();
        $paymentInfo = json_encode($payment->serialize());
        $this->redis->set($paymentId, $paymentInfo);

        return $this;
    }

    /**
     * Проверка на существование платежа
     *
     * @param string $paymentId
     * @return bool
     */
    public function has(string $paymentId): bool
    {
        return $this->redis->exists($paymentId);
    }

    /**
     * Получение платежа
     *
     * @param string $paymentId
     * @return IPayment
     * @throws Exception\NotFound
     */
    public function get(string $paymentId): IPayment
    {
        $payment = $this->redis->get($paymentId);

        if (!$payment) {
            throw new Exception\NotFound("Payment $paymentId not found");
        }

        return Payment::deserialize(json_decode($payment, true));
    }

    /**
     * Удаление платежа
     *
     * @param IPayment $payment
     * @return IStorage
     */
    public function remove(IPayment $payment): IStorage
    {
        $this->redis->del($payment->getId());
        return $this;
    }
}