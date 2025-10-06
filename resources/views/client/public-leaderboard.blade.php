<x-guest-layout>
    <html>

    <head>
        <meta content="text/html; charset=UTF-8" http-equiv="content-type">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    </head>

    <body class="w-full p-5 md:max-w-[1200px] md:py-[60px] m-auto">
        <div class="container max-w-lg mx-auto p-4">
            <img src="{{ asset('images/logo-low.png') }}" data-src="{{ asset('images/logo.svg') }}" alt="Top Users"
            class="mb-4 w-[200px] md:w-[300px] m-auto lazy-image">

            <span
                class="block text-white text-[18px] md:text-[26px] text-center mb-4">{{ __('dashboard.top-ranks-title') }}</span>

            <div class="grid grid-cols-12 gap-2">
                <!-- Column Headers -->
                <div class="col-span-2 font-semibold text-[rgba(255,255,255,0.7)] hidden md:block">#</div>
                <div class="col-span-6 font-semibold text-[rgba(255,255,255,0.7)] hidden md:block">User</div>
                <div class="col-span-4 font-semibold text-[rgba(255,255,255,0.7)] hidden md:block">Profit</div>

                @php
                    $index = 0;

                    function formatCurrency($amount)
                    {
                        // if ($amount >= 1000000) {
                        //     return '€' . number_format($amount / 1000000, 2) . 'M';
                        // } elseif ($amount >= 1000) {
                        //     return '€' . number_format($amount / 1000, 2) . 'k';
                        // } else {
                        //     return '€' . number_format($amount, 2);
                        // }

                        // return '€' . number_format($amount, 0);
                        return $amount . '€';
                    }
                @endphp

                @foreach ($topUsers as $user)
                    @php
                        $index++;
                        if ($user->pnl < 0) {
                            continue;
                        }
                    @endphp

                    <!-- Row Wrapper with Hover Effect -->
                    <div
                        class="col-span-12 grid grid-cols-12 items-center h-[70px] gap-4 bg-[rgba(255,255,255,0.1)] p-[2px] rounded-md transition duration-300 ease-in-out hover:bg-[rgba(255,255,255,0.3)] cursor-pointer">
                        <!-- Row Data -->
                        <div class="col-span-3 md:col-span-2">
                            @if ($index == 1)
                                <img src="{{ asset('images/1-low.png') }}" data-src="{{ asset('images/1.png') }}"
                                    class="m-auto w-[45px] md:w-[50px] lazy-image" alt="1st">
                            @elseif ($index == 2)
                                <img src="{{ asset('images/2-low.png') }}" data-src="{{ asset('images/2.png') }}"
                                    class="m-auto w-[35px] md:w-[45px] lazy-image" alt="2nd">
                            @elseif ($index == 3)
                                <img src="{{ asset('images/3-low.png') }}" data-src="{{ asset('images/3.png') }}"
                                    class="m-auto w-[25px] md:w-[35px] lazy-image" alt="3rd">
                            @else
                                <img src="{{ asset('images/star-low.png') }}" data-src="{{ asset('images/star.png') }}"
                                    class="m-auto w-[20px] md:w-[30px] lazy-image" alt="{{ $index }}">
                            @endif
                        </div>

                        <div class="col-span-9 md:col-span-6">
                            <!-- Email on Two Lines -->
                            <span class="block text-white">{{ getReadableName($user->user_name ?? 'Unknown User') }}</span>
                            <div class="col-span-4 text-[rgba(255,255,255,0.7)] md:hidden block">
                                @if ($index == 1)
                                    {{ formatCurrency($user->pnl * 100) }}
                                @elseif ($index == 2)
                                    {{ formatCurrency($user->pnl * 50) }}
                                @elseif ($index == 3)
                                    {{ formatCurrency($user->pnl * 30) }}
                                @else
                                    {{ formatCurrency($user->pnl * 20) }}
                                @endif
                            </div>
                            {{-- <span
                            class="block text-[rgba(255,255,255,0.7)]">{{ substr($user->name, 0, 3) . '*********@' . explode('@', $user->name)[1] }}</span> --}}
                        </div>

                        <div class="col-span-4 text-[rgba(255,255,255,0.7)] hidden md:block">
                            @if ($index == 1)
                                {{ formatCurrency($user->pnl * 100) }}
                            @elseif ($index == 2)
                                {{ formatCurrency($user->pnl * 50) }}
                            @elseif ($index == 3)
                                {{ formatCurrency($user->pnl * 30) }}
                            @else
                                {{ formatCurrency($user->pnl * 20) }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                console.log("DOM fully loaded and parsed");
                // Select all images with the class 'lazy-image'
                let lazyImages = document.querySelectorAll('.lazy-image');

                // Check if IntersectionObserver is supported
                if ("IntersectionObserver" in window) {
                    let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                let lazyImage = entry.target;
                                // Replace the placeholder src with the actual image src
                                lazyImage.src = lazyImage.dataset.src;
                                // Optionally, you can remove the 'lazy-image' class
                                lazyImage.classList.remove('lazy-image');
                                // Stop observing the image
                                lazyImageObserver.unobserve(lazyImage);
                            }
                        });
                    });

                    // Observe each lazy image
                    lazyImages.forEach(function(lazyImage) {
                        lazyImageObserver.observe(lazyImage);
                    });
                } else {
                    // Fallback for browsers that don't support IntersectionObserver
                    lazyImages.forEach(function(lazyImage) {
                        lazyImage.src = lazyImage.dataset.src;
                    });
                }
            });
        </script>
    </body>

    </html>
</x-guest-layout>
