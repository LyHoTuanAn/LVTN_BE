<?php

namespace App\Services\Voucher;

use App\Models\Voucher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoucherService
{
    /**
     * Get all vouchers with filters
     */
    public function getAllVouchers(array $filters = []): LengthAwarePaginator
    {
        $query = Voucher::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['code'])) {
            $query->where('code', 'like', '%' . $filters['code'] . '%');
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('code', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['applies_to'])) {
            $query->where('applies_to', $filters['applies_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get voucher by ID
     */
    public function getVoucherById(int $id): ?Voucher
    {
        return Voucher::find($id);
    }

    /**
     * Get voucher by code
     */
    public function getVoucherByCode(string $code): ?Voucher
    {
        return Voucher::where('code', $code)->first();
    }

    /**
     * Create a new voucher
     */
    public function createVoucher(array $data): Voucher
    {
        return DB::transaction(function () use ($data) {
            // Auto-generate code if not provided
            if (empty($data['code'])) {
                $data['code'] = $this->generateVoucherCode();
            }

            // Ensure code is uppercase
            $data['code'] = strtoupper($data['code']);

            // Normalize comma-separated IDs (trim spaces)
            if (isset($data['only_for_user']) && !empty($data['only_for_user'])) {
                $data['only_for_user'] = $this->normalizeCommaSeparatedIds($data['only_for_user']);
            }

            if (isset($data['only_for_movie']) && !empty($data['only_for_movie'])) {
                $data['only_for_movie'] = $this->normalizeCommaSeparatedIds($data['only_for_movie']);
            }

            $voucher = Voucher::create($data);

            // Update status based on dates
            $this->updateVoucherStatus($voucher);

            return $voucher;
        });
    }

    /**
     * Update voucher
     */
    public function updateVoucher(int $id, array $data): bool
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return false;
        }

        return DB::transaction(function () use ($voucher, $data) {
            // Normalize code to uppercase if provided
            if (isset($data['code'])) {
                $data['code'] = strtoupper($data['code']);
            }

            // Normalize comma-separated IDs
            if (isset($data['only_for_user'])) {
                $data['only_for_user'] = $data['only_for_user'] 
                    ? $this->normalizeCommaSeparatedIds($data['only_for_user'])
                    : null;
            }

            if (isset($data['only_for_movie'])) {
                $data['only_for_movie'] = $data['only_for_movie']
                    ? $this->normalizeCommaSeparatedIds($data['only_for_movie'])
                    : null;
            }

            $result = $voucher->update($data);

            // Update status based on dates
            $this->updateVoucherStatus($voucher->fresh());

            return $result;
        });
    }

    /**
     * Delete voucher (hard delete)
     */
    public function deleteVoucher(int $id): bool
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return false;
        }

        return $voucher->delete();
    }

    /**
     * Generate unique voucher code
     */
    protected function generateVoucherCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Voucher::where('code', $code)->exists());

        return $code;
    }

    /**
     * Normalize comma-separated IDs (trim spaces, remove empty values)
     */
    protected function normalizeCommaSeparatedIds(string $idsString): string
    {
        $ids = array_filter(
            array_map('trim', explode(',', $idsString)),
            fn($id) => !empty($id) && is_numeric($id)
        );

        return implode(',', $ids);
    }

    /**
     * Update voucher status based on dates
     */
    protected function updateVoucherStatus(Voucher $voucher): void
    {
        $now = now();

        // If voucher is expired and status is active, update to expired
        if ($voucher->status === 'active' && $now->gt($voucher->valid_to)) {
            $voucher->update(['status' => 'expired']);
        }
    }

    /**
     * Get all available vouchers for a specific user
     * 
     * This returns vouchers that:
     * - Are active
     * - Are within valid date range
     * - Haven't exceeded usage limit
     * - Apply to all users OR specifically to this user
     * - User hasn't exceeded per_user_limit
     *
     * @param int $userId
     * @param int|null $movieId Optional movie ID to filter vouchers applicable to that movie
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableVouchersForUser(int $userId, ?int $movieId = null)
    {
        $now = now();

        // Get all active vouchers within valid date range
        $vouchers = Voucher::where('status', 'active')
            ->where('valid_from', '<=', $now)
            ->where('valid_to', '>=', $now)
            ->where(function ($query) {
                // Usage limit not exceeded
                $query->whereNull('usage_limit')
                    ->orWhereRaw('used_count < usage_limit');
            })
            ->orderBy('valid_to', 'asc') // Sắp xếp theo ngày hết hạn (sắp hết hạn trước)
            ->get();

        // Filter vouchers applicable to this user
        $availableVouchers = $vouchers->filter(function ($voucher) use ($userId, $movieId) {
            // Check if voucher applies to this user
            if (!$voucher->isApplicableForUser($userId)) {
                return false;
            }

            // Check if voucher applies to this movie (if movieId provided)
            if ($movieId !== null && !$voucher->isApplicableForMovie($movieId)) {
                return false;
            }

            // Check per_user_limit
            if ($voucher->per_user_limit !== null) {
                $userUsageCount = $voucher->getUserUsageCount($userId);
                if ($userUsageCount >= $voucher->per_user_limit) {
                    return false;
                }
            }

            return true;
        });

        return $availableVouchers->values();
    }
}

