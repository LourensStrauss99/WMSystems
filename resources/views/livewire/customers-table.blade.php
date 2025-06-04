{{-- resources/views/livewire/customers-table.blade.php --}}
<div>
    <div class="flex justify-between items-center mb-2">
        <div>
            Show 
            <select wire:model="perPage" class="border rounded px-2 py-1">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            entries
        </div>
        <div>
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search" class="border rounded px-2 py-1" />
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b">ID</th>
                    <th class="px-4 py-2 border-b">Name</th>
                    <th class="px-4 py-2 border-b">Surname</th>
                    <th class="px-4 py-2 border-b">Telephone</th>
                    <th class="px-4 py-2 border-b">Address</th>
                    <th class="px-4 py-2 border-b">Email</th>
                    <th class="px-4 py-2 border-b"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-2 border-b">{{ $customer->id }}</td>
                    <td class="px-4 py-2 border-b">{{ $customer->name }}</td>
                    <td class="px-4 py-2 border-b">{{ $customer->surname }}</td>
                    <td class="px-4 py-2 border-b">{{ $customer->telephone }}</td>
                    <td class="px-4 py-2 border-b">{{ $customer->address }}</td>
                    <td class="px-4 py-2 border-b">{{ $customer->email }}</td>
                    <td class="px-4 py-2 border-b text-center">
                        <a href="{{ route('client.show', $customer->id) }}" 
                           class="btn btn-sm rounded-circle d-inline-flex align-items-center justify-content-center"
                           style="width:2rem; height:2rem; background-color:#649ff7; color:white;" 
                           title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="flex justify-between items-center mt-4">
        <div>
            Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} entries
        </div>
        <div>
            {{ $customers->links('pagination::tailwind') }}
        </div>
    </div>
</div>

