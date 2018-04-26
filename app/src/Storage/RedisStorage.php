<?php

namespace Playkot\PhpTestTask\Storage;

use Playkot\PhpTestTask\Payment\IPayment;
use Playkot\PhpTestTask\Payment\Payment;
use Playkot\PhpTestTask\Storage\Exception;

class RedisStorage extends Storage
{
    /**
     * @var \Redis - клиент redis
     */
    private $redis;

    protected function __construct(array $config = null)
    {
        $this->redis = new \Redis();
        $this->redis->connect($config['host'], $config['port']);
    }

    /**
     * @inheritdoc
     */
    public function save(IPayment $payment): IStorage
    {
        [$paymentId, $paymentInfo] = $payment->serialize();
        if ($paymentInfo) {
            $isSaved = $this->redis->hMset($paymentId, $paymentInfo);
            if ($isSaved) {
                $payment->resetChangedAttributes();
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function has(string $paymentId): bool
    {
        return $this->redis->exists($paymentId);
    }

    /**
     * @inheritdoc
     */
    public function get(string $paymentId): IPayment
    {
        if (!$this->has($paymentId)) {
            throw new Exception\NotFound("Payment $paymentId not found");
        }

        $paymentInfo = $this->redis->hGetAll($paymentId);

        return Payment::unserialize($paymentId, $paymentInfo);
    }

    /**
     * @inheritdoc
     */
    public function remove(IPayment $payment): IStorage
    {
        $this->redis->del($payment->getId());
        return $this;
    }
}