<?php declare(strict_types=1);

class SupplyStacker
{
    private readonly array $input;
    private array $stacks = [];

    public function __construct(string $filepath)
    {
        $this->input = file($filepath);
        $this->parseStacks();
        $this->shiftStacks();
    }

    public function parseStacks(): void
    {
        foreach ($this->input as $line) {
            $splitLine = str_split($line);

            // Check for the blank line between the stacks and instructions.
            if (count($splitLine) === 1) {
                return;
            }

            foreach ($splitLine as $index => $character) {
                $index += 3; // Two plus one for zero-index.
                if ($index % 4 === 0 && ctype_upper($character)) {
                    $key = $index / 4;

                    if (empty($this->stacks[$key])) {
                        $this->stacks[$key] = new SplStack();
                    }

                    $this->stacks[$key]->push($character);
                }
            }
        }
    }

    public function shiftStacks(): void
    {
        foreach ($this->input as $line) {
            if (! str_contains($line, 'move')) {
                continue;
            }

            // Using array merge this way resets the numerical keys, so we can use destructuring... interesting right?
            $values = array_merge(array_filter(explode(',', preg_replace('~\D~', ',', $line))));
            [$quantity, $source, $destination] = $values;

            for ($i = 0; $i < $quantity; $i++) {
                $value = $this->stacks[$source]->pop();
                $this->stacks[$destination]->push($value);
            }
        }
    }

    public function getTopEntries(): string
    {
        // Copy to a local variable as not to mess with the ordering of the original.
        $stacks = $this->stacks;
        ksort($stacks, SORT_NUMERIC);

        $entries = '';
        /** @var \SplStack $stack */
        foreach ($stacks as $stack) {
            $entries .= $stack->bottom();
        }

        return $entries;
    }

    public function getStacks(): array
    {
        $stacks = $this->stacks;
        ksort($stacks, SORT_NUMERIC);

        return $stacks;
    }
}

$filepath = __DIR__ . DIRECTORY_SEPARATOR . 'input.txt';
$stacker = new SupplyStacker($filepath);

// Task 1:
echo $stacker->getTopEntries() . PHP_EOL;
