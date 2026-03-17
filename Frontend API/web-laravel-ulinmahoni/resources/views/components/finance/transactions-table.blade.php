<div class="bg-white">
    <div x-data="handleSelect">

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full" @click.stop="$dispatch('set-transactionopen', true)">
                <!-- Table header -->
                <thead class="text-xs font-semibold uppercase text-slate-500 border-t border-b border-slate-200">
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
                            <div class="font-semibold text-left">Counterparty</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Payment Date</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Status</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-right">Amount</div>
                        </th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody class="text-sm divide-y divide-slate-200 border-b border-slate-200">
                    <!-- Row -->
                    @foreach($transactions as $transaction)
                        @php                    
                            if ($transaction->status === 'Completed') :
                                $status_color = 'bg-emerald-100 text-emerald-600';
                            elseif ($transaction->status === 'Canceled') :
                                $status_color = 'bg-rose-100 text-rose-500';
                            else :
                                $status_color = 'bg-slate-100 text-slate-500';
                            endif;
                            if (substr($transaction->amount, 0, 1) === '+') :
                                $amount_color = 'text-emerald-500';
                            else :
                                $amount_color = 'text-slate-700';
                            endif;
                        @endphp                     
                        <tr>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                <div class="flex items-center">
                                    <label class="inline-flex">
                                        <span class="sr-only">Select</span>
                                        <input class="table-item form-checkbox" type="checkbox" @click.stop="uncheckParent" />
                                    </label>
                                </div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap md:w-1/2">
                                <div class="flex items-center">
                                    <div class="w-9 h-9 shrink-0 mr-2 sm:mr-3">
                                        <img class="w-9 h-9 rounded-full" src="{{ asset('images/' . $transaction->image) }}" width="36" height="36" alt="{{ $transaction->name }}" />
                                    </div>
                                    <div class="font-medium text-slate-800">{{ $transaction->name }}</div>
                                </div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="text-left">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="text-left">
                                    <div class="text-xs inline-flex font-medium rounded-full text-center px-2.5 py-1 {{$status_color}}">{{ $transaction->status }}</div>
                                </div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                <div class="text-right font-medium {{$amount_color}}">{{ $transaction->amount }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
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
            },
        }))
    })    
</script>