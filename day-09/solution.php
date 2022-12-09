<?php declare(strict_types=1);

class RopeBridge
{
    private const MOVE_RIGHT = 'R';
    private const MOVE_LEFT = 'L';
    private const MOVE_UP = 'U';
    private const MOVE_DOWN = 'D';

    /** @var array<int, string> */
    private readonly array $input;

    /** @var array<int, array<int, int>> */
    private array $part1Visited = [];

    /** @var array<int, array<int, int>> */
    private array $part2Visited = [];

    public function __construct(string $filepath)
    {
        $this->input = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $this->calculate();
    }

    public function calculate(): void
    {
        // Initialise the 3D grid:
        $grid = array_fill(0, 10, [0, 0]);

        // Begin parsing input:
        foreach ($this->input as $line) {
            [$direction, $quantity] = explode(' ' , $line);

            // First, move the head:
            for ($i = 0; $i < $quantity; $i++) {
                switch ($direction) {
                    case self::MOVE_RIGHT:
                        $grid[0][0]++;
                        break;

                    case self::MOVE_LEFT:
                        $grid[0][0]--;
                        break;

                    case self::MOVE_UP:
                        $grid[0][1]++;
                        break;

                    case self::MOVE_DOWN:
                        $grid[0][1]--;
                        break;
                }

                // Then the following code determines whether we need to make a tail movement:
                $previous = $grid[0];
                for ($j = 1, $jMax = count($grid); $j < $jMax; $j++) {
                    // First, determine whether the head and tail are within acceptable distance:
                    $currentPosition = $grid[$j];
                    $adjacentTiles = [];

                    for ($k = $currentPosition[0] - 1; $k < $currentPosition[0] + 2; $k++) {
                        for ($l = $currentPosition[1] - 1; $l < $currentPosition[1] + 2; $l++) {
                            $adjacentTiles[] = [$k, $l];
                        }
                    }

                    if (in_array($previous, $adjacentTiles, true)) {
                        break;
                    }

                    // Otherwise, move the tail:
                    $grid[$j][0] += min(max((int) $previous[0] - $grid[$j][0], -1), 1);
                    $grid[$j][1] += min(max((int) $previous[1] - $grid[$j][1], -1), 1);

                    // And record this turn:
                    $previous = $grid[$j];
                }

                // Finally, make a note of visited positions:
                $this->part1Visited[] = $grid[1];
                $this->part2Visited[] = $grid[array_key_last($grid)];
            }
        }
    }
    public function getPart1(): int
    {
        return count(array_unique($this->part1Visited, SORT_REGULAR));
    }

    public function getPart2(): int
    {
        return count(array_unique($this->part2Visited, SORT_REGULAR));
    }
}

$filepath = __DIR__ . DIRECTORY_SEPARATOR . 'input.txt';
$bridge = new RopeBridge($filepath);

// Part 1:
echo $bridge->getPart1() . PHP_EOL;

// Part 2:
echo $bridge->getPart2() . PHP_EOL;
