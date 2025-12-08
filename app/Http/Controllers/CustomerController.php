<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'active'); // active|archived|all
        $search = trim((string) $request->get('q', ''));

        $customers = Customer::query()
            ->withCount('sales')
            ->when($status === 'active', fn ($q) => $q->whereNull('archived_at'))
            ->when($status === 'archived', fn ($q) => $q->whereNotNull('archived_at'))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                          ->orWhere('document', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderByRaw('archived_at IS NULL DESC')
            ->orderByDesc('last_purchase_at')
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact('customers', 'status', 'search'));
    }

    public function show(Customer $customer)
    {
        $sales = Sale::query()
            ->where('customer_id', $customer->id)
            ->with(['items'])
            ->orderByDesc('sold_at')
            ->paginate(10);

        return view('customers.show', compact('customer', 'sales'));
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json(['data' => $customer], 201);
        }

        return redirect()->route('customers.index')->with('status', 'Cliente creado.');
    }

    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();
        $customer->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $customer], 200);
        }

        return back()->with('status', 'Cliente actualizado.');
    }

    public function archive(Customer $customer)
    {
        $customer->archived_at = Carbon::now();
        $customer->save();

        return back()->with('status', 'Cliente archivado.');
    }

    public function unarchive(Customer $customer)
    {
        $customer->archived_at = null;
        $customer->save();

        return back()->with('status', 'Cliente reactivado.');
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        $results = Customer::query()
            ->when($query, function ($q) use ($query) {
                $q->where('document', 'like', "{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->orderBy('document')
            ->limit(8)
            ->get(['id', 'name', 'document', 'email', 'phone', 'archived_at', 'last_purchase_at']);

        return response()->json(['data' => $results]);
    }
}
