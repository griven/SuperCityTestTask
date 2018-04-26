<?php


namespace Playkot\PhpTestTask\Payment;


class Payment implements IPayment
{
    const CREATED_TS = 'createdTs';
    const UPDATED_TS = 'updatedTs';
    const IS_TEST = 'isTest';
    const CURRENCY_CODE = 'currencyCode';
    const AMOUNT = "amount";
    const TAX_AMOUNT = "taxAmount";
    const STATE_CODE = 'stateCode';

    const STRUCTURE = [
        self::CREATED_TS,
        self::UPDATED_TS,
        self::IS_TEST,
        self::CURRENCY_CODE,
        self::AMOUNT,
        self::TAX_AMOUNT,
        self::STATE_CODE,
    ];

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

    /**
     * Массив attribute => bool
     * нужен чтобы сохранять только изменившиеся значения
     *
     * @var array
     */
    private $changedAttributes = [];

    private function __construct() {
        $this->resetChangedAttributes();
    }

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
        $this->changedAttributes[self::CREATED_TS] = true;
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
        $this->changedAttributes[self::UPDATED_TS] = true;
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
        if ($this->isTest !== $isTest) {
            $this->isTest = $isTest;
            $this->changedAttributes[self::IS_TEST] = true;
        }

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
        if ($this->currency !== $currency) {
            $this->currency = $currency;
            $this->changedAttributes[self::CURRENCY_CODE] = true;
        }
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

        if($this->amount !== $amount) {
            $this->amount = $amount;
            $this->changedAttributes[self::AMOUNT] = true;
        }

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

        if($this->taxAmount !== $taxAmount) {
            $this->taxAmount = $taxAmount;
            $this->changedAttributes[self::TAX_AMOUNT] = true;
        }

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
        if ($this->state !== $state) {
            $this->state = $state;
            $this->changedAttributes[self::STATE_CODE] = true;
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function resetChangedAttributes()
    {
        foreach (self::STRUCTURE as $attribute) {
            $this->changedAttributes[$attribute] = false;
        }
    }

    /**
     * @inheritdoc
     */
    public function serialize() : array 
    {
        $newAttributes = [];

        foreach ($this->changedAttributes as $attribute => $isChanged) {
            if ($isChanged) {
                switch ($attribute) {
                    case self::CREATED_TS:
                        $newAttributes[self::CREATED_TS] = $this->getCreated()->getTimestamp();
                        break;
                    case self::UPDATED_TS:
                        $newAttributes[self::UPDATED_TS] = $this->getUpdated()->getTimestamp();
                        break;
                    case self::IS_TEST:
                        $newAttributes[self::IS_TEST] = $this->isTest();
                        break;
                    case self::CURRENCY_CODE:
                        $newAttributes[self::CURRENCY_CODE] = $this->getCurrency()->getCode();
                        break;
                    case self::AMOUNT:
                        $newAttributes[self::AMOUNT] = $this->getAmount();
                        break;
                    case self::TAX_AMOUNT:
                        $newAttributes[self::TAX_AMOUNT] = $this->getTaxAmount();
                        break;
                    case self::STATE_CODE:
                        $newAttributes[self::STATE_CODE] = $this->getState()->getCode();
                        break;
                }
            }
        }

        return [$this->getId(), $newAttributes];
    }

    /**
     * @inheritdoc
     */
    public static function unserialize(string $paymentId, array $paymentInfo) : IPayment
    {
        return self::instance(
            $paymentId,
            (new \DateTime())->setTimestamp($paymentInfo[self::CREATED_TS]),
            (new \DateTime())->setTimestamp($paymentInfo[self::UPDATED_TS]),
            (bool)$paymentInfo[self::IS_TEST],
            new Currency($paymentInfo[self::CURRENCY_CODE]),
            $paymentInfo[self::AMOUNT],
            $paymentInfo[self::TAX_AMOUNT],
            new State($paymentInfo[self::STATE_CODE])
        );
    }
}