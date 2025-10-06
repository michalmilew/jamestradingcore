<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
            {{__('Servers')}}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7x1 mx-auto px-8">
            <div class=" ">
                <div class="overflow-x-auto justify-between rounded-lg px-2 py-2">
                    <div class="flex justify-between">
                        <a href="{{route(\App\Models\SettingLocal::getLang().'.admin.servers.create')}}" class="flex items-center gap-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        {{__('Add')}}
                        </a>
                    </div>

                </div>
                <div class="overflow-x-auto rounded-lg px-2 mt-2">
                    <table class="table-auto w-full mb-2">
                        <thead>
                            <tr class="border-b">
                                <th class="px-4 py-2 text-left">{{__('Name')}}</th>
                                <th class="px-4 py-2 text-left">{{__('Actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($servers as $server)
                            <tr class="border-b">
                                <td class="py-2 px-4">{{$server->name}}</td>
                                <th class="flex py-2 px-4">
                                    <a href="{{ route(\App\Models\SettingLocal::getLang().'.admin.servers.edit', $server->id) }}" class="bg-green-500 hover:bg-green-700 text-gray font-bold py-2 mr-2 px-4 rounded">{{__('Edit')}}</a>
                                <form action="{{ route(\App\Models\SettingLocal::getLang().'.admin.servers.destroy', $server->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('{{__('Are you sure you want to delete this server?')}}')" class="bg-red-500 hover:bg-red-700 text-gray font-bold py-2 px-4 rounded">{{__('Delete')}}</button>
                                </form>

                                </th>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="py-4">
                        {{$servers->links()}}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
