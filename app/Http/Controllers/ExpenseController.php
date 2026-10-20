<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\FinancialReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        [$start, $end] = FinancialReportService::resolvePeriod(
            $request->query('month'),
            $request->query('from'),
            $request->query('to')
        );

        $type = in_array($request->query('type'), ['pemasukan', 'pengeluaran'], true)
            ? $request->query('type')
            : null;

        $range = [$start->toDateString(), $end->toDateString()];

        $records = Expense::query()
            ->whereBetween('expense_date', $range)
            ->when($type, fn ($q) => $q->where('type', $type))
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get();

        $totalPemasukan = (float) Expense::pemasukan()->whereBetween('expense_date', $range)->sum('amount');
        $totalPengeluaran = (float) Expense::pengeluaran()->whereBetween('expense_date', $range)->sum('amount');

        return view('owner.expenses', [
            'records' => $records,
            'type' => $type,
            'selectedMonth' => $start->format('Y-m'),
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'periodLabel' => FinancialReportService::periodLabel($start, $end),
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldo' => $totalPemasukan - $totalPengeluaran,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['pemasukan', 'pengeluaran'])],
            'expense_date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Expense::create($data);

        return back()->with('success', 'Catatan berhasil disimpan.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('success', 'Catatan berhasil dihapus.');
    }
}