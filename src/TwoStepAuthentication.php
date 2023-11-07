<?php

namespace Kohaku1907\Lara2step;

use Illuminate\Database\Eloquent\Relations\MorphOne;

trait TwoStepAuthentication
{
    protected $forceTwoStepAuthEnabled;

    protected $codeLength;

    protected $numericCode;

    /**
     * Boot the trait.
     *
     * @return void
     */
    protected  function initializeTwoStepAuthentication()
    {
        // For security, we will hide the Two-Factor Authentication data from the parent model.
        $this->makeHidden('twoStepAuth');

        $this->codeLength = config('2step.code_length');
        $this->numericCode = config('2step.numeric_code');
        
        // We will also make sure that the Two-Factor Authentication data is loaded when the parent model is loaded.
        $this->registerTwoStepAuthentication();
    }

    /**
     * Get the Two-Factor Authentication data for the model.
     *
     * @return MorphOne
     */
    public function twoStepAuth(): MorphOne
    {
        return $this->morphOne(Models\TwoStepAuth::class, 'authenticatable')
            ->withDefault(static function (Models\TwoStepAuth $model) : Models\TwoStepAuth  {
                return $model->fill([
                    'enabled_at' => null,
                ]);
            });
    }

    /**
     * Register the Two-Factor Authentication data for the model.
     *
     * @return void
     */
    public function registerTwoStepAuthentication(): void
    {
    }

    
    /**
     * Configure the channel for forced Two-Factor Authentication.
     *
     * @param string|null $channel The channel to use for Two-Factor Authentication.
     * @return void
     */
    public function configureForceEnable(string $channel = null): void
    {
        $this->forceTwoStepAuthEnabled = $channel ?? config('2step.default_channel');
    }

    /**
     * Configure the format of the Two-Factor Authentication code.
     *
     * @param int $length The length of the code.
     * @param bool $numericCode Whether the code should be numeric only.
     * @return void
     * @throws \Exception If the code length is not between 4 and 6.
     */
    public function configureCodeFormat(int $length, bool $numericCode = false): void
    {
        if($length > 6 || $length < 4) {
            throw new \Exception('Code length must be between 4 and 6');
        }
        $this->codeLength = $length;
        $this->numericCode = $numericCode;
    }

    /**
     * Get the length of the Two-Step Authentication code.
     *
     * @return int The length of the Two-Step Authentication code.
     */    
    public function getTwoStepCodeLength(): int
    {
        return $this->twoStepAuth->codeLength;
    }

    /**
     * Check if Two-Step Authentication is enabled.
     *
     * @return bool True if Two-Step Authentication is enabled, false otherwise.
     */
    public function hasTwoStepEnabled(): bool
    {
        if($this->forceTwoStepAuthEnabled) {
            $this->twoStepAuth()->updateOrCreate([], [
                'channel' => $this->forceTwoStepAuthEnabled,
                'enabled_at' => now(),
            ]);
            return true;
        }

        return $this->twoStepAuth ? $this->twoStepAuth->isEnabled() : false;
    }

    /**
     * Enable Two-Step Authentication.
     *
     * @return void
     */
    public function enableTwoStep($channel = null): void
    {
        $this->twoStepAuth()->enable($channel);
    }

    /**
     * Disable Two-Step Authentication.
     *
     * @return void
     */
    public function disableTwoStep(): void
    {
        $this->twoStepAuth()->disable();
    }

    
    public function confirmEnableTwoStep(string $code): bool
    {
        if($this->hasTwoStepEnabled()) {
            return true;
        }

        if($this->validateCode($code)) {
            $this->enableTwoStep();
            return true;
        }

        return false;
    }

    /**
     * Generate a Two-Step Authentication code.
     *
     * @return void
     */
    public function generateTwoStepCode(): void
    {
        $this->twoStepAuth->generateTwoStepCode($this->codeLength, $this->numericCode);
    }

    /**
     * Validate a Two-Step Authentication code.
     *
     * @param string $code The code to validate.
     * @return bool True if the code is valid, false otherwise.
     */
    public function validateCode(string $code): bool
    {
        return $this->twoStepAuth()->validateCode($code);
    }

    /**
     * Check if the Two-Step Authentication code format has changed.
     *
     * @return bool True if the code format has changed, false otherwise.
     */
    public function codeFormatChanged(): bool
    {
        return $this->twoStepAuth->codeLength !== $this->codeLength || $this->twoStepAuth->numericCode !== $this->numericCode;
    }
    
}