@extends('layouts.public')
@inject('siteSettings', 'App\Services\GlobalSettings')
@section('title', $title)

@section('content')
@php
    $descriptions = [
        'news' => 'ປະກາດ, ການອັບເດດລະບົບ ແລະ ຂໍ້ມູນສຳຄັນຈາກທີມງານ TIPLAO.',
        'community' => 'ຊ່ອງທາງພົບປະ, ແລກປ່ຽນ ແລະ ຮັບຂ່າວສານຂອງຄຣີເອເຕີລາວ.',
        'about' => 'ຮູ້ຈັກແນວຄິດ ແລະ ເປົ້າໝາຍຂອງ TIPLAO DONATE.',
        'terms' => 'ກະລຸນາອ່ານ ແລະ ເຂົ້າໃຈຂໍ້ກຳນົດກ່ອນໃຊ້ງານ.',
        'privacy' => 'ລາຍລະອຽດການເກັບ, ນຳໃຊ້ ແລະ ປົກປ້ອງຂໍ້ມູນຂອງທ່ານ.',
        'rules' => 'ກົດທີ່ຜູ້ສ້າງເນື້ອຫາ, ຜູ້ໂດເນດ ແລະ ສະມາຊິກທຸກຄົນຕ້ອງປະຕິບັດ.',
        'withdrawal-terms' => 'ລາຍລະອຽດຍອດທີ່ຖອນໄດ້, ຄ່າທຳນຽມ, ການກວດສອບ ແລະ ການຈ່າຍເງິນ.',
        'faq' => 'ຄຳຕອບສຳລັບຄຳຖາມທີ່ຜູ້ໃຊ້ງານສອບຖາມເລື້ອຍໆ.',
        'contact' => 'ຕິດຕໍ່ທີມງານເມື່ອຕ້ອງການຄວາມຊ່ວຍເຫຼືອ ຫຼື ພົບບັນຫາ.',
    ];
    $contactSocials = array_filter([
        'Facebook' => $siteSettings->get('social_facebook'),
        'YouTube' => $siteSettings->get('social_youtube'),
        'TikTok' => $siteSettings->get('social_tiktok'),
        'Discord' => $siteSettings->get('social_discord'),
        'Telegram' => $siteSettings->get('social_telegram'),
        'LINE' => $siteSettings->get('social_line'),
    ]);
@endphp

<section class="border-b border-slate-200 bg-slate-50/70 dark:border-white/10 dark:bg-white/[0.025]">
    <div class="mx-auto max-w-7xl px-6 py-14 sm:py-18">
        <a href="{{ route('home') }}" class="text-sm font-bold text-brand-500">← ກັບໄປໜ້າຫຼັກ</a>
        <h1 class="mt-5 max-w-4xl text-3xl font-extrabold tracking-tight sm:text-5xl">{{ $title }}</h1>
        <p class="mt-4 max-w-2xl text-base leading-8 text-slate-500 dark:text-slate-400">{{ $descriptions[$page] ?? '' }}</p>
    </div>
</section>

<div class="mx-auto max-w-7xl px-6 py-14">
    @if ($page === 'news')
        <div class="grid gap-6 lg:grid-cols-2">
            @forelse ($blocks as $block)
                <article id="news-{{ $block->id }}" class="scroll-mt-28 overflow-hidden rounded-3xl border border-slate-200 bg-white dark:border-white/10 dark:bg-slate-900/60">
                    @if ($block->image_url)
                        <img src="{{ $block->image_url }}" alt="{{ $block->heading }}" class="h-56 w-full object-cover">
                    @endif
                    <div class="p-6 sm:p-7">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <span class="rounded-full bg-brand-500/10 px-3 py-1 text-xs font-bold text-brand-500">{{ $block->subheading ?: 'ປະກາດ' }}</span>
                            <time class="text-xs text-slate-400">{{ $block->updated_at->format('d/m/Y H:i') }}</time>
                        </div>
                        <h2 class="mt-5 text-xl font-extrabold leading-8">{{ $block->heading }}</h2>
                        @if ($block->body)
                            <div class="prose prose-slate mt-4 max-w-none text-sm leading-7 dark:prose-invert">{!! $block->body !!}</div>
                        @endif
                        @if ($block->link_url)
                            <a href="{{ $block->link_url }}" class="nl-btn-ghost mt-5" target="_blank" rel="noopener">{{ $block->link_label ?: 'ເບິ່ງລາຍລະອຽດ' }} →</a>
                        @endif
                    </div>
                </article>
            @empty
                <p class="text-slate-400">ຍັງບໍ່ມີຂ່າວສານ</p>
            @endforelse
        </div>
    @elseif ($page === 'community')
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($blocks as $index => $block)
                <article class="flex min-h-64 flex-col rounded-3xl border border-slate-200 p-6 dark:border-white/10 dark:bg-white/[0.025]">
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-500/10 text-sm font-black text-brand-500">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <h2 class="mt-5 text-xl font-extrabold">{{ $block->heading }}</h2>
                    @if ($block->subheading)<p class="mt-2 text-sm font-semibold text-brand-500">{{ $block->subheading }}</p>@endif
                    @if ($block->body)<div class="mt-3 text-sm leading-7 text-slate-500 dark:text-slate-400">{!! $block->body !!}</div>@endif
                    @if ($block->link_url)
                        <a href="{{ $block->link_url }}" target="_blank" rel="noopener" class="nl-btn-primary mt-auto justify-center">{{ $block->link_label ?: 'ເຂົ້າຮ່ວມຄອມມູນິຕີ' }}</a>
                    @else
                        <span class="mt-auto rounded-xl bg-slate-100 px-4 py-2.5 text-center text-xs font-semibold text-slate-400 dark:bg-white/5">ລໍຖ້າແອັດມິນເພີ່ມລິ້ງ</span>
                    @endif
                </article>
            @empty
                <p class="text-slate-400">ຍັງບໍ່ມີຊ່ອງທາງຄອມມູນິຕີ</p>
            @endforelse
        </div>
    @elseif ($page === 'contact')
        <div class="grid gap-8 lg:grid-cols-[1.15fr_.85fr]">
            <div class="grid gap-5">
                @forelse ($blocks as $block)
                    <section class="rounded-3xl border border-slate-200 p-6 dark:border-white/10 dark:bg-white/[0.025]">
                        @if ($block->heading)<h2 class="text-xl font-extrabold">{{ $block->heading }}</h2>@endif
                        @if ($block->subheading)<p class="mt-2 font-semibold text-brand-500">{{ $block->subheading }}</p>@endif
                        @if ($block->body)<div class="mt-4 text-sm leading-7 text-slate-500 dark:text-slate-400">{!! $block->body !!}</div>@endif
                        @if ($block->link_url)<a href="{{ $block->link_url }}" class="nl-btn-ghost mt-5">{{ $block->link_label ?: 'ຕິດຕໍ່' }}</a>@endif
                    </section>
                @empty
                    <p class="text-slate-400">ຍັງບໍ່ມີຂໍ້ມູນຕິດຕໍ່</p>
                @endforelse
            </div>
            <aside class="h-fit rounded-3xl bg-slate-950 p-7 text-white">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-brand-300">OFFICIAL CONTACT</p>
                <h2 class="mt-3 text-2xl font-extrabold">ຊ່ອງທາງທາງການ</h2>
                @if ($siteSettings->get('contact_email'))
                    <a href="mailto:{{ $siteSettings->get('contact_email') }}" class="mt-5 block break-all rounded-2xl bg-white/10 px-4 py-3 text-sm font-semibold hover:bg-white/15">{{ $siteSettings->get('contact_email') }}</a>
                @else
                    <p class="mt-5 rounded-2xl bg-white/5 px-4 py-3 text-sm text-slate-300">ແອັດມິນຍັງບໍ່ໄດ້ກຳນົດອີເມວຕິດຕໍ່</p>
                @endif
                @if (count($contactSocials))
                    <div class="mt-6 grid gap-2">
                        @foreach ($contactSocials as $label => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="flex items-center justify-between rounded-xl border border-white/10 px-4 py-2.5 text-sm font-semibold hover:bg-white/5">
                                {{ $label }} <span>↗</span>
                            </a>
                        @endforeach
                    </div>
                @endif
                <p class="mt-6 text-xs leading-6 text-slate-400">ກະລຸນາຕິດຕໍ່ຜ່ານຊ່ອງທາງທີ່ສະແດງໃນໜ້ານີ້ເທົ່ານັ້ນ.</p>
            </aside>
        </div>
    @else
        @php($isLegalPage = in_array($page, ['terms', 'privacy', 'rules', 'withdrawal-terms'], true))
        <div class="{{ $isLegalPage ? 'grid gap-8 lg:grid-cols-[17rem_1fr]' : 'mx-auto max-w-3xl' }}">
            @if ($isLegalPage && $blocks->count())
                <aside class="h-fit rounded-2xl border border-slate-200 bg-slate-50 p-4 lg:sticky lg:top-24 dark:border-white/10 dark:bg-white/[0.025]">
                    <p class="px-2 text-xs font-black uppercase tracking-[0.14em] text-brand-500">ເນື້ອຫາໃນໜ້ານີ້</p>
                    <nav class="mt-3 max-h-[65vh] space-y-1 overflow-y-auto pr-1 text-sm">
                        @foreach ($blocks as $block)
                            @if ($block->heading)
                                <a href="#section-{{ $block->id }}" class="block rounded-xl px-2 py-2 leading-6 text-slate-500 transition hover:bg-white hover:text-brand-500 dark:text-slate-400 dark:hover:bg-white/5">
                                    {{ $block->heading }}
                                </a>
                            @endif
                        @endforeach
                    </nav>
                </aside>
            @endif

            <div class="space-y-7">
            @forelse ($blocks as $block)
                @if ($block->type === 'faq')
                    <div id="section-{{ $block->id }}" x-data="{ open: false }" class="scroll-mt-28 rounded-2xl border border-slate-200 bg-white dark:border-white/10 dark:bg-white/[0.025]">
                        <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-semibold">
                            <span>{{ $block->heading }}</span>
                            <span class="text-brand-500" x-text="open ? '−' : '+'"></span>
                        </button>
                        <div x-show="open" x-cloak x-transition class="px-5 pb-5 text-sm leading-7 text-slate-600 dark:text-slate-400">{!! $block->body !!}</div>
                    </div>
                @elseif ($block->type === 'image' && $block->image_url)
                    <img src="{{ $block->image_url }}" alt="{{ $block->heading }}" class="w-full rounded-3xl">
                @else
                    <section id="section-{{ $block->id }}" class="scroll-mt-28 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 dark:border-white/10 dark:bg-white/[0.025]">
                        @if ($block->heading)<h2 class="text-2xl font-extrabold">{{ $block->heading }}</h2>@endif
                        @if ($block->subheading)<p class="mt-2 text-slate-500 dark:text-slate-400">{{ $block->subheading }}</p>@endif
                        @if ($block->body)<div class="prose prose-slate mt-5 max-w-none dark:prose-invert">{!! $block->body !!}</div>@endif
                        @if ($block->link_url)<a href="{{ $block->link_url }}" class="nl-btn-primary mt-5">{{ $block->link_label ?: 'ເບິ່ງເພີ່ມ' }}</a>@endif
                    </section>
                @endif
            @empty
                <p class="text-slate-400">ຍັງບໍ່ມີເນື້ອຫາໃນໜ້ານີ້</p>
            @endforelse
            </div>
        </div>
    @endif
</div>
@endsection
