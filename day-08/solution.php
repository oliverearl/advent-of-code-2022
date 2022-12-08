<?php declare(strict_types=1);

class TreeTops
{
    /** @var array<int, array<int, string>> */
    private readonly array $forest;

    public function __construct(string $filepath)
    {
        // Create a 3D array of the input data:
        $this->forest = array_map(
            callback: static fn (string $line): array => str_split($line),
            array: file($filepath, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES),
        );
    }

    public function part_1(): int
    {
        $visible = 0;

        foreach ($this->forest as $i => $row) {
            foreach ($row as $j => $tree) {
                if ($this->isVisible($i, $j, (int) $tree)) {
                    $visible++;
                }
            }
        }

        return $visible;
    }

    public function part_2(): int
    {
        $score = 0;

        foreach ($this->forest as $i => $row) {
            foreach ($row as $j => $tree) {
                $score = max($score, $this->calculateScore($i, $j, (int) $tree));
            }
        }

        return $score;
    }

    private function calculateScore(int $i, int $j, int $height): int
    {
        $scores = [];
        $neighbourhoods = $this->getNeighbourhoods($i, $j);

        foreach ($neighbourhoods as $neighbourhood) {
            $score = 0;
            $neighbourhood = array_reverse($neighbourhood);

            foreach ($neighbourhood as $neighbour) {
                $score++;

                if ($neighbour >= $height) {
                    break;
                }
            }

            $scores[] = $score;
        }

        return array_product($scores);
    }

    private function isVisible(int $i, int $j, int $height): bool
    {
        // Check first whether the tree is on the edges of the forest:
        if (
            $i === 0 || // Top-most row
            $j === 0 || // Left-most row
            $i === count($this->forest) - 1 || // Bottom-most row
            $j === count($this->forest[0]) - 1 // Right-most row
        ) {
            return true;
        }

        // Otherwise, we'll need to check the neighbourhood rows in each direction for visibility:
        $hiddenSides = 0;
        $neighbourhoods = $this->getNeighbourhoods($i, $j);

        foreach ($neighbourhoods as $neighbourhood) {
            foreach ($neighbourhood as $neighbour) {
                if ($neighbour >= $height) {
                    $hiddenSides++;
                    break;
                }
            }
        }

        // We only return false if it's not visible from all four sides.
        return $hiddenSides !== 4;
    }

    /** @return array<int, array<int, string>> */
    private function getNeighbourhoods(int $i, int $j): array
    {
        return [
            $this->getUp($i, $j),
            $this->getDown($i, $j),
            $this->getLeft($i, $j),
            $this->getRight($i, $j),
        ];
    }

    /** @return array<int, string> */
    private function getUp(int $i, int $j): array
    {
        $result = [];

        for($k = 0; $k < $i; $k++) {
            $result[] = $this->forest[$k][$j];
        }

        return $result;
    }

    /** @return array<int, string> */
    private function getDown(int $i, int $j): array
    {
        $result = [];

        for($k = count($this->forest) - 1; $k > $i; $k--) {
            $result[] = $this->forest[$k][$j];
        }

        return $result;
    }

    /** @return array<int, string> */
    private function getLeft(int $i, int $j): array
    {
        return array_slice($this->forest[$i], 0, $j);
    }

    /** @return array<int, string> */
    private function getRight(int $i, int $j): array
    {
        return array_reverse(array_slice($this->forest[$i], $j + 1));
    }
}

$filepath = __DIR__ . DIRECTORY_SEPARATOR . 'input.txt';
$treeTops = new TreeTops($filepath);

// Task 1:
echo $treeTops->part_1() . PHP_EOL;

// Task 2:
echo $treeTops->part_2() . PHP_EOL;
