<?php declare(strict_types=1);

enum OpponentMove: string
{
    case ROCK = 'A';
    case PAPER = 'B';
    case SCISSORS = 'C';
}

enum PlayerMove: string
{
    private const ROCK_SCORE = 1;
    private const PAPER_SCORE = 2;
    private const SCISSORS_SCORE = 3;

    case ROCK = 'X';
    case PAPER = 'Y';
    case SCISSORS = 'Z';

    public function score(): int
    {
        return match($this) {
            self::ROCK => self::ROCK_SCORE,
            self::PAPER => self::PAPER_SCORE,
            self::SCISSORS => self::SCISSORS_SCORE,
        };
    }
}

enum Outcome: int
{
    case WIN = 6;
    case DRAW = 3;
    case LOSS = 0;
}

class RockPaperScissors
{
    private readonly array $input;

    private int $calculatedScore = 0;

    public function __construct(string $filepath)
    {
        $this->input = file($filepath);
        $this->calculate();
    }

    public function calculate(): void
    {
        foreach ($this->input as $line) {
            $turns = explode(' ', trim($line));

            $opponentMove = OpponentMove::tryFrom(reset($turns));
            $playerMove = PlayerMove::tryFrom(end($turns));

            $outcome = $this->performTurn($opponentMove, $playerMove);

            $this->calculatedScore += $playerMove->score();
            $this->calculatedScore += $outcome->value;
        }
    }

    public function getCalculatedScore(): int
    {
        return $this->calculatedScore;
    }

    private function performTurn(OpponentMove $opponentMove, PlayerMove $playerMove): Outcome
    {
        if ($playerMove === PlayerMove::ROCK) {
            return match($opponentMove) {
                OpponentMove::ROCK => Outcome::DRAW,
                OpponentMove::PAPER => Outcome::LOSS,
                OpponentMove::SCISSORS => Outcome::WIN,
            };
        } elseif ($playerMove === PlayerMove::PAPER) {
            return match($opponentMove) {
                OpponentMove::ROCK => Outcome::WIN,
                OpponentMove::PAPER => Outcome::DRAW,
                OpponentMove::SCISSORS => Outcome::LOSS,
            };
        } else {
            return match($opponentMove) {
                OpponentMove::ROCK => Outcome::LOSS,
                OpponentMove::PAPER => Outcome::WIN,
                OpponentMove::SCISSORS => Outcome::DRAW,
            };
        }
    }
}

$filepath = __DIR__ . DIRECTORY_SEPARATOR . 'input.txt';
$game = new RockPaperScissors($filepath);

// Part 1
echo $game->getCalculatedScore();