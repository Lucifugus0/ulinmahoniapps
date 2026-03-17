@php                    
    if ($campaign->type === 'One-Time') :
        $status_color = 'bg-emerald-100 text-emerald-600';
    elseif ($campaign->type === 'At Risk') :
        $status_color = 'bg-amber-100 text-amber-600';
    elseif ($campaign->type === 'Off-Track') :
        $status_color = 'bg-rose-100 text-rose-500';
    else :
        $status_color = 'bg-slate-100 text-slate-500';
    endif;
@endphp  
<div class="col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
    <div class="flex flex-col h-full p-5">
        <header>
            <div class="flex items-center justify-between">
                @if ($campaign->category === '1')
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-rose-500">
                        <svg class="w-9 h-9 fill-current text-rose-50" viewBox="0 0 36 36">
                            <path d="M25 24H11a1 1 0 01-1-1v-5h2v4h12v-4h2v5a1 1 0 01-1 1zM14 13h8v2h-8z" />
                        </svg>
                    </div>
                @elseif ($campaign->category === '2')
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-emerald-500">
                        <svg class="w-9 h-9 fill-current text-emerald-50" viewBox="0 0 36 36">
                            <path d="M15 13v-3l-5 4 5 4v-3h8a1 1 0 000-2h-8zM21 21h-8a1 1 0 000 2h8v3l5-4-5-4v3z" />
                        </svg>
                    </div>
                @elseif ($campaign->category === '3')
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-sky-500">
                        <svg class="w-9 h-9 fill-current text-sky-50" viewBox="0 0 36 36">
                            <path d="M23 11v2.085c-2.841.401-4.41 2.462-5.8 4.315-1.449 1.932-2.7 3.6-5.2 3.6h-1v2h1c3.5 0 5.253-2.338 6.8-4.4 1.449-1.932 2.7-3.6 5.2-3.6h3l-4-4zM15.406 16.455c.066-.087.125-.162.194-.254.314-.419.656-.872 1.033-1.33C15.475 13.802 14.038 13 12 13h-1v2h1c1.471 0 2.505.586 3.406 1.455zM24 21c-1.471 0-2.505-.586-3.406-1.455-.066.087-.125.162-.194.254-.316.422-.656.873-1.028 1.328.959.878 2.108 1.573 3.628 1.788V25l4-4h-3z" />
                        </svg>
                    </div>
                @elseif ($campaign->category === '4')
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-indigo-500">
                        <svg class="w-9 h-9 fill-current text-indigo-50" viewBox="0 0 36 36">
                            <path d="M18 10c-4.4 0-8 3.1-8 7s3.6 7 8 7h.6l5.4 2v-4.4c1.2-1.2 2-2.8 2-4.6 0-3.9-3.6-7-8-7zm4 10.8v2.3L18.9 22H18c-3.3 0-6-2.2-6-5s2.7-5 6-5 6 2.2 6 5c0 2.2-2 3.8-2 3.8z" />
                        </svg>
                    </div>
                @endif
                <div class="flex shrink-0 -space-x-3 -ml-px">
                    @foreach ($campaign->members as $member)                        
                        <a class="block" href="{{ $member->link }}">
                            <img class="rounded-full border-2 border-white box-content" src="{{ asset('images/' . $member->image) }}" width="28" height="28" alt="{{ $member->name }}" />
                        </a>
                    @endforeach
                </div>
            </div>
        </header>
        <div class="grow mt-2">
            <a class="inline-flex text-slate-800 hover:text-slate-900 mb-1" href="#0">
                <h2 class="text-xl leading-snug font-semibold">{{ $campaign->title }}</h2>
            </a>
            <div class="text-sm">{{ $campaign->content }}</div>
        </div>
        <footer class="mt-5">
            <div class="text-sm font-medium text-slate-500 mb-2">{{ \Carbon\Carbon::parse($campaign->start_date)->format('M j') }} <span class="text-slate-400">-&gt;</span> {{ \Carbon\Carbon::parse($campaign->end_date)->format('M j') }}</div>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs inline-flex font-medium rounded-full text-center px-2.5 py-1 {{ $status_color }}">{{ $campaign->type }}</div>
                </div>
                <div>
                    <a class="text-sm font-medium text-indigo-500 hover:text-indigo-600" href="#0">View -&gt;</a>
                </div>
            </div>
        </footer>
    </div>
</div>