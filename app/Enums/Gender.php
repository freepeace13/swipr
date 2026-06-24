<?php

namespace App\Enums;

enum Gender: string
{
    case Man = 'man';
    case Woman = 'woman';
    case NonBinary = 'non_binary';
    case GenderFluid = 'gender_fluid';
    case GenderQueer = 'gender_queer';
    case Agender = 'agender';
    case Bigender = 'bigender';
    case TwoSpirit = 'two_spirit';
    case TransMan = 'trans_man';
    case TransWoman = 'trans_woman';
    case Intersex = 'intersex';
    case PreferNotToSay = 'prefer_not_to_say';
    case SelfDescribe = 'self_describe';

    public function label(): string
    {
        return match ($this) {
            self::Man => 'Man',
            self::Woman => 'Woman',
            self::NonBinary => 'Non-binary',
            self::GenderFluid => 'Gender fluid',
            self::GenderQueer => 'Genderqueer',
            self::Agender => 'Agender',
            self::Bigender => 'Bigender',
            self::TwoSpirit => 'Two-spirit',
            self::TransMan => 'Trans man',
            self::TransWoman => 'Trans woman',
            self::Intersex => 'Intersex',
            self::PreferNotToSay => 'Prefer not to say',
            self::SelfDescribe => 'Self-describe',
        };
    }

    /** @return InterestedIn[] */
    public function compatibleInterestedIn(): array
    {
        return match ($this) {
            self::Man, self::TransMan => [
                InterestedIn::Men, InterestedIn::Everyone, InterestedIn::PreferNotToSay,
            ],
            self::Woman, self::TransWoman => [
                InterestedIn::Women, InterestedIn::Everyone, InterestedIn::PreferNotToSay,
            ],
            self::NonBinary, self::GenderFluid, self::GenderQueer,
            self::Agender, self::Bigender, self::TwoSpirit => [
                InterestedIn::NonBinaryPeople, InterestedIn::Everyone, InterestedIn::PreferNotToSay,
            ],
            self::Intersex, self::PreferNotToSay, self::SelfDescribe => [
                InterestedIn::Everyone, InterestedIn::PreferNotToSay,
            ],
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
