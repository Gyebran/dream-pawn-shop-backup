<?php
namespace App\Services\Loans;
use App\Models\Fines\Fine;
use App\Models\Loans\LoanRequest;
use Illuminate\Support\Facades\DB;

/**
 * Shared borrowing eligibility rules for both Admin approval and Member request flows.
 * Keep role-specific HTTP handling outside this service.
 */
class LoanEligibilityService
{
    /** Maximum physical units a Member may hold as approved reservations plus active loans. */
    public const MAX_ALLOCATED_UNITS = 2;

    public function hasUnpaidFines(int $memberId): bool
    {
        return Fine::whereHas('loanRequestItem.loanRequest', fn ($query) => $query->where('member_id', $memberId))
            ->where('status', Fine::UNPAID)->exists();
    }

    /** Units on approved (reserved) or active (loaned) requests. */
    public function allocatedUnitCount(int $memberId): int
    {
        return $this->unitCountByStatus($memberId, [LoanRequest::APPROVED, LoanRequest::ACTIVE]);
    }

    /** Units on requests still waiting for Admin review. Counted only at Member submission time. */
    public function pendingUnitCount(int $memberId): int
    {
        return $this->unitCountByStatus($memberId, [LoanRequest::PENDING]);
    }

    public function canAllocate(int $memberId, int $additionalUnits): bool
    {
        return $additionalUnits > 0 && $this->allocatedUnitCount($memberId) + $additionalUnits <= self::MAX_ALLOCATED_UNITS;
    }

    private function unitCountByStatus(int $memberId, array $statuses): int
    {
        return (int) DB::table('loan_request_items')
            ->join('loan_requests', 'loan_requests.id', '=', 'loan_request_items.loan_request_id')
            ->where('loan_requests.member_id', $memberId)
            ->whereIn('loan_requests.status', $statuses)
            ->count();
    }
}
