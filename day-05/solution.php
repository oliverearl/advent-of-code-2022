<?php declare(strict_types=1);

class SupplyStacker
{
    private readonly string $rawMatrix;
    private readonly string $rawInstructions;

    private array $matrix;
    private array $instructions;

    public function __construct(string $filepath)
    {
        [$this->rawMatrix, $this->rawInstructions] = explode("\n\n", file_get_contents($filepath));

        $this->matrix = $this->parseMatrix($this->rawMatrix);
        $this->instructions = $this->parseMoves($this->rawInstructions);
    }

    public function calculate(bool $part2 = false): string
    {
        foreach ($this->instructions as $step) {
            $quantity = $step['move'];
            $from = $step['from'];
            $to = $step['to'];

            $elements = array_slice($this->matrix[$from], - (int) $quantity);

            if (! $part2) {
                krsort($elements, SORT_NUMERIC);
            }

            array_splice($this->matrix[$from], count($this->matrix[$from]) - $quantity, (int) $quantity);
            $this->matrix[$to] = array_merge($this->matrix[$to], $elements);
        }

        $message = '';
        foreach ($this->matrix as $row){
            $message .= end($row);
        }

        return $message;
    }

    private function parseMatrix(string $matrix): array
    {
        $lines = explode(PHP_EOL, $matrix);
        $result = [];

        foreach ($lines as $line) {
            $lineCharacters = str_split($line, 4);

            foreach ($lineCharacters as $row => $characters)  {
                ++$row;

                preg_match('/[a-zA-Z]+/', $characters, $letter);
                $value = reset($letter);

                if (! empty($value)) {
                    ! empty($result[$row])
                        ? array_unshift($result[$row], $value)
                        : $result[$row][] = $value;
                }
            }
        }

        ksort($result, SORT_NUMERIC);

        return $result;
    }

    private function parseMoves(string $raw): array
    {
        $lines = explode(PHP_EOL, $raw);
        $result = [];

        foreach ($lines as $lineNumber => $line) {
            preg_match_all('/(.*?)\s\d+/', $line, $orders);

            foreach (reset($orders) as $order) {
                [$move, $step] = explode(' ', trim($order));
                $result[$lineNumber][$move] = $step;
            }
        }

        return $result;
    }
}

$filepath = __DIR__ . DIRECTORY_SEPARATOR . 'input.txt';
$stacker = new SupplyStacker($filepath);

// Task 1:
echo $stacker->calculate() . PHP_EOL;

// Task 2:
$stacker = new SupplyStacker($filepath);
echo $stacker->calculate(true) . PHP_EOL;
