<x-app-layout>
    <x-slot name="header">
        <h2>
            {{__('Course')}}
        </h2>
    </x-slot>
    <div class="py-5 px-8">
        <div class="max-w-7x1 mx-auto ">
            <div class=" ">
                <div class="overflow-x-auto justify-between rounded-lg px-2 py-2">
                    <div class="flex flex-col">
                        <h1 class="lg:py-8 ">
                            {{$course->name}}
                        </h1>

                        <style>
                            #toolbarViewer {
                                display: none;
                            }
                        </style>
                        
                        <iframe class="h-screen" src="{{$course->url}}" frameborder="0" allow="autoplay"></iframe>
                    
                    </div>

                </div>

            </div>
        </div>

    </div>
</x-app-layout>

