<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InventoryController extends Controller
{
    public function index(): View
    {
        $inventories = Inventory::paginate(15);
        return view('inventories.index', compact('inventories'));
    }

    public function create(): View
    {
        return view('inventories.create');
    }

    public function store(StoreInventoryRequest $request): RedirectResponse
    {
        $inventory = Inventory::create($request->validated());
        $inventory->updateStatus();

        return redirect()->route('inventories.index')
                        ->with('success', 'Inventory item created successfully.');
    }

    public function show(Inventory $inventory): View
    {
        $transactions = $inventory->transactions()->latest()->paginate(10);
        return view('inventories.show', compact('inventory', 'transactions'));
    }

    public function edit(Inventory $inventory): View
    {
        return view('inventories.edit', compact('inventory'));
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory): RedirectResponse
    {
        $inventory->update($request->validated());
        $inventory->updateStatus();

        return redirect()->route('inventories.show', $inventory)
                        ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(Inventory $inventory): RedirectResponse
    {
        $inventory->forceDelete();
        return redirect()->route('inventories.index')
                        ->with('success', 'Inventory item deleted successfully.');
    }

    public function search()
    {
        $term = request('q');
        $inventories = Inventory::search($term)->paginate(15);
        return view('inventories.index', compact('inventories'));
    }

    public function lowStock(): View
    {
        $inventories = Inventory::lowStock()->paginate(15);
        return view('inventories.low-stock', compact('inventories'));
    }

    public function expired(): View
    {
        $inventories = Inventory::expired()->paginate(15);
        return view('inventories.expired', compact('inventories'));
    }

    public function restock(Inventory $inventory): RedirectResponse
    {
        $quantity = request('quantity');
        $inventory->increaseQuantity($quantity);

        return redirect()->route('inventories.show', $inventory)
                        ->with('success', "Inventory restocked with {$quantity} units.");
    }
}
