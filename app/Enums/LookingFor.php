<?php

namespace App\Enums;

enum LookingFor: string
{
    // Relationship types
    case LongTermRelationship = 'long_term_relationship';
    case ShortTermRelationship = 'short_term_relationship';
    case MarriageMinded = 'marriage_minded';
    case Casual = 'casual';
    case OpenRelationship = 'open_relationship';

    // Connection types
    case Friendship = 'friendship';
    case NetworkingPenPal = 'networking_penpal';
    case ActivityPartner = 'activity_partner';

    // Exploratory
    case StillFiguringItOut = 'still_figuring_it_out';
    case OpenToAnything = 'open_to_anything';

    public function label(): string
    {
        return match ($this) {
            self::LongTermRelationship => 'Long-term relationship',
            self::ShortTermRelationship => 'Short-term relationship',
            self::MarriageMinded => 'Marriage-minded',
            self::Casual => 'Casual dating',
            self::OpenRelationship => 'Open relationship',
            self::Friendship => 'Friendship',
            self::NetworkingPenPal => 'Networking / pen pal',
            self::ActivityPartner => 'Activity partner',
            self::StillFiguringItOut => 'Still figuring it out',
            self::OpenToAnything => 'Open to anything',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::LongTermRelationship => 'Looking for something serious and lasting',
            self::ShortTermRelationship => 'Open to dating without long-term commitment',
            self::MarriageMinded => 'Actively looking toward marriage',
            self::Casual => 'Keeping things fun and low-pressure',
            self::OpenRelationship => 'Interested in ethical non-monogamy',
            self::Friendship => 'Just looking to meet new people',
            self::NetworkingPenPal => 'Professional connections or long-distance chat',
            self::ActivityPartner => 'Someone to share hobbies or activities with',
            self::StillFiguringItOut => 'Not sure yet, exploring options',
            self::OpenToAnything => 'No expectations, just seeing what happens',
        };
    }

    public static function default(): self
    {
        return self::OpenToAnything;
    }

    public static function toSelectOptions(): array
    {
        return array_map(
            fn ($case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'description' => $case->description(),
            ],
            self::cases()
        );
    }
}
