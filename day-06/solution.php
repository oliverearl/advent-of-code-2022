<?php declare(strict_types=1);

class Tuner
{
    private readonly string $input;

    public function __construct(string $filepath)
    {
        $this->input = file_get_contents($filepath);
    }

    public function calculate(bool $part2 = false): ?int
    {
        $length = $part2 ? 14 : 4;

        for ($i = 0, $iMax = strlen($this->input); $i < $iMax; $i++) {
            if (! preg_match('/([a-z]).*?\1/', substr($this->input, $i, $length))) {
                return $length + $i;
            }
        }

        return null;
    }
}

$filepath = __DIR__ . DIRECTORY_SEPARATOR . 'input.txt';
$tuner = new Tuner($filepath);

// Part 1
echo $tuner->calculate() . PHP_EOL;

// Part 2
echo $tuner->calculate(true) . PHP_EOL;
