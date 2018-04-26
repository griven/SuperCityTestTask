<?php

namespace Playkot\PhpTestTask\Storage;

use Playkot\PhpTestTask\Payment\IPayment;

class Storage implements IStorage
{
    /**
     * @var IStorage - экземляр конкретного хранилища
     */
    private $concreteStorage;

    private function __construct() {}

    /**
     * @inheritdoc
     */
    public static function instance(array $config = null): IStorage
    {
        $storage = new self();

        if ($config['engine'] == 'redis') {
            $storage->concreteStorage = new RedisStorage($config);
        } else {
            $storage->concreteStorage = new MongoStorage($config);
        }

        return $storage;
    }

    /**
     * @inheritdoc
     */
    public function save(IPayment $payment): IStorage
    {
        $this->concreteStorage->save($payment);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function has(string $paymentId): bool
    {
        return $this->concreteStorage->has($paymentId);
    }

    /**
     * @inheritdoc
     */
    public function get(string $paymentId): IPayment
    {
        return $this->concreteStorage->get($paymentId);
    }

    /**
     * @inheritdoc
     */
    public function remove(IPayment $payment): IStorage
    {
        $this->concreteStorage->remove($payment);
        return $this;
    }
}