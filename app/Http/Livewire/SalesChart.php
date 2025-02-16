<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;

class SalesChart extends Component
{
    public function render()
    {
        // Sample data for chart
        $columnChart = (new ColumnChartModel())
            ->setTitle('Sales Data')
            ->addColumn('January', 100, '#f6ad55')
            ->addColumn('February', 200, '#fc8181')
            ->addColumn('March', 150, '#90cdf4');

        return view('livewire.sales-chart', ['columnChart' => $columnChart]);
    }
}
