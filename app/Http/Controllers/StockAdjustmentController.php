<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAdjustment;
use App\Models\Part;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function adjustStock(Request $request)
    {
        $request->validate([
            'part_id' => 'required|exists:parts,id',
            'adjustment' => 'required|integer',
            'price' => 'sometimes|numeric|min:0',
            'reason' => 'required|string|max:255',
        ]);

        // Create stock adjustment record
        $adjustment = new StockAdjustment();
        $adjustment->part_id = $request->part_id;
        $adjustment->adjustment = $request->adjustment;
        $adjustment->price_per_unit = $request->price;
        $adjustment->reason = $request->reason;
        $adjustment->save();

        // Update part quantity
        $part = Part::findOrFail($request->part_id);
        $part->quantity += $request->adjustment;
        $part->save();

        return response()->json([
            'message' => 'Stock adjusted successfully.',
            'part' => $part,
            'adjustment' => $adjustment,
        ]);
    }

    public function getDashboardStats()
    {
        $partsCount = Part::count();
        $categoriesCount = Category::count();
        $suppliersCount = Supplier::count();

        // Total sales: sum of negative adjustments (converted to positive values)
        $totalSales = StockAdjustment::where('adjustment', '<', 0)
            ->sum(DB::raw('ABS(adjustment * price_per_unit)'));

        return response()->json([
            'parts_count' => $partsCount,
            'categories_count' => $categoriesCount,
            'suppliers_count' => $suppliersCount,
            'total_sales' => $totalSales,
        ]);
    }

    public function getSalesPerPart()
    {
        $sales = StockAdjustment::where('adjustment', '<', 0)
            ->where('reason', 'sold')
            ->select(
                'part_id',
                DB::raw('SUM(ABS(adjustment)) as quantity_sold'),
                DB::raw('AVG(price_per_unit) as price_per_unit'),
                DB::raw('SUM(ABS(adjustment) * price_per_unit) as total')
            )
            ->groupBy('part_id')
            ->get();

        $partIds = $sales->pluck('part_id')->all();
        $parts = Part::whereIn('id', $partIds)->pluck('name', 'id');

        $result = [];
        foreach ($sales as $item) {
            $result[] = [
                'part_name' => $parts[$item->part_id] ?? null,
                'quantity_sold' => (int) $item->quantity_sold,
                'price_per_unit' => (float) $item->price_per_unit,
                'total' => (float) $item->total,
            ];
        }

        return response()->json($result);
    }
}
