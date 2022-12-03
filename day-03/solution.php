<?php declare(strict_types=1);

class BackpackReorganizer
{
    private const VALUE_DICTIONARY = [
        'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10,
        'k' => 11, 'l' => 12, 'm' => 13, 'n' => 14, 'o' => 15, 'p' => 16, 'q' => 17, 'r' => 18, 's' => 19, 't' => 20,
        'u' => 21, 'v' => 22, 'w' => 23, 'x' => 24, 'y' => 25, 'z' => 26,

        'A' => 27, 'B' => 28, 'C' => 29, 'D' => 30, 'E' => 31, 'F' => 32, 'G' => 33, 'H' => 34, 'I' => 35, 'J' => 36,
        'K' => 37, 'L' => 38, 'M' => 39, 'N' => 40, 'O' => 41, 'P' => 42, 'Q' => 43, 'R' => 44, 'S' => 45, 'T' => 46,
        'U' => 47, 'V' => 48, 'W' => 49, 'X' => 50, 'Y' => 51, 'Z' => 52,
    ];

    private readonly array $input;

    private int $score = 0;

    public function __construct(string $filepath)
    {
        $this->input = file($filepath);
    }

    public function organize(): void
    {
        foreach ($this->input as $backpack) {
            $backpack = trim($backpack);

            $middle = (int) (strlen($backpack) / 2);
            $firstCompartment = str_split(substr($backpack, 0, $middle));
            $secondCompartment = str_split(substr($backpack, $middle));

            $mutualItems = array_unique(array_intersect($firstCompartment, $secondCompartment));

            foreach ($mutualItems as $mutualItem) {
                $this->score += self::VALUE_DICTIONARY[$mutualItem];
            }
        }
    }

    public function getScore(): int
    {
        return $this->score;
    }
}

$filepath = __DIR__ . DIRECTORY_SEPARATOR . 'input.txt';
$backpackReorganizer = new BackpackReorganizer($filepath);
$backpackReorganizer->organize();

echo $backpackReorganizer->getScore() . PHP_EOL;
