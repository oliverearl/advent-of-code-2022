<?php declare(strict_types=1);

abstract class Node
{
    protected ?int $size = null;

    abstract public function getSize(): int;
}

class DirectoryNode extends Node
{
    protected string $name;

    protected ?DirectoryNode $parent;

    /** @var array<int, FileNode> */
    protected array $files = [];

    /** @var array<int, DirectoryNode>  */
    protected array $directories = [];

    public function __construct(string $name, ?DirectoryNode $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    public function getSize(): int
    {
        if ($this->size !== null) {
            return $this->size;
        }

        $this->size = array_reduce(
            array: $this->files,
            callback: static fn (int $size, FileNode $file): int => $size + $file->getSize(),
            initial: 0,
        );

        $this->size = array_reduce(
            array: $this->directories,
            callback: static fn (int $size, DirectoryNode $directory): int => $size + $directory->getSize(),
            initial: $this->size,
        );

        return $this->size;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParent(): ?DirectoryNode
    {
        return $this->parent;
    }

    public function getDirectories(): array
    {
        return $this->directories;
    }

    public function addFile(FileNode $file): self
    {
        $this->files[$file->getName()] = $file;

        return $this;
    }

    public function addDirectory(DirectoryNode $directory): self
    {
        $this->directories[$directory->getName()] = $directory;

        return $this;
    }
}

class FileNode extends Node
{
    protected string $name;

    protected DirectoryNode $parent;

    public function __construct(string $name, DirectoryNode $parent, int $size)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->size = $size;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParent(): DirectoryNode
    {
        return $this->parent;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}

class FreeSpaceCalculator
{
    final public const TOTAL_DISK_SPACE_IN_BYTES = 70_000_000;
    final public const REQUIRED_DISK_SPACE_IN_BYTES = 30_000_000;

    /** @var array<int, string> */
    private readonly array $input;

    /** @var array<int, \DirectoryNode> */
    private array $directories;

    private DirectoryNode $rootNode;

    public function __construct(string $filepath)
    {
        $this->input = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $this->parse();

        $this->rootNode->getSize();
    }

    public function parse(): void
    {
        /** @var null|DirectoryNode $currentDirectory */
        $currentDirectory = null;

        foreach ($this->input as $input) {
            // Command to move to the absolute most root directory:
            if ($input === '$ cd /') {
                $currentDirectory = new DirectoryNode('');

                $this->rootNode = $currentDirectory;
                $this->directories[] = $currentDirectory;
            // Parsing a file and its filesize:
            } elseif (preg_match('/^(?<size>\d+) (?<name>.*)$/', $input, $matches)) {
                $file = new FileNode(
                    name: $matches['name'],
                    parent: $currentDirectory,
                    size: (int) $matches['size'],
                );

                $currentDirectory->addFile($file);
            // Parsing a directory:
            } elseif (preg_match('/^dir (?<name>.*)$/', $input, $matches)) {
                $directory = new DirectoryNode(name: $matches['name'], parent: $currentDirectory);

                $currentDirectory->addDirectory($directory);
                $this->directories[] = $directory;
            // Parsing a CD operation to an enclosed directory:
            } elseif ((preg_match('/^\$ cd (?<name>.*)$/', $input, $matches))) {
                // Special case for moving up a level:
                if ($matches['name'] === '..') {
                    $currentDirectory = $currentDirectory->getParent();
                } else {
                    $directories = $currentDirectory->getDirectories();
                    $currentDirectory = $directories[$matches['name']];
                }
            }
        }
    }

    public function part_1(): int
    {
        $answer = 0;

        foreach ($this->directories as $directory) {
            $size = $directory->getSize();

            if ($size <= 100_000) {
                $answer += $size;
            }
        }

        return $answer;
    }

    public function part_2(): int
    {
        $deletionReq = self::REQUIRED_DISK_SPACE_IN_BYTES - self::TOTAL_DISK_SPACE_IN_BYTES + $this->rootNode->getSize();
        $answer = PHP_INT_MAX;

        foreach ($this->directories as $directory) {
            $size = $directory->getSize();

            if ($size >= $deletionReq) {
                $answer = min($answer, $size);
            }
        }

        return $answer;
    }
}

$filepath = __DIR__ . DIRECTORY_SEPARATOR . 'input.txt';
$calculator = new FreeSpaceCalculator($filepath);

// Part 1:
echo $calculator->part_1() . PHP_EOL;

// Part 2:
echo $calculator->part_2() . PHP_EOL;
