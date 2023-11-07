<?php

namespace Kohaku1907\Lara2step\Models\Concerns;


use Carbon\Carbon;
use Illuminate\Support\Collection;
use Kohaku1907\Lara2step\Notifications\TwoStepCodeEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HandlesAuth
{
    /**
     * Check if the code is numeric.
     *
     * @return Attribute
     */
    protected function codeLength(): Attribute
    {
        return new Attribute(
            get: fn (mixed $value, array $attributes) => strlen($attributes['code']),
        );
    }

    /**
     * Check if the code is numeric.
     *
     * @return Attribute
     */
    protected function isNumericCode(): Attribute
    {
        return new Attribute(
            get: fn (mixed $value, array $attributes) => ctype_digit($attributes['code']),
        );
    }

    /**
     * Generate a two-step code and save it.
     *
     * @param int $length The length of the code to generate.
     * @param bool $numbersOnly Whether to generate a numeric code only.
     * @return self
     */
    public function generateTwoStepCode($length, $numbersOnly = false) {
        $this->code = $this->generateCode($length, $numbersOnly);
        $this->count = 0;
        $this->save();
        return $this;
    }

    /**
     * Validate the provided code.
     *
     * @param string $code The code to validate.
     * @return bool True if the code is valid, false otherwise.
     */
    public function validateCode(string $code): bool
    {
        if ($this->code === $code) {
            $this->resetAttempt();
            return true;
        }

        $this->count++;
        $this->save();
        return false;
    }

    /**
     * Format verification exceeded timings with Carbon.
     *
     * @return Collection
     */
    public function getExceededData(): Collection
    {
        $time = $this->updated_at;
        $timeUntilUnlock = Carbon::parse($time)->addMinutes(config('2step.exceed_countdown_minutes'))->format('l, F jS Y h:i:sa');
        $timeCountdownUnlock = $time->addMinutes(config('2step.exceed_countdown_minutes'))->diffForHumans(null, true);

        $data = [
            'timeUntilUnlock'  => $timeUntilUnlock,
            'timeCountdownUnlock' => $timeCountdownUnlock,
        ];

        return collect($data);
    }

    /**
     * Check if time since account lock has expired and return true if account verification can be reset.
     *
     * @param \Datetime $time
     *
     * @return bool
     */
    public function checkExceededTime(): bool
    {
        $now = Carbon::now();
        $expire = Carbon::parse($this->updated_at)->addMinutes(config('2step.exceed_countdown_minutes'));
        $expired = $now->gt($expire);

        if ($expired) {
            return true;
        }

        return false;
    }

    public function isExceededMaxAttempts(): bool
    {
        return $this->count > config('2step.max_attempts');
    }

    /**
     * Reset attempt
     *
     * @return void
     */
    protected function resetAttempt(): void
    {
        $this->code = $this->generateCode($this->codeLength, $this->isNumericCode);
        $this->count = 0;
        $this->verified_at = Carbon::now();
        $this->request_at = null;

        $this->save();
    }

    /**
     * Get the remaining attempts.
     *
     * @return int The number of remaining attempts.
     */
    public function getRemainingAttempts(): int
    {
        return config('2step.max_attempts') - $this->count;
    }


    /**
     * Generate a code of a given length.
     *
     * @param int $length The length of the code to generate.
     * @param bool $numbersOnly Whether to generate a numeric code only.
     * @return string The generated code.
     */
    private function generateCode(int $length, bool $numbersOnly = false): string
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            if ($numbersOnly)
                $code .= random_int(0, 9);
            else
                $code .= random_int(0, 1) ? chr(random_int(65, 90)) : random_int(0, 9);
        }
        return $code;
    }


    /**
     * Send a notification containing the verification code.
     *
     * @return void
     */
    public function sendVerificationCodeNotification(): void
    {
        $user = $this->authenticatable;
        $user->notify(new TwoStepCodeEmail($this->code));
        $this->request_at = Carbon::now();
        $this->save();
    }
    
}