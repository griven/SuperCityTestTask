<?php

namespace Playkot\PhpTestTask\Storage;

use Playkot\PhpTestTask\Payment\IPayment;
use Playkot\PhpTestTask\Storage\Exception;

class Storage implements IStorage
{
    protected $db;
    /**
     * @var IStorage
     */
    private $concreteStorage;

    /**
     * Фабричный метод для создания экземпляра хранилища
     *
     * @param array $config
     * @return IStorage
     */
    public static function instance(array $config = null): IStorage
    {
        $storage = new self();

        if (empty($config)) {
            $storage->concreteStorage = RedisStorage::instance();
        } else {
            throw new Exception\NotFound();
        }

        return $storage;
    }

    /**
     * Сохранение существующего платежа или создание нового
     *
     * @param IPayment $payment
     * @return IStorage
     */
    public function save(IPayment $payment): IStorage
    {
        $this->concreteStorage->save($payment);
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
        return $this->concreteStorage->has($paymentId);
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
        return $this->concreteStorage->get($paymentId);
    }

    /**
     * Удаление платежа
     *
     * @param IPayment $payment
     * @return IStorage
     */
    public function remove(IPayment $payment): IStorage
    {
        $this->concreteStorage->remove($payment);
        return $this;
    }
}