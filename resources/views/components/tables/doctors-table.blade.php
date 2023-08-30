@props(['doctors'])
<div class="w-[55%]">
    <div class="overflow-x-auto border border-primary rounded-xl">
        @if ($doctors != null && count($doctors)>0)

        <table class="table ">
          <!-- head -->
          <thead>
            <tr class=" text-secondary ">
              {{-- <th></th> --}}
              <th>Name</th>
              <th>Department</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($doctors as $doctor)
                <tr class="text-base-content hover:bg-base-100">
                    {{-- <th>{{$loop->index + 1}}</th> --}}
                    <td>{{$doctor->name}}</td>
                    <td>{{$doctor->department ?? 'Not specified'}}</td>
                </tr>
            @endforeach
          </tbody>
        </table>

        @else
            <h1 class=" font-semibold text-lg text-neutral-content p-4">No doctors to show</h1>
        @endif


      </div>
    <div class="mt-1.5">
        {{ $doctors->links() }}
    </div>




</div>
