<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVoucherRequest;
use App\Http\Requests\Admin\UpdateVoucherRequest;
use App\Services\Voucher\VoucherService;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    protected VoucherService $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * Display a listing of vouchers
     */
    public function index(Request $request)
    {
        $vouchers = $this->voucherService->getAllVouchers($request->all());

        return view('admin.vouchers.index', [
            'vouchers' => $vouchers,
            'filters' => [
                'status' => $request->get('status'),
                'type' => $request->get('type'),
                'applies_to' => $request->get('applies_to'),
                'search' => $request->get('search'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new voucher
     */
    public function create()
    {
        return view('admin.vouchers.create');
    }

    /**
     * Store a newly created voucher in storage
     */
    public function store(StoreVoucherRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Combine date with time (00:00:00 for valid_from, 23:59:59 for valid_to)
            if (isset($data['valid_from'])) {
                $data['valid_from'] = $data['valid_from'] . ' ' . ($request->input('valid_from_time', '00:00:00'));
            }
            if (isset($data['valid_to'])) {
                $data['valid_to'] = $data['valid_to'] . ' ' . ($request->input('valid_to_time', '23:59:59'));
            }
            
            $voucher = $this->voucherService->createVoucher($data);

            return redirect()
                ->route('admin.vouchers.show', $voucher->id)
                ->with('success', __('Voucher created successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to create voucher: :message', ['message' => $e->getMessage()])])
                ->withInput();
        }
    }

    /**
     * Display the specified voucher
     */
    public function show(int $id)
    {
        $voucher = $this->voucherService->getVoucherById($id);

        if (!$voucher) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('error', __('Voucher not found'));
        }

        return view('admin.vouchers.show', [
            'voucher' => $voucher,
        ]);
    }

    /**
     * Show the form for editing the specified voucher
     */
    public function edit(int $id)
    {
        $voucher = $this->voucherService->getVoucherById($id);

        if (!$voucher) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('error', __('Voucher not found'));
        }

        return view('admin.vouchers.edit', [
            'voucher' => $voucher,
        ]);
    }

    /**
     * Update the specified voucher in storage
     */
    public function update(UpdateVoucherRequest $request, int $id)
    {
        $voucher = $this->voucherService->getVoucherById($id);

        if (!$voucher) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('error', __('Voucher not found'));
        }

        try {
            $data = $request->validated();
            
            // Combine date with time (00:00:00 for valid_from, 23:59:59 for valid_to)
            if (isset($data['valid_from'])) {
                $data['valid_from'] = $data['valid_from'] . ' ' . ($request->input('valid_from_time', '00:00:00'));
            }
            if (isset($data['valid_to'])) {
                $data['valid_to'] = $data['valid_to'] . ' ' . ($request->input('valid_to_time', '23:59:59'));
            }
            
            $updated = $this->voucherService->updateVoucher($id, $data);

            if (!$updated) {
                return redirect()
                    ->route('admin.vouchers.edit', $id)
                    ->with('error', __('Failed to update voucher'));
            }

            return redirect()
                ->route('admin.vouchers.show', $id)
                ->with('success', __('Voucher updated successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to update voucher: :message', ['message' => $e->getMessage()])])
                ->withInput();
        }
    }

    /**
     * Remove the specified voucher from storage
     */
    public function destroy(int $id)
    {
        $voucher = $this->voucherService->getVoucherById($id);

        if (!$voucher) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('error', __('Voucher not found'));
        }

        try {
            $deleted = $this->voucherService->deleteVoucher($id);

            if (!$deleted) {
                return redirect()
                    ->route('admin.vouchers.index')
                    ->with('error', __('Failed to delete voucher'));
            }

            return redirect()
                ->route('admin.vouchers.index')
                ->with('success', __('Voucher deleted successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('error', __('Failed to delete voucher: :message', ['message' => $e->getMessage()]));
        }
    }
}

