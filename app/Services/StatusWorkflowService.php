<?php

namespace App\Services;

class StatusWorkflowService
{
    /**
     * Get valid next statuses for an estimate
     */
    public static function getValidEstimateStatuses($currentStatus)
    {
        $workflow = [
            'DRAFT' => ['PENDING_REVIEW', 'ON_HOLD', 'CANCELLED'],
            'PENDING_REVIEW' => ['UNDER_REVIEW', 'ON_HOLD', 'CANCELLED'],
            'UNDER_REVIEW' => ['APPROVED', 'REJECTED', 'ON_HOLD', 'CANCELLED'],
            'APPROVED' => ['COMPLETED', 'ON_HOLD', 'CANCELLED'],
            'REJECTED' => ['DRAFT', 'ON_HOLD', 'CANCELLED'],
            'ON_HOLD' => ['DRAFT', 'PENDING_REVIEW', 'UNDER_REVIEW', 'APPROVED', 'REJECTED', 'CANCELLED'],
            'COMPLETED' => [],
            'CANCELLED' => [],
        ];

        return $workflow[$currentStatus] ?? [];
    }

    /**
     * Check if estimate status transition is valid
     */
    public static function isValidEstimateTransition($currentStatus, $newStatus)
    {
        $validStatuses = self::getValidEstimateStatuses($currentStatus);
        return in_array($newStatus, $validStatuses);
    }

    /**
     * Get valid next statuses for an invoice
     */
    public static function getValidInvoiceStatuses($currentStatus)
    {
        $workflow = [
            'DRAFT' => ['PENDING', 'UNPAID', 'VOID'],
            'PENDING' => ['OPEN', 'OVERDUE', 'UNPAID', 'VOID'],
            'OPEN' => ['PARTIALLY_PAID', 'PAID', 'OVERDUE', 'UNPAID', 'VOID'],
            'PARTIALLY_PAID' => ['PAID', 'UNPAID', 'VOID'],
            'PAID' => ['REFUNDED', 'VOID'],
            'OVERDUE' => ['OPEN', 'PARTIALLY_PAID', 'PAID', 'UNPAID', 'VOID'],
            'UNPAID' => ['DRAFT', 'PENDING', 'OPEN', 'VOID'],
            'VOID' => [],
            'REFUNDED' => [],
        ];

        return $workflow[$currentStatus] ?? [];
    }

    /**
     * Check if invoice status transition is valid
     */
    public static function isValidInvoiceTransition($currentStatus, $newStatus)
    {
        $validStatuses = self::getValidInvoiceStatuses($currentStatus);
        return in_array($newStatus, $validStatuses);
    }
}

