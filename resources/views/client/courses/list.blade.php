<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
            {{__('Courses')}}
        </h2>
    </x-slot>

    <div class="p-5">
        <div class="max-w-7x1 mx-auto px-8">
            <div class=" ">
                <div class="overflow-x-auto rounded-lg px-2 mt-2">
                    <table class="table-auto divide-y divide-gray-200 sm:table-auto w-full mb-2">
                        <thead>
                            <tr class="border-b border-indigo-400">
                                <th class="px-4 py-2 text-left">{{__('Title')}}</th>
                                <th class="px-4 py-2 text-left">{{__('Lang')}}</th>
                                <th class="px-4 py-2 text-left">{{__('Actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                            <tr class="border-b border-indigo-400 ">
                                <th class="py-2 px-4">{{$course->name}}</th>
                                <th class="py-2 px-4">{{$course->lang}}</th>
                                <th class="py-2 px-4">
                                    <a href="{{ route(\App\Models\SettingLocal::getLang().'.client.courses.show', $course) }}" class="bg-green-500 hover:bg-green-700 text-gray font-bold py-2 mx-2 px-4 rounded h-full">{{__('Show')}}</a>
                                </th>
                            </tr>
                            @endforeach                        
                        </tbody>
                    </table>
                    <div class="py-4">
                        {{$courses->links()}}
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>