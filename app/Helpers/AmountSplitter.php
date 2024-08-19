<?php

namespace App\Helpers;

class AmountSplitter
{
    protected $people;
    protected $amount;
    protected $splits = [];

    public function __construct(int $people, int $amount)
    {
        $this->people = $people;
        $this->amount = $amount;
    }

    public function split(): array
    {
        if ($this->people === 0) {
            return []; // Return an empty array if there are no people
        }
        // Calculate base amount for each person
        $baseAmount = intdiv($this->amount, $this->people);

        // Calculate the remainder
        $remainder = $this->amount % $this->people;

        // Distribute the amounts
        for ($i = 0; $i < $this->people; $i++) {
            $this->splits[] = $i < $remainder ? $baseAmount + 1 : $baseAmount;
        }

        return $this->splits;
    }
}
