<?php

namespace Playkot\PhpTestTask\Storage;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use Playkot\PhpTestTask\Payment\IPayment;
use Playkot\PhpTestTask\Payment\Payment;
use Playkot\PhpTestTask\Storage\Exception;
use MongoDB\Driver\Manager as MongoManager;

class MongoStorage extends Storage
{
    /**
     * @var MongoManager - точка входа в mongo
     */
    private $mongo;

    /**
     * @var string - Коллекция где храним платежи
     */
    private $collection;

    protected function __construct(array $config = null)
    {
        $this->mongo = new MongoManager('mongodb://' . $config['host'] . ':' . $config['port']);
        $this->collection = $config['db'] . '.' . $config['collection'];
    }

    /**
     * @inheritdoc
     */
    public function save(IPayment $payment): IStorage
    {
        [$paymentId, $paymentInfo] = $payment->serialize();
        if ($paymentInfo) {
            $bulk = new BulkWrite();
            $bulk->update(['_id' => $paymentId], array_merge(['_id' => $paymentId], $paymentInfo), ['upsert' => true]);
            $isSaved = $this->mongo->executeBulkWrite($this->collection, $bulk);
            if ($isSaved) {
                $payment->resetChangedAttributes();
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function has(string $paymentId): bool
    {
        $command = new Query(['_id' => $paymentId]);
        $cursor = $this->mongo->executeQuery($this->collection, $command);
        return (bool)$cursor->toArray();
    }

    /**
     * @inheritdoc
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function get(string $paymentId): IPayment
    {
        if (!$this->has($paymentId)) {
            throw new Exception\NotFound("Payment $paymentId not found");
        }
        $command = new Query(['_id' => $paymentId]);
        $cursor = $this->mongo->executeQuery($this->collection, $command);
        $paymentInfo = (array)$cursor->toArray()[0];

        return Payment::unserialize($paymentId, $paymentInfo);
    }

    /**
     * @inheritdoc
     */
    public function remove(IPayment $payment): IStorage
    {
        $bulk = new BulkWrite();
        $bulk->delete(['_id' => $payment->getId()]);
        $this->mongo->executeBulkWrite($this->collection, $bulk);
        return $this;
    }
}