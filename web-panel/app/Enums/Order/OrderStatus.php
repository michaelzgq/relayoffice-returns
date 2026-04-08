<?php

namespace App\Enums\Order;


use function App\CPU\translate;

enum OrderStatus : string
{
    case COMPLETED = 'completed';
    case REFUNDED = 'refunded';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::COMPLETED => translate('Completed'),
            self::REFUNDED => translate('Refunded'),
        };
    }

    public static function fromValue(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
