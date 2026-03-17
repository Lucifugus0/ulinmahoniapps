<div class="bg-white shadow-lg rounded-sm border border-slate-200 mb-8">
    <header class="px-5 py-4">
        <h2 class="font-semibold text-slate-800">All Orders <span class="text-slate-400 font-medium">{{ $count }}</span></h2>
    </header>
    <div x-data="handleSelect">

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full divide-y divide-slate-200">
                <!-- Table header -->
                <thead class="text-xs uppercase text-slate-500 bg-slate-50 border-t border-slate-200">
                    <tr>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                            <div class="flex items-center">
                                <label class="inline-flex">
                                    <span class="sr-only">Select all</span>
                                    <input id="parent-checkbox" class="form-checkbox" type="checkbox" @click="toggleAll" />
                                </label>
                            </div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Order</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Date</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Customer</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Total</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Status</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold">Items</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Location</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Payment type</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <span class="sr-only">Menu</span>
                        </th>
                    </tr>
                </thead>
                <!-- Table body -->
                @foreach($orders as $order)
                    @php                    
                        if ($order->status === 'Approved') :
                            $status_color = 'bg-emerald-100 text-emerald-600';
                        elseif ($order->status === 'Refunded') :
                            $status_color = 'bg-amber-100 text-amber-600';
                        else :
                            $status_color = 'bg-slate-100 text-slate-500';
                        endif;
                    @endphp
                    <tbody class="text-sm" x-data="{ open: false }">
                        <!-- Row -->
                        <tr>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                <div class="flex items-center">
                                    <label class="inline-flex">
                                        <span class="sr-only">Select</span>
                                        <input class="table-item form-checkbox" type="checkbox" @click="uncheckParent" />
                                    </label>
                                </div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="flex items-center text-slate-800">
                                    <div class="w-10 h-10 shrink-0 flex items-center justify-center bg-slate-100 rounded-full mr-2 sm:mr-3">
                                        <img class="ml-1" src="{{ asset('images/' . $order->image) }}" width="20" height="20" alt="{{ $order->order }}" />
                                    </div>
                                    <div class="font-medium text-sky-500">{{ $order->order }}</div>
                                </div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div>{{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-medium text-slate-800">{{ $order->customer }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="text-left font-medium text-emerald-500">{{ $order->total }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="inline-flex font-medium rounded-full text-center px-2.5 py-0.5 {{$status_color}}">{{ $order->status }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="text-center">{{ $order->items }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="text-left">{{ $order->location }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 fill-current text-slate-400 shrink-0 mr-2" viewBox="0 0 16 16">
                                        <path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                                    </svg>
                                    <div>{{ $order->type }}</div>
                                </div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                <div class="flex items-center">
                                    <button
                                        class="text-slate-400 hover:text-slate-500 transform"
                                        :class="{ 'rotate-180': open }"
                                        @click.prevent="open = !open"
                                        :aria-expanded="open"
                                        aria-controls="description-{{ $order->id }}"
                                    >
                                        <span class="sr-only">Menu</span>
                                        <svg class="w-8 h-8 fill-current" viewBox="0 0 32 32">
                                            <path d="M16 20l-5.4-5.4 1.4-1.4 4 4 4-4 1.4 1.4z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!--
                        Example of content revealing when clicking the button on the right side:
                        Note that you must set a "colspan" attribute on the <td> element,
                        and it should match the number of columns in your table
                        -->
                        <tr id="description-{{ $order->id }}" role="region" x-show="open" x-cloak>
                            <td colspan="10" class="px-2 first:pl-5 last:pr-5 py-3">
                                <div class="flex items-center bg-slate-50 p-3 -mt-3">
                                    <svg class="w-4 h-4 shrink-0 fill-current text-slate-400 mr-2">
                                        <path d="M1 16h3c.3 0 .5-.1.7-.3l11-11c.4-.4.4-1 0-1.4l-3-3c-.4-.4-1-.4-1.4 0l-11 11c-.2.2-.3.4-.3.7v3c0 .6.4 1 1 1zm1-3.6l10-10L13.6 4l-10 10H2v-1.6z" />
                                    </svg>
                                    <div class="italic">{{ $order->description }}</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                @endforeach
            </table>

        </div>
    </div>
</div>
<script>
    // A basic demo function to handle "select all" functionality
    document.addEventListener('alpine:init', () => {
        Alpine.data('handleSelect', () => ({
            selectall: false,
            selectAction() {
                countEl = document.querySelector('.table-items-action');
                if (!countEl) return;
                checkboxes = document.querySelectorAll('input.table-item:checked');
                document.querySelector('.table-items-count').innerHTML = checkboxes.length;
                if (checkboxes.length > 0) {
                    countEl.classList.remove('hidden');
                } else {
                    countEl.classList.add('hidden');
                }
            },
            toggleAll() {
                this.selectall = !this.selectall;
                checkboxes = document.querySelectorAll('input.table-item');
                [...checkboxes].map((el) => {
                    el.checked = this.selectall;
                });
                this.selectAction();
            },
            uncheckParent() {
                this.selectall = false;
                document.getElementById('parent-checkbox').checked = false;
                this.selectAction();
            }
        }))
    })
</script>