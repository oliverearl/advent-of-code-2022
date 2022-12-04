<?php declare(strict_types=1);

class CleanupOrganizer
{
    private readonly array $input;

    private int $redundantPairs = 0;

    private int $overlappingPairs = 0;

    public function __construct(string $filepath)
    {
        $this->input = file($filepath);
    }

    public function task_1(): void
    {
        foreach ($this->input as $line) {
            $pairs = explode(',', $line);

            $firstRange = explode('-', reset($pairs));
            $secondRange = explode('-', end($pairs));

            $firstMin = reset($firstRange);
            $secondMin = reset($secondRange);

            $firstMax = end($firstRange);
            $secondMax = end($secondRange);

            if (($firstMin <= $secondMin && $firstMax >= $secondMax) ||
                ($secondMin <= $firstMin && $secondMax >= $firstMax)) {
                $this->redundantPairs++;
            }
        }
    }

    public function task_2(): void
    {
        foreach ($this->input as $line) {
            $pairs = explode(',', $line);

            $firstRange = explode('-', reset($pairs));
            $secondRange = explode('-', end($pairs));

            $overlappingValues = array_intersect(
                range(reset($firstRange), end($firstRange)),
                range(reset($secondRange), end($secondRange)),
            );

            if (empty($overlappingValues)) {
                continue;
            }

            $this->overlappingPairs++;
        }
    }

    public function getRedundantPairs(): int
    {
        return $this->redundantPairs;
    }

    public function getOverlappingPairs(): int
    {
        return $this->overlappingPairs;
    }
}

$organizer = new CleanupOrganizer(__DIR__ . DIRECTORY_SEPARATOR . 'input.txt');

// First task:
$organizer->task_1();
echo $organizer->getRedundantPairs() . PHP_EOL;

// Second task:
$organizer->task_2();
echo $organizer->getOverlappingPairs() . PHP_EOL;
