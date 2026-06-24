<?php

namespace App\Enums;

enum InterestedIn: string
{
    case Men = 'men';
    case Women = 'women';
    case NonBinaryPeople = 'non_binary_people';
    case Everyone = 'everyone';
    case PreferNotToSay = 'prefer_not_to_say';

    public function label(): string
    {
        return match ($this) {
            self::Men => 'Men',
            self::Women => 'Women',
            self::NonBinaryPeople => 'Non-binary people',
            self::Everyone => 'Everyone',
            self::PreferNotToSay => 'Prefer not to say',
        };
    }

    public static function default(): self
    {
        return self::PreferNotToSay;
    }

    /** @return Gender[] */
    public function compatibleGenders(): array
    {
        return match ($this) {
            self::Men => [Gender::Man, Gender::TransMan],
            self::Women => [Gender::Woman, Gender::TransWoman],
            self::NonBinaryPeople => [
                Gender::NonBinary, Gender::GenderFluid, Gender::GenderQueer,
                Gender::Agender, Gender::Bigender, Gender::TwoSpirit,
            ],
            self::Everyone, self::PreferNotToSay => Gender::cases(),
        };
    }

    public static function toSelectOptions(): array
    {
        return array_map(
            fn ($case) => [
                'value' => $case->value,
                'label' => $case->label(),
            ],
            self::cases()
        );
    }
}
