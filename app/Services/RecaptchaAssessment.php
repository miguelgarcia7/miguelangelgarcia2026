<?php

namespace App\Services;

/**
 * The outcome of scoring one submission.
 *
 * "verified" means Google actually scored the token. When it is false the
 * score is unknown — the visitor blocked the script, was offline, or Google
 * errored — which is reported rather than treated as guilt.
 */
class RecaptchaAssessment
{
    public function __construct(
        public readonly bool $verified,
        public readonly ?float $score = null,
        public readonly ?string $reason = null,
    ) {}

    public static function unverified(string $reason): self
    {
        return new self(verified: false, reason: $reason);
    }

    public static function scored(float $score): self
    {
        return new self(verified: true, score: $score);
    }

    /**
     * Scored below the "looks like a bot" threshold.
     */
    public function isSuspicious(): bool
    {
        return $this->verified
            && $this->score !== null
            && $this->score < (float) config('services.recaptcha.suspicious_below');
    }

    /**
     * Bad enough to reject outright. Only ever true when a block threshold
     * is configured, so the default behaviour never turns anyone away.
     */
    public function shouldBlock(): bool
    {
        $blockBelow = config('services.recaptcha.block_below');

        return $blockBelow !== null
            && $this->verified
            && $this->score !== null
            && $this->score < (float) $blockBelow;
    }

    /**
     * Short human-readable summary for the notification email.
     */
    public function summary(): string
    {
        if (! $this->verified) {
            return 'Not verified — '.($this->reason ?? 'unknown');
        }

        return $this->isSuspicious()
            ? sprintf('Suspicious — score %.1f', $this->score)
            : sprintf('Looks human — score %.1f', $this->score);
    }
}
