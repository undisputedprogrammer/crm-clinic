@props(['agents'])
<div class="w-[55%]">
    <div class="overflow-x-auto border border-primary rounded-xl">
        @if ($agents != null && count($agents)>0)

        <table class="table ">
          <!-- head -->
          <thead>
            <tr class=" text-secondary ">
              {{-- <th></th> --}}
              <th>User ID</th>
              <th>Name</th>
              <th>Email</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($agents as $agent)
                <tr class="text-base-content hover:bg-base-100 relative">
                    {{-- <th>{{$loop->index + 1}}</th> --}}
                    <td>{{$agent->id}}</td>
                    <td>{{$agent->name}}</td>
                    <td>{{$agent->email}}</td>
                    <td>
                        <button @click.prevent.stop="$dispatch('agentedit', {id: {{$agent->id}}, name: '{{$agent->name}}', email: '{{$agent->email}}'});" class="btn btn-ghost btn-xs text-warning" type="button">
                            <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4"/>
                        </button>
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>

        @else
            <h1 class=" font-semibold text-lg text-neutral-content p-4">No agents to show</h1>
        @endif


      </div>
    <div class="mt-1.5">
        {{ $agents->links() }}
    </div>

</div>
