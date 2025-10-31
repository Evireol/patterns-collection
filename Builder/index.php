<?php
class Pizza
{
    private string $dough = '';
    private string $sauce = '';
    private array $toppings = [];

    public function setDough(string $dough): void
    {
        $this->dough = $dough;
    }

    public function setSauce(string $sauce): void
    {
        $this->sauce = $sauce;
    }

    public function addTopping(string $topping): void
    {
        $this->toppings[] = $topping;
    }

    public function __toString(): string
    {
        return "Pizza(dough: {$this->dough}, sauce: {$this->sauce}, toppings: " . implode(', ', $this->toppings) . ")";
    }
}

class PizzaBuilder
{
    private Pizza $pizza;

    public function __construct()
    {
        $this->pizza = new Pizza();
    }

    public function setDough(string $dough): self
    {
        $this->pizza->setDough($dough);
        return $this;
    }

    public function setSauce(string $sauce): self
    {
        $this->pizza->setSauce($sauce);
        return $this;
    }

    public function addTopping(string $topping): self
    {
        $this->pizza->addTopping($topping);
        return $this;
    }

    public function build(): Pizza
    {
        return $this->pizza;
    }
}

// Использование
$pizza = (new PizzaBuilder())
    ->setDough('тонкое')
    ->setSauce('томатный')
    ->addTopping('моцарелла')
    ->addTopping('пепперони')
    ->build();

echo $pizza;