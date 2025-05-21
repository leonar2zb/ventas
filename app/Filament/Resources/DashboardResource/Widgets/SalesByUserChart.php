<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\SaleOrder;

class SalesByUserChart extends ChartWidget
{
    protected static ?string $heading = 'Sales';

    protected static ?string $maxWidth = '30px';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $sales = SaleOrder::selectRaw('user_id, SUM(total_price) as total')
            ->where('status', 'confirmed')
            ->groupBy('user_id')
            ->with('user')
            ->get();

        $labels = $sales->map(fn($sale) => $sale->user->name)->toArray();
        $data = $sales->map(fn($sale) => $sale->total)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }


    public function getDescription(): ?string
    {
        return 'Total sales per seller.';
    }

    protected function getType(): string
    {
        return 'pie'; // 'line', 'pie', 'bar'.
    }
}
