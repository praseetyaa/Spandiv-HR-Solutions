<?php

namespace App\Livewire\Expense;

use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\ExpenseItem;
use App\Models\ExpenseRequest;
use Livewire\Component;

class ExpenseManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public string $tab = 'requests'; // requests | categories

    // Category form
    public bool $showCatForm = false;
    public ?int $editingCatId = null;
    public string $catName = '';
    public string $catCode = '';
    public ?float $catMaxAmount = null;
    public bool $catRequiresReceipt = true;
    public bool $catRequiresApproval = true;

    // Request form
    public bool $showRequestForm = false;
    public ?int $editingRequestId = null;
    public ?int $reqEmployeeId = null;
    public string $reqTitle = '';
    public ?string $reqDate = null;
    public string $reqPurpose = '';

    // Items inline
    public array $reqItems = [];

    // Detail view
    public ?int $selectedRequestId = null;

    public function getCategoriesProperty() { return ExpenseCategory::latest()->get(); }
    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }

    public function getRequestsProperty()
    {
        return ExpenseRequest::with(['employee', 'items.category'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->get();
    }

    public function getSelectedRequestProperty()
    {
        if (!$this->selectedRequestId) return null;
        return ExpenseRequest::with(['employee', 'items.category', 'approvedBy'])->find($this->selectedRequestId);
    }

    public function selectRequest(int $id): void { $this->selectedRequestId = $id; }

    // ── Category CRUD ──
    public function openCatForm(?int $id = null): void
    {
        $this->reset(['editingCatId', 'catName', 'catCode', 'catMaxAmount', 'catRequiresReceipt', 'catRequiresApproval']);
        $this->catRequiresReceipt = true;
        $this->catRequiresApproval = true;
        if ($id) {
            $c = ExpenseCategory::findOrFail($id);
            $this->editingCatId = $c->id;
            $this->catName = $c->name;
            $this->catCode = $c->code;
            $this->catMaxAmount = $c->max_amount;
            $this->catRequiresReceipt = $c->requires_receipt;
            $this->catRequiresApproval = $c->requires_approval;
        }
        $this->showCatForm = true;
    }

    public function saveCat(): void
    {
        $this->validate(['catName' => 'required|string|max:255', 'catCode' => 'required|string|max:20']);
        $data = ['name' => $this->catName, 'code' => $this->catCode, 'max_amount' => $this->catMaxAmount, 'requires_receipt' => $this->catRequiresReceipt, 'requires_approval' => $this->catRequiresApproval];
        if ($this->editingCatId) ExpenseCategory::findOrFail($this->editingCatId)->update($data);
        else ExpenseCategory::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        $this->showCatForm = false;
    }

    public function deleteCat(int $id): void { ExpenseCategory::findOrFail($id)->delete(); }

    // ── Request CRUD ──
    public function openRequestForm(?int $id = null): void
    {
        $this->reset(['editingRequestId', 'reqEmployeeId', 'reqTitle', 'reqDate', 'reqPurpose', 'reqItems']);
        if ($id) {
            $r = ExpenseRequest::with('items')->findOrFail($id);
            $this->editingRequestId = $r->id;
            $this->reqEmployeeId = $r->employee_id;
            $this->reqTitle = $r->title;
            $this->reqDate = $r->expense_date->format('Y-m-d');
            $this->reqPurpose = $r->purpose;
            $this->reqItems = $r->items->map(fn ($i) => [
                'category_id' => $i->category_id, 'description' => $i->description,
                'amount' => $i->amount, 'item_date' => $i->item_date->format('Y-m-d'),
            ])->toArray();
        }
        if (empty($this->reqItems)) $this->addItem();
        $this->showRequestForm = true;
    }

    public function addItem(): void
    {
        $this->reqItems[] = ['category_id' => '', 'description' => '', 'amount' => 0, 'item_date' => now()->format('Y-m-d')];
    }

    public function removeItem(int $index): void
    {
        unset($this->reqItems[$index]);
        $this->reqItems = array_values($this->reqItems);
    }

    public function saveRequest(): void
    {
        $this->validate([
            'reqEmployeeId' => 'required|exists:employees,id',
            'reqTitle'      => 'required|string|max:255',
            'reqDate'       => 'required|date',
            'reqPurpose'    => 'required|string',
        ]);

        $total = collect($this->reqItems)->sum('amount');

        $data = [
            'employee_id'  => $this->reqEmployeeId,
            'title'        => $this->reqTitle,
            'expense_date' => $this->reqDate,
            'purpose'      => $this->reqPurpose,
            'total_amount' => $total,
            'status'       => 'pending',
        ];

        if ($this->editingRequestId) {
            $req = ExpenseRequest::findOrFail($this->editingRequestId);
            $req->update($data);
            $req->items()->delete();
        } else {
            $data['tenant_id'] = auth()->user()->tenant_id;
            $req = ExpenseRequest::create($data);
        }

        foreach ($this->reqItems as $item) {
            if (!empty($item['description'])) {
                $req->items()->create($item);
            }
        }

        $this->showRequestForm = false;
    }

    public function approveRequest(int $id): void
    {
        ExpenseRequest::findOrFail($id)->update([
            'status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now(),
        ]);
    }

    public function rejectRequest(int $id): void
    {
        ExpenseRequest::findOrFail($id)->update(['status' => 'rejected']);
    }

    public function markPaid(int $id): void
    {
        ExpenseRequest::findOrFail($id)->update(['status' => 'paid', 'paid_at' => now()]);
    }

    public function deleteRequest(int $id): void
    {
        ExpenseRequest::findOrFail($id)->delete();
        if ($this->selectedRequestId === $id) $this->selectedRequestId = null;
    }

    public function render()
    {
        return view('livewire.expense.expense-manager')
            ->layout('layouts.app', ['pageTitle' => 'Expense & Reimbursement']);
    }
}
