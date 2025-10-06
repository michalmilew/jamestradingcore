<div class="min-h-screen flex flex-col  justify-center items-center px-4 md:px-2 pt-6 sm:pt-0 ">
    <div class="max-w-[370px] w-full sm:max-w-md md:mx-6 px-2 py-2 md:px-6 md:py-4 shadow-md overflow-hidden sm:rounded-lg"
    style="background: #252e39;border-radius: 37px;">
        <div class="flex flex-col  justify-center items-center px-2 pt-2 md:pt-6 sm:pt-0 ">
            {{ $logo }}
        </div>
        <div style="background: #12181F; border-radius: 37px;" class="px-6 py-4">
        {{ $slot }}
        </div>        
    </div>
</div>
