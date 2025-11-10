<?php

// Интерфейс состояния
interface VendingMachineState
{
    public function insertCoin(): void;
    public function turnCrank(): void;
    public function dispense(): void;
}

// Контекст
class VendingMachine
{
    public int $count = 0;
    public VendingMachineState $state;

    private VendingMachineState $noCoinState;
    private VendingMachineState $hasCoinState;
    private VendingMachineState $soldState;
    private VendingMachineState $soldOutState;

    public function __construct(int $count)
    {
        $this->noCoinState = new NoCoinState($this);
        $this->hasCoinState = new HasCoinState($this);
        $this->soldState = new SoldState($this);
        $this->soldOutState = new SoldOutState($this);

        $this->count = $count;
        $this->state = $count > 0 ? $this->noCoinState : $this->soldOutState;
    }

    public function insertCoin(): void { $this->state->insertCoin(); }
    public function turnCrank(): void { $this->state->turnCrank(); }
    public function dispense(): void { $this->state->dispense(); }

    public function setState(VendingMachineState $state): void
    {
        $this->state = $state;
    }

    public function releaseGum(): void
    {
        if ($this->count > 0) {
            echo "Выдан мячик!\n";
            $this->count--;
        }
    }

    public function getNoCoinState(): VendingMachineState { return $this->noCoinState; }
    public function getHasCoinState(): VendingMachineState { return $this->hasCoinState; }
    public function getSoldState(): VendingMachineState { return $this->soldState; }
    public function getSoldOutState(): VendingMachineState { return $this->soldOutState; }
}

// Состояния
class NoCoinState implements VendingMachineState
{
    public function __construct(private VendingMachine $machine) {}

    public function insertCoin(): void
    {
        echo "Монета вставлена.\n";
        $this->machine->setState($this->machine->getHasCoinState());
    }

    public function turnCrank(): void
    {
        echo "Сначала вставьте монету!\n";
    }

    public function dispense(): void
    {
        echo "Сначала поверните ручку!\n";
    }
}

class HasCoinState implements VendingMachineState
{
    public function __construct(private VendingMachine $machine) {}

    public function insertCoin(): void
    {
        echo "Монета уже вставлена!\n";
    }

    public function turnCrank(): void
    {
        echo "Ручка повернута...\n";
        $this->machine->setState($this->machine->getSoldState());
        $this->machine->dispense();
    }

    public function dispense(): void
    {
        echo "Сначала поверните ручку!\n";
    }
}

class SoldState implements VendingMachineState
{
    public function __construct(private VendingMachine $machine) {}

    public function insertCoin(): void
    {
        echo "Пожалуйста, подождите — выдача уже началась.\n";
    }

    public function turnCrank(): void
    {
        echo "Вы уже повернули ручку!\n";
    }

    public function dispense(): void
    {
        $this->machine->releaseGum();
        if ($this->machine->count === 0) {
            echo "Мячики закончились.\n";
            $this->machine->setState($this->machine->getSoldOutState());
        } else {
            $this->machine->setState($this->machine->getNoCoinState());
        }
    }
}

class SoldOutState implements VendingMachineState
{
    public function __construct(private VendingMachine $machine) {}

    public function insertCoin(): void
    {
        echo "Мячики закончились. Монеты не принимаются.\n";
    }

    public function turnCrank(): void
    {
        echo "Мячики закончились. Ручка не крутится.\n";
    }

    public function dispense(): void
    {
        echo "Невозможно выдать — нет товара.\n";
    }
}

// Использование
$machine = new VendingMachine(2);

$machine->turnCrank();       // Сначала вставьте монету!
$machine->insertCoin();      // Монета вставлена.
$machine->insertCoin();      // Монета уже вставлена!
$machine->turnCrank();       // Ручка повернута... → Выдан мячик!

$machine->insertCoin();      // Монета вставлена.
$machine->turnCrank();       // Ручка повернута... → Выдан мячик! → Мячики закончились.
$machine->insertCoin();      // Мячики закончились. Монеты не принимаются.