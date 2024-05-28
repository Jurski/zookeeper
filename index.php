<?php

require_once 'vendor/autoload.php';

use App\Animal;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Respect\Validation\Validator as v;

$log = new Logger('zookeeper');
$log->pushHandler(new StreamHandler(__DIR__ . "/logs/zookeeper.log", Logger::DEBUG));

$animals = [
    new Animal('Lion', 'meat', $log),
    new Animal('Monkey', 'banana', $log),
    new Animal('Panda', 'bamboo', $log)
];

function getValidatedInput(object $validator, string $prompt): string
{
    while (true) {
        $input = readline($prompt);
        if ($validator->validate($input)) {
            return $input;
        } else {
            echo "Input is invalid!" . PHP_EOL;
        }
    }
}

while (true) {
    echo "Select an animal: " . PHP_EOL;

    foreach ($animals as $index => $animal) {
        echo "$index: " . $animal->getName() . PHP_EOL;
    }

    $selectedIndex = getValidatedInput(
        v::intVal()->between(0, count($animals) - 1),
        "Enter the number of animal: "
    );

    $selectedAnimal = $animals[$selectedIndex];

    echo "What would you like to do with " . $selectedAnimal->getName() . "?\n";
    echo "1: Play\n";
    echo "2: Work\n";
    echo "3: Pet\n";
    echo "4: Feed\n";
    echo "0: Exit\n";

    $action = getValidatedInput(
        v::intVal()->between(0, 4),
        "Enter the number of the action: "
    );

    switch ($action) {
        case 1:
            $time = getValidatedInput(
                v::intVal()->positive(),
                "Enter play time in seconds: "
            );
            echo $selectedAnimal->play($time);
            break;
        case 2:
            echo $selectedAnimal->work();
            break;
        case 3:
            echo $selectedAnimal->pet();
            break;
        case 4:
            $food = getValidatedInput(
                v::alpha()->notEmpty(),
                "Enter the type of food: "
            );
            echo $selectedAnimal->feed($food);
            break;
        case 0:
            exit("Exiting..\n");
        default:
            echo "Invalid action. Try again.\n";
    }
}