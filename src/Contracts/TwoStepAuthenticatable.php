<?php

namespace Kohaku1907\Laravel2step\Contracts;

interface TwoStepAuthenticatable
{
    public function getTwoStepCodeLength(): int;

    public function hasTwoStepEnabled(): bool;

    public function enableTwoStep($channel = null): void;

    public function disableTwoStep(): void;

    public function confirmEnableTwoStep(string $code): bool;

    public function generateTwoStepCode();

    public function validateCode(string $code): bool;

}