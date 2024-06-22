<?php

require_once 'vendor/autoload.php';

use App\Hebb;
use App\HebbMethod;

function generateDataTraining($n): array
{
    $dataTraining = [
        'inputs' => [],
        'outputs' => [],
    ];

    $combinations = [];
    $max = pow(2, $n);

    for ($i = 0; $i < $max; $i++) {
        $combination = [];

        for ($j = 0; $j < $n; $j++) {
            $combination[] = (bool)($i & (1 << $j));
        }

        $combinations[] = $combination;
    }

    foreach ($combinations as $combination) {
        $output = array_reduce($combination, fn($carry, $item) => $carry || $item, false);
        $dataTraining['inputs'][] = [
            0, // bias
            ...$combination,
        ];
        $dataTraining['outputs'][] = $output;
    }

    return $dataTraining;
}

$method = HebbMethod::BIPOLAR;

for ($i = 2; $i <= 10; $i++) {
    $dataTraining = generateDataTraining($i);

    Hebb::training(
        $dataTraining['inputs'],
        $dataTraining['outputs'],
        $method,
    );
}

Hebb::recognize($method, [
    [true, true],
    [false, true],
    [true, false],
    [false, false],
]);

Hebb::recognize($method, [
    [true, false, true],
    [false, false, false]
]);

Hebb::recognize($method, [
    [true, false, true, false],
    [false, false, false, false]
]);

Hebb::recognize($method, [
    [true, false, true, false, true],
    [false, false, false, false, false]
]);
