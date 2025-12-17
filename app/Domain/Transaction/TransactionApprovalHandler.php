<?php


abstract class TransactionApprovalHandler
{
    protected ?TransactionApprovalHandler $next = null;

    public function setNext(TransactionApprovalHandler $handler): self
    {
        $this->next = $handler;
        return $handler;
    }

    abstract public function handle(float $amount): string;
}


