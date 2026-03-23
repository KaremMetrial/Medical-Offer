<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case CREDIT = 'credit';
    case DEBIT = 'debit';
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREDIT => __('message.credit'),
            self::DEBIT => __('message.debit'),
            self::DEPOSIT => __('message.deposit'),
            self::WITHDRAW => __('message.withdraw'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::CREDIT, self::DEPOSIT => 'success',
            self::DEBIT, self::WITHDRAW => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::CREDIT, self::DEPOSIT => 'heroicon-o-plus-circle',
            self::DEBIT, self::WITHDRAW => 'heroicon-o-minus-circle',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function options(): array
    {
        return array_map(fn($case) => ['value' => $case->value, 'label' => $case->getLabel()], self::cases());
    }

    public static function optionsWithSelected($selected): array
    {
        $options['items'] = self::options();
        $options['selected'] = $selected;
        $options['selected_label'] = $selected ? self::getLabelByValue($selected) : null;
        $options['label'] = __('message.transaction_type');
        $options['key'] = 'transaction_type';
        return $options;
    }

    public static function getLabelByValue($value): ?string
    {
        $case = self::tryFrom($value);
        return $case ? $case->getLabel() : null;
    }
}