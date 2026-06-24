<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\InterestedIn;
use App\Enums\LookingFor;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravolt\Avatar\Facade as AvatarFacade;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'birthdate',
        'gender',
        'looking_for',
        'interested_in',
        'min_age_preference',
        'max_age_preference',
        'flexible_on_age',
        'last_seen_at'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gender' => Gender::class,
            'looking_for' => LookingFor::class,
            'interested_in' => InterestedIn::class,
            'birthdate' => 'date',
            'last_seen_at' => 'datetime',
            'flexible_on_age' => 'boolean',
        ];
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => Carbon::parse($attributes['birthdate'])->age
        );
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $value
                ? Storage::disk('public')->url($value)
                : 'data:image/svg+xml;base64,' . base64_encode(AvatarFacade::create($attributes['name'])->toSvg())
        );
    }

    #[Scope]
    protected function matchesByGender(Builder $query, User $me): void
    {
        $compatibleGenders = array_map(fn ($g) => $g->value, $me->interested_in->compatibleGenders());
        $candidateInterestedIn = array_map(fn ($i) => $i->value, $me->gender->compatibleInterestedIn());

        $query
            ->whereIn('users.gender', $compatibleGenders)
            ->whereIn('users.interested_in', $candidateInterestedIn);
    }

    #[Scope]
    protected function matchesByAge(Builder $query, User $me): void
    {
        $myAge = $me->age;
        $myBuffer = $me->flexible_on_age ? 3 : 0;
        $today = now()->toDateString();

        $minPref = max(18, $me->min_age_preference - $myBuffer);
        $maxPref = $me->max_age_preference + $myBuffer;

        $query
            ->whereRaw('TIMESTAMPDIFF(YEAR, birthdate, ?) BETWEEN ? AND ?', [$today, $minPref, $maxPref])
            ->where(function ($q) use ($myAge) {
                $q->where(function ($inner) use ($myAge) {
                    $inner->where('min_age_preference', '<=', $myAge)
                        ->where('max_age_preference', '>=', $myAge);
                })->orWhere(function ($inner) use ($myAge) {
                    $inner->where('flexible_on_age', true)
                        ->whereRaw('min_age_preference - 3 <= ?', [$myAge])
                        ->whereRaw('max_age_preference + 3 >= ?', [$myAge]);
                });
            });
    }

    #[Scope]
    protected function rankedByInterestMatch(Builder $query, User $me): void
    {
        $myInterests  = $me->interestIds();
        $myCategories = $me->interestCategories();

        $query->select('users.*');

        $caseParts = [];
        $bindings  = [];

        if (!empty($myInterests)) {
            $ph = implode(',', array_fill(0, count($myInterests), '?'));
            $caseParts[] = "WHEN ui.interest_id IN ({$ph}) THEN 2";
            array_push($bindings, ...$myInterests);
        }

        if (!empty($myCategories)) {
            $ph = implode(',', array_fill(0, count($myCategories), '?'));
            $caseParts[] = "WHEN ui.category_id IN ({$ph}) THEN 1";
            array_push($bindings, ...$myCategories);
        }

        if (empty($caseParts)) {
            $bindings[] = $me->looking_for->value;

            $query
                ->selectRaw('CASE WHEN users.looking_for = ? THEN 5 ELSE 0 END AS match_score', $bindings)
                ->orderByDesc('match_score');
            return;
        }

        $caseExpr = 'CASE ' . implode(' ', $caseParts) . ' ELSE 0 END';
        $bindings[] = $me->looking_for->value;

        $query
            ->selectRaw(
                "COALESCE(SUM({$caseExpr}), 0) + CASE WHEN users.looking_for = ? THEN 5 ELSE 0 END AS match_score",
                $bindings
            )
            ->leftJoin('user_interests as ui', 'ui.user_id', '=', 'users.id')
            ->groupBy('users.id')
            ->orderByDesc('match_score');
    }

    public function interestIds(): array
    {
        return $this->interests->pluck('id')->toArray();
    }

    public function interestCategories(): array
    {
        return $this->interests->pluck('category_id')->unique()->toArray();
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Interest::class,
            table: 'user_interests',
            foreignPivotKey: 'user_id',
            relatedPivotKey: 'interest_id',
        )->withPivot('weight');
    }
}
