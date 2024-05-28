<?php

namespace App;

use Monolog\Logger;

class Animal
{
    private Logger $logger;
    private string $name;
    private string $favoriteFood;
    private int $happiness = 50;
    private int $energy = 100;
    private int $energyExpendPerAction = 10;

    public function __construct(string $name, string $favoriteFood, Logger $logger)
    {
        $this->favoriteFood = $favoriteFood;
        $this->name = $name;
        $this->logger = $logger;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function showAnimalStats(string $action): string
    {
        return "$this->name has $action \n Happiness: $this->happiness \n Energy: $this->energy \n";
    }

    public function play(string $time): string
    {
        $thresholdToIncreaseEnergy = 5;
        $maxAllowedPlaytime = ($this->energy / $this->energyExpendPerAction) * $thresholdToIncreaseEnergy;

        if ($time > $maxAllowedPlaytime) {
            $this->logger->warning("Not enough energy to play that long - max allowed time: $maxAllowedPlaytime seconds");
            return "Not enough energy to play that long - max allowed time: $maxAllowedPlaytime seconds\n";
        }


        if ($time % $thresholdToIncreaseEnergy !== 0) {
            $multiplier = (int)($time / $thresholdToIncreaseEnergy);
        } else {
            $multiplier = $time / $thresholdToIncreaseEnergy;
        }

        $this->happiness += 5 * $multiplier;
        $this->energy -= $this->energyExpendPerAction * $multiplier;

        $this->logger->info("$this->name played. Happiness: $this->happiness, Energy: $this->energy");

        return $this->showAnimalStats("played");
    }

    public function work(): string
    {
        if ($this->energy - 10 < 10) {
            $this->logger->warning("Not enough energy to work!");
            return "Not enough energy($this->energy) to work, needs at least 10!\n";
        }
        $this->happiness -= 10;
        $this->energy -= 10;

        $this->logger->info("$this->name worked. Happiness: $this->happiness, Energy: $this->energy");

        return $this->showAnimalStats("worked");
    }

    public function pet(): string
    {
        $this->happiness += 5;

        $this->logger->info("$this->name got petted. Happiness: $this->happiness");

        return "You've petted $this->name, its happiness is now $this->happiness\n";
    }

    public function feed(string $food): string
    {
        if (trim(strtolower($food)) === $this->favoriteFood) {
            $this->energy += 20;

            $this->logger->info("$this->name got fed with $food. Energy now is: $this->energy");

            return "You fed $this->name with its favourite food - $food. Energy now is: $this->energy\n";
        } else {
            $this->energy -= 40;
            $this->happiness -= 20;

            $this->logger->warning("Didn't feed favourite food. Energy and happines down. $this->energy and $this->happiness");

            $animalStats = $this->showAnimalStats("been fed");
            return "Didnt feed favourite food: $this->favoriteFood, energy and happiness decreased. \n" . $animalStats;
        }
    }
}