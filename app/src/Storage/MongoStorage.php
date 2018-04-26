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
    const COLLECTION = "payments";
    const DB = "test";

    private $mongo;
    private $collection;

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
        $this->mongo = new MongoManager('mongodb://mongo:27017');
        $this->collection = self::DB . '.' . self::COLLECTION;
    }

    /**
     * Сохранение существующего платежа или создание нового
     *
     * @param IPayment $payment
     * @return IStorage
     * @throws \MongoDB\Driver\Exception\Exception
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
     * Проверка на существование платежа
     *
     * @param string $paymentId
     * @return bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function has(string $paymentId): bool
    {
        $command = new Query(['_id' => $paymentId]);
        $cursor = $this->mongo->executeQuery($this->collection, $command);
        return (bool)$cursor->toArray();
    }

    /**
     * Получение платежа
     *
     * @param string $paymentId
     * @return IPayment
     * @throws Exception\NotFound
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
     * Удаление платежа
     *
     * @param IPayment $payment
     * @return IStorage
     */
    public function remove(IPayment $payment): IStorage
    {
        $bulk = new BulkWrite();
        $bulk->delete(['_id' => $payment->getId()]);
        $this->mongo->executeBulkWrite($this->collection, $bulk);
        return $this;
    }
}