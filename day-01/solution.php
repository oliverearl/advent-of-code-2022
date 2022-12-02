<?php declare(strict_types=1);

class ElfCalorieCalculator
{
    private readonly array $input;

    private array $calories = [];

    private int $mostCalories = 0;

    public function __construct(string $filepath)
    {
        $this->input = file($filepath);
        $this->calculate();
    }

    public function calculate(): void
    {
        $elf = 1;

        foreach ($this->input as $entry) {
            if (empty($this->calories[$elf])) {
                $this->calories[$elf] = 0;
            }

            if (! is_numeric($entry)) {
                if ($this->calories[$elf] > $this->mostCalories) {
                    $this->mostCalories = $this->calories[$elf];
                }

                $elf++;
                continue;
            }

            $this->calories[$elf] += (int) $entry;
        }

        rsort($this->calories);
    }

    public function getCalories(): array
    {
        return $this->calories;
    }

    public function getMostCalories(): int
    {
        return $this->mostCalories;
    }

    public function getTopThreeSubtotal(): int
    {
        return $this->calories[0] + $this->calories[1] + $this->calories[2];
    }
}

$filepath = __DIR__ . DIRECTORY_SEPARATOR . 'input.txt';
$calculator = new ElfCalorieCalculator($filepath);

// Part 1
echo $calculator->getMostCalories() . PHP_EOL;

// Part 2
echo $calculator->getTopThreeSubtotal() . PHP_EOL;
