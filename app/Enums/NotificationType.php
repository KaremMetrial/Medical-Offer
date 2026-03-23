<?php

namespace App\Enums;

enum NotificationType: string
{
    case GENERAL = 'general';
    case WITHDRAWAL = 'withdrawal';
    case CARD_REQUEST = 'card_request';
    case WALLET = 'wallet';
    case OTHER = 'other';

    public static function fromNotificationClass(string $class): self
    {
        return match (class_basename($class)) {
            'GeneralNotification' => self::GENERAL,
            'WithdrawalApproved', 'WithdrawalRejected' => self::WITHDRAWAL,
            'CardRequestApproved', 'CardRequestRejected' => self::CARD_REQUEST,
            'WalletTransaction' => self::WALLET,
            default => self::OTHER,
        };
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
        $options['label'] = __('message.notification_types');
        $options['key'] = 'notification_types';
        return $options;
    }
    public static function getLabelByValue($value): ?string
    {
        return match ($value) {
            self::GENERAL->value => __('message.general'),
            self::WITHDRAWAL->value => __('message.withdrawal'),
            self::CARD_REQUEST->value => __('message.card_request'),
            self::WALLET->value => __('message.wallet'),
            self::OTHER->value => __('message.other'),
            default => null,
        };
    }
}
