<?php

namespace App;

class Hebb
{
    public const EPOCH = 100;
    public static ?array $weights = null;

    public static function convert(int $method, bool $value): float|int
    {
        return match ($method) {
            HebbMethod::BINARY => $value === true ? 1 : 0,
            HebbMethod::BIPOLAR => $value === true ? 1 : -1,
            default => throw new \InvalidArgumentException('Metode HEBB tidak valid'),
        };
    }

    public static function training(array $inputs, array $outputs, int $method, bool $overwrite = false): void
    {
        if (count($inputs) !== count($outputs)) {
            throw new \InvalidArgumentException('Input dan output tidak sesuai');
        }

        if (!empty(static::$weights) && !$overwrite) {
            return;
        }

        for ($e = 0; $e < static::EPOCH; $e++) {
            foreach ($inputs as $i => $rawInput) {
                $output = static::convert($method, $outputs[$i]);

                foreach ($rawInput as $j => $input) {
                    if ($j > 0) {
                        $input = static::convert($method, $input);
                    }

                    static::$weights[$j] ??= 0;
                    static::$weights[$j] += $input * $output;
                }
            }
        }
    }

    public static function recognize(int $method, array $inputs): void
    {
        $nets = [];
        $results = [];
        $bias = static::$weights[0];

        foreach ($inputs as $i => $rawInput) {
            $nets[$i] ??= 0;

            foreach ($rawInput as $j => $input) {
                $weight = (static::$weights[$j + 1] ??= 0);
                $nets[$i] += static::convert($method, $input) * $weight;
            }

            $nets[$i] += $bias;
            $results[$i] = static::convert($method, $nets[$i] >= 0);
        }


        $methodName = match ($method) {
            HebbMethod::BINARY => 'BINARY',
            HebbMethod::BIPOLAR => 'BIPOLAR',
        };

        echo "\n\n";
        echo "=========================\n";
        echo "Metode HEBB: $methodName\n";
        echo "=========================\n\n";

        foreach ($results as $i => $result) {
            echo sprintf(
                "Hasil #%s\nInput: (%s)\nOutput: %s\n\n",
                $i + 1,
                implode(', ', array_map(fn ($input) => static::convert($method, $input), $inputs[$i])),
                $result
            );
        }
    }
}
