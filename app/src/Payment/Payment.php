<?php


namespace Playkot\PhpTestTask\Payment;


/**
 * Class Payment
 * @package Playkot\PhpTestTask\Payment
 */
class Payment implements IPayment
{

    /**
     * Идентификатор
     * @var string
     */
    private $id;

    /**
     * Время создания
     * @var \DateTimeInterface
     */
    private $created;

    /**
     * Время обновления
     * @var \DateTimeInterface
     */
    private $updated;

    /**
     * Флаг теста
     * @var bool
     */
    private $isTest;

    /**
     * Валюта
     * @var Currency
     */
    private $currency;

    /**
     * Количество
     * @var float
     */
    private $amount;

    /**
     * Комиссия
     * @var float
     */
    private $taxAmount;

    /**
     * Состояние платежа
     * @var State
     */
    private $state;

    private function __construct() {}

    /**
     * @param string $paymentId
     * @param \DateTimeInterface $created
     * @param \DateTimeInterface $updated
     * @param bool $isTest
     * @param Currency $currency
     * @param float $amount
     * @param float $taxAmount
     * @param State $state
     * @return IPayment
     */
    public static function instance(
        string                  $paymentId,
        \DateTimeInterface      $created,
        \DateTimeInterface      $updated,
        bool                    $isTest,
        Currency                $currency,
        float                   $amount,
        float                   $taxAmount,
        State                   $state
    ): IPayment
    {
        return (new static())
            ->setId($paymentId)
            ->setCreated($created)
            ->setUpdated($updated)
            ->setIsTest($isTest)
            ->setCurrency($currency)
            ->setAmount($amount)
            ->setTaxAmount($taxAmount)
            ->setState($state);
    }

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @inheritdoc
     */
    public function getUpdated(): \DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * @inheritdoc
     */
    public function isTest(): bool
    {
        return $this->isTest;
    }

    /**
     * @inheritdoc
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @inheritdoc
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @inheritdoc
     */
    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    /**
     * @inheritdoc
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * Установка id
     *
     * @param string $id
     * @return Payment
     */
    private function setId(string $id) : self
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Must not be empty');
        }

        $this->id = $id;
        return $this;
    }

    /**
     * Установка времени создания
     *
     * @param \DateTimeInterface $created
     * @return Payment
     */
    private function setCreated(\DateTimeInterface $created) : self
    {
        $this->created = clone $created;
        return $this;
    }

    /**
     * Установка времени обновления
     *
     * @param \DateTimeInterface $updated
     * @return Payment
     */
    private function setUpdated(\DateTimeInterface $updated) : self
    {
        $this->updated = clone $updated;
        return $this;
    }

    /**
     * Установка флага теста
     *
     * @param bool $isTest
     * @return Payment
     */
    private function setIsTest(bool $isTest) : self
    {
        $this->isTest = $isTest;
        return $this;
    }

    /**
     * Установка валюты
     *
     * @param Currency $currency
     * @return Payment
     */
    private function setCurrency(Currency $currency) : self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Установка количества
     *
     * @param float $amount
     * @return Payment
     */
    private function setAmount(float $amount) : self
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Must be greated than 0');
        }

        $this->amount = $amount;
        return $this;
    }

    /**
     * Установка комиссии
     *
     * @param float $taxAmount
     * @return Payment
     */
    private function setTaxAmount(float $taxAmount) : self
    {
        if ($taxAmount < 0) {
            throw new \InvalidArgumentException('Must be greated than 0');
        }

        $this->taxAmount = $taxAmount;
        return $this;
    }

    /**
     * Установка состояния
     *
     * @param State $state
     * @return Payment
     */
    private function setState(State $state) : self
    {
        $this->state = $state;
        return $this;
    }
}