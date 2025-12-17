<?php

class AutoApprovalHandler extends TransactionApprovalHandler
{
    public function handle(float $amount): string
    {
        if ($amount <= 500) {
            return 'approved';
        }

        return $this->next?->handle($amount) ?? 'pending';
    }
}
