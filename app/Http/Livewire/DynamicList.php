<?php

namespace App\Http\Livewire;

use Livewire\Component;

class DynamicList extends Component
{
    public $items = [];
    public $selectedStatus;

    public function mount()
    {
        // Initialize the list
        $this->items = [
            ['name' => 'Item 1', 'disabled' => false],
            ['name' => 'Item 2', 'disabled' => false],
            ['name' => 'Item 3', 'disabled' => false],
            ['name' => 'Item 4', 'disabled' => false],
            ['name' => 'Item 5', 'disabled' => false],
        ];
    }

    public function updatedSelectedStatus()
    {
        if ($this->selectedStatus === 'disable_even') {
            // Disable even items
            foreach ($this->items as $index => &$item) {
                $item['disabled'] = $index % 2 === 1; // Even index
            }
        } elseif ($this->selectedStatus === 'disable_odd') {
            // Disable odd items
            foreach ($this->items as $index => &$item) {
                $item['disabled'] = $index % 2 === 0; // Odd index
            }
        } elseif ($this->selectedStatus === 'enable_all') {
            // Enable all items
            foreach ($this->items as &$item) {
                $item['disabled'] = false;
            }
        }
    }

    public function handleItemClick($index)
    {
        // Handle a button click event
        $itemName = $this->items[$index]['name'];
        session()->flash('message', "You clicked on {$itemName}!");
    }

    public function updateList($selectedStatus)
    {
        $this->selectedStatus = $selectedStatus;

        if ($selectedStatus === 'disable_even') {
            foreach ($this->items as $index => &$item) {
                $item['disabled'] = $index % 2 === 1; // Disable even-indexed items
            }
        } elseif ($selectedStatus === 'disable_odd') {
            foreach ($this->items as $index => &$item) {
                $item['disabled'] = $index % 2 === 0; // Disable odd-indexed items
            }
        } elseif ($selectedStatus === 'enable_all') {
            foreach ($this->items as &$item) {
                $item['disabled'] = false; // Enable all items
            }
        }
    }


    public function render()
    {
        return view('livewire.dynamic-list');
    }
}
