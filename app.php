<?php

require_once 'vendor/autoload.php';

use App\Hebb;
use App\HebbMethod;

function generateDataTraining($n): array
{
    $dataTraining = [
        'inputs' => [],
        'outputs' => [],
        'combos' => [],
    ];

    $combinations = [];
    $max = pow(2, $n);

    for ($i = 0; $i < $max; $i++) {
        $combination = [];

        for ($j = 0; $j < $n; $j++) {
            $combination[] = (bool)($i & (1 << $j));
        }

        $combinations[] = $combination;
        $dataTraining['combos'][] = $combination;
    }

    foreach ($combinations as $combination) {
        $output = array_reduce($combination, fn ($carry, $item) => $carry || $item, false);
        $dataTraining['inputs'][] = [
            0, // bias
            ...$combination,
        ];
        $dataTraining['outputs'][] = $output;
    }

    return $dataTraining;
}

$method = HebbMethod::BIPOLAR;
$dataTraining = generateDataTraining(3);

Hebb::training(
    $dataTraining['inputs'],
    $dataTraining['outputs'],
    $method,
);

Hebb::recognize($method, [
    [true, true],
    [true, false],
    [false, true],
    [false, false],
]);
