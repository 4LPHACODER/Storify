<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Concerns\ResolvesDefaultAvatar;
use App\Models\User;
use App\Services\Auth\RegistrationOtpService;
use App\Services\Phone\PhilippineMobileNormalizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules, ResolvesDefaultAvatar;

    public function __construct(
        private PhilippineMobileNormalizer $phoneNormalizer,
        private RegistrationOtpService $registrationOtpService,
    ) {}

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'phone_number' => $this->registrationPhoneRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $phoneNormalized = $this->phoneNormalizer->normalize((string) $input['phone_number']);
        $this->phoneNormalizer->assertValid($phoneNormalized);

        if (User::query()->where('phone_number', $phoneNormalized)->exists()) {
            throw ValidationException::withMessages([
                'phone_number' => __('This phone number is already registered.'),
            ]);
        }

        $avatar = $this->defaultAvatarUrl($input['name'] ?? null);

        if (($input['avatar'] ?? null) instanceof UploadedFile) {
            $avatar = ($input['avatar'])->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone_number' => $phoneNormalized,
            'password' => $input['password'],
            'role' => 'customer',
            'avatar' => $avatar,
        ]);

        $this->registrationOtpService->issueAndQueueSignupSms($user);

        return $user;
    }
}
